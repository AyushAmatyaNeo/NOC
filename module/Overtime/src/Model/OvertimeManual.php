<?php
namespace Overtime\Model;

class OvertimeManual extends Model {

    const TABLE_NAME = "HRIS_COMPULSORY_OVERTIME";
    const ID = "ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const ATTENDANCE_DATE = "ATTENDANCE_DATE";
    const OVERTIME_HOUR = "OVERTIME_HOUR";

    public $id;
    public $employeeId;
    public $attendanceDate;
    public $overtimeHour;
    public $mappings = [
        'id' => self::ID,
        'employeeId' => self::EMPLOYEE_ID,
        'attendanceDate' => self::ATTENDANCE_DATE,
        'overtimeHour' => self::OVERTIME_HOUR,
    ];

}
