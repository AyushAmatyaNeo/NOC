<?php

namespace Advance\Model;

use Application\Model\Model;

class AdvanceSetupModel extends Model {

    const TABLE_NAME = "HRIS_ADVANCE_MASTER_SETUP";
    const ADVANCE_ID = "ADVANCE_ID";
    const ADVANCE_CODE = "ADVANCE_CODE";
    const ADVANCE_ENAME = "ADVANCE_ENAME";
    const ADVANCE_LNAME = "ADVANCE_LNAME";
    const ALLOWED_TO = "ALLOWED_TO";
    const ALLOWED_MONTH_GAP = "ALLOWED_MONTH_GAP";
    const ALLOW_UNCLEARED_ADVANCE = "ALLOW_UNCLEARED_ADVANCE";
    const MAX_SALARY_RATE = "MAX_SALARY_RATE";
    const MAX_ADVANCE_MONTH = "MAX_ADVANCE_MONTH";
    const DEDUCTION_TYPE = "DEDUCTION_TYPE";
    const DEDUCTION_RATE = "DEDUCTION_RATE";
    const DEDUCTION_IN = "DEDUCTION_IN";
    const ALLOW_OVERRIDE_RATE = "ALLOW_OVERRIDE_RATE";
    const MIN_OVERRIDE_RATE = "MIN_OVERRIDE_RATE";
    const ALLOW_OVERRIDE_MONTH = "ALLOW_OVERRIDE_MONTH";
    const MAX_OVERRIDE_MONTH = "MAX_OVERRIDE_MONTH";
    const OVERRIDE_RECOMMENDER_FLAG = "OVERRIDE_RECOMMENDER_FLAG";
    const OVERRIDE_APPROVER_FLAG = "OVERRIDE_APPROVER_FLAG";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const STATUS = "STATUS";

    public $advanceId;
    public $advanceCode;
    public $advanceEname;
    public $advanceLname;
    public $allowedTo;
    public $allowedMonthGap;
    public $allowUncleardAdvance;
    public $maxSalaryRate;
    public $maxAdvanceMonth;
    public $deductionType;
    public $deductionRate;
    public $deductionIn;
    public $allowOverrideRate;
    public $minOverrideRate;
    public $allowOverrideMonth;
    public $maxOverrideMonth;
    public $overrideRecommenderFlag;
    public $overrideApproverFlag;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $status;
    public $mappings = [
        'advanceId' => self::ADVANCE_ID,
        'advanceCode' => self::ADVANCE_CODE,
        'advanceEname' => self::ADVANCE_ENAME,
        'advanceLname' => self::ADVANCE_LNAME,
        'allowedTo' => self::ALLOWED_TO,
        'allowedMonthGap' => self::ALLOWED_MONTH_GAP,
        'allowUncleardAdvance' => self::ALLOW_UNCLEARED_ADVANCE,
        'maxSalaryRate' => self::MAX_SALARY_RATE,
        'maxAdvanceMonth' => self::MAX_ADVANCE_MONTH,
        'deductionType' => self::DEDUCTION_TYPE,
        'deductionRate' => self::DEDUCTION_RATE,
        'deductionIn' => self::DEDUCTION_IN,
        'allowOverrideRate' => self::ALLOW_OVERRIDE_RATE,
        'minOverrideRate' => self::MIN_OVERRIDE_RATE,
        'allowOverrideMonth' => self::ALLOW_OVERRIDE_MONTH,
        'maxOverrideMonth' => self::MAX_OVERRIDE_MONTH,
        'overrideRecommenderFlag' => self::OVERRIDE_RECOMMENDER_FLAG,
        'overrideApproverFlag' => self::OVERRIDE_APPROVER_FLAG,
        'createdBy' => self::CREATED_BY,
        'createdDate' => self::CREATED_DATE,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDate' => self::MODIFIED_DATE,
        'status' => self::STATUS
    ];

}
