<?php
namespace Setup\Model;

use Application\Model\Model;

class ServiceQuestion extends Model{
    const TABLE_NAME = "HRIS_SERVICE_QA";
    
    const QA_ID = "QA_ID";
    const PARENT_QA_ID = "PARENT_QA_ID";
    const SERVICE_EVENT_TYPE_ID = "SERVICE_EVENT_TYPE_ID";
    const QUESTION_EDESC = "QUESTION_EDESC";
    const QUESTION_NDESC = "QUESTION_NDESC";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const COMPANY_ID = "COMPANY_ID";
    const BRANCH_ID = "BRANCH_ID";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const CHECKED = "CHECKED";
    const APPROVED = "APPROVED";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const QA_INDEX = "QA_INDEX";
    
    public $qaId;
    public $parentQaId;
    public $serviceEventTypeId;
    public $questionEdesc;
    public $questionNdesc;
    public $remarks;
    public $status;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $checked;
    public $approved;
    public $approvedBy;
    public $approvedDate;
    public $qaIndex;
    
    public $mappings = [
        'qaId'=>self::QA_ID,
        'parentQaId'=>self::PARENT_QA_ID,
        'serviceEventTypeId'=>self::SERVICE_EVENT_TYPE_ID,
        'questionEdesc'=>self::QUESTION_EDESC,
        'questionNdesc'=>self::QUESTION_NDESC,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'companyId'=>self::COMPANY_ID,
        'branchId'=>self::BRANCH_ID,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'checked'=>self::CHECKED,
        'approved'=>self::APPROVED,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'qaIndex'=>self::QA_INDEX
    ];
    
}

