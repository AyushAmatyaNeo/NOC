<?php
namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\ServiceQuestion;
use Setup\Model\ServiceEventType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ServiceQuestionRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(ServiceQuestion::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        if(!isset($temp['PARENT_QA_ID'])){
            $temp['PARENT_QA_ID']=NULL;
        }
        $this->tableGateway->update($temp, [ServiceQuestion::QA_ID => $id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        
        $select->from(['QA' => ServiceQuestion::TABLE_NAME]);
        $select->join(['ST' => ServiceEventType::TABLE_NAME], "QA." . ServiceQuestion::SERVICE_EVENT_TYPE_ID . "=ST.".ServiceEventType::SERVICE_EVENT_TYPE_ID, ['SERVICE_EVENT_TYPE_NAME' => new Expression('INITCAP(ST.SERVICE_EVENT_TYPE_NAME)')], 'left')
                ->join(['PQA' => ServiceQuestion::TABLE_NAME], "QA." . ServiceQuestion::PARENT_QA_ID . "=PQA.".ServiceQuestion::QA_ID, ['PARENT_QUESTION_EDESC'=>ServiceQuestion::QUESTION_EDESC,'PARENT_QUESTION_NDESC'=>ServiceQuestion::QUESTION_NDESC], 'left');
        
        $select->where(["QA.STATUS='E'"]);
        $select->order([
            "QA." . ServiceQuestion::QA_ID => Select::ORDER_ASCENDING,
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select(function(Select $select)use($id) {
            $select->where([ServiceQuestion::QA_ID => $id]);
        });
        return $result->current();
    }
    public function fetchByServiceEventTypeId($serviceEventTypeId,$parentQaId=null){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        
        if($parentQaId!=null){
            $where = " AND QA.PARENT_QA_ID=".$parentQaId;
        }else{
            $where = " AND QA.PARENT_QA_ID IS NULL";
        }
        
        $select->from(['QA' => ServiceQuestion::TABLE_NAME]);
        $select->join(['ST' => ServiceEventType::TABLE_NAME], "QA." . ServiceQuestion::SERVICE_EVENT_TYPE_ID . "=ST.".ServiceEventType::SERVICE_EVENT_TYPE_ID, ['SERVICE_EVENT_TYPE_NAME' => new Expression('INITCAP(ST.SERVICE_EVENT_TYPE_NAME)')], 'left')
                ->join(['PQA' => ServiceQuestion::TABLE_NAME], "QA." . ServiceQuestion::PARENT_QA_ID . "=PQA.".ServiceQuestion::QA_ID, ['PARENT_QUESTION_EDESC'=>ServiceQuestion::QUESTION_EDESC,'PARENT_QUESTION_NDESC'=>ServiceQuestion::QUESTION_NDESC], 'left');
        
        $select->where([
            "QA.STATUS='E'",
            "QA.SERVICE_EVENT_TYPE_ID=".$serviceEventTypeId.$where
            ]);
        $select->order([
            "QA." . ServiceQuestion::QA_INDEX => Select::ORDER_ASCENDING,
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }

    public function delete($id) {
        $this->tableGateway->update([ServiceQuestion::STATUS => 'D'], [ServiceQuestion::QA_ID => $id]);
    }
}
