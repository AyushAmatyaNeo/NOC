<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\OvertimeRequestForm;
use SelfService\Model\Overtime;
use SelfService\Model\OvertimeDetail;
use SelfService\Model\OvertimeClaim as OvertimeClaimModel;
use SelfService\Model\OvertimeClaimDetail; 
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Repository\OvertimeClaimRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Application\Custom\CustomViewModel;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class OvertimeClaim extends HrisController {

    private $detailRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeForm(OvertimeRequestForm::class);
        $this->repository = new OvertimeClaimRepository($adapter);
        $this->detailRepository = new OvertimeDetailRepository($adapter);
    }

    public function overtimeDetail($overtimeId) {
        $rawList = $this->detailRepository->fetchByOvertimeId($overtimeId);
        return Helper::extractDbData($rawList);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $rawList = $this->repository->getAllOTDataByEmployeeId($this->employeeId);
                return new JsonModel(['success' => true, 'data' => $rawList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, [
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = (array) $request->getPost();
            // print_r($data['dataForOvertime'][0]);die;
            $otherDetails = $data['details'][0];
            $dataForLeave = $data['dataForLeave'];
            $dataForOvertime = $data['dataForOvertime'];
            $overtimeClaimModel = new OvertimeClaimModel();
            $overtimeClaimModel->overtimeClaimId = ((int) Helper::getMaxId($this->adapter, OvertimeClaimModel::TABLE_NAME, OvertimeClaimModel::OVERTIME_CLAIM_ID)) + 1;
            $overtimeClaimModel->employeeId = $this->employeeId;
            $overtimeClaimModel->monthId = $data['monthId'];
            
            $holidayDetails = $this->repository->getHolidayDetails($data['monthId']);
            $holidayDetail = [];
            foreach ($holidayDetails as $hd){
                $holidayDetail[$hd['HOLIDAY_CODE']] = $hd['START_DATE'];
            }

            $overtimeClaimModel->reqOtHours = $otherDetails['totalOtHour'];
            $overtimeClaimModel->appOtHours = $otherDetails['totalOtHour'];
            $overtimeClaimModel->reqSubstituteLeaveNo = $otherDetails['totalLeave'];
            $overtimeClaimModel->appSubstituteLeaveNo = $otherDetails['totalLeave'];

            $overtimeClaimModel->reqDashainTiharLeave = $otherDetails['dashainTiharLeave'];
            $overtimeClaimModel->reqGrandTotalLeavel = $otherDetails['grandTotalLeave'];
            $overtimeClaimModel->reqLunchAllowance = $otherDetails['lunchExpense'];
            $overtimeClaimModel->reqNightAllowance = $otherDetails['nightAllowance'];
            $overtimeClaimModel->reqLockingAllowance = $otherDetails['lockingAllowance'];
            $overtimeClaimModel->reqOtDays = $otherDetails['totalOtDays'];
            $overtimeClaimModel->appDashainTiharLeave = $otherDetails['dashainTiharLeave'];
            $overtimeClaimModel->appGrandTotalLeavel = $otherDetails['grandTotalLeave'];
            $overtimeClaimModel->appLunchAllowance = $otherDetails['lunchExpense'];
            $overtimeClaimModel->appNightAllowance = $otherDetails['nightAllowance'];
            $overtimeClaimModel->appLockingAllowance = $otherDetails['lockingAllowance'];
            $overtimeClaimModel->appOtDays = $otherDetails['totalOtDays'];
            $overtimeClaimModel->reqFestiveOtDays = $otherDetails['festiveOtDays'];
            $overtimeClaimModel->grandTotalReqOtDays = $otherDetails['grandTotalOtDays'];
            $overtimeClaimModel->appFestiveOtDays = $otherDetails['festiveOtDays'];
            $overtimeClaimModel->grandTotalAppOtDays = $otherDetails['grandTotalOtDays'];

            foreach($dataForOvertime as $overtimeData){
                $detailModel = new OvertimeClaimDetail();
                $detailModel->overtimeClaimDetailId = ((int) Helper::getMaxId($this->adapter, OvertimeClaimDetail::TABLE_NAME, OvertimeClaimDetail::OVERTIME_CLAIM_DETAIL_ID)) + 1;
                $detailModel->overtimeClaimId = $overtimeClaimModel->overtimeClaimId;
                $detailModel->attendanceDt = $overtimeData['date'];
                $detailModel->inTime = $overtimeData['date'] . ' ' . $overtimeData['inTime'];
                $detailModel->outTime = $overtimeData['date'] . ' ' . $overtimeData['outTime'];
                $detailModel->typeFlag = 'O';
                $detailModel->status = 'RQ';
                $detailModel->createdDt = Helper::getcurrentExpressionDate();
                $detailModel->createdBy = $this->employeeId;
                $detailModel->otHour = $overtimeData['otHour'];
                $detailModel->dayCode = $overtimeData['dayCode'];
                $detailModel->canceledByRA = 'N';
                $detailModel->totalHour = $overtimeData['totalHour'];
                $detailModel->lunchAllowance = $overtimeData['lunchExpense'];
                $detailModel->lockingAllowance = $overtimeData['lockingExpense'];
                $detailModel->nightAllowance = $overtimeData['nightExpense'];
                $detailModel->otRemarks = $overtimeData['otRemarks'];
                $detailModel->dashainTiharLeave = 0;
                $detailModel->totalLeaveReward = 0;
                $detailModel->leaveReward = 0;
                $this->repository->addOvertimeClaimDetail($detailModel);
            }
            foreach($dataForLeave as $leaveData){
                $detailModel = new OvertimeClaimDetail();
                $detailModel->overtimeClaimDetailId = ((int) Helper::getMaxId($this->adapter, OvertimeClaimDetail::TABLE_NAME, OvertimeClaimDetail::OVERTIME_CLAIM_DETAIL_ID)) + 1;
                $detailModel->overtimeClaimId = $overtimeClaimModel->overtimeClaimId;
                $detailModel->attendanceDt = $leaveData['date'];
                $detailModel->inTime = $leaveData['date'] . ' ' . $leaveData['inTime'];
                $detailModel->outTime = $leaveData['date'] . ' ' . $leaveData['outTime'];
                $detailModel->typeFlag = 'L';
                $detailModel->status = 'RQ';
                $detailModel->createdDt = Helper::getcurrentExpressionDate();
                $detailModel->createdBy = $this->employeeId;
                $detailModel->otHour = $leaveData['otHour'];
                $detailModel->dayCode = $leaveData['dayCode'];
                $detailModel->otRemarks = $leaveData['otRemarks'];
                $detailModel->totalHour = $leaveData['totalHour'];
                $detailModel->leaveReward = ($leaveData['otHour']<6)?0.5:1;
                if($detailModel->attendanceDt==$holidayDetail['LP'] || $detailModel->attendanceDt==$holidayDetail['GP']){
                    $detailModel->dashainTiharLeave = $detailModel->leaveReward;
                }else if($detailModel->attendanceDt==$holidayDetail['BT']){
                    $detailModel->dashainTiharLeave = ($detailModel->leaveReward*2);
                }else{
                    $detailModel->dashainTiharLeave = 0;
                }
                $detailModel->totalLeaveReward = $detailModel->dashainTiharLeave + $detailModel->leaveReward;
                $detailModel->lunchAllowance = $leaveData['lunchExpense'];
                $detailModel->lockingAllowance = $leaveData['nightExpense'];
                $detailModel->nightAllowance = $leaveData['lockingExpense'];
                $detailModel->canceledByRA = 'N';
                $this->repository->addOvertimeClaimDetail($detailModel);
            }
            
            $overtimeClaimModel->status = 'RQ';
            $overtimeClaimModel->createdDt = Helper::getcurrentExpressionDate();
            $overtimeClaimModel->createdBy = $this->employeeId;
            $this->repository->add($overtimeClaimModel);
            $this->flashmessenger()->addMessage("Overtime Claim Successfully added!!!");
            return new CustomViewModel(['success' => true, 'data' => '']);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeId' => $this->employeeId,
        ]);
    }

    public function getHolidayDetailsAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $holidayDetails = $this->repository->getHolidayDetails($data['monthId']);
                return new JsonModel(['success' => true, 'data' => $holidayDetails, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('overtimeClaim');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Overtime Claim Successfully Cancelled!!!");
        return $this->redirect()->toRoute('overtimeClaim');
    }

    public function editAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id === 0) {
            return $this->redirect()->toRoute("overtimeClaim");
        }
        $overtimeModel = new OvertimeClaimDetail();
        $detail = $this->repository->fetchById($id);
        $subDetails = $this->repository->fetchSubDetailById($id);
        // print_r($subDetails);die;

        return Helper::addFlashMessagesToArray($this, [
            'detail' => $detail[0],
            'subDetails' => $subDetails,
            'id' => $id
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id === 0) {
            return $this->redirect()->toRoute("overtimeClaim");
        }
        $overtimeModel = new OvertimeClaimDetail();
        $detail = $this->repository->fetchById($id);
        $subDetails = $this->repository->fetchSubDetailById($id);
        // print_r($subDetails[0]);die;

        return Helper::addFlashMessagesToArray($this, [
            'detail' => $detail[0],
            'subDetails' => $subDetails,
            'id' => $id
        ]);
    }

    public function validateAttendanceAction(){
        $date = date_format(date_create($_POST['date']), "d-M-y");
        $employeeId = $_POST['employeeId'];
        $result = $this->detailRepository->getAttendanceOvertimeValidation($employeeId, $date);
        return new JSONModel(["validation" => $result["VALIDATION"]]);
    }

    public function validateOvertimeDateAction(){
        $date = date_format(date_create($_POST['date']), "d-M-y");
        $employeeId = $_POST['employeeId'];
        $result = $this->detailRepository->getOvertimeDateValidation($employeeId, $date);
        if(empty($result)){
            $result["VALIDATION"] = 'F';
        } else {
            $result["VALIDATION"] = 'T';
        }
        return new JSONModel(["validation" => $result["VALIDATION"]]);
    }

    public function validateEmployeeShiftAction(){
        $result = [];
        $date = date_format(date_create($_POST['date']), "d-M-y");
        $employeeId = $_POST['employeeId'];
        $startTime = $_POST['startTime'];
        $endTime = $_POST['endTime'];

        $result = $this->detailRepository->getOvertimeEmployeeShiftValidation($employeeId, $date, $startTime, $endTime);

        return new JSONModel(["validation" => $result["VALIDATION"]]);
    }

    public function getAllOvertimeDetailAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $data['empId'] = $this->employeeId;
                $overtimeDetails = $this->repository->getAllOvertimeDetail($data);
                foreach($overtimeDetails as $k => $otDetail){
                    $funcLvl = $otDetail['FUNCTIONAL_LEVEL_EDESC'];
                    $dayCode = $otDetail['DAY_CODE'];
                    $otHour = $otDetail['OT_HOUR'];
                    $bonuMulti = $otDetail['BONUS_MULTI'];
                    $lunchExpense = 0;
                    if($otHour>=6){
                        $otDays = 1;
                    }elseif($otHour>=4.5 && $otHour<6){
                        $otDays = 0.5;
                    }else{
                        $otDays = 0;
                    }
                    $festiveOtDays = $otDays * $bonuMulti;
                    if($funcLvl>=6){
                        if($dayCode=="H"){
                            if($otHour>=6){
                                $lunchExpense = 100;
                            }else{
                                $lunchExpense = 0;
                            }
                        }elseif($dayCode=="R" || $dayCode=="F"){
                            if($otHour>=6){
                                $lunchExpense = 0;
                            }elseif($otHour>=4.5){
                                $lunchExpense = 45;
                            }elseif($otHour>=3){
                                $lunchExpense = 100;
                            }elseif($otHour>=2){
                                $lunchExpense = 75;
                            }else{
                                $lunchExpense = 0;
                            }
                        }else{
                            $lunchExpense = 0;
                        }
                    }elseif($funcLvl>=4){
                        if($dayCode=="H"){
                            if($otHour>=6){
                                $lunchExpense = 50;
                            }else{
                                $lunchExpense = 0;
                            }
                        }elseif($dayCode=="R" || $dayCode=="F"){
                            if($otHour>=6){
                                $lunchExpense = 0;
                            }elseif($otHour>=4.5){
                                $lunchExpense = 45;
                            }elseif($otHour>=3){
                                $lunchExpense = 90;
                            }elseif($otHour>=2){
                                $lunchExpense = 60;
                            }else{
                                $lunchExpense = 0;
                            }
                        }else{
                            $lunchExpense = 0;
                        }
                    }elseif($funcLvl == "I" || $funcLvl == "II" || $funcLvl == "III" || $funcLvl == "IV" || $funcLvl == "V"){
                        if($dayCode=="H"){
                            if($otHour>=6){
                                $lunchExpense = 40;
                            }else{
                                $lunchExpense = 0;
                            }
                        }elseif($dayCode=="R" || $dayCode=="F"){
                            if($otHour>=6){
                                $lunchExpense = 0;
                            }elseif($otHour>=4.5){
                                $lunchExpense = 30;
                            }elseif($otHour>=3){
                                $lunchExpense = 65;
                            }elseif($otHour>=2){
                                $lunchExpense = 40;
                            }else{
                                $lunchExpense = 0;
                            }
                        }else{
                            $lunchExpense = 0;
                        }
                    }else{
                        $lunchExpense = 0;
                    }
                    if($otDetail['ELIGIBLE_LOCKING']=='Y'){
                        $locking_allowance = 300;
                    }else{
                        $locking_allowance = 0;
                    }
                    $overtimeDetails[$k]['LUNCH_EXPENSE'] = $lunchExpense;
                    $overtimeDetails[$k]['NIGHT_EXPENSE'] = $otDetail['NIGHT_ALLOWANCE'];
                    $overtimeDetails[$k]['LOCKING_EXPENSE'] = $otDetail['LOCKING_ALLOWANCE'];
                    $overtimeDetails[$k]['OT_DAYS'] = $otDays;
                    $overtimeDetails[$k]['FESTIVE_OT_DAYS'] = $festiveOtDays;
                }

                return new JsonModel(['success' => true, 'data' => $overtimeDetails, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }
    
}
