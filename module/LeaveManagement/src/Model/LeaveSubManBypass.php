<?php

namespace LeaveManagement\Model;

use Application\Model\Model;

class LeaveSubManBypass extends Model {

    const TABLE_NAME = "HRIS_SUB_MAN_BYPASS";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const LEAVE_ID = "LEAVE_ID";

    public $employeeId;
    public $leaveId;
    
    public $mappings = [
        'employeeId' => self::EMPLOYEE_ID,
        'leaveId' => self::LEAVE_ID
    ];

}
