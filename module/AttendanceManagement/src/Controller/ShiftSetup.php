<?php

namespace AttendanceManagement\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper as EntityHelper2;
use Application\Helper\Helper;
use AttendanceManagement\Form\ShiftForm;
use AttendanceManagement\Model\ShiftSetup as Shift;
use AttendanceManagement\Repository\ShiftRepository;
use Exception;
use Setup\Model\Company;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ShiftSetup extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(ShiftRepository::class);
        $this->initializeForm(ShiftForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $shiftList = iterator_to_array($result, false);
                return new JsonModel(['success' => true, 'data' => $shiftList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    public function addAction() {
        $request = $this->getRequest();
        $shift = new Shift();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $shift->exchangeArrayFromForm($this->form->getData());
                
                $shift->shiftId = ((int) Helper::getMaxId($this->adapter, Shift::TABLE_NAME, Shift::SHIFT_ID)) + 1;
                $startDate = $shift->startDate;
                $endDate = $shift->endDate;
                $shift->startDate = Helper::getExpressionDate($shift->startDate);
                $shift->endDate = Helper::getExpressionDate($shift->endDate);
                $shift->startTime = Helper::getExpressionDateTimeHana($shift->startTime, $startDate);
                $shift->endTime = Helper::getExpressionDateTimeHana($shift->endTime, $endDate);
                $shift->halfDayEndTime = Helper::getExpressionDateTimeHana($shift->halfDayEndTime, $endDate);
                $shift->halfTime = Helper::getExpressionDateTimeHana($shift->halfTime, $startDate);
                $shift->createdDt = Helper::getcurrentExpressionDate();
                $shift->createdBy = $this->employeeId;
                $shift->status = 'E';
                $shift->graceStartTime = Helper::getExpressionDateTimeHana($shift->graceStartTime, $startDate);
                $shift->graceEndTime = Helper::getExpressionDateTimeHana($shift->graceEndTime, $endDate);
                $shift->halfDayInTime = Helper::getExpressionDateTimeHana($shift->halfDayInTime, $startDate);
                $shift->halfDayOutTime = Helper::getExpressionDateTimeHana($shift->halfDayOutTime, $endDate);

                $shift->actualWorkingHr = Helper::hoursToMinutes($shift->actualWorkingHr);
                $shift->totalWorkingHr = Helper::hoursToMinutes($shift->totalWorkingHr);
                $shift->lateIn = Helper::hoursToMinutes($shift->lateIn);
                $shift->earlyOut = Helper::hoursToMinutes($shift->earlyOut);

                $this->repository->add($shift);
                $this->flashmessenger()->addMessage("Shift Successfully added!!!");
                return $this->redirect()->toRoute("shiftsetup");
            }
        }
        $id = (int) $this->params()->fromRoute("id", 0);
        if ($id !== 0) {
            $shift->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($shift);
        }

        $defaultShift = $this->repository->DefaultShift();

        return $this->stickFlashMessagesTo(
                        [
                            'form' => $this->form,
                            'customRenderer' => Helper::renderCustomView(),
                            'companies' => EntityHelper2::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], [Company::STATUS => 'E'], "COMPANY_NAME", "ASC", NULL, TRUE, TRUE),
                            'anotherDefaultShift' => $defaultShift
                        ]
        );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("shiftsetup");
        }

        $request = $this->getRequest();
        $shift = new Shift();
        if (!$request->isPost()) {
            $shift->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($shift);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $shift->exchangeArrayFromForm($this->form->getData());
                
                $shift->shiftId = ((int) Helper::getMaxId($this->adapter, Shift::TABLE_NAME, Shift::SHIFT_ID)) + 1;
                $startDate = $shift->startDate;
                $endDate = $shift->endDate;
                $shift->startDate = Helper::getExpressionDate($shift->startDate);
                $shift->endDate = Helper::getExpressionDate($shift->endDate);
                $shift->startTime = Helper::getExpressionDateTimeHana($shift->startTime, $startDate);
                $shift->endTime = Helper::getExpressionDateTimeHana($shift->endTime, $endDate);
                $shift->halfDayEndTime = Helper::getExpressionDateTimeHana($shift->halfDayEndTime, $endDate);
                $shift->halfTime = Helper::getExpressionDateTimeHana($shift->halfTime, $startDate);
                $shift->modifiedDt = Helper::getcurrentExpressionDate();
                $shift->modifiedBy = $this->employeeId;

                $shift->actualWorkingHr = Helper::hoursToMinutes($shift->actualWorkingHr);
                $shift->totalWorkingHr = Helper::hoursToMinutes($shift->totalWorkingHr);
                $shift->lateIn = Helper::hoursToMinutes($shift->lateIn);
                $shift->earlyOut = Helper::hoursToMinutes($shift->earlyOut);

                $shift->actualWorkingHr = Helper::hoursToMinutes($shift->actualWorkingHr);
                $shift->totalWorkingHr = Helper::hoursToMinutes($shift->totalWorkingHr);
                $shift->lateIn = Helper::hoursToMinutes($shift->lateIn);
                $shift->earlyOut = Helper::hoursToMinutes($shift->earlyOut);
                // echo '<pre>'; print_r($shift); die;
                $this->repository->edit($shift, $id);
                $this->flashmessenger()->addMessage("Shift Successfuly Updated!!!");
                return $this->redirect()->toRoute("shiftsetup");
            }
        }
        $defaultShift = $this->repository->DefaultShift();
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'customRenderer' => Helper::renderCustomView(),
                    'companies' => EntityHelper2::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], [Company::STATUS => 'E'], "COMPANY_NAME", "ASC", NULL, TRUE, TRUE),
                    'anotherDefaultShift' => $defaultShift
                        ]
                )
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('shiftsetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Shift Successfully Deleted!!!");
        return $this->redirect()->toRoute('shiftsetup');
    }

}

/* End of file ShiftController.php */
/* Location: ./Setup/src/Controller/ShiftController.php */
