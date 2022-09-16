<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\ServiceQuestionForm;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceQuestion;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\ServiceQuestionRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ServiceQuestionController extends AbstractActionController {

    private $form;
    private $repository;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->repository = new ServiceQuestionRepository($adapter);
        $this->adapter = $adapter;
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $serviceQuestionForm = new ServiceQuestionForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($serviceQuestionForm);
        }
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $serviceQuestionList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $serviceQuestionList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {
        ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this);
        $this->initializeForm();
        $request = $this->getRequest();
        $questionTypeList = [
            'TEXT' => 'Text',
            'NUMBER' => 'Number',
            'TEXTAREA' => 'Textarea',
            'DATEPICKER' => 'DatePicker',
            'TIMEPICKER' => 'TimePicker'
        ];
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $serviceQuestion = new ServiceQuestion();
                $serviceQuestion->exchangeArrayFromForm($this->form->getData());
                if ($serviceQuestion->parentQaId == 0) {
                    unset($serviceQuestion->parentQaId);
                }
                $serviceQuestion->createdDate = Helper::getcurrentExpressionDate();
                $serviceQuestion->createdBy = $this->employeeId;
                $serviceQuestion->qaId = ((int) Helper::getMaxId($this->adapter, ServiceQuestion::TABLE_NAME, ServiceQuestion::QA_ID)) + 1;
                $serviceQuestion->status = 'E';
                $serviceQuestion->approvedDate = Helper::getcurrentExpressionDate();
                $serviceQuestion->companyId = $employeeDetail['COMPANY_ID'];
                $serviceQuestion->branchId = $employeeDetail['BRANCH_ID'];
//                print "<pre>";
//                print_r($serviceQuestion); die();
                $this->repository->add($serviceQuestion);
                $this->flashmessenger()->addMessage("Question for Services Successfully added!!!");
                return $this->redirect()->toRoute("serviceQuestion");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'questionTypeList' => $questionTypeList,
                    'serviceEventType' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], ["STATUS" => "E"], "SERVICE_EVENT_TYPE_NAME", "ASC", null, false, true),
                    'serviceQuestion' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, ServiceQuestion::TABLE_NAME, ServiceQuestion::QA_ID, [ServiceQuestion::QUESTION_EDESC], ["STATUS" => "E"], "QA_ID", "ASC", null, false, false),
                        ]
        ));
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->
                            toRoute('serviceQuestion');
        }
        $this->initializeForm();
        $request = $this->getRequest();
        $questionTypeList = [
            'TEXT' => 'Text',
            'NUMBER' => 'Number',
            'TEXTAREA' => 'Textarea',
            'DATEPICKER' => 'DatePicker',
            'TIMEPICKER' => 'TimePicker'
        ];
        $serviceQuestion = new ServiceQuestion();
        $detail = $this->repository->fetchById($id)->getArrayCopy();
        if (!$request->isPost()) {
            $serviceQuestion->exchangeArrayFromDb($detail);
            $this->form->bind($serviceQuestion);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $serviceQuestion->exchangeArrayFromForm($this->form->getData());
                if ($serviceQuestion->parentQaId == 0) {
                    $serviceQuestion->parentQaId = null;
                }
                $serviceQuestion->modifiedDate = Helper::getcurrentExpressionDate();
                $serviceQuestion->modifiedBy = $this->employeeId;

                $this->repository->edit($serviceQuestion, $id);
                $this->flashmessenger()->addMessage("Question for Servicess Successfully Updated!!!");
                return $this->redirect()->toRoute("serviceQuestion");
            }
        }

        return Helper::addFlashMessagesToArray(
                        $this, [
                    'id' => $id,
                    'form' => $this->form,
                    'questionTypeList' => $questionTypeList,
                    'serviceEventType' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], ["STATUS" => "E"], "SERVICE_EVENT_TYPE_NAME", "ASC", null, false, true),
                    'serviceQuestion' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, ServiceQuestion::TABLE_NAME, ServiceQuestion::QA_ID, [ServiceQuestion::QUESTION_EDESC], ["STATUS" => "E"], "QA_ID", "ASC", null, false, false),
                        ]
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('serviceQuestion');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Question for Services Successfully Deleted!!!");
        return $this->redirect()->toRoute('serviceQuestion');
    }

}

?>