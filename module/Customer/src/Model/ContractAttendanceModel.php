<?php

namespace Customer\Model;

use Application\Model\Model;

class ContractAttendanceModel extends Model {

    CONST TABLE_NAME = "HRIS_CONTRACT_EMP_ATTENDANCE";
    CONST ATTENDANCE_DATE = "ATTENDANCE_DATE";
    CONST EMPLOYEE_ID = "EMPLOYEE_ID";
    CONST CUSTOMER_ID = "CUSTOMER_ID";
    CONST CONTRACT_ID = "CONTRACT_ID";
    CONST LOCATION_ID = "LOCATION_ID";
    CONST IN_TIME = "IN_TIME";
    CONST OUT_TIME = "OUT_TIME";
    CONST SHIFT_ID = "SHIFT_ID";
    CONST MONTH_CODE = "MONTH_CODE";
    CONST NORMAL_HOUR = "NORMARL_HOUR";
    CONST OT_HOUR = "OT_HOUR";
    CONST PT_HOUR = "PT_HOUR";
    CONST STATUS = "STATUS";

    public $attendanceDate;
    public $employeeId;
    public $customerId;
    public $contractId;
    public $locationId;
    public $inTime;
    public $outTime;
    public $shiftId;
    public $monthCode;
    public $normalHour;
    public $otHour;
    public $ptHour;
    public $status;
    
    public $mappings = [
        'attendanceDate' => self::ATTENDANCE_DATE,
        'employeeId' => self::EMPLOYEE_ID,
        'customerId' => self::CUSTOMER_ID,
        'contractId' => self::CONTRACT_ID,
        'locationId' => self::LOCATION_ID,
        'inTime' => self::IN_TIME,
        'outTime' => self::OUT_TIME,
        'shiftId' => self::SHIFT_ID,
        'monthCode' => self::MONTH_CODE,
        'normalHour' => self::NORMAL_HOUR,
        'otHour' => self::OT_HOUR,
        'ptHour' => self::PT_HOUR,
        'status' => self::STATUS
    ];

}
