<?php

namespace AttendanceManagement\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Form\ShiftAdjustmentForm;
use AttendanceManagement\Repository\ShiftAdjustmentRepository;
use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class ShiftAdjustment extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new ShiftAdjustmentRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $shiftAdjustmentForm = new ShiftAdjustmentForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($shiftAdjustmentForm);
    }

    public function indexAction() {
        $shiftAdjustmentList = $this->repository->fetchAll();
        $shiftsAdj = [];
        foreach ($shiftAdjustmentList as $shiftRow) {
            array_push($shiftsAdj, $shiftRow);
        }
        return Helper::addFlashMessagesToArray($this, ['shiftsAdjust' => $shiftsAdj]);
    }

    public function addAction() {
        $id = (int) $this->params()->fromRoute("id");
        $editData = null;
        if ($id !== 0) {
            $editData['shiftAdjustment'] = $this->repository->fetchById($id);
            $editData['assignedEmployees'] = [];
            $employees = $this->repository->fetchAssignedEmployees($id);
            foreach ($employees as $employee) {
                array_push($editData['assignedEmployees'], $employee['EMPLOYEE_ID']);
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'editData' => $editData
                        ]
        );
    }

    public function shiftAdjustAddAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postedData = $request->getPost();

            $startTime = $postedData['startTime'];
            $endTime = $postedData['endTime'];
            $adjustmentStartDate = $postedData['adjustmentStartDate'];
            $adjustmentEndDate = $postedData['adjustmentEndDate'];
            $employeeList = $postedData['employeeList'];
            $adjustmentId = $postedData['adjustmentId'];
            $startTime = Helper::getTimestampHana($startTime, $adjustmentStartDate);
            $endTime = Helper::getTimestampHana($endTime, $adjustmentStartDate);
            $result = $this->repository->insertShiftAdjustment($startTime, $endTime, $adjustmentStartDate, $adjustmentEndDate, $employeeList, $this->employeeId, $adjustmentId);
            return new CustomViewModel(['success' => true, 'data' => $result, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
