<?php
namespace Setup\Model;

use Application\Model\Model;

class Institute extends Model{
    const TABLE_NAME = "HRIS_INSTITUTE_MASTER_SETUP";
    const INSTITUTE_ID = "INSTITUTE_ID";
    const INSTITUTE_CODE = "INSTITUTE_CODE";
    const INSTITUTE_NAME = "INSTITUTE_NAME";
    const LOCATION = "LOCATION";
    const TELEPHONE = "TELEPHONE";
    const EMAIL = "EMAIL";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DATE = "CREATED_DATE";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    
    public $instituteId;
    public $instituteCode;
    public $instituteName;
    public $location;
    public $email;
    public $telephone;
    public $remarks;
    public $status;
    public $createdDate;
    public $createdBy;
    public $modifiedDate;
    public $modifiedBy;
    
    public $mappings = [
        'instituteId'=>self::INSTITUTE_ID,
        'instituteCode'=>self::INSTITUTE_CODE,
        'instituteName'=>self::INSTITUTE_NAME,
        'location'=>self::LOCATION,
        'telephone'=>self::TELEPHONE,
        'email'=>self::EMAIL,
        'remarks'=>self::REMARKS,
        'status'=>self::STATUS,
        'createdDate'=>self::CREATED_DATE,
        'createdBy'=>self::CREATED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'modifiedBy'=>self::MODIFIED_BY       
    ];
    
}
