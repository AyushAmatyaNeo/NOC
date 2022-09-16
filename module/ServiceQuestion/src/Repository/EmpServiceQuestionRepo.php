<?php

namespace ServiceQuestion\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use ServiceQuestion\Model\EmpServiceQuestion;
use Application\Model\Model;
use Setup\Model\ServiceQuestion;
use Setup\Model\ServiceEventType;
use Application\Helper\EntityHelper;

class EmpServiceQuestionRepo implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(EmpServiceQuestion::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $tempData = $model->getArrayCopyForDB();
        $this->tableGateway->update($tempData,[EmpServiceQuestion::EMP_QA_ID=>$id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(EmpServiceQuestion::class, null, [EmpServiceQuestion::QA_DATE, EmpServiceQuestion::CREATED_DATE, EmpServiceQuestion::MODIFIED_DATE], NULL, NULL, NULL, 'EQA'), false);
        $select->from(['EQA' => EmpServiceQuestion::TABLE_NAME]);
        $select->join(['ST' => ServiceEventType::TABLE_NAME], "EQA." . EmpServiceQuestion::SERVICE_EVENT_TYPE_ID . "=ST." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['SERVICE_EVENT_TYPE_NAME' => new Expression('INITCAP(ST.SERVICE_EVENT_TYPE_NAME)'), 'SERVICE_EVENT_TYPE_ID'], 'left')
               ->join(['E' => 'HRIS_EMPLOYEES'], 'EQA.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)"),"FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left");

        $select->where(["EQA.STATUS='E'"]);
        $select->order([
            "EQA." . EmpServiceQuestion::QA_DATE => Select::ORDER_ASCENDING,
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(EmpServiceQuestion::class, null, [EmpServiceQuestion::QA_DATE, EmpServiceQuestion::CREATED_DATE, EmpServiceQuestion::MODIFIED_DATE], NULL, NULL, NULL, 'EQA'), false);
        $select->from(['EQA' => EmpServiceQuestion::TABLE_NAME]);
        $select->join(['ST' => ServiceEventType::TABLE_NAME], "EQA." . EmpServiceQuestion::SERVICE_EVENT_TYPE_ID . "=ST." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['SERVICE_EVENT_TYPE_NAME' => new Expression('INITCAP(ST.SERVICE_EVENT_TYPE_NAME)'), 'SERVICE_EVENT_TYPE_ID'], 'left')
               ->join(['E' => 'HRIS_EMPLOYEES'], 'EQA.EMPLOYEE_ID=E.EMPLOYEE_ID', ["EMPLOYEE_ID","FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"),"MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"),"LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")], "left");

        $select->where(["EQA.STATUS='E'","EQA.EMP_QA_ID=".$id]);
        $select->order([
            "EQA." . EmpServiceQuestion::QA_DATE => Select::ORDER_ASCENDING,
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getDistinctValue() {
        $sql = "SELECT
DISTINCT
  ST.SERVICE_EVENT_TYPE_ID AS SERVICE_EVENT_TYPE_ID,
  EQA.EMPLOYEE_ID AS EMPLOYEE_ID,
  INITCAP(TO_CHAR(EQA.QA_DATE,'DD-MON-YYYY')) AS QA_DATE,
  ST.SERVICE_EVENT_TYPE_NAME AS SERVICE_EVENT_TYPE_NAME,
  E.FIRST_NAME AS FIRST_NAME,
  E.MIDDLE_NAME AS MIDDLE_NAME,
  E.LAST_NAME AS LAST_NAME
FROM HRIS_EMPLOYEE_SERVICE_QA EQA
LEFT JOIN HRIS_SERVICE_QA QA
ON QA.QA_ID=EQA.QA_ID
LEFT JOIN HRIS_SERVICE_EVENT_TYPES ST
ON QA.SERVICE_EVENT_TYPE_ID=ST.SERVICE_EVENT_TYPE_ID
LEFT JOIN HRIS_EMPLOYEES E
ON EQA.EMPLOYEE_ID=E.EMPLOYEE_ID
WHERE EQA.STATUS           ='E'
AND QA.STATUS              ='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
