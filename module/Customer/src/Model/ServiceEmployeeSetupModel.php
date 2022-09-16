<?php

namespace Customer\Model;

use Application\Model\Model;

class ServiceEmployeeSetupModel extends Model {

    CONST TABLE_NAME = "HRIS_SERVICE_EMPLOYEES";
    const EMPLOYEE_ID = "EMPLOYEE_ID";

    public $employeeId;

    const EMPLOYEE_CODE = "EMPLOYEE_CODE";

    public $employeeCode;

    const FIRST_NAME = 'FIRST_NAME';

    public $firstName;

    const MIDDLE_NAME = 'MIDDLE_NAME';

    public $middleName;

    const LAST_NAME = 'LAST_NAME';

    public $lastName;

    const FULL_NAME = "FULL_NAME";

    public $fullName;

    const GENDER_ID = 'GENDER_ID';

    public $genderId;

    const BLOOD_GROUP_ID = "BLOOD_GROUP_ID";

    public $bloodGroupId;

    const TELEPHONE_NO = 'TELEPHONE_NO';

    public $telephoneNo;

    const MOBILE_NO = 'MOBILE_NO';

    public $mobileNo;

    const EMAIL = "EMAIL";

    public $email;

    const CITIZENSHIP_NO = "CITIZENSHIP_NO";

    public $citizenshipNo;

    const CITIZENSHIP_ISSUE_DATE = "CITIZENSHIP_ISSUE_DATE";

    public $citizenshipIssueDate;

    const CITIZENSHIP_ISSUE_PLACE = "CITIZENSHIP_ISSUE_PLACE";

    public $citizenshipIssuePlace;

    const PERMANENT_ZONE_ID = "PERMANENT_ZONE_ID";

    public $permanentZoneId;

    const PERMANENT_DISTRICT_ID = "PERMANENT_DISTRICT_ID";

    public $permanentDistrictId;

    const TEMPORARY_ZONE_ID = "TEMPORARY_ZONE_ID";
    
    public $temporaryZoneId;

    const TEMPORARY_DISTRICT_ID = "TEMPORARY_DISTRICT_ID";
    
    public $temporaryDistrictId;

    const STATUS = "STATUS";

    public $status;

    const CREATED_BY = "CREATED_BY";

    public $createdBy;

    const CREATED_DT = "CREATED_DT";

    public $createdDt;

    const MODIFIED_BY = "MODIFIED_BY";

    public $modifiedBy;

    const MODIFIED_DT = "MODIFIED_DT";

    public $modifiedDt;

    const REMARKS = "REMARKS";
    
    public $remarks;
    

    const EMPLOYEE_TYPE = "EMPLOYEE_TYPE";
    public $employeeType;

    const ACCOUNT_NO = "ACCOUNT_NO";
    public $accountNo;
    const BRANCH_ID = "BRANCH_ID";
    public $branchId;
    const DEPARTMENT_ID = "DEPARTMENT_ID";
    public $departmentId;
    const DESIGNATION_ID = "DESIGNATION_ID";
    public $designationId;
    const POSITION_ID = "POSITION_ID";
    public $positionId;
    

    
    
    

    
    public $mappings = [
        'employeeId' => self::EMPLOYEE_ID,
        'employeeCode' => self::EMPLOYEE_CODE,
        'firstName' => self::FIRST_NAME,
        'middleName' => self::MIDDLE_NAME,
        'lastName' => self::LAST_NAME,
        'fullName' => self::FULL_NAME,
        'genderId' => self::GENDER_ID,
        'bloodGroupId' => self::BLOOD_GROUP_ID,
        'telephoneNo' => self::TELEPHONE_NO,
        'mobileNo' => self::MOBILE_NO,
        'email' => self::EMAIL,
        'citizenshipNo' => self::CITIZENSHIP_NO,
        'citizenshipIssueDate' => self::CITIZENSHIP_ISSUE_DATE,
        'citizenshipIssuePlace' => self::CITIZENSHIP_ISSUE_PLACE,
        'permanentZoneId' => self::PERMANENT_ZONE_ID,
        'permanentDistrictId' => self::PERMANENT_DISTRICT_ID,
        'temporaryZoneId' => self::TEMPORARY_ZONE_ID,
        'temporaryDistrictId' => self::TEMPORARY_DISTRICT_ID,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'employeeType' => self::EMPLOYEE_TYPE,
        'accountNo' => self::ACCOUNT_NO,
        'branchId' => self::BRANCH_ID,
        'departmentId' => self::DEPARTMENT_ID,
        'designationId' => self::DESIGNATION_ID,
        'positionId' => self::POSITION_ID
    ];

}
