<?php

namespace TransferSettlement\Controller;

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
use SelfService\Model\TransferSettlement as TransferSettlementModel;
use SelfService\Repository\TravelExpenseDtlRepository;
use SelfService\Repository\TravelRequestRepository;
use Travel\Repository\TravelItnaryRepository;
use TransferSettlement\Repository\TransferSettlementStatusRepository;
use ManagerService\Repository\TransferSettlementApproveRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use SelfService\Repository\TravelExpensesRepository;
use SelfService\Model\TravelExpenses as TravelExpensesModel;

class TransferSettlementStatus extends HrisController {

    private $travelApproveRepository;
    private $travelStatusRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeForm(TravelRequestForm::class);
        $this->travelApproveRepository = new TravelApproveRepository($adapter);
        $this->transferSettlementStatusRepo = new TransferSettlementApproveRepository($adapter);
        $this->travelRequestRepository = new TravelRequestRepository($adapter);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $searchQuery = $request->getPost();
                $searchQuery['employees']= implode(",", $searchQuery['employees']);
                $search['employeeId'] = $this->employeeId;
                $search['status'] = ['RQ', 'RC'];
                $rawList = $this->transferSettlementStatusRepo->getStatusList((array) $searchQuery);
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
            'travelStatus' => $statusSE,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
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

            $travelRequest->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $travelRequest->status = "R";
                $this->flashmessenger()->addMessage("Travel Request Rejected!!!");
            } else if ($action == "Approve") {
                $travelRequest->status = "AP";
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
        $detail = $this->transferSettlementStatusRepo->fetchById($id);
        $detail['TRANSPORT_TYPE_LIST'] = explode (",", $detail['TRANSPORT_TYPE_LIST']);
        
        for($i=0; $i<count($detail['TRANSPORT_TYPE_LIST']); $i++){
            $transportTypeDetailListArray = [];
            foreach ($detail['TRANSPORT_TYPE_LIST'] as $transportTypeDetail){
                $tempTransportTypeDetail = $this->transferSettlementStatusRepo->getTravelTypeDetail($transportTypeDetail);
                array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
            }
            $detail['TRANSPORT_TYPE_LIST_DETAIL'] = $transportTypeDetailListArray;
        }
        $detail['TRANSPORT_TYPE_LIST_DETAIL_STR']=implode(', ',$detail['TRANSPORT_TYPE_LIST_DETAIL']);
        $issueNum = $this->transferSettlementStatusRepo->getIssueNum($id,$detail['EMPLOYEE_ID']);
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
        if ($detail['ITNARY_ID']) {
            $travelItnaryRepo = new TravelItnaryRepository($this->adapter);
            $travelItnaryDet = $travelItnaryRepo->fetchItnaryDetails($detail['ITNARY_ID']);
            $travelItnaryMemDet = $travelItnaryRepo->fetchItnaryMembers($detail['ITNARY_ID']);
        }
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
                    'itnaryId' => $detail['ITNARY_ID'],
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
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("transferSettlementStatus");
        }

        $serialNumber = (int) $this->params()->fromRoute('serialNumber');

        if ($serialNumber === 0) {
            return $this->redirect()->toRoute("transferSettlementStatus");
        }

        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $expenseDtlList = [];
        $result = $this->transferSettlementStatusRepo->fetchByJobHistoryId($id, $serialNumber);
        $allExpenseDetail = [];
        $totalAmount = 0;
        $grandTotal = 0;
        foreach ($result as $row) {
        // print_r($row);die;
            $row['TOTAL'] = $row['TOTAL_TADA_AMT'] + $row['PLANE_EXPENSE_AP_AMT'] + $row['VEHICLE_EXPENSE_AP_AMT'] + $row['MISC_EXPENSE_AP_AMT'];
            $grandTotal = $grandTotal + $row['TOTAL'];
            array_push($allExpenseDetail, $row);
        }

        $request = $this->getRequest();
        $model = new TravelExpensesModel();
        $transferSettlementModel = new TransferSettlementModel();
        $repo = new TravelExpensesRepository($this->adapter);

        $detail = $this->transferSettlementStatusRepo->getTransferDetails($id);
        // print_r($allExpenseDetail);die;
        if ($request->isPost()) {
            // $this->flashmessenger()->addMessage("Successfully Added!!!");
            //     return;
            $jobHistoryId = (int) $this->params()->fromRoute('id');
            $postData = $request->getPost()->getArrayCopy();
            // print_r($postData);die;
            // echo('<pre>');print_r($jobHistoryId);die;

            if ($postData['depDate'][0] != null){
                for ($i = 0; $i < count($postData['depDate']); $i++){

                    $transferSettlementModel->weightApAmt = $postData['priceOfGoods'][0]?$postData['priceOfGoods'][0]:0;
                    $transferSettlementModel->vehicleExpenseApAmt = $postData['rate1'][$i]?$postData['rate1'][$i]:0;
                    $transferSettlementModel->planeExpenseApAmt = $postData['rate2'][$i]?$postData['rate2'][$i]:0;
                    $transferSettlementModel->miscExpenseApAmt = $postData['otherExpenses'][$i]?$postData['otherExpenses'][$i]:0;
                    $transferSettlementModel->modifiedDt = Helper::getcurrentExpressionDate();
                    $transferSettlementModel->modifiedBy = $this->employeeId;
                    $this->transferSettlementStatusRepo->edit($transferSettlementModel, $postData['transferSettlementId'][$i]);
                }
            }
            $error = "";

            $this->flashmessenger()->addMessage("Successfully Edited!!!");
                return $this->redirect()->toRoute("transferSettlementStatus");
        }


        // print_r($allExpenseDetail);die;
        // print_r($detail);die;
        // $recommenderApproverList = $this->repository->getRecommenderApproverList($this->employeeId);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail,
                    'id' => $id,
                    'recommenderApproverList' => $recommenderApproverList,
                    'allDetails' => $allExpenseDetail,
                    'grandTotal' => $grandTotal,
                    'serialNumber' => $serialNumber,
        ]);
    }

    public function expenseDetailAction() {
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("transferSettlementStatus");
        }
        $serialNumber = (int) $this->params()->fromRoute('serialNumber');

        if ($serialNumber === 0) {
            return $this->redirect()->toRoute("transferSettlementStatus");
        }
        $detail = $this->transferSettlementStatusRepo->getTransferDetails($id);


        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $expenseDtlList = [];
        $result = $this->transferSettlementStatusRepo->fetchByJobHistoryId($id, $serialNumber);
        $allExpenseDetail = [];
        $totalAmount = 0;
        $grandTotal = 0;
        foreach ($result as $row) {
            $row['TOTAL'] = $row['TOTAL_TADA_AMT'] + $row['PLANE_EXPENSE_AP_AMT'] + $row['VEHICLE_EXPENSE_AP_AMT'] + $row['MISC_EXPENSE_AP_AMT'];
            $grandTotal = $grandTotal + $row['TOTAL'];
            array_push($allExpenseDetail, $row);
        }
        // print_r($row);die;
        $balance = $detail['REQUESTED_AMOUNT'] - $totalAmount;

        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        $totalExpenseInWords = $numberInWord->toText($totalAmount);

        $expenseRepo = new TravelExpensesRepository($this->adapter);
        
        $linkedId = $expenseRepo->getLinkedId($id);
        $AllInOrder = $expenseRepo->fetchAllInOrder($id);
        $AllDomesticExpenseDtlList = $expenseRepo->fetchDomesticById($id);
        $AllInternationalExpenseDtlList = $expenseRepo->fetchInternationalById($id);
        $domesticExpenseDtlList = [];
        $internationalExpenseDtlList = [];
        $allExpenseInOrder = [];
        $totalDomesticAmount = 0;
        $totalInternationalAmount = 0;

        foreach ($AllInOrder as $row) {
            if ($row['HALF_DAY_FLAG']=='Y'){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            }
            array_push($allExpenseInOrder, $row);
        }

        foreach ($AllDomesticExpenseDtlList as $row) {
            // print_r(strtoupper($detail['RETURNED_DATE']));
            //  print_r($row);die;

            // if(strtoupper($detail['RETURNED_DATE'])==strtoupper($row['ARRAIVAL_DT'])){
            //     $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            // }
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
            // if(strtoupper($detail['RETURNED_DATE'])==strtoupper($row['ARRAIVAL_DT'])){
            //     $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            // }
            $totalInternationalAmount += ($row['TOTAL']*$row['EXCHANGE_RATE']);
            array_push($internationalExpenseDtlList, $row);
        }

        $totalNoOfAttachment = $this->transferSettlementStatusRepo->getTotalNoOfAttachment($id, $serialNumber);
        // print_r($allExpenseDetail);die;
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
                    'grandTotal' => $grandTotal,
                    'internationalExpenseDtlList' => $internationalExpenseDtlList,
                    'totalInternationalAmount' => $totalInternationalAmount,
                    'totalAttachment' => $totalNoOfAttachment[0]['COUNT(*)'],
                    'allInOrder' => $allExpenseInOrder,
                    'allDetails' => $allExpenseDetail,
                    'serialNumber' => $serialNumber,
                    'bankName' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_BANKS", "BANK_ID", ["BANK_NAME"], ["STATUS" => 'E'], "BANK_ID", "ASC", "-"),
        ]);
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
            $travelRequest->approvedBy = $this->employeeId;
            $travelRequest->approvedRemarks = $reason;
            // print_r($travelRequest);die;
            // print_r($travelRequest);die;
            $this->travelApproveRepository->linkTravelWithFiles($id);
            $this->travelApproveRepository->edit($travelRequest, $id);

            return $this->redirect()->toRoute("travelStatus");
        }
    }

    public function jvAddAction() {
        
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("transferSettlementStatus");
        }
        $serialNumber = (int) $this->params()->fromRoute('serialNumber');

        if ($serialNumber === 0) {
            return $this->redirect()->toRoute("transferSettlementStatus");
        }
        $detail = $this->transferSettlementStatusRepo->getTransferDetails($id);

        $bankList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_BANKS", "BANK_ID", ["BANK_NAME"], ["STATUS" => 'E'], "BANK_ID", "ASC", "-");
        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $expenseDtlList = [];
        $result = $this->transferSettlementStatusRepo->fetchByJobHistoryId($id, $serialNumber);
        $allExpenseDetail = [];
        $totalAmount = 0;
        $grandTotal = 0;
        foreach ($result as $row) {
            $row['TOTAL'] = $row['TOTAL_TADA_AMT'] + $row['PLANE_EXPENSE_AP_AMT'] + $row['VEHICLE_EXPENSE_AP_AMT'] + $row['MISC_EXPENSE_AP_AMT'];
            $grandTotal = $grandTotal + $row['TOTAL'];
            array_push($allExpenseDetail, $row);
        }
        // print_r($row);die;
        $balance = $detail['REQUESTED_AMOUNT'] - $totalAmount;

        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        $totalExpenseInWords = $numberInWord->toText($totalAmount);

        $expenseRepo = new TravelExpensesRepository($this->adapter);
        
        $linkedId = $expenseRepo->getLinkedId($id);
        $AllInOrder = $expenseRepo->fetchAllInOrder($id);
        $AllDomesticExpenseDtlList = $expenseRepo->fetchDomesticById($id);
        $AllInternationalExpenseDtlList = $expenseRepo->fetchInternationalById($id);
        $domesticExpenseDtlList = [];
        $internationalExpenseDtlList = [];
        $allExpenseInOrder = [];
        $totalDomesticAmount = 0;
        $totalInternationalAmount = 0;

        foreach ($AllInOrder as $row) {
            if ($row['HALF_DAY_FLAG']=='Y'){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            }
            array_push($allExpenseInOrder, $row);
        }

        foreach ($AllDomesticExpenseDtlList as $row) {
            // print_r(strtoupper($detail['RETURNED_DATE']));
            //  print_r($row);die;

            // if(strtoupper($detail['RETURNED_DATE'])==strtoupper($row['ARRAIVAL_DT'])){
            //     $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            // }
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
            // if(strtoupper($detail['RETURNED_DATE'])==strtoupper($row['ARRAIVAL_DT'])){
            //     $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            // }
            $totalInternationalAmount += ($row['TOTAL']*$row['EXCHANGE_RATE']);
            array_push($internationalExpenseDtlList, $row);
        }

        $totalNoOfAttachment = $this->transferSettlementStatusRepo->getTotalNoOfAttachment($id, $serialNumber);
        $request = $this->getRequest();
        $transferSettlementModel = new TransferSettlementModel();
        if ($request->isPost()) {

            $postedData = $request->getPost();
            for ($i = 0; $i < count($postedData['depDate']); $i++){
                $transferSettlementModel->jvNumber = $postedData['jvNumber'];
                $transferSettlementModel->chequeNumber = $postedData['chequeNumber'];
                $transferSettlementModel->bankId = $postedData['bankId'];
                $transferSettlementModel->checkedDt = Helper::getcurrentExpressionDate();
                $transferSettlementModel->checkedBy = $this->employeeId;
                $this->transferSettlementStatusRepo->edit($transferSettlementModel, $postedData['transferSettlementId'][$i]);
            }
            $this->flashmessenger()->addMessage("Successfully Added JV Number!!!");
            return $this->redirect()->toRoute("transferSettlementStatus");
            //print_r($postedData);die;
        } 
        // print_r($allExpenseDetail);die;
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
                    'grandTotal' => $grandTotal,
                    'internationalExpenseDtlList' => $internationalExpenseDtlList,
                    'totalInternationalAmount' => $totalInternationalAmount,
                    'totalAttachment' => $totalNoOfAttachment[0]['COUNT(*)'],
                    'allInOrder' => $allExpenseInOrder,
                    'allDetails' => $allExpenseDetail,
                    'serialNumber' => $serialNumber,
                    'bankName' => $bankList
        ]);
    }

    public function approveAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postedData = (array) $request->getPost();
            //  print_r($postedData);die;
            $transferSettlementModel = new TransferSettlementModel();
            $status = 'AP';
            if($postedData['submit']=='APPROVE'){
                $status = 'AP';
            }else if($postedData['submit']=='REJECT'){
                $status = 'R';
            }
            if ($postedData['depDate'][0] != null){
                for ($i = 0; $i < count($postedData['depDate']); $i++){
                    $transferSettlementModel->approverRemarks = $postedData['approverRemarks'][0];
                    $transferSettlementModel->status = $status;
                    $transferSettlementModel->approvedBy = $this->employeeId;
                    $transferSettlementModel->approvedDt = Helper::getcurrentExpressionDate();
                    $this->transferSettlementStatusRepo->edit($transferSettlementModel, $postedData['transferSettlementId'][$i]);
                }
            }
            if($status=='AP'){
                $this->flashmessenger()->addMessage("Successfully Approved!!!");
            }else{
                $this->flashmessenger()->addMessage("Successfully Rejected!!!");
            }
            return $this->redirect()->toRoute("transferSettlementStatus");
        }
    }

}