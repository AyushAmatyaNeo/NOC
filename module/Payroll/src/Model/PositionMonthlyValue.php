<?php

namespace Payroll\Model;

use Application\Model\Model;

class PositionMonthlyValue extends Model {

    const TABLE_NAME = "HRIS_POSITION_MONTHLY_VALUE";
    const MTH_ID = "MTH_ID";
    const POSITION_ID = "POSITION_ID";
    const MONTH_ID = "MONTH_ID";
    const ASSIGNED_VALUE = "ASSIGNED_VALUE";

    public $mthId;
    public $positionId;
    public $monthId;
    public $assignedValue;
    public $mappings = [
        'mthId' => self::MTH_ID,
        'positionId' => self::POSITION_ID,
        'monthId' => self::MONTH_ID,
        'assignedValue' => self::ASSIGNED_VALUE,
    ];

}
