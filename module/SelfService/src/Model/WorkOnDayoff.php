<?php
namespace SelfService\Model;

use Application\Model\Model;

class WorkOnDayoff extends Model{
    const TABLE_NAME = "HRIS_EMPLOYEE_WORK_DAYOFF";
    const ID = "ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const FROM_DATE = "FROM_DATE";
    const TO_DATE = "TO_DATE";
    const DURATION = "DURATION";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const REQUESTED_DATE = "REQUESTED_DATE";
    const RECOMMENDED_BY = "RECOMMENDED_BY";
    const RECOMMENDED_DATE = "RECOMMENDED_DATE";
    const RECOMMENDED_REMARKS = "RECOMMENDED_REMARKS";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const APPROVED_REMARKS = "APPROVED_REMARKS";
    const MODIFIED_DATE = "MODIFIED_DATE";
    
    public $id;
    public $employeeId;
    public $fromDate;
    public $toDate;
    public $duration;
    public $remarks;
    public $status;
    public $requestedDate;
    public $recommendedBy;
    public $recommendedDate;
    public $recommendedRemarks;
    public $approvedBy;
    public $approvedDate;
    public $approvedRemarks;
    public $modifiedDate;
    
    public $mappings = [
        'id'=>self::ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'fromDate'=>self::FROM_DATE,
        'toDate'=>self::TO_DATE,
        'duration'=>self::DURATION,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'requestedDate'=>self::REQUESTED_DATE,
        'recommendedBy'=>self::RECOMMENDED_BY,
        'recommendedDate'=>self::RECOMMENDED_DATE,
        'recommendedRemarks'=>self::RECOMMENDED_REMARKS,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'approvedRemarks'=>self::APPROVED_REMARKS,
        'modifiedDate'=>self::MODIFIED_DATE
    ];
}