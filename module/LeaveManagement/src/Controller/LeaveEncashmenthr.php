<?php

namespace LeaveManagement\Controller;

use LeaveManagement\Form\LeaveEncashmenthrForm;
use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use LeaveManagement\Repository\LeaveEncashmenthrRepository;
use Application\Repository\MonthRepository;
use LeaveManagement\Model\LeaveEncashmenthr as LeaveEncashmentModelhr;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
//use Notification\Controller\HeadNotification;

class LeaveEncashmenthr extends HrisController {
    //private $acl;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(LeaveEncashmenthrRepository::class);
        $this->initializeForm(LeaveEncashmenthrForm::class);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        //$this->acl = $this->storageData['acl'];
    }

    public function indexAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $rawList = $this->repository->getLeaveEncashment($data['employeeId'],$data['leaveId']);
                return new JsonModel(['success' => true, 'data' => $rawList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $FiscalYearList=EntityHelper::getTableKVList($this->adapter, "HRIS_FISCAL_YEARS", "FISCAL_YEAR_ID", ["FISCAL_YEAR_NAME"], null,null,false,'FISCAL_YEAR_ID','desc');

        return Helper::addFlashMessagesToArray($this, [
            // 'leaves' => $leaveSE,
            'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N', 'IS_ADMIN' => "N"], "FULL_NAME", "ASC", "-", FALSE, TRUE),
            'leave_year'=> $FiscalYearList
            // 'employeeId' => $this->employeeId,
            //  'acl' => $this->acl
]);
    }

    public function addAction() {
        $monthRepo = new MonthRepository($this->adapter);
        $fiscalYear = $monthRepo->getCurrentFiscalYear();
        $currFiscalYear = $fiscalYear['FISCAL_YEAR_ID'];

        $fiscal_year_month_no = 0;
        $leave_types =  $this->repository->fetchEncashableLeave($this->employeeId, $currFiscalYear);
        $fiscal_year_month_arr = $this->repository->getfiscalYearMontNo($currFiscalYear);
        if(!empty($fiscal_year_month_arr)) {
            $fiscal_year_month_no = $fiscal_year_month_arr['FISCAL_YEAR_MONTH_NO'];
        }


        $ready_to_post = TRUE;
        $total_accum_days = 0;
        $no_of_leaves_to_apply = 0;
        $total_accum_days_initital = 0;
        $max_requested_days = 150;
        /*$total_accumulated_days =  $this->repository->fetchTotalAccumulatedDays($this->employeeId, $currFiscalYear, $fiscal_year_month_no);
        if(!empty($total_accumulated_days) && $total_accumulated_days['BALANCE'] > 0) {
            $total_accum_days = $total_accumulated_days['BALANCE'];
            $previous_year_balcance = $total_accumulated_days['PREVIOUS_YEAR_BAL'];
            $total_accum_days_initital = $total_accumulated_days['BALANCE'];

            if($total_accum_days > 150) {
                $total_accum_days = 150;
            }

            //echo 'total='.$total_accum_days;die;

            $no_of_leaves_to_apply = $total_accum_days - 60;
        }*/

        $lv_ID = 0;
        //$if_exist_encash = 0;
        $lvID = $this->repository->getLeaveId($currFiscalYear, 'HOUSLEV');
        if(!empty($lvID)) {
            $lv_ID = $lvID['LEAVE_ID'];
        }

        /*$if_exist_encash = $this->repository->checkifAlreadyEncashApplied($this->employeeId, $currFiscalYear, $lv_ID);

        if($if_exist_encash == 1) {
            $this->flashmessenger()->addMessage("You have already applied for the leave encashment in this year!");
        }*/

        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();
            $employee_id = 0;

            //echo '<pre>';print_r($postData);die;
            $employee_id = $postData->employeeId;

            $this->form->setData($postData);

            $total_accumulated_days =  $this->repository->fetchTotalAccumulatedDays($employee_id, $currFiscalYear, $fiscal_year_month_no);
            if(!empty($total_accumulated_days) && $total_accumulated_days['BALANCE'] > 0) {
                $total_accum_days = $total_accumulated_days['BALANCE'];
                $previous_year_balcance = $total_accumulated_days['PREVIOUS_YEAR_BAL'];
                $total_accum_days_initital = $total_accumulated_days['BALANCE'];

                if($total_accum_days > 150) {
                    $total_accum_days = 150;
                }

                //echo 'total='.$total_accum_days;die;

                $no_of_leaves_to_apply = $total_accum_days - 60;
            }

            $if_exist_encash = 0;
            $if_exist_encash = $this->repository->checkifAlreadyEncashApplied($employee_id, $currFiscalYear, $lv_ID);

            if($if_exist_encash == 1) {
                $this->flashmessenger()->addMessage("You have already applied for the leave encashment in this year!");
            }

            //echo 'if encash='.$if_exist_encash;die;

            //check if it exceeds max number of days
            if ($postData['requestedDays'] > $max_requested_days) {
                //DISPLAY MESSAGE HERE
                $this->flashmessenger()->addMessage("You cannot request more than 150 days for encashment!");
                $ready_to_post = FALSE;
            } 

            //check whether that is allowable to request or not 
            if($total_accum_days > 0 && $total_accum_days < 60) {
                 //DISPLAY MESSAGE HERE
                $this->flashmessenger()->addMessage("You are not eligible to request leave encashment!");
                $ready_to_post = FALSE;
            }  else {
                $allowable_days = $total_accum_days - 60;
                if($postData['requestedDays'] > $allowable_days ) {
                     //DISPLAY MESSAGE HERE
                    $this->flashmessenger()->addMessage("You do not have sufficient balance to request!");
                    $ready_to_post = FALSE;
                }
            } 
            
            //echo 'I am here';die;

            //if ($this->form->isValid()) {
                if(!empty($postData) && $ready_to_post == TRUE && $if_exist_encash == 0) {
                    $LeaveEncashment_Model = new LeaveEncashmentModelhr();
                    $LeaveEncashment_Model->leId = null;
                    $LeaveEncashment_Model->leaveId = $postData['leaveId'];
                    $LeaveEncashment_Model->employeeId = $employee_id;
                    $LeaveEncashment_Model->totalAccumulatedDays = $total_accum_days;
                    $LeaveEncashment_Model->requestedDaysToEncash = $postData['requestedDays'];
                    $LeaveEncashment_Model->fiscalYearId =  $currFiscalYear;
                    $LeaveEncashment_Model->remainingBalance = $total_accum_days - $postData['requestedDays'];
                    $LeaveEncashment_Model->requestedDate = date('Y-m-d');
                    $LeaveEncashment_Model->modifiedDate = null;
                    $LeaveEncashment_Model->remarks = $postData['reason'];

                    $is_posted_encashment = $this->repository->addLeaveEncashmentDetails($LeaveEncashment_Model);

                    if($is_posted_encashment == 1) {
                        //UPDATE PREVIOUS YEAR BALANCE
                        $leave_bal = $previous_year_balcance - $postData['requestedDays'];
                        $is_previous_bal_updated = $this->repository->updatePreviousYearBalance($employee_id, $lv_ID, $fiscal_year_month_no, $leave_bal);
                        if($is_previous_bal_updated == 1) {
                            $this->flashmessenger()->addMessage("Leave Encashment Successfully added!!!");
                        }
                    }

                }

                return $this->redirect()->toRoute("LeaveEncashmenthr");
                
            //}
        }

        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'leave_types' => $leave_types,
            'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N', 'IS_ADMIN' => "N"], "FULL_NAME", "ASC", "-", FALSE, TRUE, $this->employeeId),
        ]);
    }


}

?>