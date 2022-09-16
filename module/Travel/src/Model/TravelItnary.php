<?php
namespace Travel\Model;

use Application\Model\Model;

class TravelItnary extends Model{
    const TABLE_NAME = "HRIS_TRAVEL_ITNARY";
    const ITNARY_ID = "ITNARY_ID";
    const ITNARY_CODE = "ITNARY_CODE";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const FROM_DT = "FROM_DT";
    const TO_DT = "TO_DT";
    const NO_OF_DAYS = "NO_OF_DAYS";
    Const PURPOSE = "PURPOSE";
    const FLOAT_MONEY = "FLOAT_MONEY";
    const TRANSPORT_TYPE = "TRANSPORT_TYPE";
    const TOTAL_DAYS = "TOTAL_DAYS";
    const REMARKS = "REMARKS";
    const LOCKED_FLAG = "LOCKED_FLAG";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";
    
    
    public $itnaryId;
    public $itnaryCode;
    public $employeeId;
    public $fromDt;
    public $toDt;
    public $noOfDays;
    Public $purpose;
    public $floatMoney;
    public $transportType;
    public $totalDays;
    public $remarks;
    public $lockedFlag;
    public $status;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $deletedBy;
    public $deletedDt;

    public $mappings= [
        'itnaryId'=>self::ITNARY_ID,
        'itnaryCode'=>self::ITNARY_CODE,
        'employeeId'=>self::EMPLOYEE_ID,
        'fromDt'=>self::FROM_DT,
        'toDt'=>self::TO_DT,
        'noOfDays'=>self::NO_OF_DAYS,
        'purpose'=>self::PURPOSE,
        'floatMoney'=>self::FLOAT_MONEY,
        'transportType'=>self::TRANSPORT_TYPE,
        'totalDays'=>self::TOTAL_DAYS,
        'remarks'=>self::REMARKS,
        'lockedFlag'=>self::LOCKED_FLAG,
        'status'=>self::STATUS,
        'createdBy'=>self::CREATED_BY,
        'createdDt'=>self::CREATED_DT,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDt'=>self::MODIFIED_DT,
        'deletedBy'=>self::DELETED_BY,
        'deletedDt'=>self::DELETED_DT,
    ];   
}
