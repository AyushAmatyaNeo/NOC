<?php

namespace ServiceQuestion\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use ServiceQuestion\Model\EmpServiceQuestion as EmpServiceQuestionModel;
use ServiceQuestion\Model\EmpServiceQuestionDtl;
use ServiceQuestion\Repository\EmpServiceQuestionDtlRepo;
use ServiceQuestion\Repository\EmpServiceQuestionRepo;
use Setup\Model\HrEmployees;
use Setup\Model\ServiceEventType;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\ServiceEventTypeRepository;
use Setup\Repository\ServiceQuestionRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class EmpServiceQuestion extends AbstractActionController {

    private $adapter;
    private $repository;
    private $employeeId;
    private $dtlRepository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new EmpServiceQuestionRepo($adapter);
        $this->dtlRepository = new EmpServiceQuestionDtlRepo($adapter);
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }

    public function addAction() {
        $request = $this->getRequest();
        $empServiceQuestion = new EmpServiceQuestionModel();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        $empServiceQuestionDtl = new EmpServiceQuestionDtl();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $serviceQuestionAnswer = $postData['serviceQuestionAnswer'];
            $empServiceQuestion->empQaId = (int) Helper::getMaxId($this->adapter, $empServiceQuestion::TABLE_NAME, $empServiceQuestion::EMP_QA_ID) + 1;
            $empServiceQuestion->employeeId = $postData['employeeId'];
            $empServiceQuestion->qaDate = $postData['questionDate'];
            $empServiceQuestion->remarks = $postData['remarks'];
            $empServiceQuestion->serviceEventTypeId = $postData['serviceEventTypeId'];
            $empServiceQuestion->createdBy = $this->employeeId;
            $empServiceQuestion->createdDate = Helper::getcurrentExpressionDate();
            $empServiceQuestion->status = 'E';
            $empServiceQuestion->approvedDate = Helper::getcurrentExpressionDate();
            $empServiceQuestion->qaDate = Helper::getExpressionDate($empServiceQuestion->qaDate);
            $empServiceQuestion->companyId = $employeeDetail['COMPANY_ID'];
            $empServiceQuestion->branchId = $employeeDetail['BRANCH_ID'];
            $this->repository->add($empServiceQuestion);
            foreach ($serviceQuestionAnswer as $qaId => $answer) {
                $empServiceQuestionDtl->qaId = $qaId;
                $empServiceQuestionDtl->answer = $answer;
                $empServiceQuestionDtl->status = 'E';
                $empServiceQuestionDtl->empQaId = $empServiceQuestion->empQaId;
                $this->dtlRepository->add($empServiceQuestionDtl);
            }
            $this->flashmessenger()->addMessage("Answer for Service Question Successfully added!!!");
            $this->redirect()->toRoute("empServiceQuestion");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], HrEmployees::FIRST_NAME, "ASC", " ", FALSE, TRUE),
                    'serviceEventTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E'], ServiceEventType::SERVICE_EVENT_TYPE_NAME, "ASC", null, FALSE, TRUE)
        ]);
    }

    public function editAction() {
        $id = $this->params()->fromRoute('id');
        if ($id === 0) {
            $this->redirect()->toRoute('empServiceQuestion');
        }
        $request = $this->getRequest();
        $empServiceQuestion = new EmpServiceQuestionModel();
        $empServiceQuestionDtl = new EmpServiceQuestionDtl();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();

            $serviceQuestionAnswer = $postData['serviceQuestionAnswer'];
            $empServiceQuestion->qaDate = $postData['questionDate'];
            $empServiceQuestion->remarks = $postData['remarks'];
            $empServiceQuestion->modifiedBy = $this->employeeId;
            $empServiceQuestion->modifiedDate = Helper::getcurrentExpressionDate();
            $this->repository->edit($empServiceQuestion, $id);

            foreach ($serviceQuestionAnswer as $qaId => $answer) {
                $empServiceQuestionDtl->answer = $answer;
                $empServiceQuestionDtl->status = 'E';
                $ids = ['qaId' => $qaId, 'empQaId' => $id];
                $this->dtlRepository->edit($empServiceQuestionDtl, $ids);
            }
            $this->flashmessenger()->addMessage("Answer for Service Question Successfully updated!!!");
            $this->redirect()->toRoute("empServiceQuestion");
        }
        $detail = $this->repository->fetchById($id);
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'detail' => $detail,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], HrEmployees::FIRST_NAME, "ASC", " ", FALSE, TRUE),
                    'serviceEventTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E'], ServiceEventType::SERVICE_EVENT_TYPE_NAME, "ASC", null, FALSE, TRUE)
        ]);
    }

    public function viewAction() {
        $id = $this->params()->fromRoute('id');
        $employeeRepository = new EmployeeRepository($this->adapter);
        $detail = $this->repository->fetchById($id);
        $empDetail = $employeeRepository->fetchForProfileById($detail['EMPLOYEE_ID']);
        $serviceEventTypeRepo = new ServiceEventTypeRepository($this->adapter);
        if ($detail['SERVICE_EVENT_TYPE_ID'] == 5) {
            $detail['SERVICE_EVENT_TYPE_NAME'] = 'EXIT INTERVIEW';
        }
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'detail' => $detail,
                    'empDetail' => $empDetail
        ]);
    }

    public function pullServiceQuestionListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $serviceEventTypeId = $data['id'];
            $empQaId = (gettype($data['empQaId']) == 'undefined' || $data['empQaId'] == null || $data['empQaId'] == "") ? 0 : $data['empQaId'];
            $serviceQuestionRepo = new ServiceQuestionRepository($this->adapter);
            $empServiceQuestionDtlRepo = new EmpServiceQuestionDtlRepo($this->adapter);
            $result = $serviceQuestionRepo->fetchByServiceEventTypeId($serviceEventTypeId);
            $questionDtlArray = [];
            $i = 1;
            foreach ($result as $row) {
                $tempResult = $this->pullHierarchicalQuestion($serviceEventTypeId, $empQaId, $row['QA_ID']);
                $questionAnswerDtl = $empServiceQuestionDtlRepo->fetchByEmpQaIdQaId($row['QA_ID'], $empQaId);
                if ($tempResult) {
                    $questionDtlArray[] = array(
                        "sn" => $i,
                        "qaId" => $row['QA_ID'],
                        "questionEdesc" => $row['QUESTION_EDESC'],
                        "subQuestion" => true,
                        "subQuestionList" => $tempResult['array'],
                        "answer" => (!isset($questionAnswerDtl) || $questionAnswerDtl == null || gettype($questionAnswerDtl) == 'undefined') ? null : $questionAnswerDtl->ANSWER
                    );
                } else {
                    $questionDtlArray[] = array(
                        "sn" => $i,
                        "qaId" => $row['QA_ID'],
                        "questionEdesc" => $row['QUESTION_EDESC'],
                        "subQuestion" => false,
                        "answer" => (!isset($questionAnswerDtl) || $questionAnswerDtl == null || gettype($questionAnswerDtl) == 'undefined') ? null : $questionAnswerDtl->ANSWER
                    );
                }
                $i++;
            }

            return new JsonModel(['success' => true, 'data' => $questionDtlArray, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
