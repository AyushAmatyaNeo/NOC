<?php

namespace Overtime\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Overtime\Repository\OvertimeAutomationRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class OvertimeAutomation extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(OvertimeAutomationRepository::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    public function assignListAction() {
        try {
            $request = $this->getRequest();
            $postedData = $request->getPost();
            $compulsoryOvertimeId = $postedData['compulsoryOvertimeId'];
            $list = $this->repository->fetchAssignedEmployees($compulsoryOvertimeId);
            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function wizardAction() {
        $id = (int) $this->params()->fromRoute("id");
        $editData = null;
        if ($id !== 0) {
            $editData['compulsoryOTSetup'] = $this->repository->fetchById($id);
            $editData['assignedEmployees'] = [];
            $employees = $this->repository->fetchAssignedEmployees($id);
            foreach ($employees as $employee) {
                array_push($editData['assignedEmployees'], $employee['EMPLOYEE_ID']);
            }
        }

        return $this->stickFlashMessagesTo([
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'editData' => $editData
        ]);
    }

    public function handleWizardAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postedData = $request->getPost();

            $compulsoryOtDesc = $postedData['compulsoryOtDesc'];
            $earlyOvertimeHour = $postedData['earlyOvertimeHour'];
            $lateOvertimeHour = $postedData['lateOvertimeHour'];
            $startDate = $postedData['startDate'];
            $endDate = $postedData['endDate'];
            $employeeList = $postedData['employeeList'];
            $compulsoryOvertimeId = $postedData['compulsoryOvertimeId'];

            $result = $this->repository->wizardProcedure($compulsoryOtDesc, Helper::hoursToMinutes($earlyOvertimeHour), Helper::hoursToMinutes($lateOvertimeHour), $startDate, $endDate, $employeeList, $this->employeeId, $compulsoryOvertimeId);
            return new JsonModel(['success' => true, 'data' => $result, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('overtimeAutomation');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Compulsory OT successfully deleted.");
        return $this->redirect()->toRoute('overtimeAutomation');
    }

}
