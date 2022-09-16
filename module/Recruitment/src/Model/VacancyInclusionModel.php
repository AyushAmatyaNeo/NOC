<?php
namespace Recruitment\Model;

use Application\Model\Model;

class VacancyInclusionModel extends Model {
    const TABLE_NAME            =  "HRIS_REC_VACANCY_INCLUSION";
    const VACANCY_INCLUSION_ID  =  "VACANCY_INCLUSION_ID";
    const INCLUSION_ID          =  "INCLUSION_ID";
    const VACANCY_ID            =  "VACANCY_ID";
    const STATUS                =  "STATUS";
    const CREATED_BY            =  "CREATED_BY";
    const CREATED_DATE          =  "CREATED_DATE"; 
    const MODIFIED_BY           =  "MODIFIED_BY";
    const MODIFIED_DATE         =  "MODIFIED_DATE";
    const DELETED_BY            =  "DELETED_BY";
    const DELETED_DATE          =  "DELETED_DATE";


    public $vacancyInclusionId;
    public $InclusionId;
    public $VacancyId;
    public $Status;
    public $CreatedBy;
    public $CreatedDt;
    public $ModifiedBy;
    public $ModifiedDt;
    public $DeletedBy;
    public $DeletedDt;

    public $mappings = [
        'vacancyInclusionId'    => self::VACANCY_INCLUSION_ID,
        'InclusionId'           => self::INCLUSION_ID,
        'VacancyId'             => self::VACANCY_ID,
        'Status'                => self::STATUS,
        'CreatedBy'             => self::CREATED_BY,
        'CreatedDt'             => self::CREATED_DATE,
        'ModifiedBy'            => self::MODIFIED_BY,
        'ModifiedDt'            => self::MODIFIED_DATE,
        'DeletedBy'             => self::DELETED_BY,
        'DeletedDt'             => self::DELETED_DATE,
        
    ];
}