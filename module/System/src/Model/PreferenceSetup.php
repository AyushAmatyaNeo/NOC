<?php
namespace System\Model;

use Application\Model\Model;

class PreferenceSetup extends Model{
    const TABLE_NAME = "HRIS_PREFERENCE_SETUP";
    
    const PREFERENCE_NAME_LIST = [
        "OVERTIME_REQUEST"=>"Overtime Request"
    ];
    const PREFERENCE_CONSTRAINT_LIST = [
        "OVERTIME_GRACE_TIME" => "Overtime Grace Time"
    ];
    const CONSTRAINT_TYPE_LIST = [
        'TEXT'=>'Text',
        'NUMBER'=>"Number",
        'DATE'=>"Date",
        'TIMESTAMP'=>"Timestamp",
        'HOUR'=>"Hour"
    ];
    const PREFERENCE_CONDITION_LIST =[
        "LESS_THAN"=>"Less than",
        "GREATER_THAN"=>"Greater than",
        "EQUAL"=>"Equal"
    ];
    const REQUEST_TYPE_LIST = [
        'RQ'=>"Pending",
        'AP'=>'Approved'
    ];
    const  EMPLOYEE_TYPE_LIST = [
        'R' => 'Regular',
        'C' => 'Contract'
    ];
    
    const PREFERENCE_ID = "PREFERENCE_ID";
    const COMPANY_ID = "COMPANY_ID";
    const PREFERENCE_NAME = "PREFERENCE_NAME";
    const PREFERENCE_CONSTRAINT = "PREFERENCE_CONSTRAINT";
    const CONSTRAINT_VALUE = "CONSTRAINT_VALUE";
    const CONSTRAINT_TYPE = "CONSTRAINT_TYPE";
    const PREFERENCE_CONDITION = "PREFERENCE_CONDITION";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const STATUS = "STATUS";
    const REQUEST_TYPE = "REQUEST_TYPE";
    const EMPLOYEE_TYPE = "EMPLOYEE_TYPE";
    
    public $preferenceId;
    public $companyId;
    public $preferenceName;
    public $preferenceConstraint;
    public $preferenceCondition;
    public $constraintValue;
    public $constraintType;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $status;
    public $requestType;
    public $employeeType;
    
    public $mappings = [
        'companyId'=>self::COMPANY_ID,
        'preferenceName'=>self::PREFERENCE_NAME,
        'preferenceId'=>self::PREFERENCE_ID,
        'preferenceCondition'=>self::PREFERENCE_CONDITION,
        'preferenceConstraint'=>self::PREFERENCE_CONSTRAINT,
        'constraintValue'=>self::CONSTRAINT_VALUE,
        'constraintType'=>self::CONSTRAINT_TYPE,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'status'=>self::STATUS,
        'requestType'=>self::REQUEST_TYPE,
        'employeeType'=>self::EMPLOYEE_TYPE
    ];
}
