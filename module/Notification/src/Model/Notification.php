<?php

namespace Notification\Model;

use Application\Model\Model;

class Notification extends Model {

    const TABLE_NAME = "HRIS_NOTIFICATION";
    const MESSAGE_ID = "MESSAGE_ID";
    const MESSAGE_DATETIME = "MESSAGE_DATETIME";
    const MESSAGE_TITLE = "MESSAGE_TITLE";
    const MESSAGE_DESC = "MESSAGE_DESC";
    const MESSAGE_FROM = "MESSAGE_FROM";
    const MESSAGE_TO = "MESSAGE_TO";
    const STATUS = "STATUS";
    const EXPIRY_TIME = "EXPIRY_TIME";
    const ROUTE = "ROUTE";

    public $messageId;
    public $messageDateTime;
    public $messageTitle;
    public $messageDesc;
    public $messageFrom;
    public $messageTo;
    public $status;
    public $expiryTime;
    public $route;
    public $mappings = [
        'messageId' => self::MESSAGE_ID,
        'messageDateTime' => self::MESSAGE_DATETIME,
        'messageTitle' => self::MESSAGE_TITLE,
        'messageDesc' => self::MESSAGE_DESC,
        'messageFrom' => self::MESSAGE_FROM,
        'messageTo' => self::MESSAGE_TO,
        'status' => self::STATUS,
        'expiryTime' => self::EXPIRY_TIME,
        'route' => self::ROUTE
    ];

}
