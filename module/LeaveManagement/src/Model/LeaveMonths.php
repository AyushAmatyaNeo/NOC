<?php

namespace LeaveManagement\Model;
use Application\Model\Model;

class LeaveMonths extends Model {

    const TABLE_NAME = "HRIS_LEAVE_MONTH_CODE";
    const LEAVE_YEAR_ID = "LEAVE_YEAR_ID";
    const MONTH_ID = "MONTH_ID";
    const MONTH_EDESC = "MONTH_EDESC";
    const MONTH_NDESC = "MONTH_NDESC";
    const FROM_DATE = "FROM_DATE";
    const TO_DATE = "TO_DATE";
    const REMARKS = "REMARKS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const YEAR = "YEAR";
    const MONTH_NO = "MONTH_NO";
    const LEAVE_YEAR_MONTH_NO = "LEAVE_YEAR_MONTH_NO";

    public $leaveYearId;
    public $monthId;
    public $monthEdesc;
    public $monthNdesc;
    public $fromDate;
    public $toDate;
    public $remarks;
    public $createdDt;
    public $modifiedDt;
    public $status;
    public $year;
    public $monthNo;
    public $leaveYearMonthNo;
    public $mappings = [
        'leaveYearId' => self::LEAVE_YEAR_ID,
        'monthId' => self::MONTH_ID,
        'monthEdesc' => self::MONTH_EDESC,
        'monthNdesc' => self::MONTH_NDESC,
        'fromDate' => self::FROM_DATE,
        'toDate' => self::TO_DATE,
        'remarks' => self::REMARKS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'year' => self::YEAR,
        'monthNo' => self::MONTH_NO,
        'leaveYearMonthNo' => self::LEAVE_YEAR_MONTH_NO,
    ];

}
