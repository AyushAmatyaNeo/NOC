<?php

namespace System\Model;

use Application\Model\Model;

class Setting extends Model {

    const TABLE_NAME = "HRIS_USER_SETTING";
    const USER_ID = "USER_ID";
    const ENABLE_NOTIFICATION = "ENABLE_NOTIFICATION";
    const ENABLE_EMAIL = "ENABLE_EMAIL";

    public $userId;
    public $enableNotification;
    public $enableEmail;
    public $mappings = [
        'userId' => self::USER_ID,
        'enableNotification' => self::ENABLE_NOTIFICATION,
        'enableEmail' => self::ENABLE_EMAIL,
    ];

}
