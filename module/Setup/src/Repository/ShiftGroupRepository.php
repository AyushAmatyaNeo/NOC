<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\ShiftGroup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\Helper;

class ShiftGroupRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(ShiftGroup::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        unset($array[ShiftGroup::CASE_ID]);
        unset($array[ShiftGroup::CREATED_DT]);

        $this->tableGateway->update($array, [ShiftGroup::CASE_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([ShiftGroup::STATUS => 'D'], [ShiftGroup::CASE_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function fetchGroupRecord() {
        $sql = "
                select 
                CASE_ID,
                CASE_NAME,
                TO_CHAR(START_DATE,'DD-MON-YYYY') as START_DATE,
                TO_CHAR(END_DATE,'DD-MON-YYYY') as END_DATE
                from HRIS_BEST_CASE_SETUP where status = 'E' ";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return Helper::extractDbData($result);
    }

    public function fetchById($id) {

//        $sql = "select CASE_ID,
//                CASE_NAME,
//                TO_CHAR(START_DATE,'DD-Mon-YYYY') as START_DATE,
//                TO_CHAR(END_DATE,'DD-Mon-YYYY') as END_DATE
//                from HRIS_BEST_CASE_SETUP where CASE_ID = {$id}";
//
//        $statement = $this->adapter->query($sql);
//        $result = $statement->execute();
//        return $result->current();
//        
        $row = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ShiftGroup::class, [ShiftGroup::CASE_ID, ShiftGroup::CASE_NAME], [ShiftGroup::START_DATE, ShiftGroup::END_DATE]), false);
            $select->where([ShiftGroup::CASE_ID => $id]);
        });
        
        return $row->current();
    }

    public function getShifts() {
        $sql = "  
            SELECT SHIFT_ID FROM HRIS_BEST_CASE_SHIFT_MAP";
        return Helper::extractDbData(EntityHelper::rawQueryResult($this->adapter, $sql));
    }

    public function getShiftsById($id) {
        $boundedParams = [];
        $boundedParams['id'] = $id;
        $sql = "  
            SELECT SHIFT_ID FROM HRIS_BEST_CASE_SHIFT_MAP WHERE CASE_ID = ?";
        return Helper::extractDbData(EntityHelper::rawQueryResult($this->adapter, $sql, $boundedParams));
    }

    public function mapShifts($caseId, $shifts) {
        $boundedParams = [];
        
            
        foreach ($shifts as $shift) {
            $boundedParams['caseId'] = $caseId;
            $boundedParams['shift'] = $shift;
            $sql = "INSERT INTO HRIS_BEST_CASE_SHIFT_MAP(CASE_ID, SHIFT_ID) VALUES (?, ?)";
            
            $statement = $this->adapter->query($sql);
            $statement->execute($boundedParams);
        }
        return;
    }

    public function deleteMappedShifts($caseId) {
        $boundedParams = [];
        $boundedParams['caseId'] = $caseId;
        $sql = "DELETE FROM HRIS_BEST_CASE_SHIFT_MAP WHERE CASE_ID = ?";
        EntityHelper::rawQueryResult($this->adapter, $sql, $boundedParams);
        return;
    }

}
