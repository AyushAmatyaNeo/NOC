<?php

namespace AttendanceManagement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Form\AttendanceByHrForm;
use AttendanceManagement\Model\AttendanceDetail as AttendanceByHrModel;
use AttendanceManagement\Model\OT as OTModel;
use AttendanceManagement\Model\OTDetail as OTDetailModel;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use AttendanceManagement\Repository\AttendanceRepository;
use DateTime;
use Exception;
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Repository\OvertimeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Application\Repository\MonthRepository;
use Zend\Authentication\Storage\StorageInterface;

class CalculateOvertime extends AbstractActionController {

    private $adapter;
    private $repository;
    private $employeeId;
    protected $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new AttendanceDetailRepository($adapter);
        $this->storageData = $storage->read();
        $this->acl = $this->storageData['acl'];
    }

    public function indexAction() {
        $overtimeRepo = new OvertimeRepository($this->adapter);

        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $status = array(
            "All" => "All Status",
            "P" => "Present Only",
            "A" => "Absent Only",
            "H" => "On Holiday",
            "L" => "On Leave"
        );
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setValue("P");
        $statusFormElement->setAttributes(["id" => "statusId", "class" => "form-control reset-field"]);
        $statusFormElement->setLabel("Status");

        $employeeTypeFormElement = new Select();
        $employeeTypeFormElement->setName("employeeType");
        $employeeType = array(
            '-1' => "All Employee Type",
            "C" => "Contract",
            "R" => "Regular"
        );
        $employeeTypeFormElement->setValueOptions($employeeType);
        $employeeTypeFormElement->setAttributes(["id" => "employeeTypeId", "class" => "form-control"]);
        $employeeTypeFormElement->setLabel("Employee Type");

        $monthRepo = new MonthRepository($this->adapter);
        $fiscalYear = $monthRepo->getCurrentFiscalYear();
        $currFiscalYear = $fiscalYear['FISCAL_YEAR_ID'];

        $currMonthModal = $monthRepo->getCurrentMonth();
        $preMnDtArr = $overtimeRepo->getPreviousMonDate($currFiscalYear, $currMonthModal['MONTH_NO']);

        $is_otCalc = $overtimeRepo->isOTAlreadyCalulated($preMnDtArr['fiscal_year_id'], $preMnDtArr['month_no']);

        $getAllYears = $this->getAllYearsToCalulateOvertime($overtimeRepo, $preMnDtArr['fiscal_year_id'], $preMnDtArr['month_no']);
        $firstKeyYears = key($getAllYears);
        if(!empty($getAllYears)){
            $yearsFormElement = new Select();
            $yearsFormElement->setName("Year");
            $yearsFormElement->setValueOptions($getAllYears);
            $yearsFormElement->setValue($firstKeyYears);
            $yearsFormElement->setAttributes(["id" => "allyearsId", "class" => "form-control reset-field"]);
            $yearsFormElement->setLabel("Year");
        }

        $getAllMonths = $this->getMontsForCalculateOvertime($overtimeRepo, $preMnDtArr['fiscal_year_id'], $preMnDtArr['month_no']);
        if(!empty($getAllMonths)){
            $monthsFormElement = new Select();
            $monthsFormElement->setName("Month");
            $monthsFormElement->setValueOptions($getAllMonths);
            $monthsFormElement->setValue(0);
            $monthsFormElement->setAttributes(["id" => "allmonthsId", "class" => "form-control reset-field"]);
            $monthsFormElement->setLabel("Month");
        }

        $getMonthsOTRead = $overtimeRepo->getOTReadMonths();
        if(!empty($getMonthsOTRead)) {
            $monthsOTReadElement = new Select();
            $monthsOTReadElement->setName("Month");
            $monthsOTReadElement->setValueOptions($getMonthsOTRead);
            $monthsOTReadElement->setValue(1);
            $monthsOTReadElement->setAttributes(["id" => "monthsotreadId", "class" => "form-control reset-field"]);
            $monthsOTReadElement->setLabel("Month");
        }

        $getyearsOTRead = $overtimeRepo->getCurrentFiscalYears($currFiscalYear);
        if(!empty($getyearsOTRead)) {
            $yearsOTReadElement = new Select();
            $yearsOTReadElement->setName("Year");
            $yearsOTReadElement->setValueOptions($getyearsOTRead);
            //$yearsOTReadElement->setValue(1);
            $yearsOTReadElement->setAttributes(["id" => "yearsotreadId", "class" => "form-control reset-field"]);
            $yearsOTReadElement->setLabel("Year");
        }

        return Helper::addFlashMessagesToArray($this, [
                    'status' => $statusFormElement,
                    'employeeType' => $employeeTypeFormElement,
                    'allyears' => $yearsFormElement,
                    'allmonths' => $monthsFormElement,
                    'monthsotRead' =>  $monthsOTReadElement,
                    'yearsotRead' =>  $yearsOTReadElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'isotCalc' => $is_otCalc,
        ]);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $attendanceByHr = new AttendanceByHrForm();
        $this->form = $builder->createForm($attendanceByHr);
    }

    public function viewAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("calculateOvertime");
        }
        $attendanceByHrModel = new AttendanceByHrModel();
        $overtimeRepo = new OvertimeRepository($this->adapter);

        $detail = $this->repository->fetchById($id);
        $attendanceByHrModel->exchangeArrayFromDB($detail);
        $this->form->bind($attendanceByHrModel);
        $overtime = $overtimeRepo->getAllByEmployeeId($detail['EMPLOYEE_ID'], $detail['ATTENDANCE_DT'], 'AP')->current();
        $overtimeDetailRepo = new OvertimeDetailRepository($this->adapter);
        $overtimeDetailResult = $overtimeDetailRepo->fetchByOvertimeId($overtime['OVERTIME_ID']);
        $overtimeDetails = [];
        foreach ($overtimeDetailResult as $overtimeDetailRow) {
            array_push($overtimeDetails, $overtimeDetailRow);
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'overtimeDetails' => $overtimeDetails,
                    'overtimeInHour' => $overtime['TOTAL_HOUR'],
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", NULL, FALSE, TRUE)
                        ]
        );
    }

    public function calculateAction() {
        $overtimeRepo = new OvertimeRepository($this->adapter);

        $request = $this->getRequest();

        if (!$request->isPost()) {
            $this->redirect()->toRoute("calculateOvertime");
        }

        try {
            //$request = $this->getRequest();
            $postData = $request->getPost();

            $this->auth = new AuthenticationService();
            $this->employeeId = $this->auth->getStorage()->read()['employee_id'];
            $fcLevelArr = $overtimeRepo->fetchFCLevelByEmployeeID($this->employeeId);

            if(!empty($fcLevelArr)) {
                $fcLevelID = $fcLevelArr['FUNCTIONAL_LEVEL_ID'];

                if(!empty($postData)) {
                    $postYear = $postData['yearId'];
                    $postMonth = (int) $postData['monthId'] + 1;

                    $is_otCalculated = $overtimeRepo->isOTAlreadyCalulated($postYear, $postMonth);

                    $monthRepo = new MonthRepository($this->adapter);
                    $fiscalYear = $monthRepo->getCurrentFiscalYear();
                    $currFiscalYear = $fiscalYear['FISCAL_YEAR_ID'];

                    $currMonth = $monthRepo->getCurrentMonth();

                    $MonthDtArr = $overtimeRepo->getMonthDateRange($postYear, $postMonth);
                    $fromDateAD = $MonthDtArr['from_dt'];
                    $toDateAD = $MonthDtArr['to_dt'];

                    $preMonthDtArr = $overtimeRepo->getPreviousMonDate($currFiscalYear, $currMonth['MONTH_NO']);

                    $otProcData = $overtimeRepo->fetchEmpOTDetails($fcLevelID, $fromDateAD, $toDateAD);
                    $otEMPArr = Helper::extractDbData($otProcData);

                    $totMonDays = $overtimeRepo->calcTotalMonDays($postYear, $postMonth);

                    //Calculate Employee Overtime
                    if(!empty($otEMPArr) && $is_otCalculated != 'Y') {
                        $is_halfday = FALSE;
                        $is_dayoff = 0;
                        $is_holiday = '';
                        $is_insertedOt = 0;
                        $is_insertedOtDetail = 0;
                        $$isEmpShift = 0;
                        $otDetailDataInsert = [];
                        $inc = 1;
                        foreach($otEMPArr as $indx=>$otSinEMPArr) {
                            $otEMPID = $otEMPArr[$indx]['EMPLOYEE_ID'];
                            $otEMPOTDate = $otEMPArr[$indx]['OVERTIME_DATE_AD'];
                            if(isset($otEMPID) && isset($otEMPOTDate)) {
                                $is_summer = $overtimeRepo->isSummer($otEMPOTDate);

                                $is_holiday = $overtimeRepo->isHoliday($otEMPID, $otEMPOTDate, $currFiscalYear);

                                $isEmpShift = $overtimeRepo->isEmployeeShift($otEMPID, $otEMPOTDate);

                                if(!empty($isEmpShift) && $isEmpShift === 1) {
                                    $is_dayoff = $overtimeRepo->isDayOff($otEMPID, $otEMPOTDate);
                                }

                                if(!empty($is_holiday)) {
                                    $is_holiday = $is_holiday;
                                }

                                if(empty($is_holiday) && !empty($is_dayoff)){
                                    $is_holiday = 'DAYOFF';
                                }

                                $designation_level = $overtimeRepo->fetchDesignationLevel($otEMPID);
                                $designation_level_id = $designation_level['FUNCTIONAL_LEVEL_EDESC'];
                                $AFSLocationCode = $overtimeRepo->isFuelStationWorker($otEMPID);
                                $isFDWorker = $overtimeRepo->isFuelDepositWorker($otEMPID);
                                //Check Dashain or Tihar Holiday
                                $hld_name = $overtimeRepo->isHoliday($otEMPID, $otEMPOTDate, $currFiscalYear);
                                $emp_onleave = $overtimeRepo->isOnLeave($otEMPID, $otEMPOTDate);

                                $otStartTime = strtotime($otEMPArr[$indx]['START_TIME_FORMATTED']);
                                $otEndTime = strtotime($otEMPArr[$indx]['END_TIME_FORMATTED']);
                                $diffOTime = $otEndTime - $otStartTime;

                                if(PHP_VERSION == '7.2.9') {
                                    $diffOTimeFinal = $diffOTime;
                                } else if(PHP_VERSION == '7.4.16') {
                                    $diffOTimeFinal = $diffOTime - 3600;
                                } else {
                                    $diffOTimeFinal = $diffOTime - 3600;
                                }

                                $diffOTimeToMinutes = date('H:i', $diffOTimeFinal);
                                $otEMPArr[$indx]['RAW_OT'] = $diffOTimeToMinutes;
                                $calcOT = round((int)$otEMPArr[$indx]['TOTAL_HOUR'] / 60, 2);
                                $otEMPArr[$indx]['CALC_OT'] = number_format($calcOT, 2);

                                $calcRL = $this->calculateReplacementLeave($otEMPID, $is_holiday, $otEMPArr[$indx]['CALC_OT']);
                                
                                //Get Replacement leave for Dashain and Tihar
                                $DTOMLM = $overtimeRepo->CalculateDashainTiharOMLM($otEMPID, $otEMPOTDate, $preMonthDtArr['fiscal_year_id']);
                                if(!empty($DTOMLM)) {
                                    $RLOMLM = $DTOMLM['LEAVE_MULTIPLE'];
                                    if(!empty($DTOMLM['LEAVE_MULTIPLE'])) {
                                        $calcRL = $DTOMLM['LEAVE_MULTIPLE'];
                                    }
                                }

                                $otEMPArr[$indx]['REP_LEAVE'] = $calcRL;

                                $calcRLAmount = $this->calcRLPayableAmount($otEMPID, $calcRL, $is_holiday, $overtimeRepo, $totMonDays, $preMonthDtArr['fiscal_year_id'], $otEMPOTDate);
                                 $otEMPArr[$indx]['REP_LEAVE_AMOUNT'] = $calcRLAmount;

                                $lunchAllowance = $this->calculateLunchAllowance($otEMPID, $designation_level_id, $is_holiday, $otEMPArr[$indx]['CALC_OT'], $hld_name);

                                $extLunchAllowance = $this->calcExtraLunchAllowance($otEMPID, $AFSLocationCode, $otStartTime, $otEndTime, $designation_level_id, $diffOTimeFinal);

                                $otEMPArr[$indx]['LUNCH_ALLOWANCE'] = $lunchAllowance;
                                $otEMPArr[$indx]['EXTRA_LUNCH_ALLOWANCE'] = $extLunchAllowance;
                                $otEMPArr[$indx]['TOTAL_LUNCH_ALLOWANCE'] = $lunchAllowance + $extLunchAllowance;

                                $nighTimeAllowance = $this->calculateNightTimeAllowance($otEMPID, $AFSLocationCode, $otStartTime, $otEndTime, $isFDWorker, $diffOTimeFinal);

                                $otEMPArr[$indx]['NIGHT_TIME_ALLOWANCE'] = $nighTimeAllowance;

                            }
                                $otModel = new OTModel();
                                $otModel->fiscalyearId = $postYear;
                                $otModel->monthNo = $postMonth;
                                //$otModel->createdDt = '';
                                //$otModel->modifiedDt = '';
                                if($inc === 1) {
                                    $is_OtRecordAdded = $overtimeRepo->addOT($otModel);
                                }                                

                                if($is_OtRecordAdded === 1) {
                                    $last_otId = $overtimeRepo->fetchIdbyTableName('OT_ID', 'HRIS_OT');

                                    $otDetailModel = new OTDetailModel();
                                    $otDetailModel->otId = $last_otId;
                                    $otDetailModel->employeeId = $otSinEMPArr['EMPLOYEE_ID'];
                                    $otDetailModel->approvedRemarks = $otSinEMPArr['APPROVED_REMARKS'];
                                    $otDetailModel->designationId = $otSinEMPArr['DESIGNATION_ID'];
                                    $otDetailModel->toDate = $otSinEMPArr['OVERTIME_DATE_AD'];
                                    $otDetailModel->inTime = Helper::getExpressionTime($otEMPArr[$indx]['START_TIME_FORMATTED']);
                                    $otDetailModel->outTime = Helper::getExpressionTime($otEMPArr[$indx]['END_TIME_FORMATTED']);
                                    $otDetailModel->rawOt = $diffOTimeToMinutes;
                                    $otDetailModel->calc = $calcOT;
                                    $otDetailModel->sattaBida = $calcRL;
                                    $otDetailModel->sattabidaAmount = $calcRLAmount;
                                    $otDetailModel->khajaKarcha = $lunchAllowance;
                                    $otDetailModel->khajakarcha10PM = $extLunchAllowance;
                                    $otDetailModel->ratriVatta = $nighTimeAllowance;
                                    $otDetailModel->approvedBy = $otSinEMPArr['APPROVED_BY'];
                                    $otDetailModel->status = $otSinEMPArr['OVERTIME_STATUS'];
                                    $otDetailModel->overtimeId = $otSinEMPArr['OVERTIME_ID'];

                                    $is_OtDetailRecordAdded = $overtimeRepo->addOTDetail($otDetailModel);

                                    if($is_OtDetailRecordAdded === 1) {
                                        $otDetailDataInsert[$inc] = $is_OtDetailRecordAdded;
                                    } else {
                                         $otDetailDataInsert[$inc] = 0;
                                    }
                                }                              

                                $inc++;
                        }
                        if($is_OtRecordAdded === 1 && !empty($otDetailDataInsert) && !in_array(0, $otDetailDataInsert)) {
                            $overtimeRepo->updateOTStatus($last_otId);
                        }
                        if($is_OtRecordAdded === 1 && !empty($otDetailDataInsert) && in_array(0, $otDetailDataInsert)) {
                            $is_otdet_deleted = $overtimeRepo->delOTDetData($last_otId);
                            while($is_otdet_deleted != 1) {
                                $overtimeRepo->delOTDetData($last_otId);
                            }
                        }
                    }
                }
            }
            $is_otCalculated = $overtimeRepo->isOTAlreadyCalulated($postYear, $postMonth);
            if($is_OtRecordAdded === 1 && !empty($otDetailDataInsert) && !in_array(0, $otDetailDataInsert)) {
                return new JsonModel(['success' => true, 'data' => $otEMPArr, 'isotCalc' =>$is_otCalculated, 'message' => 'Overtime Calculation Successful!']);
            } else {
                return new JsonModel(['success' => false, 'data' => null, 'message' => 'Problem while Calculating Overtime']);
            }            
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }

        $this->redirect()->toRoute("calculateOvertime");
    }


    /**
    * This action is to retrieve Overtime Details data to Read only
    * @method otreadAction
    * 
    */
    public function otreadAction(){
        $overtimeRepo = new OvertimeRepository($this->adapter);

        try {
            $request = $this->getRequest();
            $postData = $request->getPost();

            $otreadArr = [];

            if(!empty($postData)) {
                $postYear = $postData['yearId'];
                $postMonth = (int) $postData['monthId'] + 1;
                $otreadData = $overtimeRepo->fetchEmpOTDetailsRead($postYear, $postMonth);
                $otreadArr = Helper::extractDbData($otreadData);
                if(!empty($otreadArr)) {
                    $i = 0;
                    for($i = 0; $i<count($otreadArr); $i++){
                        if($otreadArr[$i]['REP_LEAVE'] == 0.0) {
                            $otreadArr[$i]['REP_LEAVE'] = 0;
                        } else {
                            $otreadArr[$i]['REP_LEAVE'] = $otreadArr[$i]['REP_LEAVE'];
                        }
                        $i++;
                    }
                }
            }
            return new JsonModel(['success' => true, 'data' => $otreadArr, 'message' => 'Overtime listed successfully!']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => 'Problem in accessing Data: Try Again!']);
        }
    }

    
    /**
    * Method to get the data with filter
    * 
    * @method otreadwfilterAction
    */
    public function otreadwfilterAction(){
        $overtimeRepo = new OvertimeRepository($this->adapter);

        try {
            $request = $this->getRequest();
            $postData = $request->getPost();

            $otreadArr = [];

            if(!empty($postData)) {
                $otreadData = $overtimeRepo->fetchEmpOTDetailsReadFilter($postData);
                $otreadArr = Helper::extractDbData($otreadData);
                if(!empty($otreadArr)) {
                    $i = 0;
                    for($i = 0; $i<count($otreadArr); $i++){
                        if($otreadArr[$i]['REP_LEAVE'] == 0.0) {
                            $otreadArr[$i]['REP_LEAVE'] = 0;
                        } else {
                            $otreadArr[$i]['REP_LEAVE'] = $otreadArr[$i]['REP_LEAVE'];
                        }
                        $i++;
                    }
                }
            }
            if(!empty($otreadArr)) {
                return new JsonModel(['success' => true, 'data' => $otreadArr, 'message' => 'Overtime listed successfully!']);
            } else {
                return new JsonModel(['success' => true, 'data' => $otreadArr, 'message1' => 'No Data Found!']);
            }
            
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => 'Problem in accessing Data: Try Again!']);
        }
    }

    /**
    * Action to display information of the overtime
    *
    * @method viewotdAction
    *
    */
    public function viewotdAction() {
        $overtimeRepo = new OvertimeRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("calculateOvertime");
        }

        $detail = $overtimeRepo->fetchByIdForOTDet($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];

        $recommApprove = $detail['RECOMMENDER_ID'] == $detail['APPROVER_ID'] ? 1 : 0;

        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];

        $stTime = '';
        $endTime = '';
        if(isset($detail['OVERTIME_ID']) && !empty($detail['OVERTIME_ID'])) {
            $stendTime = $overtimeRepo->fetchStartEndTime($detail['OVERTIME_ID']);
            if(!empty($stendTime)) {
                $stTime = $stendTime['START_TIME_FORMAT'];
                $endTime = $stendTime['END_TIME_FORMAT'];
            }
        }

        try {
            $request = $this->getRequest();

            if (!$request->isPost()) {
                return Helper::addFlashMessagesToArray($this, [
                    'employeeName' => $detail['FULL_NAME'],
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $detail['RECOMMENDER_NAME'],
                    'approver' => $detail['APPROVED_BY_NAME'],
                    'status' => $detail['STATUS_DETAIL'],
                    'overtimeDt' => $detail['OVERTIME_DATE'],
                    'overtimeDtBS' => $detail['OVERTIME_DATE_BS'],
                    'description' => $detail['DESCRIPTION'],
                    'remarks' => $detail['REMARKS'],
                    'approvedRemarks' => $detail['APPROVED_REMARKS'],
                    'totalMins' => $detail['TOTAL_HOUR'],
                    'totalHour' => $detail['TOTAL_HOUR_DETAIL'],
                    'starTime' => $stTime,
                    'endTime' => $endTime
                ]);          
            }
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
        }       
    }

    /**
    * Action to give all years 
    * @action getAllYearsToCalulateOvertime
    * @param overtimeRepo
    * @return coYearsUnique
    */
    public function getAllYearsToCalulateOvertime($overtimeRepo, $fiscalYear, $monthNo){
        $allYears = [];
        try {
            $fiscalYears = $overtimeRepo->getAllYearsFromFiscalYears($fiscalYear, $monthNo);
            $fyears_list = Helper::extractDbData($fiscalYears);
            if(!empty($fyears_list) && is_array($fyears_list)) {
                foreach($fyears_list as $fyears) {
                    $allYears[$fyears['FISCAL_YEAR_ID']] = $fyears['YEAR'];
                }
            }
        } catch(Exception $e){
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }

        return $allYears;
    }

    /**
    * Give the lists of all months
    * @action getMontsForCalculateOvertime
    * @param overtimeRepo, fiscalYear, monthNo
    * @return allMonths
    */
    public function getMontsForCalculateOvertime($overtimeRepo, $fiscalYear, $monthNo){
        $allMonths = [];
        try{
            if(isset($monthNo) && !empty($monthNo)) {
                $mon_indx = (int) $monthNo - 1;
            }
            $getAllMonths = $overtimeRepo->getMonthsFromMonth($fiscalYear, $monthNo);
            $months_list = Helper::extractDbData($getAllMonths);

            if(!empty($months_list) && is_array($months_list)) {
                foreach($months_list as $cmonths) {
                    $allMonths[$mon_indx] = $cmonths['MONTH_EDESC'];
                }
            }
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }

        return $allMonths;
    }

    public function pullAttendanceWidOvertimeListAction() {
        throw new Exception("Need Rework on this Page.");
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $attendanceDetailRepository = new AttendanceDetailRepository($this->adapter);
            $overtimeRepo = new OvertimeRepository($this->adapter);
            $overtimeDetailRepo = new OvertimeDetailRepository($this->adapter);
            $employeeId = $data['employeeId'];
            $companyId = $data['companyId'];
            $branchId = $data['branchId'];
            $departmentId = $data['departmentId'];
            $positionId = $data['positionId'];
            $designationId = $data['designationId'];
            $serviceTypeId = $data['serviceTypeId'];
            $serviceEventTypeId = $data['serviceEventTypeId'];
            $fromDate = $data['fromDate'];
            $toDate = $data['toDate'];
            $status = $data['status'];
            $employeeTypeId = $data['employeeTypeId'];
            $overtimeOnly = (int) $data['overtimeOnly'];
            $result = $attendanceDetailRepository->filterRecord($employeeId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $fromDate, $toDate, $status, $companyId, $employeeTypeId, true);
            $list = [];
            foreach ($result as $row) {
                if ($status == 'L') {
                    $row['STATUS'] = "On Leave[" . $row['LEAVE_ENAME'] . "]";
                } else if ($status == 'H') {
                    $row['STATUS'] = "On Holiday[" . $row['HOLIDAY_ENAME'] . "]";
                } else if ($status == 'A') {
                    $row['STATUS'] = "Absent";
                } else if ($status == 'P') {
                    $row['STATUS'] = "Present";
                } else {
                    if ($row['LEAVE_ENAME'] != null) {
                        $row['STATUS'] = "On Leave[" . $row['LEAVE_ENAME'] . "]";
                    } else if ($row['HOLIDAY_ENAME'] != null) {
                        $row['STATUS'] = "On Holiday[" . $row['HOLIDAY_ENAME'] . "]";
                    } else if ($row['HOLIDAY_ENAME'] == null && $row['LEAVE_ENAME'] == null && $row['IN_TIME'] == null) {
                        $row['STATUS'] = "Absent";
                    } else if ($row['IN_TIME'] != null) {
                        $row['STATUS'] = "Present";
                    }
                }
                $overtimeDetailResult = $overtimeDetailRepo->fetchByOvertimeId($row['ID']);
                $overtimeDetails = [];
                foreach ($overtimeDetailResult as $overtimeDetailRow) {
                    array_push($overtimeDetails, $overtimeDetailRow);
                }
                $middleName = ($row['MIDDLE_NAME'] != null) ? " " . $row['MIDDLE_NAME'] . " " : " ";
                $row['EMPLOYEE_NAME'] = $row['FIRST_NAME'] . $middleName . $row['LAST_NAME'];
                $row['DETAILS'] = $overtimeDetails;
                if ($overtimeOnly == 1 && $row['OVERTIME_ID'] != null) {
                    array_push($list, $row);
                } else if ($overtimeOnly == 0) {
                    array_push($list, $row);
                }
            }

            return new JsonModel(['success' => true, 'data' => $list, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function pullInOutTimeAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $attendanceDt = $data['attendanceDt'];
            $employeeId = $data['employeeId'];

            $attendanceRepository = new AttendanceRepository($this->adapter);
            $result = $attendanceRepository->fetchInOutTimeList($employeeId, $attendanceDt);
            $list = [];
            foreach ($result as $row) {
                array_push($list, $row);
            }

            return new JsonModel(['success' => true, 'data' => $list, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    /**
    * Calculate the night time allowance of all staffs, AFS staffs including and excluding Sinamangal Staffs
    *
    * @method calculateNightTimeAllowance
    * @param empID, location_code, in_time, out_time
    * @return nightAllowance
    */
    public function calculateNightTimeAllowance($empID, $location_code, $in_time, $out_time, $is_fdw, $otHours) {
        $nightAllowance = 0;
        $AFSSinamangal = FALSE;

        $overtime_hour = date('H', $otHours);

        $PM5 = strtotime('05:00 PM');
        $PM8 = strtotime('08:00 PM');

        if(isset($empID) && !empty($empID)) {
            if(isset($in_time) && !empty($in_time) && isset($out_time) && !empty($out_time)) {
                if(isset($location_code) && !empty($location_code)) {
                    if($location_code == trim('AFSSIN')) {
                        $AFSSinamangal = TRUE;
                    }
                }

                if ($out_time > $PM8 && (int)$overtime_hour>=3) {
                    if($AFSSinamangal === FALSE || $is_fdw === TRUE){
                        $nightAllowance = 400;
                    }
                    if($AFSSinamangal === TRUE) {
                        $nightAllowance = 500;
                    } 
                } else if($in_time>=$PM5 && $out_time <= $PM8 && (int)$overtime_hour>=3) {
                    if($AFSSinamangal === FALSE || $is_fdw === TRUE){
                        $nightAllowance = 150;
                    }                
                    if($AFSSinamangal === TRUE) {
                        $nightAllowance = 200;
                    }                
                } else {
                    $nightAllowance = 0;
                }
            }    
        }        
        return number_format($nightAllowance, 2);
    }


    /**
    * calculate the Replacement leave
    *
    * @method calculateReplacementLeave
    * @param empID, is_Holiday, $calcOT
    * @return result
    */
    public function calculateReplacementLeave($empID, $is_Holiday, $calcOT) {
        $replacement_leave = 0;
        if(isset($empID) && isset($calcOT)) {
            $overtime_hour = $calcOT;

            if(isset($is_Holiday) && !empty($is_Holiday)){
                if((int) $overtime_hour >= 3 && (int) $overtime_hour<6){
                    $replacement_leave = 0.5;
                } else if ((int) $overtime_hour >= 6) {
                    $replacement_leave = 1;
                } 
                else {
                    $replacement_leave = 0;
                }
            } else {
                if((float) $overtime_hour >= 4.5 && (int) $overtime_hour<6){
                    $replacement_leave = 0.5;
                } else if ((int) $overtime_hour >= 6) {
                    $replacement_leave = 1;
                } else {
                    $replacement_leave = 0;
                }
            }
        }
        return $replacement_leave;
    }

    /**
    * Check if the replacement leave is cashable and calculate the amount
    *
    * @method calcRLPayableAmount
    * @param empID, replacementLeave, is_Holiday, overtimeRepo, totDays, fiscal_year_id, overtimeHours
    * @return result
    */
    public function calcRLPayableAmount($empID, $replacementLeave, $is_Holiday, $overtimeRepo, $totDays, $fiscal_year_id, $overtimeDate) {
        $totalSalGd = 0;

        if(isset($empID) && isset($replacementLeave) && !empty($empID) && !empty($replacementLeave)) {
            $SalGd = 0;
            $SalGd = $overtimeRepo->CalculateSalaryGrade($empID, $totDays, $fiscal_year_id);

            if(isset($is_Holiday) && !empty($is_Holiday)) {
                //Check Replacement Leave and Overtime for Dashain and Tihar
                $omlm = $overtimeRepo->CalculateDashainTiharOMLM($empID, $overtimeDate, $fiscal_year_id);
                if(!empty($omlm)){
                    $overtimeMultiple = $omlm[0]['OVERTIME_MULTIPLE'];
                    $leaveMultiple = $omlm[0]['LEAVE_MULTIPLE'];
                    //$totalSalGd = ($SalGd * $replacementLeave) * $leaveMultiple;
                    $totalSalGd = $SalGd * $leaveMultiple;
                } 
            } else {
                $totalSalGd = (float)$SalGd * (float)$replacementLeave;
            }
            
        }
        return number_format($totalSalGd, 2);
    }

    /**
    * calculate the Replacement leave
    * This method might go to the CalculateOvertime Controller
    *
    * @method calculateLunchAllowance
    * @param empID, dsnLevel, is_Holiday, calcOT, isfestival
    * @return lnchAllowance
    */
    public function calculateLunchAllowance($empID, $dsnLevel, $is_Holiday, $calcOT, $isfestival) {
        $lnchAllowance = 0;
         if(isset($empID) && isset($dsnLevel) && isset($calcOT)){
            $overtime_hour = $calcOT;
            //$dsn_level = (int) $dsnLevel;
            $dsn_level = trim($dsnLevel);
            if((int) $dsn_level >= 6){ // For Designation level greater than 6
                if(isset($is_Holiday) && !empty($is_Holiday)) {
                    if((int) $overtime_hour >= 6){
                        $lnchAllowance = 100;
                    } else {
                        $lnchAllowance = 0; 
                    }
                } else {
                    if((int) $overtime_hour >= 2 && (int) $overtime_hour <3) {
                        $lnchAllowance = 75;
                    } else if ((int) $overtime_hour >=3 && (float) $overtime_hour <4.5) {
                         $lnchAllowance = 100;
                    } else if ((float) $overtime_hour >= 4.5 && (int) $overtime_hour <6) {
                         $lnchAllowance = 45;
                    } else if ((int) $overtime_hour >= 6) {
                         $lnchAllowance = 0;
                    } else {
                       $lnchAllowance = 0; 
                    }
                }
            } else if ($dsn_level == '5') { //For Designation Level = 5
                if(isset($is_Holiday) && !empty($is_Holiday)) {
                    if((int) $overtime_hour >= 6){
                        $lnchAllowance = 50; 
                    } else {
                        $lnchAllowance = 0; 
                    }
                } else {
                    if ((int) $overtime_hour >= 2 && (int) $overtime_hour <3){
                         $lnchAllowance = 60; 
                    } else if((int) $overtime_hour >= 3 && (float) $overtime_hour <4.5){
                        $lnchAllowance = 90;
                    } else if((float) $overtime_hour >= 4.5 && (int) $overtime_hour <6) {
                        $lnchAllowance = 45; 
                    } else if ((int) $overtime_hour >= 6) {
                        $lnchAllowance = 0;
                    } else {
                        $lnchAllowance = 0; 
                    }
                }
            } else if ($dsn_level == '4') { //For Designation Level = 4
                if(isset($is_Holiday) && !empty($is_Holiday)) {
                    if((int) $overtime_hour >= 6){
                        $lnchAllowance = 50; 
                    } else {
                        $lnchAllowance = 0; 
                    }
                } else {
                    if((int) $overtime_hour >= 2 && (int) $overtime_hour <3) {
                         $lnchAllowance = 50; 
                    } else if ((int) $overtime_hour >= 3 && (float) $overtime_hour <4.5) {
                        $lnchAllowance = 75; 
                    } else if((float) $overtime_hour >= 4.5 && (int) $overtime_hour <6) {
                        $lnchAllowance = 45; 
                    } else if ((int) $overtime_hour >= 6) {
                        $lnchAllowance = 0;
                    } else {
                        $lnchAllowance = 0; 
                    }
                }
            } else if ($dsn_level == '3') { //For Designation Level = 3
                if(isset($is_Holiday) && !empty($is_Holiday)) {
                    if((int) $overtime_hour >= 6) {
                        $lnchAllowance = 40;
                    } else {
                        $lnchAllowance = 0; 
                    }
                } else {
                    if ((int) $overtime_hour >= 2 && (int) $overtime_hour<3) {
                        $lnchAllowance = 40;
                    } else if((int) $overtime_hour >= 3 && (float) $overtime_hour<4.5) {
                        $lnchAllowance = 70;
                    } else if ((float) $overtime_hour >= 4.5 && (int) $overtime_hour<6) {
                        $lnchAllowance = 35;
                    } else if ((int) $overtime_hour >= 6) {
                        $lnchAllowance = 0;
                    } else {
                        $lnchAllowance = 0; 
                    }
                }              
            } else if ($dsn_level == '2') { //For Designation Level = 2
                if(isset($is_Holiday) && !empty($is_Holiday)) {
                    if((int) $overtime_hour >= 6) {
                        $lnchAllowance = 40;
                    } else {
                        $lnchAllowance = 0; 
                    }
                } else {
                    if ((int) $overtime_hour >= 2 && (int) $overtime_hour <3) {
                        $lnchAllowance = 40;
                    } else if((int) $overtime_hour >= 3 && (float) $overtime_hour <4.5) {
                        $lnchAllowance = 70;
                    } else if ((float) $overtime_hour >= 4.5 && (int) $overtime_hour <6) {
                        $lnchAllowance = 35;
                    } else if ((int) $overtime_hour >= 6) {
                        $lnchAllowance = 0;
                    } else {
                        $lnchAllowance = 0; 
                    }
                }
            } else if ($dsn_level == '1' || $dsn_level == 'I' || $dsn_level == 'II' || $dsn_level == 'III' || $dsn_level == 'IV' || $dsn_level == 'V') { //For Designation Level = 1
                if(isset($is_Holiday) && !empty($is_Holiday)) {
                    if((int) $overtime_hour >= 6) {
                        $lnchAllowance = 40;
                    } else {
                        $lnchAllowance = 0; 
                    }
                } else {
                    if ((int) $overtime_hour >= 2 && (int) $overtime_hour<3) {
                        $lnchAllowance = 40;
                    } else if((int) $overtime_hour >= 3 && (float) $overtime_hour<4.5) {
                        $lnchAllowance = 65;
                    } else if ((float) $overtime_hour >= 4.5 && (int) $overtime_hour<6) {
                        $lnchAllowance = 35;
                    } else if ((int) $overtime_hour >= 6) {
                        $lnchAllowance = 0;
                    } else {
                        $lnchAllowance = 0; 
                    }
                }
                
            }
            else { //For Other Designations
                if(isset($is_Holiday) && !empty($is_Holiday)) { 
                    if((int) $overtime_hour >= 6) {
                        $lnchAllowance = 40;
                    } else {
                        $lnchAllowance = 0; 
                    }
                } else { //Regular days
                    if ((int) $overtime_hour >= 2 && (float) $overtime_hour<4.5) {
                        $lnchAllowance = 40;
                    } else if((float) $overtime_hour >= 4.5 && (int) $overtime_hour<6) {
                        $lnchAllowance = 35;
                    } else if((int) $overtime_hour >= 6) {
                        $lnchAllowance = 0;
                    } else {
                        $lnchAllowance = 0; 
                    }
                }
            }
        }
        return number_format($lnchAllowance, 2);
    }

    /**
    * Calculate the extra lunch allowance for AFS Workers working beyond 10 PM
    *
    * @method calcExtraLunchAllowance
    * @param empID, location_code, in_time, out_time, designation_level_id, otHours
    * @return extraLunchAllowance
    */
    public function calcExtraLunchAllowance($empID, $location_code, $in_time, $out_time, $designation_level_id, $otHours){
        $extraLunchAllowance = 0;
        $dsn_level_id = trim($designation_level_id);

        $overtime_hour = date('H', $otHours);

        $PM10 = strtotime('10:00 PM');

        if(isset($empID) && !empty($empID)) {
            if(isset($in_time) && !empty($in_time) && isset($out_time) && !empty($out_time)) {
                if($out_time >= $PM10 && (int)$overtime_hour>=3) {
                   if(isset($location_code) && !empty($location_code)){
                        if(isset($dsn_level_id) && !empty($dsn_level_id)) {
                            if((int) $dsn_level_id >= 6) {
                                $extraLunchAllowance = 200;
                            } else if ($dsn_level_id == '5' || $dsn_level_id == '4' || $dsn_level_id == '3' || $dsn_level_id == '2' || $dsn_level_id == '1' || $dsn_level_id == 'I' || $dsn_level_id == 'II' || $dsn_level_id == 'III' || $dsn_level_id == 'IV' || $dsn_level_id == 'V'){
                                $extraLunchAllowance = 150;
                            } else {
                                $extraLunchAllowance = 150;
                            }
                        }
                    } 
                }
            }    
        }        
        return number_format($extraLunchAllowance, 2);
    }


    /**
    * Method to Delte Overtime Data for testing purpose
    */
    public function deleteOTDataAction(){
        $is_del_OTDet = 0;
        $dt = [];
        try {
            $overtimeRepo = new OvertimeRepository($this->adapter);
            $is_del_OTDet = $overtimeRepo->delOTDetailData();
            if($is_del_OTDet === 1)  {
                $overtimeRepo->delOTData();
            }
            return new JsonModel(['success' => true, 'data' => $dt, 'message' => 'Reset Successful']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message1' => 'Problem in Resetting Overtime!']);
        }
        
    }
    /* Implement Test Data function ends */

}
