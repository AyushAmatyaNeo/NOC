<?php
namespace Appraisal\Model;

use Application\Model\Model;

class AppraisalAssign extends Model{
    const TABLE_NAME = "HRIS_APPRAISAL_ASSIGN";
    
    const APPRAISAL_ID = "APPRAISAL_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const APPRAISER_ID = "APPRAISER_ID";
    const REVIEWER_ID = "REVIEWER_ID";
    const REMARKS = "REMARKS";
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
    const CURRENT_STAGE_ID = "CURRENT_STAGE_ID";
    const ANNUAL_RATING_KPI = "ANNUAL_RATING_KPI";
    const ANNUAL_RATING_COMPETENCY = "ANNUAL_RATING_COMPETENCY";
    const APPRAISER_OVERALL_RATING = "APPRAISER_OVERALL_RATING";
    const ALT_APPRAISER_ID = "ALT_APPRAISER_ID";
    const ALT_REVIEWER_ID = "ALT_REVIEWER_ID";
    const SUPER_REVIEWER_ID = "SUPER_REVIEWER_ID";

    public $appraisalId;
    public $employeeId;
    public $appraiserId;
    public $reviewerId;
    public $remarks;
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
    public $currentStageId;
    public $annualRatingKPI;
    public $annualRatingCompetency;
    public $appraiserOverallRating;
    public $altAppraiserId;
    public $altReviewerId;
    public $superReviewerId;
    
    public $mappings = [
        'appraisalId'=>self::APPRAISAL_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'appraiserId'=>self::APPRAISER_ID,
        'reviewerId'=>self::REVIEWER_ID,
        'remarks'=>self::REMARKS,
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
        'currentStageId'=>self::CURRENT_STAGE_ID,
        'annualRatingKPI'=>self::ANNUAL_RATING_KPI,
        'annualRatingCompetency'=>self::ANNUAL_RATING_COMPETENCY,
        'appraiserOverallRating'=>self::APPRAISER_OVERALL_RATING,
        'altAppraiserId'=>self::ALT_APPRAISER_ID,
        'altReviewerId'=>self::ALT_REVIEWER_ID,
        'superReviewerId'=>self::SUPER_REVIEWER_ID
    ];
}