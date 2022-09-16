<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Model\CustContractEmp;
use Customer\Model\Customer;
use Customer\Model\DutyTypeModel;
use Customer\Repository\ContractAttendanceRepo;
use Customer\Repository\CustContractEmpRepo;
use Customer\Repository\CustomerContractRepo;
use Customer\Repository\CustomerLocationRepo;
use Exception;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class ContractEmployees extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CustContractEmpRepo::class);
    }

    public function indexAction() {

        $employeeListSql = "select E.EMPLOYEE_ID,''||E.EMPLOYEE_CODE||'  '||E.FULL_NAME AS FULL_NAME 
            from  HRIS_EMPLOYEES E
            LEFT JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=E.DESIGNATION_ID)
            where E.status='E' and E.RESIGNED_FLAG='N'";


        $employeeDetails = EntityHelper::rawQueryResult($this->adapter, $employeeListSql);
        $employeeList = Helper::extractDbData($employeeDetails);


        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    'customerList' => EntityHelper::getTableList($this->adapter, Customer::TABLE_NAME, [Customer::CUSTOMER_ID, Customer::CUSTOMER_ENAME], [Customer::STATUS => "E"]),
                    'designationList' => EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE], [Designation::STATUS => "E"]),
                    'dutyTypeList' => EntityHelper::getTableList($this->adapter, DutyTypeModel::TABLE_NAME, [DutyTypeModel::DUTY_TYPE_ID, DutyTypeModel::DUTY_TYPE_NAME], [DutyTypeModel::STATUS => "E"]),
                    'employeeList' => $employeeList
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("contract-attendance");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $contractAttendnceRepo = new ContractAttendanceRepo($this->adapter);
                $result = $contractAttendnceRepo->fetchById($id);
                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        $customerContractRepo = new CustomerContractRepo($this->adapter);
        $customerContractDetails = $customerContractRepo->fetchById($id);

        $customerId = $customerContractDetails['CUSTOMER_ID'];

        $customerLocationRepo = new CustomerLocationRepo($this->adapter);
        $locationList = $customerLocationRepo->fetchAllLocationByCustomer($customerId);

        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E"]),
                    'customerContractDetails' => $customerContractDetails,
//                    'contractEmpDetails' => $custEmployeeDetails,
                    'customerId' => $customerId,
                    'locationList' => $locationList
        ]);
    }

    public function employeeAssignAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();


            echo '<Pre>';
            print_r($postData);
            die();

            $customerId = $request->getPost('customerId');
            $contractId = $request->getPost('contractId');
            $employeedesignation = $request->getPost('designation');
            $employees = $request->getPost('employee');
            $employeeLocation = $request->getPost('location');
            $employeeStartTime = $request->getPost('employeeStartTime');
            $employeeEndTime = $request->getPost('employeeEndTime');



            if ($employees) {
                $i = 0;
                $custEmployeeModel = new CustContractEmp();
                $custEmployeeModel->contractId = $contractId;

                $custEmployeeModel->status = 'D';
                $custEmployeeModel->modifiedDt = Helper::getcurrentExpressionDate();
                $custEmployeeModel->modifiedBy = $this->employeeId;



//                echo'<Pre>';
//                print_r($custEmployeeModel);
//                die();
                //to delete old assigned
                $this->repository->edit($custEmployeeModel, $contractId);


                $custEmployeeRepo = new CustContractEmpRepo($this->adapter);
                $custEmployeeModel->customerId = $customerId;
                $custEmployeeModel->lastAssignedDate = Helper::getCurrentDate();

                foreach ($employees as $employeeDetails) {
                    if ($employeeDetails > 0) {
                        $custEmployeeModel->employeeId = $employeeDetails;
                        $custEmployeeModel->designationId = $employeedesignation[$i];
                        $custEmployeeModel->locationId = $employeeLocation[$i];
                        $custEmployeeModel->startTime = Helper::getExpressionTime($employeeStartTime[$i]);
                        $custEmployeeModel->endTime = Helper::getExpressionTime($employeeEndTime[$i]);
                        $custEmployeeModel->status = 'E';
                        $custEmployeeModel->modifiedDt = NULL;
                        $custEmployeeModel->modifiedDt = NULL;

                        $custEmployeeRepo->add($custEmployeeModel);
                    }
                    $i++;
                }
            }


            $this->flashmessenger()->addMessage("Contract Employee updated successfully.");
            $this->redirect()->toRoute("contract-employees", ["action" => "index"]);
//            return $this->redirect()->toRoute("contract-employees", ["action" => "view", "id" => $id]);
        }
    }

    public function monthWiseEmployeeListAction() {

        try {
            $id = (int) $this->params()->fromRoute("id");
            if ($id === 0) {
                throw new Exception('id is undefined');
            }
            $request = $this->getRequest();
            $postData = $request->getPost();
            $monthId = $request->getPost('monthId');
            $employeeDetails = $this->repository->getAllMonthWiseEmployees($id, $monthId);
            return new JsonModel(['success' => true, 'data' => $employeeDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchAllContractCustomerWiseAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();
            $cutomerId = $request->getPost('customerId');
            if (!$cutomerId) {
                throw new Exception('no CustomerId passed');
            }
            $contractDetails = EntityHelper::getTableList($this->adapter, \Customer\Model\CustomerContract::TABLE_NAME, [\Customer\Model\CustomerContract::CONTRACT_ID, \Customer\Model\CustomerContract::CONTRACT_NAME], [\Customer\Model\CustomerContract::CUSTOMER_ID => $cutomerId, \Customer\Model\CustomerContract::STATUS => "E"]);
            return new JsonModel(['success' => true, 'data' => $contractDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchContractDetailsAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();
            $contractId = $request->getPost('contractId');
            if (!$contractId) {
                throw new Exception('no ContractId passed');
            }
            $contractDetailRepo = new \Customer\Repository\CustomerContractDetailRepo($this->adapter);
            $contractDetails = $contractDetailRepo->fetchAllContractDetailByContractId($contractId);
            $locationList = [];


            $customerId = $contractDetails[0]['CUSTOMER_ID'];
            if ($customerId > 0) {
                $customerLocationRepo = new CustomerLocationRepo($this->adapter);
                $locationList = $customerLocationRepo->fetchAllLocationByCustomer($customerId);
            }
            $returnData = [];
            $returnData['locationList'] = $locationList;
            $returnData['contractDetails'] = $contractDetails;
            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchContractDesginationWiseEmployeeAssignAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();

            $contractId = $request->getPost('contractId');
            $designationId = $request->getPost('designationId');
            $dutyTypeId = $request->getPost('dutyTypeId');

            $employeeData = $this->repository->getEmployeeAssignedDesignationWise($contractId, $designationId, $dutyTypeId);

            return new JsonModel(['success' => true, 'data' => $employeeData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchContractWiseEmployeeAssignAction() {
        try {
            $request = $this->getRequest();
            $contractId = $request->getPost('contractId');
            $customerId = $request->getPost('customerId');

            $customerLocationRepo = new CustomerLocationRepo($this->adapter);
            $locationList = $customerLocationRepo->fetchAllLocationByCustomer($customerId);

            $employeeData = $this->repository->getEmployeeAssignedContractWise($contractId);

            $customerContractRepo = new CustomerContractRepo($this->adapter);
            $contractDetails = $customerContractRepo->fetchById($contractId);

            $returnData['contractDetails'] = $contractDetails;
            $returnData['locationList'] = $locationList;
            $returnData['empDetails'] = $employeeData;
            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchContractEmpLocDesWiseAction() {
        try {
            $request = $this->getRequest();

            $contractId = $request->getPost('contractId');
            $employeeId = $request->getPost('employeeId');
            $locationId = $request->getPost('locationId');
            $designationId = $request->getPost('designationId');


            $returnData = $this->repository->getContractEmpLocDesWise($contractId, $employeeId, $locationId, $designationId);

            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function addContractEmpAssignAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();

            $customer = $request->getPost('customer');
            $contract = $request->getPost('contract');
            $designation = $request->getPost('designation');
            $dutyType = $request->getPost('dutyType');
            $rate = $request->getPost('rate');



            $contractDetails = $this->repository->getContractDetail($contract, $designation, $dutyType);
            if (!$contractDetails) {
                $contractDetailModel = new \Customer\Model\CustomerContractDetailModel();
                $contractDetialRepo = new \Customer\Repository\CustomerContractDetailRepo($this->adapter);

                $contractDetailModel->customerId = $customer;
                $contractDetailModel->contractId = $contract;
                $contractDetailModel->designationId = $designation;
                $contractDetailModel->dutyTypeId = $dutyType;
                $contractDetailModel->status = 'E';
                $contractDetailModel->createdBy = $this->employeeId;
                $contractDetailModel->rate = $rate;
                $contractDetialRepo->add($contractDetailModel);
            }

            return new JsonModel(['success' => true, 'data' => [], 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function deleteAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();



            $id = $request->getPost('id');
            $contract = $request->getPost('contract');
            $designation = $request->getPost('designation');
            $dutyType = $request->getPost('dutyType');

            $this->repository->delete($id);

            $contractDetailModel = new \Customer\Model\CustomerContractDetailModel();
            $contractDetialRepo = new \Customer\Repository\CustomerContractDetailRepo($this->adapter);

            $contractDetails = $this->repository->getContractDetail($contract, $designation, $dutyType);


            if ($contractDetails['QUANTITY'] > 1) {
                $contractDetailModel->quantity = $contractDetails['QUANTITY'] - 1;
                $contractDetialRepo->editWithCondition($contractDetailModel, array(
                    $contractDetailModel::STATUS => 'E',
                    $contractDetailModel::CONTRACT_ID => $contract,
                    $contractDetailModel::DESIGNATION_ID => $designation,
                    $contractDetailModel::DUTY_TYPE_ID => $dutyType
                ));
            } else {
                $contractDetailModel->status = 'D';
                $contractDetialRepo->editWithCondition($contractDetailModel, array(
                    $contractDetailModel::STATUS => 'E',
                    $contractDetailModel::CONTRACT_ID => $contract,
                    $contractDetailModel::DESIGNATION_ID => $designation,
                    $contractDetailModel::DUTY_TYPE_ID => $dutyType
                ));
            }



            return new JsonModel(['success' => true, 'data' => [], 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function updateAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();

            $id = $request->getPost('id');
            $contractId = $request->getPost('contractId');
            $customerId = $request->getPost('customerId');
            $locationId = $request->getPost('locationId');
            $employeeId = $request->getPost('employeeId');
            $designationId = $request->getPost('designationId');
            $dutyTypeId = $request->getPost('dutyTypeId');
            $startTime = $request->getPost('startTime');
            $endTime = $request->getPost('endTime');
            $startDate = $request->getPost('startDate');
            $endDate = $request->getPost('endDate');
            $monthlyRate = $request->getPost('monthlyRate');


            $custEmployeeModel = new CustContractEmp();


            $custEmployeeModel->employeeId = $employeeId;
            $custEmployeeModel->locationId = $locationId;
            $custEmployeeModel->startDate = Helper::getExpressionDate($startDate);
            $custEmployeeModel->endDate = Helper::getExpressionDate($endDate);
            $custEmployeeModel->startTime = Helper::getExpressionTime($startTime);
            $custEmployeeModel->endTime = Helper::getExpressionTime($endTime);
            $custEmployeeModel->monthlyRate = $monthlyRate;


            if ($id > 0) {
                $returnData['operation'] = 'edit';
                $custEmployeeModel->modifiedBy = $this->employeeId;
                $custEmployeeModel->modifiedDt = Helper::getcurrentExpressionDate();
                $this->repository->edit($custEmployeeModel, $id);
            } else {
                $returnData['operation'] = 'add';
                $custEmployeeModel->contractId = $contractId;
                $custEmployeeModel->customerId = $customerId;
                $custEmployeeModel->designationId = $designationId;
                $custEmployeeModel->dutyTypeId = $dutyTypeId;
                $custEmployeeModel->empAssignId = (int) Helper::getMaxId($this->adapter, CustContractEmp::TABLE_NAME, CustContractEmp::EMP_ASSIGN_ID) + 1;
                $custEmployeeModel->createdBy = $this->employeeId;
                $custEmployeeModel->status = 'E';
                $this->repository->add($custEmployeeModel);
                $returnData['id'] = $custEmployeeModel->empAssignId;
            }

            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullCdContractDesignationDutyTypeAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();

            $contract = $request->getPost('contract');
            $designation = $request->getPost('designation');
            $dutyType = $request->getPost('dutyType');

            $contractDetails = $this->repository->getContractDetail($contract, $designation, $dutyType);

            return new JsonModel(['success' => true, 'data' => $contractDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullEmployeeAssignDataByIdAction() {
        try {
            $request = $this->getRequest();

            $id = $request->getPost('id');

            $details = $this->repository->pullEmployeeAssignDataById($id);
            return new JsonModel(['success' => true, 'data' => $details, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullEmployeeRateAction() {
        try {
            $request = $this->getRequest();

            $dutyTypeId = $request->getPost('dutyTypeId');
            $employeeId = $request->getPost('employeeId');


            $data = $this->repository->pullEmployeeRate($dutyTypeId, $employeeId);

            $returnAmt = 'N/A';

            if ($data['HOURLY_AMOUNT'] != '' && $data['NORMAL_HOUR'] != '') {
                $returnAmt = ($data['NORMAL_HOUR'] / 60) * $data['HOURLY_AMOUNT'] * 30;
            }

            return new JsonModel(['success' => true, 'data' => $returnAmt, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
