<?php

/**
 * Copyright 2017 Intacct Corporation.
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

namespace Intacct\Functions\Projects;

use Intacct\Xml\XMLWriter;
use InvalidArgumentException;

/**
 * Create a new timesheet entry record
 */
class TimesheetEntryCreate extends AbstractTimesheetEntry
{

    /**
     * Write the function block XML
     *
     * @param XMLWriter $xml
     * @throw InvalidArgumentException
     */
    public function writeXml(XMLWriter &$xml)
    {
        $xml->startElement('TIMESHEETENTRY');

        if (!$this->getEntryDate()) {
            throw new InvalidArgumentException('Entry Date is required for create');
        }
        $xml->writeElementDate('ENTRYDATE', $this->getEntryDate(), $xml::IA_DATE_FORMAT, true);

        $xml->writeElement('QTY', $this->getQuantity(), true);

        $xml->writeElement('DESCRIPTION', $this->getDescription());
        $xml->writeElement('NOTES', $this->getNotes());
        $xml->writeElement('TASKKEY', $this->getTaskRecordNo());
        $xml->writeElement('TIMETYPE', $this->getTimeTypeName());
        $xml->writeElement('BILLABLE', $this->isBillable());

        $xml->writeElement('EXTBILLRATE', $this->getOverrideBillingRate());
        $xml->writeElement('EXTCOSTRATE', $this->getOverrideLaborCostRate());

        $xml->writeElement('DEPARTMENTID', $this->getDepartmentId());
        $xml->writeElement('LOCATIONID', $this->getLocationId());
        $xml->writeElement('PROJECTID', $this->getProjectId());
        $xml->writeElement('CUSTOMERID', $this->getCustomerId());
        $xml->writeElement('VENDORID', $this->getVendorId());
        $xml->writeElement('ITEMID', $this->getItemId());
        $xml->writeElement('CLASSID', $this->getClassId());
        $xml->writeElement('CONTRACTID', $this->getContractId());
        $xml->writeElement('WAREHOUSEID', $this->getWarehouseId());

        $this->writeXmlImplicitCustomFields($xml);

        $xml->endElement(); //TIMESHEETENTRY
    }
}