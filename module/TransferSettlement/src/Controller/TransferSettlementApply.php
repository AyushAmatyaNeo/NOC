<?php

namespace TransferSettlement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Application\Custom\CustomViewModel;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelRequest as TravelRequestModel;
use SelfService\Model\TravelExpenses as TravelExpensesModel;
use SelfService\Repository\TravelExpensesRepository;
use SelfService\Model\TravelSubstitute;
use SelfService\Repository\TravelRequestRepository;
use TransferSettlement\Repository\TransferSettlementApplyRepository;
use SelfService\Repository\TravelSubstituteRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use SelfService\Model\TransferSettlement as TransferSettlementModel;
use Application\Controller\HrisController;

class TransferSettlementApply extends HrisController {

//    private $form;
//    private $adapter;
//    private $travelRequesteRepository;
//    private $employeeId;
//    private $preference;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new TransferSettlementApplyRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        $this->preference = $auth->getStorage()->read()['preference'];
        $this->initializeForm(TravelRequestForm::class);
    }

//    public function initializeForm(string $formClass) {
//        $builder = new AnnotationBuilder();
//        $form = new TravelRequestForm();
//        $this->form = $builder->createForm($form);
//    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                if($data['startDate']){
                    $data['startDate'] = $data['startDate'];
                }
                if($data['endDate']){
                    $data['endDate'] = $data['endDate'];
                }
                if($data['eventDate']){
                    $data['eventDate'] = $data['eventDate'];
                }
                // print_r($data);die;
                $data['employeeId'] = $this->employeeId;
                $data['employees']= implode(",", $data['employees']);
                $rawList = $this->repository->getFilteredRecords($data);
                $list = $rawList;
                

                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'statusId', 'class' => 'form-control reset-field', 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
                    'status' => $statusSE,
                    'employeeId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }


    public function addAction() {
        $this->initializeForm(TravelRequestForm::class);
        $request = $this->getRequest();

        $model = new TravelRequestModel();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $travelSubstitute = null;
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                //pass fromDate
                $model->fromDate = Helper::getExpressionDate($model->fromDate);
                //pass toDate
                $model->toDate = Helper::getExpressionDate($model->toDate);
//                $model->status = 'RQ';
                $model->deductOnSalary = 'Y';
                $model->status = ($postData['applyStatus'] == 'AP') ? 'AP' : 'RQ';

                if($model->status == 'AP'){
                    $model->hardcopySignedFlag = 'Y';
                }
                $model->transportTypeList = implode(',',$model->transportTypeList);
                $this->travelRequesteRepository->add($model);
                // Print_r('asdf');die;
                $this->flashmessenger()->addMessage("Travel Request Successfully added!!!");


                if ($travelSubstitute !== null) {
                    $travelSubstituteModel = new TravelSubstitute();
                    $travelSubstituteRepo = new TravelSubstituteRepository($this->adapter);

                    $travelSubstitute = $postData->travelSubstitute;

                    $travelSubstituteModel->travelId = $model->travelId;
                    $travelSubstituteModel->employeeId = $travelSubstitute;
                    $travelSubstituteModel->createdBy = $this->employeeId;
                    $travelSubstituteModel->createdDate = Helper::getcurrentExpressionDate();
                    $travelSubstituteModel->status = 'E';

                    if (isset($this->preference['travelSubCycle']) && $this->preference['travelSubCycle'] == 'N') {
                        $travelSubstituteModel->approvedFlag = 'Y';
                        $travelSubstituteModel->approvedDate = Helper::getcurrentExpressionDate();
                    }

                    $travelSubstituteRepo->add($travelSubstituteModel);
                    if (!isset($this->preference['travelSubCycle']) OR ( isset($this->preference['travelSubCycle']) && $this->preference['travelSubCycle'] == 'Y')) {
                        try {
                            HeadNotification::pushNotification(NotificationEvents::TRAVEL_SUBSTITUTE_APPLIED, $model, $this->adapter, $this);
                        } catch (Exception $e) {
                            $this->flashmessenger()->addMessage($e->getMessage());
                        }
                    } else {
                        try {
                            HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this);
                        } catch (Exception $e) {
                            $this->flashmessenger()->addMessage($e->getMessage());
                        }
                    }
                } else {
                    $travelSubstituteModel = new TravelSubstitute();
                    $travelSubstituteRepo = new TravelSubstituteRepository($this->adapter);

                    $travelSubstitute = $postData->travelSubstitute;
                    $travelSubstituteModel->approvedFlag = 'Y';
                    $travelSubstituteModel->approvedDate = Helper::getcurrentExpressionDate();
                    $travelSubstituteModel->travelId = $model->travelId;
                    $travelSubstituteModel->employeeId = $travelSubstitute;
                    $travelSubstituteModel->createdBy = $this->employeeId;
                    $travelSubstituteModel->createdDate = Helper::getcurrentExpressionDate();
                    $travelSubstituteModel->status = 'E';

                    $travelSubstituteRepo->add($travelSubstituteModel);
                    try {
                        HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this);
                    } catch (Exception $e) {
                        $this->flashmessenger()->addMessage($e->getMessage());
                    }
                }
                return $this->redirect()->toRoute("travelStatus");
            }
        }
        $requestType = array(
            'ad' => 'Advance'
        );
        $transportTypes = array(
            'AP' => 'Aeroplane',
            'OV' => 'Office Vehicles',
            'TI' => 'Taxi',
            'BS' => 'Bus',
            'OF'  => 'On Foot'
        );
        
        $applyOptionValues = [
            'RQ' => 'Pending',
            'AP' => 'Approved'
        ];
        $applyOption = $this->getSelectElement(['name' => 'applyStatus', 'id' => 'applyStatus', 'class' => 'form-control', 'label' => 'Type'], $applyOptionValues);

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'requestTypes' => $requestType,
                    'transportTypes' => $transportTypes,
                    'applyOption' => $applyOption,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FULL_NAME", "ASC", "-", false, true, $this->employeeId)
        ]);
    }

    public function expenseAddAction() {
        $request = $this->getRequest();
        $model = new TravelExpensesModel();
        $transferSettlementModel = new TransferSettlementModel();
        $repo = new TravelExpensesRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }
        $detail = $this->repository->getTransferDetails($id);

        $classId = $this->repository->getClassIdFromEmpId($this->employeeId);
        $configId = $this->repository->getCongifId($classId);
        $configDetails = $this->repository->getConfigDetails($configId);
        
        if ($request->isPost()) {
            $jobHistoryId = (int) $this->params()->fromRoute('id');
            $postData = $request->getPost()->getArrayCopy();
            $numberOfFamily = 0;
            if ($postData['depDate'][0] != null){
                for ($i = 0; $i < count($postData['depDate']); $i++){
                    if($postData['isForFamily'][$i] == 'Y'){
                        $numberOfFamily = $numberOfFamily + 1;
                    }
                }
            }
            $serialNumber = $this->repository->getSerialNumber($jobHistoryId);
            if ($postData['depDate'][0] != null){
                for ($i = 0; $i < count($postData['depDate']); $i++){
                    $transferSettlementId = ((int) Helper::getMaxId($this->adapter, TransferSettlementModel::TABLE_NAME, TransferSettlementModel::TRANSFER_SETTLEMENT_ID)) + 1;

                    $unit = $postData['noOfDays'][$i];
                    
                    $transferSettlementModel->forFamily = $postData['isForFamily'][$i];
                    
                    if($postData['priceOfGoods'][0]){
                        if($postData['priceOfGoods'][0] > $configDetails['MAX_ALLOWED_WEIGHT_AMT']){
                            $transferSettlementModel->weightApAmt = $configDetails['MAX_ALLOWED_WEIGHT_AMT']?$configDetails['MAX_ALLOWED_WEIGHT_AMT']:0; 
                            $transferSettlementModel->weightReqAmt = $postData['priceOfGoods'][0]?$postData['priceOfGoods'][0]:0;
                        }else{
                            $transferSettlementModel->weightApAmt = $postData['priceOfGoods'][0]?$postData['priceOfGoods'][0]:0;
                            $transferSettlementModel->weightReqAmt = $postData['priceOfGoods'][0]?$postData['priceOfGoods'][0]:0;
                        }   
                    }else{
                        $transferSettlementModel->weightApAmt = 0;
                        $transferSettlementModel->weightReqAmt = 0;
                    }                                                                                
                    $rate = $configDetails['TADA_AMT'];
                    $settlement_amt = $configDetails['YEARLY_SETTTLEMENT_AMT'];
                    $total = (float)$unit * (float)$rate;
                    $transferSettlementModel->serialNumber = $serialNumber;
                    $transferSettlementModel->weight = $postData['weightOfGoods'][0]?$postData['weightOfGoods'][0]:0;
                    $transferSettlementModel->transferSettlementId = $transferSettlementId;
                    $transferSettlementModel->jobHistoryId= $jobHistoryId;
                    $transferSettlementModel->employeeId = $postData['tansferEmployeeId'];
                    $transferSettlementModel->familyTadaAmt = 0;
                    $transferSettlementModel->hours = 0;
                    $transferSettlementModel->requestedDate = Helper::getcurrentExpressionDate();
                    $transferSettlementModel->toDate = Helper::getExpressionDate($postData['depDate'][$i]);
                    $transferSettlementModel->fromDate = Helper::getExpressionDate($postData['depDate'][$i]);
                    $transferSettlementModel->departure = $postData['locFrom'][$i];
                    $transferSettlementModel->destination = $postData['locto'][$i];
                    $transferSettlementModel->travelledDays = $postData['noOfDays'][$i];
                    $transferSettlementModel->totalTadaAmt = $total;
                    $transferSettlementModel->planeExpenseReqAmt = $postData['rate2'][$i]?$postData['rate2'][$i]:0;
                    $transferSettlementModel->familyNoTravlledWith = $numberOfFamily?$numberOfFamily:0;
                    $transferSettlementModel->vehicleExpenseReqAmt = $postData['rate1'][$i]?$postData['rate1'][$i]:0;
                    $transferSettlementModel->planeExpenseApAmt = $postData['rate2'][$i]?$postData['rate2'][$i]:0;
                    $transferSettlementModel->vehicleExpenseApAmt = $postData['rate1'][$i]?$postData['rate1'][$i]:0;
                    $transferSettlementModel->miscExpenseReqAmt = $postData['otherExpenses'][$i]?$postData['otherExpenses'][$i]:0;
                    $transferSettlementModel->miscExpenseApAmt = $postData['otherExpenses'][$i]?$postData['otherExpenses'][$i]:0;
                    $transferSettlementModel->createdDt = Helper::getcurrentExpressionDate();
                    $transferSettlementModel->createdBy = $postData['tansferEmployeeId'];
                    $transferSettlementModel->miles = $postData['miles'][$i]?$postData['miles'][$i]:0;
                    $transferSettlementModel->miscExpenseDetail = $postData['otherExpenseDetail'][$i];
                    $transferSettlementModel->purpose = $postData['detPurpose'][$i];
                    $transferSettlementModel->transportClass = $postData['transportClass'][$i];
                    $transferSettlementModel->expenseCategory = $postData['mot'][$i];
                    $transferSettlementModel->transportation = $postData['transport'][$i];
                    $transferSettlementModel->status = 'RQ';
                    $transferSettlementModel->remarks = $postData['detRemarks'][$i];
                    $transferSettlementModel->familyName= $postData['familyName'][$i];
                    if($detail['ELIGIBLE_FOR_SETTLEMENT_AMT'] == 'Y'){
                        $transferSettlementModel->yearlySettlementReqAmt = $settlement_amt;
                        $transferSettlementModel->yearlySettlementApAmt = $settlement_amt;
                    }else{
                        $transferSettlementModel->yearlySettlementReqAmt = 0;
                        $transferSettlementModel->yearlySettlementApAmt = 0;
                    }
                    $this->repository->add($transferSettlementModel);
                }
            }
            $error = "";

            $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("transferSettlementApply");
        }


        $rowNumber = $this->repository->getRowNumber($id);
        // print_r($detail);die;
        $maxWeightamt = $configDetails['MAX_ALLOWED_WEIGHT_AMT'];
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail,
                    'maxWeightamt'=>$maxWeightamt,
                    'id' => $id,
                    'configDetails' => $configDetails,
                    'rowNumber' => $rowNumber
        ]);
    }

    public function editExpenseAction() {
        $request = $this->getRequest();
        $model = new TravelExpensesModel();
        $repo = new TravelExpensesRepository($this->adapter);
        
        $transportList = ['On Foot' =>'On Foot','Office Vehicle' => 'Office Vehicle', 'Train' => 'Train', 
        'Airplane' => 'Airplan', 'Cruise' => 'Cruise', 'Taxi' => 'Taxi', 'Bus' => 'Bus'];

        $transportTypes = ['WALKING'=>'Walking', 'TRAVEL' => 'Transportation'];
        $internationalPlaces = ['LISTED CITIES'=>'Listed Cities','OTHER INDIA CITIES'=>'Other India Cities','OTHER COUNTRIES'=>'Other Countries'];
       

        $id = (int) $this->params()->fromRoute('id');
        $expenseRepo = new TravelExpensesRepository($this->adapter);
        $linkedId = $expenseRepo->getLinkedId($id);
        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus",["action" => "settlementReport"]);
        }
        if ($this->travelRequesteRepository->checkAllowEdit($id) == 'N') {
            return $this->redirect()->toRoute("travelStatus",["action" => "settlementReport"]);
        }

        if ($request->isPost()) {
            $postData = $request->getPost();
            $employeeIdForExpense = $postData['employeeId'];
            $detail = $this->travelRequesteRepository->fetchById($id);
            $travelRequest = new TravelRequestModel();
        $travelRequest->exchangeArrayFromDB($detail);
        $this->form->bind($travelRequest);
        
        // echo('<pre>');print_r($travelRequest);die;
            if ($this->form->isValid()) {
                // print_r('true');die;
                $repo->deletePreviousData($id);
                if ($postData['depDate'][0] != null){
                    for ($i = 0; $i < count($postData['depDate']); $i++){
                        if($postData['mot'][$i] == "WALKING"){
                            $unit = $postData['kmWalked'][$i];
                        }else{
                            $unit = $postData['noOfDays'][$i];
                        }
                        if (fmod($postData['noOfDays'][$i],1)==0){
                            $model->halfDayFlag = 'N';
                        }else{
                            $model->halfDayFlag='Y';
                        }
                        $classId = $this->travelRequesteRepository->getClassIdFromEmpId($employeeIdForExpense);
                        $configId = $this->travelRequesteRepository->getCongifId("DOMESTIC", $postData['mot'][$i], $classId);
                        $rate = $this->travelRequesteRepository->getRateFromConfigId($configId);
                        // print_r($detail['FROM_DATE']);
                        // print_r($postData['depDate'][$i]);
                        // print_r($detail['TO_DATE']);die;

                        if((date("Y-m-d", strtotime($postData['depDate'][$i]))<date("Y-m-d", strtotime($detail['FROM_DATE'])) && date("Y-m-d", strtotime($postData['arrDate'][$i]))<date("Y-m-d", strtotime($detail['FROM_DATE'])))
                        || (date("Y-m-d", strtotime($postData['depDate'][$i]))>date("Y-m-d", strtotime($detail['TO_DATE'])) && date("Y-m-d", strtotime($postData['arrDate'][$i]))>date("Y-m-d", strtotime($detail['TO_DATE'])))){
                            $total = 0;
                        }else{
                            $total = (float)$unit * (float)$rate;
                        }
                        // $total = (float)$unit * (float)$rate;
                        $model->travelExpenseId = ((int) Helper::getMaxId($this->adapter, "hris_travel_expense", "TRAVEL_EXPENSE_ID")) + 1;
                        $model->travelId= $id;
                        $model->configId= $configId;
                        $model->amount= $total;
                        $model->other_expense= (float)$postData['otherExpenses'][$i];
                        $model->total= (float)$total+(float)$postData['otherExpenses'][$i]+(float)$postData['rate1'][$i]+(float)$postData['rate2'][$i];
                        $model->exchangeRate=1;
                        $model->expenseDate=Helper::getcurrentExpressionDate();
                        $model->status='E';
                        $model->remarks=$postData['detRemarks'][$i];
                        
                        $model->createdDt=Helper::getcurrentExpressionDate();
                        $model->departure_DT = Helper::getExpressionDate($postData['depDate'][$i]);
                        $model->departure_Place = $postData['locFrom'][$i];
                        $model->arraival_DT = Helper::getExpressionDate($postData['arrDate'][$i]);
                        
                        $model->arraival_place = $postData['locto'][$i];

                        $model->arraival_place = $postData['locto'][$i];
                        $model->transportation = $postData['transport'][$i];
                        $model->transportationClass = $postData['transportClass'][$i];
                        $model->rate1 = $postData['rate1'][$i]?$postData['rate1'][$i]:0;
                        $model->rate2 = $postData['rate2'][$i]?$postData['rate2'][$i]:0;
                        $model->miles = $postData['miles'][$i]?$postData['miles'][$i]:0;
                        $model->purpose = $postData['detPurpose'][$i];
                        $model->otherExpenseDetail = $postData['otherExpenseDetail'][$i];
                        $repo->add($model);
                        
                    }
                }
                
                if ($postData['depDateInternational'][0] != null){
                   
                    for ($j = 0; $j < count($postData['depDateInternational']); $j++){
                        $unit = $postData['noOfDaysInternational'][$j];
                        $classId = $this->travelRequesteRepository->getClassIdFromEmpId($employeeIdForExpense);
                        $configId = $this->travelRequesteRepository->getCongifId("INTERNATIONAL", $postData['motInternational'][$j], $classId);
                        $rate = $this->travelRequesteRepository->getRateFromConfigId($configId);
                        if((date("Y-m-d", strtotime($postData['depDateInternational'][$j]))<date("Y-m-d", strtotime($detail['FROM_DATE'])) && date("Y-m-d", strtotime($postData['arrDateInternational'][$j]))<date("Y-m-d", strtotime($detail['FROM_DATE'])))
                        || (date("Y-m-d", strtotime($postData['depDateInternational'][$j]))>date("Y-m-d", strtotime($detail['TO_DATE'])) && date("Y-m-d", strtotime($postData['arrDateInternational'][$j]))>date("Y-m-d", strtotime($detail['TO_DATE'])))){
                            $total = 0;
                        }else{
                            $total = (float)$unit * (float)$rate;
                        }
                        if (fmod($postData['noOfDaysInternational'][$j],1)==0){
                            $model->halfDayFlag = 'N';
                        }else{
                            $model->halfDayFlag='Y';
                        }
                        // $total = $unit * $rate;
                        $model->travelExpenseId = ((int) Helper::getMaxId($this->adapter, "hris_travel_expense", "TRAVEL_EXPENSE_ID")) + 1;
                        $model->travelId= $id;
                        $model->configId= $configId;
                        $model->amount= $total;
                        $model->other_expense= $postData['otherExpensesInternational'][$j];
                        $model->total= (float)$total+(float)$postData['otherExpensesInternational'][$j]+($postData['internationalRate1'][$j]?(float)$postData['internationalRate1'][$j]:0)+($postData['internationalRate2'][$j]?(float)$postData['internationalRate2'][$j]:0);
                        $model->exchangeRate=$postData['exchangeRateInternational'][$j];
                        $model->expenseDate=Helper::getcurrentExpressionDate();
                        $model->status='E';
                        $model->remarks=$postData['detRemarksInternational'][$j];
                        $model->createdDt=Helper::getcurrentExpressionDate();
                        $model->departure_DT = Helper::getExpressionDate($postData['depDateInternational'][$j]);
                        $model->departure_Place = $postData['locFromInternational'][$j];
                        $model->arraival_DT = Helper::getExpressionDate($postData['arrDateInternational'][$j]);
                        $model->arraival_place = $postData['loctoInternational'][$j];
                        $model->transportation = $postData['internationalTransport'][$j];
                        $model->transportationClass = $postData['internationalTransportClass'][$j];
                        $model->rate1 = $postData['internationalRate1'][$j]?$postData['internationalRate1'][$j]:0;
                        $model->rate2 = $postData['internationalRate2'][$j]?$postData['internationalRate2'][$j]:0;
                        $model->miles = $postData['internationalMiles'][$j]?$postData['internationalMiles'][$j]:0;
                        $model->purpose = $postData['internationalDetPurpose'][$j];
                        $model->otherExpenseDetail = $postData['internationalOtherExpenseDetail'][$j];
                        $repo->add($model);
                    }
                }
                if($postData['teamLead']){
                    $travelRequest->isTeamLead = 'Y';
                }else{
                    $travelRequest->isTeamLead = 'N';
                }
                $travelRequest->requestedDate = Helper::getExpressionDate($travelRequest->requestedDate);
                $travelRequest->modifiedDt = Helper::getcurrentExpressionDate();
                $travelRequest->fromDate = Helper::getExpressionDate($travelRequest->fromDate);
                $travelRequest->toDate = Helper::getExpressionDate($travelRequest->toDate);
                $travelRequest->recommendedDate = Helper::getExpressionDate($travelRequest->recommendedDate);
                $travelRequest->departureDate = $travelRequest->fromDate;
                $travelRequest->returnedDate = $travelRequest->toDate;
                $travelRequest->approvedDate = Helper::getExpressionDate($travelRequest->approvedDate);

                $travelRequest->employeeId = $employeeIdForExpense;  
                $travelRequest->requestedAmount = $this->travelRequesteRepository->getTotalExpenseAmount($id);
                $this->travelRequesteRepository->deletePreviouseLinkFiles($id);
                $this->travelRequesteRepository->linkTravelWithFiles($id);
                $this->travelRequesteRepository->edit($travelRequest, $id);
                
                $this->flashmessenger()->addMessage("Travel Request Successfully Edited!!!");
                
                return $this->redirect()->toRoute("travelStatus",["action" => "settlementReport"]);
            }
            print_r('form not valid');die;
        }

        $detail = $this->travelRequesteRepository->fetchById($id);
        // print_r($detail);die;
        //$fileDetails = $this->repository->fetchAttachmentsById($id);
        $model = new TravelRequestModel();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);

        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);

        
        
        if($linkedId == null){
            $AllDomesticExpenseDtlList = $expenseRepo->fetchDomesticById($id);
            $AllInternationalExpenseDtlList = $expenseRepo->fetchInternationalById($id);
        }else{
            $AllDomesticExpenseDtlList = $expenseRepo->fetchDomesticById($id);
            $AllInternationalExpenseDtlList = $expenseRepo->fetchInternationalById($id);
        }
        
        
        $domesticExpenseDtlList = [];
        $internationalExpenseDtlList = [];
        $totalDomesticAmount = 0;
        $totalInternationalAmount = 0;
        foreach ($AllDomesticExpenseDtlList as $row) {

            if ($row['HALF_DAY_FLAG']=='Y'){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            }
            $totalDomesticAmount += $row['TOTAL'];
            array_push($domesticExpenseDtlList, $row);
        }
        foreach ($AllInternationalExpenseDtlList as $row) {
            // print_r($row);die;
            if ($row['HALF_DAY_FLAG']=='Y'){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            }
            $totalInternationalAmount += ($row['TOTAL']*$row['EXCHANGE_RATE']);
            array_push($internationalExpenseDtlList, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'recommender' => $detail['NAME_RECOMMENDER'] == null ? ($detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME']) : $detail['NAME_RECOMMENDER'],
                    'approver' => $detail['NAME_APPROVER'] == null ? ($detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME']) : $detail['NAME_APPROVER'],
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
                    'id' => $id,
                    'domesticExpenseDtlList' => $domesticExpenseDtlList,
                    'totalDomesticAmount' => $totalDomesticAmount,
                    'internationalExpenseDtlList' => $internationalExpenseDtlList,
                    'totalInternationalAmount' => $totalInternationalAmount,
                    'transportTypes' => $transportTypes,
                    'internationalPlaces' => $internationalPlaces,
                    'transportList' => $transportList,
                        //'files' => $fileDetails
        ]);
    }

    public function cancelAction() {
        
        $request = $this->getRequest();
        if ($request->isPost()) {
           
            try {
                $data = (array) $request->getPost();
                
                $data['employeeId'] = $this->employeeId;
                $data['requestedType'] = 'ad';
                
                $rawList = $this->repository->getFilteredRecords($data);

                

               
                $list = iterator_to_array($rawList, false);
               
                if($this->preference['displayHrApproved'] == 'Y'){
                    for($i = 0; $i < count($list); $i++){
                        if($list[$i]['HARDCOPY_SIGNED_FLAG'] == 'Y'){
                            $list[$i]['APPROVER_ID'] = '-1';
                            $list[$i]['APPROVER_NAME'] = 'HR';
                            $list[$i]['RECOMMENDER_ID'] = '-1';
                            $list[$i]['RECOMMENDER_NAME'] = 'HR';
                        }
                    }
                }

                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

    public function fileUploadAction()
    {
        $request = $this->getRequest();
        $responseData = [];
        $files = $request->getFiles()->toArray();
        try {
            if (sizeof($files) > 0) {
                $ext =  strtolower(pathinfo($files['file']['name'], PATHINFO_EXTENSION));
                if ($ext == 'txt' || $ext == 'pdf' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext=='docx' || $ext=='odt' || $ext=='doc' ) {
                    $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                    $unique = Helper::generateUniqueName();
                    $newFileName = $unique . "." . $ext;
                    $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/transfer_settlement_documents/" . $newFileName);
                    $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
                } else { 
                    throw new Exception("Upload unsuccessful.");
                    //$this->flashmessenger()->addMessage("Employee Successfully Deleted!!!");
                    // echo '<script>alert("Welcome to Geeks for Geeks")</script>';
                    // echo ("<script type='text/javascript'>alert('We welcome the New World');</script>");
                    // echo ('<script language="javascript">alert("hello")</script>');
                    ///============================== 
                }
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine(),
            ];
        }
        return new JsonModel($responseData);
    }

    public function validateSettlementExpenseAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $error = '';
                $postedData = $request->getPost();

                $allowSelfExpense = $this->repository->checkAllowSelf($postedData['jobHistoryId']);
                // print_r($allowSelfExpense);die;

                $dateCounts = array_count_values($postedData['dates']);
                $isFamilyCounts = array_count_values($postedData['isFamily']);
                if($allowSelfExpense == 'Y'){
                    if($isFamilyCounts['N'] > 1){
                        $error = 'Can not claim expense for self twice';
                    }else if(!$isFamilyCounts['N']){
                        $error = 'You compulsorily need to add expense for self';
                    }else{
                        foreach ($dateCounts as $key=>$count){
                            for($i=0; $i<count($postedData['dates']);$i++){
                                if($postedData['dates'][$i] == $key){
                                    if($postedData['isFamily'][$i] == 'N'){
                                        $count = $count - 1;
                                    }
                                }
                            }
                            if ($count > 3){
                                $error = 'Can not claim expense of more than 3 families in same date';
                            }
                        }
                    }
                }else{
                    if($isFamilyCounts['N']){
                        $error = 'Can not apply for self again';
                    }else{
                        foreach ($dateCounts as $key=>$count){
                            for($i=0; $i<count($postedData['dates']);$i++){
                                if($postedData['dates'][$i] == $key){
                                    if($postedData['isFamily'][$i] == 'N'){
                                        $count = $count - 1;
                                    }
                                }
                            }
                            if ($count > 3){
                                $error = 'Can not claim expense of more than 3 families in same date';
                            }
                        }
                    }
                }
                // print_r($error);die;
                // print_r($isFamilyCounts);print_r($dateCounts);die;

                // $error = $this->repository->validateTravelRequest($postedData['startDate'], $postedData['endDate'], $postedData['employeeId']);
                // print_r($error);die;
                return new CustomViewModel(['success' => true, 'data' => $error, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
    

}
