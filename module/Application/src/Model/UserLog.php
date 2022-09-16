<?php

namespace Application\Model;


class UserLog extends Model {
    
    const TABLE_NAME = "HRIS_USER_LOG";
    const USER_ID = "USER_ID";
    const LOGIN_IP = "LOGIN_IP";
    const LOGIN_DATE = "LOGIN_DATE";


    public $userId;
    public $loginIp;
    public $loginDate;
    
    public $mappings = [
        'userId' => self::USER_ID,
        'loginIp' => self::LOGIN_IP,
        'loginDate' => self::LOGIN_DATE,
    ];
}