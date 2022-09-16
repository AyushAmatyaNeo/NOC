<?php

namespace Asset\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Asset\Model\Group;
use Asset\Model\Setup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\EntityHelper;

class SetupRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Setup::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
//                        echo '<pre>';
//                print_r($model);
//                echo '</pre>';
//                die();
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
         $this->tableGateway->update([Setup::STATUS=>'D'],[Setup::ASSET_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[Setup::ASSET_ID]);
        unset($data[Setup::CREATED_DATE]);
        unset($data[Setup::STATUS]);
        $this->tableGateway->update($data, [Setup::ASSET_ID => $id]);
    }
    
    
    public function updateRemainingAssetBalance(Model $model, $id){
        $data = $model->getArrayCopyForDB();
        unset($data[Setup::ASSET_ID]);
        unset($data[Setup::CREATED_DATE]);
        unset($data[Setup::MODIFIED_DATE]);
        unset($data[Setup::STATUS]);
        $this->tableGateway->update($data, [Setup::ASSET_ID => $id]);
        
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Setup::class,[Setup::ASSET_EDESC,Setup::ASSET_NDESC,Setup::BRAND_NAME],null,null,null,null,"A"),false);
        $select->from(['A' => Setup::TABLE_NAME])
                ->join(['AG' => Group::TABLE_NAME], 'A.' . Setup::ASSET_GROUP_ID . '=AG.' . Group::ASSET_GROUP_ID, ["ASSET_GROUP_EDESC"=>new Expression("INITCAP(AG.ASSET_GROUP_EDESC)")], "left");

        $select->where(["A." . Setup::STATUS . "='E'"]);
        $select->order("A." . Setup::ASSET_EDESC);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['E' => Setup::TABLE_NAME]);
        $select->where(["E." . Setup::ASSET_ID . "='".$id."'"]);
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Setup::class,
                [Setup::ASSET_EDESC,Setup::ASSET_NDESC,Setup::BRAND_NAME],
                [Setup::PURCHASE_DATE,Setup::EXPIARY_DATE],null,null,null,"E"),false);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

}
