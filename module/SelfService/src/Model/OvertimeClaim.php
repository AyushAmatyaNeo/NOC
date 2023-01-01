<?php
namespace SelfService\Model;

use Application\Model\Model;

class OvertimeClaim extends Model{
    const TABLE_NAME ="HRIS_EMPLOYEE_OVERTIME_CLAIM_REQUEST";
    
    const OVERTIME_CLAIM_ID = "OVERTIME_CLAIM_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const MONTH_ID = "MONTH_ID";
    const TOTAL_REQ_OT_HOURS = "TOTAL_REQ_OT_HOURS";
    const TOTAL_REQ_SUBSTITUTE_LEAVE = "TOTAL_REQ_SUBSTITUTE_LEAVE";
    const TOTAL_REQ_DASHAIN_TIHAR_LEAVE = "TOTAL_REQ_DASHAIN_TIHAR_LEAVE";
    const TOTAL_REQ_GRAND_TOTAL_LEAVE = "TOTAL_REQ_GRAND_TOTAL_LEAVE";
    const TOTAL_REQ_LUNCH_ALLOWANCE = "TOTAL_REQ_LUNCH_ALLOWANCE";
    const TOTAL_REQ_NIGHT_ALLOWANCE = "TOTAL_REQ_NIGHT_ALLOWANCE";
    const TOTAL_REQ_LOCKING_ALLOWANCE = "TOTAL_REQ_LOCKING_ALLOWANCE";
    const TOTAL_REQ_OT_DAYS = "TOTAL_REQ_OT_DAYS";
    const REQ_FESTIVE_OT_DAYS = "REQ_FESTIVE_OT_DAYS";
    const GRAND_TOTAL_REQ_OT_DAYS = "GRAND_TOTAL_REQ_OT_DAYS";
    const TOTAL_APP_OT_HOURS = "TOTAL_APP_OT_HOURS";
    const TOTAL_APP_SUBSTITUTE_LEAVE = "TOTAL_APP_SUBSTITUTE_LEAVE";
    const TOTAL_APP_DASHAIN_TIHAR_LEAVE = "TOTAL_APP_DASHAIN_TIHAR_LEAVE";
    const TOTAL_APP_GRAND_TOTAL_LEAVE = "TOTAL_APP_GRAND_TOTAL_LEAVE";
    const TOTAL_APP_LUNCH_ALLOWANCE = "TOTAL_APP_LUNCH_ALLOWANCE";
    const TOTAL_APP_NIGHT_ALLOWANCE = "TOTAL_APP_NIGHT_ALLOWANCE";
    const TOTAL_APP_LOCKING_ALLOWANCE = "TOTAL_APP_LOCKING_ALLOWANCE";
    const TOTAL_APP_OT_DAYS = "TOTAL_APP_OT_DAYS";
    const APP_FESTIVE_OT_DAYS = "APP_FESTIVE_OT_DAYS";
    const GRAND_TOTAL_APP_OT_DAYS = "GRAND_TOTAL_APP_OT_DAYS";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const RECOMMENDED_BY = "RECOMMENDED_BY";
    const RECOMMENDED_DATE = "RECOMMENDED_DATE";
    const RECOMMENDED_REMARKS = "RECOMMENDED_REMARKS";
    const APPROVED_BY ="APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const APPROVED_REMARKS = "APPROVED_REMARKS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    
    public $overtimeClaimId;
    public $employeeId;
    public $monthId;
    public $reqOtHours;
    public $reqSubstituteLeaveNo;
    public $reqDashainTiharLeave;
    public $reqGrandTotalLeavel;
    public $reqLunchAllowance;
    public $reqNightAllowance;
    public $reqLockingAllowance;
    public $reqOtDays;
    public $appOtHours;
    public $appSubstituteLeaveNo;
    public $appDashainTiharLeave;
    public $appGrandTotalLeavel;
    public $appLunchAllowance;
    public $appNightAllowance;
    public $appLockingAllowance;
    public $appOtDays;
    public $remarks;
    public $status;
    public $recommendedBy;
    public $recommendedDate;
    public $recommendedRemarks;
    public $approvedBy;
    public $approvedDate;
    public $approvedRemarks;
    public $createdBy;
    public $modifiedBy;
    public $createdDt;
    public $modifiedDt;
    public $reqFestiveOtDays;
    public $grandTotalReqOtDays;
    public $appFestiveOtDays;
    public $grandTotalAppOtDays;
    
    public $mappings = [
        'overtimeClaimId'=>self::OVERTIME_CLAIM_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'monthId'=>self::MONTH_ID,
        'reqOtHours'=>self::TOTAL_REQ_OT_HOURS,
        'reqSubstituteLeaveNo'=>self::TOTAL_REQ_SUBSTITUTE_LEAVE,
        'appOtHours'=>self::TOTAL_APP_OT_HOURS,
        'appSubstituteLeaveNo'=>self::TOTAL_APP_SUBSTITUTE_LEAVE,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'recommendedBy'=>self::RECOMMENDED_BY,
        'recommendedDate'=>self::RECOMMENDED_DATE,
        'recommendedRemarks'=>self::RECOMMENDED_REMARKS,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'approvedRemarks'=>self::APPROVED_REMARKS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'createdBy'=>self::CREATED_BY,
        'modifiedBy'=>self::MODIFIED_BY,
        'reqDashainTiharLeave'=>self::TOTAL_REQ_DASHAIN_TIHAR_LEAVE,
        'reqGrandTotalLeavel'=>self::TOTAL_REQ_GRAND_TOTAL_LEAVE,
        'reqLunchAllowance'=>self::TOTAL_REQ_LUNCH_ALLOWANCE,
        'reqNightAllowance'=>self::TOTAL_REQ_NIGHT_ALLOWANCE,
        'reqLockingAllowance'=>self::TOTAL_REQ_LOCKING_ALLOWANCE,
        'reqOtDays'=>self::TOTAL_REQ_OT_DAYS,
        'appDashainTiharLeave'=>self::TOTAL_APP_DASHAIN_TIHAR_LEAVE,
        'appGrandTotalLeavel'=>self::TOTAL_APP_GRAND_TOTAL_LEAVE,
        'appLunchAllowance'=>self::TOTAL_APP_LUNCH_ALLOWANCE,
        'appNightAllowance'=>self::TOTAL_APP_NIGHT_ALLOWANCE,
        'appLockingAllowance'=>self::TOTAL_APP_LOCKING_ALLOWANCE,
        'appOtDays'=>self::TOTAL_APP_OT_DAYS,
        'reqFestiveOtDays'=>self::REQ_FESTIVE_OT_DAYS,
        'grandTotalReqOtDays'=>self::GRAND_TOTAL_REQ_OT_DAYS,
        'appFestiveOtDays'=>self::APP_FESTIVE_OT_DAYS,
        'grandTotalAppOtDays'=>self::GRAND_TOTAL_APP_OT_DAYS
    ];
}