<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\CustomerContract;
use Customer\Model\CustomerContractDetailModel;
use Customer\Model\DutyTypeModel;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class CustomerContractDetailRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(CustomerContractDetailModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [CustomerContractDetailModel::CONTRACT_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchAllContractDetailByContractId($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustomerContractDetailModel::class, NULL, NULL, NULL, NULL, NULL, "CD"
                ), false);
        $select->from(["CD" => CustomerContractDetailModel::TABLE_NAME]);
        $select->join(["C" => CustomerContract::TABLE_NAME], "CD." . CustomerContractDetailModel::CONTRACT_ID . "=C." . CustomerContract::CONTRACT_ID, ["START_DATE", "END_DATE"], "left");
        $select->join(["D" => Designation::TABLE_NAME], "CD." . CustomerContractDetailModel::DESIGNATION_ID . "=D." . Designation::DESIGNATION_ID, ["DESIGNATION_TITLE" => new Expression('(D.DESIGNATION_TITLE)')], "left");
        $select->join(["DT" => DutyTypeModel::TABLE_NAME], "CD." . CustomerContractDetailModel::DUTY_TYPE_ID . "=DT." . DutyTypeModel::DUTY_TYPE_ID, ["DUTY_TYPE_NAME" => new Expression('INITCAP(DT.DUTY_TYPE_NAME)')], "left");
        $select->where(["CD." . CustomerContractDetailModel::STATUS => 'E']);
        $select->where(["CD." . CustomerContractDetailModel::CONTRACT_ID => $id]);
        $select->order("CD.DESIGNATION_ID ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function editWithCondition(Model $model, $updateCondition) {
//        $this->gateway->update($model->getArrayCopyForDB(), [CustomerContractDetailModel::CONTRACT_ID => $id]);
        $this->gateway->update($model->getArrayCopyForDB(), $updateCondition);
    }

}
