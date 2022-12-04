<?php 
namespace Recruitment\Model;

use Application\Model\Model;

class HrisRecApplicationStage extends Model{
    
    const TABLE_NAME      = "HRIS_REC_APPLICATION_STAGE";
    const ID    = "ID";    
    const APPLICATION_ID     = "APPLICATION_ID";
    const STAGE_ID     = "STAGE_ID";
    const CREATED_BY        = "CREATED_BY";   
    const CREATED_DATE_TIME        = "CREATED_DATE_TIME";       
    const REMARKS_EN          = "REMARKS_EN";       
    const REMARKS_NP      = "REMARKS_NP";     
    

    public $id;
    public $applicationId;
    public $stageId;
    public $createdBy;
    public $createdDateTime;
    public $remarksEn;
    public $remarksNp;

    public $mappings = [

        'id'       => self::ID,
        'applicationId'       => self::APPLICATION_ID,
        'stageId'       => self::STAGE_ID,
        'createdBy'          => self::CREATED_BY,
        'createdDateTime'          => self::CREATED_DATE_TIME,
        'remarksEn'           => self::REMARKS_EN,
        'remarksNp'        => self::REMARKS_NP,
    ];
}