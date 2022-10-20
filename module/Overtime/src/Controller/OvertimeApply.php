<?php

namespace Overtime\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use SelfService\Form\OvertimeRequestForm;
use SelfService\Model\Overtime;
use SelfService\Model\OvertimeDetail;
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Repository\OvertimeRepository;
use SelfService\Repository\OvertimeClaimRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Model\OvertimeClaim as OvertimeClaimModel;
use SelfService\Model\OvertimeClaimDetail; 
use Application\Custom\CustomViewModel;
use Zend\View\Model\JsonModel;

class OvertimeApply extends AbstractActionController {

    private $form;
    private $adapter;
    private $overtimeRepository;
    private $overtimeDetailRepository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->overtimeRepository = new OvertimeRepository($adapter);
        $this->overtimeDetailRepository = new OvertimeDetailRepository($adapter);
        $this->repository = new OvertimeClaimRepository($adapter);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new OvertimeRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        return $this->redirect()->toRoute("overtimeStatus");
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new Overtime();
        if ($request->isPost()) {
            $data = (array) $request->getPost();

            $otherDetails = $data['details'][0];
            $dataForLeave = $data['dataForLeave'];
            $dataForOvertime = $data['dataForOvertime'];
            $overtimeClaimModel = new OvertimeClaimModel();
            $overtimeClaimModel->overtimeClaimId = ((int) Helper::getMaxId($this->adapter, OvertimeClaimModel::TABLE_NAME, OvertimeClaimModel::OVERTIME_CLAIM_ID)) + 1;
            $overtimeClaimModel->employeeId = $data['empId'];
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
                $detailModel->createdBy = $data['empId'];
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
                $detailModel->createdBy = $data['empId'];
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
            'otstatusOptions' => ['RQ' => 'Pending', 'AP' => 'Approved'],
            'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => "E" , 'RETIRED_FLAG' => 'N'], "FULL_NAME", "ASC", " ", false, true),
        ]);
    }

    public function addRadAction(){
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new Overtime();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $postDataArray = $postData->getArrayCopy();
                $model->exchangeArrayFromForm($this->form->getData());
                $model->overtimeId = ((int) Helper::getMaxId($this->adapter, Overtime::TABLE_NAME, Overtime::OVERTIME_ID)) + 1;
                $model->employeeId = $postData['employeeId'];
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->allTotalHour = Helper::hoursToMinutes($postDataArray['allTotalHour']);
                $this->overtimeRepository->add($model);

                $overtimeDetailModel = new OvertimeDetail();
                for ($i = 0; $i < sizeof($postDataArray['startTime']); $i++) {
                    $startTime = $postDataArray['startTime'][$i];
                    $endTime = $postDataArray['endTime'][$i];
                    $totalHour = $postDataArray['totalHour'][$i];
                    $overtimeDetailModel->overtimeId = $model->overtimeId;
                    $overtimeDetailModel->detailId = ((int) Helper::getMaxId($this->adapter, OvertimeDetail::TABLE_NAME, OvertimeDetail::DETAIL_ID)) + 1;
                    $overtimeDetailModel->startTime = Helper::getExpressionTime($startTime);
                    $overtimeDetailModel->endTime = Helper::getExpressionTime($endTime);
                    $overtimeDetailModel->totalHour = Helper::hoursToMinutes($totalHour);
                    $overtimeDetailModel->status = 'E';
                    $overtimeDetailModel->createdBy = $this->employeeId;
                    $overtimeDetailModel->createdDate = Helper::getcurrentExpressionDate();
                    $this->overtimeDetailRepository->add($overtimeDetailModel);
                }
                $this->flashmessenger()->addMessage("Overtime Request Successfully added!!!");
                return $this->redirect()->toRoute("overtimeStatus");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
//                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", false, true),
            'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => "E" , 'RETIRED_FLAG' => 'N'], "FULL_NAME", "ASC", " ", false, true),
        ]);
    }

    public function attendanceDetailAction() {
        $date = date_format(date_create($_POST['date']), "d-M-y");
        $employeeId = $_POST['employeeId'];
        $result = $this->overtimeRepository->fetchAttendanceDetail($employeeId,$date);
        return new JSONModel(['data' => $result]);
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

    public function getAllOvertimeDetailAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $overtimeDetails = $this->repository->getAllOvertimeDetail($data);
                // echo('<pre>');print_r($overtimeDetails);die;
                foreach($overtimeDetails as $k => $otDetail){
                    $funcLvl = $otDetail['FUNCTIONAL_LEVEL_EDESC'];
                    $dayCode = $otDetail['DAY_CODE'];
                    $otHour = $otDetail['OT_HOUR'];
                    $bonuMulti = $otDetail['BONUS_MULTI'];
                    $lunchExpense = 0;
                    if($otHour>=6){
                        $otDays = 1 * $bonuMulti;
                    }elseif($otHour>=4.5 && $otHour<6){
                        $otDays = 0.5 * $bonuMulti;
                    }else{
                        $otDays = 0;
                    }
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
                }

                return new JsonModel(['success' => true, 'data' => $overtimeDetails, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

}
