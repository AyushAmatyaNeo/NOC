<?php
namespace Recruitment\Model;

use Application\Model\Model;

class Instruction extends Model {


    const TABLE_NAME            =  "HRIS_REC_INSTRUCTIONS";
    const INSTRUCTION_ID        =  "INSTRUCTION_ID";
    const INSTRUCTION_CODE      =  "INSTRUCTION_CODE";
    const DESCRIPTION_EDESC     =  "DESCRIPTION_EDESC";
    const DESCRIPTION_NDESC     =  "DESCRIPTION_NDESC";
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


    public $InstructionId;
    public $InstructionCode;
    public $Description_Edesc;
    public $Description_Ndesc;
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
        'InstructionId'         => self::INSTRUCTION_ID,
        'InstructionCode'         => self::INSTRUCTION_CODE,
        'DescriptionEdesc'  => self::DESCRIPTION_EDESC,
        'DescriptionNdesc'     => self::DESCRIPTION_NDESC,
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