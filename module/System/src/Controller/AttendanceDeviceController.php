<?php

namespace System\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Model\Branch;
use Setup\Model\Company;
use System\Form\AttendanceDeviceForm;
use System\Model\AttendanceDevice;
use System\Repository\AttendanceDeviceRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Setup\Model\HrEmployees;

class AttendanceDeviceController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AttendanceDeviceRepository::class);
        $this->initializeForm(AttendanceDeviceForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
//                $list = $this->repository->fetchAll();
                $list = $this->repository->fetchAllWithBranchManager(); //use where BranchManager is required
                $attendanceDevice = [];
                foreach ($list as $row) {
                    $row['PING_STATUS'] = '---';
                    array_push($attendanceDevice, $row);
                }
                return new JsonModel(['success' => true, 'data' => $attendanceDevice, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return $this->stickFlashMessagesTo([
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $attendanceDevice = new AttendanceDevice();
                $attendanceDevice->exchangeArrayFromForm($this->form->getData());
                $boundedParameter=[];
                $boundedParameter['deviceIp']=$attendanceDevice->deviceIp;
                $sameIp = EntityHelper::rawQueryResult($this->adapter, "SELECT * FROM HRIS_ATTD_DEVICE_MASTER 
                WHERE DEVICE_IP=:deviceIp",$boundedParameter)->current();
                if (!$sameIp) {
                  
                    $attendanceDevice->deviceId = ((int) Helper::getMaxId($this->adapter, AttendanceDevice::TABLE_NAME, AttendanceDevice::DEVICE_ID)) + 1;
                    $attendanceDevice->status = 'E';
//                    $attendanceDevice->branchId = $attendanceDevice->deviceId;
                    $this->repository->add($attendanceDevice);

                    $this->flashmessenger()->addMessage("Attendance Device Successfully Added!!!");
                    return $this->redirect()->toRoute("AttendanceDevice");
                } else {
                    $deviceIpForm = $this->form->get('deviceIp');
                    $deviceIpForm->setMessages(['cus' => 'The ip address is already in the database']);
                }
            }
        }
        $this->prepareForm();
        return $this->stickFlashMessagesTo([
                    'form' => $this->form,
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $request = $this->getRequest();
        $attendanceDevice = new AttendanceDevice();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $attendanceDevice->exchangeArrayFromForm($this->form->getData());
                
                $boundedParameter=[];
                $boundedParameter['deviceIp']=$attendanceDevice->deviceIp;
                $boundedParameter['id']=$id;
                $sameIp = EntityHelper::rawQueryResult($this->adapter, "SELECT * FROM HRIS_ATTD_DEVICE_MASTER 
                WHERE DEVICE_IP=:deviceIp AND DEVICE_ID NOT IN (:id) ",$boundedParameter)->current();
                if (!$sameIp) {
                    $this->repository->edit($attendanceDevice, $id);
                    $this->flashmessenger()->addMessage("Attendance Device Successfully Updated!!!");
                    return $this->redirect()->toRoute("AttendanceDevice");
                } else {
                    $deviceIpForm = $this->form->get('deviceIp');
                    $deviceIpForm->setMessages(['cus' => 'The ip address is already in the database']);
                }
            }
        } else {
            $detail = $this->repository->fetchById($id)->getArrayCopy();
            $attendanceDevice->exchangeArrayFromDB($detail);
            $this->form->bind($attendanceDevice);
        }
        $this->prepareForm();
        return $this->stickFlashMessagesTo([
                    'id' => $id,
                    'form' => $this->form,
        ]);
    }

    private function prepareForm() {
        $companyList = EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", null, true, TRUE);
        $companySE = $this->form->get('companyId');
        $companySE->setValueOptions($companyList);

        $branchList = EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], ["STATUS" => "E"], Branch::BRANCH_NAME, "ASC", null, true, TRUE);
        $branchSE = $this->form->get('branchId');
        $branchSE->setValueOptions($branchList);
        
        $employeeList = EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::EMPLOYEE_CODE ,   HrEmployees::FULL_NAME], ["STATUS" => "E"], HrEmployees::FULL_NAME, "ASC", null, true, TRUE);
        $employeeSE = $this->form->get('branchManager');
        $employeeSE->setValueOptions($employeeList);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('usersetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Attendnace Device Successfully Deleted!!!");
        return $this->redirect()->toRoute('AttendanceDevice');
    }

    function pingAddress($ip) {
        $output = shell_exec('ping -n 1 ' . $ip);
        if (strpos($output, 'Destination host unreachable') !== false) {
            $result = "Destination host unreachable";
        } elseif (strpos($output, 'Request timed out') !== false) {
            $result = "Request timed out";
        } elseif (strpos($output, 'Expired') !== false) {
            $result = "Expired in Transit";
        } elseif (strpos($output, 'data') !== false) {
            $result = "ONLINE";
        } else {
            $result = "Unknown Error";
        }
        return $result;
    }

    public function pullDeviceWithPingStatusAction() {
        try {
            $list = $this->repository->fetchAll();
            $attendanceDevice = [];
            foreach ($list as $row) {
                $row['PING_STATUS'] = $this->pingAddress($row['DEVICE_IP']);
                array_push($attendanceDevice, $row);
            }
            return new JsonModel(['success' => true, 'data' => $attendanceDevice, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    function attendanceLogAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $postData = (array) $request->getPost();
                $attendanceData = $this->repository->fetchByIP($postData['ipList'], $postData['fromDate'], $postData['toDate']);
                return new JsonModel(['success' => true, 'data' => $attendanceData, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        $deviceIPList = EntityHelper::getTableList($this->adapter, AttendanceDevice::TABLE_NAME, [AttendanceDevice::DEVICE_IP]);
        $deviceIPKV = $this->listValueToKV($deviceIPList, AttendanceDevice::DEVICE_IP, AttendanceDevice::DEVICE_IP);
        $deviceIPSE = $this->getSelectElement(['name' => 'deviceIP', 'id' => 'deviceIP', 'class' => 'form-control', 'label' => 'Device IP'], $deviceIPKV);
        $deviceIPSE->setAttributes(['multiple' => 'multiple']);
        return $this->stickFlashMessagesTo([
                    'deviceIPSE' => $deviceIPSE
        ]);
    }

    function checkExeStatusAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("should be a post request.");
            }
            $postData = (array) $request->getPost();
            if ($postData['action'] == 'start-service') {
                exec('Start ');
            }
            $taskList = [];
            $isRunning = false;
            exec('tasklist', $taskList);
            foreach ($taskList as $task) {
                if (strpos(strtolower($task), strtolower('NEO')) !== false) {
                    $isRunning = true;
                }
            }

            return new JsonModel(['success' => true, 'data' => ['isRunning' => $isRunning], 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
