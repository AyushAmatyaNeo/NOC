<?php
namespace Setup\Model;

use Application\Model\Model;

class Relation extends Model {
    const TABLE_NAME = "HRIS_RELATIONS";
    const RELATION_ID = "RELATION_ID";
    const RELATION_NAME = "RELATION_NAME";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";
    
    public $relationId;
    public $relationName;
    public $status;
    public $createdBy;
    public $createdDt;
    public $deletedBy;
    public $deletedDt;
    


    public $mappings = [
        'relationId'=>self::RELATION_ID,
        'relationName'=>self::RELATION_NAME,
        'status'=>self::STATUS,
        'createdBy'=>self::CREATED_BY,
        'createdDt'=>self::CREATED_DT,
        'deletedBy'=> self::DELETED_BY,
        'deletedDt'=> self::DELETED_DT,
    ];
}