<?php

namespace Customer\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Form\CustomerContractFrom;
use Customer\Model\Customer;
use Customer\Model\CustomerContract as CustomerContractModel;
use Customer\Repository\CustomerContractRepo;
use Exception;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CustomerContract extends AbstractActionController {

    private $form;
    private $repository;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->repository = new CustomerContractRepo($adapter);
        $this->adapter = $adapter;
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
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

    private function getForm() {
        if (!$this->form) {
            $form = new CustomerContractFrom();
            $builder = new AnnotationBuilder();
            $this->form = $builder->createForm($form);
        }

        return $this->form;
    }

    public function addAction() {
        $request = $this->getRequest();
        $form = $this->getForm();
        $customerIdElement = $form->get('customerId');
        $customerIdElement->setValueOptions(EntityHelper::getTableKVList($this->adapter, Customer::TABLE_NAME, Customer::CUSTOMER_ID, [Customer::CUSTOMER_ENAME], [Customer::STATUS => EntityHelper::STATUS_ENABLED], null, true));
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {


                $customerContract = new CustomerContractModel();
                $customerContract->exchangeArrayFromForm($form->getData());
                $customerContract->contractId = ((int) Helper::getMaxId($this->adapter, CustomerContractModel::TABLE_NAME, CustomerContractModel::CONTRACT_ID)) + 1;
                $customerContract->createdBy = $this->employeeId;

                $this->repository->add($customerContract);

                $this->flashmessenger()->addMessage("Customer Contract added successfully.");
                return $this->redirect()->toRoute("customer-contract");
            }
        }
        return new ViewModel([
            'form' => $form,
            'customRenderer' => Helper::renderCustomView(),
            'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"])
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("customer-contract");
        }
        $form = $this->getForm();


        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $customerContract = new CustomerContractModel();
                $customerContract->exchangeArrayFromForm($form->getData());

                $customerContract->modifiedBy = $this->employeeId;
                $customerContract->modifiedDt = Helper::getCurrentDate();



                $this->repository->edit($customerContract, $id);

                $this->flashmessenger()->addMessage("Customer Contract updated successfully.");
                return $this->redirect()->toRoute("customer-contract");
            }
        }




        $customerIdElement = $form->get('customerId');
        $customerIdElement->setValueOptions(EntityHelper::getTableKVList($this->adapter, Customer::TABLE_NAME, Customer::CUSTOMER_ID, [Customer::CUSTOMER_ENAME], [Customer::STATUS => EntityHelper::STATUS_ENABLED], null, true));
        $customerContract = new CustomerContractModel();
        $detail = $this->repository->fetchById($id);
        

        $customerContract->exchangeArrayFromDB($detail);
        $form->bind($customerContract);


        return new ViewModel([
            'form' => $form,
            'id' => $id,
            'customRenderer' => Helper::renderCustomView(),
            'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"]),
        ]);
    }

    public function printAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("customer-contract");
        }
        $contract = $this->repository->fetchById($id);

        $contractDetailRepo = new \Customer\Repository\CustomerContractDetailRepo($this->adapter);

        $contractDetails = $contractDetailRepo->fetchAllContractDetailByContractId($id);



        return new ViewModel([
            'id' => $id,
            'contract' => $contract,
            'contractDetails' => $contractDetails,
        ]);
    }

}
