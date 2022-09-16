<?php

namespace LeaveManagement\Model;

use Application\Model\Model;

class LeaveCarryForward extends Model { 

    const ID = "ID";
    const TABLE_NAME = "HRIS_EMP_SELF_LEAVE_CLOSING";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const LEAVE_ID = "LEAVE_ID";
    const ENCASH_DAYS ="ENCASH_DAYS";
    const CARRY_FORWARD_DAYS ="CARRY_FORWARD_DAYS";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFED_DT  = "MODIFED_DT ";
    const STATUS = "STATUS";
    

    public $id;
    public $employeeId;
    public $leaveId;
    public $encashDays;
    public $carryForwardDays;
    public $createdDate; 
    public $modifiedDate;
    public $status = [
        'id' => self::ID,
        'employeeId' => self::EMPLOYEE_ID,
        'leaveId' => self::LEAVE_ID,
        'encashDays' => self::ENCASH_DAYS,
        'carryForwardDays' => self::CARRY_FORWARD_DAYS,
        'createdDate' => self::CREATED_DATE,
        'modifiedDate' => self::MODIFED_DT,
        'status' => self::STATUS
       
    ];

}
