<?php

namespace Payroll\Model;

use Application\Model\Model;

class FinanceData extends Model {

    CONST TABLE_NAME = "HRIS_FINANCE_DATA";
    CONST FINANCE_DATA_ID = "FINANCE_DATA_ID";
    CONST MODULE_CODE = "MODULE_CODE";
    CONST MASTER_ID = "MASTER_ID";
    CONST REQUEST_ID = "REQUEST_ID";
    CONST AMOUNT = "AMOUNT";
    CONST BRANCH_ID = "BRANCH_ID";
    CONST OFFICE_ID = "OFFICE_ID";
    CONST DEPARTMENT_ID = "DEPARTMENT_ID";

    public $financeDataId;
    public $moduleCode;
    public $masterId;
    public $requestId;
    public $amount;
    public $branchId;
    public $officeId;
    public $departmentId;
    
    public $mappings = [
        'financeDataId' => self::FINANCE_DATA_ID,
        'moduleCode' => self::MODULE_CODE,
        'masterId' => self::MASTER_ID,
        'requestId' => self::REQUEST_ID,
        'amount' => self::AMOUNT,
        'branchId' => self::BRANCH_ID,
        'officeId' => self::OFFICE_ID,
        'departmentId' => self::DEPARTMENT_ID
    ];

}
