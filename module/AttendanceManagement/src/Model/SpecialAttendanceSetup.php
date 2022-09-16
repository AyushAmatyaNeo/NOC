<?php

namespace AttendanceManagement\Model;

use Application\Model\Model;

class SpecialAttendanceSetup extends Model {

    const TABLE_NAME = "HRIS_SPECIAL_ATTENDANCE_SETUP";
    const ID = "ID";
    const SP_CODE = "SP_CODE";
    const SP_EDESC = "SP_EDESC";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $id;
    public $spCode;
    public $spEdesc;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;

    public $mappings = [
        'id' => self::ID,
        'spCode' => self::SP_CODE,
        'spEdesc' => self::SP_EDESC,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'createdBy'=>self::CREATED_BY,
        'modifiedBy'=>self::MODIFIED_BY
    ];
    
}
