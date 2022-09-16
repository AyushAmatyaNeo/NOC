<?php

namespace Travel\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Exception;
use ManagerService\Repository\TravelApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelRequest;
use SelfService\Model\TravelRequest as TravelRequestModel;
use SelfService\Repository\TravelExpenseDtlRepository;
use SelfService\Repository\TravelRequestRepository;
use Travel\Repository\TravelItnaryRepository;
use Travel\Repository\TravelStatusRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use SelfService\Repository\TravelExpensesRepository;
use SelfService\Model\TravelExpenses as TravelExpensesModel;

class TravelStatus extends HrisController {

    private $travelApproveRepository;
    private $travelStatusRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeForm(TravelRequestForm::class);
        $this->travelApproveRepository = new TravelApproveRepository($adapter);
        $this->travelStatusRepository = new TravelStatusRepository($adapter);
        $this->travelRequestRepository = new TravelRequestRepository($adapter);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $search = $request->getPost();
                $list = $this->travelStatusRepository->getFilteredRecord($search);
                for($i=0; $i<count($list); $i++){
                    $transportTypeListArray = explode (",", $list[$i]['TRANSPORT_TYPE_LIST']);
                    
                    $transportTypeDetailListArray = [];
                    foreach ($transportTypeListArray as $transportTypeDetail){
                        $tempTransportTypeDetail = $this->travelStatusRepository->getTravelTypeDetail($transportTypeDetail);
                        array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
                    }
                    $list[$i]['TRANSPORT_TYPE_LIST'] = implode(', ',$transportTypeDetailListArray);
                }

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
        
        $statusSE = $this->getStatusSelectElement(['name' => 'status', "id" => "status", "class" => "form-control reset-field", 'label' => 'status']);
        return Helper::addFlashMessagesToArray($this, [
                    'travelStatus' => $statusSE,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'preference' => $this->preference,
//                    'itnaryCodeList'=>[], 
                    'itnaryCodeList' =>EntityHelper::getTableList($this->adapter, 'HRIS_TRAVEL_ITNARY', ['ITNARY_ID','ITNARY_CODE'], ['STATUS' => "E"]),
        ]);
    }

    public function actionAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $travelRequest = new TravelRequest();
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;
            $reqAmnt = $this->travelApproveRepository->fetchById($id)['REQUESTED_AMOUNT'];
            $travelRequest->fromDate = Helper::getExpressionDate($this->travelApproveRepository->fetchById($id)['FROM_DATE']);
            $travelRequest->toDate = Helper::getExpressionDate($this->travelApproveRepository->fetchById($id)['TO_DATE']);
            $travelRequest->employeeId = $this->travelApproveRepository->fetchById($id)['EMPLOYEE_ID'];
            $travelRequest->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $travelRequest->status = "R";
                $this->flashmessenger()->addMessage("Travel Request Rejected!!!");
            } else if ($action == "Approve") {
                $travelRequest->status = "AP";
                $travelRequest->advanceAmount = $reqAmnt?$reqAmnt:0;
                $this->flashmessenger()->addMessage("Travel Request Approved");
            }
            $travelRequest->approvedBy = $this->employeeId;
            $travelRequest->approvedRemarks = $reason;
            // print_r($travelRequest);die;
            $this->travelApproveRepository->linkTravelWithFiles($id);
            $this->travelApproveRepository->edit($travelRequest, $id);

            return $this->redirect()->toRoute("travelStatus");
        }
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }
        $travelRequestModel = new TravelRequest();
        $detail = $this->travelApproveRepository->fetchById($id);
        $detail['TRANSPORT_TYPE_LIST'] = explode (",", $detail['TRANSPORT_TYPE_LIST']);
        
        for($i=0; $i<count($detail['TRANSPORT_TYPE_LIST']); $i++){
            $transportTypeDetailListArray = [];
            foreach ($detail['TRANSPORT_TYPE_LIST'] as $transportTypeDetail){
                $tempTransportTypeDetail = $this->travelApproveRepository->getTravelTypeDetail($transportTypeDetail);
                array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
            }
            $detail['TRANSPORT_TYPE_LIST_DETAIL'] = $transportTypeDetailListArray;
        }
        $detail['TRANSPORT_TYPE_LIST_DETAIL_STR']=implode(', ',$detail['TRANSPORT_TYPE_LIST_DETAIL']);
        $issueNum = $this->travelApproveRepository->getIssueNum($id,$detail['EMPLOYEE_ID']);
        // print_r($issueNum['0']['ROW_NUM']);die;
        if($this->preference['displayHrApproved'] == 'Y' && $detail['HARDCOPY_SIGNED_FLAG'] == 'Y'){
            $detail['APPROVER_ID'] = '-1';
            $detail['APPROVER_NAME'] = 'HR';
            $detail['RECOMMENDER_ID'] = '-1';
            $detail['RECOMMENDER_NAME'] = 'HR';
            $detail['RECOMMENDED_BY_NAME'] = 'HR';
            $detail['APPROVED_BY_NAME'] = 'HR';
        }
        //$fileDetails = $this->travelApproveRepository->fetchAttachmentsById($id);
        $travelRequestModel->exchangeArrayFromDB($detail);
        $this->form->bind($travelRequestModel);
        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        
         $travelItnaryDet = [];
        $travelItnaryMemDet = [];

        $transportTypes = array(
            'AP' => 'Aeroplane',
            'OV' => 'Office Vehicles',
            'TI' => 'Taxi',
            'BS' => 'Bus',
            'OF'  => 'On Foot'
        );
        $detail['APPROVER_NAME']=$this->travelApproveRepository->getAlternateApproverName($this->employeeId)[0]['NAME'];
        $detail['RECOMMENDER_NAME']=$this->travelApproveRepository->getAlternateRecommenderName($this->employeeId)[0]['NAME'];
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'form' => $this->form,
                    'recommender' => $detail['NAME_RECOMMENDER'] == null ? ($detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME']) : $detail['NAME_RECOMMENDER'],
                    'approver' => $detail['NAME_APPROVER'] == null ? ($detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME']) : $detail['NAME_APPROVER'],
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
                    'itnaryId' => null,
                    'travelItnaryDet' => $travelItnaryDet,
                    'travelItnaryMemDet' => $travelItnaryMemDet,
                    'acl' => $this->acl,
                    'issueNum' => $issueNum['0']['ROW_NUM'],
                    'transportTypes' => $transportTypes,
                    'status' => $detail['STATUS']
                        //'files' => $fileDetails
        ]);
    }

    public function editAction() {
        $request = $this->getRequest();

        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }
        if ($this->travelRequestRepository->checkAllowEdit($id) == 'N') {
            return $this->redirect()->toRoute("travelRequest");
        }

        if ($request->isPost()) {
            $travelRequest = new TravelRequestModel();
            $postedData = $request->getPost();
            $this->form->setData($postedData);

            if ($this->form->isValid()) {
                $travelRequest->exchangeArrayFromForm($this->form->getData());
                $travelRequest->modifiedDt = Helper::getcurrentExpressionDate();
                $travelRequest->employeeId = $this->employeeId;
                $travelRequest->fromDate = Helper::getExpressionDate($travelRequest->fromDate);
                $travelRequest->toDate = Helper::getExpressionDate($travelRequest->toDate);
                $travelRequest->transportTypeList = implode(',',$travelRequest->transportTypeList);
                $this->travelRequestRepository->edit($travelRequest, $id);
                $this->flashmessenger()->addMessage("Travel Request Successfully Edited!!!");
                return $this->redirect()->toRoute("travelApply");
            }
        }

        $detail = $this->travelRequestRepository->fetchById($id);
        $detail['TRANSPORT_TYPE_LIST'] = explode (",", $detail['TRANSPORT_TYPE_LIST']);
        
        for($i=0; $i<count($detail['TRANSPORT_TYPE_LIST']); $i++){
            $transportTypeDetailListArray = [];
            foreach ($detail['TRANSPORT_TYPE_LIST'] as $transportTypeDetail){
                $tempTransportTypeDetail = $this->travelApproveRepository->getTravelTypeDetail($transportTypeDetail);
                array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
            }
            $detail['TRANSPORT_TYPE_LIST_DETAIL'] = $transportTypeDetailListArray;
        }
        //$fileDetails = $this->repository->fetchAttachmentsById($id);
        $model = new TravelRequestModel();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);

        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);

        $transportTypes = array(
            'AP' => 'Aeroplane',
            'OV' => 'Office Vehicles',
            'TI' => 'Taxi',
            'BS' => 'Bus',
            'OF'  => 'On Foot'
        );

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'recommender' => $detail['NAME_RECOMMENDER'] == null ? ($detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME']) :$detail['NAME_RECOMMENDER'],
                    'approver' => $detail['NAME_APPROVER'] == null ? ($detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME']) : $detail['NAME_APPROVER'],
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
                    'transportTypes' => $transportTypes,
                        //'files' => $fileDetails
        ]);
    }

    public function expenseDetailAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
        $detail = $this->travelApproveRepository->fetchById($id);

        $authRecommender = $detail['NAME_RECOMMENDER'] == null ? ($detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME']) : $detail['NAME_RECOMMENDER'];
        $authApprover = $detail['NAME_APPROVER'] == null ? ($detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME']) : $detail['NAME_APPROVER'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];


        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $result = $expenseDtlRepo->fetchByTravelId($id);
        $expenseDtlList = [];
        $totalAmount = 0;
        foreach ($result as $row) {
            $totalAmount += $row['TOTAL_AMOUNT'];
            array_push($expenseDtlList, $row);
        }
        $transportType = [
            "AP" => "Aeroplane",
            "OV" => "Office Vehicles",
            "TI" => "Taxi",
            "BS" => "Bus",
            "OF"  => "On Foot"
        ];
        $numberInWord = new NumberHelper();
        $totalAmountInWords = $numberInWord->toText($totalAmount);
        $balance = $detail['REQUESTED_AMOUNT'] - $totalAmount;

        $expenseRepo = new TravelExpensesRepository($this->adapter);
        
        $linkedId = $expenseRepo->getLinkedId($id);
        $AllDomesticExpenseDtlList = $expenseRepo->fetchDomesticById($id);
        $AllInOrder = $expenseRepo->fetchAllInOrder($id);
        $AllInternationalExpenseDtlList = $expenseRepo->fetchInternationalById($id);
        $domesticExpenseDtlList = [];
        $internationalExpenseDtlList = [];
        $totalDomesticAmount = 0;
        $allExpenseInOrder = [];
        $totalInternationalAmount = 0;
        foreach ($AllInOrder as $row) {
            if ($row['HALF_DAY_FLAG']=='Y'){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            }
            array_push($allExpenseInOrder, $row);
        }
        foreach ($AllDomesticExpenseDtlList as $row) {
            if ($row['HALF_DAY_FLAG']=='Y'){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            }
            $totalDomesticAmount += $row['TOTAL'];
            array_push($domesticExpenseDtlList, $row);
        }
        foreach ($AllInternationalExpenseDtlList as $row) {
            if ($row['HALF_DAY_FLAG']=='Y'){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            }
            $totalInternationalAmount += ($row['TOTAL']*$row['EXCHANGE_RATE']);
            array_push($internationalExpenseDtlList, $row);
        }
        // print_r($linkedId);die;
        $advanceForTravel = $this->travelApproveRepository->getValueAdvanceForTravel($id);
        $totalNoOfAttachment = $this->travelApproveRepository->getTotalNoOfAttachment($id);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'role' => $role,
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'recommendedBy' => $recommenderId,
                    'employeeId' => $this->employeeId,
                    'expenseDtlList' => $expenseDtlList,
                    'transportType' => $transportType,
                    'todayDate' => date('d-M-Y'),
                    'detail' => $detail,
                    'totalAmount' => $totalAmount,
                    'totalAmountInWords' => $totalAmountInWords,
                    'balance' => $balance,
                    'domesticExpenseDtlList' => $domesticExpenseDtlList,
                    'totalDomesticAmount' => $totalDomesticAmount,
                    'internationalExpenseDtlList' => $internationalExpenseDtlList,
                    'totalInternationalAmount' => $totalInternationalAmount,
                    'status' => $detail['STATUS'],
                    'advaceForTravel' => $advanceForTravel[0]['REQUESTED_AMOUNT'],
                    'totalAttachment' => $totalNoOfAttachment[0]['COUNT(*)'],
                    'allInOrder' => $allExpenseInOrder,
                        ]
        );
    }

    public function settlementReportAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $list = $this->travelStatusRepository->notSettled();
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return [];
    }

    public function bulkAction() {
        $request = $this->getRequest();
        try {
            $postData = $request->getPost();
            if ($postData['super_power'] == 'true') {
                $this->makeSuperDecision($postData['id'], $postData['action'] == "approve");
            } else {
                $this->makeDecision($postData['id'], $postData['action'] == "approve");
            }
            return new JsonModel(['success' => true, 'data' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function makeDecision($id, $approve, $remarks = null, $enableFlashNotification = false) {

        $detail = $this->travelApproveRepository->fetchById($id);

        if ($detail['STATUS'] == 'RQ' || $detail['STATUS'] == 'RC') {
            $model = new TravelRequest();
            $model->travelId = $id;
            $model->recommendedDate = Helper::getcurrentExpressionDate();
            $model->recommendedBy = $this->employeeId;
            $model->approvedRemarks = $remarks;
            $model->approvedDate = Helper::getcurrentExpressionDate();
            $model->approvedBy = $this->employeeId;
            $model->status = $approve ? "AP" : "R";
            $message = $approve ? "Travel Request Approved" : "Travel Request Rejected";
            $notificationEvent = $approve ? NotificationEvents::TRAVEL_APPROVE_ACCEPTED : NotificationEvents::TRAVEL_APPROVE_REJECTED;
            $this->travelApproveRepository->edit($model, $id);
            if ($enableFlashNotification) {
                $this->flashmessenger()->addMessage($message);
            }
            try {
                HeadNotification::pushNotification($notificationEvent, $model, $this->adapter, $this);
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
    }

    private function makeSuperDecision($id, $approve, $remarks = null, $enableFlashNotification = false) {

        $detail = $this->travelApproveRepository->fetchById($id);

        if ($detail['STATUS'] == 'AP') {
            $model = new TravelRequest();
            $model->travelId = $id;
            $model->recommendedDate = Helper::getcurrentExpressionDate();
            $model->recommendedBy = $this->employeeId;
            $model->approvedRemarks = $remarks;
            $model->approvedDate = Helper::getcurrentExpressionDate();
            $model->approvedBy = $this->employeeId;
            $model->status = $approve ? "AP" : "R";
            $message = $approve ? "Travel Request Approved" : "Travel Request Rejected";
            $notificationEvent = $approve ? NotificationEvents::TRAVEL_APPROVE_ACCEPTED : NotificationEvents::TRAVEL_APPROVE_REJECTED;
            $this->travelApproveRepository->edit($model, $id);
            if ($enableFlashNotification) {
                $this->flashmessenger()->addMessage($message);
            }
            try {
                HeadNotification::pushNotification($notificationEvent, $model, $this->adapter, $this);
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
    }

    public function expenseClaimReportAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $list = $this->travelStatusRepository->expenseClaim();
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return [];
    }

    public function expenseViewAction() {
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }

        $detail = $this->travelRequestRepository->fetchById($id);
        $model = new TravelRequestModel();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);

        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $expenseDtlList = [];
        $result = $expenseDtlRepo->fetchByTravelId($id);
        $totalAmount = 0;
        foreach ($result as $row) {
            $totalAmount += $row['TOTAL_AMOUNT'];
            array_push($expenseDtlList, $row);
        }
        $balance = $detail['REQUESTED_AMOUNT'] - $totalAmount;

        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        $totalExpenseInWords = $numberInWord->toText($totalAmount);

        $expenseRepo = new TravelExpensesRepository($this->adapter);
        
        $linkedId = $expenseRepo->getLinkedId($id);
        $AllDomesticExpenseDtlList = $expenseRepo->fetchDomesticById($id);
        $AllInternationalExpenseDtlList = $expenseRepo->fetchInternationalById($id);
        $domesticExpenseDtlList = [];
        $internationalExpenseDtlList = [];
        $totalDomesticAmount = 0;
        $totalInternationalAmount = 0;
        foreach ($AllDomesticExpenseDtlList as $row) {
            $totalDomesticAmount += $row['TOTAL'];
            array_push($domesticExpenseDtlList, $row);
        }
        foreach ($AllInternationalExpenseDtlList as $row) {
            $totalInternationalAmount += ($row['TOTAL']*$row['EXCHANGE_RATE']);
            array_push($internationalExpenseDtlList, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'recommender' => $detail['NAME_RECOMMENDER'] == null ? ($detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME']) : $detail['NAME_RECOMMENDER'],
                    'approver' => $detail['NAME_APPROVER'] == null ? ($detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME']) : $detail['NAME_APPROVER'],
                    'detail' => $detail,
                    'expenseDtlList' => $expenseDtlList,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
                    'totalExpenseInWords' => $totalExpenseInWords,
                    'totalExpense' => $totalAmount,
                    'balance' => $balance,
                    'id' => $id,
                    'domesticExpenseDtlList' => $domesticExpenseDtlList,
                    'totalDomesticAmount' => $totalDomesticAmount,
                    'internationalExpenseDtlList' => $internationalExpenseDtlList,
                    'totalInternationalAmount' => $totalInternationalAmount
        ]);
    }

    public function expenseEditAction() {
        $request = $this->getRequest();
        $model = new TravelExpensesModel();
        $repo = new TravelExpensesRepository($this->adapter);
        

        $transportTypes = ['WALKING'=>'Walking', 'TRAVEL' => 'Travel'];
        $internationalPlaces = ['LISTED CITIES'=>'Listed Cities','OTHER INDIA CITIES'=>'Other India Cities','OTHER COUNTRIES'=>'Other Countries'];
       
        // print_r($transportTypes);die;

        $id = (int) $this->params()->fromRoute('id');
        $expenseRepo = new TravelExpensesRepository($this->adapter);
        $linkedId = $expenseRepo->getLinkedId($id);
        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }
        if ($this->travelRequestRepository->checkAllowEdit($id) == 'N') {
            return $this->redirect()->toRoute("travelRequest");
        }

        if ($request->isPost()) {
            $travelRequest = new TravelRequestModel();
            $postData = $request->getPost();
            // print_r($postedData['loctoInternational']);die;
            $this->form->setData($postData);
            
            if ($this->form->isValid()) {
                // print_r($postData['depDateInternational']);die;
                $repo->deletePreviousData($id);
                if ($postData['depDate'][0] != null){
                // print_r($postData['depDate']);die;
                    for ($i = 0; $i < count($postData['depDate']); $i++){
                        if($postData['mot'][$i] == "WALKING"){
                            $unit = $postData['kmWalked'][$i];
                        }else{
                            $unit = $postData['noOfDays'][$i];
                        }
                        $classId = $this->travelRequestRepository->getClassIdFromEmpId($this->employeeId);
                        $configId = $this->travelRequestRepository->getCongifId("DOMESTIC", $postData['mot'][$i], $classId);
                        $rate = $this->travelRequestRepository->getRateFromConfigId($configId);
                        $total = (int)$unit * (int)$rate;
                        $model->travelExpenseId = ((int) Helper::getMaxId($this->adapter, "hris_travel_expense", "TRAVEL_EXPENSE_ID")) + 1;
                        $model->travelId= $id;
                        $model->configId= $configId;
                        $model->amount= $total;
                        
                        $model->other_expense= (int)$postData['otherExpenses'][$i];
                        $model->total= (int)$total+(int)$postData['otherExpenses'][$i];
                        $model->exchangeRate=1;
                        $model->expenseDate=Helper::getcurrentExpressionDate();
                        $model->status='E';
                        $model->remarks=$postData['detRemarks'][$i];
                        
                        $model->createdDt=Helper::getcurrentExpressionDate();
                        $model->departure_DT = Helper::getExpressionDate($postData['depDate'][$i]);
                        $model->departure_Place = $postData['locFrom'][$i];
                        $model->arraival_DT = Helper::getExpressionDate($postData['arrDate'][$i]);
                        
                        $model->arraival_place = $postData['locto'][$i];
                        // print_r($model->departure_Dt);die;
                        
                         
                        $repo->add($model);
                        
                    }
                }
                
                if ($postData['depDateInternational'][0] != null){
                   
                    for ($j = 0; $j < count($postData['depDateInternational']); $j++){
                        $unit = $postData['noOfDaysInternational'][$j];
                        $classId = $this->travelRequestRepository->getClassIdFromEmpId($this->employeeId);
                        $configId = $this->travelRequestRepository->getCongifId("INTERNATIONAL", $postData['motInternational'][$j], $classId);
                        $rate = $this->travelRequestRepository->getRateFromConfigId($configId);
                        $total = $unit * $rate;
                        $model->travelExpenseId = ((int) Helper::getMaxId($this->adapter, "hris_travel_expense", "TRAVEL_EXPENSE_ID")) + 1;
                        $model->travelId= $id;
                        $model->configId= $configId;
                        $model->amount= $total;
                        $model->other_expense= $postData['otherExpensesInternational'][$j];
                        $model->total= (int)$total+(int)$postData['otherExpensesInternational'][$j];
                        $model->exchangeRate=$postData['exchangeRateInternational'][$j];
                        $model->expenseDate=Helper::getcurrentExpressionDate();
                        $model->status='E';
                        $model->remarks=$postData['detRemarksInternational'][$j];
                        $model->createdDt=Helper::getcurrentExpressionDate();
                        $model->departure_DT = Helper::getExpressionDate($postData['depDateInternational'][$j]);
                        $model->departure_Place = $postData['locFromInternational'][$j];
                        $model->arraival_DT = Helper::getExpressionDate($postData['arrDateInternational'][$j]);
                        $model->arraival_place = $postData['loctoInternational'][$j];
                        // print_r($postData['total'][$i]+$postData['otherExpenses'][$i]);die;
                        $repo->add($model);
                    }
                }
                
                $travelRequest->exchangeArrayFromForm($this->form->getData());
                $travelRequest->modifiedDt = Helper::getcurrentExpressionDate();
                $travelRequest->fromDate = Helper::getExpressionDate($travelRequest->fromDate);
                $travelRequest->toDate = Helper::getExpressionDate($travelRequest->toDate);
                $travelRequest->recommendedDate = Helper::getExpressionDate($travelRequest->recommendedDate);
                $travelRequest->departureDate = Helper::getExpressionDate($travelRequest->departureDate);
                $travelRequest->returnedDate = Helper::getExpressionDate($travelRequest->returnedDate);
                $travelRequest->approvedDate = Helper::getExpressionDate($travelRequest->approvedDate);
                $travelRequest->employeeId = $this->employeeId;               
                $travelRequest->requestedAmount = $this->travelRequestRepository->getTotalExpenseAmount($id);
                // echo"<pre>";print_r($travelRequest->requestedAmount);die;
                $this->travelRequestRepository->deletePreviouseLinkFiles($id);
                $this->travelRequestRepository->linkTravelWithFiles($id);
                $this->travelRequestRepository->edit($travelRequest, $id);
                
                $this->flashmessenger()->addMessage("Travel Request Successfully Edited!!!");
                return $this->redirect()->toRoute("travelStatus", array(
                'action' => 'expenseClaimReport'));
            }
        }

        $detail = $this->travelRequestRepository->fetchById($id);
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
            $totalDomesticAmount += $row['TOTAL'];
            
            array_push($domesticExpenseDtlList, $row);
        }
        // print_r($AllDomesticExpenseDtlList);die;
        foreach ($AllInternationalExpenseDtlList as $row) {
            $totalInternationalAmount += $row['TOTAL'];
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
                    'internationalPlaces' => $internationalPlaces
                        //'files' => $fileDetails
        ]);
    }

    public function expenseDeleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        $repo = new TravelExpensesRepository($this->adapter);
        $expenseRepo = new TravelExpensesRepository($this->adapter);
        $linkedId = $expenseRepo->getLinkedId($id);
        if (!$id) {
            return $this->redirect()->toRoute("travelStatus", array(
                'action' => 'expenseClaimReport'));
        }
        $this->travelRequestRepository->delete($id);
        $repo->deletePreviousData($linkedId);
        $this->travelRequestRepository->deletePreviouseLinkFiles($id);
        $this->flashmessenger()->addMessage("Travel Expense Claim Successfully Cancelled!!!");
        return $this->redirect()->toRoute("travelStatus", array(
            'action' => 'expenseClaimReport'));
    }

    public function expenseApproveAction(){
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $travelRequest = new TravelRequest();
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;
            $travelRequest->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $travelRequest->status = "R";
                $this->flashmessenger()->addMessage("Travel Exoense Rejected!!!");
            } else if ($action == "Approve") {
                $travelRequest->status = "AP";
                $this->flashmessenger()->addMessage("Travel Expense Approved");
            }
            
            if ($travelRequest->status == 'AP') {
                $allDetails = $this->travelApproveRepository->fetchById($id);

                $updateModel = new TravelRequest();
                $advanceAmount = $this->travelApproveRepository->getAdvanceAmount($id);
                $travelMasterId = $this->travelApproveRepository->getMasterTravelId($id);
                $updateModel->expenseAmount = (float)$allDetails['REQUESTED_AMOUNT'] + (float)$advanceAmount;
                $updateModel->itnaryId = $id;
                if($allDetails['REQUESTED_AMOUNT']<0){
                    $updateModel->adjustmentFlag = 'D';
                    $updateModel->adjustmentAmount =  (float)$allDetails['REQUESTED_AMOUNT'] * (-1);
                }else if ($advanceAmount == null || $advanceAmount == 0){
                    $updateModel->adjustmentFlag = 'N';
                    $updateModel->adjustmentAmount = null;
                }else{
                    $updateModel->adjustmentFlag = 'A';
                    $updateModel->adjustmentAmount =  (float)$allDetails['REQUESTED_AMOUNT'];
                }
                $this->travelApproveRepository->updateForVoucherImpact($updateModel, $travelMasterId);
            }

            $travelRequest->approvedBy = $this->employeeId;
            $travelRequest->approvedRemarks = $reason;
            // print_r($travelRequest);die;
            // print_r($travelRequest);die;
            $this->travelApproveRepository->linkTravelWithFiles($id);
            $this->travelApproveRepository->edit($travelRequest, $id);

            return $this->redirect()->toRoute("travelStatus");
        }
    }

    public function financialImpact($travelModel){
        $updateModel = new TravelRequest();
        $advanceAmount = $this->travelApproveRepository->getAdvanceAmount($travelModel->travelId);
        $updateModel->expenseAmount = (float)$travelModel->requestedAmount + (float)$advanceAmount;
        $updateModel->adjustmentAmount = (float)$travelModel->requestedAmount;
        if($travelModel->requestedAmount<0){
            $updateModel->adjustmentFlag = 'D';
        }else{
            $updateModel->adjustmentFlag = 'A';
        }
        $this->travelApproveRepository->updateForVoucherImpact($updateModel, $travelModel->travelId);
    }

}