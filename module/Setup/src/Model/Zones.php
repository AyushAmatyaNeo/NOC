<?php

namespace Setup\Model;

use Application\Model\Model;

class Zones extends Model {

    const TABLE_NAME = "HRIS_ZONES";
    const ZONE_ID = "ZONE_ID";
    const ZONE_CODE = "ZONE_CODE";
    const ZONE_NAME = "ZONE_NAME";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";

    public $zoneId;
    public $zoneCode;
    public $zoneName;
    public $remarks;
    public $status;
    public $mappings = [
        'zoneId' => self::ZONE_ID,
        'zoneCode' => self::ZONE_CODE,
        'zoneName' => self::ZONE_NAME,
        'remarks' => self::REMARKS,
        'status' => self::STATUS
    ];

}
