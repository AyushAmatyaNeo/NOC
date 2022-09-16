<?php

namespace Overtime\Repository;

use Application\Helper\EntityHelper;
use Overtime\Model\CompulsoryOvertime;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class OvertimeAutomationRepository {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(CompulsoryOvertime::TABLE_NAME, $adapter);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CompulsoryOvertime::class, NULL, [
                                CompulsoryOvertime::START_DATE,
                                CompulsoryOvertime::END_DATE,
                                    ], NULL, NULL, NULL, NULL, FALSE, FALSE, [
                                CompulsoryOvertime::EARLY_OVERTIME_HR,
                                CompulsoryOvertime::LATE_OVERTIME_HR
                            ]), false);
                    $select->where([CompulsoryOvertime::STATUS => EntityHelper::STATUS_ENABLED]);
                });
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(CompulsoryOvertime::TABLE_NAME);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CompulsoryOvertime::class, NULL, [
                    CompulsoryOvertime::START_DATE,
                    CompulsoryOvertime::END_DATE,
                        ], NULL, NULL, NULL, NULL, FALSE, FALSE, [
                    CompulsoryOvertime::EARLY_OVERTIME_HR,
                    CompulsoryOvertime::LATE_OVERTIME_HR
                ]), false);
        $select->where([CompulsoryOvertime::COMPULSORY_OVERTIME_ID => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchAssignedEmployees($id) {
        $sql = "SELECT ECO.EMPLOYEE_ID,
                  E.FULL_NAME AS EMPLOYEE_NAME,
                  C.COMPANY_NAME,
                  B.BRANCH_NAME,
                  D.DEPARTMENT_NAME
                FROM HRIS_EMPLOYEE_COMPULSORY_OT ECO
                LEFT JOIN HRIS_EMPLOYEES E
                ON (ECO.EMPLOYEE_ID= E.EMPLOYEE_ID)
                LEFT JOIN HRIS_COMPANY C
                ON (E.COMPANY_ID=C.COMPANY_ID)
                LEFT JOIN HRIS_BRANCHES B
                ON (E.BRANCH_ID=B.BRANCH_ID)
                LEFT JOIN HRIS_DEPARTMENTS D
                ON (E.DEPARTMENT_ID             =D.DEPARTMENT_ID)
                WHERE ECO.COMPULSORY_OVERTIME_ID={$id}";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return iterator_to_array($rawResult, false);
    }

    public function wizardProcedure($compulsoryOtDesc, $earlyOvertimeHr, $lateOvertimeHr, $startDate, $endDate, $employeeList, $employeeId = null, $id = null) {
        $employeeAssign = '';


        foreach ($employeeList as $employee) {
            $employeeAssign = $employeeAssign . "
                 INSERT
                      INTO HRIS_EMPLOYEE_COMPULSORY_OT
                        (
                          EMPLOYEE_ID,
                          COMPULSORY_OVERTIME_ID
                        )
                        VALUES
                        (
                          {$employee},V_COMPULSORY_OVERTIME_ID
                        );";
        }
        if ($id == "") {
            $sql = "
            DO BEGIN        
            DECLARE  V_COMPULSORY_OVERTIME_ID NUMBER;
            DECLARE  V_COMPULSORY_OT_DESC     VARCHAR2(255) default '{$compulsoryOtDesc}';
            DECLARE  V_EARLY_OVERTIME_HR      NUMBER default {$earlyOvertimeHr};
            DECLARE  V_LATE_OVERTIME_HR       NUMBER default {$lateOvertimeHr};
            DECLARE  V_START_DATE             DATE  default TO_DATE('{$startDate}','DD-MON-YYYY');
            DECLARE  V_END_DATE               DATE  default TO_DATE('{$endDate}','DD-MON-YYYY');
            DECLARE  V_EMPLOYEE_ID            NUMBER  default {$employeeId};
            DECLARE  V_STATUS                 CHAR(1) default 'E';
                    BEGIN
                      SELECT IFNULL(MAX(COMPULSORY_OVERTIME_ID),0)+1
                      INTO V_COMPULSORY_OVERTIME_ID
                      FROM HRIS_COMPULSORY_OVERTIME;
                      INSERT
                      INTO HRIS_COMPULSORY_OVERTIME
                        (
                          COMPULSORY_OVERTIME_ID,
                          COMPULSORY_OT_DESC,
                          EARLY_OVERTIME_HR,
                          LATE_OVERTIME_HR,
                          START_DATE,
                          END_DATE,
                          CREATED_DT,
                          CREATED_BY,
                          STATUS
                        )
                        VALUES
                        (
                          V_COMPULSORY_OVERTIME_ID,
                          V_COMPULSORY_OT_DESC,
                          V_EARLY_OVERTIME_HR,
                          V_LATE_OVERTIME_HR,
                          V_START_DATE,
                          V_END_DATE,
                          CURRENT_DATE,
                          V_EMPLOYEE_ID,
                          V_STATUS
                        );
                     {$employeeAssign}
                    END;
                    END;";
                  
        } else {
            $sql = "
                
            DO BEGIN
            DECLARE      V_COMPULSORY_OVERTIME_ID NUMBER default {$id};
            DECLARE      V_EARLY_OVERTIME_HR      NUMBER       default {$earlyOvertimeHr};
            DECLARE      V_LATE_OVERTIME_HR       NUMBER       default {$lateOvertimeHr};
            DECLARE      V_START_DATE             DATE         default TO_DATE('{$startDate}','DD-MON-YYYY');
            DECLARE      V_END_DATE               DATE         default TO_DATE('{$endDate}','DD-MON-YYYY');
            DECLARE      V_EMPLOYEE_ID            NUMBER       default {$employeeId};
            DECLARE      V_STATUS                 CHAR(1) default 'E';
                BEGIN
                  UPDATE HRIS_COMPULSORY_OVERTIME
                  SET EARLY_OVERTIME_HR       =V_EARLY_OVERTIME_HR,
                    LATE_OVERTIME_HR          =V_LATE_OVERTIME_HR,
                    START_DATE                =V_START_DATE,
                    END_DATE                  =V_END_DATE,
                    MODIFIED_DT               =CURRENT_DATE,
                    MODIFIED_BY               =V_EMPLOYEE_ID
                  WHERE COMPULSORY_OVERTIME_ID=V_COMPULSORY_OVERTIME_ID;
                  DELETE
                  FROM HRIS_EMPLOYEE_COMPULSORY_OT
                  WHERE COMPULSORY_OVERTIME_ID=V_COMPULSORY_OVERTIME_ID;
                  {$employeeAssign}
                END;
                END;";
                
        }
        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

    public function delete($id) {
        $sql = "
            BEGIN
              HRIS_COMPULSORY_OT_CANCEL({$id});
            END;";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }

}
