<?php

namespace Medical\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Medical\Model\MedicalBill;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class MedicalBillRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(MedicalBill::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $rawResult = $this->gateway->select(function(Select $select)use($id) {
            $select->columns(['*'], false);
            $select->where([MedicalBill::MEDICAL_ID => $id]);
            $select->order([MedicalBill::SERIAL_NO => Select::ORDER_ASCENDING]);
        });
        return $rawResult;
    }

}
