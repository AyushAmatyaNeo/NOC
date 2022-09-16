<?php
namespace System\Model;

use Application\Model\Model;

class RoleControl extends Model {

    const TABLE_NAME = "HRIS_ROLE_CONTROL";
    const ROLE_ID = "ROLE_ID";
    const CONTROL = "CONTROL";
    const VAL = "VAL";

    public $roleId;
    public $control;
    public $val;
    public $mappings = [
        'roleId' => self::ROLE_ID,
        'control' => self::CONTROL,
        'val' => self::VAL
    ];

}
