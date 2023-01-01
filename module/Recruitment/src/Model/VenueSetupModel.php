<?php
namespace Recruitment\Model;

use Application\Model\Model;

class VenueSetupModel extends Model {

    const TABLE_NAME            = "HRIS_REC_VENUE_SETUP";
    const VENUE_SETUP_ID        = "VENUE_SETUP_ID";
    const VENUE_NAME            = "VENUE_NAME";
    const STATUS                = "STATUS";
    const CREATED_DATE            = "CREATED_DATE"; 
    const MODIFIED_DATE           = "MODIFIED_DATE";
    const CREATED_BY            = "CREATED_BY";


    public $venueSetupId;
    public $venueName;
    public $status;
    public $createdDate;
    public $modifiedDate;
    public $createdBy;
    

    public $mappings = [
        'venueSetupId'  => self::VENUE_SETUP_ID,
        'venueName'           => self::VENUE_NAME,
        'status'             => self::STATUS,
        'createdDate'          => self::CREATED_DATE,
        'modifiedDate'          => self::MODIFIED_DATE,
        'createdBy'         => self::CREATED_BY
    ];
}