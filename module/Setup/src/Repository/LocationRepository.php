<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Location;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LocationRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Location::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(["D1" => Location::TABLE_NAME])
                ->join(["D2" => Location::TABLE_NAME], 'D1.PARENT_LOCATION_ID=D2.LOCATION_ID', ["PARENT_LOCATION_EDESC" => new Expression('INITCAP(D2.LOCATION_EDESC)')], "left");
        $select->where(["D1.STATUS= 'E'"]);
        $select->order(["D1." . Location::LOCATION_EDESC => Select::ORDER_ASCENDING]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchParentList($id = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(["D1" => Location::TABLE_NAME])
                ->join(["D2" => Location::TABLE_NAME], 'D1.PARENT_LOCATION_ID=D2.LOCATION_ID', ["PARENT_LOCATION_EDESC" => new Expression('INITCAP(D2.LOCATION_EDESC)')], "left");
        $select->where(["D1.STATUS= 'E'"]);
        if ($id != null) {
            $select->where(["D1.LOCATION_ID != {$id}"]);
        }
        $select->order(["D1." . Location::LOCATION_EDESC => Select::ORDER_ASCENDING]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select)use($id) {
            $select->where([Location::LOCATION_ID => $id, Location::STATUS => 'E']);
        });
        return $rowset->current();
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Location::LOCATION_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([Location::STATUS => 'D'], ["LOCATION_ID" => $id]);
    }

}
