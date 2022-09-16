<?php

namespace Application\Repository;

use Application\Model\Model;
use Application\Model\TaskModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\Helper;
use Application\Helper\EnHelper;

class TaskRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TaskModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([TaskModel::DELETED_FLAG => 'Y'], [TaskModel::TASK_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[TaskModel::CREATED_BY]);
        unset($data[TaskModel::CREATED_DT]);
        $this->tableGateway->update($data, [TaskModel::TASK_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['T' => TaskModel::TABLE_NAME]);
        $select->where(["T." . TaskModel::TASK_ID . "='" . $id . "'"]);
//        $select->columns(Helper::convertColumnDateFormat($this->adapter, new NewsModel(), [
//                    'newsDate',
//                        ], NULL, 'N'), false);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchEmployeeTask($id) {
        // $sql = new Sql($this->adapter);
        // $select = $sql->select();
        // $select->from(['T' => TaskModel::TABLE_NAME]);
        // $select->where(["T." . TaskModel::EMPLOYEE_ID . "='" . $id . "'"]);
        // $select->columns(Helper::convertColumnDateFormat($this->adapter, new TaskModel(), [
        //             'endDate',
        //                 ], NULL, 'T'), false);
        // $select->where(["T." . TaskModel::DELETED_FLAG . "='N'"]);
        // $statement = $sql->prepareStatementForSqlObject($select);

        // print_r($statement->getSql());
        // die;

        $sql = "SELECT INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY')) AS END_DATE, T.TASK_ID AS TASK_ID, T.TASK_EDESC AS TASK_EDESC, T.TASK_NDESC AS TASK_NDESC, T.START_DATE AS START_DATE, T.ESTIMATED_TIME AS ESTIMATED_TIME, T.EMPLOYEE_ID AS EMPLOYEE_ID, T.STATUS AS STATUS, T.TASK_PRIORITY AS TASK_PRIORITY, T.REMARKS AS REMARKS, T.COMPANY_ID AS COMPANY_ID, T.BRANCH_ID AS BRANCH_ID, T.CREATED_BY AS CREATED_BY, T.CREATED_DT AS CREATED_DT, T.MODIFIED_BY AS MODIFIED_BY, T.MODIFIED_DT AS MODIFIED_DT, T.APPROVED_FLAG AS APPROVED_FLAG, T.APPROVED_BY AS APPROVED_BY, T.APPROVED_DATE AS APPROVED_DATE, T.DELETED_FLAG AS DELETED_FLAG, T.TASK_TITLE AS TASK_TITLE FROM HRIS_TASK T WHERE T.EMPLOYEE_ID={$id} AND T.DELETED_FLAG='N' ";
        
        //print_r($sql); die();
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

}
