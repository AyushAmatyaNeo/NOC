<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Company;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\HrEmployees;

class BranchRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Branch::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Branch::BRANCH_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Branch::class, [Branch::BRANCH_NAME]), false);
                    $select->where([Branch::STATUS => EntityHelper::STATUS_ENABLED]);
                    $select->order([Branch::BRANCH_NAME => Select::ORDER_ASCENDING]);
                });
    }

    public function fetchAllIncludeDisabled() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Branch::class, [Branch::BRANCH_NAME]), false);
                    $select->order([Branch::BRANCH_NAME => Select::ORDER_ASCENDING]);
                });
    }

    public function fetchAllWithCompany() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['B' => Branch::TABLE_NAME]);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Branch::class, [Branch::BRANCH_NAME], null, null, null, null, 'B'), false);
        $companyIdKey = Company::COMPANY_ID;
        $companyNameKey = Company::COMPANY_NAME;
        $select->join(['C' => Company::TABLE_NAME], "C.{$companyIdKey} = B.{$companyIdKey}", [Company::COMPANY_NAME => new Expression("(C.{$companyNameKey})")], Join::JOIN_LEFT);
        $select->where(['B.' . Branch::STATUS => EntityHelper::STATUS_ENABLED]);
        $select->order([
            'B.' . Branch::BRANCH_NAME => Select::ORDER_ASCENDING,
            'C.' . Company::COMPANY_NAME => Select::ORDER_ASCENDING
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $return = $statement->execute();
        return $return;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([Branch::BRANCH_ID => $id]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([Branch::STATUS => 'D'], [Branch::BRANCH_ID => $id]);
    }
    
    public function fetchAllWithBranchManager() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['B' => Branch::TABLE_NAME]);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Branch::class, [Branch::BRANCH_NAME], null, null, null, null, 'B'), false);
        $companyIdKey = Company::COMPANY_ID;
        $companyNameKey = Company::COMPANY_NAME;
        $employeeIdKey = HrEmployees::EMPLOYEE_ID;
        $branchManagerIdKey = Branch::BRANCH_MANAGER_ID;
        $employeeNameKey = HrEmployees::FULL_NAME;
        $select->join(['C' => Company::TABLE_NAME], "C.{$companyIdKey} = B.{$companyIdKey}", [Company::COMPANY_NAME => new Expression("INITCAP(C.{$companyNameKey})")], Join::JOIN_LEFT);
        $select->join(['E' => HrEmployees::TABLE_NAME],"E.{$employeeIdKey} = B.{$branchManagerIdKey}",[HrEmployees::FULL_NAME => new Expression("INITCAP(E.{$employeeNameKey})")], Join::JOIN_LEFT);
        $select->where(['B.' . Branch::STATUS => EntityHelper::STATUS_ENABLED]);
        $select->order([
            'B.' . Branch::BRANCH_NAME => Select::ORDER_ASCENDING,
            'C.' . Company::COMPANY_NAME => Select::ORDER_ASCENDING
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

}
