<?php
namespace SelfService\Model;

use Application\Model\Model;

class LeaveEncashment extends Model{
    const TABLE_NAME = "HRIS_LEAVE_ENCASHMENT";
   
    const LE_ID = "LE_ID";
    const LEAVE_ID = "LEAVE_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const TOTAL_ACCUMULATED_DAYS = "TOTAL_ACCUMULATED_DAYS";
    const REQUESTED_DAYS_TO_ENCASH = "REQUESTED_DAYS_TO_ENCASH";
    const FISCAL_YEAR_ID = "FISCAL_YEAR_ID";
    const REMAINING_BALANCE = "REMAINING_BALANCE";
    const REQUESTED_DATE = "REQUESTED_DATE";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const REMARKS = "REMARKS";
    const MONTH_NO = "MONTH_NO";
    
    public $leId;
    public $leaveId;
    public $employeeId;
    public $totalAccumulatedDays;
    public $requestedDaysToEncash;
    public $fiscalYearId;
    public $remainingBalance;
    public $requestedDate;
    public $modifiedDate;
    public $remarks;
    public $monthNo;
    
    public $mappings= [
        'leId'=>self::LE_ID,
        'leaveId'=>self::LEAVE_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'totalAccumulatedDays'=>self::TOTAL_ACCUMULATED_DAYS,
        'requestedDaysToEncash'=>self::REQUESTED_DAYS_TO_ENCASH,
        'fiscalYearId'=>self::FISCAL_YEAR_ID,
        'remainingBalance'=>self::REMAINING_BALANCE,
        'requestedDate'=>self::REQUESTED_DATE,
        'modifiedDate'=>self::MODIFIED_DATE,
        'remarks'=>self::REMARKS,
        'monthNo' => self::MONTH_NO
    ];
}