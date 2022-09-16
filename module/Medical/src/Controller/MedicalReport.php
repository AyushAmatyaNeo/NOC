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

class MedicalReport extends HrisController {

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

    public function pullBalanceAction() {
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

            $results = $this->repository->fetchMedicalBalance($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId);

            $result = [];
            $result['success'] = true;
            $result['data'] = Helper::extractDbData($results);
            $result['error'] = "";
            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullEmpMedicalDetailAction($employeeId) {
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

    public function transactionRepAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
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

                $result = $this->repository->fetchTransactionList($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId, $fromDate, $toDate);
                $total = $this->repository->fetchTransactionTotal($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId, $fromDate, $toDate);

                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'total' => $total, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'employeeId' => $this->employeeId,
        ]);
    }

    public function voucherAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
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

                $result = $this->repository->fetchVoucherList($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId, $fromDate, $toDate);
                $total = $this->repository->fetchTransactionTotal($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId, $fromDate, $toDate);

                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'total' => $total, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'employeeId' => $this->employeeId,
        ]);
    }

}
