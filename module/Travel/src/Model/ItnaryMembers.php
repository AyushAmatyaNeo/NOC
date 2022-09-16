<?php
namespace Travel\Model;

use Application\Model\Model;

class ItnaryMembers extends Model{
    const TABLE_NAME = "HRIS_ITNARY_MEMBERS";
    const ITNARY_ID = "ITNARY_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const STATUS = "STATUS";
    
    
    public $itnaryId;
    public $employeeId;
    public $status;

    public $mappings= [
        'itnaryId'=>self::ITNARY_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'status'=>self::STATUS,
    ];   
}
