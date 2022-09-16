<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Customer\Form\CustomerLocationForm;
use Customer\Model\CustomerLocationModel;
use Customer\Repository\CustomerLocationRepo;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CustomerLocation extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CustomerLocationRepo::class);
        $this->initializeForm(CustomerLocationForm::class);
    }

    public function indexAction() {
        
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('customer-setup');
        }
        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    "id" => $id
        ]);
    }

    public function fetchAllCustomerLocationAction() {
        try {
            $id = (int) $this->params()->fromRoute("id", -1);

            $result = $this->repository->fetchAllLocationByCustomer($id);
            $list = Helper::extractDbData($result);
            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function addAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('customer-setup');
        }

        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);

            if ($this->form->isValid()) {
                $customerLocationModel = new CustomerLocationModel();
                $customerLocationModel->exchangeArrayFromForm($this->form->getData());

                $customerLocationModel->locationId = (int) Helper::getMaxId($this->adapter, $customerLocationModel::TABLE_NAME, $customerLocationModel::LOCATION_ID) + 1;

                $customerLocationModel->customerId = $id;
                $customerLocationModel->createdBy = $this->employeeId;
                $customerLocationModel->status = 'E';

                $this->repository->add($customerLocationModel);

                $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("customer-location", ["action" => "view", 'id' => $id]);
            }
        }

        return new ViewModel([
            'form' => $this->form,
            'id' => $id
        ]);
    }

    public function editAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('customer-setup');
        }
        $customerLocationModel = new CustomerLocationModel();

        $locationDetails = $this->repository->fetchById($id);

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $customerLocationModel->exchangeArrayFromForm($this->form->getData());

                $customerLocationModel->modifiedDate = Helper::getcurrentExpressionDate();
                $customerLocationModel->modifiedBy = $this->employeeId;
                $this->repository->edit($customerLocationModel, $id);
                $this->flashmessenger()->addMessage("Successfully Updated!!!");
                return $this->redirect()->toRoute("customer-location", ["action" => "view", 'id' => $locationDetails['CUSTOMER_ID']]);
            }
        }



        $customerLocationModel->exchangeArrayFromDB($locationDetails);
        $this->form->bind($customerLocationModel);

        return new ViewModel([
            'form' => $this->form,
            'id' => $id
        ]);
    }

    public function deleteAction($id) {
        $id = (int) $this->params()->fromRoute("id", -1);
        $locationDetails = $this->repository->fetchById($id);
        if ($id == -1) {
            return $this->redirect()->toRoute("customer-location", ["action" => "view", 'id' => $locationDetails['CUSTOMER_ID']]);
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage(" deleted successfully.");
        return $this->redirect()->toRoute("customer-location", ["action" => "view", 'id' => $locationDetails['CUSTOMER_ID']]);
    }

}
