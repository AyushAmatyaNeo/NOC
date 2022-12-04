<?php

namespace JobResponsibilityManagement\Model;

use Application\Model\Model;

class JobResponsibilityAssign extends Model {

    const TABLE_NAME = "HRIS_EMPLOYEE_JOB_RESPONSIBILITY_ASSIGN";
    const ID = "ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const JOB_RESPONSIBILITY_ID = "JOB_RESPONSIBILITY_ID";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";

    public $id;           
    public $employeeId;
    public $jobResponsibilityId;
    public $remarks;
    public $status;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $deletedBy;
    public $deletedDt;
    public $mappings = [
        'id' => self::ID,
        'employeeId' => self::EMPLOYEE_ID,
        'jobResponsibilityId' => self::JOB_RESPONSIBILITY_ID,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'deletedBy' => self::DELETED_BY,
        'deletedDt' => self::DELETED_DT
    ];

}
