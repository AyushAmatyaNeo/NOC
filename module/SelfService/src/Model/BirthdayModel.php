<?php

namespace SelfService\Model;

use Application\Model\Model;

class BirthdayModel extends Model {

    const TABLE_NAME = "HRIS_BIRTHDAY_MESSAGES";
    const BIRTHDAY_ID = "BIRTHDAY_ID";
    const BIRTHDAY_DATE = "BIRTHDAY_DATE";
    const FROM_EMPLOYEE = "FROM_EMPLOYEE";
    const TO_EMPLOYEE = "TO_EMPLOYEE";
    const MESSAGE = "MESSAGE";
    const CREATED_DT = "CREATED_DT";
    const STATUS = "STATUS";
    const MODIFIED_DT = "MODIFIED_DT";
    

    public $birthdayId;
    public $birthdayDate;
    public $fromEmployee;
    public $toEmployee;
    public $message;
    public $createdDt;
    public $status;
    public $modifiedDt;
    
    public $mappings = [
        'birthdayId' => self::BIRTHDAY_ID,
        'birthdayDate' => self::BIRTHDAY_DATE,
        'fromEmployee' => self::FROM_EMPLOYEE,
        'toEmployee' => self::TO_EMPLOYEE,
        'message' => self::MESSAGE,
        'createdDt' => self::CREATED_DT,
        'status' => self::STATUS,
        'modifiedDt' => self::MODIFIED_DT,
        
    ];

}
