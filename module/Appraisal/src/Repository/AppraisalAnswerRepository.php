<?php
namespace Appraisal\Repository;

use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Application\Model\Model;
use Setup\Model\HrEmployees;
use Appraisal\Model\AppraisalAnswer;
use Appraisal\Model\Heading;
use Appraisal\Model\Question;
use Appraisal\Model\StageQuestion; 
use Appraisal\Model\Stage;

class AppraisalAnswerRepository implements RepositoryInterface{
    private $adapter;
    private $tableGateway;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AppraisalAnswer::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(),[AppraisalAnswer::ANSWER_ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    public function fetchByAllDtl($appraisalId,$questionId,$employeeId,$userId,$appraiserId=null,$reviewerId=null){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['APS' => AppraisalAnswer::TABLE_NAME]);
        if($appraiserId!=null){
            $appId = trim(implode(",",$appraiserId), ",");
            $select->join(['APS1' => "(SELECT ANSWER, RATING, ANSWER_ID,APPRAISAL_ID,QUESTION_ID,EMPLOYEE_ID,USER_ID FROM HRIS_APPRAISAL_ANSWER WHERE USER_ID in (".$appId."))"], "(APS1.APPRAISAL_ID = APS.APPRAISAL_ID AND APS1.EMPLOYEE_ID = APS.EMPLOYEE_ID AND APS1.QUESTION_ID = APS.QUESTION_ID)", ["APPRAISER_ANSWER"=>"ANSWER","APPRAISER_RATING_VAL"=>"RATING","APPRAISER_ANSWER_ID"=>"ANSWER_ID"], "left");
        }
        if($reviewerId!=null){
           $reId = trim(implode(",",$reviewerId), ",");
           $select->join(['APS2' => "(SELECT ANSWER, RATING, ANSWER_ID,APPRAISAL_ID,QUESTION_ID,EMPLOYEE_ID,USER_ID FROM HRIS_APPRAISAL_ANSWER WHERE USER_ID in (".$reId."))"], "(APS2.APPRAISAL_ID = APS.APPRAISAL_ID AND APS2.EMPLOYEE_ID = APS.EMPLOYEE_ID AND APS2.QUESTION_ID = APS.QUESTION_ID)", ["REVIEWER_ANSWER"=>"ANSWER","REVIEWER_RATING_VAL"=>"RATING","REVIEWER_ANSWER_ID"=>"ANSWER_ID"], "left");
        }
        
        $select->where([
            "APS.".AppraisalAnswer::APPRAISAL_ID=>$appraisalId,
            "APS.".AppraisalAnswer::EMPLOYEE_ID=>$employeeId,
            "APS.".AppraisalAnswer::USER_ID=>$userId,
            "APS.".AppraisalAnswer::QUESTION_ID =>$questionId]);
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result->current();
    }
    public function getByAppIdEmpIdUserId($headingId,$appraisalId,$employeeId,$userId,$orderCondition=null,$flag=null){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['APS' => AppraisalAnswer::TABLE_NAME])
                ->join(['Q' => Question::TABLE_NAME], "Q.". Question::QUESTION_ID.'=APS.'.AppraisalAnswer::QUESTION_ID, ["QUESTION_EDESC","ANSWER_TYPE","APPRAISEE_FLAG","APPRAISER_FLAG","REVIEWER_FLAG","APPRAISEE_RATING","APPRAISER_RATING","REVIEWER_RATING","MIN_VALUE","MAX_VALUE"], "left")
                ->join(['H' => Heading::TABLE_NAME], "H.".Heading::HEADING_ID.'=Q.'.Question::HEADING_ID, ["HEADING_EDESC"=>new Expression("INITCAP(H.HEADING_EDESC)")], "left")
                ->join(['S' => Stage::TABLE_NAME], "S.".Stage::STAGE_ID.'=APS.'.AppraisalAnswer::STAGE_ID, ["STAGE_EDESC"=>new Expression("INITCAP(S.STAGE_EDESC)")], "left");
        $select->where([
            "APS.".AppraisalAnswer::APPRAISAL_ID=>$appraisalId,
            "APS.".AppraisalAnswer::EMPLOYEE_ID=>$employeeId,
            "H.".Heading::HEADING_ID =>$headingId]);
        if(gettype($userId)=='array'){
            $user = trim(implode(",",$userId), ",");
            $select->where(["APS.".AppraisalAnswer::USER_ID." in (".$user.")"]);
        }else{
            $select->where(["APS.".AppraisalAnswer::USER_ID=>$userId]);
        }
        if($orderCondition!=null){
            $select->where(["S.ORDER_NO".$orderCondition]);
        }
        if($flag!=null){
           $select->where($flag);
        }
        $select->order("Q.ORDER_NO");
        $statement = $sql->prepareStatementForSqlObject($select);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }
    public function fetchByEmpAppraisalId($employeeId,$appraisalId){
        $result = $this->tableGateway->select([AppraisalAnswer::APPRAISAL_ID=>$appraisalId, AppraisalAnswer::EMPLOYEE_ID=>$employeeId]);
        return $result->current(); 
    }
}
