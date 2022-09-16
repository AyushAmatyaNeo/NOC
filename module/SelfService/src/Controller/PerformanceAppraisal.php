<?php

namespace SelfService\Controller;

use Application\Helper\AppraisalHelper;
use Application\Helper\CustomFormElement;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Appraisal\Model\AppraisalAnswer;
use Appraisal\Model\AppraisalStatus;
use Appraisal\Model\Question;
use Appraisal\Model\Stage;
use Appraisal\Repository\AppraisalAnswerRepository;
use Appraisal\Repository\AppraisalAssignRepository;
use Appraisal\Repository\AppraisalStatusRepository;
use Appraisal\Repository\HeadingRepository;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Repository\AppraisalCompetenciesRepo;
use SelfService\Repository\AppraisalKPIRepository;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class PerformanceAppraisal extends AbstractActionController {

    private $repository;
    private $adapter;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
        $this->repository = new AppraisalAnswerRepository($this->adapter);
    }

    public function indexAction() {
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $result = $appraisalAssignRepo->fetchByEmployeeId($this->employeeId);
        $list = [];
        foreach ($result as $row) {
            $result = $this->repository->fetchByEmpAppraisalId($this->employeeId, $row['APPRAISAL_ID']);
            if ($result != null) {
                $row['ALLOW_ADD'] = false;
                $row['ALLOW_EDIT'] = true;
            } else {
                $row['ALLOW_ADD'] = true;
                $row['ALLOW_EDIT'] = false;
            }
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }

    public function monthlyAction() {
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $result = $appraisalAssignRepo->fetchByEmployeeId($this->employeeId, true);
        $list = [];
        foreach ($result as $row) {
            $result = $this->repository->fetchByEmpAppraisalId($this->employeeId, $row['APPRAISAL_ID']);
            if ($result != null) {
                $row['ALLOW_ADD'] = false;
                $row['ALLOW_EDIT'] = true;
            } else {
                $row['ALLOW_ADD'] = true;
                $row['ALLOW_EDIT'] = false;
            }
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }

    public function viewAction() {
        $request = $this->getRequest();
        $appraisalId = $this->params()->fromRoute('appraisalId');
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $appraisalDurationType = (array) $appraisalAssignRepo->getDurationType($appraisalId);
        $action = null;
        if ($appraisalDurationType != null) {
            $action = ['action' => $appraisalDurationType['DURATION_TYPE'] == 'M' ? 'monthly' : 'index'];
        }
        $employeeRepo = new EmployeeRepository($this->adapter);
        $headingRepo = new HeadingRepository($this->adapter);

        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($this->employeeId, $appraisalId);
        $fromDate = $assignedAppraisalDetail['FROM_DATE'];
        $employeeDetail = $employeeRepo->fetchForProfileById($this->employeeId, $fromDate);
        $appraisalTypeId = $assignedAppraisalDetail['APPRAISAL_TYPE_ID'];
        $currentStageId = $assignedAppraisalDetail['STAGE_ID'];
        $headingList = $headingRepo->fetchByAppraisalTypeId($appraisalTypeId);
        $questionTemplate = [];

        $appraiseeFlag = ["(Q." . Question::APPRAISEE_FLAG . "='Y' OR Q." . Question::APPRAISEE_RATING . "='Y')"];
        $appraiserFlag = ["(Q." . Question::APPRAISER_FLAG . "='Y' OR Q." . Question::APPRAISER_RATING . "='Y') AND (Q." . Question::APPRAISEE_FLAG . "='N' AND Q." . Question::APPRAISEE_RATING . "='N')"];
        $reviewerFlag = ["(Q." . Question::REVIEWER_FLAG . "='Y' OR Q." . Question::REVIEWER_RATING . "='Y') AND (Q." . Question::APPRAISEE_FLAG . "='N' AND Q." . Question::APPRAISEE_RATING . "='N') AND (Q." . Question::APPRAISER_FLAG . "='N' AND Q." . Question::APPRAISER_RATING . "='N')"];

        $appraiserQuestionTemplate = [];
        $appraiseeQuestionTemplate = [];
        $reviewerQuestionTemplate = [];
        $questionForCurStage = 0;
        $questionForCurStageAppraisee = 0;
        $appraiserAvailableAnswer = false;
        $reviewerAvailableAnswer = false;
        foreach ($headingList as $headingRow) {
            //get question list for appraisee with current stage id
            $questionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $appraiseeFlag, $appraisalId, $this->employeeId, $this->employeeId, "=1", [$assignedAppraisalDetail['APPRAISER_ID'], $assignedAppraisalDetail['ALT_APPRAISER_ID']], [$assignedAppraisalDetail['REVIEWER_ID'], $assignedAppraisalDetail['ALT_REVIEWER_ID']]);
            $appraiserQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $appraiserFlag, $appraisalId, $this->employeeId, [$assignedAppraisalDetail['APPRAISER_ID'], $assignedAppraisalDetail['ALT_APPRAISER_ID']], null, null, [$assignedAppraisalDetail['REVIEWER_ID'], $assignedAppraisalDetail['ALT_REVIEWER_ID']]);
            $appraiseeQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $appraiseeFlag, $appraisalId, $this->employeeId, $this->employeeId, "!=1");
            $reviewerQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $reviewerFlag, $appraisalId, $this->employeeId, [$assignedAppraisalDetail['REVIEWER_ID'], $assignedAppraisalDetail['ALT_REVIEWER_ID']]);

            if ($appraiseeQuestionList['questionForCurStage']) {
                $questionForCurStageAppraisee += 1;
            }
            if ($questionList['questionForCurStage']) {
                $questionForCurStage += 1;
            }
            if ($appraiserQuestionList['availableAnswer']) {
                $appraiserAvailableAnswer = true;
            }
            if ($reviewerQuestionList['availableAnswer']) {
                $reviewerAvailableAnswer = true;
            }
            if (count($questionList['questionList']) > 0) {
                array_push($questionTemplate, [
                    'HEADING_ID' => $headingRow['HEADING_ID'],
                    'HEADING_EDESC' => $headingRow['HEADING_EDESC'],
                    'QUESTIONS' => $questionList['questionList']]);
            }
            if (count($appraiserQuestionList['questionList']) > 0) {
                array_push($appraiserQuestionTemplate, [
                    'HEADING_ID' => $headingRow['HEADING_ID'],
                    'HEADING_EDESC' => $headingRow['HEADING_EDESC'],
                    'QUESTIONS' => $appraiserQuestionList['questionList']]);
            }
            if (count($appraiseeQuestionList['questionList']) > 0) {
                array_push($appraiseeQuestionTemplate, [
                    'HEADING_ID' => $headingRow['HEADING_ID'],
                    'HEADING_EDESC' => $headingRow['HEADING_EDESC'],
                    'QUESTIONS' => $appraiseeQuestionList['questionList']]);
            }
            if (count($reviewerQuestionList['questionList']) > 0) {
                array_push($reviewerQuestionTemplate, [
                    'HEADING_ID' => $headingRow['HEADING_ID'],
                    'HEADING_EDESC' => $headingRow['HEADING_EDESC'],
                    'QUESTIONS' => $reviewerQuestionList['questionList']]);
            }
        }
        if ($request->isPost()) {
            try {
                $appraisalStatusRepo = new AppraisalStatusRepository($this->adapter);
                $appraisalStatus = new AppraisalStatus();
                $appraisalStatus->exchangeArrayFromDB($appraisalStatusRepo->fetchByEmpAppId($this->employeeId, $appraisalId)->getArrayCopy());
                $appraisalAnswerModel = new AppraisalAnswer();
                $postData = $request->getPost()->getArrayCopy();
                $answer = $postData['answer'];
                $appraiseeAgree = (isset($postData['appraiseeAgree'])) ? $postData['appraiseeAgree'] : null;
                $i = 0;
                $editMode = false;
                foreach ($answer as $key => $value) {
                    if (strpos($key, 'sr') !== false) {
                        $appraisalAnswerModel->rating = $value;
                        $appraisalAnswerModel->modifiedDate = Helper::getcurrentExpressionDate();
                        $appraisalAnswerModel->modifiedBy = $this->employeeId;
                        $maxAnswerId = (int) (Helper::getMaxId($this->adapter, AppraisalAnswer::TABLE_NAME, AppraisalAnswer::ANSWER_ID));
                        $answerId = ($postData['answerId'][$i] == 0) ? $maxAnswerId : $postData['answerId'][$i];
                        $this->repository->edit($appraisalAnswerModel, $answerId);
                        unset($appraisalAnswerModel);
                    } else {
                        $appraisalAnswerModel = new AppraisalAnswer();
                        $appraisalAnswerModel->answer = (gettype($value) == 'array') ? json_encode($value) : $value;
                        if ($postData['answerId'][$i] == 0) {
                            $appraisalAnswerModel->answerId = (int) (Helper::getMaxId($this->adapter, AppraisalAnswer::TABLE_NAME, AppraisalAnswer::ANSWER_ID)) + 1;
                            $appraisalAnswerModel->appraisalId = $appraisalId;
                            $appraisalAnswerModel->employeeId = $this->employeeId;
                            $appraisalAnswerModel->userId = $this->employeeId;
                            $appraisalAnswerModel->questionId = $key;
                            $appraisalAnswerModel->stageId = $currentStageId;
                            $appraisalAnswerModel->createdDate = Helper::getcurrentExpressionDate();
                            $appraisalAnswerModel->status = 'E';
                            $appraisalAnswerModel->createdBy = $this->employeeId;
                            $appraisalAnswerModel->approvedDate = Helper::getcurrentExpressionDate();
                            $appraisalAnswerModel->companyId = $employeeDetail['COMPANY_ID'];
                            $appraisalAnswerModel->branchId = $employeeDetail['BRANCH_ID'];
                            $this->repository->add($appraisalAnswerModel);
                        } else {
                            $editMode = true;
                            $appraisalAnswerModel->modifiedDate = Helper::getcurrentExpressionDate();
                            $appraisalAnswerModel->modifiedBy = $this->employeeId;
                            $this->repository->edit($appraisalAnswerModel, $key);
                        }
                    }
                    $i += 1;
                }
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::APPRAISEE_AGREE => $appraiseeAgree], $appraisalId, $this->employeeId);

//                if ($assignedAppraisalDetail['HR_FEEDBACK_ENABLE'] != null && $assignedAppraisalDetail['HR_FEEDBACK_ENABLE'] == 'Y') {
//                    $nextStageId = 9; //hr comment stage
//                } else {
                    $nextStageId = AppraisalHelper::getNextStageId($this->adapter, $assignedAppraisalDetail['STAGE_ORDER_NO'] + 1);
//                }
                $appraisalAssignRepo->updateCurrentStageByAppId($nextStageId, $appraisalId, $this->employeeId);
                if ($assignedAppraisalDetail['STAGE_ID'] != 1) {
                    HeadNotification::pushNotification(NotificationEvents::APPRAISEE_FEEDBACK, $appraisalStatus, $this->adapter, $this, null, ['ID' => ($assignedAppraisalDetail['REVIEWED_BY'] != null) ? $assignedAppraisalDetail['REVIEWED_BY'] : $assignedAppraisalDetail['REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                    HeadNotification::pushNotification(NotificationEvents::APPRAISEE_FEEDBACK, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['APPRAISED_BY'], 'USER_TYPE' => "APPRAISER"]);
                    if ($assignedAppraisalDetail['SUPER_REVIEWER_ID'] != null) {
                        HeadNotification::pushNotification(NotificationEvents::APPRAISEE_FEEDBACK, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['SUPER_REVIEWER_ID'], 'USER_TYPE' => "SUPER_REVIEWER"]);
                    }
                }
                $this->flashmessenger()->addMessage("Appraisal Successfully Submitted!!");
                $this->redirect()->toRoute("performanceAppraisal", $action);
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage("Appraisal Submit Failed!!");
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
        $defaultRatingDtl = AppraisalHelper::checkDefaultRatingForEmp($this->adapter, $this->employeeId, $appraisalTypeId);
        $appraisalKPI = new AppraisalKPIRepository($this->adapter);
        $appraisalCompetencies = new AppraisalCompetenciesRepo($this->adapter);
        $keyAchievementDtlNum = $appraisalKPI->countKeyAchievementDtl($this->employeeId, $appraisalId)['NUM'];
        $appraiserRatingDtlNum = $appraisalKPI->countAppraiserRatingDtl($this->employeeId, $appraisalId)['NUM'];
        $appCompetenciesRatingDtlNum = $appraisalCompetencies->countCompetenciesRatingDtl($this->employeeId, $appraisalId)['NUM'];
        
        
        return Helper::addFlashMessagesToArray($this, [
                    'assignedAppraisalDetail' => $assignedAppraisalDetail,
                    'employeeDetail' => $employeeDetail,
                    'questionTemplate' => $questionTemplate,
                    'appraiserQuestionTemplate' => $appraiserQuestionTemplate,
                    'appraiseeQuestionTemplate' => $appraiseeQuestionTemplate,
                    'reviewerQuestionTemplate' => $reviewerQuestionTemplate,
                    'performanceAppraisalObj' => CustomFormElement::formElement(),
                    'customRenderer' => Helper::renderCustomView(),
                    'customRendererForCheckbox' => Helper::renderCustomViewForCheckbox(),
                    'appraisalId' => $appraisalId,
                    'questionForCurStage' => $questionForCurStage,
                    'questionForCurStageAppraisee' => $questionForCurStageAppraisee,
                    'reviewerAvailableAnswer' => $reviewerAvailableAnswer,
                    'appraiserAvailableAnswer' => $appraiserAvailableAnswer,
                    'keyAchievementDtlNum' => $keyAchievementDtlNum,
                    'appraiserRatingDtlNum' => $appraiserRatingDtlNum,
                    'appCompetenciesRatingDtlNum' => $appCompetenciesRatingDtlNum,
                    'defaultRatingDtl' => $defaultRatingDtl,
                    'stagesInstrunction' => EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::INSTRUCTION]),
                    'listUrl' => $this->url()->fromRoute('performanceAppraisal', $action)
        ]);
    }

}
