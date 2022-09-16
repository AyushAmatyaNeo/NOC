<?php

namespace Other\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Other\Repository\AllowanceAssignRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class AllowanceAssign extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AllowanceAssignRepository::class);
    }

    public function indexAction() {
        return $this->stickFlashMessagesTo([
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function getEmployeeListAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();

                $employeeId = $postedData['employeeId'];
                $branchId = $postedData['branchId'];
                $departmentId = $postedData['departmentId'];
                $designationId = $postedData['designationId'];
                $positionId = $postedData['positionId'];
                $serviceTypeId = $postedData['serviceTypeId'];
                $serviceEventTypeId = $postedData['serviceEventTypeId'];
                $companyId = $postedData['companyId'];
                $genderId = $postedData['genderId'];
                $employeeTypeId = $postedData['employeeTypeId'];

                $raw = $this->repository->filterEmployees($employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $companyId, $genderId, $employeeTypeId);
                $reportData = Helper::extractDbData($raw);
                return new JsonModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage(), 'errorDetail' => $e->getTraceAsString()]);
        }
    }

    public function getAllowanceAssignedEmployeesAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $allowanceId = $postedData['allowanceId'];

                $raw = EntityHelper::getTableKVList($this->adapter, 'HRIS_EMPLOYEE_ALLOWANCE_ASSIGN', null, ['EMPLOYEE_ID'], ['ALLOWANCE' => $allowanceId], null, null, null, null, TRUE);
                $reportData = Helper::extractDbData($raw);
                return new JsonModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
//
//    public function assignHolidayToEmployeesAction() {
//        try {
//            $request = $this->getRequest();
//            if ($request->isPost()) {
//                $postedData = $request->getPost();
//                $holidayId = $postedData['holidayId'];
//                if (!isset($postedData['employeeIdList'])) {
//                    $employeeIdList = [];
//                } else {
//                    $employeeIdList = $postedData['employeeIdList'];
//                }
//                $reportData = $this->repository->multipleEmployeeAssignToHoliday($holidayId, $employeeIdList);
//                return new JsonModel(['success' => true, 'data' => $reportData, 'error' => '']);
//            } else {
//                throw new Exception("The request should be of type post");
//            }
//        } catch (Exception $e) {
//            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
//        }
//    }
}
