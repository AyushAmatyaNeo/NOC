<?php
namespace Appraisal\Model;

use Application\Model\Model;
class AppraisalAnswer extends Model{
    const TABLE_NAME = "HRIS_APPRAISAL_ANSWER";
    
    const ANSWER_ID = "ANSWER_ID";
    const APPRAISAL_ID = "APPRAISAL_ID";
    const QUESTION_ID = "QUESTION_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const USER_ID = "USER_ID";
    const ANSWER = "ANSWER";
    const RATING = "RATING";
    const STAGE_ID = "STAGE_ID";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const COMPANY_ID = "COMPANY_ID";
    const BRANCH_ID = "BRANCH_ID";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const CHECKED = "CHECKED";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const APPROVED = "APPROVED";
    
    public $answerId;
    public $appraisalId;
    public $questionId;
    public $employeeId;
    public $userId;
    public $answer;
    public $rating;
    public $stageId;
    public $remarks;
    public $status;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $checked;
    public $approvedBy;
    public $approvedDate;
    public $approved;
    
    public $mappings = [
        'answerId'=>self::ANSWER_ID,
        'appraisalId'=>self::APPRAISAL_ID,
        'questionId'=>self::QUESTION_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'userId'=>self::USER_ID,
        'answer'=>self::ANSWER,
        'rating'=>self::RATING,
        'stageId'=>self::STAGE_ID,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'companyId'=>self::COMPANY_ID,
        'branchId'=>self::BRANCH_ID,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'checked'=>self::CHECKED,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'approved'=>self::APPROVED
    ];
}
