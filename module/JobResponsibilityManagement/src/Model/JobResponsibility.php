<?php

namespace JobResponsibilityManagement\Model;

use Application\Model\Model;

class JobResponsibility extends Model {

    const TABLE_NAME = "HRIS_JOB_RESPONSIBILITY";
    const ID = "ID";
    const JOB_RES_ENG_NAME = "JOB_RES_ENG_NAME";
    const JOB_RES_NEP_NAME = "JOB_RES_NEP_NAME";
    const JOB_RES_ENG_DESCRIPTION = "JOB_RES_ENG_DESCRIPTION";
    const JOB_RES_NEP_DESCRIPTION = "JOB_RES_NEP_DESCRIPTION";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";

    public $id;           
    public $jobResEngName;
    public $jobResNepName;
    public $jobResEngDescription;
    public $jobResNepDescription;
    public $status;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $deletedBy;
    public $deletedDt;
    public $mappings = [
        'id' => self::ID,
        'jobResEngName' => self::JOB_RES_ENG_NAME,
        'jobResNepName' => self::JOB_RES_NEP_NAME,
        'jobResEngDescription' => self::JOB_RES_ENG_DESCRIPTION,
        'jobResNepDescription' => self::JOB_RES_NEP_DESCRIPTION,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'deletedBy' => self::DELETED_BY,
        'deletedDt' => self::DELETED_DT
    ];

}
