<?php

namespace Setup\Model;

use Application\Model\Model;

class FunctionalLevels extends Model {

    const TABLE_NAME = "HRIS_FUNCTIONAL_LEVELS";
    const FUNCTIONAL_LEVEL_ID = "FUNCTIONAL_LEVEL_ID";
    const FUNCTIONAL_LEVEL_NO = "FUNCTIONAL_LEVEL_NO";
    const FUNCTIONAL_LEVEL_EDESC = "FUNCTIONAL_LEVEL_EDESC";
    const FUNCTIONAL_LEVEL_LDESC = "FUNCTIONAL_LEVEL_LDESC";
    const FUNCTIONAL_TYPE_ID = "FUNCTIONAL_TYPE_ID";
    const STATUS = "STATUS";
    const ROLES = "ROLES";

    public $functionalLevelId;
    public $functionalLevelNo;
    public $functionalLevelEdesc;
    public $functionalLevelLdesc;
    public $functionalTypeId;
    public $status;
    public $roles;
    public $mappings = [
        'functionalLevelId' => self::FUNCTIONAL_LEVEL_ID,
        'functionalLevelNo' => self::FUNCTIONAL_LEVEL_NO,
        'functionalLevelEdesc' => self::FUNCTIONAL_LEVEL_EDESC,
        'functionalLevelLdesc' => self::FUNCTIONAL_LEVEL_LDESC,
        'functionalTypeId' => self::FUNCTIONAL_TYPE_ID,
        'status' => self::STATUS,
        'roles' => self::ROLES,
    ];

}
