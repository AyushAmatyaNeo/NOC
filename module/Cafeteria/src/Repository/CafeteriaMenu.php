<?php

namespace Cafeteria\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Cafeteria\Model\CafeteriaMenuModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class CafeteriaMenu implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(CafeteriaMenuModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([CafeteriaMenuModel::STATUS => 'D'], [CafeteriaMenuModel::MENU_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [CafeteriaMenuModel::MENU_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    
    public function fetchMenu(){
        $sql = "SELECT MENU_ID, MENU_NAME, QUANTITY, RATE, REMARKS, STATUS FROM HRIS_CAFETERIA_MENU_SETUP ORDER BY MENU_ID";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
    
    public function fetchMenuById($id){
        $sql = "SELECT MENU_ID, MENU_NAME, QUANTITY, RATE, REMARKS FROM HRIS_CAFETERIA_MENU_SETUP WHERE MENU_ID = $id AND STATUS = 'E'";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
}
