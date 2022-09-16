<?php

namespace ManagerService\Controller;

use Application\Controller\HrisController;
use Application\Helper\AppraisalHelper;
use Application\Helper\CustomFormElement;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Appraisal\Model\AppraisalAnswer;
use Appraisal\Model\AppraisalStatus;
use Appraisal\Model\Question;
use Appraisal\Model\Setup;
use Appraisal\Model\Stage;
use Appraisal\Repository\AppraisalAnswerRepository;
use Appraisal\Repository\AppraisalAssignRepository;
use Appraisal\Repository\AppraisalReportRepository;
use Appraisal\Repository\AppraisalStatusRepository;
use Appraisal\Repository\HeadingRepository;
use ManagerService\Repository\AppraisalEvaluationRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Model\AppraisalCompetencies;
use SelfService\Model\AppraisalKPI;
use SelfService\Repository\AppraisalCompetenciesRepo;
use SelfService\Repository\AppraisalKPIRepository;
use Setup\Repository\EmployeeRepository;
use TheSeer\Tokenizer\Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class AppraisalEvaluation extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AppraisalEvaluationRepository::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $data['isMonthly'] = false;
                return $this->pullAppraisalViewList($data);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $appraisalList = EntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::APPRAISAL_ID, [Setup::APPRAISAL_EDESC], [Setup::STATUS => 'E'], Setup::APPRAISAL_EDESC, "ASC", NULL, [-1 => "All Type"], TRUE);
        $appraisalSE = $this->getSelectElement(['name' => 'Appraisal', 'id' => 'appraisalId', 'class' => 'form-control', 'label' => 'Appraisal'], $appraisalList);


        $appraisalStageList = EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::STAGE_EDESC], [Stage::STATUS => 'E'], Stage::STAGE_EDESC, "ASC", NULL, [-1 => "All Stage"], TRUE);
        $appraisalStageSE = $this->getSelectElement(['name' => 'Appraisal Stage', 'id' => 'appraisalStageId', 'class' => 'form-control', 'label' => 'Appraisal Stage'], $appraisalStageList);

        return $this->stickFlashMessagesTo([
                    'appraisals' => $appraisalSE,
                    'appraisalStages' => $appraisalStageSE,
                    'userId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function monthlyAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $data['isMonthly'] = true;
                return $this->pullAppraisalViewList($data);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $appraisalList = EntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::APPRAISAL_ID, [Setup::APPRAISAL_EDESC], [Setup::STATUS => 'E'], Setup::APPRAISAL_EDESC, "ASC", NULL, [-1 => "All Type"], TRUE);
        $appraisalSE = $this->getSelectElement(['name' => 'Appraisal', 'id' => 'appraisalId', 'class' => 'form-control', 'label' => 'Appraisal'], $appraisalList);


        $appraisalStageList = EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::STAGE_EDESC], [Stage::STATUS => 'E'], Stage::STAGE_EDESC, "ASC", NULL, [-1 => "All Stage"], TRUE);
        $appraisalStageSE = $this->getSelectElement(['name' => 'Appraisal Stage', 'id' => 'appraisalStageId', 'class' => 'form-control', 'label' => 'Appraisal Stage'], $appraisalStageList);

        return $this->stickFlashMessagesTo([
                    'appraisals' => $appraisalSE,
                    'appraisalStages' => $appraisalStageSE,
                    'userId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function pullAppraisalViewList($data) {
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $appraisalId = $data['appraisalId'];
        $appraisalStageId = $data['appraisalStageId'];
        $userId = $data['userId'];
        $reportType = $data['reportType'];
        $isMonthly = $data['durationType'] == 'M';

        $appraisalStatusRepo = new AppraisalReportRepository($this->adapter);
        $result = $appraisalStatusRepo->fetchFilterdData($fromDate, $toDate, $employeeId, $companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $appraisalId, $appraisalStageId, $reportType, $userId, $isMonthly);
        $list = Helper::extractDbData($result);
        return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
    }

    public function viewAction() {
        $request = $this->getRequest();
        $appraisalId = $this->params()->fromRoute('appraisalId');
        $employeeId = $this->params()->fromRoute('employeeId');
        $tab = $this->params()->fromRoute('tab');
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $appraisalDurationType = (array) $appraisalAssignRepo->getDurationType($appraisalId);
        $action = null;
        if ($appraisalDurationType != null) {
            $action = ['action' => $appraisalDurationType['DURATION_TYPE'] == 'M' ? 'monthly' : 'index'];
        }
        $appraisalAnswerRepo = new AppraisalAnswerRepository($this->adapter);
        $employeeRepo = new EmployeeRepository($this->adapter);
        $headingRepo = new HeadingRepository($this->adapter);
        $userDetail = $employeeRepo->getById($this->employeeId);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($employeeId, $appraisalId);
        $fromDate = $assignedAppraisalDetail['FROM_DATE'];
        $employeeDetail = $employeeRepo->fetchForProfileById($employeeId, $fromDate);
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
        $reviewerAvailableAnswer = false;
        $appraiseeAvailableAnswer = false;
        foreach ($headingList as $headingRow) {
            //get question list for appraisee with current stage id
            $questionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $appraiseeFlag, $appraisalId, $employeeId, $employeeId, "=1", [$assignedAppraisalDetail['APPRAISER_ID'], $assignedAppraisalDetail['ALT_APPRAISER_ID']], [$assignedAppraisalDetail['REVIEWER_ID'], $assignedAppraisalDetail['ALT_REVIEWER_ID']]);
            $appraiserQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $appraiserFlag, $appraisalId, $employeeId, [$assignedAppraisalDetail['APPRAISER_ID'], $assignedAppraisalDetail['ALT_APPRAISER_ID']], null, null, [$assignedAppraisalDetail['REVIEWER_ID'], $assignedAppraisalDetail['ALT_REVIEWER_ID']]);
            $appraiseeQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $appraiseeFlag, $appraisalId, $employeeId, $employeeId, "!=1");
            $reviewerQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $reviewerFlag, $appraisalId, $employeeId, [$assignedAppraisalDetail['REVIEWER_ID'], $assignedAppraisalDetail['ALT_REVIEWER_ID']]);
            if ($appraiserQuestionList['questionForCurStage']) {
                $questionForCurStage += 1;
            }
            if ($reviewerQuestionList['availableAnswer']) {
                $reviewerAvailableAnswer = true;
            }
            if ($appraiseeQuestionList['availableAnswer']) {
                $appraiseeAvailableAnswer = true;
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
                $appraisalKPIRepo = new AppraisalKPIRepository($this->adapter);
                $appraisalComRepo = new AppraisalCompetenciesRepo($this->adapter);
                $appraisalStatus = new AppraisalStatus();
                $appraisalStatus->exchangeArrayFromDB($appraisalStatusRepo->fetchByEmpAppId($employeeId, $appraisalId)->getArrayCopy());
                $postData = $request->getPost()->getArrayCopy();
                $defaultRating = (isset($postData['defaultRating'])) ? $postData['defaultRating'] : null;
                $appraiserOverallRating = (isset($postData['appraiserOverallRating'])) ? $postData['appraiserOverallRating'] : null;
                $appraisalAnswerModel = new AppraisalAnswer();
                $answer = $postData['answer'];
                $i = 0;
                $editMode = false;
                foreach ($answer as $key => $value) {
                    if (strpos($key, 'ar') !== false) {
                        $appraisalAnswerModel->rating = $value;
                        $appraisalAnswerModel->modifiedDate = Helper::getcurrentExpressionDate();
                        $appraisalAnswerModel->modifiedBy = $this->employeeId;
                        $maxAnswerId = (int) (Helper::getMaxId($this->adapter, AppraisalAnswer::TABLE_NAME, AppraisalAnswer::ANSWER_ID));
                        $answerId = ($postData['answerId'][$i] == 0) ? $maxAnswerId : $postData['answerId'][$i];
                        $appraisalAnswerRepo->edit($appraisalAnswerModel, $answerId);
                        unset($appraisalAnswerModel);
                    } else {
                        $appraisalAnswerModel = new AppraisalAnswer();
                        $appraisalAnswerModel->answer = (gettype($value) == 'array') ? json_encode($value) : $value;
                        if ($postData['answerId'][$i] == 0) {
                            $appraisalAnswerModel->answerId = (int) (Helper::getMaxId($this->adapter, AppraisalAnswer::TABLE_NAME, AppraisalAnswer::ANSWER_ID)) + 1;
                            $appraisalAnswerModel->appraisalId = $appraisalId;
                            $appraisalAnswerModel->employeeId = $employeeId;
                            $appraisalAnswerModel->userId = $this->employeeId;
                            $appraisalAnswerModel->questionId = $key;
                            $appraisalAnswerModel->stageId = $currentStageId;
                            $appraisalAnswerModel->createdDate = Helper::getcurrentExpressionDate();
                            $appraisalAnswerModel->status = 'E';
                            $appraisalAnswerModel->createdBy = $this->employeeId;
                            $appraisalAnswerModel->approvedDate = Helper::getcurrentExpressionDate();
                            $appraisalAnswerModel->companyId = $userDetail['COMPANY_ID'];
                            $appraisalAnswerModel->branchId = $userDetail['BRANCH_ID'];
                            $appraisalAnswerRepo->add($appraisalAnswerModel);
                        } else {
                            $editMode = true;
                            $appraisalAnswerModel->modifiedDate = Helper::getcurrentExpressionDate();
                            $appraisalAnswerModel->modifiedBy = $this->employeeId;
                            $appraisalAnswerRepo->edit($appraisalAnswerModel, $postData['answerId'][$i]);
                        }
                    }
                    $i += 1;
                }
                switch ($tab) {
                    case 1:
                        $this->redirect()->toRoute("appraisal-evaluation", ['action' => 'view', 'appraisalId' => $appraisalId, 'employeeId' => $employeeId, 'tab' => 2]);
                        break;
                    case 2:
                        $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::APPRAISED_BY => $this->employeeId], $appraisalId, $employeeId);
                        if ($defaultRating == 'Y') {
                            $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::APPRAISER_OVERALL_RATING => $appraiserOverallRating, AppraisalStatus::DEFAULT_RATING => 'Y', AppraisalStatus::ANNUAL_RATING_COMPETENCY => "", AppraisalStatus::ANNUAL_RATING_KPI => ""], $appraisalId, $employeeId);
                            $appraisalKPIRepo->updateColumnByEmpAppId([AppraisalKPI::APPRAISER_RATING => ""], $employeeId, $appraisalId);
                            $appraisalComRepo->updateColumnByEmpAppId([AppraisalCompetencies::RATING => ""], $employeeId, $appraisalId);
                            $stageId = 6; //appraisee stage
                        } else {
                            $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::APPRAISER_OVERALL_RATING => $appraiserOverallRating, AppraisalStatus::DEFAULT_RATING => 'N'], $appraisalId, $employeeId);
                            $stageId = AppraisalHelper::getNextStageId($this->adapter, $assignedAppraisalDetail['STAGE_ORDER_NO'] + 1);
                        }
                        $appraisalAssignRepo->updateCurrentStageByAppId($stageId, $appraisalId, $employeeId);
                        HeadNotification::pushNotification(NotificationEvents::APPRAISAL_EVALUATION, $appraisalStatus, $this->adapter, $this, ['ID' => $this->employeeId], ['ID' => $assignedAppraisalDetail['REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);

                        $this->flashmessenger()->addMessage("Appraisal Successfully Submitted!!");
                        $this->redirect()->toRoute("appraisal-evaluation", $action);
                        break;
                }
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage("Appraisal Submit Failed!!");
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
        $defaultRatingDtl = AppraisalHelper::checkDefaultRatingForEmp($this->adapter, $employeeId, $appraisalTypeId);
        $appraisalKPI = new AppraisalKPIRepository($this->adapter);
        $appraisalCompetencies = new AppraisalCompetenciesRepo($this->adapter);
        $keyAchievementDtlNum = $appraisalKPI->countKeyAchievementDtl($employeeId, $appraisalId)['NUM'];
        $appraiserRatingDtlNum = $appraisalKPI->countAppraiserRatingDtl($employeeId, $appraisalId)['NUM'];
        $appCompetenciesRatingDtlNum = $appraisalCompetencies->countCompetenciesRatingDtl($employeeId, $appraisalId)['NUM'];
        return Helper::addFlashMessagesToArray($this, [
                    'assignedAppraisalDetail' => $assignedAppraisalDetail,
                    'employeeDetail' => $employeeDetail,
                    'questionTemplate' => $questionTemplate,
                    'appraiserQuestionTemplate' => $appraiserQuestionTemplate,
                    'appraiseeQuestionTemplate' => $appraiseeQuestionTemplate,
                    'reviewerQuestionTemplate' => $reviewerQuestionTemplate,
                    'questionForCurStage' => $questionForCurStage,
                    'performanceAppraisalObj' => CustomFormElement::formElement(),
                    'customRenderer' => Helper::renderCustomView(),
                    'customRendererForCheckbox' => Helper::renderCustomViewForCheckbox(),
                    'appraisalId' => $appraisalId,
                    'employeeId' => $employeeId,
                    'tab' => $tab,
                    'reviewerAvailableAnswer' => $reviewerAvailableAnswer,
                    'appraiseeAvailableAnswer' => $appraiseeAvailableAnswer,
                    'keyAchievementDtlNum' => $keyAchievementDtlNum,
                    'appraiserRatingDtlNum' => $appraiserRatingDtlNum,
                    'appCompetenciesRatingDtlNum' => $appCompetenciesRatingDtlNum,
                    'defaultRatingDtl' => $defaultRatingDtl,
                    'stagesInstrunction' => EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::INSTRUCTION]),
                    'listUrl' => $this->url()->fromRoute('appraisal-evaluation', $action)
        ]);
    }

}
