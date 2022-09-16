<?php

namespace AttendanceManagement\Model;

use Application\Model\Model;

class SpecialAttendanceAssign extends Model {

    const TABLE_NAME = "HRIS_SPECIAL_ATTENDANCE_ASSIGN";
    const ID = "ID";
    const SP_ID = "SP_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const ATTENDANCE_DT = "ATTENDANCE_DT";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $id;
    public $spId;
    public $employeeId;
    public $attendanceDate;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;

    public $mappings = [
        'id' => self::ID,
        'spId' => self::SP_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'attendanceDt' => self::ATTENDANCE_DT,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'createdBy'=>self::CREATED_BY,
        'modifiedBy'=>self::MODIFIED_BY
    ];
    
}
