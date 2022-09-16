<?php
namespace Appraisal\Model;

use Application\Model\Model;

class QuestionOption extends Model{
    const TABLE_NAME = "HRIS_APPRAISAL_QUESTION_OPTS";
    
    const OPTION_ID = "OPTION_ID";
    const OPTION_CODE = "OPTION_CODE";
    const QUESTION_ID = "QUESTION_ID";
    const OPTION_EDESC = "OPTION_EDESC";
    const OPTION_NDESC = "OPTION_NDESC";
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
    const STATUS = "STATUS";
    
    public $optionId;
    public $optionCode;
    public $questionId;
    public $optionEdesc;
    public $optionNdesc;
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
    public $status;
    
    public $mappings = [
        'optionId'=>self::OPTION_ID,
        'optionCode'=>self::OPTION_CODE,
        'questionId'=>self::QUESTION_ID,
        'optionEdesc'=>self::OPTION_EDESC,
        'optionNdesc'=>self::OPTION_NDESC,
        'companyId'=>self::COMPANY_ID,
        'branchId'=>self::BRANCH_ID,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'checked'=>self::CHECKED,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'approved'=>self::APPROVED,
        'status'=>self::STATUS
    ];
}