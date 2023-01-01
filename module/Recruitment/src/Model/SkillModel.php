<?php
namespace Recruitment\Model;

use Application\Model\Model;

class SkillModel extends Model {

    const TABLE_NAME    = "HRIS_REC_SKILL";
    const SKILL_ID      = "SKILL_ID";
    const SKILL_NAME    = "SKILL_NAME";
    const SKILL_CODE    = "SKILL_CODE";
    const REQUIRED_FLAG = "REQUIRED_FLAG";
    const UPLOAD_FLAG = "UPLOAD_FLAG";
    const STATUS        = "STATUS";  
    const CREATED_BY    = "CREATED_BY";
    const CREATED_DT    = "CREATED_DT";
    const MODIFIED_BY   = "MODIFIED_BY";
    const MODIFIED_DT   = "MODIFIED_DT";
    const DELETED_BY    = "DELETED_BY";
    const DELETED_DT    = "DELETED_DT";

    public $SkillId;
    public $SkillName;
    public $SkillCode;
    public $RequiredFlag;
    public $UploadFlag;
    public $Status;
    public $CreatedBy;
    public $CreatedDt;
    public $ModifiedBy;
    public $ModifiedDt;
    public $DeletedBy;
    public $DeletedDt;



    public $mappings = [

        'SkillId'          => self::SKILL_ID,
        'SkillName'        => self::SKILL_NAME,
        'SkillCode'        => self::SKILL_CODE,
        'RequiredFlag'     => self::REQUIRED_FLAG, 
        'UploadFlag'       => self::UPLOAD_FLAG,
        'Status'           => self::STATUS,
        'CreatedBy'        => self::CREATED_BY,
        'CreatedDt'        => self::CREATED_DT,
        'ModifiedBy'       => self::MODIFIED_BY,
        'ModifiedDt'       => self::MODIFIED_DT,
        'DeletedBy'        => self::DELETED_BY,
        'DeletedDt'        => self::DELETED_DT,
    ];


}
