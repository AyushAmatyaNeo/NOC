<?php
namespace Setup\Model;

use Application\Model\Model;


class FunctionalTypes extends Model {

    const TABLE_NAME = "HRIS_FUNCTIONAL_TYPES";
    const FUNCTIONAL_TYPE_ID = "FUNCTIONAL_TYPE_ID";
    const FUNCTIONAL_TYPE_CODE = "FUNCTIONAL_TYPE_CODE";
    const FUNCTIONAL_TYPE_EDESC = "FUNCTIONAL_TYPE_EDESC";
    const FUNCTIONAL_TYPE_LDESC = "FUNCTIONAL_TYPE_LDESC";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";

    public $functionalTypeId;
    public $functionalTypesCode;
    public $functionalTypesEdesc;
    public $functionalTypesLdesc;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $deletedBy;
    public $deletedDt;
    public $mappings = [
        'functionalTypeId' => self::FUNCTIONAL_TYPE_ID,
        'functionalTypesCode' => self::FUNCTIONAL_TYPE_CODE,
        'functionalTypesEdesc' => self::FUNCTIONAL_TYPE_EDESC,
        'functionalTypesLdesc' => self::FUNCTIONAL_TYPE_LDESC,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'deletedBy' => self::DELETED_BY,
        'deletedDt' => self::DELETED_DT,
    ];

}
