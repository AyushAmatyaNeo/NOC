<?php

namespace Cafeteria\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;

class CafeteriaActivity implements RepositoryInterface {

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
    
    public function fetchMenuByTimeId($id){
        $sql = "SELECT MENU_ID FROM HRIS_CAFETERIA_MENU_TIME_MAP WHERE TIME_ID = $id";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
    
    public function fetchTimes(){
        $sql = "SELECT TIME_ID, TIME_NAME FROM HRIS_CAFETERIA_TIME_CODE WHERE STATUS = 'E'";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
    
    public function saveLog($data, $logNo, $createdBy){
        $timeId = $data['menuTime'];
        $payType = $data['payType'];
        $logDate = $data['logDate'];
        $employeeId = $data['employee'];
        $grandTotal = $data['total'][count($data['total'])-1];
        $sql = "INSERT INTO HRIS_CAFETERIA_LOG(LOG_NO, LOG_DATE, EMPLOYEE_ID, TIME_ID, CREATED_BY, CREATED_DATE, PAY_TYPE, TOTAL_AMOUNT) VALUES($logNo, '$logDate', $employeeId, $timeId, $createdBy, trunc(sysdate), '$payType', $grandTotal)";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }
    
    public function saveLogDetails($data, $logNo, $createdBy){
        $payType = $data['payType'];
        $timeId = $data['menuTime'];
        $employeeId = $data['employee'];
        $logDate = $data['logDate'];
        for($i = 0; $i < count($data['menu_id']); $i++){
            if($data['qty'][$i] == ''){ continue; } 
            $menuId = $data['menu_id'][$i];
            $qty = $data['qty'][$i];
            $rate = $data['rate'][$i];
            $total = $data['total'][$i];
            $sql = "INSERT INTO HRIS_CAFETERIA_LOG_DETAIL(SERIAL_NO, LOG_NO, LOG_DATE, TIME_CODE, MENU_CODE, QUANTITY, RATE, TOTAL_AMOUNT, CREATED_BY, CREATED_DATE, EMPLOYEE_ID, PAY_TYPE, MODIFY_DATE) VALUES((SELECT NVL(MAX(SERIAL_NO)+1, 1) FROM HRIS_CAFETERIA_LOG_DETAIL where log_no = {$logNo} ), $logNo, '$logDate', $timeId, $menuId, $qty, $rate, $total, $createdBy, trunc(sysdate), $employeeId, '$payType', trunc(sysdate))";
            $statement = $this->adapter->query($sql);
            $statement->execute();
        }
    }
    
    public function getNextLogNo(){
        $sql = "SELECT NVL(MAX(LOG_NO)+1, 1) AS LOG_NO FROM HRIS_CAFETERIA_LOG";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function getPresentStatus($date){
        $sql = "SELECT E.EMPLOYEE_ID, E.FULL_NAME, (CASE WHEN HAD.IN_TIME
                IS NULL THEN 'AB' ELSE 'PR' END) AS STATUS 
                FROM HRIS_EMPLOYEES E JOIN HRIS_ATTENDANCE_DETAIL HAD
                ON E.EMPLOYEE_ID = HAD.EMPLOYEE_ID WHERE 
                HAD.ATTENDANCE_DT = '{$date}'";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function getEmployeesDetails(){
        $sql = "SELECT E.EMPLOYEE_ID, E.EMPLOYEE_CODE, E.FULL_NAME, D1.DEPARTMENT_NAME, D2.DESIGNATION_TITLE, HEF.FILE_PATH 
        FROM HRIS_EMPLOYEES E 
        LEFT JOIN HRIS_DEPARTMENTS D1 ON E.DEPARTMENT_ID = D1.DEPARTMENT_ID
        LEFT JOIN HRIS_DESIGNATIONS D2 ON E.DESIGNATION_ID = D2.DESIGNATION_ID
        LEFT JOIN HRIS_EMPLOYEE_FILE HEF ON E.PROFILE_PICTURE_ID = HEF.FILE_CODE";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
}
