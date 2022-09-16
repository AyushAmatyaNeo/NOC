<?php
namespace Setup\Model;

use Application\Model\Model;

class EmployeeExperience extends Model{
    const TABLE_NAME = "HRIS_EMPLOYEE_EXPERIENCES";
    
    const ID = "ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const ORGANIZATION_NAME = "ORGANIZATION_NAME";
    const ORGANIZATION_TYPE = "ORGANIZATION_TYPE";
    const POSITION = "POSITION";
    const FROM_DATE = "FROM_DATE";
    const TO_DATE = "TO_DATE";
    const REMARKS = "REMARKS";
    const COMPANY_ID = "COMPANY_ID";
    const BRANCH_ID = "BRANCH_ID";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const APPROVED = "APPROVED";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const STATUS = "STATUS";
    const ADDRESS = "ADDRESS";
    
    public $id;
    public $employeeId;
    public $organizationName;
    public $organizationType;
    public $position;
    public $fromDate;
    public $toDate;
    public $remarks;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $approved;
    public $approvedBy;
    public $approvedDate;
    public $status;
    public $address;
    
    public $mappings= [
        'id'=>self::ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'organizationName'=>self::ORGANIZATION_NAME,
        'organizationType'=>self::ORGANIZATION_TYPE,
        'position'=>self::POSITION,
        'fromDate'=>self::FROM_DATE,
        'toDate'=>self::TO_DATE,
        'remarks'=>self::REMARKS,
        'companyId'=>self::COMPANY_ID,
        'branchId'=>self::BRANCH_ID,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'approved'=>self::APPROVED,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'status'=>self::STATUS,
        'address'=>self::ADDRESS

    ];
}