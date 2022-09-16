<?php

namespace Setup\Model;

use Application\Model\Model;

class Designation extends Model {

    const TABLE_NAME = "HRIS_DESIGNATIONS";
    const DESIGNATION_ID = "DESIGNATION_ID";
    const DESIGNATION_CODE = "DESIGNATION_CODE";
    const DESIGNATION_TITLE = "DESIGNATION_TITLE";
    const BASIC_SALARY = "BASIC_SALARY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const PARENT_DESIGNATION = "PARENT_DESIGNATION";
    const WITHIN_BRANCH = "WITHIN_BRANCH";
    const WITHIN_DEPARTMENT = "WITHIN_DEPARTMENT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const COMPANY_ID = "COMPANY_ID";
    const ORDER_NO = "ORDER_NO";

    public $designationId;
    public $designationCode;
    public $designationTitle;
    public $basicSalary;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $parentDesignation;
    public $withinBranch;
    public $withinDepartment;
    public $createdBy;
    public $modifiedBy;
    public $companyId;
    public $orderNo;
    public $mappings = [
        'designationId' => self::DESIGNATION_ID,
        'designationCode' => self::DESIGNATION_CODE,
        'designationTitle' => self::DESIGNATION_TITLE,
        'basicSalary' => self::BASIC_SALARY,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'parentDesignation' => self::PARENT_DESIGNATION,
        'withinBranch' => self::WITHIN_BRANCH,
        'withinDepartment' => self::WITHIN_DEPARTMENT,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'companyId' => self::COMPANY_ID,
        'orderNo' => self::ORDER_NO,
    ];

}
