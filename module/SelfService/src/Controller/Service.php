<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 11:12 AM
 */

namespace SelfService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper as EntityHelper1;
use Application\Helper\Helper;
use Exception;
use SelfService\Repository\ServiceRepository;
use Setup\Form\JobHistoryForm;
use Setup\Model\JobHistory;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class Service extends AbstractActionController {

    private $adapter;
    private $employeeId;
    private $authService;
    private $form;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->employeeId = $recordDetail['employee_id'];
        $this->repository = new ServiceRepository($adapter);
    }

    public function initializeForm() {
        $jobHistoryForm = new JobHistoryForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($jobHistoryForm);
    }

    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['employeeId' => $this->employeeId]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('jobHistory');
        }
        $this->initializeForm();
        $request = $this->getRequest();

        $jobHistory = new JobHistory();
        if (!$request->isPost()) {
//            $jobHistory->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $jobHistory->exchangeArrayFromDb($this->repository->fetchById($id));
            $this->form->bind($jobHistory);
        } else {
            
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'messages' => $this->flashmessenger()->getMessages(),
                    'employees' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], null, null, null, false, true),
                    'departments' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], null, null, null, false, true),
                    'designations' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E'], null, null, null, false, true),
                    'branches' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E'], null, null, null, false, true),
                    'positions' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E'], null, null, null, false, true),
                    'serviceTypes' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_SERVICE_TYPES", "SERVICE_TYPE_ID", ["SERVICE_TYPE_NAME"], ["STATUS" => 'E'], null, null, null, false, true),
                    'serviceEventTypes' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_SERVICE_EVENT_TYPES", "SERVICE_EVENT_TYPE_ID", ["SERVICE_EVENT_TYPE_NAME"], ["STATUS" => 'E'], null, null, null, false, true)
                        ]
        );
    }

    public function fetchAllemployeeServiceAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $postedData = $request->getPost();
                $data = $postedData->data;

                $employeeId = $data['employeeId'];
                $fromDate = $data['fromDate'];
                $toDate = $data['toDate'];

                $serviceRepository = new ServiceRepository($this->adapter);
                $history = $serviceRepository->getAllHistoryWidEmpId($employeeId, $fromDate, $toDate);

                $serviceList = Helper::extractDbData($history);
                return new CustomViewModel(['success' => true, 'data' => $serviceList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

}
