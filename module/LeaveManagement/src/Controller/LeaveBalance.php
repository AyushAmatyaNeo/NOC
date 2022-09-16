<?php

namespace LeaveManagement\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use LeaveManagement\Form\LeaveApplyForm;
use LeaveManagement\Repository\LeaveBalanceRepository;
use LeaveManagement\Repository\LeaveStatusRepository;
use SelfService\Repository\LeaveRequestRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class LeaveBalance extends HrisController {

    private $leaveRequestRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(LeaveBalanceRepository::class);
        $this->initializeForm(LeaveApplyForm::class);
        $this->leaveRequestRepository = new LeaveRequestRepository($adapter);
    }

    public function indexAction() {
        $leaveList = $this->repository->getAllLeave();
        $leaves = Helper::extractDbData($leaveList);
        
        $leaveYearList=EntityHelper::getTableKVList($this->adapter, "HRIS_LEAVE_YEARS", "LEAVE_YEAR_ID", ["LEAVE_YEAR_NAME"], null,null,false,'LEAVE_YEAR_ID','desc');
        $leaveYearSE = $this->getSelectElement(['name' => 'leaveYear', 'id' => 'leaveYear', 'class' => 'form-control ', 'label' => 'Type'], $leaveYearList);
        
        $leaveStatusReposotory = new LeaveStatusRepository($this->adapter);
                
        $allLeaveForReport= $leaveStatusReposotory->getMonthlyLeaveforReport();
        
        return $this->stickFlashMessagesTo([
                    'leavesArrray' => $leaves,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'preference' => $this->preference,
                    'leaveYearSelect'  =>$leaveYearSE,
                    'allLeaveForReport'  =>$allLeaveForReport,
        ]);
    }

    public function monthlyAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $rawList = $this->repository->getPivotedList($data, true);
                $list = Helper::extractDbData($rawList);
                return new JsonModel([
                    "success" => true,
                    "data" => $list,
                    "message" => null,
                ]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }

        $leaveList = $this->repository->getAllLeave(true);
        $leaves = iterator_to_array($leaveList, false);
        
        $leaveStatusReposotory = new LeaveStatusRepository($this->adapter);
        $allLeaveForReport= $leaveStatusReposotory->getMonthlyLeaveforReport(true);
        
        return $this->stickFlashMessagesTo([
                    'leavesArrray' => $leaves,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'preference' => $this->preference,
                    'allLeaveForReport'  =>$allLeaveForReport,
        ]);
    }

    public function pullLeaveBalanceDetailAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $leaveList = $this->repository->getAllLeave(false, $data['leaveId'],$data['leaveYear']);
            $leaves = Helper::extractDbData($leaveList);
            
            $rawList = $this->repository->getPivotedList($data);
            $list = Helper::extractDbData($rawList);
            return new JsonModel([
                "success" => true,
                "data" => $list,
                "leaves" => $leaves,
                "message" => null,
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function betweenDatesAction() {
        $leaveList = $this->repository->getAllLeave();
        $leaves = Helper::extractDbData($leaveList);
        
        $leaveYearList=EntityHelper::getTableKVList($this->adapter, "HRIS_LEAVE_YEARS", "LEAVE_YEAR_ID", ["LEAVE_YEAR_NAME"], null,null,false,'LEAVE_YEAR_ID','desc');
        $leaveYearSE = $this->getSelectElement(['name' => 'leaveYear', 'id' => 'leaveYear', 'class' => 'form-control ', 'label' => 'Type'], $leaveYearList);
        $leaveStatusReposotory = new LeaveStatusRepository($this->adapter);
        $allLeaveForReport= $leaveStatusReposotory->getMonthlyLeaveforReport();
        
        return $this->stickFlashMessagesTo([
                    'leavesArrray' => $leaves,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'preference' => $this->preference,
                    'leaveYearSelect'  =>$leaveYearSE, 
                    'allLeaveForReport'  =>$allLeaveForReport,
        ]);
    }

    public function pullBalanceBetweenDatesAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $rawList = $this->repository->getPivotedListBetnDates($data);
            $list = Helper::extractDbData($rawList);
            return new JsonModel([
                "success" => true,
                "data" => $list,
                "message" => null,
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function leaveAdditionReportAction() {
        $request = $this->getRequest();
        
        if($request->isPost()) {
            
            try {
                $data = $request->getPost();
                $reportData = $this->repository->fetchLeaveAddition($data);
               
                return new JsonModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }

        $leaveList = $this->repository->getLeaveTypes();
        $leaves = Helper::extractDbData($leaveList);
        
        return $this->stickFlashMessagesTo([
                    'leavesArray' => $leaves,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'preference' => $this->preference,
        ]);
    }
    
    public function getLeaveYearMonthAction(){
        
        try {
            $data['years'] = EntityHelper::getTableList($this->adapter, "HRIS_LEAVE_YEARS", ["LEAVE_YEAR_ID", "LEAVE_YEAR_NAME"]);
            $data['months'] = iterator_to_array($this->repository->fetchLeaveYearMonth(), false);
            $data['currentMonth'] = $this->repository->getCurrentLeaveMonth();
            return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
