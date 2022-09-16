<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\ServiceEmployeeSetupModel;
use Customer\Model\WagedEmployeeSetupModel;
use Setup\Model\Gender;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ServiceEmployeeSetupRepo implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(ServiceEmployeeSetupModel::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([ServiceEmployeeSetupModel::STATUS => EntityHelper::STATUS_DISABLED], [ServiceEmployeeSetupModel::EMPLOYEE_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [ServiceEmployeeSetupModel::EMPLOYEE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $columns = EntityHelper::getColumnNameArrayWithOracleFns(ServiceEmployeeSetupModel::class, [ServiceEmployeeSetupModel::FIRST_NAME, ServiceEmployeeSetupModel::MIDDLE_NAME, ServiceEmployeeSetupModel::LAST_NAME, ServiceEmployeeSetupModel::FULL_NAME], [
                    ServiceEmployeeSetupModel::CITIZENSHIP_ISSUE_DATE,
                        ], NULL, NULL, NULL, 'WE');
        $select->columns($columns, false);

        $select->from(['WE' => ServiceEmployeeSetupModel::TABLE_NAME])
                ->join(['G' => Gender::TABLE_NAME], "WE." . ServiceEmployeeSetupModel::GENDER_ID . "=G." . Gender::GENDER_ID, ['GENDER_NAME' => new Expression('INITCAP(G.GENDER_NAME)')], 'left')
                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "WE." . ServiceEmployeeSetupModel::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left');

        $select->where([
            "WE.STATUS='E'"
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);

//        echo $statement->getSql();
//        die();
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $columns = EntityHelper::getColumnNameArrayWithOracleFns(ServiceEmployeeSetupModel::class, [ServiceEmployeeSetupModel::FIRST_NAME, ServiceEmployeeSetupModel::MIDDLE_NAME, ServiceEmployeeSetupModel::LAST_NAME, ServiceEmployeeSetupModel::FULL_NAME], [
                    ServiceEmployeeSetupModel::CITIZENSHIP_ISSUE_DATE,
                        ], NULL, NULL, NULL, 'WE');
        $select->columns($columns, false);

        $select->from(['WE' => ServiceEmployeeSetupModel::TABLE_NAME])
                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "WE." . ServiceEmployeeSetupModel::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left');

        $select->where([
            "WE.STATUS='E'"
        ]);
        $select->where([
            "WE.EMPLOYEE_ID=$id"
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

}
