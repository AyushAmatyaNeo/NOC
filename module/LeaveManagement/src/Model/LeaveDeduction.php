<?php

namespace LeaveManagement\Model;

use Application\Model\Model;

class LeaveDeduction extends Model {

    const TABLE_NAME = "HRIS_EMPLOYEE_LEAVE_DEDUCTION";
    const ID = "ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const LEAVE_ID = "LEAVE_ID";
    const DEDUCTION_DT = "DEDUCTION_DT";
    const NO_OF_DAYS = "NO_OF_DAYS";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";

    public $id;
    public $employeeId;
    public $leaveId;
    public $deductionDt;
    public $noOfDays;
    public $status;
    public $remarks;
    public $modifiedDt;
    public $modifiedBy;
    public $createdDt;
    public $createdBy;


    public $mappings = [
        'id' => self::ID,
        'employeeId' => self::EMPLOYEE_ID,
        'leaveId' => self::LEAVE_ID,
        'deductionDt' => self::DEDUCTION_DT,
        'noOfDays' => self::NO_OF_DAYS,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'modifiedDt' => self::MODIFIED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
    ];

}
