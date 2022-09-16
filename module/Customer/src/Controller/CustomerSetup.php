<?php

namespace Customer\Controller;

use Application\Helper\Helper;
use Customer\Form\CustomerForm;
use Customer\Model\Customer;
use Customer\Repository\CustomerSetupRepo;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CustomerSetup extends AbstractActionController {

    private $form;
    private $repository;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->repository = new CustomerSetupRepo($adapter);
        $this->adapter = $adapter;
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    private function getForm() {
        if (!$this->form) {
            $form = new CustomerForm();
            $builder = new AnnotationBuilder();
            $this->form = $builder->createForm($form);
        }

        return $this->form;
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
        $form = $this->getForm();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $customer = new Customer();
                $customer->exchangeArrayFromForm($form->getData());
                $customer->customerId = ((int) Helper::getMaxId($this->adapter, Customer::TABLE_NAME, Customer::CUSTOMER_ID)) + 1;
                $customer->createdBy = $this->employeeId;
                $this->repository->add($customer);
                $this->flashmessenger()->addMessage("Customer added successfully.");
                return $this->redirect()->toRoute("customer-setup");
            }
        }
        return new ViewModel(['form' => $this->form,]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('customer-setup');
        }
        $request = $this->getRequest();
        $form = $this->getForm();


        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $customer = new Customer();
                $customer->exchangeArrayFromForm($form->getData());
                $customer->modifiedBy = $this->employeeId;
                $customer->modifiedDt = Helper::getcurrentExpressionDate();
                $this->repository->edit($customer, $id);
                $this->flashmessenger()->addMessage("Customer edited successfully.");
                return $this->redirect()->toRoute("customer-setup");
            }
        }
        $customer = new Customer();
        $detail = $this->repository->fetchById($id)->getArrayCopy();
        $customer->exchangeArrayFromDB($detail);
        $form->bind($customer);

        return new ViewModel(['form' => $this->form, "id" => $id]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id == -1) {
            return $this->redirect()->toRoute('department');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Customer deleted successfully.");
        return $this->redirect()->toRoute('customer-setup');
    }

}
