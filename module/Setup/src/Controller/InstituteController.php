<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\InstituteForm;
use Setup\Model\Institute;
use Setup\Repository\InstituteRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class InstituteController extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new InstituteRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new InstituteForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchActiveRecord();
                $instituteList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $instituteList, 'error' => '']);
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

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $instituteModel = new Institute();
                $instituteModel->exchangeArrayFromForm($this->form->getData());
                $instituteModel->instituteId = ((int) Helper::getMaxId($this->adapter, Institute::TABLE_NAME, Institute::INSTITUTE_ID)) + 1;
                $instituteModel->createdDate = Helper::getcurrentExpressionDate();
                $instituteModel->status = 'E';
                $instituteModel->createdBy = $this->employeeId;
                $this->repository->add($instituteModel);
                $this->flashmessenger()->addMessage("Institute Successfully added!!!");
                return $this->redirect()->toRoute('institute');
            }
        }
        return Helper::addFlashMessagesToArray($this, ['form' => $this->form]);
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('institute');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $instituteModel = new Institute();
        if (!$request->isPost()) {
            $instituteModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($instituteModel);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $instituteModel->exchangeArrayFromForm($this->form->getData());
                $instituteModel->modifiedDate = Helper::getcurrentExpressionDate();
                $instituteModel->modifiedBy = $this->employeeId;
                $this->repository->edit($instituteModel, $id);
                $this->flashmessenger()->addMessage("Institute Successfully Updated!!!");
                return $this->redirect()->toRoute("institute");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, ['form' => $this->form, 'id' => $id]
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('institute');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Institute Successfully Deleted!!!");
        return $this->redirect()->toRoute('institute');
    }

}
