<?php

namespace LeaveManagement\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use LeaveManagement\Form\LeaveCarryForwardForm;
use LeaveManagement\Model\LeaveMaster;
use LeaveManagement\Repository\LeaveCarryForwardRepository;
use SelfService\Repository\LeaveRequestRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class LeaveCarryForward extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(LeaveCarryForwardRepository::class);
        $this->initializeForm(LeaveCarryForwardForm::class);
    }

    public function indexAction() {
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $rawList = $this->repository->fetchCarryForward($data);
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $leaveList = EntityHelper::getTableKVListWithSortOption($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID, [LeaveMaster::LEAVE_ENAME], [LeaveMaster::STATUS => 'E'], LeaveMaster::LEAVE_ENAME, "ASC", null, true);
        $leaveSE = $this->getSelectElement(['name' => 'leave', 'id' => 'leaveId', 'class' => 'form-control', 'label' => 'Leave Type'], $leaveList);
       
        return Helper::addFlashMessagesToArray($this, [
                    'leaves' => $leaveSE,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N', 'IS_ADMIN' => "N"], "FULL_NAME", "ASC", "-", FALSE, TRUE),
                    'employeeId' => $this->employeeId,
                     'acl' => $this->acl
        ]);
    }
    
    public function deleteAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
        $this->repository->deleteRecord($id);
        return $this->redirect()->toRoute("leavecarryforward");
    }

    public function addAction() {
        
        $request = $this->getRequest();
        if ($request->isPost()) { 
  
            $postData = $request->getPost(); 
            $this->form->setData($postData);

            //if ($this->form->isValid()) {
                //$leaveRequest = new LeaveCarryForwardR($this->adapter);
                //$leaveRequest->exchangeArrayFromForm($this->form->getData());
                $leaveRequest->carryforward = (empty($_POST['carryforward'])) ? 0 : $_POST['carryforward'];
                $leaveRequest->encashment = (empty($_POST['encashment'])) ? 0 : $_POST['encashment'];
                $leaveRequest->leaveId = (empty($_POST['leaveId'])) ? 0 : $_POST['leaveId'];
                $leaveRequest->employeeId = $_POST['employeeId'];
                $leaveRequest->createdDate = Helper::getcurrentExpressionDate();
                $leaveRequest->status = "E";

                $this->repository->carryForward($leaveRequest);
                $this->flashmessenger()->addMessage("Leave Carry Forward Sucessfully added!!!");

               
                return $this->redirect()->toRoute("leavecarryforward");
            //}
        }

        return Helper::addFlashMessagesToArray($this, [
                    'leaveMaxEncash' => $this->preference['leaveEncashMaxDays'],
                    'form' => $this->form,
                    'employeeId' => $this->employeeId,
                    'leave' => $this->repository->getLeaveList($this->employeeId),
                    'customRenderer' => Helper::renderCustomView(),
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N', 'IS_ADMIN' => "N"], "FULL_NAME", "ASC", "-", FALSE, TRUE)
        ]);
    }

    public function editAction() {
       $id = (int) $this->params()->fromRoute('id', 0);
      
        $request = $this->getRequest();
        if ($request->isPost()) { 
            
            $postData = $request->getPost(); 
           
            $data = $this->repository->editCarryForward($postData);
            $this->flashmessenger()->addMessage("Leave Carry Forward Sucessfully edited!!!");
                return $this->redirect()->toRoute("leavecarryforward");
        }
        $details = Helper::extractDbData($this->repository->getDetailsById($id))[0];
//        print_r($details); die;
        $balance = $this->repository->getBalance($id);
        $balance = Helper::extractDbData($balance);
        $ad = $details['ENCASH_DAYS']+$balance[0]['BALANCE'];
       
       return $this->stickFlashMessagesTo([
          'leaveMaxEncash' => $this->preference['leaveEncashMaxDays'],
             'form' => $this->form,
            'employeeId' => $this->employeeId,
           'availabledays' => $ad,
            'details' => $details,
            'customRenderer' => Helper::renderCustomView(),
               'id' => $id
        ]);
    }

    public function viewAction() {
         
        $data = $this->repository->fetchCarryForward($_POST['employees']);
        $result = Helper::extractDbData($data);
        return new JsonModel(['success' => true, 'data' => $result]);
        
    }

    public function pullLeaveDetailWidEmployeeIdAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();

                
                $employeeId = $postedData['employeeId'];
                $leaveList = $this->repository->getLeaveList($employeeId);

                $leaveRow = [];
                foreach ($leaveList as $key => $value) {
                    array_push($leaveRow, ["id" => $key, "name" => $value]);
                }
                
                return new CustomViewModel(['success' => true, 'data' => $leaveRow, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullLeaveDetailAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $leaveRequestRepository = new LeaveRequestRepository($this->adapter);
                $leaveId = $postedData['leaveId'];
                $employeeId = $postedData['employeeId'];
                $startDate = $postedData['startDate'];
                $leaveDetail = $leaveRequestRepository->getLeaveDetail($employeeId, $leaveId, $startDate);

                return new CustomViewModel(['success' => true, 'data' => $leaveDetail, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
    
    public function fetchAvailableDaysAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
//                $postedData = $request->getPost();
//                $leaveRequestRepository = new LeaveRequestRepository($this->adapter);
//                $availableDays = $leaveRequestRepository->fetchAvailableDays(Helper::getExpressionDate($postedData['startDate'])->getExpression(), Helper::getExpressionDate($postedData['endDate'])->getExpression(), $postedData['employeeId'], $postedData['halfDay'], $postedData['leaveId']);
                return new CustomViewModel(['success' => true, 'data' => [], 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function validateLeaveRequestAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $leaveRequestRepository = new LeaveRequestRepository($this->adapter);
                $error = $leaveRequestRepository->validateLeaveRequest(Helper::getExpressionDate($postedData['startDate'])->getExpression(), Helper::getExpressionDate($postedData['endDate'])->getExpression(), $postedData['employeeId']);
                return new CustomViewModel(['success' => true, 'data' => $error, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

 

 
   

}
