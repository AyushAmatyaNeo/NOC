<?php

namespace Setup\Model;

use Application\Model\Model;

class ServiceType extends Model {

    const TABLE_NAME = "HRIS_SERVICE_TYPES";
    const SERVICE_TYPE_ID = "SERVICE_TYPE_ID";
    const SERVICE_TYPE_CODE = "SERVICE_TYPE_CODE";
    const SERVICE_TYPE_NAME = "SERVICE_TYPE_NAME";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const TYPE = "TYPE";
    const PF_ELIGIBLE = "PF_ELIGIBLE";

    public $serviceTypeId;
    public $serviceTypeCode;
    public $serviceTypeName;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $type;
    public $pfEligible;
    public $mappings = [
        'serviceTypeId' => self::SERVICE_TYPE_ID,
        'serviceTypeCode' => self::SERVICE_TYPE_CODE,
        'serviceTypeName' => self::SERVICE_TYPE_NAME,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'type' => self::TYPE,
        'pfEligible' => self::PF_ELIGIBLE
    ];

}
