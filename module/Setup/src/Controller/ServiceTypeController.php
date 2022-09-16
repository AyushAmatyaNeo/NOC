<?php

namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Exception;
use Setup\Form\ServiceTypeForm;
use Setup\Model\ServiceType;
use Setup\Repository\ServiceTypeRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class ServiceTypeController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(ServiceTypeRepository::class);
        $this->initializeForm(ServiceTypeForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchActiveRecord();
                $serviceTypeList = iterator_to_array($result, false);
                return new JsonModel(['success' => true, 'data' => $serviceTypeList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    private function prepareForm() {
        $type = $this->form->get('type');
        $typeList = ["PERMANENT" => "Permanent", "PROBATION" => "Probation", "CONTRACT" => "Contract", "TEMPORARY" => "Temporary", "RESIGNED" => "Resigned", "RETIRED" => "Retired"];
        $type->setValueOptions($typeList);
    }

    public function addAction() {
        $this->prepareForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                try {
                    $serviceType = new ServiceType();
                    $serviceType->exchangeArrayFromForm($this->form->getData());
                    $serviceType->serviceTypeId = ((int) Helper::getMaxId($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID)) + 1;
                    $serviceType->createdDt = Helper::getcurrentExpressionDate();
                    $serviceType->createdBy = $this->employeeId;
                    $serviceType->status = 'E';
                    $this->repository->add($serviceType);

                    $this->flashmessenger()->addMessage("Service Type Successfully Added!!!");
                    return $this->redirect()->toRoute("serviceType");
                } catch (Exception $e) {
                    
                }
            }
        }
        return $this->stickFlashMessagesTo([
                    'form' => $this->form,
                'customRenderer' => Helper::renderCustomView()
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute();
        }
        $this->prepareForm();
        $request = $this->getRequest();
        $serviceType = new ServiceType();
        if (!$request->isPost()) {
            $serviceType->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($serviceType);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $serviceType->exchangeArrayFromForm($this->form->getData());
                $serviceType->modifiedDt = Helper::getcurrentExpressionDate();
                $serviceType->modifiedBy = $this->employeeId;
                $this->repository->edit($serviceType, $id);
                $this->flashmessenger()->addMessage("Service Type Successfully Updated!!!");
                return $this->redirect()->toRoute("serviceType");
            }
        }
        return $this->stickFlashMessagesTo(['form' => $this->form, 'id' => $id, 'customRenderer' => Helper::renderCustomView()]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('serviceType');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Service Type Successfully Deleted!!!");
        return $this->redirect()->toRoute('serviceType');
    }

}
