<?php

namespace Insurance\Model;

use Application\Model\Model;

class InsuranceDtl extends Model {

    const TABLE_NAME = "HRIS_EMPLOYEE_INSURANCE_DTL";
    const INSURANCE_DTL_ID = "INSURANCE_DTL_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const INSURANCE_ID = "INSURANCE_ID";
    const PREMIUM_AMT = "PREMIUM_AMT";
    const PREMIUM_DT = "PREMIUM_DT";
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
   
    public $insuranceDtlId;
    public $employeeId;
    public $insuranceId;
    public $premiumAmt;
    public $premiumDt;
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
        'insuranceDtlId' => self::INSURANCE_DTL_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'insuranceId' => self::INSURANCE_ID,
        'premiumAmt' => self::PREMIUM_AMT,
        'premiumDt' => self::PREMIUM_DT,
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
