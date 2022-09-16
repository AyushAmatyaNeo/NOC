<?php
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\FunctionalTypes;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;





class FunctionalTypesRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(FunctionalTypes::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(["D1" => FunctionalTypes::TABLE_NAME]);
        $select->where(["D1.STATUS= 'E'"]);
        $select->order(["D1." . FunctionalTypes::FUNCTIONAL_TYPE_EDESC => Select::ORDER_ASCENDING]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchParentList($id = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(["D1" => FunctionalTypes::TABLE_NAME]);
        $select->where(["D1.STATUS= 'E'"]);
        if ($id != null) {
            $select->where->notEqualTo("D1.FUNCTIONAL_TYPE_ID",  $id);
        }
        $select->order(["D1." . FunctionalTypes::FUNCTIONAL_TYPE_EDESC => Select::ORDER_ASCENDING]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select)use($id) {
            $select->where([FunctionalTypes::FUNCTIONAL_TYPE_ID => $id, FunctionalTypes::STATUS => 'E']);
        });
        return $rowset->current();
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [FunctionalTypes::FUNCTIONAL_TYPE_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([FunctionalTypes::STATUS => 'D'], ["FUNCTIONAL_TYPE_ID" => $id]);
    }

}
