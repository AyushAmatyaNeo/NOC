<?php

namespace Setup\Model;

use Application\Model\Model;

class District extends Model {

    const TABLE_NAME = "HRIS_DISTRICTS";
    const DISTRICT_ID = "DISTRICT_ID";
    const DISTRICT_NAME = "DISTRICT_NAME";
    const ZONE_ID = "ZONE_ID";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";

    public $districtId;
    public $districtName;
    public $zoneId;
    public $remarks;
    public $status;
    public $mappings = [
        'districtId' => self::DISTRICT_ID,
        'districtName' => self::DISTRICT_NAME,
        'zoneId' => self::ZONE_ID,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
    ];

}
