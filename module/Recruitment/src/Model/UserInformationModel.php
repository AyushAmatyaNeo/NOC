<?php
namespace Recruitment\Model;

use Application\Model\Model;

class UserInformationModel extends Model {

    const TABLE_NAME = "HRIS_REC_VACANCY_USERS";
    const USER_ID = "USER_ID";
    const FIRST_NAME = "FIRST_NAME";
    const MIDDLE_NAME = "MIDDLE_NAME";
    const LAST_NAME = "LAST_NAME";
    const MOBILE_NO = "MOBILE_NO";
    const EMAIL_ID = "EMAIL_ID";
    const USERNAME = "USERNAME";
    const PASSWORD = "PASSWORD";
    const MODIFIED_DT = "MODIFIED_DT";

    public $userId;
    public $firstName;
    public $middleName;
    public $lastName;
    public $mobileNo;
    public $emailId;
    public $userName;
    public $password;
    public $modifiedDt;

    public $mappings = [
        'userId'  => self::USER_ID,
        'firstName'  => self::FIRST_NAME,
        'middleName'  => self::MIDDLE_NAME,
        'lastName'  => self::LAST_NAME,
        'mobileNo'  => self::MOBILE_NO,
        'emailId'  => self::EMAIL_ID,
        'userName' => self::USERNAME,
        'password' => self::PASSWORD,
        'modifiedDt' => self::MODIFIED_DT,
    ];

}