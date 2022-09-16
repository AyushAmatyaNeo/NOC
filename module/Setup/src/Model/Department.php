<?php

namespace Setup\Model;

use Application\Model\Model;

class Department extends Model {

    const TABLE_NAME = "HRIS_DEPARTMENTS";
    const DEPARTMENT_ID = "DEPARTMENT_ID";
    const DEPARTMENT_CODE = "DEPARTMENT_CODE";
    const DEPARTMENT_NAME = "DEPARTMENT_NAME";
    const COUNTRY_ID = "COUNTRY_ID";
    const PARENT_DEPARTMENT = "PARENT_DEPARTMENT";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const BRANCH_ID = "BRANCH_ID";
    const COMPANY_ID = "COMPANY_ID";
    const DEPARTMENT_NDESC = "DEPARTMENT_NDESC";

    public $departmentId;
    public $departmentCode;
    public $departmentName;
    public $countryId;
    public $remarks;
    public $parentDepartment;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $branchId;
    public $companyId;
    public $departmentNdesc;
    public $mappings = [
        'departmentId' => self::DEPARTMENT_ID,
        'departmentCode' => self::DEPARTMENT_CODE,
        'departmentName' => self::DEPARTMENT_NAME,
        'countryId' => self::COUNTRY_ID,
        'parentDepartment' => self::PARENT_DEPARTMENT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'branchId' => self::BRANCH_ID,
        'companyId'=> self::COMPANY_ID,
        'departmentNdesc'=>self::DEPARTMENT_NDESC,
    ];

}
