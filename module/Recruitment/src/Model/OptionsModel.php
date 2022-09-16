<?php
namespace Recruitment\Model;

use Application\Model\Model;

class OptionsModel extends Model {


    const TABLE_NAME            =  "HRIS_REC_OPTIONS";
    const OPTION_ID             =  "OPTION_ID";
    const OPTION_EDESC          =  "OPTION_EDESC";
    const OPTION_NDESC          =  "OPTION_NDESC";
    const REMARKS               =  "REMARKS";
    const STATUS                =  "STATUS";
    const CREATED_BY            =  "CREATED_BY";
    const CREATED_DT            =  "CREATED_DT"; 
    const MODIFIED_BY           =  "MODIFIED_BY";
    const MODIFIED_DT           =  "MODIFIED_DT";
    const DELETED_BY            =  "DELETED_BY";
    const DELETED_DT            =  "DELETED_DT";


    public $OptionId;
    public $OptionsEdesc;
    public $OptionsNdesc;
    public $Remarks;
    public $Status;
    public $CreatedBy;
    public $CreatedDt;
    public $ModifiedBy;
    public $ModifiedDt;
    public $DeletedBy;
    public $DeletedDt;
   

    public $mappings = [
        'OptionId'          => self::OPTION_ID,
        'OptionsEdesc'      => self::OPTION_EDESC,
        'OptionsNdesc'      => self::OPTION_NDESC,
        'Remarks'           => self::REMARKS,
        'Status'            => self::STATUS,
        'CreatedBy'         => self::CREATED_BY,
        'CreatedDt'         => self::CREATED_DT,
        'ModifiedBy'        => self::MODIFIED_BY,
        'ModifiedDt'        => self::MODIFIED_DT,
        'DeletedBy'         => self::DELETED_BY,
        'DeletedDt'         => self::DELETED_DT,
    ];
}

?>