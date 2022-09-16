<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\CustomerLocationModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class CustomerLocationRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(CustomerLocationModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->gateway->update([CustomerLocationModel::STATUS => EntityHelper::STATUS_DISABLED], [CustomerLocationModel::LOCATION_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [CustomerLocationModel::LOCATION_ID => $id]);
    }

    public function fetchAll() {
//        $rawResult = $this->gateway->select(function(Select $select) {
//            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Customer::class, [
//                        Customer::CUSTOMER_ENAME,
//                        Customer::CUSTOMER_LNAME,
//                        Customer::CONTACT_PERSON_NAME,
//                    ]), false);
//            $select->where([Customer::STATUS => EntityHelper::STATUS_ENABLED]);
//            $select->order([Customer::CUSTOMER_ENAME => Select::ORDER_ASCENDING]);
//        });
//        return Helper::extractDbData($rawResult);
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustomerLocationModel::class, [
                    CustomerLocationModel::LOCATION_ID,
                    CustomerLocationModel::CUSTOMER_ID,
                    CustomerLocationModel::LOCATION_NAME,
                    CustomerLocationModel::ADDRESS,
                ]), false);
        $select->from(CustomerLocationModel::TABLE_NAME);
        $select->where([CustomerLocationModel::LOCATION_ID => $id]);
        $select->where([CustomerLocationModel::STATUS => EntityHelper::STATUS_ENABLED]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchAllLocationByCustomer($customerId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustomerLocationModel::class, [
//                    CustomerLocationModel::LOCATION_ID,
//                    CustomerLocationModel::CUSTOMER_ID,
                    CustomerLocationModel::LOCATION_NAME,
                    CustomerLocationModel::ADDRESS,
                        ], NULL, NULL, NULL, NULL, 'CL'
                ), false);
        $select->from(['CL' => CustomerLocationModel::TABLE_NAME]);
//                ->join(['C' => "HRIS_CUSTOMER"], "C." . Customer::CUSTOMER_ID . "=CC." . CustomerContract::CUSTOMER_ID, ['CUSTOMER_ENAME' => new Expression("INITCAP(C.CUSTOMER_ENAME)")], 'left');
        $select->where([CustomerLocationModel::STATUS => EntityHelper::STATUS_ENABLED]);
        $select->where([CustomerLocationModel::CUSTOMER_ID => $customerId]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
