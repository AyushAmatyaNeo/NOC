<?php
namespace Appraisal\Model;

use Application\Model\Model;

class Heading extends Model{
    const TABLE_NAME = "HRIS_APPRAISAL_HEADING";
    
    const HEADING_ID = "HEADING_ID";
    const HEADING_CODE = "HEADING_CODE";
    const APPRAISAL_TYPE_ID = "APPRAISAL_TYPE_ID";
    const PERCENTAGE = "PERCENTAGE";
    const HEADING_EDESC = "HEADING_EDESC";
    const HEADING_NDESC = "HEADING_NDESC";
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
    
    public $headingId;
    public $headingCode;
    public $appraisalTypeId;
    public $percentage;
    public $headingEdesc;
    public $headingNdesc;
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
        'headingId'=>self::HEADING_ID,
        'headingCode'=>self::HEADING_CODE,
        'appraisalTypeId'=>self::APPRAISAL_TYPE_ID,
        'percentage'=>self::PERCENTAGE,
        'headingEdesc'=>self::HEADING_EDESC,
        'headingNdesc'=>self::HEADING_NDESC,
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