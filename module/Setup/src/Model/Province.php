<?php

namespace Setup\Model;

use Application\Model\Model;

class Province extends Model {

    const TABLE_NAME = "HRIS_PROVINCES";
    const PROVINCE_ID = "PROVINCE_ID";
    const PROVINCE_NAME = "PROVINCE_NAME";
    const STATUS = "STATUS";

    public $provinceId;
    public $provinceName;
    public $status;
    public $mappings = [
        '$provinceId' => self::PROVINCE_ID,
        '$provinceName' => self::PROVINCE_NAME,
        'status' => self::STATUS
    ];

}
