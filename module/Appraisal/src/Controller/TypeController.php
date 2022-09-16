<?php

namespace Appraisal\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Appraisal\Form\TypeForm;
use Appraisal\Model\Type;
use Appraisal\Repository\TypeRepository;
use Exception;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class TypeController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TypeRepository::class);
        $this->initializeForm(TypeForm::class);
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
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $type = new Type();
                $type->exchangeArrayFromForm($this->form->getData());
                $type->createdDate = Helper::getcurrentExpressionDate();
                $type->approvedDate = Helper::getcurrentExpressionDate();
                $type->createdBy = $this->employeeId;
                $type->companyId = $employeeDetail['COMPANY_ID'];
                $type->branchId = $employeeDetail['BRANCH_ID'];
                $type->appraisalTypeId = ((int) Helper::getMaxId($this->adapter, "HRIS_APPRAISAL_TYPE", "APPRAISAL_TYPE_ID")) + 1;
                $type->status = 'E';
                $this->repository->add($type);
                $this->flashmessenger()->addMessage("Appraisal Type Successfully added!!!");
                return $this->redirect()->toRoute("type");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

    public function editAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('type');
        }
        $request = $this->getRequest();
        $type = new Type();
        if (!$request->isPost()) {
            $type->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($type);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $type->exchangeArrayFromForm($this->form->getData());
                $type->modifiedDate = Helper::getcurrentExpressionDate();
                $type->modifiedBy = $this->employeeId;
                $this->repository->edit($type, $id);
                $this->flashmessenger()->addMessage("Appraisal Type Successfully Updated!!!");
                return $this->redirect()->toRoute("type");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('type');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Appraisal Type Successfully Deleted!!!");
        return $this->redirect()->toRoute("type");
    }

}
