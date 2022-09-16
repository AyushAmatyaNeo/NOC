<?php
namespace Recruitment\Model;

use Application\Model\Model;

class RecruitmentPersonal extends Model {


    const TABLE_NAME                = "HRIS_REC_APPLICATION_PERSONAL";
    const PERSONAL_ID               = "PERSONAL_ID";
    const APPLICATION_ID            = "APPLICATION_ID";
    const USER_ID                   = "USER_ID";
    const SKILL_ID                  = "SKILL_ID";
    const INCLUSION_ID              = 'INCLUSION_ID';
    const MAX_QUALIFICATION_ID      = 'MAX_QUALIFICATION_ID';
    const STATUS                    = 'STATUS';
    const CREATED_DATE              =  "CREATED_DATE"; 
    const MODIFIED_DATE             =  "MODIFIED_DATE";

    public $PersonalId;
    public $ApplicationId;
    public $UserId;
    public $SkillId;
    public $InclusionId;
    public $MaxQualificationId;
    public $Status;
    public $CreatedDate;
    public $ModifiedDate;
   

    public $mappings = [
        'PersonalId'             => self::PERSONAL_ID,
        'ApplicationId'          => self::APPLICATION_ID,
        'UserId'                => self::USER_ID,
        'SkillId'               => self::SKILL_ID,
        'InclusionId'           => self::INCLUSION_ID,
        'MaxQualificationId'    =>self::MAX_QUALIFICATION_ID,
        'Status'                => self::STATUS,
        'CreatedDate'           => self::CREATED_DATE,
        'ModifiedDate'          => self::MODIFIED_DATE,
    ];
}

?>