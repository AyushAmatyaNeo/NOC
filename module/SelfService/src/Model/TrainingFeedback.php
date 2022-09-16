<?php
namespace SelfService\Model;

use Application\Model\Model;

class TrainingFeedback extends Model{
    const TABLE_NAME = "NOC_TRAINING_FEEDBACK";
    const FEEDBACK_ID = "FEEDBACK_ID";
    const TRAINING_ID = "TRAINING_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const DEPARTMENT_ID = "DEPARTMENT_ID";
    const TRAINING_FEEDBACK = "TRAINING_FEEDBACK";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";
    
    public $feedbackId;
    public $trainingId;
    public $employeeId;
    public $departmentId;
    public $trainingFeedback;
    public $remarks;
    public $status;
    public $createdBy;
    public $createdDt;
    public $deletedBy;
    public $deletedDt;
    
    public $mappings = [
        'feedbackId'=>self::FEEDBACK_ID,
        'trainingId'=>self::TRAINING_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'departmentId'=>self::DEPARTMENT_ID,
        'trainingFeedback'=>self::TRAINING_FEEDBACK,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'createdBy'=>self::CREATED_BY,
        'createdDt'=>self::CREATED_DT,
        'deletedBy'=>self::DELETED_BY,
        'deletedDt'=>self::DELETED_DT,
    ];
}