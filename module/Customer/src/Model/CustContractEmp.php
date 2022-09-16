<?php

namespace Customer\Model;

use Application\Model\Model;

class CustContractEmp extends Model {

    CONST TABLE_NAME = "HRIS_CONTRACT_EMP_ASSIGN";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST CUSTOMER_ID = "CUSTOMER_ID";
    CONST LOCATION_ID = "LOCATION_ID";
    CONST EMPLOYEE_ID = "EMPLOYEE_ID";
    CONST DESIGNATION_ID = "DESIGNATION_ID";
    CONST START_TIME = "START_TIME";
    CONST END_TIME = "END_TIME";
    CONST LAST_ASSIGNED_DATE = "LAST_ASSIGNED_DATE";
    CONST CREATED_BY = "CREATED_BY";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_BY = "MODIFIED_BY";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST REMARKS = "REMARKS";
    CONST STATUS = "STATUS";
    CONST START_DATE = "START_DATE";
    CONST END_DATE = "END_DATE";
    CONST EMP_ASSIGN_ID = "EMP_ASSIGN_ID";
    CONST DUTY_TYPE_ID = "DUTY_TYPE_ID";
    CONST MONTHLY_RATE = "MONTHLY_RATE";

    public $contractId;
    public $customerId;
    public $locationId;
    public $employeeId;
    public $designationId;
    public $startTime;
    public $endTime;
    public $lastAssignedDate;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $remarks;
    public $status;
    public $startDate;
    public $endDate;
    public $empAssignId;
    public $dutyTypeId;
    public $monthlyRate;
    public $mappings = [
        'contractId' => self::CONTRACT_ID,
        'customerId' => self::CUSTOMER_ID,
        'locationId' => self::LOCATION_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'designationId' => self::DESIGNATION_ID,
        'startTime' => self::START_TIME,
        'endTime' => self::END_TIME,
        'lastAssignedDate' => self::LAST_ASSIGNED_DATE,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'empAssignId' => self::EMP_ASSIGN_ID,
        'dutyTypeId' => self::DUTY_TYPE_ID,
        'monthlyRate' => self::MONTHLY_RATE
    ];

}
