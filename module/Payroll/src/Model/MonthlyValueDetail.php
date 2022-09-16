<?php

namespace Payroll\Model;

use Application\Model\Model;

class MonthlyValueDetail extends Model {

    CONST TABLE_NAME = "HRIS_MONTHLY_VALUE_DETAIL";
    CONST MTH_ID = "MTH_ID";
    CONST EMPLOYEE_ID = "EMPLOYEE_ID";
    CONST MTH_VALUE = "MTH_VALUE";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST FISCAL_YEAR_ID = "FISCAL_YEAR_ID";
    CONST MONTH_ID = "MONTH_ID";

    public $mthId;
    public $employeeId;
    public $mthValue;
    public $createdDt;
    public $modifiedDt;
    public $fiscalYearId;
    public $monthId;
    public $mappings = [
        'mthId' => self::MTH_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'mthValue' => self::MTH_VALUE,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'fiscalYearId' => self::FISCAL_YEAR_ID,
        'monthId' => self::MONTH_ID
    ];

}
