<?php

namespace SelfService\Controller;

use SelfService\Form\LeaveEncashmentForm;
use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use SelfService\Repository\LeaveEncashmentRepository;
use Application\Repository\MonthRepository;
use SelfService\Model\LeaveEncashment as LeaveEncashmentModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
//use Notification\Controller\HeadNotification;

class LeaveEncashment extends HrisController {
    //private $acl;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(LeaveEncashmentRepository::class);
        $this->initializeForm(LeaveEncashmentForm::class);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        //$this->acl = $this->storageData['acl'];
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $rawList = $this->repository->getLeaveEncashment($this->employeeId);
                return new JsonModel(['success' => true, 'data' => $rawList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo([]);
    }

    public function addAction() {
        $monthRepo = new MonthRepository($this->adapter);
        $fiscalYear = $monthRepo->getCurrentFiscalYear();
        $currFiscalYear = $fiscalYear['FISCAL_YEAR_ID'];

        $fiscal_year_month_no = 0;
        $leave_types =  $this->repository->fetchEncashableLeave($this->employeeId, $currFiscalYear);
        $fiscal_year_month_arr = $this->repository->getfiscalYearMontNo($currFiscalYear);
        if(!empty($fiscal_year_month_arr)) {
               //$fiscal_year_month_no = $fiscal_year_month_arr['FISCAL_YEAR_MONTH_NO'] - 1;
               $fiscal_year_month_no = $fiscal_year_month_arr['MONTH_NO'] - 1;
        }


        $ready_to_post = TRUE;
        $total_accum_days = 0;
        $no_of_leaves_to_apply = 0;
        $total_accum_days_initital = 0;
        $max_requested_days = 150;
        $total_accumulated_days =  $this->repository->fetchTotalAccumulatedDays($this->employeeId, $currFiscalYear, $fiscal_year_month_no);
        if(!empty($total_accumulated_days) && $total_accumulated_days['BALANCE'] > 0) {
            $total_accum_days = $total_accumulated_days['BALANCE'];
            $previous_year_balcance = $total_accumulated_days['PREVIOUS_YEAR_BAL'];
            $total_accum_days_initital = $total_accumulated_days['BALANCE'];

            if($total_accum_days > 150) {
                $total_accum_days = 150;
            }

            $no_of_leaves_to_apply = $total_accum_days - 60;
        }

        $lv_ID = 0;
        $if_exist_encash = 0;
        $lvID = $this->repository->getLeaveId($currFiscalYear, 'HOUSLEV');
        if(!empty($lvID)) {
            $lv_ID = $lvID['LEAVE_ID'];
        }

        $if_exist_encash = $this->repository->checkifAlreadyEncashApplied($this->employeeId, $currFiscalYear, $lv_ID);

        if($if_exist_encash == 1) {
            $this->flashmessenger()->addMessage("You have already applied for the leave encashment in this year!");
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();

            $this->form->setData($postData);
            //check if it exceeds max number of days
            if ($postData['requestedDays'] > $max_requested_days) {
                //DISPLAY MESSAGE HERE
                $this->flashmessenger()->addMessage("You cannot request more than 150 days for encashment!");
                $ready_to_post = FALSE;
                // return $this->redirect()->toRoute("LeaveEncashment");
                
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

            if ($this->form->isValid()) {
                if(!empty($postData) && $ready_to_post == TRUE && $if_exist_encash == 0) {
                    $LeaveEncashment_Model = new LeaveEncashmentModel();
                    $LeaveEncashment_Model->leId = null;
                    $LeaveEncashment_Model->leaveId = $postData['leaveId'];
                    $LeaveEncashment_Model->employeeId = $this->employeeId;
                    $LeaveEncashment_Model->totalAccumulatedDays = $total_accum_days;
                    $LeaveEncashment_Model->requestedDaysToEncash = $postData['requestedDays'];
                    $LeaveEncashment_Model->fiscalYearId =  $currFiscalYear;
                    $LeaveEncashment_Model->remainingBalance = $total_accum_days - $postData['requestedDays'];
                    $LeaveEncashment_Model->requestedDate = date('Y-m-d');
                    $LeaveEncashment_Model->modifiedDate = null;
                    $LeaveEncashment_Model->remarks = $postData['reason'];
                    $LeaveEncashment_Model->monthNo = $fiscal_year_month_no;

                    $is_posted_encashment = $this->repository->addLeaveEncashmentDetails($LeaveEncashment_Model);

                    if($is_posted_encashment == 1) {
                        //UPDATE PREVIOUS YEAR BALANCE
                        $leave_bal = $previous_year_balcance - $postData['requestedDays'];
                        $is_previous_bal_updated = $this->repository->updatePreviousYearBalance($this->employeeId, $lv_ID, $fiscal_year_month_no, $leave_bal);
                        if($is_previous_bal_updated == 1) {
                            $this->flashmessenger()->addMessage("Leave Encashment Successfully added!!!");
                        }
                    }

                }

                return $this->redirect()->toRoute("LeaveEncashment");
                
            }
        }

        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'leave_types' => $leave_types,
            'total_accum_days' => $total_accum_days_initital,
            'no_of_leaves_to_apply' => $no_of_leaves_to_apply
        ]);
    }


}

?>