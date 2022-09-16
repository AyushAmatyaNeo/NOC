<?php 
namespace Recruitment\Model;

use Application\Model\Model;

class StageModel extends Model{
    
    const TABLE_NAME      = "HRIS_REC_STAGES";
    const REC_STAGE_ID    = "REC_STAGE_ID";    
    const STAGE_EDESC     = "STAGE_EDESC";
    const STAGE_NDESC     = "STAGE_NDESC";
    const ORDER_NO        = "ORDER_NO";   
    const IS_FINAL        = "IS_FINAL";       
    const STATUS          = "STATUS";       
    const CREATED_BY      = "CREATED_BY";     
    const CREATED_DT      = "CREATED_DT";
    const MODIFIED_BY     = "MODIFIED_BY";    
    const MODIFIED_DT     = "MODIFIED_DT";
    const CHECKED_BY      = "CHECKED_BY";    
    const CHECKED_DT      = "CHECKED_DT";
    const APPROVED_BY     = "APPROVED_BY";   
    const APPROVED_DT     = "APPROVED_DT";
    const DELETED_BY      = "DELETED_BY";    
    const DELETED_DT      = "DELETED_DT";

    public $RecStageId;
    public $StageEdesc;
    public $StageNdesc;
    public $OrderNo;
    public $IsFinal;
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

        'RecStageId'       => self::REC_STAGE_ID,
        'StageEdesc'       => self::STAGE_EDESC,
        'StageNdesc'       => self::STAGE_NDESC,
        'OrderNo'          => self::ORDER_NO,
        'IsFinal'          => self::IS_FINAL,
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