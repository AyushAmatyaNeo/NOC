<?php

namespace ManagerService\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Exception;
use ManagerService\Repository\TransferSettlementApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use Payroll\Model\FinanceData;
use Payroll\Repository\FinanceDataRepository;
use SelfService\Model\TravelRequest;
use SelfService\Model\TravelRequest as TravelRequestModel;
use SelfService\Model\TransferSettlement as TransferSettlementModel;
use SelfService\Repository\TravelExpenseDtlRepository;
use Travel\Repository\TravelItnaryRepository;
use SelfService\Form\TravelRequestForm;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use SelfService\Model\TravelExpenses as TravelExpensesModel;
use SelfService\Repository\TravelExpensesRepository;

class TransferSettlementApproveController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TransferSettlementApproveRepository::class);
        $this->initializeForm(TravelRequestForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $search['employeeId'] = $this->employeeId;
                $search['status'] = ['RQ', 'RC'];
                $rawList = $this->repository->getPendingList();
                $list = Helper::extractDbData($rawList);
                for($i=0; $i<count($list); $i++){
                    $transportTypeListArray = explode (",", $list[$i]['TRANSPORT_TYPE_LIST']);
                    

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

    public function expenseDetailAction() {
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("transferSettlementApprove");
        }
        $serialNumber = (int) $this->params()->fromRoute('role');

        if ($serialNumber === 0) {
            return $this->redirect()->toRoute("transferSettlementApprove");
        }
        $detail = $this->repository->getTransferDetails($id);


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

        $totalNoOfAttachment = $this->repository->getTotalNoOfAttachment($id, $serialNumber);
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
    
    public function statusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $searchQuery = $request->getPost();
                $search['employeeId'] = $this->employeeId;
                $search['status'] = ['RQ', 'RC'];
                $rawList = $this->repository->getStatusList((array) $searchQuery);
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
            'travelStatus' => $statusSE,
        ]);
    }

    public function batchApproveRejectAction() {
        $request = $this->getRequest();
        try {
            $postData = $request->getPost();
            $transferSettlementModel = new TransferSettlementModel();
            $status = 'AP';
            
            if($postData['btnAction'] == "btnApprove"){
                $status = 'AP';
            }else if($postData['btnAction'] == "btnReject"){
                $status = 'R';
            }
            $result = $this->repository->fetchByJobHistoryId($postData['id'], $postData['role']);
            foreach ($result as $row) {
                $transferSettlementModel->status = $status;
                $transferSettlementModel->approvedBy = $this->employeeId;
                $transferSettlementModel->approvedDt = Helper::getcurrentExpressionDate();
                $this->repository->edit($transferSettlementModel, $row['TRANSFER_SETTLEMENT_ID']);
            }
            if($status=='AP'){
                $this->flashmessenger()->addMessage("Successfully Approved!!!");
            }else{
                $this->flashmessenger()->addMessage("Successfully Rejected!!!");
            }
            return new JsonModel(['success' => true, 'data' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function jvAddAction() {
        
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("transferSettlementApprove");
        }
        $serialNumber = (int) $this->params()->fromRoute('role');

        if ($serialNumber === 0) {
            return $this->redirect()->toRoute("transferSettlementApprove");
        }
        $detail = $this->repository->getTransferDetails($id);

        $bankList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_BANKS", "BANK_ID", ["BANK_NAME"], ["STATUS" => 'E'], "BANK_ID", "ASC", "-");
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

        $totalNoOfAttachment = $this->repository->getTotalNoOfAttachment($id, $serialNumber);
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
                $this->repository->edit($transferSettlementModel, $postedData['transferSettlementId'][$i]);
            }
            $this->flashmessenger()->addMessage("Successfully Added JV Number!!!");
            return $this->redirect()->toRoute("transferSettlementApprove");
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

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("transferSettlementApprove");
        }

        $serialNumber = (int) $this->params()->fromRoute('role');

        if ($serialNumber === 0) {
            return $this->redirect()->toRoute("transferSettlementApprove");
        }

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

        $request = $this->getRequest();
        $model = new TravelExpensesModel();
        $transferSettlementModel = new TransferSettlementModel();
        $repo = new TravelExpensesRepository($this->adapter);

        $detail = $this->repository->getTransferDetails($id);
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
                    $this->repository->edit($transferSettlementModel, $postData['transferSettlementId'][$i]);
                }
            }
            $error = "";

            $this->flashmessenger()->addMessage("Successfully Edited!!!");
                return $this->redirect()->toRoute("transferSettlementApprove");
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
                    $this->repository->edit($transferSettlementModel, $postedData['transferSettlementId'][$i]);
                }
            }
            if($status='AP'){
                $this->flashmessenger()->addMessage("Successfully Approved!!!");
            }else{
                $this->flashmessenger()->addMessage("Successfully Rejected!!!");
            }
            return $this->redirect()->toRoute("transferSettlementApprove");
        }
    }

    public function pullFilebyIdAction()
    {

        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // $fileLinkId = $this->repository->getFileLinkId($data->id);
            $returnData = $this->repository->pullFilebyId($data->id, $data->serialNumber);

            return new JsonModel(['success' => true, 'data' => $returnData, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
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

}


