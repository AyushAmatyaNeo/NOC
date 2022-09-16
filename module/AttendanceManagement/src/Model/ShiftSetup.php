<?php

namespace AttendanceManagement\Model;

use Application\Model\Model;

class ShiftSetup extends Model {

    const TABLE_NAME = "HRIS_SHIFTS";
    const SHIFT_ID = "SHIFT_ID";
    const SHIFT_CODE = "SHIFT_CODE";
    const SHIFT_ENAME = "SHIFT_ENAME";
    const SHIFT_LNAME = "SHIFT_LNAME";
    const START_TIME = "START_TIME";
    const END_TIME = "END_TIME";
    const HALF_DAY_END_TIME = "HALF_DAY_END_TIME";
    const HALF_TIME = "HALF_TIME";
    const LATE_IN = "LATE_IN";
    const EARLY_OUT = "EARLY_OUT";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
    const WEEKDAY1 = "WEEKDAY1";
    const WEEKDAY2 = "WEEKDAY2";
    const WEEKDAY3 = "WEEKDAY3";
    const WEEKDAY4 = "WEEKDAY4";
    const WEEKDAY5 = "WEEKDAY5";
    const WEEKDAY6 = "WEEKDAY6";
    const WEEKDAY7 = "WEEKDAY7";
    const CURRENT_SHIFT = "CURRENT_SHIFT";
    const TWO_DAY_SHIFT = "TWO_DAY_SHIFT";
    const DEFAULT_SHIFT = "DEFAULT_SHIFT";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const TOTAL_WORKING_HR = "TOTAL_WORKING_HR";
    const ACTUAL_WORKING_HR = "ACTUAL_WORKING_HR";
    const COMPANY_ID = "COMPANY_ID";
    const GRACE_START_TIME = "GRACE_START_TIME";
    const GRACE_END_TIME = "GRACE_END_TIME";
    const HALF_DAY_IN_TIME = "HALF_DAY_IN_TIME";
    const HALF_DAY_OUT_TIME = "HALF_DAY_OUT_TIME";
    const NIGHT_SHIFT = "NIGHT_SHIFT";
    const BREAK_DEDUCT_FLAG = "BREAK_DEDUCT_FLAG";

    public $shiftId;
    public $shiftCode;
    public $shiftEname;
    public $shiftLname;
    public $startTime;
    public $endTime;
    public $halfDayEndTime;
    public $halfTime;
    public $lateIn;
    public $earlyOut;
    public $startDate;
    public $endDate;
    public $weekday1;
    public $weekday2;
    public $weekday3;
    public $weekday4;
    public $weekday5;
    public $weekday6;
    public $weekday7;
    public $currentShift;
    public $twoDayShift;
    public $defaultShift;
    public $createdDt;
    public $modifiedDt;
    public $remarks;
    public $status;
    public $createdBy;
    public $modifiedBy;
    public $totalWorkingHr;
    public $actualWorkingHr;
    public $companyId;
    public $graceStartTime;
    public $graceEndTime;
    public $halfDayInTime;
    public $halfDayOutTime;
    public $breakDeductFlag;
    PUBLIC $nightShift;
    public $mappings = [
        'shiftId' => self::SHIFT_ID,
        'shiftCode' => self::SHIFT_CODE,
        'shiftEname' => self::SHIFT_ENAME,
        'shiftLname' => self::SHIFT_LNAME,
        'startTime' => self::START_TIME,
        'endTime' => self::END_TIME,
        'halfDayEndTime' => self::HALF_DAY_END_TIME,
        'halfTime' => self::HALF_TIME,
        'lateIn' => self::LATE_IN,
        'earlyOut' => self::EARLY_OUT,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'weekday1' => self::WEEKDAY1,
        'weekday2' => self::WEEKDAY2,
        'weekday3' => self::WEEKDAY3,
        'weekday4' => self::WEEKDAY4,
        'weekday5' => self::WEEKDAY5,
        'weekday6' => self::WEEKDAY6,
        'weekday7' => self::WEEKDAY7,
        'currentShift' => self::CURRENT_SHIFT,
        'twoDayShift' => self::TWO_DAY_SHIFT,
        'defaultShift' => self::DEFAULT_SHIFT,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'totalWorkingHr' => self::TOTAL_WORKING_HR,
        'actualWorkingHr' => self::ACTUAL_WORKING_HR,
        'companyId' => self::COMPANY_ID,
        'graceStartTime' => self::GRACE_START_TIME,
        'graceEndTime' => self::GRACE_END_TIME,
        'halfDayInTime' => self::HALF_DAY_IN_TIME,
        'halfDayOutTime' => self::HALF_DAY_OUT_TIME,
        'nightShift' => self::NIGHT_SHIFT,
        'breakDeductFlag' => self::BREAK_DEDUCT_FLAG
    ];

}
