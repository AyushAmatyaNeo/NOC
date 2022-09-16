<?php

namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Appraisal\Form\QuestionForm;
use Appraisal\Model\Question;
use Appraisal\Repository\QuestionRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Appraisal\Model\Heading;
use Setup\Repository\EmployeeRepository;
use Application\Custom\CustomViewModel;
use Appraisal\Model\QuestionOption;
use Appraisal\Repository\QuestionOptionRepository;

class QuestionController extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $employeeId;
    private $userId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new QuestionRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()["employee_id"];
        $this->userId = $auth->getStorage()->read()["user_id"];
    }

    public function initializeForm() {
        $form = new QuestionForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'questions' => $list
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        $answerTypeList = [
            'TEXT' => 'Text',
            'NUMBER' => 'Number',
            'SELECT' => 'Select',
            'TEXTAREA' => 'Textarea',
            'RADIO' => 'Radio',
            'CHECKBOX' => 'Checkbox',
            'PERCENTAGE' => 'Percentage'
        ];
        if ($request->isPost()) {
            $question = new Question();
            $employeeRepo = new EmployeeRepository($this->adapter);
            $employeeDetail = $employeeRepo->fetchById($this->employeeId);
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $question->exchangeArrayFromForm($this->form->getData());
                $question->questionId = ((int) Helper::getMaxId($this->adapter, Question::TABLE_NAME, Question::QUESTION_ID)) + 1;
                $question->createdBy = $this->employeeId;
                $question->createdDate = Helper::getcurrentExpressionDate();
                $question->approvedDate = Helper::getcurrentExpressionDate();
                $question->companyId = $employeeDetail['COMPANY_ID'];
                $question->branchId = $employeeDetail['BRANCH_ID'];
                $question->status = 'E';
                $this->repository->add($question);
                $this->flashmessenger()->addMessage("Appraisal Question Successfully added!!!");
                return $this->redirect()->toRoute("question");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'headings' => EntityHelper::getTableKVListWithSortOption($this->adapter, Heading::TABLE_NAME, Heading::HEADING_ID, [Heading::HEADING_EDESC], [Heading::STATUS => 'E'], Heading::HEADING_EDESC, "ASC", NULL, FALSE, TRUE),
                    'answerTypeList' => $answerTypeList,
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

    public function editAction() {
        $this->initializeForm();
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('question');
        }
        $request = $this->getRequest();
        $question = new Question();
        $answerTypeList = [
            'TEXT' => 'Text',
            'NUMBER' => 'Number',
            'SELECT' => 'Select',
            'TEXTAREA' => 'Textarea',
            'RADIO' => 'Radio',
            'CHECKBOX' => 'Checkbox',
            'PERCENTAGE' => 'Percentage'
        ];
        if (!$request->isPost()) {
            $question->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($question);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $question->exchangeArrayFromForm($this->form->getData());
                $question->modifiedDate = Helper::getcurrentExpressionDate();
                $question->modifiedBy = $this->employeeId;
                $this->repository->edit($question, $id);
                $this->flashmessenger()->addMessage("Appraisal Question Successfully Updated!!!");
                return $this->redirect()->toRoute("question");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'headings' => EntityHelper::getTableKVListWithSortOption($this->adapter, Heading::TABLE_NAME, Heading::HEADING_ID, [Heading::HEADING_EDESC], [Heading::STATUS => 'E'], Heading::HEADING_EDESC, "ASC", NULL, FALSE, TRUE),
                    'answerTypeList' => $answerTypeList,
                    'customRenderer' => Helper::renderCustomView(),
                    'id' => $id
        ]);
    }

    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        $questionOptionRepo = new QuestionOptionRepository($this->adapter);
        if ($id === 0) {
            $this->redirect()->toRoute('question');
        }
        $this->repository->delete($id);
        $questionOptionRepo->deleteByQuestionId($id);
        $this->flashmessenger()->addMessage("Appraisal Question Successfully Deleted!!!");
        return $this->redirect()->toRoute("question");
    }

    public function pullQuestionDetailAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $questionOptionRepo = new QuestionOptionRepository($this->adapter);

            $postRecord = $request->getPost()->getArrayCopy();
            $questionId = $postRecord['data']['questionId'];
            $questionDetail = $this->repository->fetchById($questionId)->getArrayCopy();
            $questionOptionResult = $questionOptionRepo->fetchByQuestionId($questionId);
            $questionOptionList = [];
            foreach ($questionOptionResult as $questionOptionRow) {
                array_push($questionOptionList, $questionOptionRow);
            }
            $response = [
                "success" => true,
                "data" => [
                    "questionDetail" => $questionDetail,
                    "questionOptionList" => $questionOptionList,
                    "num" => count($questionOptionList)
            ]];
            return new CustomViewModel($response);
        }
    }

    public function submitAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $question = new Question();
            $employeeRepo = new EmployeeRepository($this->adapter);
            $questionOptionRepo = new QuestionOptionRepository($this->adapter);
            $employeeDetail = $employeeRepo->fetchById($this->employeeId);
            $postRecord = $request->getPost()->getArrayCopy();
            $questionDetail = $postRecord['data']['questionDetail'];
            $optionListEmpty = $postRecord['data']['optionListEmpty'];
            $questionId = (int) $postRecord['data']['questionId'];

            $question->status = 'E';
            $question->questionCode = $questionDetail['questionCode'];
            $question->questionEdesc = $questionDetail['questionEdesc'];
            $question->questionNdesc = $questionDetail['questionNdesc'];
            $question->answerType = $questionDetail['answerType'];
            $question->headingId = (int) $questionDetail['headingId'];
            $question->appraiseeFlag = $questionDetail['appraiseeFlag'];
            $question->appraiserFlag = $questionDetail['appraiserFlag'];
            $question->reviewerFlag = $questionDetail['reviewerFlag'];
            $question->appraiseeRating = $questionDetail['appraiseeRating'];
            $question->appraiserRating = $questionDetail['appraiserRating'];
            $question->reviewerRating = $questionDetail['reviewerRating'];
            $question->orderNo = ($questionDetail['orderNo'] != null) ? (int) $questionDetail['orderNo'] : null;
            $question->remarks = $questionDetail['remarks'];
            $question->minValue = ($questionDetail['minValue'] != null) ? (int) $questionDetail['minValue'] : null;
            $question->maxValue = ($questionDetail['maxValue'] != null) ? (int) $questionDetail['maxValue'] : null;

            if ($questionId == 0) {
                $question->questionId = ((int) Helper::getMaxId($this->adapter, Question::TABLE_NAME, Question::QUESTION_ID)) + 1;
                $question->createdBy = $this->employeeId;
                $question->createdDate = Helper::getcurrentExpressionDate();
                $question->approvedDate = Helper::getcurrentExpressionDate();
                $question->companyId = $employeeDetail['COMPANY_ID'];
                $question->branchId = $employeeDetail['BRANCH_ID'];
                $this->repository->add($question);
                $msg = "Appraisal Quesstion Successfully Added!!!";
            } else {
                $question->modifiedBy = $this->employeeId;
                $question->modifiedDate = Helper::getcurrentExpressionDate();
                $this->repository->edit($question, $questionId);
                $msg = "Appraisal Question Successfully Updated!!";
            }

            if ($optionListEmpty == 1) {
                $questionOptionList = $postRecord['data']['questionOptionList'];
                foreach ($questionOptionList as $questionOption) {
                    $questionOptionModel = new QuestionOption();
                    $questionIdNew = $question->questionId;
                    $questionOptionModel->questionId = ($questionId != 0) ? $questionId : $questionIdNew;
                    $questionOptionModel->status = 'E';
                    $questionOptionModel->optionEdesc = $questionOption['optionEdesc'];
                    $questionOptionModel->optionNdesc = $questionOption['optionNdesc'];

                    $questionOptionId = $questionOption['optionId'];
                    if ($questionOptionId == 0) {
                        $questionOptionModel->optionId = ((int) Helper::getMaxId($this->adapter, $questionOptionModel::TABLE_NAME, $questionOptionModel::OPTION_ID)) + 1;
                        $questionOptionModel->createdBy = $this->employeeId;
                        $questionOptionModel->optionCode = $questionOptionModel->optionId;
                        $questionOptionModel->createdDate = Helper::getcurrentExpressionDate();
                        $questionOptionModel->approvedDate = Helper::getcurrentExpressionDate();
                        $questionOptionModel->companyId = $employeeDetail['COMPANY_ID'];
                        $questionOptionModel->branchId = $employeeDetail['BRANCH_ID'];
                        $questionOptionRepo->add($questionOptionModel);
                    } else {
                        $questionOptionModel->modifiedBy = $this->employeeId;
                        $questionOptionModel->modifiedDate = Helper::getcurrentExpressionDate();
                        $questionOptionRepo->edit($questionOptionModel, $questionOptionId);
                    }
                }
            }
            $response = ["success" => true, "data" => $msg];
        } else {
            $response = ["success" => false];
        }
        return new CustomViewModel($response);
    }

    public function deleteQuestionOptionAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postRecord = $request->getPost()->getArrayCopy();
            $optionId = $postRecord['data']['optionId'];

            $questionOptionRepo = new QuestionOptionRepository($this->adapter);
            $result = $questionOptionRepo->delete($optionId);
            $response = ["success" => true, "data" => ["msg" => "Question Option Successfully Deleted!!!"]];
        }
        return new CustomViewModel($response);
    }

}
