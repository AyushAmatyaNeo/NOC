<?php

namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\ShiftAdjustmentModel;
use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ShiftAdjustmentRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(ShiftAdjustmentModel::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[ShiftAdjustmentModel::ADJUSTMENT_ID]);
        unset($array[ShiftAdjustmentModel::CREATED_DT]);
        $this->tableGateway->update($array, [ShiftAdjustmentModel::ADJUSTMENT_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $customCols = ["BS_DATE((SA.ADJUSTMENT_START_DATE)) AS ADJUSTMENT_START_DATE_N",
            "BS_DATE((SA.ADJUSTMENT_END_DATE)) AS ADJUSTMENT_END_DATE_N"];
        $select->from(['SA' => ShiftAdjustmentModel::TABLE_NAME]);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ShiftAdjustmentModel::class, NULL, [
                    ShiftAdjustmentModel::ADJUSTMENT_START_DATE,
                    ShiftAdjustmentModel::ADJUSTMENT_END_DATE
                        ], [
                    ShiftAdjustmentModel::START_TIME,
                    ShiftAdjustmentModel::END_TIME
                        ], NULL, NULL, 'SA', FALSE, FALSE, NULL, $customCols), false);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(ShiftAdjustmentModel::TABLE_NAME);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ShiftAdjustmentModel::class, NULL, [
                    ShiftAdjustmentModel::ADJUSTMENT_START_DATE,
                    ShiftAdjustmentModel::ADJUSTMENT_END_DATE
                        ], [
                    ShiftAdjustmentModel::START_TIME,
                    ShiftAdjustmentModel::END_TIME
                        ], NULL, NULL, NULL, FALSE, FALSE, NULL), false);
        $select->where([ShiftAdjustmentModel::ADJUSTMENT_ID . '=' . $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchAssignedEmployees($id) {
        $sql = "SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEE_SHIFT_ADJUSTMENT WHERE ADJUSTMENT_ID= {$id}";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($rawResult);
    }

    public function insertShiftAdjustment($startTime, $endTime, $adjustmentStartDate, $adjustmentEndDate, $employeeList, $employeeId = null, $id = null) {
        $employeeAdjustment = '';
        foreach ($employeeList as $employee) {
            $employeeAdjustment = $employeeAdjustment . "
                INSERT INTO HRIS_EMPLOYEE_SHIFT_ADJUSTMENT
                (
                  ADJUSTMENT_ID,
                  EMPLOYEE_ID
                )
                VALUES
                (
                  V_ADJUSTMENT_ID,
                  {$employee}
                );";
        }
        if ($id == "") {
            $sql = "DO BEGIN
            DECLARE V_ADJUSTMENT_ID NUMBER;
            DECLARE V_ADJUSTMENT_START_DATE DATE  :=TO_DATE('{$adjustmentStartDate}', 'DD-MON-YYYY');
            DECLARE V_ADJUSTMENT_END_DATE   DATE  :=TO_DATE('{$adjustmentEndDate}','DD-MON-YYYY');
            DECLARE V_START_TIME            TIMESTAMP  :={$startTime};
            DECLARE V_END_TIME              TIMESTAMP  :={$endTime};
            DECLARE V_EMPLOYEE_ID           NUMBER  :={$employeeId};    
            DECLARE  V_DIFF NUMBER:=(DAYS_BETWEEN((V_ADJUSTMENT_START_DATE),(V_ADJUSTMENT_END_DATE)));
                BEGIN
                  SELECT IFNULL(MAX(ADJUSTMENT_ID)+1,1) INTO V_ADJUSTMENT_ID FROM HRIS_SHIFT_ADJUSTMENT;
                  INSERT
                  INTO HRIS_SHIFT_ADJUSTMENT
                    (
                      ADJUSTMENT_ID,
                      START_TIME,
                      END_TIME,
                      ADJUSTMENT_START_DATE,
                      ADJUSTMENT_END_DATE,
                      CREATED_DT,
                      CREATED_BY
                    )
                    VALUES
                    (
                      V_ADJUSTMENT_ID,
                    V_START_TIME,
                      V_END_TIME,
                      V_ADJUSTMENT_START_DATE,
                      V_ADJUSTMENT_END_DATE,
                      CURRENT_DATE,
                     V_EMPLOYEE_ID
                    );
                    {$employeeAdjustment}
                    
                  END;
            END ";
        } else {
            $sql = "
            DO BEGIN    
            DECLARE  V_ADJUSTMENT_ID         NUMBER:={$id};
            DECLARE  V_ADJUSTMENT_START_DATE DATE  :=TO_DATE('{$adjustmentStartDate}', 'DD-MON-YYYY');
            DECLARE  V_ADJUSTMENT_END_DATE   DATE  :=TO_DATE('{$adjustmentEndDate}','DD-MON-YYYY');
            DECLARE  V_START_TIME            TIMESTAMP  :={$startTime};
            DECLARE  V_END_TIME              TIMESTAMP  :={$endTime};
            DECLARE  V_EMPLOYEE_ID           NUMBER  :={$employeeId};    
            DECLARE  V_DIFF NUMBER:=(DAYS_BETWEEN((V_ADJUSTMENT_START_DATE),(V_ADJUSTMENT_END_DATE)));
                BEGIN
                  UPDATE HRIS_SHIFT_ADJUSTMENT
                    SET START_TIME         =V_START_TIME,
                      END_TIME             =V_END_TIME,
                      ADJUSTMENT_START_DATE=V_ADJUSTMENT_START_DATE,
                      ADJUSTMENT_END_DATE  =V_ADJUSTMENT_END_DATE,
                      MODIFIED_DT          =CURRENT_DATE,
                      MODIFIED_BY          =V_EMPLOYEE_ID
                    WHERE ADJUSTMENT_ID    = V_ADJUSTMENT_ID; 
                  DELETE FROM HRIS_EMPLOYEE_SHIFT_ADJUSTMENT WHERE ADJUSTMENT_ID=V_ADJUSTMENT_ID;
                    {$employeeAdjustment}
                END;
            END ";
        }

        // echo"<pre>";print_r($sql);die;
        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

}
