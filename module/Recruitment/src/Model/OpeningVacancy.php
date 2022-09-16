<?php
namespace Recruitment\Model;

use Application\Model\Model;

class OpeningVacancy extends Model {


    const TABLE_NAME            =  "HRIS_REC_OPENINGS";
    const OPENING_ID            =  "OPENING_ID";
    const OPENING_NO            =  "OPENING_NO";
    const VACANCY_TOTAL_NO      =  "VACANCY_TOTAL_NO";
    const RESERVATION_NO        = "RESERVATION_NO";
    const START_DATE            =  "START_DATE";
    const END_DATE              =  "END_DATE";
    const EXTENDED_DATE         =  "EXTENDED_DATE";
    const INSTRUCTION_EDESC     =  "INSTRUCTION_EDESC";
    const INSTRUCTION_NDESC     =  "INSTRUCTION_NDESC";
    const STATUS                =  "STATUS";
    const CREATED_BY            =  "CREATED_BY";
    const CREATED_DT            =  "CREATED_DT"; 
    const MODIFIED_BY           =  "MODIFIED_BY";
    const MODIFIED_DT           =  "MODIFIED_DT";
    const CHECKED_BY            =  "CHECKED_BY";
    const CHECKED_DT            =  "CHECKED_DT";
    const APPROVED_BY           =  "APPROVED_BY";
    const APPROVED_DT           =  "APPROVED_DT";
    const DELETED_BY            =  "DELETED_BY";
    const DELETED_DT            =  "DELETED_DT";


    public $OpeningId;
    public $OpeningNo;
    public $Vacancy_total_no;
    public $ReservationNo;
    public $Start_dt;
    public $End_dt;
    public $Extended_dt;
    public $Instruction_Edesc;
    public $Instruction_Ndesc;
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
        'OpeningId'         => self::OPENING_ID,
        'OpeningNo'         => self::OPENING_NO,
        'Vacancy_total_no'  => self::VACANCY_TOTAL_NO,
        'ReservationNo'     => self::RESERVATION_NO,
        'Start_dt'          => self::START_DATE,
        'End_dt'            => self::END_DATE,
        'Extended_dt'       => self::EXTENDED_DATE,
        'Instruction_Edesc' => self::INSTRUCTION_EDESC,
        'Instruction_Ndesc' => self::INSTRUCTION_NDESC,
        'Status'            => self::STATUS,
        'CreatedBy'         => self::CREATED_BY,
        'CreatedDt'         => self::CREATED_DT,
        'ModifiedBy'        => self::MODIFIED_BY,
        'ModifiedDt'        => self::MODIFIED_DT,
        'CheckedBy'         => self::CHECKED_BY,
        'CheckedDt'         => self::CHECKED_DT,
        'ApprovedBy'        => self::APPROVED_BY,
        'ApprovedDt'        => self::APPROVED_DT,
        'DeletedBy'         => self::DELETED_BY,
        'DeletedDt'         => self::DELETED_DT,
        
    ];
}

?>