<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Model\Customer;
use Customer\Repository\ContractAttendanceRepo;
use Customer\Repository\CustomerLocationRepo;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class ContractAttendance extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(ContractAttendanceRepo::class);
//        $this->initializeForm(WagedEmployeeSetupForm::class);
    }

    public function indexAction() {

        $monthList = $this->repository->getMonthList();

        $employeeList = $this->repository->getEmployeeListWithCode();


        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    'customerList' => EntityHelper::getTableList($this->adapter, Customer::TABLE_NAME, [Customer::CUSTOMER_ID, Customer::CUSTOMER_ENAME], [Customer::STATUS => "E"]),
                    'employeeList' => $employeeList,
                    'monthList' => $monthList
        ]);
    }

    public function pullCustomerMonthlyAttendanceAction() {
        try {
            $request = $this->getRequest();
            $customerId = $request->getPost('customerId');
            $monthId = $request->getPost('monthId');
            $locationId = $request->getPost('locationId');

            $attendnaceDetails = $this->repository->getCutomerEmpAttendnaceMonthly($monthId, $customerId, $locationId);
            return new JsonModel(['success' => true, 'data' => $attendnaceDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function billPrintAction() {

        $monthList = $this->repository->getMonthList();

        return Helper::addFlashMessagesToArray($this, [
                    'customerList' => EntityHelper::getTableList($this->adapter, Customer::TABLE_NAME, [Customer::CUSTOMER_ID, Customer::CUSTOMER_ENAME], [Customer::STATUS => "E"]),
                    'monthList' => $monthList
        ]);
    }

    public function pullMonthlyBillCustomerWiseAction() {
        try {
            $request = $this->getRequest();
            $customerId = $request->getPost('customerId');
            $monthId = $request->getPost('monthId');




            $returnData['attendnaceDetails'] = $this->repository->pullMonthlyBillCustomerWise($monthId, $customerId);


//            $attendnaceDetails = $this->repository->pullMonthlyBillCustomerWise($monthId, $customerId);
            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullAttendanceAbsentDataAction() {
        try {
            $request = $this->getRequest();

            $monthStartDate = $request->getPost('monthStartDate');
            $column = $request->getPost('column');
            $customerId = $request->getPost('customerId');
            $contractId = $request->getPost('contractId');
            $employeeId = $request->getPost('employeeId');
            $locationId = $request->getPost('locationId');
            $dutyTypeId = $request->getPost('dutyTypeId');
            $designationId = $request->getPost('designationId');
            $empAssignId = $request->getPost('empAssignId');
            $startTime = $request->getPost('startTime');
            $endTime = $request->getPost('endTime');


            $returnData = $this->repository->pullAttendanceAbsentData($monthStartDate, $column, $customerId, $contractId, $employeeId, $locationId, $dutyTypeId, $designationId, $startTime, $endTime
                    , $empAssignId);
            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function updateAttendanceDataAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();




            $attendanceDate = $request->getPost('attendanceDate');
            $customerId = $request->getPost('customerId');
            $contractId = $request->getPost('contractId');
            $employeeId = $request->getPost('employeeId');
            $locationId = $request->getPost('locationId');
            $dutyTypeId = $request->getPost('dutyTypeId');
            $designationId = $request->getPost('designationId');
            $empAssignId = $request->getPost('empAssignId');
            $status = $request->getPost('stauts');
            $normalHour = $request->getPost('normalHour');
            $otHour = $request->getPost('otHour');
            $subEmployeeId = $request->getPost('subEmployeeId');
            $postingType = $request->getPost('postingType');
            $rate = $request->getPost('rate');
            $otRate = $request->getPost('otRate');
            $otType = $request->getPost('otType');


            $returnData = $this->repository->updateAttendanceData($attendanceDate, $customerId, $contractId, $employeeId, $locationId, $dutyTypeId, $designationId, $empAssignId, $status, $normalHour, $otHour, $subEmployeeId, $postingType, $rate, $otRate, $otType
            );

            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullCustomerLocationAction() {
        try {
            $request = $this->getRequest();
            $customerId = $request->getPost('customerId');
            $customerLocationRepo = new CustomerLocationRepo($this->adapter);
            $locationList = $customerLocationRepo->fetchAllLocationByCustomer($customerId);
            return new JsonModel(['success' => true, 'data' => $locationList, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function reportAction() {

        $monthList = $this->repository->getMonthList();

        $employeeList = $this->repository->getEmployeeListWithCode();


        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    'customerList' => EntityHelper::getTableList($this->adapter, Customer::TABLE_NAME, [Customer::CUSTOMER_ID, Customer::CUSTOMER_ENAME], [Customer::STATUS => "E"]),
                    'employeeList' => $employeeList,
                    'monthList' => $monthList
        ]);
    }

    public function pullCustomerMonthlyAttendanceReportAction() {
        try {
            $request = $this->getRequest();
            $customerId = $request->getPost('customerId');
            $monthId = $request->getPost('monthId');
            $locationId = $request->getPost('locationId');

            $attendnaceDetails = $this->repository->getCutomerEmpAttendnaceReportMonthly($monthId, $customerId, $locationId);
            return new JsonModel(['success' => true, 'data' => $attendnaceDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function empWiseReportAction() {

        $monthList = $this->repository->getMonthList();



        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    'monthList' => $monthList
        ]);
    }

    public function pullEmpWiseMonthlyReportAction() {
        try {
            $request = $this->getRequest();
            $monthId = $request->getPost('monthId');

            $attendnaceDetails = $this->repository->fetchEmpWiseMonthlyReport($monthId);
            return new JsonModel(['success' => true, 'data' => $attendnaceDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
