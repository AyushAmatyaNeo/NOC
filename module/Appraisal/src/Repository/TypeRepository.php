<?php

namespace Appraisal\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Appraisal\Model\Type;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class TypeRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Type::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Type::STATUS => 'D'], [Type::APPRAISAL_TYPE_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[Type::APPRAISAL_TYPE_ID]);
        unset($array[Type::CREATED_DATE]);
        unset($array[Type::STATUS]);
        $this->tableGateway->update($array, [Type::APPRAISAL_TYPE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("T.APPRAISAL_TYPE_ID AS APPRAISAL_TYPE_ID"),
            new Expression("T.APPRAISAL_TYPE_CODE AS APPRAISAL_TYPE_CODE"),
            new Expression("INITCAP(T.APPRAISAL_TYPE_EDESC) AS APPRAISAL_TYPE_EDESC"),
            new Expression("T.DURATION_TYPE AS DURATION_TYPE"),
            new Expression("(CASE WHEN T.DURATION_TYPE = 'A' THEN 'Annual' ELSE 'Monthly' END) AS DURATION_TYPE_DETAIL")
                ], true);
        $select->from(["T" => Type::TABLE_NAME]);
        $select->where([Type::STATUS => EntityHelper::STATUS_ENABLED]);
        $select->order([Type::APPRAISAL_TYPE_EDESC => Select::ORDER_ASCENDING]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select) use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Type::class, [Type::APPRAISAL_TYPE_EDESC, Type::APPRAISAL_TYPE_NDESC]), false);
            $select->where([Type::APPRAISAL_TYPE_ID => $id, Type::STATUS => 'E']);
        });
        return $result = $rowset->current();
    }

}
