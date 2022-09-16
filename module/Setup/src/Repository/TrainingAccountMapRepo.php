<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use Setup\Model\Institute;
use Setup\Model\TrainingAccountMap;
use Setup\Model\Training;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class TrainingAccountMapRepo implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(TrainingAccountMap::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        if (!$temp['INSTITUTE_ID']) {
            $temp['INSTITUTE_ID'] = null;
        }
        if (!$temp['COMPANY_ID']) {
            $temp['COMPANY_ID'] = null;
        }
        $this->tableGateway->update($temp, [TrainingExpenseSetup::TRAINING_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from(['TE' => TrainingAccountMap::TABLE_NAME]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
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
        $select->where(["T.TRAINING_ID" => $id]);
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
        $this->tableGateway->update([Training::STATUS => 'D'], [Training::TRAINING_ID => $id]);
    }

}
