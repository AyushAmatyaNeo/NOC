<?php
namespace SelfService\Model;

use Application\Model\Model;

class TravelSubstitute extends Model{
    const TABLE_NAME = "HRIS_TRAVEL_SUBSTITUTE";
   
    const TRAVEL_ID = "TRAVEL_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const REMARKS = "REMARKS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DATE = "CREATED_DATE";
    const APPROVED_FLAG = "APPROVED_FLAG";
    const APPROVED_DATE = "APPROVED_DATE";
    const STATUS = "STATUS";
    
    public $travelId;
    public $employeeId;
    public $remarks;
    public $createdBy;
    public $createdDate;
    public $approvedFlag;
    public $approvedDate;
    public $status;
    
    public $mappings= [
        'travelId'=>self::TRAVEL_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'remarks' => self::REMARKS,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'approvedFlag'=>self::APPROVED_FLAG,
        'approvedDate'=>self::APPROVED_DATE,
        'status'=>self::STATUS
    ];
}