<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/9/16
 * Time: 10:53 AM
 */

namespace LeaveManagement\Model;

use Application\Model\Model;

class LeaveAssign extends Model
{
    const TABLE_NAME="HRIS_EMPLOYEE_LEAVE_ASSIGN";

    const EMPLOYEE_LEAVE_ASSIGN_ID="EMPLOYEE_LEAVE_ASSIGN_ID";
    const EMPLOYEE_ID="EMPLOYEE_ID";
    const LEAVE_ID="LEAVE_ID";
    const PREVIOUS_YEAR_BAL="PREVIOUS_YEAR_BAL";
    const TOTAL_DAYS="TOTAL_DAYS";
    const BALANCE="BALANCE";
    const FISCAL_YEAR="FISCAL_YEAR";
    const REMARKS="REMARKS";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT="MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const FISCAL_YEAR_MONTH_NO = "FISCAL_YEAR_MONTH_NO";

    public $employeeLeaveAssignId;
    public $employeeId;
    public $leaveId;
    public $previousYearBalance;
    public $totalDays;
    public $balance;
    public $fiscalYear;
    public $remarks;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $fiscalYearMonthNo;

    public $mappings=[
        'employeeLeaveAssignId'=>self::EMPLOYEE_LEAVE_ASSIGN_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'leaveId'=>self::LEAVE_ID,
        'previousYearBalance'=>self::PREVIOUS_YEAR_BAL,
        'totalDays'=>self::TOTAL_DAYS,
        'balance'=>self::BALANCE,
        'fiscalYear'=>self::FISCAL_YEAR,
        'remarks'=>self::REMARKS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'fiscalYearMonthNo' => self::FISCAL_YEAR_MONTH_NO
    ];
}