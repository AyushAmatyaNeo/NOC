<?php
namespace Appraisal\Model;

use Application\Model\Model;

class Stage extends Model{
    const TABLE_NAME="HRIS_APPRAISAL_STAGE";
    
    const STAGE_ID = "STAGE_ID";
    const STAGE_CODE = "STAGE_CODE";
    const STAGE_EDESC = "STAGE_EDESC";
    const STAGE_NDESC = "STAGE_NDESC";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
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
    const INSTRUCTION = "INSTRUCTION";
   
    public $stageId;
    public $stageCode;
    public $stageEdesc;
    public $stageNdesc;
    public $startDate;
    public $endDate;
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
    public $instruction;
    
    public $mappings = [
        'stageId'=>self::STAGE_ID,
        'stageCode'=>self::STAGE_CODE,
        'stageEdesc'=>self::STAGE_EDESC,
        'stageNdesc'=>self::STAGE_NDESC,
        'startDate'=>self::START_DATE,
        'endDate'=>self::END_DATE,
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
        'instruction'=>self::INSTRUCTION
    ];
}