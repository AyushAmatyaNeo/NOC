<?php

namespace System\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use System\Model\PreferenceSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class PreferenceSetupRepo implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(PreferenceSetup::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [PreferenceSetup::PREFERENCE_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(PreferenceSetup::class, null, null, null, null, null, "P", false,false,[PreferenceSetup::CONSTRAINT_VALUE]), false);
        $select->from(['P' => PreferenceSetup::TABLE_NAME]);
        $companyIdKey = Company::COMPANY_ID;
        $companyNameKey = Company::COMPANY_NAME;
        $select->join(['C' => Company::TABLE_NAME], "C.{$companyIdKey} = P.{$companyIdKey}", [Company::COMPANY_NAME => new Expression("(C.{$companyNameKey})")], Join::JOIN_LEFT);
        $select->where(['P.' . PreferenceSetup::STATUS."='E'"]);
        $select->order([
            'P.' . PreferenceSetup::PREFERENCE_NAME => Select::ORDER_ASCENDING,
            'C.' . Company::COMPANY_NAME => Select::ORDER_ASCENDING
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $return = $statement->execute();
        return $return;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select)use($id){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(PreferenceSetup::class, null, null, null, null, null, null, false,false,[PreferenceSetup::CONSTRAINT_VALUE]), false);
            $select->where([PreferenceSetup::PREFERENCE_ID => $id]);
        });
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->update([PreferenceSetup::STATUS => 'D'], [PreferenceSetup::PREFERENCE_ID => $id]);
    }

    public function fetchByPreferenceName($preferenceName) {
        $result = $this->tableGateway->select(function(Select $select)use($preferenceName) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(PreferenceSetup::class, null, null, null, null, null, null, false,false,[PreferenceSetup::CONSTRAINT_VALUE]), false);
            $select->where([PreferenceSetup::PREFERENCE_NAME => $preferenceName, PreferenceSetup::STATUS => 'E']);
            $select->order(PreferenceSetup::PREFERENCE_ID . " ASC");
        });
        return $result;
    }

    public function getByCondition($preferenceCondition, $preferenceName) {
        $result = $this->tableGateway->select(function(Select $select)use($preferenceCondition, $preferenceName){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(PreferenceSetup::class, null, null, null, null, null, null, false,false,[PreferenceSetup::CONSTRAINT_VALUE]), false);
            $select->where([PreferenceSetup::PREFERENCE_CONDITION => $preferenceCondition, PreferenceSetup::PREFERENCE_NAME => $preferenceName, PreferenceSetup::STATUS => 'E']);
        });
        return $result->current();
    }

}
