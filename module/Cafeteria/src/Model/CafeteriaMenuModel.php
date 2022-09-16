<?php
namespace Cafeteria\Model;

use Application\Model\Model;

class CafeteriaMenuModel extends Model{
    const TABLE_NAME = "HRIS_CAFETERIA_MENU_SETUP";
    const MENU_ID = "MENU_ID";
    const MENU_NAME = "MENU_NAME";
    const QUANTITY = "QUANTITY";
    const RATE = "RATE";
    const REMARKS = "REMARKS";
    const CREATED_BY = "CREATED_BY";
    const COMPANY_ID = "COMPANY_ID";
    const STATUS = "STATUS";
     
    public $id;
    public $menuName;
    public $quantity;
    public $companyId;
    public $rate;
    public $remarks;
    public $createdBy;
    public $status;
     
    public $mappings = [
        'id'=> self::MENU_ID,
        'menuName'=> self::MENU_NAME,
        'quantity'=> self::QUANTITY,
        'rate'=>self::RATE,
        'remarks'=>self::REMARKS,
        'createdBy'=>self::CREATED_BY,
        'companyId'=>self::COMPANY_ID,
        'status'=>self::STATUS
    ];
}