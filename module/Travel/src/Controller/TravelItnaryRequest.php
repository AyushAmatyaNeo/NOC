<?php

namespace Travel\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Repository\TravelRequestRepository;
use Setup\Model\HrEmployees;
use Travel\Form\TravelItnaryForm;
use Travel\Model\ItnaryDetails;
use Travel\Model\ItnaryMembers;
use Travel\Model\TravelItnary;
use SelfService\Model\TravelRequest;
//use Travel\Model\TravelRequest;
use Travel\Repository\TravelItnaryRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class TravelItnaryRequest extends HrisController {


    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
         $this->initializeRepository(TravelItnaryRepository::class);
        $this->initializeForm(TravelItnaryForm::class);
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
                $data['employeeId'] = $this->employeeId;
                $data['requestedType'] = 'ad';
                $rawList = $this->repository->getFilteredRecord($data);
//                $rawList = $this->repository->getFilteredRecords($data);
//                print_r($rawList);
//                die();
//                $rawList =[];
                


                return new JsonModel(['success' => true, 'data' => $rawList, 'error' => '']);
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

    public function deleteAction(){
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelItnary");
        }
        $this->repository->cancel($id, $this->employeeId);
        return $this->redirect()->toRoute("travelItnary");
    }

    public function addAction() {
//        $this->initializeForm(TravelRequestForm::class);
        $request = $this->getRequest();

        $model = new TravelItnary();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->itnaryId = ((int) Helper::getMaxId($this->adapter, TravelItnary::TABLE_NAME, TravelItnary::ITNARY_ID)) + 1;
                $model->itnaryCode = 'GWTN/TVI/'.$model->itnaryId;
                $model->employeeId=$this->employeeId;
                $model->createdBy=$this->employeeId;
                $model->createdDt=Helper::getcurrentExpressionDate();
                $model->fromDt=Helper::getExpressionDate($model->fromDt);
                $model->toDt=Helper::getExpressionDate($model->toDt);
                $model->status='E';
                $model->lockedFlag='N';
                
                $this->repository->add($model);
                
                
//             to add itnary employees 
             $itnaryMemberModel = new ItnaryMembers();
             $itnaryMemberModel->itnaryId=$model->itnaryId;
             $itnaryMemberModel->status='E';
             
             $travelRequestModel=new TravelRequest();
             $travelRequestModel->itnaryId=$model->itnaryId;
             $travelRequestModel->requestedDate=$model->createdDt;
             $travelRequestModel->fromDate=$model->fromDt;
             $travelRequestModel->toDate=$model->toDt;
             $travelRequestModel->departure='ITNARY';
             $travelRequestModel->destination=$model->purpose;
             $travelRequestModel->purpose=$model->purpose;
             $travelRequestModel->requestedType='ad';
             $travelRequestModel->requestedAmount=$model->floatMoney;
             $travelRequestModel->remarks=$model->remarks;
             $travelRequestModel->status='RQ';
             $travelRequestModel->transportType=$model->transportType;
             
            $travelRequestRepo=new TravelRequestRepository($this->adapter);
             
            foreach($postData['employeeId'] as $empList){
                $itnaryMemberModel->employeeId=$empList;
                    $this->repository->addItnaryMembers($itnaryMemberModel);
                    
    //             for tavel request
                $travelRequestModel->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequest::TABLE_NAME, TravelRequest::TRAVEL_ID)) + 1; 
                $travelRequestModel->travelCode = $model->itnaryCode;
                $travelRequestModel->employeeId= $empList;
                if($empList != $this->employeeId){
                     $travelRequestModel->requestedAmount = 0;
                }
                $travelRequestRepo->add($travelRequestModel);
                try {
                    HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $travelRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
                $travelRequestModel->requestedAmount = $model->floatMoney;
            }
            
//            to add itnary details
            $detailCount=count($postData['depDate']);
              $itnaryDetailModel = new ItnaryDetails();
             $itnaryDetailModel->itnaryId=$model->itnaryId;
            for($i=0; $i<$detailCount; $i++)
            {
                $itnaryDetailModel->departureDt=Helper::getExpressionDate($postData['depDate'][$i]);
                $itnaryDetailModel->departureTime=$postData['depTime'][$i];
                $itnaryDetailModel->locationFrom=$postData['locFrom'][$i];
                $itnaryDetailModel->locationTo=$postData['locto'][$i];
                $itnaryDetailModel->transportType=$postData['mot'][$i];
                $itnaryDetailModel->arriveDt=Helper::getExpressionDate($postData['arrDate'][$i]);
                $itnaryDetailModel->arriveTime=$postData['arrTime'][$i];
                $itnaryDetailModel->remarks=$postData['detRemarks'][$i];
                $itnaryDetailModel->sno=($i+1);
                
                $this->repository->addItnaryDetails($itnaryDetailModel);
            }
//            echo '<pre>';
//            print_r($postData);
//            die();
            
            
//                $model->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
//                $model->requestedDate = Helper::getcurrentExpressionDate();
////                $model->status = 'RQ';
//                $model->deductOnSalary = 'Y';
//                $model->status = ($postData['applyStatus'] == 'AP') ? 'AP' : 'RQ';
//
//                if ($model->status == 'AP') {
//                    $model->hardcopySignedFlag = 'Y';
//                }
//
//                $this->travelRequesteRepository->add($model);
//                $this->flashmessenger()->addMessage("Travel Request Successfully added!!!");
//
//
//                if ($travelSubstitute !== null) {
//                    $travelSubstituteModel = new TravelSubstitute();
//                    $travelSubstituteRepo = new TravelSubstituteRepository($this->adapter);
//
//                    $travelSubstitute = $postData->travelSubstitute;
//
//                    $travelSubstituteModel->travelId = $model->travelId;
//                    $travelSubstituteModel->employeeId = $travelSubstitute;
//                    $travelSubstituteModel->createdBy = $this->employeeId;
//                    $travelSubstituteModel->createdDate = Helper::getcurrentExpressionDate();
//                    $travelSubstituteModel->status = 'E';
//
//                    if (isset($this->preference['travelSubCycle']) && $this->preference['travelSubCycle'] == 'N') {
//                        $travelSubstituteModel->approvedFlag = 'Y';
//                        $travelSubstituteModel->approvedDate = Helper::getcurrentExpressionDate();
//                    }
//
//                    $travelSubstituteRepo->add($travelSubstituteModel);
//                    if (!isset($this->preference['travelSubCycle']) OR ( isset($this->preference['travelSubCycle']) && $this->preference['travelSubCycle'] == 'Y')) {
//                        try {
//                            HeadNotification::pushNotification(NotificationEvents::TRAVEL_SUBSTITUTE_APPLIED, $model, $this->adapter, $this);
//                        } catch (Exception $e) {
//                            $this->flashmessenger()->addMessage($e->getMessage());
//                        }
//                    } else {
//                        try {
//                            HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this);
//                        } catch (Exception $e) {
//                            $this->flashmessenger()->addMessage($e->getMessage());
//                        }
//                    }
//                } else {
//                    try {
//                        HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this);
//                    } catch (Exception $e) {
//                        $this->flashmessenger()->addMessage($e->getMessage());
//                    }
//                }
                return $this->redirect()->toRoute("travelItnary");
            }
        }
        $requestType = array(
            'ad' => 'Advance'
        );
        
        $transportTypes = EntityHelper::getTableList($this->adapter, 'HRIS_TRANSPORT_TYPES', ['TRANSPORT_ID','TRANSPORT_NAME','TRANSPORT_CODE'], null);
        
        
        print_r($transportTypes);die;
        $applyOptionValues = [
            'RQ' => 'Pending',
            'AP' => 'Approved'
        ];
        $applyOption = $this->getSelectElement(['name' => 'applyStatus', 'id' => 'applyStatus', 'class' => 'form-control', 'label' => 'Type'], $applyOptionValues);

        return Helper::addFlashMessagesToArray($this, [
                    'selfId' => $this->employeeId,
                    'selfName' => $this->storageData['employee_detail']['EMPLOYEE_CODE']. '-' . $this->storageData['employee_detail']['FULL_NAME'],
                    'form' => $this->form,
                    'requestTypes' => $requestType,
                    'transportTypes' => $transportTypes,
                    'applyOption' => $applyOption,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FULL_NAME", "ASC", "-", false, true, $this->employeeId),
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, 'FULL_NAME'=>"EMPLOYEE_CODE||'-'||FULL_NAME"], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"])
        ]);
    }
    
    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("travelItnary");
        }

        $itnaryDtl = $this->repository->fetchItnary($id, $this->employeeId);
        $itnaryMembersDtl = $this->repository->fetchItnaryMembers($id);
        $itnaryTravelDtl = $this->repository->fetchItnaryDetails($id);
        

//        if($this->preference['displayHrApproved'] == 'Y' && $detail['HARDCOPY_SIGNED_FLAG'] == 'Y'){
//            $detail['APPROVER_ID'] = '-1';
//            $detail['APPROVER_NAME'] = 'HR';
//            $detail['RECOMMENDER_ID'] = '-1';
//            $detail['RECOMMENDER_NAME'] = 'HR';
//        }
//        //$fileDetails = $this->repository->fetchAttachmentsById($id);
        $model = new TravelItnary();
        $model->exchangeArrayFromDB($itnaryDtl);
        $this->form->bind($model);
        
//        echo '<pre>';
//        print_r($itnaryDtl);
//        print_r($itnaryMembersDtl);
//        print_r($itnaryTravelDtl);
//        die();

//        $numberInWord = new NumberHelper();
//        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        
        $transportTypes = EntityHelper::getTableList($this->adapter, 'HRIS_TRANSPORT_TYPES', ['TRANSPORT_ID','TRANSPORT_NAME','TRANSPORT_CODE'], null);
        $transportTypesKv = EntityHelper::getTableKVList($this->adapter, 'HRIS_TRANSPORT_TYPES', 'TRANSPORT_CODE', ['TRANSPORT_NAME']);
        
        return Helper::addFlashMessagesToArray($this, [
//             'selfId' => $this->employeeId,
//                    'selfName' => $this->storageData['employee_detail']['FULL_NAME'],
                    'form' => $this->form,
//                    'recommender' => $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'],
//                    'approver' => $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'],
                    'transportTypesKv' => $transportTypesKv,
                    'transportTypes' => $transportTypes,
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, 'FULL_NAME'=>"EMPLOYEE_CODE||'-'||FULL_NAME"], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"]),
                    'itnaryDtl' => $itnaryDtl,
                    'itnaryMembersDtl' => $itnaryMembersDtl,
                    'itnaryTravelDtl' => $itnaryTravelDtl,
//                    'todayDate' => date('d-M-Y'),
//                    'advanceAmount' => $advanceAmount
                        //'files' => $fileDetails
        ]);
    }
        

}
