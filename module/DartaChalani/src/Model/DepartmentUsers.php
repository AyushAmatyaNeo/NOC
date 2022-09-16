<?php

namespace DartaChalani\Model;

use Application\Model\Model;

class DepartmentUsers extends Model {

    const TABLE_NAME = "DC_DEPARTMENTS_USERS";
    
    const DU_ID = "DU_ID";
    const DEPARTMENT_ID = "DEPARTMENT_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
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

    
    public $duId;
    public $departmentId;
    public $employeeId;
    
    public $remarks;
    public $status;
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

        
        'duId' => self::DU_ID,
        'departmentId' => self::DEPARTMENT_ID,
        'employeeId' => self::EMPLOYEE_ID,

        
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
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
        'locationId' => self::LOCATION_ID,
    ];

}
