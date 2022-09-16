<?php

namespace DartaChalani\Model;

use Application\Model\Model;

class UserAssign extends Model {

    const TABLE_NAME = "DC_USER_ASSIGN";
    const ASSIGN_ID = "ASSIGN_ID";
    const DEPTARTMENT_ID = "DEPTARTMENT_ID";
    const BRANCH_ID = "BRANCH_ID";
    const PROCESS_ID = "PROCESS_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const DISPATCH_DRAFT_ID = "DISPATCH_DRAFT_ID";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const CHECKED_BY = "CHECKED_BY";
    const CHECKED_DATE = "CHECKED_DATE";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";
    const LOCATION_ID = "LOCATION_ID";

    
    public $assignId;
    public $departmentId;
    public $branchId;
    public $processId;
    public $employeeId;
    public $dispatchDraftId;
    public $status;
    public $remarks;
    public $createdBy;
    public $createdDt;
    public $checkedBy;
    public $checkedDt;
    public $approvedBy;
    public $approvedDt;
    public $modifiedBy;
    public $modifiedDt;
    public $deletedBy;
    public $deletedDt;
    public $locationId;
    public $mappings = [

        'assignId' => self::ASSIGN_ID,
        'departmentId' => self::DEPTARTMENT_ID,
        'branchId' => self::BRANCH_ID,
        'processId' => self::PROCESS_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'dispatchDraftId' => self::DISPATCH_DRAFT_ID,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'checkedBy' => self::CHECKED_BY,
        'checkedDt' => self::CHECKED_DATE,
        'approvedBy' => self::APPROVED_BY,
        'approvedDt' => self::APPROVED_DATE,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'deletedBy' => self::DELETED_BY,
        'deletedDt' => self::DELETED_DT,
        'locationId' => self::LOCATION_ID
    ];

}
