<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/18/16
 * Time: 2:05 PM
 */
namespace System\Model;

use Application\Model\Model;

class MenuSetup extends Model {
    const TABLE_NAME = "HRIS_MENUS";
    const MENU_ID = 'MENU_ID';
    const MENU_CODE = 'MENU_CODE';
    const MENU_NAME = 'MENU_NAME';
    const ROUTE = 'ROUTE';
    const ACTION = 'ACTION';
    const MENU_INDEX = "MENU_INDEX";
    const PARENT_MENU = 'PARENT_MENU';
    const MENU_DESCRIPTION = 'MENU_DESCRIPTION';
    const STATUS = 'STATUS';
    const CREATED_DT = 'CREATED_DT';
    const MODIFIED_DT = 'MODIFIED_DT';
    const ICON_CLASS = "ICON_CLASS";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    const IS_VISIBLE = "IS_VISIBLE"; 

    public $menuId;
    public $menuCode;
    public $menuName;
    public $route;
    public $action;
    public $menuIndex;
    public $parentMenu;
    public $menuDescription;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $iconClass;
    public $createdBy;
    public $modifiedBy;
    public $isVisible;

    public $mappings = [
        'menuId'=>self::MENU_ID,
        'menuCode'=>self::MENU_CODE,
        'menuName'=>self::MENU_NAME,
        'route'=>self::ROUTE,
        'action'=>self::ACTION,
        'menuIndex'=>self::MENU_INDEX,
        'parentMenu'=>self::PARENT_MENU,
        'menuDescription'=>self::MENU_DESCRIPTION,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
        'iconClass'=>self::ICON_CLASS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'isVisible'=>self::IS_VISIBLE
    ];
}