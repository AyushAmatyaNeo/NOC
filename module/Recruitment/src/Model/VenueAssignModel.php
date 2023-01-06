<?php
namespace Recruitment\Model;

use Application\Model\Model;

class VenueAssignModel extends Model {

    const TABLE_NAME            = "HRIS_REC_VENUE_ASSIGN";
    const VENUE_ASSIGN_ID = "VENUE_ASSIGN_ID";
    const VENUE_SETUP_ID        = "VENUE_SETUP_ID";
    const EXAM_TYPE            = "EXAM_TYPE";
    const STATUS                = "STATUS";
    const CREATED_BY            = "CREATED_BY";
    const CREATED_DATE            = "CREATED_DATE"; 
    const MODIFIED_DATE           = "MODIFIED_DATE";    
    const START_TIME            = "START_TIME";
    const END_TIME            = "END_TIME";
    const EXAM_DATE            = "EXAM_DATE";
    const VACANCY_IDS            = "VACANCY_IDS";


    public $venueAssignId;
    public $venueSetupId;
    public $examType;
    public $status;
    public $createdBy;
    public $createdDate;
    public $modifiedDate;
    public $startTime;
    public $endTime;
    public $examDate;
    public $vacancyIds;

    public $mappings = [
        'venueAssignId'  => self::VENUE_ASSIGN_ID,
        'venueSetupId'  => self::VENUE_SETUP_ID,
        'examType'          => self::EXAM_TYPE,
        'status'          => self::STATUS,
        'createdBy'         => self::CREATED_BY,
        'createdDate'         => self::CREATED_DATE,
        'modifiedDate'          => self::MODIFIED_DATE,
        'startTime'         => self::START_TIME,
        'endTime'          => self::END_TIME,
        'examDate'          => self::EXAM_DATE,
        'vacancyIds'  => self::VACANCY_IDS,
    ];
}