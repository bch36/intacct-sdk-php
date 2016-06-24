<?php

/**
 * Copyright 2016 Intacct Corporation.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License"). You may not
 *  use this file except in compliance with the License. You may obtain a copy
 *  of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "LICENSE" file accompanying this file. This file is distributed on
 * an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 */

namespace Intacct\Query;

use Intacct\Content;
use Intacct\Functions\ReadByQuery;
use Intacct\Functions\ReadMore;
use ArrayIterator;
use Intacct\Xml\Response\Operation\ResultException;
use Intacct\Xml\RequestHandler;

class QueryClient
{

    /**
     * @var int
     */
    private static $MAX_QUERY_TOTAL_COUNT = 100000;

    /**
     * Accepts the following options:
     *
     * - sender_id: (string)
     * - sender_password: (string)
     * - session_id: (string)
     * - control_id: (string)
     * - doc_par_id: (string)
     * - fields: (array)
     * - max_total_count: (int, default=int(100000))
     * - object: (string, required)
     * - page_size: (int, default=int(1000)
     * - query: (string)
     * - return_format: (string, default=string(3) "xml")
     *
     * @param array $params
     * @return ArrayIterator
     * @throws ResultException
     */
    public function readAllObjectsByQuery(array $params)
    {

        $defaults = [
            'max_total_count' => self::$MAX_QUERY_TOTAL_COUNT,
        ];

        $config = array_merge($defaults, $params);

        $result = $this->performReadByQuery($config);

        $records = new ArrayIterator();

        $this->addRecords($records, $result);

        while ($result->getNumRemaining() > 0) {
            // do readMore
            $result = $this->performReadMore($result->getResultId(), $config);

            $this->addRecords($records, $result);
        }

        return $records;
    }

    /**
     * @param $params
     * @return \Intacct\Xml\Response\Operation\Result
     * @throws ResultException
     */
    private function performReadByQuery($params)
    {
        $readByQuery = new ReadByQuery($params);

        $contentBlock = new Content([$readByQuery]);

        $requestHandler = new RequestHandler($params);
        $response = $requestHandler->executeSynchronous($params, $contentBlock);

        $result = $response->getOperation()->getResult();

        if ($result->getStatus() !== 'success') {
            throw new ResultException(
                'An error occurred trying to get query records', $result->getErrors()
            );
        }

        if ($result->getTotalCount() > $params['max_total_count']) {
            throw new ResultException(
                'Query result totalcount of ' . $result->getTotalCount() .
                ' exceeds max_total_count parameter of ' . $params['max_total_count']
            );
        }

        return $result;
    }

    /**
     * @param $resultId
     * @param $params
     * @return \Intacct\Xml\Response\Operation\Result
     */
    private function performReadMore($resultId, $params)
    {

        $param = ['result_id' => $resultId];
        $readMore = new ReadMore($param);

        $contentBlock = new Content([$readMore]);

        $requestHandler = new RequestHandler($params);
        $response = $requestHandler->executeSynchronous($params, $contentBlock);
        $result = $response->getOperation()->getResult();

        if ($result->getStatus() !== 'success') {
            throw new ResultException(
                'An error occurred trying to get query records', $result->getErrors()
            );
        }

        return $result;
    }

    /**
     * @param $records
     * @param $result
     */
    private function addRecords($records, $result)
    {
        foreach ($result->getDataArray() as $record) {
            $records->append($record);
        }
    }
}