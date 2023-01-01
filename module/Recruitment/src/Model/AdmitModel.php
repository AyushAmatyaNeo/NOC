<?php
namespace Recruitment\Model;

use Application\Model\Model;

class AdmitModel extends Model {

	const TABLE_NAME       = "HRIS_REC_ADMIT_SETUP";
    const ADMIT_SETUP_ID   = "ADMIT_SETUP_ID";
    const DECLARATION_TEXT = "DECLARATION_TEXT";
    const TERMS            = "TERMS";
    const FILE_NAME        = "FILE_NAME";
    const STATUS           = "STATUS";  
    const CREATED_BY       = "CREATED_BY";
    const CREATED_DT       = "CREATED_DT";
    const MODIFIED_BY      = "MODIFIED_BY";
    const MODIFIED_DT      = "MODIFIED_DT";
    const URL              = "URL";

    public $AdmitSetupId;
    public $DeclarationText;
    public $Terms;
    public $FileName;
    public $Status;
    public $CreatedBy;
    public $CreatedDt;
    public $ModifiedBy;
    public $ModifiedDt;
    public $url;

    public $mappings = [

        'AdmitSetupId'     => self::ADMIT_SETUP_ID,
        'DeclarationText'  => self::DECLARATION_TEXT,
        'Terms'            => self::TERMS,
        'FileName'         => self::FILE_NAME,
        'Status'           => self::STATUS,
        'CreatedBy'        => self::CREATED_BY,
        'CreatedDt'        => self::CREATED_DT,
        'ModifiedBy'       => self::MODIFIED_BY,
        'ModifiedDt'       => self::MODIFIED_DT,
        'url'              => self::URL,
    ];
}