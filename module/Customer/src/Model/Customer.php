<?php

namespace Customer\Model;

use Application\Model\Model;

class Customer extends Model {

    const TABLE_NAME = "HRIS_CUSTOMER";
    const CUSTOMER_ID = "CUSTOMER_ID";
    const CUSTOMER_CODE = "CUSTOMER_CODE";
    const CUSTOMER_ENAME = "CUSTOMER_ENAME";
    const CUSTOMER_LNAME = "CUSTOMER_LNAME";
    const ADDRESS = "ADDRESS";
    const PHONE_NO = "PHONE_NO";
    const CONTACT_PERSON_NAME = "CONTACT_PERSON_NAME";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const PAN_NO = "PAN_NO";

    public $customerId;
    public $customerCode;
    public $customerEname;
    public $customerLname;
    public $address;
    public $phoneNo;
    public $contactPersonName;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $remarks;
    public $status;
    public $panNo;
    
    public $mappings = [
        'customerId' => self::CUSTOMER_ID,
        'customerCode' => self::CUSTOMER_CODE,
        'customerEname' => self::CUSTOMER_ENAME,
        'customerLname' => self::CUSTOMER_LNAME,
        'address' => self::ADDRESS,
        'phoneNo' => self::PHONE_NO,
        'contactPersonName' => self::CONTACT_PERSON_NAME,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'panNo'=>self::PAN_NO
    ];

}
