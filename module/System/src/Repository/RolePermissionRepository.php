<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/21/16
 * Time: 2:50 PM
 */

namespace System\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use System\Model\MenuSetup;
use System\Model\RolePermission;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class RolePermissionRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(RolePermission::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        // TODO: Implement edit() method.
    }

    public function fetchAll() {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id) {
        // TODO: Implement fetchById() method.
    }

    public function delete($id) {
        $this->tableGateway->update(['STATUS' => 'D'], ['MENU_ID' => $id]);
    }

    public function deleteAll($menuId, $roleId) {
        $this->tableGateway->update(['STATUS' => 'D'], ['MENU_ID' => $menuId, 'ROLE_ID' => $roleId]);
    }

    public function findAllRoleByMenuId($menuId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("RP.ROLE_ID AS ROLE_ID"),
            new Expression("RP.MENU_ID AS MENU_ID"),
                ], true);

        $select->from(['RP' => RolePermission::TABLE_NAME])
                ->join(['R' => 'HRIS_ROLES'], "R.ROLE_ID=RP.ROLE_ID", ['ROLE_NAME']);

        $select->where([
            "RP.STATUS='E'",
            "RP.MENU_ID"=>$menuId
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }

    public function selectRoleMenu($menuId, $roleId) {
        $result = $this->tableGateway->select(['MENU_ID' => $menuId, 'ROLE_ID' => $roleId]);
        return $result->current();
    }

    public function getActiveRoleMenu($menuId, $roleId) {
        $result = $this->tableGateway->select(['MENU_ID' => $menuId, 'ROLE_ID' => $roleId, 'STATUS' => 'E']);
        return $result->current();
    }

    public function updateDetail($menuId, $roleId) {
        $this->tableGateway->update(['STATUS' => 'E'], ['MENU_ID' => $menuId, 'ROLE_ID' => $roleId]);
    }

    public function fetchAllMenuByRoleId($roleId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from(['M' => MenuSetup::TABLE_NAME])
                ->join(['RP' => RolePermission::TABLE_NAME], 'M.' . MenuSetup::MENU_ID . "=RP." . RolePermission::MENU_ID, []);

        $select->where(["RP." . RolePermission::ROLE_ID => $roleId]);
        $select->where(["RP." . RolePermission::STATUS . "='E'"]);
        $select->where(["M." . MenuSetup::STATUS . "='E'"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function menuRoleAssign($menuId, $roleId, $assignFlag) {
        $boundedParameter = [];
        $boundedParameter['menuId']=$menuId;
        $boundedParameter['roleId']=$roleId;
        $boundedParameter['assignFlag']=$assignFlag;
        $sql = "CALL HRIS_MENU_ROLE_ASSIGN(?,?,?);";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result;
    }

}
