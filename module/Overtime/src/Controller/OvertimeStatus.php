<?php
namespace Overtime\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Repository\AttendanceRepository;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use Overtime\Repository\OvertimeStatusRepository;
use PHPUnit\TextUI\Help;
use SelfService\Form\OvertimeRequestForm;
use SelfService\Model\Overtime;
use SelfService\Model\OvertimeDetail;
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Repository\OvertimeRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Model\HrEmployees;
use System\Repository\PreferenceSetupRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use ManagerService\Repository\OvertimeClaimApproveRepository;
use SelfService\Repository\LeaveRequestRepository;
use SelfService\Model\OvertimeClaim as OvertimeClaimModel;
use SelfService\Model\OvertimeClaimDetail; 
use Application\Custom\CustomViewModel;

class OvertimeStatus extends HrisController {

    private $detailRepo;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(OvertimeStatusRepository::class);
        $this->detailRepo = new OvertimeDetailRepository($this->adapter);
        $this->approveRepo = new OvertimeClaimApproveRepository($this->adapter);
        $this->initializeForm(OvertimeRequestForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                // print_r($data);die;
                $result = $this->repository->getOTRequestList($data);
                $recordList = [];
                foreach ($result as $row) {
                    $row['DETAILS'] = $this->detailRepo->fetchByOvertimeId($row['OVERTIME_ID']);
                    array_push($recordList, $row);
                }
                return new JsonModel(["success" => "true", "data" => $recordList]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getStatusSelectElement(['name' => 'status', "id" => "requestStatusId", "class" => "form-control reset-field", 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
                'status' => $statusSE,
                'searchValues' => EntityHelper::getSearchData($this->adapter),
                'acl' => $this->acl,
                'employeeDetail' => $this->storageData['employee_detail'],
                'preference' => $this->preference
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id === 0) {
            return $this->redirect()->toRoute("overtimeClaim");
        }
        $overtimeModel = new OvertimeClaimDetail();
        $detail = $this->approveRepo->fetchById($id);
        $subDetails = $this->approveRepo->fetchSubDetailById($id);
        // print_r($subDetails);die;

        return Helper::addFlashMessagesToArray($this, [
            'detail' => $detail[0],
            'subDetails' => $subDetails,
            'id' => $id
        ]);
    }

    public function calculateAction() {
        $preferenceSetupRepo = new PreferenceSetupRepo($this->adapter);
        $employeeRepo = new EmployeeRepository($this->adapter);
        $overtimeModel = new Overtime();
        $overtimeRepository = new OvertimeRepository($this->adapter);
        $overtimeDetailModel = new OvertimeDetail();
        $overtimeDetailRepo = new OvertimeDetailRepository($this->adapter);
        $overtimeRequestSetting = $preferenceSetupRepo->fetchByPreferenceName("OVERTIME_REQUEST");
        $employeeAdmin = $employeeRepo->fetchByAdminFlag();
        foreach ($overtimeRequestSetting as $overtimeRequestSettingRow) {
//            $attendanceDt = date(Helper::PHP_DATE_FORMAT);
            $attendanceDt = "10-May-2017";
            $employeeResult = $employeeRepo->fetchByEmployeeTypeWidShift($overtimeRequestSettingRow['EMPLOYEE_TYPE'], $attendanceDt);
            $preferenceConstraint = $overtimeRequestSettingRow['PREFERENCE_CONSTRAINT'];
            $preferenceCondition = $overtimeRequestSettingRow['PREFERENCE_CONDITION'];
            $constraintValue = $overtimeRequestSettingRow['CONSTRAINT_VALUE'];
            $constraintType = $overtimeRequestSettingRow['CONSTRAINT_TYPE'];
            $requestType = $overtimeRequestSettingRow['REQUEST_TYPE'];
            foreach ($employeeResult as $employeeRow) {
                if ($preferenceConstraint == 'OVERTIME_GRACE_TIME' && $constraintType == 'HOUR') {
                    $attendanceRepository = new AttendanceRepository($this->adapter);
                    $attendanceResult = $attendanceRepository->fetchAllByEmpIdAttendanceDt($employeeRow['EMPLOYEE_ID'], $attendanceDt);
                    $attendanceNum = count($attendanceResult);
                    if ($attendanceNum != 0 && $attendanceNum % 2 == 0) {
                        $getTotalHourTime = $attendanceRepository->getTotalByEmpIdAttendanceDt($employeeRow['EMPLOYEE_ID'], $attendanceDt);
                        $shiftTotalWorkingHrMin = Helper::hoursToMinutes($employeeRow['TOTAL_WORKING_HR']);
                        $lateInHrMin = Helper::hoursToMinutes($employeeRow['LATE_IN']);
                        $earlyOutHrMin = Helper::hoursToMinutes($employeeRow['EARLY_OUT']);
                        $actualWorkingHrMin = Helper::hoursToMinutes($employeeRow['ACTUAL_WORKING_HR']);
                        $actualBreakTime = $shiftTotalWorkingHrMin - $actualWorkingHrMin;
                        $totalWorkingHrMin = $getTotalHourTime['WORKING']['TOTAL_MINS'];
                        $totalNonWorkingHrMin = $getTotalHourTime['NON-WORKING']['TOTAL_MINS'];
                        if ($totalWorkingHrMin > $actualWorkingHrMin) {
                            $extraOvertime = ($actualBreakTime > $totalNonWorkingHrMin) ? $actualBreakTime - $totalNonWorkingHrMin : 0;
                            $overtime = ($totalWorkingHrMin - $actualWorkingHrMin) - $extraOvertime;
                            $overtimeHr = Helper::minutesToHours($overtime);
                            $constraintValueMin = Helper::hoursToMinutes($constraintValue);
                            $overtimeModel->overtimeId = ((int) Helper::getMaxId($this->adapter, Overtime::TABLE_NAME, Overtime::OVERTIME_ID)) + 1;
                            $overtimeModel->employeeId = $employeeRow['EMPLOYEE_ID'];
                            $overtimeModel->overtimeDate = Helper::getExpressionDate($attendanceDt);
                            $overtimeModel->requestedDate = Helper::getcurrentExpressionDate();
                            $overtimeModel->description = "Overtime Request";
                            $overtimeModel->allTotalHour = Helper::getExpressionTime($overtimeHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                            $overtimeModel->status = $requestType;
                            if ($requestType == 'AP') {
                                $overtimeModel->recommendedBy = $employeeAdmin['EMPLOYEE_ID'];
                                $overtimeModel->approvedBy = $employeeAdmin['EMPLOYEE_ID'];
                            }
                            $inTime = strtotime($employeeRow['IN_TIME']);
                            $shiftStartTime = strtotime($employeeRow['START_TIME']);
                            $outTime = strtotime($employeeRow['OUT_TIME']);
                            $shiftEndTime = strtotime($employeeRow['END_TIME']);
                            $result = 0;
                            if ($preferenceCondition == "LESS_THAN") {
                                if ($overtime < $constraintValueMin) {
                                    $result = $overtimeRepository->add($overtimeModel);
                                }
                            } else if ($preferenceCondition == "GREATER_THAN") {
                                if ($overtime > $constraintValueMin) {
                                    $result = $overtimeRepository->add($overtimeModel);
                                }
                            } else if ($preferenceCondition == 'EQUAL') {
                                if ($overtime == $constraintValueMin) {
                                    $result = $overtimeRepository->add($overtimeModel);
                                }
                            }
                            if ($result == 1) {
                                if ($inTime != $shiftStartTime && $inTime < $shiftStartTime) {
                                    $dtlTotalHr = Helper::minutesToHours(round(abs($shiftStartTime - $inTime) / 60, 2));
                                    $overtimeDetailModel->overtimeId = $overtimeModel->overtimeId;
                                    $overtimeDetailModel->detailId = ((int) Helper::getMaxId($this->adapter, OvertimeDetail::TABLE_NAME, OvertimeDetail::DETAIL_ID)) + 1;
                                    $overtimeDetailModel->startTime = Helper::getExpressionTime($employeeRow['IN_TIME']);
                                    $overtimeDetailModel->endTime = Helper::getExpressionTime($employeeRow['START_TIME']);
                                    $overtimeDetailModel->status = 'E';
                                    $overtimeDetailModel->createdBy = $this->employeeId;
                                    $overtimeDetailModel->totalHour = Helper::getExpressionTime($dtlTotalHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                                    $overtimeDetailModel->createdDate = Helper::getcurrentExpressionDate();
                                    $overtimeDetailRepo->add($overtimeDetailModel);
                                }
                                if ($outTime != $shiftEndTime && $shiftEndTime < $outTime) {
                                    $dtlTotalHr = Helper::minutesToHours(round(abs($outTime - $shiftEndTime) / 60, 2));
                                    $overtimeDetailModel->overtimeId = $overtimeModel->overtimeId;
                                    $overtimeDetailModel->detailId = ((int) Helper::getMaxId($this->adapter, OvertimeDetail::TABLE_NAME, OvertimeDetail::DETAIL_ID)) + 1;
                                    $overtimeDetailModel->startTime = Helper::getExpressionTime($employeeRow['END_TIME']);
                                    $overtimeDetailModel->endTime = Helper::getExpressionTime($employeeRow['OUT_TIME']);
                                    $overtimeDetailModel->status = 'E';
                                    $overtimeDetailModel->totalHour = Helper::getExpressionTime($dtlTotalHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                                    $overtimeDetailModel->createdBy = $this->employeeId;
                                    $overtimeDetailModel->createdDate = Helper::getcurrentExpressionDate();
                                    $overtimeDetailRepo->add($overtimeDetailModel);
                                }
                                $this->flashmessenger()->addMessage("Overtime Request Successfully Generated!!!");
                            } else {
                                $this->flashmessenger()->addMessage("There is no required data to generate overtime request!!!");
                            }
                        }
                    }
                }
            }
        }
        $this->redirect()->toRoute('overtimeStatus');
    }

    public function bulkAction() {
        $request = $this->getRequest();
        try {
            if (!$request->ispost()) {
                throw new Exception('the request is not post');
            }
            $postData = $request->getPost()['data'];
            $btnAction = $request->getPost()['btnAction'];
            foreach ($postData as $data) {
                $subDetails = $this->approveRepo->fetchSubDetailById($data['id']);
                $role = $data['role'];
                $overtimeClaimModel = new OvertimeClaimModel();
                // print_r($role);die;
                if($btnAction=='btnApprove'){
                    if($role == 2){
                        $overtimeClaimModel->status = 'RC';
                        $overtimeClaimModel->recommendedBy = $this->employeeId;
                        $overtimeClaimModel->recommendedDate = Helper::getcurrentExpressionDate();
                        $messageSuccess = 'Recommended';
                    }else if($role == 3){
                        $overtimeClaimModel->status = 'AP';
                        $overtimeClaimModel->approvedBy = $this->employeeId;
                        $overtimeClaimModel->approvedDate = Helper::getcurrentExpressionDate();
                        $messageSuccess = 'Approved';
                    }else if($role == 4){
                        $overtimeClaimModel->status = 'AP';
                        $overtimeClaimModel->approvedBy = $this->employeeId;
                        $overtimeClaimModel->approvedDate = Helper::getcurrentExpressionDate();
                        $overtimeClaimModel->recommendedBy = $this->employeeId;
                        $overtimeClaimModel->recommendedDate = Helper::getcurrentExpressionDate();
                        $messageSuccess = 'Approved';
                    }
                    foreach($subDetails as $subDetail){
                        $detailModel = new OvertimeClaimDetail();
                        $detailModel->status = $overtimeClaimModel->status;
                        if($subDetail['CANCELED_BY_RA'] == 'N' && $detailModel->status == 'AP'){
                            if($subDetail['TYPE_FLAG'] == 'L'){
                                $this->approveRepo->classifySubstituteLeave($subDetail['ID']);                            
                            }
                        }
                        $this->approveRepo->editDetail($detailModel, $subDetail['ID']);
                    }
                    $this->approveRepo->edit($overtimeClaimModel, $data['id']);
                }else if($btnAction=='btnReject'){
                    $overtimeClaimModel->status = 'R';
                    
                    foreach($subDetails as $subDetail){
                        $detailModel = new OvertimeClaimDetail();
                        $detailModel->status = $overtimeClaimModel->status;
                        $detailModel->modifiedBy= $this->employeeId;
                        $detailModel->modifiedDt = Helper::getcurrentExpressionDate();
                        $this->approveRepo->editDetail($detailModel, $subDetail['ID']);
                    }
                    $overtimeClaimModel->modifiedBy= $this->employeeId;
                    $overtimeClaimModel->modifiedDt = Helper::getcurrentExpressionDate();
                    $this->approveRepo->edit($overtimeClaimModel, $data['id']);
                    $messageSuccess = 'Rejected';
    
                }
            }
            $this->flashmessenger()->addMessage("Overtime Claim Successfully ".$messageSuccess." !!!");
            return new CustomViewModel(['success' => true, 'message' => $messageSuccess]);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function makeDecision($id, $approve, $remarks = null, $enableFlashNotification = false) {
        $model = new Overtime();
        $model->overtimeId = $id;
        $model->recommendedDate = Helper::getcurrentExpressionDate();
        $model->recommendedBy = $this->employeeId;
        $model->approvedRemarks = $remarks;
        $model->approvedDate = Helper::getcurrentExpressionDate();
        $model->approvedBy = $this->employeeId;
        $model->status = $approve ? "AP" : "R";
        $message = $approve ? "Travel Request Approved" : "Travel Request Rejected";
        $notificationEvent = $approve ? NotificationEvents::OVERTIME_APPROVE_ACCEPTED : NotificationEvents::OVERTIME_APPROVE_REJECTED;
        $this->repository->edit($model, $id);
        if ($enableFlashNotification) {
            $this->flashmessenger()->addMessage($message);
        }
        try {
            HeadNotification::pushNotification($notificationEvent, $model, $this->adapter, $this);
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
        }
    }

    public function approveRejectAction(){
        $request = $this->getRequest();
        try {
            if (!$request->ispost()) {
                throw new Exception('the request is not post');
            }
            $id = (int) $this->params()->fromRoute('id');
            $role = 4;
            $data = $request->getPost();
            $overtimeClaimModel = new OvertimeClaimModel();
            $overtimeClaimModel->appOtHours = $data['totalOT'];
            $overtimeClaimModel->appSubstituteLeaveNo = $data['totalLeave'];
            $overtimeClaimModel->appOtDays = $data['appOtDays'];
            $overtimeClaimModel->appOtHours = $data['appOtHours'];
            $overtimeClaimModel->appLunchAllowance = $data['appLunchAllowance'];
            $overtimeClaimModel->appLockingAllowance = $data['appLockingAllowance'];
            $overtimeClaimModel->appNightAllowance = $data['appNightAllowance'];
            $overtimeClaimModel->appSubstituteLeaveNo = $data['appSattaBida'];
            $overtimeClaimModel->appDashainTiharLeave = $data['appTiharBida'];
            $overtimeClaimModel->appGrandTotalLeavel = $data['appTotalBida'];

            $overtimeClaimModel->appFestiveOtDays = $data['festiveOtDays'];
            $overtimeClaimModel->grandTotalAppOtDays = $data['grandTotalOtDays'];
            $messageSuccess = '';

            if($data['btnId']=='btnApprove'){
                if($role == 2){
                    $overtimeClaimModel->status = 'RC';
                    $overtimeClaimModel->recommendedBy = $this->employeeId;
                    $overtimeClaimModel->recommendedDate = Helper::getcurrentExpressionDate();
                    $overtimeClaimModel->recommendedRemarks = $data['recommenderRemarks'];
                    $messageSuccess = 'Recommended';
                }else if($role == 3){
                    $overtimeClaimModel->status = 'AP';
                    $overtimeClaimModel->approvedBy = $this->employeeId;
                    $overtimeClaimModel->approvedDate = Helper::getcurrentExpressionDate();
                    $overtimeClaimModel->approvedRemarks = $data['approverRemarks'];
                    $messageSuccess = 'Approved';
                }else if($role == 4){
                    $overtimeClaimModel->status = 'AP';
                    $overtimeClaimModel->approvedBy = $this->employeeId;
                    $overtimeClaimModel->approvedDate = Helper::getcurrentExpressionDate();
                    $overtimeClaimModel->approvedRemarks = $data['raRemarks'];
                    $overtimeClaimModel->recommendedBy = $this->employeeId;
                    $overtimeClaimModel->recommendedDate = Helper::getcurrentExpressionDate();
                    $overtimeClaimModel->recommendedRemarks = $data['raRemarks'];
                    $messageSuccess = 'Approved';
                }
            // echo('<pre>');print_r($overtimeClaimModel);die;

                foreach($data['subDetail'] as $k => $subDetail){
                    $detailModel = new OvertimeClaimDetail();
                    $detailModel->status = $overtimeClaimModel->status;
                    if($subDetail['checked'] == 'true'){
                        $detailModel->canceledByRA = 'Y';
                    }else if($overtimeClaimModel->status == 'AP'){
                        if($subDetail['typeFlag'] == 'L'){
                            $this->approveRepo->classifySubstituteLeave($k);                            
                        }
                    }
                    $this->approveRepo->editDetail($detailModel, $k);
                }
                $this->approveRepo->edit($overtimeClaimModel, $id);
                $this->flashmessenger()->addMessage("Overtime Claim Successfully ".$messageSuccess." !!!");
                return new CustomViewModel(['success' => true, 'message' => $messageSuccess]);
            }else if($data['btnId']=='btnReject'){
                $overtimeClaimModel->status = 'R';
                if($role == 2){
                    $overtimeClaimModel->recommendedRemarks = $data['recommenderRemarks'];
                }else if($role == 3){
                    $overtimeClaimModel->approvedRemarks = $data['approverRemarks'];
                }else if($role == 4){
                    $overtimeClaimModel->approvedRemarks = $data['raRemarks'];
                    $overtimeClaimModel->recommendedRemarks = $data['raRemarks'];
                }
                foreach($data['subDetail'] as $k => $subDetail){
                    $detailModel = new OvertimeClaimDetail();
                    $detailModel->status = $overtimeClaimModel->status;
                    $detailModel->modifiedBy= $this->employeeId;
                    $detailModel->modifiedDt = Helper::getcurrentExpressionDate();
                    $this->approveRepo->editDetail($detailModel, $k);
                }
                $overtimeClaimModel->modifiedBy= $this->employeeId;
                $overtimeClaimModel->modifiedDt = Helper::getcurrentExpressionDate();
                echo('<pre>');print_r($overtimeClaimModel);die;
                $this->approveRepo->edit($overtimeClaimModel, $id);
                $messageSuccess = 'Rejected';
                $this->flashmessenger()->addMessage("Overtime Claim Successfully ".$messageSuccess." !!!");
                return new CustomViewModel(['success' => true, 'message' => $messageSuccess]);

            }
            

        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
