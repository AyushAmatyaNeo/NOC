<?php
namespace Payroll\Model;

use Application\Model\Model;

class SpecialRules extends Model {

    const TABLE_NAME = "HRIS_PAY_SETUP_SPECIAL";
    const PAY_ID = "PAY_ID";
    const SALARY_TYPE_ID = "SALARY_TYPE_ID";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const STATUS = "STATUS";
    const FORMULA = "FORMULA";
    const FLAG = "FLAG";

    public $payId;
    public $salaryTypeId;
    public $createdBy;
    public $createdDt;
    public $modifiedDt;
    public $modifiedBy;
    public $status;
    public $formula;
    public $flag;
    public $mappings = [
        'payId' => self::PAY_ID,
        'salaryTypeId' => self::SALARY_TYPE_ID,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'status' => self::STATUS,
        'formula' => self::FORMULA,
        'flag' => self::FLAG
    ];

}
