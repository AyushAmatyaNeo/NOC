<?php
namespace Application\Helper;

use Appraisal\Repository\AppraisalAnswerRepository;
use Appraisal\Repository\DefaultRatingRepository;
use Appraisal\Repository\QuestionOptionRepository;
use Appraisal\Repository\StageQuestionRepository;
use Appraisal\Repository\StageRepository;
use Setup\Repository\EmployeeRepository;

class AppraisalHelper{
    public static function getAllQuestionWidOptions($adapter,$headingId,$currentStageId,$flag,$appraisalId=null,$employeeId=null,$userId=null,$orderCondition=null,$appraiserId=null, $reviewerId=null,$hrId=null){
        $stageQuestionRepo = new StageQuestionRepository($adapter);
        $questionOptionRepo = new QuestionOptionRepository($adapter);
        $appraisalAnswerRepo = new AppraisalAnswerRepository($adapter);
        $curResult = $stageQuestionRepo->getByStageIdHeadingId($headingId,$currentStageId,$flag,$orderCondition);
        if($curResult==null){
            $result = $appraisalAnswerRepo->getByAppIdEmpIdUserId($headingId,$appraisalId,$employeeId,$userId,$orderCondition,$flag);
        }else{
            $result = $curResult;
        }
        $questionList = [];
        $availableAnswer = false;
        foreach($result as $row){
            $optionList = $questionOptionRepo->fetchByQuestionId($row['QUESTION_ID']);
            $answerDtl  = $appraisalAnswerRepo->fetchByAllDtl($appraisalId, $row['QUESTION_ID'], $employeeId, $userId,$appraiserId,$reviewerId,$hrId);
            $options = [];
            foreach($optionList as $optionRow){
                $options[$optionRow['OPTION_ID']]=$optionRow['OPTION_EDESC'];
            }
            
            if((!ISSET($answerDtl)|| gettype($answerDtl)=='undefined'|| $answerDtl==null)){
                $answer=[];
            }else{
                $answer[$answerDtl['ANSWER_ID']]=$answerDtl;
                $availableAnswer=true;
            }
            $new_array = array_merge($row, ['QUESTION_OPTIONS'=>$options,"ANSWER"=>$answer]);
            array_push($questionList,$new_array);
            $answer=[];
        }
        return ['questionList'=>$questionList,'questionForCurStage'=>(($curResult==null)?false:true),'availableAnswer'=>$availableAnswer];
    }
    public static function getNextStageId($adapter,$orderNo){
        $stageRepo = new StageRepository($adapter);
        $stageDetail = $stageRepo->getNextStageId($orderNo);
        return $stageDetail['STAGE_ID'];
    }
    public static function checkDefaultRatingForEmp($adapter,$employeeId,$appraisalTypeId){
        $defaultRatingRepo = new DefaultRatingRepository($adapter);
        $employeeRepo = new EmployeeRepository($adapter);
        $employeeDtl = $employeeRepo->fetchById($employeeId);
        $defaultRatingDtl = $defaultRatingRepo->fetechByAppraisalTypeId($appraisalTypeId);
        $list = [];
        foreach($defaultRatingDtl as $defaultRatingRow){
            $defaultRatingRow['DESIGNATION_IDS'] = json_decode($defaultRatingRow['DESIGNATION_IDS']);
//            $defaultRatingRow['POSITION_IDS'] = json_decode($defaultRatingRow['POSITION_IDS']);
//            if(($employeeDtl['DESIGNATION_ID']!=null && in_array($employeeDtl['DESIGNATION_ID'], $defaultRatingRow['DESIGNATION_IDS'], TRUE))&&($employeeDtl['POSITION_ID']!=NULL && in_array($employeeDtl['POSITION_ID'], $defaultRatingRow['POSITION_IDS'], TRUE))){
            if($employeeDtl['DESIGNATION_ID']!=null && in_array($employeeDtl['DESIGNATION_ID'], $defaultRatingRow['DESIGNATION_IDS'], TRUE)){
                array_push($list,$defaultRatingRow);
            }
        }
        $result = (count($list)>0)?$list[0]->getArrayCopy():null;
        return $result;
    }
}