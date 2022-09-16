<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/21/16
 * Time: 2:51 PM
 */
namespace System\Model;

use Application\Model\Model;

class RolePermission extends Model {

    const TABLE_NAME="HRIS_ROLE_PERMISSIONS";
    const ROLE_ID="ROLE_ID";
    const MENU_ID="MENU_ID";
    const STATUS="STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";

    public $roleId;
    public $menuId;
    public $status;
    public $createdDt;
    public $modifiedDt;

    public $mappings=[
        'roleId'=>Self::ROLE_ID,
        'menuId'=>Self::MENU_ID,
        'status'=>Self::STATUS,
        'createdDt'=>Self::CREATED_DT,
        'modifiedDt'=>Self::MODIFIED_DT
    ];
}