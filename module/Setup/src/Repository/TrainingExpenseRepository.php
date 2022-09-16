<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use Setup\Model\Institute;
use Setup\Model\TrainingExpenseSetup;
use Setup\Model\TrainingAccountMap;
use Setup\Model\Training;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class TrainingExpenseRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(TrainingExpenseSetup::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();

        $this->tableGateway->update($temp, [TrainingExpenseSetup::EXPENSE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        
        $select = $sql->select();

        $select->from(['TE' => TrainingExpenseSetup::TABLE_NAME]);
        $select->join(['TM' => Training::TABLE_NAME], "TE." . TrainingExpenseSetup::TRAINING_ID . "=TM." . Training::TRAINING_ID, [Training::TRAINING_NAME => new Expression('(TM.' . Training::TRAINING_NAME . ')')], 'left');
        $select->join(['TA' => TrainingAccountMap::TABLE_NAME], "TE." . TrainingExpenseSetup::EXPENSE_HEAD_ID . "=TA." . TrainingAccountMap::EXPENSE_HEAD_ID, [TrainingAccountMap::EXPENSE_NAME => new Expression('(TA.' . TrainingAccountMap::EXPENSE_NAME . ')')], 'left');
        $select->where(["TE.STATUS='E'"]);
        $select->order("TM." . Training::TRAINING_NAME . " ASC");
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(
                        TrainingExpenseSetup::class, [
                    TrainingExpenseSetup::AMOUNT,TrainingExpenseSetup::DESCRIPTION
                  ],NULL, NULL, NULL, NULL, 'TE')
                , false);

        $select->from(['TE' => TrainingExpenseSetup::TABLE_NAME]);
        $select->join(['T' => Training::TABLE_NAME], "T." . Training::TRAINING_ID . "=TE." . TrainingExpenseSetup::TRAINING_ID, [Training::TRAINING_NAME => new Expression('(T.' . Training::TRAINING_NAME . ')')], 'left');
        $select->where(["TE.EXPENSE_ID" => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();

        return $result->current();

    }

    public function selectAll($employeeId) {
        $today = Helper::getcurrentExpressionDate();
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(
                        Training::class, [
                    Training::TRAINING_NAME
                        ], [
                    Training::START_DATE,
                    Training::END_DATE
                        ], NULL, NULL, NULL, 'T')
                , false);
        $select->from(['T' => Training::TABLE_NAME]);
        $select->join(['I' => Institute::TABLE_NAME], "T." . Training::INSTITUTE_ID . "=I." . Institute::INSTITUTE_ID, [Institute::INSTITUTE_NAME => new Expression('(I.' . Institute::INSTITUTE_NAME . ')')], 'left');

        $select->where([
            "T.STATUS='E'",
            "T.TRAINING_ID NOT IN (SELECT TRAINING_ID FROM HRIS_EMPLOYEE_TRAINING_ASSIGN WHERE STATUS='E' AND EMPLOYEE_ID=$employeeId)"
        ]);

        $select->order("T.START_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function delete($id) {
        $this->tableGateway->update([TrainingExpenseSetup::STATUS => 'D'], [TrainingExpenseSetup::EXPENSE_ID => $id]);
    }

}
