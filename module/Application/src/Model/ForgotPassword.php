<?php
namespace Application\Model;

use Application\Model\Model;

class ForgotPassword extends Model{
    const TABLE_NAME = "HRIS_FORGOT_PWD_DTL";
    
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const CODE = "CODE";
    const EXPIRY_DATE = "EXPIRY_DATE";
    
    public $employeeId;
    public $code;
    public $expiryDate;
    
    public $mappings =[
        'employeeId'=>self::EMPLOYEE_ID,
        'code'=>self::CODE,
        'expiryDate'=>self::EXPIRY_DATE
    ];
}