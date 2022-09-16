<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Appraisal\Model\StageQuestion;
use Application\Model\Model;
use Appraisal\Model\Question;
use Appraisal\Model\Stage;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;

class StageQuestionRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter){
        $this->tableGateway = new TableGateway(StageQuestion::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }
    public function add(Model $model){
        $this->tableGateway->insert($model->getArrayCopyForDb());
    }
    public function edit(Model $model,$id){
        //$data = $model->getArrayCopyForDb();
        //unset($data[''])
    }
    public function delete($id){
        $this->tableGateway->update(
            [StageQuestion::STATUS=>'D'],
            [
                StageQuestion::STAGE_ID=>$combo['STAGE_ID'],
                StageQuestion::QUESTION_ID=>$combo['QUESTION_ID']
                ]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function fetchByQuestionId($questionId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("QS.STAGE_ID AS STAGE_ID"), 
            new Expression("QS.QUESTION_ID AS QUESTION_ID"),
            new Expression("QS.STATUS AS STATUS"), 
            ], true);
        $select->from(['QS' => StageQuestion::TABLE_NAME])
                ->join(['S' => Stage::TABLE_NAME], "S.".Stage::STAGE_ID.'=QS.'.StageQuestion::STAGE_ID, ["STAGE_EDESC"=>new Expression("INITCAP(S.STAGE_EDESC)")], "left")
                ->join(['Q' => Question::TABLE_NAME], "Q.". Question::QUESTION_ID.'=QS.'.StageQuestion::QUESTION_ID, ["QUESTION_EDESC"=>new Expression("INITCAP(Q.QUESTION_EDESC)")], "left");
        
        $select->where(["QS.STATUS='E' AND QS.QUESTION_ID=".$questionId]);
        $select->order("S.STAGE_ID");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    
    public function fetchByQuestionStageId($questionId,$stageId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("QS.STAGE_ID AS STAGE_ID"), 
            new Expression("QS.QUESTION_ID AS QUESTION_ID"),
            new Expression("QS.STATUS AS STATUS"), 
            ], true);
        $select->from(['QS' => StageQuestion::TABLE_NAME])
                ->join(['S' => Stage::TABLE_NAME], "S.".Stage::STAGE_ID.'=QS.'.StageQuestion::STAGE_ID, ["STAGE_EDESC"=>new Expression("INITCAP(S.STAGE_EDESC)")], "left")
                ->join(['Q' => Question::TABLE_NAME], "Q.". Question::QUESTION_ID.'=QS.'.StageQuestion::QUESTION_ID, ["QUESTION_EDESC"=>new Expression("INITCAP(Q.QUESTION_EDESC)")], "left");
        
        $select->where(["QS.QUESTION_ID=".$questionId." AND QS.STAGE_ID=".$stageId." AND QS.STATUS='E' AND Q.STATUS='E' AND S.STATUS='E'"]);
        $select->order("S.STAGE_ID");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function getByStageIdHeadingId($headingId,$currentStageId,$flag=null,$orderCondition=null){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['Q' => Question::TABLE_NAME])
                ->join(['QS' => StageQuestion::TABLE_NAME], "Q.". Question::QUESTION_ID.'=QS.'.StageQuestion::QUESTION_ID, [StageQuestion::QUESTION_ID])
                ->join(['S' => Stage::TABLE_NAME], "S.".Stage::STAGE_ID.'=QS.'.StageQuestion::STAGE_ID, ["STAGE_EDESC"=>new Expression("INITCAP(S.STAGE_EDESC)")]);
        
        $select->where(["Q.HEADING_ID=".$headingId." AND QS.STAGE_ID=".$currentStageId." AND QS.STATUS='E' AND Q.STATUS='E' AND S.STATUS='E'"]);
        if($flag!=null){
            $select->where($flag);
        }
        if($orderCondition!=null){
            $select->where(["S.ORDER_NO".$orderCondition]);
        }
        
        $select->order("Q.ORDER_NO");
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        $list = [];
        foreach($result as $row){
            array_push($list,$row);
        }
        return $list;
    }
    
    public function updateDetail($questionId, $stageId) {
        $this->tableGateway->update(['STATUS' => 'E','MODIFIED_DATE'=> Helper::getcurrentExpressionDate()], ['QUESTION_ID' => $questionId, 'STAGE_ID' => $stageId]);
    }
    
    public function deleteAll($questionId,$stageId){
        $this->tableGateway->update(['STATUS' => 'D','MODIFIED_DATE'=> Helper::getcurrentExpressionDate()], ['QUESTION_ID' => $questionId, 'STAGE_ID' => $stageId]);
    }
}