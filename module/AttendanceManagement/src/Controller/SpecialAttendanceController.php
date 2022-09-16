<?php

namespace AttendanceManagement\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use AttendanceManagement\Repository\SpecialAttendanceRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use AttendanceManagement\Model\SpecialAttendanceAssign;
use AttendanceManagement\Model\SpecialAttendanceSetup;
use AttendanceManagement\Form\SpecialAttendanceSetupForm;

class SpecialAttendanceController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(SpecialAttendanceRepository::class);
        $this->initializeForm(SpecialAttendanceSetupForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                return new JsonModel(['success' => true, 'data' => $result, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $spModel = new SpecialAttendanceSetup();
                $spModel->exchangeArrayFromForm($this->form->getData());
                $spModel->id = ((int) Helper::getMaxId($this->adapter, SpecialAttendanceSetup::TABLE_NAME, SpecialAttendanceSetup::ID)) + 1;
                $spModel->createdDt = Helper::getcurrentExpressionDate();
                $spModel->createdBy = $this->employeeId;
                $spModel->status = 'E';
                $this->repository->add($spModel);
                $this->flashmessenger()->addMessage("Special Attendance Successfully added!!!");
                return $this->redirect()->toRoute("special-attendance");
            }
        }
        return Helper::addFlashMessagesToArray( $this, ['form' => $this->form] );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("special-attendance");
        }
        $request = $this->getRequest();
        $spModel = new SpecialAttendanceSetup();
        if (!$request->isPost()) {
            $spModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($spModel);
        }
        else {
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $spModel->exchangeArrayFromForm($this->form->getData());
                $spModel->modifiedDt = Helper::getcurrentExpressionDate();
                $spModel->modifiedBy = $this->employeeId;
                $this->repository->edit($spModel, $id);
                $this->flashmessenger()->addMessage("Special Attendance Successfuly Updated!!!");
                return $this->redirect()->toRoute("special-attendance");
            }
        }
        return Helper::addFlashMessagesToArray( $this, ['form' => $this->form, 'id' => $id] );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('special-attendance');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Special Attendance Successfully Deleted!!!");
        return $this->redirect()->toRoute('special-attendance');
    }

    public function assignAction() {
        return $this->stickFlashMessagesTo([
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'spList' => $this->repository->fetchAll(),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'preference' => $this->preference,
                    'provinces' => EntityHelper::getProvinceList($this->adapter),
                    'braProv' => EntityHelper::getBranchFromProvince($this->adapter),
        ]);
    }

    public function getEmployeeListAction(){
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $data = Helper::extractDbData($this->repository->filterEmployees($postedData));
                return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage(), 'errorDetail' => $e->getTraceAsString()]);
        }
    }

    public function getAssignedEmployeesAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $spId = $postedData['spId'];
                $date = $postedData['fromDate'];
                $reportData = Helper::extractDbData(EntityHelper::getTableKVList($this->adapter, SpecialAttendanceAssign::TABLE_NAME, null, [SpecialAttendanceAssign::EMPLOYEE_ID], [SpecialAttendanceAssign::SP_ID => $spId, SpecialAttendanceAssign::ATTENDANCE_DT => $date], null, null, null, null, TRUE));
                return new JsonModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function assignSpToEmployeesAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $spId = $postedData['spId'];
                // print_r($postedData);die;
                $employeeIdList = $postedData['employeeIdList'];
                $fromDate = $postedData['fromDate'];
                $toDate = $postedData['toDate'];
                $displayInOutFlag = json_decode($postedData['displayInOutFlag']) ? 'Y' : 'N';
                $iterableDate = $this->getDateIterable($fromDate, $toDate);
                foreach ($iterableDate as $dt) {
                    $date = $dt->format("Y-m-d");
                    // print_r($date);die;
                    // print_r($employeeIdList);die;

                    foreach($employeeIdList as $employee){
                        json_decode($employee['assignFlag']) ? $this->repository->assignSpToEmployees($spId, $employee['employeeId'], $date, $displayInOutFlag, $this->employeeId) : $this->repository->removeSpFromEmployees($employee['employeeId'], $date) ;
                        $this->repository->reAttendance($employee['employeeId'], $date);
                    }
                } 
                return new JsonModel(['success' => true, 'data' => null, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function getDateIterable($fromDate, $toDate){
        $fromDate = date("d-M-y", strtotime($fromDate));
        if(empty($toDate)){ $toDate = $fromDate; }
        $toDate = date("d-M-y", strtotime($toDate));

        $begin = new \DateTime($fromDate);
        $end = new \DateTime($toDate);
        $end->modify('+1 day');

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        return $period;
    }
}
