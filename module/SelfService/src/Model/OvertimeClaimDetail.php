<?php
namespace SelfService\Model;

use Application\Model\Model;

class OvertimeClaimDetail extends Model{
    const TABLE_NAME ="HRIS_EMPLOYEE_OVERTIME_CLAIM_DETAIL";
    
    const OVERTIME_CLAIM_DETAIL_ID = "OVERTIME_CLAIM_DETAIL_ID";
    const OVERTIME_CLAIM_ID = "OVERTIME_CLAIM_ID";
    const ATTENDANCE_DT = "ATTENDANCE_DT";
    const IN_TIME = "IN_TIME";
    const OUT_TIME = "OUT_TIME";
    const TYPE_FLAG = "TYPE_FLAG";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY ="MODIFIED_BY";
    const OT_HOUR = "OT_HOUR";
    const LEAVE_REWARD = "LEAVE_REWARD";
    const DAY_CODE = "DAY_CODE";
    const TOTAL_HOUR = "TOTAL_HOUR";
    const CANCELED_BY_RA = "CANCELED_BY_RA";
    const OT_REMARKS = "OT_REMARKS";
	const LUNCH_ALLOWANCE = "LUNCH_ALLOWANCE";
	const LOCKING_ALLOWANCE = "LOCKING_ALLOWANCE";
	const NIGHT_ALLOWANCE = "NIGHT_ALLOWANCE";
	const DASHAIN_TIHAR_LEAVE_REWARD = "DASHAIN_TIHAR_LEAVE_REWARD";
	const TOTAL_LEAVE_REWARD = "TOTAL_LEAVE_REWARD";

    public $lunchAllowance;
    public $lockingAllowance;
    public $nightAllowance;
    public $dashainTiharLeave;
    public $totalLeaveReward;
    public $overtimeClaimDetailId;
    public $overtimeClaimId;
    public $attendanceDt;
    public $inTime;
    public $outTime;
    public $typeFlag;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $otHour;
    public $leaveReward;
    public $dayCode;
    public $totalHour;
    public $canceledByRA;
    public $otRemarks;
    
    public $mappings = [
        'otRemarks'=>self::OT_REMARKS,
        'overtimeClaimDetailId'=>self::OVERTIME_CLAIM_DETAIL_ID,
        'overtimeClaimId'=>self::OVERTIME_CLAIM_ID,
        'attendanceDt'=>self::ATTENDANCE_DT,
        'inTime'=>self::IN_TIME,
        'outTime'=>self::OUT_TIME,
        'typeFlag'=>self::TYPE_FLAG,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'createdBy'=>self::CREATED_BY,
        'modifiedBy'=>self::MODIFIED_BY,
        'otHour' => self::OT_HOUR,
        'leaveReward'=> self::LEAVE_REWARD,
        'dayCode' => self::DAY_CODE,
        'totalHour' => self::TOTAL_HOUR,
        'canceledByRA' => self::CANCELED_BY_RA,
        'lunchAllowance' => self::LUNCH_ALLOWANCE,
        'lockingAllowance' => self::LOCKING_ALLOWANCE,
        'nightAllowance' => self::NIGHT_ALLOWANCE,
        'dashainTiharLeave' => self::DASHAIN_TIHAR_LEAVE_REWARD,
        'totalLeaveReward' => self::TOTAL_LEAVE_REWARD,
    ];
}