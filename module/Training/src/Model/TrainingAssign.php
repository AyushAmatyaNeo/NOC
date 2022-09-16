<?php
namespace Training\Model;

use Application\Model\Model;

class TrainingAssign extends Model{
    const TABLE_NAME = "HRIS_EMPLOYEE_TRAINING_ASSIGN";
    const TRAINING_ID = "TRAINING_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    
    public $trainingId;
    public $employeeId;
    public $remarks;
    public $status;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    
    public $mappings = [
        'trainingId'=>self::TRAINING_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE
    ];
}