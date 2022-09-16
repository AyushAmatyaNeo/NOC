<?php

namespace Cafeteria\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;

class CafeteriaMap implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        //$this->tableGateway = new TableGateway(CafeteriaMenuModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        //$this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        //$this->tableGateway->update([CafeteriaMenuModel::STATUS => 'D'], [CafeteriaMenuModel::MENU_ID => $id]);
    }

    public function edit(Model $model, $id) {
        //$this->tableGateway->update($model->getArrayCopyForDB(), [CafeteriaMenuModel::MENU_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    
    public function mapping($menu, $time, $type, $createdBy){
        $sql = "DELETE FROM HRIS_CAFETERIA_MENU_TIME_MAP WHERE TIME_ID = $time AND type = '$type'";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        if(count($menu) > 0){
            for($i = 0; $i < count($menu); $i++){
                $sql = "INSERT INTO HRIS_CAFETERIA_MENU_TIME_MAP (MAP_ID, TIME_ID, MENU_ID, CREATED_BY, COMPANY_CODE, TYPE) VALUES((SELECT NVL(MAX(MAP_ID)+1, 1) FROM HRIS_CAFETERIA_MENU_TIME_MAP), $time, $menu[$i], $createdBy, 1, '$type')";
                $statement = $this->adapter->query($sql);
                $statement->execute();
            }
        }
    }
    
    public function fetchMappingDetails(){
        $sql = "SELECT MENU_ID, TIME_ID FROM HRIS_CAFETERIA_MENU_TIME_MAP";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
    
    public function fetchMappingDetailsByTime($timeId){
        $sql = "SELECT MENU_ID, TYPE FROM HRIS_CAFETERIA_MENU_TIME_MAP WHERE TIME_ID = $timeId AND MENU_ID IN(SELECT MENU_ID FROM HRIS_CAFETERIA_MENU_SETUP WHERE STATUS = 'E')";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
    
    public function fetchSchedules(){
        $sql = "SELECT TIME_ID, TIME_NAME, REMARKS, STATUS FROM HRIS_CAFETERIA_TIME_CODE WHERE STATUS = 'E' ORDER BY TIME_ID";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
    
    public function fetchMenu(){
        $sql = "SELECT MENU_ID, MENU_NAME, QUANTITY, RATE, REMARKS, STATUS FROM HRIS_CAFETERIA_MENU_SETUP WHERE STATUS = 'E' ORDER BY MENU_ID";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
}
