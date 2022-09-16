<?php

namespace Setup\Model;

use Application\Model\Model;

class VdcMunicipalities extends Model {

    const TABLE_NAME = "HRIS_VDC_MUNICIPALITIES";
    const VDC_MUNICIPALITY_ID = "VDC_MUNICIPALITY_ID";
    const VDC_MUNICIPALITY_NAME = "VDC_MUNICIPALITY_NAME";
    const DISTRICT_ID = "DISTRICT_ID";
    const IS_MUNICIPALITY = "IS_MUNICIPALITY";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";

    public $vdcMunicipalityId;
    public $vdcMunicipalityName;
    public $districtId;
    public $isMunicipality;
    public $remarks;
    public $status;
    
    public $mappings = [
        'vdcMunicipalityId' => self::VDC_MUNICIPALITY_ID,
        'vdcMunicipalityName' => self::VDC_MUNICIPALITY_NAME,
        'districtId' => self::DISTRICT_ID,
        'isMunicipality' => self::IS_MUNICIPALITY,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
    ];

}
