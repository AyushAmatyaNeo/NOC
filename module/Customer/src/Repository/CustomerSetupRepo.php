<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\Customer;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CustomerSetupRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(Customer::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->gateway->update([Customer::STATUS => EntityHelper::STATUS_DISABLED], [Customer::CUSTOMER_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [Customer::CUSTOMER_ID => $id]);
    }

    public function fetchAll() {
        $rawResult = $this->gateway->select(function(Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Customer::class, [
                        Customer::CUSTOMER_ENAME,
                        Customer::CUSTOMER_LNAME,
                        Customer::CONTACT_PERSON_NAME,
                    ]), false);
            $select->where([Customer::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->order([Customer::CUSTOMER_ENAME => Select::ORDER_ASCENDING]);
        });
        return Helper::extractDbData($rawResult);
    }

    public function fetchById($id) {
        $rawResult = $this->gateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Customer::class, [
                        Customer::CUSTOMER_ENAME,
                        Customer::CUSTOMER_LNAME,
                        Customer::CONTACT_PERSON_NAME,
                    ]), false);
            $select->where([Customer::CUSTOMER_ID => $id]);
            $select->where([Customer::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->order([Customer::CUSTOMER_ENAME => Select::ORDER_ASCENDING]);
        });
        return $rawResult->current();
    }

}
