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
    const ASSIGNED_BY = "ASSIGNED_BY";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";

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
    public $assignedBy;
    public $startDate;
    public $endDate;
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
        'deletedDt' => self::DELETED_DT,
        'assignedBy' => self::ASSIGNED_BY,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE
    ];

}
