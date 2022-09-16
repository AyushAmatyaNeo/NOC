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
use SelfService\Model\TransferSettlement as TransferSettlementModel;
use SelfService\Model\TravelSubstitute;
use SelfService\Repository\TravelExpenseDtlRepository;
use SelfService\Repository\TransferSettlementRepository;
use SelfService\Repository\TravelSubstituteRepository;
use Setup\Model\HrEmployees;
use Travel\Repository\TravelItnaryRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use SelfService\Model\TravelExpenses as TravelExpensesModel;
use SelfService\Repository\TravelExpensesRepository;

class TransferSettlement extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TransferSettlementRepository::class);
        $this->initializeForm(TravelRequestForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                if($data['startDate']){
                    $data['startDate'] = Helper::getExpressionDate($data['startDate']);
                }
                if($data['endDate']){
                    $data['endDate'] = Helper::getExpressionDate($data['endDate']);
                }
                if($data['eventDate']){
                    $data['eventDate'] = Helper::getExpressionDate($data['eventDate']);
                }
                // print_r($data);die;
                $data['employeeId'] = $this->employeeId;
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
                    'employeeId' => $this->employeeId
        ]);
    }


    public function deleteAction() {
        $jobHistoryId = (int) $this->params()->fromRoute('id');
        $serialNumber = (int) $this->params()->fromRoute('role');
        if (!$jobHistoryId) {
            return $this->redirect()->toRoute('transferSettlement');
        }
        $this->repository->cancelTransferSettlement($jobHistoryId,$serialNumber);
        $this->flashmessenger()->addMessage("Transfer Settlement Successfully Cancelled!!!");
        return $this->redirect()->toRoute('transferSettlement');
    }

    public function expenseAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $data['employeeId'] = $this->employeeId;
                $rawList = $this->repository->getFilteredRecordsExpense($data);
                $list = Helper::extractDbData($rawList);
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
                    $transferSettlementModel->employeeId = $this->employeeId;
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
                    $transferSettlementModel->createdBy = $this->employeeId;
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
                return $this->redirect()->toRoute("transferSettlement");
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

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id');
        $serialNumber = (int) $this->params()->fromRoute('role');
        if ($id === 0) {
            return $this->redirect()->toRoute("transferSettlement");
        }
        if ($serialNumber === 0) {
            return $this->redirect()->toRoute("transferSettlement");
        }
        $request = $this->getRequest();
        $detail = $this->repository->getTransferDetails($id);

        $transferSettlementModel = new TransferSettlementModel();
        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $expenseDtlList = [];
        $result = $this->repository->fetchByJobHistoryId($id, $serialNumber);
        $allExpenseDetail = [];
        $totalAmount = 0;
        $grandTotal = 0;

        

        foreach ($result as $row) {
            $row['TOTAL'] = $row['TOTAL_TADA_AMT'] + $row['PLANE_EXPENSE_AP_AMT'] + $row['VEHICLE_EXPENSE_AP_AMT'] + $row['MISC_EXPENSE_AP_AMT'];
            $grandTotal = $grandTotal + $row['TOTAL'];
            array_push($allExpenseDetail, $row);
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
        $totalNoOfAttachment = $this->repository->getTotalNoOfAttachment($id, $serialNumber);
        $classId = $this->repository->getClassIdFromEmpId($this->employeeId);
        $configId = $this->repository->getCongifId($classId);
        $configDetails = $this->repository->getConfigDetails($configId);
        if ($request->isPost()) {
            $jobHistoryId = $id;
            $postData = $request->getPost()->getArrayCopy();
            $this->repository->deletePreviousRecordsForEdit($jobHistoryId,$serialNumber);
            $this->repository->deletePreviousFilesRecordsForEdit($jobHistoryId,$serialNumber);
            $numberOfFamily = 0;
            if ($postData['depDate'][0] != null){
                for ($i = 0; $i < count($postData['depDate']); $i++){
                    if($postData['isForFamily'][$i] == 'Y'){
                        $numberOfFamily = $numberOfFamily + 1;
                    }
                }
            }
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
                    $transferSettlementModel->employeeId = $this->employeeId;
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
                    $transferSettlementModel->createdBy = $this->employeeId;
                    $transferSettlementModel->miles = $postData['miles'][$i]?$postData['miles'][$i]:0;
                    $transferSettlementModel->miscExpenseDetail = $postData['otherExpenseDetail'][$i];
                    $transferSettlementModel->purpose = $postData['detPurpose'][$i];
                    $transferSettlementModel->transportClass = $postData['transportClass'][$i];
                    $transferSettlementModel->expenseCategory = $postData['mot'][$i];
                    $transferSettlementModel->transportation = $postData['transport'][$i];
                    $transferSettlementModel->status = 'RQ';
                    $transferSettlementModel->remarks = $postData['detRemarks'][$i];
                    $transferSettlementModel->familyName= $postData['familyName'][$i];
                    $transferSettlementModel->modifiedDt = Helper::getcurrentExpressionDate();
                    $transferSettlementModel->modifiedBy = $this->employeeId;
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

            $this->flashmessenger()->addMessage("Successfully Editted!!!");
                return $this->redirect()->toRoute("transferSettlement", ['action' => 'expense']);
        }
        $transportTypes = ['On Foot'=>'On Foot', 'Office Vehicle' => 'Office Vehicle', 'Train' => 'Train', 'Airplane' => 'Airplane', 'Cruise' => 'Cruise', 'Taxi' => 'Taxi', 'Bus' => 'Bus'];

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
                    'configDetails' => $configDetails,
                    'serialNumber' => $serialNumber,
                    'transportTypes' => $transportTypes,
        ]);
    }

    public function expenseViewAction() {
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("transferSettlement");
        }

        $serialNumber = (int) $this->params()->fromRoute('role');
        if ($serialNumber === 0) {
            return $this->redirect()->toRoute("transferSettlement");
        }

        $detail = $this->repository->getTransferDetails($id);


        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $expenseDtlList = [];
        $result = $this->repository->fetchByJobHistoryId($id, $serialNumber);
        $allExpenseDetail = [];
        $totalAmount = 0;
        $grandTotal = 0;
        foreach ($result as $row) {
        // print_r($row);die;
            $row['TOTAL'] = $row['TOTAL_TADA_AMT'] + $row['PLANE_EXPENSE_AP_AMT'] + $row['VEHICLE_EXPENSE_AP_AMT'] + $row['MISC_EXPENSE_AP_AMT'];
            $grandTotal = $grandTotal + $row['TOTAL'];
            array_push($allExpenseDetail, $row);
        }
        // print_r($allExpenseDetail);die;
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
            $totalInternationalAmount += ($row['TOTAL']*$row['EXCHANGE_RATE']);
            array_push($internationalExpenseDtlList, $row);
        }
        // print_r($id);die;
        $totalNoOfAttachment = $this->repository->getTotalNoOfAttachment($id, $serialNumber);
        //    print_r($allExpenseDetail);die;
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
            $returnData = $this->repository->pullFilebyId($data->id, $data->serialNumber);

            return new JsonModel(['success' => true, 'data' => $returnData, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
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
