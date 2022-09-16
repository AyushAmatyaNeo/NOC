<?php
namespace Recruitment\Model;

use Application\Model\Model;

class Vacancyoptions extends Model {

    const TABLE_NAME         = "HRIS_REC_VACANCY_OPTIONS";
    const VACANCY_OPTION_ID  = "VACANCY_OPTION_ID";
    const VACANCY_ID         = "VACANCY_ID";
    const OPTION_ID          = "OPTION_ID";
    const QUOTA              = "QUOTA";
    const OPEN_INTERNAL      = "OPEN_INTERNAL";
    const REMARKS            = "REMARKS";
    const NORMAL_AMT         = "NORMAL_AMT";
    const LATE_AMT           = "LATE_AMT";
    const STATUS             = "STATUS";
    const CREATED_BY         = "CREATED_BY";
    const CREATED_DT         = "CREATED_DT"; 
    const MODIFIED_BY        = "MODIFIED_BY";
    const MODIFIED_DT        = "MODIFIED_DT";
    const CHECKED_BY         = "CHECKED_BY";
    const CHECKED_DT         = "CHECKED_DT";
    const APPROVED_BY        = "APPROVED_BY";
    const APPROVED_DT        = "APPROVED_DT";
    const DELETED_BY         = "DELETED_BY";
    const DELETED_DT         = "DELETED_DT";

    public $VacancyOptionId;
    public $VacancyId;
    public $OptionId;
    public $Quota;
    public $OpenInternal;
    public $Remarks;
    public $NormalAmt;
    public $LateAmt;
    public $Status;
    public $CreatedBy;
    public $CreatedDt;
    public $ModifiedBy;
    public $ModifiedDt;
    public $CheckedBy;
    public $CheckedDt;
    public $ApprovedBy;
    public $ApprovedDt;
    public $DeletedBy;
    public $DeletedDt;
    
    public $mappings = [
        'VacancyOptionId'  => self::VACANCY_OPTION_ID,
        'VacancyId'        => self::VACANCY_ID,
        'OptionId'         => self::OPTION_ID,
        'Quota'            => self::QUOTA,
        'OpenInternal'     => self::OPEN_INTERNAL,
        'Remarks'          => self::REMARKS,
        'NormalAmt'        => self::NORMAL_AMT,
        'LateAmt'          => self::LATE_AMT,
        'Status'           => self::STATUS,
        'CreatedBy'        => self::CREATED_BY,
        'CreatedDt'        => self::CREATED_DT,
        'ModifiedBy'       => self::MODIFIED_BY,
        'ModifiedDt'       => self::MODIFIED_DT,
        'CheckedBy'        => self::CHECKED_BY,
        'CheckedDt'        => self::CHECKED_DT,
        'ApprovedBy'       => self::APPROVED_BY,
        'ApprovedDt'       => self::APPROVED_DT,
        'DeletedBy'        => self::DELETED_BY,
        'DeletedDt'        => self::DELETED_DT,

    ];
}

