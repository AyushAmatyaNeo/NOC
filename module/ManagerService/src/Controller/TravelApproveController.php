<?php

namespace ManagerService\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Exception;
use ManagerService\Repository\TravelApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use Payroll\Model\FinanceData;
use Payroll\Repository\FinanceDataRepository;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelRequest;
use SelfService\Model\TravelRequest as TravelRequestModel;
use SelfService\Repository\TravelExpenseDtlRepository;
use Travel\Repository\TravelItnaryRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use SelfService\Repository\TravelExpensesRepository;

class TravelApproveController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TravelApproveRepository::class);
        $this->initializeForm(TravelRequestForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $search['employeeId'] = $this->employeeId;
                $search['status'] = ['RQ', 'RC'];
                $rawList = $this->repository->getPendingList($this->employeeId);
                $list = Helper::extractDbData($rawList);
                for($i=0; $i<count($list); $i++){
                    $transportTypeListArray = explode (",", $list[$i]['TRANSPORT_TYPE_LIST']);
                    
                    $transportTypeDetailListArray = [];
                    foreach ($transportTypeListArray as $transportTypeDetail){
                        $tempTransportTypeDetail = $this->repository->getTravelTypeDetail($transportTypeDetail);
                        array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
                    }
                    $list[$i]['TRANSPORT_TYPE_LIST'] = implode(', ',$transportTypeDetailListArray);
                }
                //print_r($list);die;
                //echo('<pre>');print_r($list);die;
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo([]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0 || $role === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
        $request = $this->getRequest();
        //$filesData = $this->repository->fetchAttachmentsById($id);
        $travelRequestModel = new TravelRequest();
        if ($request->isPost()) {
            $postedData = (array) $request->getPost();
            $action = $postedData['submit'];
            $this->makeDecision($id, $role, $action == 'Approve', $postedData[$role == 2 ? 'recommendedRemarks' : 'approvedRemarks'], true);
            return $this->redirect()->toRoute("travelApprove");
        }

        $detail = $this->repository->fetchById($id);
        $detail['TRANSPORT_TYPE_LIST'] = explode (",", $detail['TRANSPORT_TYPE_LIST']);
        
        for($i=0; $i<count($detail['TRANSPORT_TYPE_LIST']); $i++){
            $transportTypeDetailListArray = [];
            foreach ($detail['TRANSPORT_TYPE_LIST'] as $transportTypeDetail){
                $tempTransportTypeDetail = $this->repository->getTravelTypeDetail($transportTypeDetail);
                array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
            }
            $detail['TRANSPORT_TYPE_LIST_DETAIL'] = $transportTypeDetailListArray;
        }
        $detail['TRANSPORT_TYPE_LIST_DETAIL_STR']=implode(', ',$detail['TRANSPORT_TYPE_LIST_DETAIL']);
        $issueNum = $this->repository->getIssueNum($id,$detail['EMPLOYEE_ID']);
        $travelRequestModel->exchangeArrayFromDB($detail);
        $this->form->bind($travelRequestModel);

        // print_r( $this->form);die;
        
        $travelItnaryDet = [];
        $travelItnaryMemDet = [];


        $transportTypes = array(
            'AP' => 'Aeroplane',
            'OV' => 'Office Vehicles',
            'TI' => 'Taxi',
            'BS' => 'Bus',
            'OF'  => 'On Foot'
        );
        $jvDetails = $this->repository->getJvDetails($id);
        // print_r($jvDetails[0]);die;

        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        $detail['APPROVER_NAME']=$this->repository->getAlternateApproverName($id)[0]['NAME'];
        $detail['RECOMMENDER_NAME']=$this->repository->getAlternateRecommenderName($id)[0]['NAME'];
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'role' => $role,
                    'form' => $this->form,
                    'recommender' => $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'],
                    'approver' => $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'],
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
                    //'files' => $filesData,
                    'itnaryId' => null,
                    'travelItnaryDet' => $travelItnaryDet,
                    'travelItnaryMemDet' => $travelItnaryMemDet,
                    'issueNum' => $issueNum['0']['ROW_NUM'],
                    'transportTypes' => $transportTypes,
                    'Jvdetails'=>$jvDetails[0]
        ]);
    }

    public function expenseDetailAction() {
        $id = (int) $this->params()->fromRoute('id');

        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
        $detail = $this->repository->fetchById($id);
        $detail['ACCOMPLISHMENT'] = base64_decode($detail['ACCOMPLISHMENT']);
        
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
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
        $AllInOrder = $expenseRepo->fetchAllInOrder($id);
        $allExpenseInOrder = [];
        $AllDomesticExpenseDtlList = $expenseRepo->fetchDomesticById($id);
        $AllInternationalExpenseDtlList = $expenseRepo->fetchInternationalById($id);
        $domesticExpenseDtlList = [];
        $internationalExpenseDtlList = [];
        $totalDomesticAmount = 0;
        $totalInternationalAmount = 0;
        foreach ($AllInOrder as $row) {
            if ($row['HALF_DAY_FLAG']=='Y'){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            }
            array_push($allExpenseInOrder, $row);
        }
        foreach ($AllDomesticExpenseDtlList as $row) {
            if(strtoupper($detail['RETURNED_DATE'])==strtoupper($row['ARRAIVAL_DT'])){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            }
            $totalDomesticAmount += $row['TOTAL'];
            array_push($domesticExpenseDtlList, $row);
        }
        foreach ($AllInternationalExpenseDtlList as $row) {
             if(strtoupper($detail['RETURNED_DATE'])==strtoupper($row['ARRAIVAL_DT'])){
                $row['NOOFDAYS'] = $row['NOOFDAYS'] - 0.5;
            } 
                $totalInternationalAmount += ($row['TOTAL']*$row['EXCHANGE_RATE']);
                array_push($internationalExpenseDtlList, $row);
        }

        $jvDetails = $this->repository->getJvDetails($id);

        $advanceForTravel = $this->repository->getValueAdvanceForTravel($id);
        $totalNoOfAttachment = $this->repository->getTotalNoOfAttachment($id);

        // print_r($jvDetails);die;
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
                    'Jvdetails'=>$jvDetails[0],
                    'advaceForTravel' => $advanceForTravel[0]['REQUESTED_AMOUNT'],
                    'totalAttachment' => $totalNoOfAttachment[0]['COUNT(*)'],
                    'allInOrder' => $allExpenseInOrder,
                        ]
        );
    }
    
    public function statusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $searchQuery = $request->getPost();
                $searchQuery['employeeId'] = $this->employeeId;
                
                $rawList = $this->repository->getAllFiltered((array) $searchQuery);
                $list = Helper::extractDbData($rawList);
                for($i=0; $i<count($list); $i++){
                    $transportTypeListArray = explode (",", $list[$i]['TRANSPORT_TYPE_LIST']);
                    
                    $transportTypeDetailListArray = [];
                    foreach ($transportTypeListArray as $transportTypeDetail){
                        $tempTransportTypeDetail = $this->repository->getTravelTypeDetail($transportTypeDetail);
                        array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
                    }
                    $list[$i]['TRANSPORT_TYPE_LIST'] = implode(', ',$transportTypeDetailListArray);
                }
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
                    'travelStatus' => $statusSE,
                    'recomApproveId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function batchApproveRejectAction() {
        $request = $this->getRequest();
        try {
            $postData = $request->getPost();
            $this->makeDecision($postData['id'], $postData['role'], $postData['btnAction'] == "btnApprove");
            return new JsonModel(['success' => true, 'data' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function makeDecision($id, $role, $approve, $remarks = null, $enableFlashNotification = false) {
        $notificationEvent = null;
        $message = null;
        $model = new TravelRequest();
        $model->travelId = $id;
    //    $model->exchangeArrayFromDB($this->repository->fetchById($id));
        $model->requestedType = $this->repository->fetchById($id)['REQUESTED_TYPE'];
        $model->requestedAmount = $this->repository->fetchById($id)['REQUESTED_AMOUNT'];
        $model->fromDate = Helper::getExpressionDate($this->repository->fetchById($id)['FROM_DATE']);
        $model->toDate = Helper::getExpressionDate($this->repository->fetchById($id)['TO_DATE']);
        $model->employeeId = $this->repository->fetchById($id)['EMPLOYEE_ID'];
        switch ($role) {
            case 2:
                $model->recommendedRemarks = $remarks;
                $model->recommendedDate = Helper::getcurrentExpressionDate();
                $model->recommendedBy = $this->employeeId;
                $model->status = $approve ? "RC" : "R";
                $message = $approve ? "Travel Request Recommended" : "Travel Request Rejected";
                $notificationEvent = $approve ? NotificationEvents::TRAVEL_RECOMMEND_ACCEPTED : NotificationEvents::TRAVEL_RECOMMEND_REJECTED;
                break;
            case 4:
                $model->recommendedDate = Helper::getcurrentExpressionDate();
                $model->recommendedBy = $this->employeeId;
            case 3:
                $model->approvedRemarks = $remarks;
                $model->approvedDate = Helper::getcurrentExpressionDate();
                $model->approvedBy = $this->employeeId;
                $model->status = $approve ? "AP" : "R";
                $message = $approve ? "Travel Request Approved" : "Travel Request Rejected";
                $notificationEvent = $approve ? NotificationEvents::TRAVEL_APPROVE_ACCEPTED : NotificationEvents::TRAVEL_APPROVE_REJECTED;
                break;
        }
        if($model->requestedType == 'ad' && $model->status == 'AP'){
            $model->advanceAmount = $model->requestedAmount?$model->requestedAmount:0;
        }
        $editError=$this->repository->edit($model, $id);
        if ($model->requestedType == 'ep' && $model->status == 'AP') {
            $this->financialImpact($model);
        }
        if ($enableFlashNotification) {
            $this->flashmessenger()->addMessage($message);
            $this->flashmessenger()->addMessage($editError);
        }
        try {
            //HeadNotification::pushNotification($notificationEvent, $model, $this->adapter, $this);
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
        }
    }

    public function financialImpact($travelModel){
        // print_r($travelModel);die;
        $updateModel = new TravelRequest();
        $advanceAmount = $this->repository->getAdvanceAmount($travelModel->travelId);
        $travelMasterId = $this->repository->getMasterTravelId($travelModel->travelId);
        $updateModel->expenseAmount = (float)$travelModel->requestedAmount + (float)$advanceAmount;
        $updateModel->itnaryId = $travelModel->travelId;
        if($travelModel->requestedAmount<0){
            $updateModel->adjustmentFlag = 'D';
            $updateModel->adjustmentAmount = (float)$travelModel->requestedAmount * (-1);
        }else if ($advanceAmount == null || $advanceAmount == 0){
            $updateModel->adjustmentFlag = 'N';
            $updateModel->adjustmentAmount = null;
        }else{
            $updateModel->adjustmentFlag = 'A';
            $updateModel->adjustmentAmount = (float)$travelModel->requestedAmount;
        }
        $this->repository->updateForVoucherImpact($updateModel, $travelMasterId);
    }

    public function editTravelAction(){
        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $travelRequest = new TravelRequestModel();
            $postedData = $request->getPost();
            $this->form->setData($postedData);

            if ($this->form->isValid()) {
                $pastData = $this->repository->getpastData($id);
                $travelRequest->exchangeArrayFromForm($this->form->getData());
                $travelRequest->travelId = $pastData[0]['TRAVEL_ID']; 
                $travelRequest->employeeId = $pastData[0]['EMPLOYEE_ID'] ;
                $travelRequest->requestedDate = $pastData[0]['REQUESTED_DATE'] ;
                $travelRequest->status = $pastData[0]['STATUS'] ;
                $travelRequest->recommendedDate = $pastData[0]['RECOMMENDED_DATE'] ;
                $travelRequest->recommendedBy = $pastData[0]['RECOMMENDED_BY'] ;
                $travelRequest->recommendedRemarks = $pastData[0]['RECOMMENDED_REMARKS'] ;
                $travelRequest->approvedDate = $pastData[0]['APPROVED_DATE'] ;
                $travelRequest->approvedBy = $pastData[0]['APPROVED_BY'] ;
                $travelRequest->approvedRemarks = $pastData[0]['APPROVED_REMARKS'] ;
                $travelRequest->travelCode = $pastData[0]['TRAVEL_CODE'] ;
                $travelRequest->referenceTravelId = $pastData[0]['REFERENCE_TRAVEL_ID'] ;
                $travelRequest->departureDate = $pastData[0]['DEPARTURE_DATE'] ;
                $travelRequest->returnedDate = $pastData[0]['RETURNED_DATE'] ;
                $travelRequest->hardcopySignedFlag = $pastData[0]['HARDCOPY_SIGNED_FLAG'] ;
                $travelRequest->itnaryId = $pastData[0]['ITNARY_ID'] ;
                // $travelRequest->exchangeArrayFromForm($pastData[0]);
                $travelRequest->modifiedDt = Helper::getcurrentExpressionDate();
                // $travelRequest->employeeId = $this->employeeId;
                $travelRequest->fromDate = Helper::getExpressionDate($travelRequest->fromDate);
                $travelRequest->toDate = Helper::getExpressionDate($travelRequest->toDate);
                $travelRequest->departureDate = $travelRequest->fromDate ;
                $travelRequest->returnedDate = $travelRequest->toDate ;
                $travelRequest->transportTypeList = implode(',',$travelRequest->transportTypeList);
                //print_r($travelRequest);die;
            //echo('<pre>');print_r($travelRequest);die;

                $this->repository->edit($travelRequest, $id);
                $this->flashmessenger()->addMessage("Travel Request Successfully Edited!!!");
                return $this->redirect()->toRoute("travelApprove");
            }
        }

        $detail = $this->repository->fetchById($id);
        $detail['TRANSPORT_TYPE_LIST'] = explode (",", $detail['TRANSPORT_TYPE_LIST']);
        
        for($i=0; $i<count($detail['TRANSPORT_TYPE_LIST']); $i++){
            $transportTypeDetailListArray = [];
            foreach ($detail['TRANSPORT_TYPE_LIST'] as $transportTypeDetail){
                $tempTransportTypeDetail = $this->repository->getTravelTypeDetail($transportTypeDetail);
                array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
            }
            $detail['TRANSPORT_TYPE_LIST_DETAIL'] = $transportTypeDetailListArray;
        }
        //print_r($detail);die;
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
                    'recommender' => $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'],
                    'approver' => $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'],
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
                    'transportTypes' => $transportTypes
                        //'files' => $fileDetails
        ]);
    }

    public function jvAddAction() {
        
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $postedData = $request->getPost();
            //print_r($postedData);die;
            $this->repository->insertJVdata($id,$postedData->jvNumber, $postedData->chequeNumber,$postedData->bank);
            return $this->redirect()->toRoute("travelApprove");
            // print_r($postedData);die;
        }
        $detail = $this->repository->fetchById($id);
        $detail['TRANSPORT_TYPE_LIST'] = explode (",", $detail['TRANSPORT_TYPE_LIST']);
        
        for($i=0; $i<count($detail['TRANSPORT_TYPE_LIST']); $i++){
            $transportTypeDetailListArray = [];
            foreach ($detail['TRANSPORT_TYPE_LIST'] as $transportTypeDetail){
                $tempTransportTypeDetail = $this->repository->getTravelTypeDetail($transportTypeDetail);
                array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
            }
            $detail['TRANSPORT_TYPE_LIST_DETAIL'] = $transportTypeDetailListArray;
        }
        $detail['TRANSPORT_TYPE_LIST_DETAIL_STR']=implode(', ',$detail['TRANSPORT_TYPE_LIST_DETAIL']);
        //print_r($detail);die;
        if($this->preference['displayHrApproved'] == 'Y' && $detail['HARDCOPY_SIGNED_FLAG'] == 'Y'){
            $detail['APPROVER_ID'] = '-1';
            $detail['APPROVER_NAME'] = 'HR';
            $detail['RECOMMENDER_ID'] = '-1';
            $detail['RECOMMENDER_NAME'] = 'HR';
        }
        //$fileDetails = $this->repository->fetchAttachmentsById($id);
        $model = new TravelRequestModel();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);

        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        
        $travelItnaryDet = [];
        $travelItnaryMemDet = [];

        $transportTypeListArray = explode (",", $detail['TRANSPORT_TYPE_LIST']);
        $transportTypeDetailListArray = [];
        foreach ($transportTypeListArray as $transportTypeDetail){
            $tempTransportTypeDetail = $this->repository->getTravelTypeDetail($transportTypeDetail);
            array_push($transportTypeDetailListArray,$tempTransportTypeDetail[0]['DETAIL']);
        }
        $detail['TRANSPORT_TYPE_LIST_DETAIL'] = $transportTypeDetailListArray;
        
        $transportTypes = array(
            'AP' => 'Aeroplane',
            'OV' => 'Office Vehicles',
            'TI' => 'Taxi',
            'BS' => 'Bus',
            'OF'  => 'On Foot'
        );
        $bankId =EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_BANKS', 'BANK_ID', ['BANK_NAME'], ['STATUS' => "E"],'BANK_NAME', "ASC", " ", false, true);

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'recommender' => $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'],
                    'approver' => $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'],
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
                        //'files' => $fileDetails
                    'itnaryId' => null,
                    'travelItnaryDet' => $travelItnaryDet,
                    'travelItnaryMemDet' => $travelItnaryMemDet,
                    'transportTypes' => $transportTypes,
                    'bankId' => $bankId,
                    'id' => $id
        ]);
    }

    public function jvAddExpenseAction() {
        $id = (int) $this->params()->fromRoute('id');
    
        $role = $this->params()->fromRoute('role');
    
        if ($id === 0) {
            return $this->redirect()->toRoute("travelApprove");
        }
         $request = $this->getRequest();
         $expenseRepo = new TravelExpensesRepository($this->adapter);
            if ($request->isPost()) {


                $postedData = $request->getPost();
                //print_r($postedData);die;
                $this->repository->insertJVdata($id,$postedData->jvNumber, $postedData->chequeNumber,$postedData->bank);
                return $this->redirect()->toRoute("travelApprove");
                //print_r($postedData);die;
            } else{
        $detail = $this->repository->fetchById($id);
    
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
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
        $bankId =EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_BANKS', 'BANK_ID', ['BANK_NAME'], ['STATUS' => "E"],'BANK_NAME', "ASC", " ", false, true);
    
        $numberInWord = new NumberHelper();
        $totalAmountInWords = $numberInWord->toText($totalAmount);
        $balance = $detail['REQUESTED_AMOUNT'] - $totalAmount;
    
        
        
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
    }
        // print_r($linkedId);die;
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
                    'bankId' => $bankId
                        ]
        );
    }

    
 public function batchApproveReject2Action() {
    $request = $this->getRequest();
    try {
        $postData = $request->getPost();
        // print_r($postData);
        $this->makeDecision2($postData['id'], $postData['role'], $postData['btnAction'] == "btnApprove");
        return new JsonModel(['success' => true, 'data' => null]);
    } catch (Exception $e) {
        return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
    }
}


private function makeDecision2($id, $role, $approve, $remarks = null, $enableFlashNotification = false) {
    $notificationEvent = null;
    $message = null;
    $model = new TravelRequest();
    $model->travelId = $id;
    switch ($role) {
        case 2:
            $model->recommendedRemarks = $remarks;
            $model->recommendedDate = Helper::getcurrentExpressionDate();
            $model->recommendedBy = $this->employeeId;
            $model->status = $approve ? "CR" : "R";
            $message = $approve ? "Travel Request Recommended" : "Travel Request Rejected";
            $notificationEvent = $approve ? NotificationEvents::TRAVEL_RECOMMEND_ACCEPTED : NotificationEvents::TRAVEL_RECOMMEND_REJECTED;
            break;
        case 4:
            $model->recommendedDate = Helper::getcurrentExpressionDate();
            $model->recommendedBy = $this->employeeId;
        case 3:
            $model->approvedRemarks = $remarks;
            $model->approvedDate = Helper::getcurrentExpressionDate();
            $model->approvedBy = $this->employeeId;
            $model->status = $approve ? "C" : "R";
            $message = $approve ? "Travel Request Approved" : "Travel Request Rejected";
            $notificationEvent = $approve ? NotificationEvents::TRAVEL_APPROVE_ACCEPTED : NotificationEvents::TRAVEL_APPROVE_REJECTED;
            break;
    } 
    $editError=$this->repository->edit($model, $id);


    if ($enableFlashNotification) {
        $this->flashmessenger()->addMessage($message);
        $this->flashmessenger()->addMessage($editError);
    }
    try {
        HeadNotification::pushNotification($notificationEvent, $model, $this->adapter, $this);
        return new JsonModel(['success' => true, 'msg' => 'success', 'error' => '']);
    } catch (Exception $e) {
        $this->flashmessenger()->addMessage($e->getMessage());
    }
}




public function cancelListAction(){
    
    $request = $this->getRequest();
      if ($request->isPost()) {
          try {
              $rawList = $this->repository->getAllCancelRequest($this->employeeId);
              $list = Helper::extractDbData($rawList);
              return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
          } catch (Exception $e) {
              return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
          }
      }

      return $this->stickFlashMessagesTo([]);
  }	




public function cancelViewAction(){
    $id = (int) $this->params()->fromRoute('id');
    $role = $this->params()->fromRoute('role');

    if ($id === 0 || $role === 0) {
        return $this->redirect()->toRoute("travelApprove/cancelList");
    }
    $request = $this->getRequest();
    //$filesData = $this->repository->fetchAttachmentsById($id);
    $travelRequestModel = new TravelRequest();
    if ($request->isPost()) {
       
        $postedData = (array) $request->getPost();
        $action = $postedData['submit'];
        
        $this->makeDecision2($id, $role, $action == 'Approve', $postedData[$role == 2 ? 'recommendedRemarks' : 'approvedRemarks'], true);
        return $this->redirect()->toRoute("travelApprove");
    }

    $detail = $this->repository->fetchById($id);
    $travelRequestModel->exchangeArrayFromDB($detail);
    $this->form->bind($travelRequestModel);
    
    $travelItnaryDet = [];
    $travelItnaryMemDet = [];
    

    $numberInWord = new NumberHelper();
    $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
    return Helper::addFlashMessagesToArray($this, [
                'id' => $id,
                'role' => $role,
                'form' => $this->form,
                'recommender' => $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'],
                'approver' => $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'],
                'detail' => $detail,
                'todayDate' => date('d-M-Y'),
                'advanceAmount' => $advanceAmount,
                //'files' => $filesData,
                'itnaryId' => null,
                'travelItnaryDet' => $travelItnaryDet,
                'travelItnaryMemDet' => $travelItnaryMemDet
    ]);
  }

}


