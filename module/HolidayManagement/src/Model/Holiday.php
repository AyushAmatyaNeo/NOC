<?php

namespace HolidayManagement\Model;

use Application\Model\Model;

class Holiday extends Model {

    const TABLE_NAME = "HRIS_HOLIDAY_MASTER_SETUP";
    const HOLIDAY_ID = "HOLIDAY_ID";
    const HOLIDAY_CODE = "HOLIDAY_CODE";
    const HOLIDAY_ENAME = "HOLIDAY_ENAME";
    const HOLIDAY_LNAME = "HOLIDAY_LNAME";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
    const HALFDAY = "HALFDAY";
    const FISCAL_YEAR = "FISCAL_YEAR";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const ASSIGN_ON_EMPLOYEE_SETUP = "ASSIGN_ON_EMPLOYEE_SETUP";
    const COMPANY_ID = "COMPANY_ID";
    const BRANCH_ID = "BRANCH_ID";
    const DEPARTMENT_ID = "DEPARTMENT_ID";
    const DESIGNATION_ID = "DESIGNATION_ID";
    const POSITION_ID = "POSITION_ID";
    const SERVICE_TYPE_ID = "SERVICE_TYPE_ID";
    const EMPLOYEE_TYPE = "EMPLOYEE_TYPE";
    const GENDER_ID = "GENDER_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";

    public $holidayId;
    public $holidayCode;
    public $holidayEname;
    public $holidayLname;
    public $startDate;
    public $endDate;
    public $halfday;
    public $fiscalYear;
    public $createdDt;
    public $modifiedDt;
    public $status;
    public $remarks;
    public $createdBy;
    public $modifiedBy;
    public $assignOnEmployeeSetup;
    public $companyId;
    public $branchId;
    public $departmentId;
    public $designationId;
    public $positionId;
    public $serviceTypeId;
    public $employeeType;
    public $genderId;
    public $employeeId;
    public $mappings = [
        'holidayId' => self::HOLIDAY_ID,
        'holidayCode' => self::HOLIDAY_CODE,
        'holidayEname' => self::HOLIDAY_ENAME,
        'holidayLname' => self::HOLIDAY_LNAME,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'halfday' => self::HALFDAY,
        'fiscalYear' => self::FISCAL_YEAR,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'assignOnEmployeeSetup' => self::ASSIGN_ON_EMPLOYEE_SETUP,
        'companyId' => self::COMPANY_ID,
        'branchId' => self::BRANCH_ID,
        'departmentId' => self::DEPARTMENT_ID,
        'designationId' => self::DESIGNATION_ID,
        'positionId' => self::POSITION_ID,
        'serviceTypeId' => self::SERVICE_TYPE_ID,
        'employeeType' => self::EMPLOYEE_TYPE,
        'genderId' => self::GENDER_ID,
        'employeeId' => self::EMPLOYEE_ID,
    ];

}
