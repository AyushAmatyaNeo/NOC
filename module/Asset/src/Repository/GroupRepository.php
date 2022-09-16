<?php

namespace Asset\Repository;

use Application\Model\Model;
use Application\Helper\EntityHelper;
use Application\Repository\RepositoryInterface;
use Asset\Model\Group;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class GroupRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter; 
       $this->tableGateway = new TableGateway(Group::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
         $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Group::STATUS=>'D'],[Group::ASSET_GROUP_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[Group::ASSET_GROUP_ID]);
        unset($data[Group::CREATED_DATE]);
        unset($data[Group::STATUS]);
        $this->tableGateway->update($data,[Group::ASSET_GROUP_ID=>$id]);
    }

//    public function fetchAll() {
//        return $this->tableGateway->select();
//    }
    
    public function fetchAll() {
         return $this->tableGateway->select(function(Select $select){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Group::class,[Group::ASSET_GROUP_EDESC,Group::ASSET_GROUP_NDESC]),false);
            $select->where([Group::STATUS=>'E']);
            $select->order(Group::ASSET_GROUP_EDESC." ASC");
        });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select) use($id){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Group::class,[Group::ASSET_GROUP_EDESC,Group::ASSET_GROUP_NDESC]),false);
            $select->where([Group::ASSET_GROUP_ID => $id, Group::STATUS => 'E']);
        });
        return $result = $rowset->current();
    }

}
