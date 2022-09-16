<?php

namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\ShiftSetup;
use Setup\Model\Company;
use Traversable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ShiftRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(ShiftSetup::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[ShiftSetup::SHIFT_ID]);
        unset($array[ShiftSetup::CREATED_DT]);
        unset($array[ShiftSetup::STATUS]);
        $this->tableGateway->update($array, [ShiftSetup::SHIFT_ID => $id]);
    }

    public function fetchAll(): Traversable {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ShiftSetup::class, [ShiftSetup::SHIFT_ENAME], [
                    ShiftSetup::START_DATE,
                    ShiftSetup::END_DATE
                        ], [
                    ShiftSetup::START_TIME,
                    ShiftSetup::END_TIME,
                    ShiftSetup::HALF_TIME,
                    ShiftSetup::HALF_DAY_END_TIME
                        ], NULL, NULL, 'S', FALSE, FALSE, [
                    ShiftSetup::LATE_IN,
                    ShiftSetup::EARLY_OUT,
                    ShiftSetup::TOTAL_WORKING_HR,
                    ShiftSetup::ACTUAL_WORKING_HR
                ]), false);
        $select->from(['S' => ShiftSetup::TABLE_NAME]);
        $select->join(['C' => Company::TABLE_NAME], "C." . Company::COMPANY_ID . "=S." . ShiftSetup::COMPANY_ID, [Company::COMPANY_NAME => new Expression('(C.COMPANY_NAME)')], 'left');
        $select->where(["S." . ShiftSetup::STATUS . "='E'"]);
        $select->order("S." . ShiftSetup::SHIFT_ENAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(ShiftSetup::TABLE_NAME);
//        $select->columns(Helper::convertColumnDateFormat($this->adapter, new ShiftSetup(), ['startDate', 'endDate'], ['startTime', 'endTime', 'halfTime', 'halfDayEndTime'], null, ['lateIn', 'earlyOut', 'totalWorkingHr', 'actualWorkingHr']), false);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ShiftSetup::class, [ShiftSetup::SHIFT_ENAME, ShiftSetup::SHIFT_LNAME], [
                    ShiftSetup::START_DATE,
                    ShiftSetup::END_DATE
                        ], [
                    ShiftSetup::START_TIME,
                    ShiftSetup::END_TIME,
                    ShiftSetup::HALF_TIME,
                    ShiftSetup::HALF_DAY_END_TIME,
                    ShiftSetup::GRACE_START_TIME,
                    ShiftSetup::GRACE_END_TIME,
                    ShiftSetup::HALF_DAY_IN_TIME,
                    ShiftSetup::HALF_DAY_OUT_TIME,
                        ], null, null, null, false, false, [
                    ShiftSetup::LATE_IN,
                    ShiftSetup::EARLY_OUT,
                    ShiftSetup::TOTAL_WORKING_HR,
                    ShiftSetup::ACTUAL_WORKING_HR
                ]), false);

//        $select->columns(Helper::convertColumnDateFormat($this->adapter, new ShiftSetup(), ['startDate', 'endDate'], ['startTime', 'endTime', 'halfTime', 'halfDayEndTime'], null, ['lateIn', 'earlyOut', 'totalWorkingHr', 'actualWorkingHr']), false);
        $select->where([ShiftSetup::SHIFT_ID . '=' . $id]);
        $statement = $sql->prepareStatementForSqlObject($select);

//        print_r($statement->getSql());
//        die();

        $result = $statement->execute();
        return $result->current();
    }

    public function fetchActiveRecord() {
        return $rowset = $this->tableGateway->select(function(Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ShiftSetup::class, [ShiftSetup::SHIFT_ENAME, ShiftSetup::SHIFT_LNAME], [
                        ShiftSetup::START_DATE,
                        ShiftSetup::END_DATE
                            ], [
                        ShiftSetup::START_TIME,
                        ShiftSetup::END_TIME,
                        ShiftSetup::HALF_TIME,
                        ShiftSetup::HALF_DAY_END_TIME
                            ], null, null, null, false, false, [
                        ShiftSetup::LATE_IN,
                        ShiftSetup::EARLY_OUT,
                        ShiftSetup::TOTAL_WORKING_HR,
                        ShiftSetup::ACTUAL_WORKING_HR
                    ]), false);
            $select->where([ShiftSetup::STATUS => 'E']);
            $select->order(ShiftSetup::SHIFT_ENAME . " ASC");
        });
    }

    public function delete($id) {
        $this->tableGateway->update([ShiftSetup::STATUS => 'D'], [ShiftSetup::SHIFT_ID => $id]);
    }

    public function DefaultShift() {
        $data = $this->tableGateway->select([ShiftSetup::STATUS => 'E', ShiftSetup::DEFAULT_SHIFT => 'Y']);
        return $data->current();
    }

}
