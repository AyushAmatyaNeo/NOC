<?php

namespace Setup\Model;

use Application\Model\Model;

class Insurance extends Model {

    const TABLE_NAME = "HRIS_INSURANCE_SETUP";
    const INSURANCE_ID = "INSURANCE_ID";
    const INSURANCE_CODE = "INSURANCE_CODE";
    const INSURANCE_ENAME = "INSURANCE_ENAME";
    const INSURANCE_NNAME = "INSURANCE_NNAME";
    const TYPE = "TYPE";
    const OPEN = "OPEN";
    const SERVICE_TYPE_ID = "SERVICE_TYPE_ID";
    const MONTH_ID = "MONTH_ID";
    const FLAT_AMOUNT = "FLAT_AMOUNT";
    const ELIGIBLE_AFTER = "ELIGIBLE_AFTER";
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
   
    public $insuranceId;
    public $insuranceCode;
    public $insuranceEname;
    public $insuranceNname;
    public $type;
    public $open;
    public $serviceType;
    public $month;
    public $flatAmt;
    public $eligibleAfter;
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
        'insuranceId' => self::INSURANCE_ID,
        'insuranceCode' => self::INSURANCE_CODE,
        'insuranceEname' => self::INSURANCE_ENAME,
        'insuranceNname' => self::INSURANCE_NNAME,
        'type' => self::TYPE,
        'open' => self::OPEN,
        'serviceType' => self::SERVICE_TYPE_ID,
        'month' => self::MONTH_ID,
        'flatAmt' => self::FLAT_AMOUNT,
        'eligibleAfter' => self::ELIGIBLE_AFTER,
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
