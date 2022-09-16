<?php
namespace Recruitment\Model;

use Application\Model\Model;

class VacancyLevelModel extends Model {
    const TABLE_NAME            =  "HRIS_REC_VACANCY_LEVELS";
    const VACANCY_LEVEL_ID      =  "VACANCY_LEVEL_ID";
    const FUNCTIONAL_LEVEL_ID   =  "FUNCTIONAL_LEVEL_ID";
    const OPENING_ID            =  "OPENING_ID";
    const POSITION_ID           =  "POSITION_ID";
    const EFFECTIVE_DATE        =  "EFFECTIVE_DATE";
    const NORMAL_AMOUNT         =  "NORMAL_AMOUNT";
    const LATE_AMOUNT           =  "LATE_AMOUNT";
    const INCLUSION_AMOUNT      =  "INCLUSION_AMOUNT";
    const MIN_AGE               =  "MIN_AGE";
    const MAX_AGE               =  "MAX_AGE";
    const STATUS                =  "STATUS";
    const CREATED_BY            =  "CREATED_BY";
    const CREATED_DATE          =  "CREATED_DATE"; 
    const MODIFIED_BY           =  "MODIFIED_BY";
    const MODIFIED_DATE         =  "MODIFIED_DATE";
    const DELETED_BY            =  "DELETED_BY";
    const DELETED_DATE          =  "DELETED_DATE";


    public $vacacnyLevelId;
    public $FunctionalLevelId;
    public $OpeningId;
    public $PositionId;
    public $EffectiveDate;
    public $NormalAmount;
    public $LateAmount;
    public $InclusionAmount;
    public $MinAge;
    public $MaxAge;
    public $Status;
    public $CreatedBy;
    public $CreatedDt;
    public $ModifiedBy;
    public $ModifiedDt;
    public $DeletedBy;
    public $DeletedDt;

    public $mappings = [
        'vacacnyLevelId'    => self::VACANCY_LEVEL_ID,
        'FunctionalLevelId' => self::FUNCTIONAL_LEVEL_ID,
        'OpeningId'         => self::OPENING_ID,
        'PositionId'        => self::POSITION_ID,
        'EffectiveDate'     => self::EFFECTIVE_DATE,
        'NormalAmount'      => self::NORMAL_AMOUNT,
        'LateAmount'        => self::LATE_AMOUNT,
        'InclusionAmount'   => self::INCLUSION_AMOUNT,
        'MinAge'            => self::MIN_AGE,
        'MaxAge'            => self::MAX_AGE,
        'Status'            => self::STATUS,
        'CreatedBy'         => self::CREATED_BY,
        'CreatedDt'         => self::CREATED_DATE,
        'ModifiedBy'        => self::MODIFIED_BY,
        'ModifiedDt'        => self::MODIFIED_DATE,
        'DeletedBy'         => self::DELETED_BY,
        'DeletedDt'         => self::DELETED_DATE,
        
    ];
}