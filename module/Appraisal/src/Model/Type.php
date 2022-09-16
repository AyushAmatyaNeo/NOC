<?php

namespace Appraisal\Model;

use Application\Model\Model;

class Type extends Model {

    const TABLE_NAME = "HRIS_APPRAISAL_TYPE";
    const APPRAISAL_TYPE_ID = "APPRAISAL_TYPE_ID";
    const APPRAISAL_TYPE_CODE = "APPRAISAL_TYPE_CODE";
    const APPRAISAL_TYPE_EDESC = "APPRAISAL_TYPE_EDESC";
    const APPRAISAL_TYPE_NDESC = "APPRAISAL_TYPE_NDESC";
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
    const DURATION_TYPE = "DURATION_TYPE";

    public $appraisalTypeId;
    public $appraisalTypeCode;
    public $appraisalTypeEdesc;
    public $appraisalTypeNdesc;
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
    public $durationType;
    public $mappings = [
        'appraisalTypeId' => self::APPRAISAL_TYPE_ID,
        'appraisalTypeCode' => self::APPRAISAL_TYPE_CODE,
        'appraisalTypeEdesc' => self::APPRAISAL_TYPE_EDESC,
        'appraisalTypeNdesc' => self::APPRAISAL_TYPE_NDESC,
        'companyId' => self::COMPANY_ID,
        'branchId' => self::BRANCH_ID,
        'createdBy' => self::CREATED_BY,
        'createdDate' => self::CREATED_DATE,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDate' => self::MODIFIED_DATE,
        'checked' => self::CHECKED,
        'approvedBy' => self::APPROVED_BY,
        'approvedDate' => self::APPROVED_DATE,
        'approved' => self::APPROVED,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'durationType' => self::DURATION_TYPE,
    ];

}
