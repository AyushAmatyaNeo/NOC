<?php
namespace SelfService\Model;

use Application\Model\Model;

class AppraisalKPI extends Model{
    const TABLE_NAME = "HRIS_APPRAISAL_KPI";
    
    const SNO = "SNO";
    const APPRAISAL_ID = "APPRAISAL_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const TITLE = "TITLE";
    const SUCCESS_CRITERIA = "SUCCESS_CRITERIA";
    const WEIGHT = "WEIGHT";
    const KEY_ACHIEVEMENT = "KEY_ACHIEVEMENT";
    const SELF_RATING = "SELF_RATING";
    const APPRAISER_RATING = "APPRAISER_RATING";
    const REVIEWER_RATING = "REVIEWER_RATING";
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
    
    public $sno;
    public $appraisalId;
    public $employeeId;
    public $title;
    public $successCriteria;
    public $weight;
    public $keyAchievement;
    public $selfRating;
    public $appraiserRating;
    public $reviewerRating;
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
        'sno'=>self::SNO,
        'appraisalId'=>self::APPRAISAL_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'title'=>self::TITLE,
        'successCriteria'=>self::SUCCESS_CRITERIA,
        'weight'=>self::WEIGHT,
        'keyAchievement'=>self::KEY_ACHIEVEMENT,
        'selfRating'=>self::SELF_RATING,
        'appraiserRating'=>self::APPRAISER_RATING,
        'reviewerRating'=>self::REVIEWER_RATING,
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

