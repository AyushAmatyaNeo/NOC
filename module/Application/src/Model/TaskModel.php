<?php

namespace Application\Model;

class TaskModel extends Model {

    const TABLE_NAME = "HRIS_TASK";
    const TASK_ID = "TASK_ID";
    const TASK_EDESC = "TASK_EDESC";
    const TASK_NDESC = "TASK_NDESC";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
    const ESTIMATED_TIME = "ESTIMATED_TIME";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const STATUS = "STATUS";
    const TASK_PRIORITY = "TASK_PRIORITY";
    const REMARKS = "REMARKS";
    const COMPANY_ID = "COMPANY_ID";
    const BRANCH_ID = "BRANCH_ID";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const APPROVED_FLAG = "APPROVED_FLAG";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const DELETED_FLAG = "DELETED_FLAG";
    const TASK_TITLE = "TASK_TITLE";

    
    public $taskId;
    public $taskEdesc;
    public $taskNdesc;
    public $startDate;
    public $endDate;
    public $estimatedTime;
    public $employeeId;
    public $status;
    public $taskPriority;
    public $remarks;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $approvedFlag;
    public $approvedBy;
    public $approvedDate;
    public $deletedFlag;
    public $taskTitle;
    
    
    public $mappings = [
    'taskId' => self::TASK_ID,
    'taskEdesc' => self::TASK_EDESC,
    'taskNdesc' => self::TASK_NDESC,
    'startDate' => self::START_DATE,
    'endDate' => self::END_DATE,
    'estimatedTime' => self::ESTIMATED_TIME,
    'employeeId' => self::EMPLOYEE_ID,
    'status' => self::STATUS,
    'taskPriority' => self::TASK_PRIORITY,
    'remarks' => self::REMARKS,
    'companyId' => self::COMPANY_ID,
    'branchId' => self::BRANCH_ID,
    'createdBy' => self::CREATED_BY,
    'createdDt' => self::CREATED_DT,
    'modifiedBy' => self::MODIFIED_BY,
    'modifiedDt' => self::MODIFIED_DT,
    'approvedFlag' => self::APPROVED_FLAG,
    'approvedBy' => self::APPROVED_BY,
    'approvedDate' => self::APPROVED_DATE,
    'deletedFlag' => self::DELETED_FLAG,
    'taskTitle' => self::TASK_TITLE,
    ];
    
}
