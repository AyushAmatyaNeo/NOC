<?php

namespace Medical\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Medical\Form\MedicalForm;
use Medical\Model\Medical;
use Medical\Repository\MedicalRepo;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRelationRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\View\Model\JsonModel;

class MedicalEntry extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(MedicalRepo::class);
        $this->initializeForm(MedicalForm::class);
    }

    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'employeeId' => $this->employeeId,
                    'status' => $this->getMedicalStatusSelect()
        ]);
    }

    public function addAction() {

        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);

//                echo '<pre>';
//                print_r($this->form->isValid());
//                die();
            if ($this->form->isValid()) {

                $medicalModel = new Medical();
                $medicalModel->exchangeArrayFromForm($this->form->getData());

                if ($medicalModel->eRId == -1) {
                    $medicalModel->eRId = NULL;
                }
                $medicalModel->requestedBy = $this->employeeId;
                $medicalModel->billStatus = 'RQ';
                $medicalModel->status = 'E';
                $medicalModel->medicalId = (int) Helper::getMaxId($this->adapter, Medical::TABLE_NAME, Medical::MEDICAL_ID) + 1;
                $totalRequestAmt = 0;
                foreach ($postData->billAmt as $amt) {
                    $totalRequestAmt += $amt;
                }
                $medicalModel->requestedAmt = $totalRequestAmt;

                $this->repository->add($medicalModel);
                $this->addBillDetails($medicalModel->medicalId, $postData->billNo, $postData->billDate, $postData->billAmt);

                $this->flashmessenger()->addMessage("Medial Entry Successfully added!!!");
                return $this->redirect()->toRoute("medicalEntry");
            }
        }
        
       
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
//                    'CuremployeeId' => $this->employeeId,
                    'customRenderer' => Helper::renderCustomView(),
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, 'FULL_NAME'=>"EMPLOYEE_CODE||'-'||FULL_NAME"], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"])
        ]);
    }

    public function pullEmployeeRelationAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $employeeId = (int) $data->employeeId;
            $repository = new EmployeeRelationRepo($this->adapter);
            $relationList = [];
            $result = $repository->getByEmpId($employeeId);
            foreach ($result as $row) {
                array_push($relationList, $row);
            }
            return new JsonModel([
                "success" => true,
                "data" => $relationList
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function addBillDetails($medicalNo, $billNo, $billDate, $billAmt) {
//        $returnValue = true;
//        try {
        $counter = 0;
        $medicalBillRepo = new \Medical\Repository\MedicalBillRepo($this->adapter);
        foreach ($billNo as $bill) {
            $medicalBillModel = new \Medical\Model\MedicalBill();
            $medicalBillModel->medicalId = $medicalNo;
            $medicalBillModel->billNO = $bill;
            $medicalBillModel->billDate = $billDate[$counter];
            $medicalBillModel->billAmt = $billAmt[$counter];
            $medicalBillRepo->add($medicalBillModel);
            $counter++;
        }
//        } catch (Exception $e) {
//            $returnValue = $e->getMessage();
//        }
//        return $returnValue;
    }

    public function pullMedicalListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
            $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
            $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
            $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
            $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
            $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
            $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
            $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
            $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
            $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
            $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
            $fromDate = $data['fromDate'];
            $toDate = $data['toDate'];
            $status = $data['status'];

            $results = $this->repository->filterRecord($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId, $fromDate, $toDate, $status);

            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($results);
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    private function getMedicalStatusSelect() {
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $status = array(
            "-1" => "All",
            "RQ" => "Requested",
            "AP" => "Approved",
            "PD" => "Paid",
            "C" => "Cancelled",
        );
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "statusId", "class" => "form-control reset-field"]);
        $statusFormElement->setLabel("Status");
        return $statusFormElement;
    }

    public function pullEmpMedicalDetailAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $employeeId = (int) $data->employeeId;
            $result = $this->repository->fetchEmpMedicalDetail($employeeId);
            return new JsonModel([
                "success" => true,
                "data" => $result
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
    
    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("medicalVerify");
        }
        $request = $this->getRequest();
        $detail = $this->repository->fetchById($id);
        $medical = new Medical();


        if ($request->isPost()) {
            $getData = $request->getPost();
            $action = $getData->submit;
            if ($action == "Cancel") {
                $medical->billStatus = "C";
                $this->flashmessenger()->addMessage("Medical Bill Cancelled!!!");
            } else if ($action == "Approve") {
                $medical->billStatus = "AP";
                $this->flashmessenger()->addMessage("Medical Bill Approved");
            }

            $medical->approvedAmt = $getData->approvedAmt;
            $medical->remarks = $getData->remarks;
            $medical->approvedBy = $this->employeeId;
            $medical->approvedDt = Helper::getcurrentExpressionDate();

            $this->repository->edit($medical, $id);
            return $this->redirect()->toRoute("medicalEntry");
        }
        $medicalBillRepo = new \Medical\Repository\MedicalBillRepo($this->adapter);
        $billDetails = Helper::extractDbData($medicalBillRepo->fetchById($id));
        $medical->exchangeArrayFromDB($detail);
        $this->form->bind($medical);

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'detail' => $detail,
                    'billDetail' => $billDetails,
                    'employeeList' => EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ", FALSE, TRUE),
        ]);
    }
    

}
