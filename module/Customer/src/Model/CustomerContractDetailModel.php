<?php

namespace Customer\Model;

use Application\Model\Model;

class CustomerContractDetailModel extends Model {

    CONST TABLE_NAME = "HRIS_CUSTOMER_CONTRACT_DETAILS";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST CUSTOMER_ID = "CUSTOMER_ID";
    CONST LOCATION_ID = "LOCATION_ID";
    CONST DESIGNATION_ID = "DESIGNATION_ID";
    CONST QUANTITY = "QUANTITY";
    CONST RATE = "RATE";
    CONST DUTY_TYPE_ID = "DUTY_TYPE_ID";
    CONST DAYS_IN_MONTH = "DAYS_IN_MONTH";
    CONST WEEK_DETAILS = "WEEK_DETAILS";
    CONST CREATED_BY = "CREATED_BY";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_BY = "MODIFIED_BY";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST REMARKS = "REMARKS";
    CONST STATUS = "STATUS";

    public $contractId;
    public $customerId;
    public $locationId;
    public $designationId;
    public $quantity;
    public $rate;
    public $dutyTypeId;
    public $daysInMonth;
    public $weekDetails;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $remarks;
    public $status;
    public $mappings = [
        'contractId' => self::CONTRACT_ID,
        'customerId' => self::CUSTOMER_ID,
        'locationId' => self::LOCATION_ID,
        'designationId' => self::DESIGNATION_ID,
        'quantity' => self::QUANTITY,
        'rate' => self::RATE,
        'dutyTypeId' => self::DUTY_TYPE_ID,
        'daysInMonth' => self::DAYS_IN_MONTH,
        'weekDetails' => self::WEEK_DETAILS,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
    ];

}
