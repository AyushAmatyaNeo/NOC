<?php

namespace Setup\Model;

use Application\Model\Model;

class Services extends Model {

    const TABLE_NAME = "HRIS_SERVICES";
    const SERVICE_ID = "SERVICE_ID";
    const SERVICE_NAME = "SERVICE_NAME";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const COMPANY_ID = "COMPANY_ID";


    public $servicesId;
    public $serviceName;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $companyId;

    public $mappings = [
        'servicesId' => self::SERVICE_ID,
        'serviceName' => self::SERVICE_NAME,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'companyId' => self::COMPANY_ID,
    ];

}
