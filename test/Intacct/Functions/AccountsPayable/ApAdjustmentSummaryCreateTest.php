<?php

/**
 * Copyright 2016 Intacct Corporation.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"). You may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "LICENSE" file accompanying this file. This file is distributed on
 * an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Intacct\Functions\AccountsPayable;

use Intacct\FieldTypes\DateType;
use Intacct\Xml\XMLWriter;
use InvalidArgumentException;

class ApAdjustmentSummaryCreateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Intacct\Functions\AccountsPayable\ApAdjustmentSummaryCreate::writeXml
     */
    public function testDefaultParams()
    {
        $expected = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<function controlid="unittest">
    <create_apadjustmentbatch>
        <batchtitle>unit test</batchtitle>
        <datecreated>
            <year>2015</year>
            <month>06</month>
            <day>30</day>
        </datecreated>
    </create_apadjustmentbatch>
</function>
EOF;

        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('    ');
        $xml->startDocument();

        $record = new ApAdjustmentSummaryCreate('unittest');
        $record->setTitle('unit test');
        $record->setGlPostingDate(new DateType('2015-06-30'));

        $record->writeXml($xml);

        $this->assertXmlStringEqualsXmlString($expected, $xml->flush());
    }

    /**
     * @covers Intacct\Functions\AccountsPayable\ApAdjustmentSummaryCreate::writeXml
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Title is required for create
     */
    public function testMissingTitle()
    {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('    ');
        $xml->startDocument();

        $record = new ApAdjustmentSummaryCreate('unittest');

        $record->writeXml($xml);
    }

    /**
     * @covers Intacct\Functions\AccountsPayable\ApAdjustmentSummaryCreate::writeXml
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage GL Posting Date is required for create
     */
    public function testMissingDate()
    {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('    ');
        $xml->startDocument();

        $record = new ApAdjustmentSummaryCreate('unittest');
        $record->setTitle('unit test');

        $record->writeXml($xml);
    }
}
