<?php

namespace Cafeteria\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Cafeteria\Model\CafeteriaScheduleModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class CafeteriaSchedule implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(CafeteriaScheduleModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([CafeteriaScheduleModel::STATUS => 'D'], [CafeteriaScheduleModel::TIME_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [CafeteriaScheduleModel::TIME_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    
    public function fetchSchedules(){
        $sql = "SELECT TIME_ID, TIME_NAME, REMARKS, STATUS FROM HRIS_CAFETERIA_TIME_CODE ORDER BY TIME_ID";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
    
    public function fetchScheduleById($id){
        $sql = "SELECT TIME_ID, TIME_NAME, REMARKS, STATUS FROM HRIS_CAFETERIA_TIME_CODE WHERE TIME_ID = $id AND STATUS = 'E'";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
}
