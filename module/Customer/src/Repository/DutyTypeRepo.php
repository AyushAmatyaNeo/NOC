<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\DutyTypeModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class DutyTypeRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(DutyTypeModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->gateway->update([DutyTypeModel::STATUS => EntityHelper::STATUS_DISABLED], [DutyTypeModel::DUTY_TYPE_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [DutyTypeModel::DUTY_TYPE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(DutyTypeModel::class, [DutyTypeModel::DUTY_TYPE_NAME], NULL, NULL, null, null, null, false, false, [
                    DutyTypeModel::NORMAL_HOUR,
                    DutyTypeModel::OT_HOUR
                ]), false);
        $select->from(['DT' => DutyTypeModel::TABLE_NAME]);
        $select->where([
            "DT.STATUS='E'"
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(DutyTypeModel::TABLE_NAME);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(DutyTypeModel::class, [DutyTypeModel::DUTY_TYPE_NAME], NULL, NULL, null, null, null, false, false, [
                    DutyTypeModel::NORMAL_HOUR,
                    DutyTypeModel::OT_HOUR
                ]), false);

        $select->where([DutyTypeModel::DUTY_TYPE_ID . '=' . $id]);
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return $result->current();
    }

}
