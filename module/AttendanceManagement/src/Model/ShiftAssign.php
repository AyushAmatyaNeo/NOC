<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/13/16
 * Time: 12:49 PM
 */

namespace AttendanceManagement\Model;

use Application\Model\Model;

class ShiftAssign extends  Model {
    const TABLE_NAME="HRIS_EMPLOYEE_SHIFT_ASSIGN";

    const EMPLOYEE_ID="EMPLOYEE_ID";
    const SHIFT_ID="SHIFT_ID";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT="MODIFIED_DT";
    const REMARKS="REMARKS";
    const STATUS="STATUS";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $employeeId;
    public $shiftId;
    public $createdDt;
    public $modifiedDt;
    public $remarks;
    public $status;
    public $createdBy;
    public $modifiedBy;

    public $mappings=[
        'employeeId'=>self::EMPLOYEE_ID,
        'shiftId'=>self::SHIFT_ID,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY
    ];

}