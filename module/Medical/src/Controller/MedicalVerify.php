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

class MedicalVerify extends HrisController {

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
            if ($action == "Reject") {
                $medical->billStatus = "C";
                $this->flashmessenger()->addMessage("Medical Bill Rejected!!!");
            } else if ($action == "Approve") {
                $medical->billStatus = "AP";
                $this->flashmessenger()->addMessage("Medical Bill Approved");
            }

            $medical->approvedAmt = $getData->approvedAmt;
            $medical->remarks = $getData->remarks;
            $medical->approvedBy = $this->employeeId;
            $medical->approvedDt = Helper::getcurrentExpressionDate();

            $this->repository->edit($medical, $id);
            return $this->redirect()->toRoute("medicalVerify");
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

    public function bulkAction() {
        $request = $this->getRequest();
        try {
            $postData = $request->getPost();
            $this->makeDecision($postData['id'], $postData['action']);
            return new JsonModel(['success' => true, 'data' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function makeDecision($id, $approve) {
        $detail = $this->repository->fetchById($id);
        $status = $detail['BILL_STATUS'];
        $requestedAmt = $detail['REQUESTED_AMT'];
        if ($status == 'RQ') {
            $medical = new Medical();
            if ($approve == "reject") {
                $medical->billStatus = "C";
            } else if ($approve == "approve") {
                $medical->billStatus = "AP";
                $medical->approvedAmt = $requestedAmt;
                $medical->approvedBy = $this->employeeId;
                $medical->approvedDt = Helper::getcurrentExpressionDate();
            }
            $this->repository->edit($medical, $id);
        }
    }

}
