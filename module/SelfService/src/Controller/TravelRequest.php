<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelExpenseDetail;
use SelfService\Model\TravelRequest as TravelRequestModel;
use SelfService\Model\TravelSubstitute;
use SelfService\Repository\TravelExpenseDtlRepository;
use SelfService\Repository\TravelRequestRepository;
use SelfService\Repository\TravelSubstituteRepository;
use Setup\Model\HrEmployees;
use Travel\Repository\TravelItnaryRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use SelfService\Model\TravelExpenses as TravelExpensesModel;
use SelfService\Repository\TravelExpensesRepository;
// use SelfService\Repository\TravelExpensesRepository;

class TravelRequest extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TravelRequestRepository::class);
        $this->initializeForm(TravelRequestForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $data['employeeId'] = $this->employeeId;
                $data['requestedType'] = 'ad';
                $rawList = $this->repository->getFilteredRecords($data);
                $list = iterator_to_array($rawList, false);
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
        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'statusId', 'class' => 'form-control reset-field', 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
                    'status' => $statusSE,
                    'employeeId' => $this->employeeId
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();

        $model = new TravelRequestModel();
        if ($request->isPost()) {
            $postData = $request->getPost();

            $travelSubstitute = null;
            $this->form->setData($postData);

            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->transportTypeList = implode(',',$model->transportTypeList);
                $model->requestedAmount = ($model->requestedAmount == null) ? 0 : $model->requestedAmount;
                $model->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
                $model->employeeId = $this->employeeId;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->fromDate = Helper::getExpressionDate($model->fromDate);
                $model->toDate = Helper::getExpressionDate($model->toDate);
                $model->departureDate = $model->fromDate;
                $model->returnedDate = $model->toDate;
                $model->recommenderId = $postData->recommenderId;
                $model->approverId = $postData->approverId;
                // print_r($model);die;
                // $this->repository->addAlternaterRecommenderApprover($this->employeeId, $postData->recommenderId, $postData->approverId);
                // print_r($model);die;
                $this->repository->add($model);

                $this->flashmessenger()->addMessage("Travel Request Successfully added!!!");

                if ($travelSubstitute != null) {
                    $travelSubstituteModel = new TravelSubstitute();
                    $travelSubstituteRepo = new TravelSubstituteRepository($this->adapter);

                    $travelSubstitute = $postData->travelSubstitute;

                    if (isset($this->preference['travelSubCycle']) && $this->preference['travelSubCycle'] == 'N') {
                        $travelSubstituteModel->approvedFlag = 'Y';
                        $travelSubstituteModel->approvedDate = Helper::getcurrentExpressionDate();
                    }
                    $travelSubstituteModel->travelId = $model->travelId;
                    $travelSubstituteModel->employeeId = $travelSubstitute;
                    $travelSubstituteModel->createdBy = $this->employeeId;
                    $travelSubstituteModel->createdDate = Helper::getcurrentExpressionDate();
                    $travelSubstituteModel->status = 'E';

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
                return $this->redirect()->toRoute("travelRequest");
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
        $recommenderApproverList = $this->repository->getRecommenderApproverList($this->employeeId);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeId' => $this->employeeId,
                    'requestTypes' => $requestType,
                    'transportTypes' => $transportTypes,
                    'recommenderApproverList' => $recommenderApproverList,
                    'employeeList' => EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ", false, true)
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('travelRequest');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Travel Request Successfully Cancelled!!!");
        return $this->redirect()->toRoute("travelRequest", ["action" => "expense"]);
    }

    public function expenseAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $data['employeeId'] = $this->employeeId;
                $data['requestedType'] = 'ep';
                $rawList = $this->repository->getFilteredRecords($data);
                $list = iterator_to_array($rawList, false);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'statusId', 'class' => 'form-control reset-field', 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
                    'status' => $statusSE,
                    'employeeId' => $this->employeeId
        ]);
    }

    public function expenseAddAction() {
        $request = $this->getRequest();
        $model = new TravelExpensesModel();
        $reqModel = new TravelRequestModel();
        $repo = new TravelExpensesRepository($this->adapter);
        if ($request->isPost()) {
            
            // print_r($request->getPost()->getArrayCopy());die;
            $travelNewId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
            $travelId = (int) $this->params()->fromRoute('id');
            $detail = $this->repository->fetchById($travelId);
            $postData = $request->getPost()->getArrayCopy();
            // print_r($postData);die;
            $departureDate = $postData['departureDate'];
            $returnedDate = $postData['returnedDate'];
            
            // print_r($postData['approverId']);die;


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
                    $classId = $this->repository->getClassIdFromEmpId($this->employeeId);
                    $configId = $this->repository->getCongifId("DOMESTIC", $postData['mot'][$i], $classId);
                    $rate = $this->repository->getRateFromConfigId($configId);
                    if((date("Y-m-d", strtotime($postData['depDate'][$i]))<date("Y-m-d", strtotime($detail['FROM_DATE'])) && date("Y-m-d", strtotime($postData['arrDate'][$i]))<date("Y-m-d", strtotime($detail['FROM_DATE'])))
                    || (date("Y-m-d", strtotime($postData['depDate'][$i]))>date("Y-m-d", strtotime($detail['TO_DATE'])) && date("Y-m-d", strtotime($postData['arrDate'][$i]))>date("Y-m-d", strtotime($detail['TO_DATE'])))){
                        $total = 0;
                    }else{
                        $total = (float)$unit * (float)$rate;
                    }
                    // print_r((float)$unit * (float)$rate);die;
                    // $total = (float)$unit * (float)$rate;
                    $model->travelExpenseId = ((int) Helper::getMaxId($this->adapter, "hris_travel_expense", "TRAVEL_EXPENSE_ID")) + 1;
                    $model->travelId= $travelNewId;
                    $model->configId= $configId;
                    $model->amount= $total;
                    if($postData['otherExpenses'][$i]){
                        $model->other_expense= $postData['otherExpenses'][$i];
                    }else{
                        $model->other_expense= 0;
                    }
                   
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
                    if (fmod($postData['noOfDaysInternational'][$j],1)==0){
                        $model->halfDayFlag = 'N';
                    }else{
                        $model->halfDayFlag='Y';
                    }
                    $classId = $this->repository->getClassIdFromEmpId($this->employeeId);
                    $configId = $this->repository->getCongifId("INTERNATIONAL", $postData['motInternational'][$j], $classId);
                    $rate = $this->repository->getRateFromConfigId($configId);
                    if((date("Y-m-d", strtotime($postData['depDateInternational'][$j]))<date("Y-m-d", strtotime($detail['FROM_DATE'])) && date("Y-m-d", strtotime($postData['arrDateInternational'][$j]))<date("Y-m-d", strtotime($detail['FROM_DATE'])))
                    || (date("Y-m-d", strtotime($postData['depDateInternational'][$j]))>date("Y-m-d", strtotime($detail['TO_DATE'])) && date("Y-m-d", strtotime($postData['arrDateInternational'][$j]))>date("Y-m-d", strtotime($detail['TO_DATE'])))){
                        $total = 0;
                    }else{
                        $total = (float)$unit * (float)$rate;
                    }
                    // $total = $unit * $rate;
                    $model->travelExpenseId = ((int) Helper::getMaxId($this->adapter, "hris_travel_expense", "TRAVEL_EXPENSE_ID")) + 1;
                    $model->travelId= $travelNewId;
                    $model->configId= $configId;
                    $model->amount= $total;
                    $model->other_expense= $postData['otherExpensesInternational'][$j]?$postData['otherExpensesInternational'][$j]:0;
                    $model->exchangeRate=$postData['exchangeRateInternational'][$j]?$postData['exchangeRateInternational'][$j]:0;
                    $model->total= (float)((float)$total*(float)$model->exchangeRate)+(float)$postData['otherExpensesInternational'][$j]+($postData['internationalRate1'][$j]?(float)$postData['internationalRate1'][$j]:0)+ ($postData['internationalRate2'][$j]?(float)$postData['internationalRate2'][$j]:0);
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
                $reqModel->isTeamLead = 'Y';
            }else{
                $reqModel->isTeamLead = 'N';
            }

            $reqModel->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
            $reqModel->employeeId = $this->employeeId;
            $reqModel->requestedDate = Helper::getcurrentExpressionDate();
            $reqModel->status = 'RQ';
            $reqModel->fromDate = $detail['FROM_DATE'];
            $reqModel->toDate = $detail['TO_DATE'];
            $reqModel->destination = $detail['DESTINATION'];
            $reqModel->departure = $detail ['DEPARTURE'];
            $reqModel->purpose = $detail['PURPOSE'];
            $reqModel->travelCode = $detail['TRAVEL_CODE'];
            $reqModel->requestedType = 'ep';
            $reqModel->requestedAmount = $this->repository->getTotalExpenseAmount($travelNewId, $travelId, $reqModel->isTeamLead);
            $reqModel->referenceTravelId = $travelId;
            $reqModel->departureDate = Helper::getExpressionDate($departureDate);
            $reqModel->returnedDate = Helper::getExpressionDate($returnedDate);
            $reqModel->fromDate = Helper::getExpressionDate($reqModel->fromDate);
            $reqModel->toDate = Helper::getExpressionDate($reqModel->toDate);
            $reqModel->departureDate = $reqModel->fromDate;
            $reqModel->returnedDate = $reqModel->toDate;
            $reqModel->recommenderId = $postData['recommenderId'];
            $reqModel->approverId = $postData['approverId'];
            // print_r($reqModel->requestedAmount);die;
            // $this->repository->addAlternaterRecommenderApprover($this->employeeId, $postData['recommenderId'],$postData['approverId']);

            $this->repository->add($reqModel);

            $error = "";
            try {
                HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $reqModel, $this->adapter, $this);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("travelRequest");
        }


        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }
        $detail = $this->repository->fetchById($id);
        $recommenderApproverList = $this->repository->getRecommenderApproverList($this->employeeId);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail,
                    'id' => $id,
                    'recommenderApproverList' => $recommenderApproverList
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
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
        if($this->preference['displayHrApproved'] == 'Y' && $detail['HARDCOPY_SIGNED_FLAG'] == 'Y'){
            $detail['APPROVER_ID'] = '-1';
            $detail['APPROVER_NAME'] = 'HR';
            $detail['RECOMMENDER_ID'] = '-1';
            $detail['RECOMMENDER_NAME'] = 'HR';
        }
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
        $jvDetails = $this->repository->getJvDetails($id);
        $detail['APPROVER_NAME']=$this->repository->getAlternateApproverName($this->employeeId)[0]['NAME'];
        $detail['RECOMMENDER_NAME']=$this->repository->getAlternateRecommenderName($this->employeeId)[0]['NAME'];
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'recommender' => $detail['NAME_RECOMMENDER'] == null ? ($detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME']) : $detail['NAME_RECOMMENDER'],
                    'approver' => $detail['NAME_APPROVER'] == null ? ($detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME']) : $detail['NAME_APPROVER'],
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
                    'itnaryId' => null,
                    'travelItnaryDet' => $travelItnaryDet,
                    'travelItnaryMemDet' => $travelItnaryMemDet,
                    'transportTypes' => $transportTypes,
                    'Jvdetails'=>$jvDetails[0],
                    'transportTypes' => $transportTypes,
                    'id' => $id,
                    'status' => $detail['STATUS']
        ]);
    }

    public function editAction() {
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
            return $this->redirect()->toRoute("travelRequest");
        }
        if ($this->repository->checkAllowEdit($id) == 'N') {
            return $this->redirect()->toRoute("travelRequest");
        }

        if ($request->isPost()) {
            $postData = $request->getPost();
            $detail = $this->repository->fetchById($id);
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
                        $classId = $this->repository->getClassIdFromEmpId($this->employeeId);
                        $configId = $this->repository->getCongifId("DOMESTIC", $postData['mot'][$i], $classId);
                        $rate = $this->repository->getRateFromConfigId($configId);
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
                        $classId = $this->repository->getClassIdFromEmpId($this->employeeId);
                        $configId = $this->repository->getCongifId("INTERNATIONAL", $postData['motInternational'][$j], $classId);
                        $rate = $this->repository->getRateFromConfigId($configId);
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
                        $model->exchangeRate=$postData['exchangeRateInternational'][$j];
                        $model->total= (float)((float)$total*(float)$model->exchangeRate)+(float)$postData['otherExpensesInternational'][$j]+($postData['internationalRate1'][$j]?(float)$postData['internationalRate1'][$j]:0)+ ($postData['internationalRate2'][$j]?(float)$postData['internationalRate2'][$j]:0);
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

                $travelRequest->employeeId = $this->employeeId;  
                $this->repository->edit($travelRequest, $id);
                $travelRequest->requestedAmount = $this->repository->getTotalExpenseAmount($id, $linkedId, $travelRequest->isTeamLead);
                $this->repository->deletePreviouseLinkFiles($id);
                $this->repository->linkTravelWithFiles($id);
                $this->repository->edit($travelRequest, $id);
                
                $this->flashmessenger()->addMessage("Travel Request Successfully Edited!!!");
                
                return $this->redirect()->toRoute("travelRequest", array(
                'action' => 'expense'));
            }
            print_r('form not valid');die;
        }

        $detail = $this->repository->fetchById($id);
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

    public function expenseViewAction() {
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }

        $detail = $this->repository->fetchById($id);

        // print_r($detail);die;

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
            // print_r($row);die;

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
            $totalInternationalAmount += ($row['TOTAL']);
            array_push($internationalExpenseDtlList, $row);
        }
        $jvDetails = $this->repository->getJvDetails($id);
        $advanceForTravel = $this->repository->getValueAdvanceForTravel($id);
        // print_r($id);die;
        $totalNoOfAttachment = $this->repository->getTotalNoOfAttachment($id);
        // print_r($totalNoOfAttachment[0]['COUNT(*)']);die;
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
                    'totalInternationalAmount' => $totalInternationalAmount,
                    'Jvdetails'=>$jvDetails[0],

                    'advaceForTravel' => $advanceForTravel[0]['REQUESTED_AMOUNT'],
                    'totalAttachment' => $totalNoOfAttachment[0]['COUNT(*)'],
                    'allInOrder' => $allExpenseInOrder,
        ]);
    }

    public function deleteExpenseDetailAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $id = $postData['data']['id'];
            $repository = new TravelExpenseDtlRepository($this->adapter);
            $repository->delete($id);
            $responseData = [
                "success" => true,
                "data" => "Expense Detail Successfully Removed"
            ];
        } else {
            $responseData = [
                "success" => false,
            ];
        }
        return new CustomViewModel($responseData);
    }

    public function ExpenseDetailListAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy()['data'];
            $travelId = $postData['travelId'];
            $travelDetail = $this->repository->fetchById($travelId);
            $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
            $expenseDtlList = [];
            $result = $expenseDtlRepo->fetchByTravelId($travelId);
            foreach ($result as $row) {
                array_push($expenseDtlList, $row);
            }
            return new CustomViewModel([
                'success' => true,
                'data' => [
                    'travelDetail' => $travelDetail,
                    'expenseDtlList' => $expenseDtlList,
                    'numExpenseDtlList' => count($expenseDtlList)
                ]
            ]);
        } else {
            return new CustomViewModel(['success' => false]);
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
                    $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/travel_documents/" . $newFileName);
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

    public function pushDCFileLinkAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $userID = $this->employeeId;
            $returnData = $this->repository->pushFileLink($data, $userID);
            return new JsonModel(['success' => true, 'data' => $returnData[0], 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function pullFilebyIdAction()
    {

        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // print_r($data);die;
            // $fileLinkId = $this->repository->getFileLinkId($data->id);
            $returnData = $this->repository->pullFilebyId($data->id);

            return new JsonModel(['success' => true, 'data' => $returnData, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function editTravelAction() {
        $request = $this->getRequest();

        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }
        if ($this->repository->checkAllowEdit($id) == 'N') {
            return $this->redirect()->toRoute("travelRequest");
        }

        if ($request->isPost()) {
            $travelRequest = new TravelRequestModel();
            $postedData = $request->getPost();
            //print_r($postedData);die;
            $this->form->setData($postedData);

            if ($this->form->isValid()) {
                $travelRequest->exchangeArrayFromForm($this->form->getData());
                $travelRequest->modifiedDt = Helper::getcurrentExpressionDate();
                $travelRequest->employeeId = $this->employeeId;
                $travelRequest->fromDate = Helper::getExpressionDate($travelRequest->fromDate);
                $travelRequest->toDate = Helper::getExpressionDate($travelRequest->toDate);
                $travelRequest->transportTypeList = implode(',',$travelRequest->transportTypeList);
                $travelRequest->departureDate = $travelRequest->fromDate;
                $travelRequest->returnedDate = $travelRequest->toDate;
                //print_r($travelRequest);die;
                $this->repository->edit($travelRequest, $id);
                $this->flashmessenger()->addMessage("Travel Request Successfully Edited!!!");
                return $this->redirect()->toRoute("travelRequest");
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

        $detail['APPROVER_NAME']=$this->repository->getAlternateApproverName($this->employeeId)[0]['NAME'];
        $detail['RECOMMENDER_NAME']=$this->repository->getAlternateRecommenderName($this->employeeId)[0]['NAME'];
        
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'recommender' => $detail['NAME_RECOMMENDER'] == null ? ($detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME']) : $detail['NAME_RECOMMENDER'],
                    'approver' => $detail['NAME_APPROVER'] == null ? ($detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME']) : $detail['NAME_APPROVER'],
                    'detail' => $detail,
                    'todayDate' => date('d-M-Y'),
                    'advanceAmount' => $advanceAmount,
                    'transportTypes' => $transportTypes,
                    
                        //'files' => $fileDetails
        ]);
    }

    // public function getLineTotalAction(){
    //     try{
    //         $request = $this->getRequest();
    //         $travelType = $request->getPost('travelType');
    //         $mot = $request->getPost('mot');
    //         $unit = $request->getPost('unit');
    //         $classId = $this->repository->getClassIdFromEmpId($this->employeeId);
    //         $configId = $this->repository->getCongifId($travelType, $mot, $classId);
    //         $rate = $this->repository->getRateFromConfigId($configId);
    //         $total = $unit * $rate;
    //         return new JsonModel(['success' => true, 'data' => $total, 'error' => '']);
    //     } catch (Exception $e) {
    //         return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
    //     }
    // }

    public function jvAddAction() {
        
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $postedData = $request->getPost();
            //print_r($postedData);die;
            $this->repository->insertJVdata($id,$postedData->jvNumber, $postedData->chequeNumber,$postedData->bank);
            return $this->redirect()->toRoute("travelRequest");
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
                    'recommender' => $detail['NAME_RECOMMENDER'] == null ? ($detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME']) : $detail['NAME_RECOMMENDER'],
                    'approver' => $detail['NAME_APPROVER'] == null ? ($detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME']) : $detail['NAME_APPROVER'],
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
        // $leaveList = EntityHelper::getTableKVListWithSortOption($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID, [TravelRequestModel::STATUS => 'E'],  "ASC", null, true);
        // $leaveSE = $this->getSelectElement(['name' => 'leave', 'id' => 'leaveId', 'class' => 'form-control', 'label' => 'Leave Type'], $leaveList);
        // $leaveStatusFE = $this->getStatusSelectElement(['name' => 'leaveStatus', 'id' => 'leaveRequestStatusId', 'class' => 'form-control', 'label' => 'Leave Request Status']);



        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'statusId', 'class' => 'form-control reset-field', 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
                    'status' => $statusSE,
                    'employeeId' => $this->employeeId
        ]);

        
    }

    public function validateTravelRequestAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $error = $this->repository->validateTravelRequest($postedData['startDate'], $postedData['endDate'], $postedData['employeeId']);
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
