<?php

namespace Setup\Model;

use Application\Model\Model;

class HolidayDesignation extends Model {

    const TABLE_NAME = "HRIS_HOLIDAY_DESIGNATION";
    const HOLIDAY_ID = "HOLIDAY_ID";
    const DESIGNATION_ID = "DESIGNATION_ID";

    public $holidayId;
    public $designationId;
    public $mappings = [
        'holidayId' => self::HOLIDAY_ID,
        'designationId' => self::DESIGNATION_ID
    ];

}
