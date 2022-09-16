<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Appraisal\Model\Stage;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\EntityHelper;

class StageRepository implements RepositoryInterface{
    
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
       $this->adapter = $adapter; 
       $this->tableGateway = new TableGateway(Stage::TABLE_NAME,$adapter);
    }

    public function add(\Application\Model\Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Stage::STATUS=>'D'],[Stage::STAGE_ID=>$id]);
    }

    public function edit(\Application\Model\Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[Stage::STAGE_ID]);
        unset($data[Stage::CREATED_DATE]);
        unset($data[Stage::STATUS]);
        $this->tableGateway->update($data,[Stage::STAGE_ID=>$id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(
        EntityHelper::getColumnNameArrayWithOracleFns(Stage::class,
                [Stage::STAGE_EDESC, Stage::STAGE_NDESC],
                [Stage::START_DATE, Stage::END_DATE]),false);
        $select->from("HRIS_APPRAISAL_STAGE");
        
        $select->where(["STATUS='E'"]);
        $select->order("STAGE_EDESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(
        EntityHelper::getColumnNameArrayWithOracleFns(Stage::class,
                [Stage::STAGE_EDESC, Stage::STAGE_NDESC],
                [Stage::START_DATE, Stage::END_DATE]),false);
        $select->from("HRIS_APPRAISAL_STAGE");
        
        $select->where(["STAGE_ID=".$id]);
        $select->order("STAGE_EDESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function getNextStageId($orderNo){
        $result = $this->tableGateway->select([Stage::ORDER_NO=>$orderNo,Stage::STATUS=>'E']);
        return $result->current();
    }
}
