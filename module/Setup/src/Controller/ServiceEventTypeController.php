<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use Setup\Form\ServiceEventTypeForm;
use Setup\Model\ServiceEventType;
use Setup\Repository\ServiceEventTypeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class ServiceEventTypeController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new ServiceEventTypeRepository($adapter);
    }

    private function initializeForm() {
        $serviceEventTypeForm = new ServiceEventTypeForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($serviceEventTypeForm);
        }
    }

    public function indexAction() {
        $serviceEventTypeList = $this->repository->fetchActiveRecord();
        $serviceEventTypes = [];
        foreach ($serviceEventTypeList as $serviceEventTypeRow) {
            array_push($serviceEventTypes, $serviceEventTypeRow);
        }
        return Helper::addFlashMessagesToArray($this, ['serviceEventTypes' => $serviceEventTypes]);
    }

    public function addAction() {

        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                try {
                    $serviceEventType = new ServiceEventType();
                    $serviceEventType->exchangeArrayFromForm($this->form->getData());
                    $serviceEventType->serviceEventTypeId = ((int) Helper::getMaxId($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID)) + 1;
                    $serviceEventType->createdDt = Helper::getcurrentExpressionDate();
                    $serviceEventType->status = 'E';
                    $this->repository->add($serviceEventType);

                    $this->flashmessenger()->addMessage("Service Event Type Successfully Added!!!");
                    return $this->redirect()->toRoute("serviceEventType");
                } catch (Exception $e) {
                    
                }
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages()
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute();
        }
        $this->initializeForm();
        $request = $this->getRequest();
        $serviceEventType = new ServiceEventType();
        if (!$request->isPost()) {
            $serviceEventType->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($serviceEventType);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $serviceEventType->exchangeArrayFromForm($this->form->getData());
                $serviceEventType->modifiedDt = Helper::getcurrentExpressionDate();

                $this->repository->edit($serviceEventType, $id);
                $this->flashmessenger()->addMessage("Service Event Type Successfully Updated!!!");
                return $this->redirect()->toRoute("serviceEventType");
            }
        }
        return Helper::addFlashMessagesToArray($this, ['form' => $this->form, 'id' => $id]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('serviceEventType');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Service Event Type Successfully Deleted!!!");
        return $this->redirect()->toRoute('serviceEventType');
    }

}
