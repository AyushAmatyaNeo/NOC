<?php

namespace Travel\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelRequest as TravelRequestModel;
use SelfService\Model\TravelExpenses as TravelExpensesModel;
use SelfService\Repository\TravelExpensesRepository;
use SelfService\Model\TravelSubstitute;
use SelfService\Repository\TravelRequestRepository;
use SelfService\Repository\TravelSubstituteRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Controller\HrisController;

class TravelApply extends HrisController {

//    private $form;
//    private $adapter;
//    private $travelRequesteRepository;
//    private $employeeId;
//    private $preference;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->travelRequesteRepository = new TravelRequestRepository($adapter);
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
        return $this->redirect()->toRoute("travelStatus");
    }

    /*
      public function fileUploadAction() {
      $request = $this->getRequest();
      $responseData = [];
      $files = $request->getFiles()->toArray();
      try {
      if (sizeof($files) > 0) {
      $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
      $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
      $unique = Helper::generateUniqueName();
      $newFileName = $unique . "." . $ext;
      $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/travel_documents/" . $newFileName);
      if (!$success) {
      throw new Exception("Upload unsuccessful.");
      }
      $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
      }
      } catch (Exception $e) {
      $responseData = [
      "success" => false,
      "message" => $e->getMessage(),
      "traceAsString" => $e->getTraceAsString(),
      "line" => $e->getLine()
      ];
      }
      return new JsonModel($responseData);
      }

      public function pushTravelFileLinkAction() {
      try {
      $newsId = $this->params()->fromRoute('id');
      $request = $this->getRequest();
      $data = $request->getPost();
      $returnData = $this->travelRequesteRepository->pushFileLink($data);
      return new JsonModel(['success' => true, 'data' => $returnData[0], 'message' => null]);
      } catch (Exception $e) {
      return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
      }
      }
     */

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
                    $model->advanceAmount = $model->requestedAmount?$model->requestedAmount:0;
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

    public function expenseAddAction(){
        $request = $this->getRequest();
        $model = new TravelExpensesModel();
        $reqModel = new TravelRequestModel();
        $repo = new TravelExpensesRepository($this->adapter);
        if ($request->isPost()) {
            $travelNewId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
            $travelId = (int) $this->params()->fromRoute('id');
            $detail = $this->travelRequesteRepository->fetchById($travelId);
            $postData = $request->getPost()->getArrayCopy();
            $employeeIdForExpense = $postData['employeeId'];
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
                    $classId = $this->travelRequesteRepository->getClassIdFromEmpId($employeeIdForExpense);
                    $configId = $this->travelRequesteRepository->getCongifId("DOMESTIC", $postData['mot'][$i], $classId);
                    $rate = $this->travelRequesteRepository->getRateFromConfigId($configId);
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
                    $classId = $this->travelRequesteRepository->getClassIdFromEmpId($employeeIdForExpense);
                    $configId = $this->travelRequesteRepository->getCongifId("INTERNATIONAL", $postData['motInternational'][$j], $classId);
                    $rate = $this->travelRequesteRepository->getRateFromConfigId($configId);
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
                    $model->total= (float)$total+(float)$postData['otherExpensesInternational'][$j]+($postData['internationalRate1'][$j]?(float)$postData['internationalRate1'][$j]:0)+ ($postData['internationalRate2'][$j]?(float)$postData['internationalRate2'][$j]:0);
                    $model->exchangeRate=$postData['exchangeRateInternational'][$j]?$postData['exchangeRateInternational'][$j]:0;
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
            $reqModel->employeeId = $employeeIdForExpense;
            $reqModel->requestedDate = Helper::getcurrentExpressionDate();
            $reqModel->status = 'RQ';
            $reqModel->fromDate = $detail['FROM_DATE'];
            $reqModel->toDate = $detail['TO_DATE'];
            $reqModel->destination = $detail['DESTINATION'];
            $reqModel->departure = $detail ['DEPARTURE'];
            $reqModel->purpose = $detail['PURPOSE'];
            $reqModel->travelCode = $detail['TRAVEL_CODE'];
            $reqModel->requestedType = 'ep';
            // $reqModel->requestedAmount = $this->travelRequesteRepository->getTotalExpenseAmount($travelNewId);
            $reqModel->requestedAmount = $this->travelRequesteRepository->getTotalExpenseAmount($travelNewId, $travelId, $reqModel->isTeamLead);
            $reqModel->referenceTravelId = $travelId;
            $reqModel->departureDate = Helper::getExpressionDate($departureDate);
            $reqModel->returnedDate = Helper::getExpressionDate($returnedDate);
            $reqModel->fromDate = Helper::getExpressionDate($reqModel->fromDate);
            $reqModel->toDate = Helper::getExpressionDate($reqModel->toDate);
            $reqModel->departureDate = $reqModel->fromDate;
            $reqModel->returnedDate = $reqModel->toDate;
            $reqModel->recommenderId = $postData['recommenderId'];
            $reqModel->approverId = $postData['approverId'];
            // $this->repository->addAlternaterRecommenderApprover($this->employeeId, $postData['recommenderId'],$postData['approverId']);

            $this->travelRequesteRepository->add($reqModel);

            $error = "";
            try {
                HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $reqModel, $this->adapter, $this);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("travelStatus",["action" => "settlementReport"]);
        }


        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus",["action" => "settlementReport"]);
        }
        $detail = $this->travelRequesteRepository->fetchById($id);
        $recommenderApproverList = $this->travelRequesteRepository->getRecommenderApproverList($employeeIdForExpense);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail,
                    'id' => $id,
                    'recommenderApproverList' => $recommenderApproverList
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
                // $travelRequest->requestedAmount = $this->travelRequesteRepository->getTotalExpenseAmount($id);
                $travelRequest->requestedAmount = $this->travelRequesteRepository->getTotalExpenseAmount($id, $linkedId, $travelRequest->isTeamLead);
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
    

}
