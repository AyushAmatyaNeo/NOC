<?php

namespace Customer\Model;

use Application\Model\Model;

class DutyTypeModel extends Model {

    CONST TABLE_NAME = "HRIS_DUTY_TYPE";
    CONST DUTY_TYPE_ID = "DUTY_TYPE_ID";
    CONST DUTY_TYPE_NAME = "DUTY_TYPE_NAME";
    CONST NORMAL_HOUR = "NORMAL_HOUR";
    CONST OT_HOUR = "OT_HOUR";
    CONST CREATED_BY = "CREATED_BY";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_BY = "MODIFIED_BY";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST REMARKS = "REMARKS";
    CONST STATUS = "STATUS";

    public $dutyTypeId;
    public $dutyTypeName;
    public $normalHour;
    public $otHour;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $remarks;
    public $status;
    public $mappings = [
        'dutyTypeId' => self::DUTY_TYPE_ID,
        'dutyTypeName' => self::DUTY_TYPE_NAME,
        'normalHour' => self::NORMAL_HOUR,
        'otHour' => self::OT_HOUR,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
    ];

}
