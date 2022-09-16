<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\ShiftGroupForm;
use Setup\Model\ShiftGroup;
use Setup\Repository\ShiftGroupRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ShiftGroupController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new ShiftGroupRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $shiftGroupForm = new ShiftGroupForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($shiftGroupForm);
        }
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $shiftGroupList = $this->repository->fetchGroupRecord();
                return new CustomViewModel(['success' => true, 'data' => $shiftGroupList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {
        ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this);
        $this->initializeForm();
        $request = $this->getRequest();

        $shifts = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_SHIFTS", "SHIFT_ID", ["SHIFT_ENAME"], ["STATUS" => 'E'], "SHIFT_ENAME", "ASC", "-");

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $shiftGroup = new ShiftGroup();
                $shiftGroup->exchangeArrayFromForm($this->form->getData());
                $shiftGroup->caseId = ((int) Helper::getMaxId($this->adapter, ShiftGroup::TABLE_NAME, ShiftGroup::CASE_ID)) + 1;
                $shiftGroup->createdDt = Helper::getcurrentExpressionDate();
                $shiftGroup->startDate = Helper::getExpressionDate($request->getPost('startDate'));
                $shiftGroup->endDate = Helper::getExpressionDate($request->getPost('endDate'));
                $shiftGroup->createdBy = $this->employeeId;
                $shiftGroup->status = 'E';
                $this->repository->add($shiftGroup);

                $caseId = $shiftGroup->caseId;
                $shifts = $request->getPost('shifts');

                $this->repository->mapShifts($caseId, $shifts);

                $this->flashmessenger()->addMessage("Shift Group Successfully added!!!");
                return $this->redirect()->toRoute("shiftGroup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages(),
                    'shiftList' => $shifts,
                    'shift' => $this->repository->getShifts()
                        ]
                )
        );
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('shiftGroup');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $shifts = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_SHIFTS", "SHIFT_ID", ["SHIFT_ENAME"], ["STATUS" => 'E'], "SHIFT_ENAME", "ASC", "-");
        $shiftGroup = new ShiftGroup();
        if (!$request->isPost()) {
            $shiftGroup->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($shiftGroup);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $shiftGroup->exchangeArrayFromForm($this->form->getData());
                $shiftGroup->modifiedDt = Helper::getcurrentExpressionDate();
                $shiftGroup->modifiedBy = $this->employeeId;
                $shiftGroup->startDate = Helper::getExpressionDate($shiftGroup->startDate);
                $shiftGroup->endDate = Helper::getExpressionDate($shiftGroup->endDate);
                // echo"<pre>";print_r($shiftGroup);die;
                
                $this->repository->edit($shiftGroup, $id);

                

                $shifts = $request->getPost('shifts');

                $this->repository->deleteMappedShifts($id);
                $this->repository->mapShifts($id, $shifts);

                $this->flashmessenger()->addMessage("Shift Group Successfully Updated!!!");
                return $this->redirect()->toRoute("shiftGroup");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'id' => $id,
                    'shiftList' => $shifts,
                    'shift' => $this->repository->getShiftsById($id)
                        ]
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        }
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('shiftGroup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Shift Group Successfully Deleted!!!");
        return $this->redirect()->toRoute('shiftGroup');
    }

}
