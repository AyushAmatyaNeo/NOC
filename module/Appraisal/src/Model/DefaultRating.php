<?php
namespace Appraisal\Model;

use Application\Model\Model;

class DefaultRating extends Model{
    const TABLE_NAME = "HRIS_APPRAISAL_DEFAULT";
    const ID = "ID";
    const MIN_VALUE = "MIN_VALUE";
    const MAX_VALUE = "MAX_VALUE";
    const APPRAISAL_TYPE_ID = "APPRAISAL_TYPE_ID";
    const DESIGNATION_IDS = "DESIGNATION_IDS";
    const POSITION_IDS = "POSITION_IDS";
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
    const DEFAULT_VALUE = "DEFAULT_VALUE";
    
    public $id;
    public $minValue;
    public $maxValue;
    public $appraisalTypeId;
    public $designationIds;
    public $positionIds;
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
    public $defaultValue;
    
    public $mappings = [
        'id'=>self::ID,
        'minValue'=>self::MIN_VALUE,
        'maxValue'=>self::MAX_VALUE,
        'appraisalTypeId'=>self::APPRAISAL_TYPE_ID,
        'designationIds'=>self::DESIGNATION_IDS,
        'positionIds'=>self::POSITION_IDS,
        'companyId'=>self::COMPANY_ID,
        'branchId'=>self::BRANCH_ID,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'checked'=>self::CHECKED,
        'approvedBy'=>self::APPROVED_BY,
        'approved'=>self::APPROVED,
        'approvedDate'=>self::APPROVED_DATE,
        'status'=>self::STATUS,
        'defaultValue'=>self::DEFAULT_VALUE
    ];
}
