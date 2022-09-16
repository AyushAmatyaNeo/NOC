<?php
namespace Appraisal\Model;

use Application\Model\Model;

class Setup extends Model{
    const TABLE_NAME = "HRIS_APPRAISAL_SETUP";
    
    const APPRAISAL_ID = "APPRAISAL_ID";
    const APPRAISAL_CODE = "APPRAISAL_CODE";
    const APPRAISAL_EDESC = "APPRAISAL_EDESC";
    const APPRAISAL_NDESC = "APPRAISAL_NDESC";
    const APPRAISAL_TYPE_ID = "APPRAISAL_TYPE_ID";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
    const CURRENT_STAGE_ID = "CURRENT_STAGE_ID";
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
    const KPI_SETTING = "KPI_SETTING";
    const COMPETENCIES_SETTING = "COMPETENCIES_SETTING";
    const HR_FEEDBACK_ENABLE = "HR_FEEDBACK_ENABLE";
    
    public $appraisalId;
    public $appraisalCode;
    public $appraisalEdesc;
    public $appraisalNdesc;
    public $appraisalTypeId;
    public $startDate;
    public $endDate;
    public $currentStageId;
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
    public $kpiSetting;
    public $competenciesSetting;
    public $hrFeedbackEnable;
    
    public $mappings = [
        "appraisalId"=>self::APPRAISAL_ID,
        'appraisalCode'=>self::APPRAISAL_CODE,
        'appraisalEdesc'=>self::APPRAISAL_EDESC,
        'appraisalNdesc'=>self::APPRAISAL_NDESC,
        'appraisalTypeId'=>self::APPRAISAL_TYPE_ID,
        'startDate'=>self::START_DATE,
        'endDate'=>self::END_DATE,
        'currentStageId'=>self::CURRENT_STAGE_ID,
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
        'kpiSetting'=>self::KPI_SETTING,
        'competenciesSetting'=>self::COMPETENCIES_SETTING,
        'hrFeedbackEnable'=>self::HR_FEEDBACK_ENABLE
    ]; 
}
