<?php
namespace Workforce\Model;

use Application\Model\Model;

class HeadOfficeModel extends Model{
    const TABLE_NAME = "HRIS_WORKFORCE";
    const WORKFORCE_ID = "WORKFORCE_ID";
    const COMPANY_ID = "COMPANY_ID";
    const LOCATION_ID = "LOCATION_ID";
    const DEPARTMENT_ID = "DEPARTMENT_ID";
    const POSITION_ID = "POSITION_ID";
    const SERVICE_GROUP_ID = "SERVICE_GROUP_ID";
    const SERVICE_TYPE_ID = "SERVICE_TYPE_ID";
    const DESIGNATION_ID = "DESIGNATION_ID";
    const QUOTA = "QUOTA";
    const REMARKS = "REMARKS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const CHECKED_BY = "CHECKED_BY";
    const CHECKED_DT = "CHECKED_DT";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DT = "APPROVED_DT";
    const STATUS = "STATUS";
    const FUNCTIONAL_LEVEL_ID ="FUNCTIONAL_LEVEL_ID";
    const BRANCH_ID = "BRANCH_ID";
    const SERVICE_ID ="SERVICE_ID";
    const SERVICE_SUBGROUP_ID ="SERVICE_SUBGROUP_ID";
    
    public $workforceId;
    public $companyId;
    public $locationId;
    public $departmentId;
    public $positionId;
    public $serviceGroupId;
    public $serviceTypeId;
    public $designationId;
    public $quota;
    public $remarks;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $checkedBy;
    public $checkedDt;
    public $approvedBy;
    public $approvedDt;
    public $status;
    public $functionalLevelId;
    public $branchId;
    public $serviceId;
    public $serviceSubgroupId;

    public $mappings= [
        'workforceId'=>self::WORKFORCE_ID,
        'companyId'=>self::COMPANY_ID,
        'locationId'=>self::LOCATION_ID,
        'departmentId'=>self::DEPARTMENT_ID,
        'positionId'=>self::POSITION_ID,
        'serviceGroupId'=>self::SERVICE_GROUP_ID,
        'serviceTypeId'=>self::SERVICE_TYPE_ID,
        'quota'=>self::QUOTA,
        'remarks'=>self::REMARKS,
        'createdBy'=>self::CREATED_BY,
        'createdDt'=>self::CREATED_DT,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDt'=>self::MODIFIED_DT,
        'checkedBy'=>self::CHECKED_BY,
        'checkedDt'=>self::CHECKED_DT,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDt'=>self::APPROVED_DT,
        'status'=>self::STATUS,
        'designationId'=>self::DESIGNATION_ID,
        'functionalLevelId'=>self::FUNCTIONAL_LEVEL_ID,
        'branchId'=>self::BRANCH_ID,
        'serviceId'=>self::SERVICE_ID,
        'serviceSubgroupId'=> self::SERVICE_SUBGROUP_ID
    ];   
}
