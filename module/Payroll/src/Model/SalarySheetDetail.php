<?php

namespace Payroll\Model;

use Application\Model\Model;

class SalarySheetDetail extends Model {

    const TABLE_NAME = "HRIS_SALARY_SHEET_DETAIL";
    const SHEET_NO = "SHEET_NO";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const PAY_ID = "PAY_ID";
    const VAL = "VAL";

    public $sheetNo;
    public $employeeId;
    public $payId;
    public $val;
    public $mappings = [
        'sheetNo' => self::SHEET_NO,
        'employeeId' => self::EMPLOYEE_ID,
        'payId' => self::PAY_ID,
        'val' => self::VAL,
    ];

}
