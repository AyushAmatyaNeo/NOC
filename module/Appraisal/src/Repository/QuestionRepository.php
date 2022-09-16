<?php
namespace Appraisal\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Appraisal\Model\Question;
use Zend\Db\Sql\Select;
use Application\Repository\RepositoryInterface;
use Application\Helper\EntityHelper;

class QuestionRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Question::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(\Application\Model\Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([Question::STATUS=>'D'],[Question::QUESTION_ID=>$id]);
    }

    public function edit(\Application\Model\Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[Question::QUESTION_ID]);
        unset($data[Question::CREATED_DATE]);
        unset($data[Question::STATUS]);
        $this->tableGateway->update($data,[Question::QUESTION_ID=>$id]);
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['AQ' => "HRIS_APPRAISAL_QUESTION"])
                ->join(['AH' => 'HRIS_APPRAISAL_HEADING'], 'AH.HEADING_ID=AQ.HEADING_ID', ["HEADING_EDESC"=>new Expression("INITCAP(AH.HEADING_EDESC)")], "left");
        
        $select->where(["AQ.STATUS='E'"]);
        $select->order("AQ.QUESTION_EDESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select) use($id){
            $select->where([Question::QUESTION_ID => $id, Question::STATUS => 'E']);
        });
        return $result = $rowset->current();
    }
    public function fetchByHeadingId($headingId){
        $rowset= $this->tableGateway->select(function(Select $select) use($headingId) {
            $select->where([Question::STATUS=>'E',Question::HEADING_ID=>$headingId]);
            $select->order(Question::QUESTION_ID." ASC");
        });
        $result = [];
        foreach($rowset as $row){
            array_push($result,$row);
        }
        return $result;
    }
}