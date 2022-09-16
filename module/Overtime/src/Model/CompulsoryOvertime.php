<?php

namespace Overtime\Model;

use Application\Model\Model;

class CompulsoryOvertime extends Model {

    const TABLE_NAME = "HRIS_COMPULSORY_OVERTIME";
    const COMPULSORY_OVERTIME_ID = "COMPULSORY_OVERTIME_ID";
    const COMPULSORY_OT_DESC = "COMPULSORY_OT_DESC";
    const LATE_OVERTIME_HR = "LATE_OVERTIME_HR";
    const EARLY_OVERTIME_HR = "EARLY_OVERTIME_HR";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";

    public $compulsoryOvertimeId;
    public $compulsoryOtDesc;
    public $lateOvertimeHr;
    public $earlyOvertimeHr;
    public $startDate;
    public $endDate;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $status;
    public $mappings = [
        'compulsoryOvertimeId' => self::COMPULSORY_OVERTIME_ID,
        'compulsoryOtDesc' => self::COMPULSORY_OT_DESC,
        'earlyOvertimeHr' => self::EARLY_OVERTIME_HR,
        'lateOvertimeHr' => self::LATE_OVERTIME_HR,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
    ];

}
