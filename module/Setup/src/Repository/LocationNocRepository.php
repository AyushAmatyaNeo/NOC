<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\LocationNoc;
use Setup\Model\Company;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\HrEmployees;

class LocationNocRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LocationNoc::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [LocationNoc::LOCATION_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LocationNoc::class, [LocationNoc::LOCATION_EDESC]), false);
                    $select->where([LocationNoc::STATUS => EntityHelper::STATUS_ENABLED]);
                    $select->order([LocationNoc::LOCATION_EDESC => Select::ORDER_ASCENDING]);
                });
    }

    public function fetchAllIncludeDisabled() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LocationNoc::class, [LocationNoc::LOCATION_EDESC]), false);
                    $select->order([LocationNoc::LOCATION_EDESC => Select::ORDER_ASCENDING]);
                });
    }

    public function fetchAllWithCompany() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['B' => LocationNoc::TABLE_NAME]);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LocationNoc::class, [LocationNoc::LOCATION_EDESC], null, null, null, null, 'B'), false);
        $companyIdKey = Company::COMPANY_ID;
        $companyNameKey = Company::COMPANY_NAME;
        $select->join(['C' => Company::TABLE_NAME], "C.{$companyIdKey} = B.{$companyIdKey}", [Company::COMPANY_NAME => new Expression("(C.{$companyNameKey})")], Join::JOIN_LEFT);
        $select->where(['B.' . LocationNoc::STATUS => EntityHelper::STATUS_ENABLED]);
        $select->order([
            'B.' . LocationNoc::LOCATION_EDESC => Select::ORDER_ASCENDING,
            'C.' . Company::COMPANY_NAME => Select::ORDER_ASCENDING
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $return = $statement->execute();
        return $return;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([LocationNoc::LOCATION_ID => $id]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([LocationNoc::STATUS => 'D'], [LocationNoc::LOCATION_ID => $id]);
    }
    
    public function fetchAllWithBranchManager() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['B' => LocationNoc::TABLE_NAME]);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LocationNoc::class, [LocationNoc::LOCATION_EDESC], null, null, null, null, 'B'), false);
        $companyIdKey = Company::COMPANY_ID;
        $companyNameKey = Company::COMPANY_NAME;
        $employeeIdKey = HrEmployees::EMPLOYEE_ID;
        $branchManagerIdKey = LocationNoc::BRANCH_MANAGER_ID;
        $employeeNameKey = HrEmployees::FULL_NAME;
        $select->join(['C' => Company::TABLE_NAME], "C.{$companyIdKey} = B.{$companyIdKey}", [Company::COMPANY_NAME => new Expression("INITCAP(C.{$companyNameKey})")], Join::JOIN_LEFT);
        $select->join(['E' => HrEmployees::TABLE_NAME],"E.{$employeeIdKey} = B.{$branchManagerIdKey}",[HrEmployees::FULL_NAME => new Expression("INITCAP(E.{$employeeNameKey})")], Join::JOIN_LEFT);
        $select->where(['B.' . LocationNoc::STATUS => EntityHelper::STATUS_ENABLED]);
        $select->order([
            'B.' . LocationNoc::LOCATION_EDESC => Select::ORDER_ASCENDING,
            'C.' . Company::COMPANY_NAME => Select::ORDER_ASCENDING
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchParentList($id = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(["D1" => LocationNoc::TABLE_NAME])
                ->join(["D2" => LocationNoc::TABLE_NAME], 'D1.PARENT_LOCATION_ID=D2.LOCATION_ID', ["PARENT_LOCATION_EDESC" => new Expression('INITCAP(D2.LOCATION_EDESC)')], "left");
        $select->where(["D1.STATUS= 'E'"]);
        if ($id != null) {
            $select->where(["D1.LOCATION_ID != {$id}"]);
        }
        $select->order(["D1." . LocationNoc::LOCATION_EDESC => Select::ORDER_ASCENDING]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

}
