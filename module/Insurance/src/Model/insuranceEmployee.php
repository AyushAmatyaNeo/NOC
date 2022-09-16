<?php

namespace Insurance\Model;

use Application\Model\Model;

class InsuranceEmployee extends Model {

    const TABLE_NAME = "HRIS_EMPLOYEE_INSURANCE";
    const INSURANCE_EMP_ID = "INSURANCE_EMP_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const INSURANCE_ID = "INSURANCE_ID";
    const APPROVED_AMT = "APPROVED_AMT";
    const RELEASED_AMT = "RELEASED_AMT";
    const INSURANCE_DT = "INSURANCE_DT";
    const COMPLETED = "COMPLETED";
    const MATURED_DT = "MATURED_DT";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const CHECKED_BY = "CHECKED_BY";
    const CHECKED_DT = "CHECKED_DT";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DT = "APPROVED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";
   
    public $insuranceEmpId;
    public $employeeId;
    public $insuranceId;
    public $approvedAmt;
    public $releasedAmt;
    public $insuranceDt;
    public $completed;
    public $maturedDt;
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
    public $mappings = [
        'insuranceEmpId' => self::INSURANCE_EMP_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'insuranceId' => self::INSURANCE_ID,
        'approvedAmt' => self::APPROVED_AMT,
        'releasedAmt' => self::RELEASED_AMT,
        'insuranceDt' => self::INSURANCE_DT,
        'completed' => self::COMPLETED,
        'maturedDt' => self::MATURED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'checkedBy' => self::CHECKED_BY,
        'checkedDt' => self::CHECKED_DT,
        'approvedBy' => self::APPROVED_BY,
        'approvedDt' => self::APPROVED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'deletedBy' => self::DELETED_BY,
        'deletedDt' => self::DELETED_DT,
    ];

}
