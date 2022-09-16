<?php

namespace AttendanceManagement\Model;

use Application\Model\Model;

class Attendance extends Model {

    const TABLE_NAME = "HRIS_ATTENDANCE";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const ATTENDANCE_DT = "ATTENDANCE_DT";
    const IP_ADDRESS = "IP_ADDRESS";
    const ATTENDANCE_FROM = "ATTENDANCE_FROM";
    const ATTENDANCE_TIME = "ATTENDANCE_TIME";
    const REMARKS = "REMARKS";
    const LOCATION="LOCATION";

    public $employeeId;
    public $attendanceDt;
    public $ipAddress;
    public $attendanceFrom;
    public $attendanceTime;
    public $remarks;
    public $location;
    public $mappings = [
        'employeeId' => self::EMPLOYEE_ID,
        'attendanceDt' => self::ATTENDANCE_DT,
        'ipAddress' => self::IP_ADDRESS,
        'attendanceFrom' => self::ATTENDANCE_FROM,
        'attendanceTime' => self::ATTENDANCE_TIME,
        'remarks' => self::REMARKS,
        'location'=>self::LOCATION
    ];

}
