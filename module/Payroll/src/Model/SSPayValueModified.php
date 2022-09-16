<?php

namespace Payroll\Model;

use Application\Model\Model;

class SSPayValueModified extends Model {

    const TABLE_NAME = "HRIS_SS_PAY_VALUE_MODIFIED";
    const MONTH_ID = "MONTH_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const PAY_ID = "PAY_ID";
    const VAL = "VAL";
    const SALARY_TYPE_ID = "SALARY_TYPE_ID";

    public $monthId;
    public $employeeId;
    public $payId;
    public $val;
    public $salaryTypeId;
    public $mappings = [
        'monthId' => self::MONTH_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'payId' => self::PAY_ID,
        'val' => self::VAL,
        'salaryTypeId' => self::SALARY_TYPE_ID,
    ];

}
