<?php

namespace Customer\Model;

use Application\Model\Model;

class CustomerContract extends Model {

    CONST TABLE_NAME = "HRIS_CUSTOMER_CONTRACT";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST CUSTOMER_ID = "CUSTOMER_ID";
    CONST CONTRACT_NAME = "CONTRACT_NAME";
    CONST START_DATE = "START_DATE";
    CONST END_DATE = "END_DATE";
    CONST BILLING_MONTH = "BILLING_MONTH";
    CONST FREEZED = "FREEZED";
    CONST BILLING_TYPE = "BILLING_TYPE";
    CONST CREATED_BY = "CREATED_BY";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_BY = "MODIFIED_BY";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST REMARKS = "REMARKS";
    CONST STATUS = "STATUS";
    CONST OT_RATE = "OT_RATE";
    CONST OT_TYPE = "OT_TYPE";

    public $contractId;
    public $customerId;
    public $contractName;
    public $startDate;
    public $endDate;
    public $billingMonth;
    public $freezed;
    public $billingType;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $remarks;
    public $status;
    public $otRate;
    public $otType;
    
    public $mappings = [
        'contractId' => self::CONTRACT_ID,
        'customerId' => self::CUSTOMER_ID,
        'contractName' => self::CONTRACT_NAME,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'billingMonth' => self::BILLING_MONTH,
        'freezed' => self::FREEZED,
        'billingType' => self::BILLING_TYPE,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'otRate' => self::OT_RATE,
        'otType' => self::OT_TYPE,
    ];

}
