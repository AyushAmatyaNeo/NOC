<?php

namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Appraisal\Repository\StageQuestionRepository;
use Appraisal\Repository\StageRepository;
use Appraisal\Repository\QuestionRepository;
use Appraisal\Repository\HeadingRepository;
use Application\Custom\CustomViewModel;
use Appraisal\Repository\TypeRepository;
use Appraisal\Model\StageQuestion;

class StageQuestionController extends AbstractActionController {

    private $repository;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new StageQuestionRepository($adapter);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            switch ($postData->action) {
                case "getAssignedStagList":
                    $responseData = $this->getAssignedStagList($postData->data);
                    break;
                case "getHeadingList":
                    $responseData = $this->getHeadingList($postData->data);
                    break;
                case "stageAssign":
                    $responseData = $this->stageAssign($postData->data);
                    break;
                default:
                    $responseData = [
                        "success" => false
                    ];
                    break;
            }
            return new CustomViewModel($responseData);
        } else {
            $typeRepo = new TypeRepository($this->adapter);
            $typeResult = $typeRepo->fetchAll();
            return Helper::addFlashMessagesToArray($this, [
                        'list' => "list",
                        'typeResult' => $typeResult
            ]);
        }
    }

    public function getAssignedStagList($data) {
        $questionId = $data['questionId'];
        $stageRepo = new StageRepository($this->adapter);
        $stageResult = $stageRepo->fetchAll();
        $stageList = [];

        $questionRepo = new QuestionRepository($this->adapter);
        $stageQuestionRepo = new StageQuestionRepository($this->adapter);
        $assignedStageList = [];
        if (strpos($questionId, "H") !== false) {
            $headingId = trim($questionId, "H");
            $questionList = $questionRepo->fetchByHeadingId((int) $headingId);
            foreach ($questionList as $questionRow) {
                $result = $stageQuestionRepo->fetchByQuestionId((int) $questionRow['QUESTION_ID']);
                if ($result) {
                    foreach ($result as $row) {
                        array_push($assignedStageList, [
                            'STAGE_ID' => $row['STAGE_ID']
                        ]);
                    }
                }
            }
        } else {
            $result = $stageQuestionRepo->fetchByQuestionId((int) $questionId);
            if ($result) {
                foreach ($result as $row) {
                    array_push($assignedStageList, [
                        'STAGE_ID' => $row['STAGE_ID']
                    ]);
                }
            }
        }
        $newArrayList = array_unique($assignedStageList, SORT_REGULAR);
        foreach ($stageResult as $stageRow) {
            array_push($stageList, $stageRow);
        }
        return ["success" => true, "data" => [
                'stageList' => $stageList,
                'assignedStageList' => $newArrayList
        ]];
    }

    public function generateQuestion($headingId) {
        $questionRepo = new QuestionRepository($this->adapter);
        $result = $questionRepo->fetchByHeadingId($headingId);
        $questionList = array();
        foreach ($result as $row) {
            $questionList[] = array(
                "text" => $row['QUESTION_EDESC'],
                "id" => $row['QUESTION_ID'],
                "icon" => "fa fa-folder icon-state-success"
            );
        }
        return $questionList;
    }

    public function getHeadingList($data) {
        $appraisalTypeId = $data['appraisalTypeId'];
        $headingRepo = new HeadingRepository($this->adapter);
        $result = $headingRepo->fetchByAppraisalTypeId($appraisalTypeId);
        $num = count($result);
        if ($num > 0) {
            $temArray = array();
            foreach ($result as $row) {
                $question = $this->generateQuestion($row['HEADING_ID']);
                if ($question) {
                    $temArray[] = array(
                        "text" => $row['HEADING_EDESC'],
                        "id" => "H" . $row['HEADING_ID'],
                        "icon" => "fa fa-folder icon-state-success",
                        "children" => $question
                    );
                } else {
                    $temArray[] = array(
                        "text" => $row['HEADING_EDESC'],
                        "id" => "H" . $row['HEADING_ID'],
                        "icon" => "fa fa-folder icon-state-success"
                    );
                }
            }
            return ["success" => true, "data" => $temArray];
        } else {
            return ["success" => false];
        }
    }

    public function stageAssign($data) {
        $stageQuestionModel = new StageQuestion();
        $stageQuestionRepo = new StageQuestionRepository($this->adapter);
        $questionRepo = new QuestionRepository($this->adapter);
        $stageId = (int) $data['stageId'];
        $questionId = $data['questionId'];
        $checked = $data['checked'];

        if (strpos($questionId, "H") !== false) {
            $headingId = trim($questionId, "H");
            $questionList = $questionRepo->fetchByHeadingId((int) $headingId);
        } else {
            $questionList = array(["QUESTION_ID" => $questionId]);
        }
        if ($checked == "true") {
            foreach ($questionList as $questionRow) {
                $result = $stageQuestionRepo->fetchByQuestionStageId((int) $questionRow['QUESTION_ID'], $stageId);
                if ($result) {
                    $stageQuestionRepo->updateDetail((int) $questionRow['QUESTION_ID'], $stageId);
                } else {
                    $stageQuestionModel->stageId = $stageId;
                    $stageQuestionModel->questionId = (int) $questionRow['QUESTION_ID'];
                    $stageQuestionModel->status = 'E';
                    $stageQuestionModel->createdDate = Helper::getcurrentExpressionDate();
                    $stageQuestionRepo->add($stageQuestionModel);
                }
            }
            $msg = "Stage successfully assigned";
        } else if ($checked == "false") {
            foreach ($questionList as $questionRow) {
                $stageQuestionRepo->deleteAll((int) $questionRow['QUESTION_ID'], $stageId);
            }
            $msg = "Stage assigned list successfully removed";
        }
        return $responseData = ["success" => true, "data" => ["msg" => $msg]];
    }

}
