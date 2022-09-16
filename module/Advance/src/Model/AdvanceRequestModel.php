<?php

namespace Advance\Model;

use Application\Model\Model;

class AdvanceRequestModel extends Model {

    const TABLE_NAME = "HRIS_EMPLOYEE_ADVANCE_REQUEST";
    const ADVANCE_REQUEST_ID = "ADVANCE_REQUEST_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const ADVANCE_ID = "ADVANCE_ID";
    const REQUESTED_AMOUNT = "REQUESTED_AMOUNT";
    const REQUESTED_DATE = "REQUESTED_DATE";
    const DATE_OF_ADVANCE = "DATE_OF_ADVANCE";
    const REASON = "REASON";
    const RECOMMENDED_BY = "RECOMMENDED_BY";
    const RECOMMENDED_DATE = "RECOMMENDED_DATE";
    const RECOMMENDED_REMARKS = "RECOMMENDED_REMARKS";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const APPROVED_REMARKS = "APPROVED_REMARKS";
    const STATUS = "STATUS";
    const DEDUCTION_TYPE = "DEDUCTION_TYPE";
    const DEDUCTION_RATE = "DEDUCTION_RATE";
    const DEDUCTION_IN = "DEDUCTION_IN";
    const OVERRIDE_RECOMMENDER_ID = "OVERRIDE_RECOMMENDER_ID";
    const OVERRIDE_APPROVER_ID = "OVERRIDE_APPROVER_ID";

    public $advanceRequestId;
    public $employeeId;
    public $advanceId;
    public $requestedAmount;
    public $requestedDate;
    public $dateOfadvance;
    public $reason;
    public $recommendedBy;
    public $recommendedDate;
    public $recommendedRemarks;
    public $approvedBy;
    public $approvedDate;
    public $approvedRemarks;
    public $status;
    public $deductionType;
    public $deductionRate;
    public $deductionIn;
    public $overrideRecommenderId;
    public $overrideApproverId;
    public $mappings = [
        'advanceRequestId' => self::ADVANCE_REQUEST_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'advanceId' => self::ADVANCE_ID,
        'requestedAmount' => self::REQUESTED_AMOUNT,
        'requestedDate' => self::REQUESTED_DATE,
        'dateOfadvance' => self::DATE_OF_ADVANCE,
        'reason' => self::REASON,
        'recommendedBy' => self::RECOMMENDED_BY,
        'recommendedDate' => self::RECOMMENDED_DATE,
        'recommendedRemarks' => self::RECOMMENDED_REMARKS,
        'approvedBy' => self::APPROVED_BY,
        'approvedDate' => self::APPROVED_DATE,
        'approvedRemarks' => self::APPROVED_REMARKS,
        'status' => self::STATUS,
        'deductionType' => self::DEDUCTION_TYPE,
        'deductionRate' => self::DEDUCTION_RATE,
        'deductionIn' => self::DEDUCTION_IN,
        'overrideRecommenderId' => self::OVERRIDE_RECOMMENDER_ID,
        'overrideApproverId' => self::OVERRIDE_APPROVER_ID
    ];

}
