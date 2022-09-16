<?php

namespace Gratuity\Model;

use Application\Model\Model;

class Gratuity extends Model { 

    const TABLE_NAME = "HRIS_GRATUITY";
    const GRATUITY_ID ="GRATUITY_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const EXTRA_SERVICE_YR = "EXTRA_SERVICE_YR";
    const TOTAL_AMOUNT = "TOTAL_AMOUNT";
    const APPROVED_AMOUNT = "APPROVED_AMOUNT";
    const VOUCHER_NO = "VOUCHER_NO";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const CHECKED_BY = "CHECKED_BY";
    const CHECKED_DATE = "CHECKED_DATE";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";

    public $gratuityId;
    public $employeeId;
    public $extraServiceYr;
    public $totalAmount;
    public $approvedAmount;
    public $voucherNo;
    public $status;
    public $remarks;
    public $createdBy;
    public $createdDate;
    public $approvedBy;
    public $approvedDate;
    public $modifiedBy;
    public $modifiedDt;
    public $deletedBy;
    public $deletedDt;
    public $mappings = [
        'gratuityId' => self::GRATUITY_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'extraServiceYr' => self::EXTRA_SERVICE_YR,
        'totalAmount' => self::TOTAL_AMOUNT,
        'approvedAmount' => self::APPROVED_AMOUNT,
        'voucherNo' => self::VOUCHER_NO,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'createdBy' => self::CREATED_BY,
        'createdDate' => self::CREATED_DT,
        'approvedBy' => self::APPROVED_BY,
        'approvedDate' => self::APPROVED_DATE,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'deletedBy' => self::DELETED_BY,
        'deletedDt' => self::DELETED_DT,
    ];

}
