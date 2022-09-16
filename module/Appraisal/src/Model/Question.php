<?php
namespace Appraisal\Model;

use Application\Model\Model;

class Question extends Model{
    const TABLE_NAME = "HRIS_APPRAISAL_QUESTION";
    
    const QUESTION_ID = "QUESTION_ID";
    const QUESTION_CODE = "QUESTION_CODE"; 
    const HEADING_ID = "HEADING_ID";
    const QUESTION_EDESC = "QUESTION_EDESC";
    const QUESTION_NDESC = "QUESTION_NDESC";
    const APPRAISER_FLAG = "APPRAISER_FLAG";
    const APPRAISEE_FLAG = "APPRAISEE_FLAG";
    const REVIEWER_FLAG = "REVIEWER_FLAG";
    const APPRAISEE_RATING = "APPRAISEE_RATING";
    const REVIEWER_RATING = "REVIEWER_RATING";
    const APPRAISER_RATING = "APPRAISER_RATING";
    const MAX_VALUE = "MAX_VALUE";
    const MIN_VALUE = "MIN_VALUE";
    const ORDER_NO = "ORDER_NO";
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
    const REMARKS = "REMARKS";
    const ANSWER_TYPE = "ANSWER_TYPE";
    
    public $questionId;
    public $questionCode;
    public $headingId;
    public $questionEdesc;
    public $questionNdesc;
    public $appraiseeFlag;
    public $appraiserFlag;
    public $reviewerFlag;
    public $appraiseeRating;
    public $appraiserRating;
    public $reviewerRating;
    public $maxValue;
    public $minValue;
    public $orderNo;
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
    public $remarks;
    public $answerType;
    
    public $mappings = [
        'questionId'=>self::QUESTION_ID,
        'questionCode'=>self::QUESTION_CODE,
        'headingId'=>self::HEADING_ID,
        'questionEdesc'=>self::QUESTION_EDESC,
        'questionNdesc'=>self::QUESTION_NDESC,
        'appraiseeFlag'=>self::APPRAISEE_FLAG,
        'appraiserFlag'=>self::APPRAISER_FLAG,
        'reviewerFlag'=>self::REVIEWER_FLAG,
        'appraiseeRating'=>self::APPRAISEE_RATING,
        'appraiserRating'=>self::APPRAISER_RATING,
        'reviewerRating'=>self::REVIEWER_RATING,
        'minValue'=>self::MIN_VALUE,
        'maxValue'=>self::MAX_VALUE,
        'answerType'=>self::ANSWER_TYPE,
        'orderNo'=>self::ORDER_NO,
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
        'status'=>self::STATUS,
        'remarks'=>self::REMARKS,
    ];
}
