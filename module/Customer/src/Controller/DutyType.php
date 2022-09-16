<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Customer\Form\DutyTypeForm;
use Customer\Model\DutyTypeModel;
use Customer\Repository\DutyTypeRepo;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class DutyType extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(DutyTypeRepo::class);
        $this->initializeForm(DutyTypeForm::class);
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
        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();

            $this->form->setData($postData);

            if ($this->form->isValid()) {
                $dutyTypeModel = new DutyTypeModel();
                $dutyTypeModel->exchangeArrayFromForm($this->form->getData());

                $dutyTypeModel->dutyTypeId = (int) Helper::getMaxId($this->adapter, DutyTypeModel::TABLE_NAME, DutyTypeModel::DUTY_TYPE_ID) + 1;

                $dutyTypeModel->normalHour = Helper::hoursToMinutes($dutyTypeModel->normalHour);
                $dutyTypeModel->otHour = Helper::hoursToMinutes($dutyTypeModel->otHour);

                $dutyTypeModel->createdBy = $this->employeeId;
                $dutyTypeModel->status = 'E';
                $this->repository->add($dutyTypeModel);

                $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("duty-type");
            }
        }


        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
        ]);
    }

    public function editAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('duty-type');
        }
        $dutyTypeModel = new DutyTypeModel();

        $details = $this->repository->fetchById($id);


        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $dutyTypeModel->exchangeArrayFromForm($this->form->getData());

                $dutyTypeModel->normalHour = Helper::hoursToMinutes($dutyTypeModel->normalHour);
                $dutyTypeModel->otHour = Helper::hoursToMinutes($dutyTypeModel->otHour);

                $dutyTypeModel->modifiedDate = Helper::getcurrentExpressionDate();
                $dutyTypeModel->modifiedBy = $this->employeeId;

                $this->repository->edit($dutyTypeModel, $id);
                $this->flashmessenger()->addMessage("Successfully Updated!!!");
                return $this->redirect()->toRoute("duty-type");
            }
        }

        $dutyTypeModel->exchangeArrayFromDB($details);
        $this->form->bind($dutyTypeModel);

        return new ViewModel([
            'form' => $this->form,
            'id' => $id
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        $locationDetails = $this->repository->fetchById($id);
        if ($id == -1) {
            return $this->redirect()->toRoute("duty-type");
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage(" deleted successfully.");
        return $this->redirect()->toRoute("duty-type");
    }

}
