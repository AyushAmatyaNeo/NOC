<?php
namespace Recruitment\Model;

use Application\Model\Model;

class VenueAssignModel extends Model {

    const TABLE_NAME            = "HRIS_REC_VENUE_ASSIGN";
    const VENUE_ASSIGN_ID = "VENUE_ASSIGN_ID";
    const VENUE_SETUP_ID        = "VENUE_SETUP_ID";
    const START_INDEX            = "START_INDEX";
    const END_INDEX            = "END_INDEX";
    const ASSIGN_TYPE            = "ASSIGN_TYPE";
    const EXAM_TYPE            = "EXAM_TYPE";
    const STATUS                = "STATUS";
    const CREATED_BY            = "CREATED_BY";
    const CREATED_DATE            = "CREATED_DATE"; 
    const MODIFIED_DATE           = "MODIFIED_DATE";    
    const START_TIME            = "START_TIME";
    const END_TIME            = "END_TIME";
    const EXAM_DATE            = "EXAM_DATE";


    public $venueAssignId;
    public $venueSetupId;
    public $startIndex;
    public $endIndex;
    public $assignType;
    public $examType;
    public $status;
    public $createdBy;
    public $createdDate;
    public $modifiedDate;
    public $startTime;
    public $endTime;
    public $examDate;

    public $mappings = [
        'venueAssignId'  => self::VENUE_ASSIGN_ID,
        'venueSetupId'  => self::VENUE_SETUP_ID,
        'startIndex'           => self::START_INDEX,
        'endIndex'             => self::END_INDEX,
        'assignType'          => self::ASSIGN_TYPE,
        'examType'          => self::EXAM_TYPE,
        'status'          => self::STATUS,
        'createdBy'         => self::CREATED_BY,
        'createdDate'         => self::CREATED_DATE,
        'modifiedDate'          => self::MODIFIED_DATE,
        'startTime'         => self::START_TIME,
        'endTime'          => self::END_TIME,
        'examDate'          => self::EXAM_DATE,
    ];
}