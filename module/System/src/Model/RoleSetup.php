<?php

namespace System\Model;

use Application\Model\Model;

class RoleSetup extends Model {

    const TABLE_NAME = "HRIS_ROLES";
    const ROLE_ID = "ROLE_ID";
    const ROLE_NAME = "ROLE_NAME";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const CONTROL = "CONTROL";
    const ALLOW_ADD = "ALLOW_ADD";
    const ALLOW_UPDATE = "ALLOW_UPDATE";
    const ALLOW_DELETE = "ALLOW_DELETE";
    const HR_APPROVE = "HR_APPROVE";
    const HR_CANCEL = "HR_CANCEL";
    const CHECKER = "CHECKER";
    const MAKER = "MAKER";

    public $roleId;
    public $roleName;
    public $status;
    public $remarks;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $control;
    public $allowAdd;
    public $allowUpdate;
    public $allowDelete;
    public $hrApprove;
    public $hrCancel;
    public $checker;
    public $maker;
    public $mappings = [
        'roleId' => Self::ROLE_ID,
        'roleName' => Self::ROLE_NAME,
        'status' => Self::STATUS,
        'remarks' => Self::REMARKS,
        'createdDt' => Self::CREATED_DT,
        'modifiedDt' => Self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'control' => self::CONTROL,
        'allowAdd' => self::ALLOW_ADD,
        'allowUpdate' => self::ALLOW_UPDATE,
        'allowDelete' => self::ALLOW_DELETE,
        'hrApprove' => self::HR_APPROVE,
        'hrCancel' => self::HR_CANCEL,
        'checker' => self::CHECKER,
        'maker' => self::MAKER
    ];

}
