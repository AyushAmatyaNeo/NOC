<?php

namespace System\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use System\Model\RoleControl;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class RoleControlRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(RoleControl::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
//        $this->tableGateway->update($model->getArrayCopyForDB(), [role::ROLE_ID => $id]);
    }

    public function fetchAll() {
//        $sql = "SELECT ROLE_ID,
//                  ROLE_NAME,
//                  ROLE_CONTROL_DESC(CONTROL) AS CONTROL,
//                  BOOLEAN_DESC(ALLOW_ADD)    AS ALLOW_ADD,
//                  BOOLEAN_DESC(ALLOW_UPDATE) AS ALLOW_UPDATE,
//                  BOOLEAN_DESC(ALLOW_DELETE) AS ALLOW_DELETE,
//                  REMARKS
//                FROM HRIS_ROLES WHERE STATUS='E'";
//        $rowset = EntityHelper::rawQueryResult($this->adapter, $sql);
//        $result = Helper::extractDbData($rowset);
//        return $result;
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select(function(Select $select)use($id) {
            $select->where([RoleControl::ROLE_ID => $id]);
        });
        return $result->toArray();
    }

    public function delete($id) {
        $this->tableGateway->delete([RoleControl::ROLE_ID=> $id]);
    }

}
