<?php
namespace Cafeteria\Model;

use Application\Model\Model;

class CafeteriaScheduleModel extends Model{
    const TABLE_NAME = "HRIS_CAFETERIA_TIME_CODE";
    const TIME_ID = "TIME_ID";
    const TIME_NAME = "TIME_NAME";
    const REMARKS = "REMARKS";
    const CREATED_BY = "CREATED_BY";
    const COMPANY_ID = "COMPANY_ID";
    const STATUS = "STATUS";
     
    public $id;
    public $timeName;
    public $remarks;
    public $createdBy;
    public $companyId;
    public $status;
    
    public $mappings = [
        'id'=> self::TIME_ID,
        'timeName'=> self::TIME_NAME,
        'remarks'=> self::REMARKS,
        'createdBy'=>self::CREATED_BY,
        'companyId'=>self::COMPANY_ID,
        'status'=>self::STATUS
    ];
}