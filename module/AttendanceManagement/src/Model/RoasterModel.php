<?php

namespace AttendanceManagement\Model;

use Application\Model\Model;

class RoasterModel extends Model {

    const TABLE_NAME = "HRIS_EMPLOYEE_SHIFT_ROASTER";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const SHIFT_ID = "SHIFT_ID";
    const FOR_DATE = "FOR_DATE";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";

    public $employeeId;
    public $shiftId;
    public $forDate;
    public $createdBy;
    public $modifiedBy;
    public $createdDt;
    public $modifiedDt;
    public $mappings = [
        'employeeId' => self::EMPLOYEE_ID,
        'shiftId' => self::SHIFT_ID,
        'forDate' => self::FOR_DATE,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
    ];

}
