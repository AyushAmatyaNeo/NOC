<?php

namespace Setup\Model;

use Application\Model\Model;

class JobHistory extends Model {

    const TABLE_NAME = "HRIS_JOB_HISTORY";
    const JOB_HISTORY_ID = "JOB_HISTORY_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
    const SERVICE_EVENT_TYPE_ID = "SERVICE_EVENT_TYPE_ID";
    const TO_BRANCH_ID = "TO_BRANCH_ID";
    const TO_DEPARTMENT_ID = "TO_DEPARTMENT_ID";
    const TO_DESIGNATION_ID = "TO_DESIGNATION_ID";
    const TO_POSITION_ID = "TO_POSITION_ID";
    const TO_SERVICE_TYPE_ID = "TO_SERVICE_TYPE_ID";
    const TO_COMPANY_ID = "TO_COMPANY_ID";
    const TO_SALARY = "TO_SALARY";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const RETIRED_FLAG = "RETIRED_FLAG";
    const DISABLED_FLAG = "DISABLED_FLAG";
    const EVENT_DATE = "EVENT_DATE";
    const FILE_ID = "FILE_ID";
    const ROLES = "ROLES";
    const TO_FUNCTIONAL_LEVEL = "TO_FUNCTIONAL_LEVEL";
    const FROM_BRANCH_ID ="FROM_BRANCH_ID";
    const FROM_DESIGNATION_ID ="FROM_DESIGNATION_ID";
    const FROM_DEPARTMENT_ID= "FROM_DEPARTMENT_ID";
    const FROM_POSITION_ID ="FROM_POSITION_ID";
    const FROM_SERVICE_TYPE_ID="FROM_SERVICE_TYPE_ID";
    const FROM_COMPANY_ID="FROM_COMPANY_ID";
    const FROM_SALARY="FROM_SALARY";
    const FROM_FUNCTIONAL_LEVEL="FROM_FUNCTIONAL_LEVEL";
    const FROM_LOCATION_ID="FROM_LOCATION_ID";
    const TO_LOCATION_ID="TO_LOCATION_ID";
    const FROM_ACTING_POSITION_ID="FROM_ACTING_POSITION_ID";
    const TO_ACTING_POSITION_ID="TO_ACTING_POSITION_ID";
    const TO_ACTING_FUNCTIONAL_LEVEL_ID ="TO_ACTING_FUNCTIONAL_LEVEL_ID";
    const TO_SERIVCE_GROUP_ID="TO_SERVICE_GROUP_ID";
    const TO_SERVICE_SUB_GROUP_ID ="TO_SERVICE_SUB_GROUP_ID";

    public $jobHistoryId;
    public $employeeId;
    public $startDate;
    public $endDate;
    public $serviceEventTypeId;
    public $toServiceTypeId;
    public $toBranchId;
    public $toDepartmentId;
    public $toDesignationId;
    public $toPositionId;
    public $toCompanyId;
    public $toSalary;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $retiredFlag;
    public $disabledFlag;
    public $eventDate;
    public $fileId;
    public $roles;
    public $toFunctionalLevelId;
    public $fromBranchId;
    public $fromDesignationId;
    public $fromDepartmentId;
    public $fromPositionId;
    public $fromSeviceTypeId;
    public $fromCompanyId;
    public $fromSalary;
    public $fromFunctionalLevelId;
    public $fromLocationId;
    public $toLocationId;
    public $toActingPositionId;
    public $fromActingPositionId;
    public $toActingFunctionalLevelId;
    public $toServiceGroupId;
    public $toServiceSubGroupId;
    public $mappings = [
        'jobHistoryId' => self::JOB_HISTORY_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'serviceEventTypeId' => self::SERVICE_EVENT_TYPE_ID,
        'toServiceTypeId' => self::TO_SERVICE_TYPE_ID,
        'toBranchId' => self::TO_BRANCH_ID,
        'toDepartmentId' => self::TO_DEPARTMENT_ID,
        'toDesignationId' => self::TO_DESIGNATION_ID,
        'toPositionId' => self::TO_POSITION_ID,
        'toCompanyId' => self::TO_COMPANY_ID,
        'toSalary' => self::TO_SALARY,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'retiredFlag' => self::RETIRED_FLAG,
        'disabledFlag' => self::DISABLED_FLAG,
        'eventDate' => self::EVENT_DATE,
        'fileId' => self::FILE_ID,
        'roles' => self::ROLES,
        'toFunctionalLevelId' => self::TO_FUNCTIONAL_LEVEL,
        'fromBranchId' =>self::FROM_BRANCH_ID,
        'fromDesignationId'=>self::FROM_DESIGNATION_ID,
        'fromDepartmentId'=>self::FROM_DEPARTMENT_ID,
        'fromPositionId' =>self::FROM_POSITION_ID,
        'fromSeviceTypeId'=>self::FROM_SERVICE_TYPE_ID,
        'fromCompanyId'=>self::FROM_COMPANY_ID,
        'fromSalary'=>self::FROM_SALARY,
        'fromFunctionalLevelId' =>self::FROM_FUNCTIONAL_LEVEL ,
        'fromLocationId' =>self::FROM_LOCATION_ID,
        'toLocationId' =>self::TO_LOCATION_ID,
        'fromActingPositionId' =>self::FROM_ACTING_POSITION_ID,
        'toActingPositionId' =>self::TO_ACTING_POSITION_ID,
        'toActingFunctionalLevelId'=>self::TO_ACTING_FUNCTIONAL_LEVEL_ID,
        'toServiceGroupId' => self::TO_SERIVCE_GROUP_ID,
        'toServiceSubGroupId' => self::TO_SERVICE_SUB_GROUP_ID,
    ];


}
