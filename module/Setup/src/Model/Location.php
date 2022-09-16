<?php

namespace Setup\Model;

use Application\Model\Model;

class Location extends Model {

    const TABLE_NAME = "HRIS_LOCATIONS";
    const LOCATION_ID = "LOCATION_ID";
    const LOCATION_CODE = "LOCATION_CODE";
    const LOCATION_EDESC = "LOCATION_EDESC";
    const LOCATION_LDESC = "LOCATION_LDESC";
    const LOCATION_NDESC = "LOCATION_NDESC";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const PARENT_LOCATION_ID = "PARENT_LOCATION_ID";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $locationId;
    public $locationCode;
    public $locationEdesc;
    public $locationLdesc;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $parentLocationId;
    public $createdBy;
    public $modifiedBy;
    public $locationNdesc;
    public $mappings = [
        'locationId' => self::LOCATION_ID,
        'locationCode' => self::LOCATION_CODE,
        'locationEdesc' => self::LOCATION_EDESC,
        'locationLdesc' => self::LOCATION_LDESC,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'parentLocationId' => self::PARENT_LOCATION_ID,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'locationNdesc' => self::LOCATION_NDESC
    ];

}
