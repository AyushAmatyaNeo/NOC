<?php
namespace Recruitment\Model;

use Application\Model\Model;

class RecruitmentVacancy extends Model {


    const TABLE_NAME                = "HRIS_REC_VACANCY";
    const VACANCY_ID                = "VACANCY_ID";
    const VACANCY_NO                = "VACANCY_NO";
    const VACANCY_TYPE              = "VACANCY_TYPE";
    const OPENING_ID                = "OPENING_ID";
    const LEVEL_ID                  = "LEVEL_ID";
    const SERVICE_TYPES_ID          = "SERVICE_TYPES_ID";
    const AD_NO                     = 'AD_NO';
    const SERVICE_EVENTS_ID         = "SERVICE_EVENTS_ID";
    const POSITION_ID               = "POSITION_ID";
    const QUALIFICATION_ID          = "QUALIFICATION_ID";
    const EXPERIENCE                = "EXPERIENCE";
    const DEPARTMENT_ID             = "DEPARTMENT_ID";
    const VACANCY_RESERVATION_NO    = "VACANCY_RESERVATION_NO";
    const SKILL_ID                  = "SKILL_ID";
    const INCLUSION_ID              = "INCLUSION_ID";
    const REMARK                    = 'REMARK';
    const STATUS                    = "STATUS";
    const CREATED_BY                =  "CREATED_BY";
    const CREATED_DT                =  "CREATED_DT"; 
    const MODIFIED_BY               =  "MODIFIED_BY";
    const MODIFIED_DT               =  "MODIFIED_DT";
    const DELETED_BY                =  "DELETED_BY";
    const DELETED_DT                =  "DELETED_DT";

    public $VacancyId;
    public $Vacancy_no;
    public $Vacancy_type;    
    public $OpeningId;
    public $LevelId;
    public $ServiceTypesId;
    public $ServiceEventsId;
    public $PositionId;
    public $QualificationId;
    public $Experience;
    public $DepartmentId;
    public $VacancyReservationNo;
    public $AdNo;
    public $SkillId;
    public $InclusionId;
    public $Remark;
    public $Status;
    public $CreatedBy;
    public $CreatedDt;
    public $ModifiedBy;
    public $ModifiedDt;
    public $DeletedBy;
    public $DeletedDt;
   
 
    public $mappings = [
        'VacancyId'             => self::VACANCY_ID,
        'Vacancy_no'             => self::VACANCY_NO,
        'Vacancy_type'           => self::VACANCY_TYPE,
        'OpeningId'             => self::OPENING_ID,
        'LevelId'               => self::LEVEL_ID,
        'ServiceTypesId'         => self::SERVICE_TYPES_ID,
        'ServiceEventsId'       => self::SERVICE_EVENTS_ID,
        'PositionId'            => self::POSITION_ID,
        'QualificationId'       => self::QUALIFICATION_ID,
        'Experience'            => self::EXPERIENCE,
        'DepartmentId'          => self::DEPARTMENT_ID,
        'VacancyReservationNo'  => self::VACANCY_RESERVATION_NO,
        'SkillId'               => self::SKILL_ID,
        'InclusionId'           => self::INCLUSION_ID,
        'AdNo'                  => self::AD_NO,
        'Remark'                => self::REMARK,
        'Status'                => self::STATUS,
        'CreatedBy'             => self::CREATED_BY,
        'CreatedDt'             => self::CREATED_DT,
        'ModifiedBy'            => self::MODIFIED_BY,
        'ModifiedDt'            => self::MODIFIED_DT,
        'DeletedBy'             => self::DELETED_BY,
        'DeletedDt'             => self::DELETED_DT,

    ];
}

?>