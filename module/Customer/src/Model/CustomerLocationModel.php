<?php

namespace Customer\Model;

use Application\Model\Model;

class CustomerLocationModel extends Model {

    const TABLE_NAME = "HRIS_CUSTOMER_LOCATION";
    const LOCATION_ID = "LOCATION_ID";
    const CUSTOMER_ID = "CUSTOMER_ID";
    const LOCATION_NAME = "LOCATION_NAME";
    const ADDRESS = "ADDRESS";
    CONST CREATED_BY = "CREATED_BY";
    CONST CREATED_DT = "CREATED_DT";
    CONST MODIFIED_BY = "MODIFIED_BY";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST REMARKS = "REMARKS";
    CONST STATUS = "STATUS";

    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $remarks;
    public $status;
    public $locationId;
    public $customerId;
    public $locationName;
    public $address;
    public $mappings = [
        'locationId' => self::LOCATION_ID,
        'customerId' => self::CUSTOMER_ID,
        'locationName' => self::LOCATION_NAME,
        'address' => self::ADDRESS,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
    ];

}
