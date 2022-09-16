<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\PositionForm;
use Setup\Model\Company;
use Setup\Model\Position;
use Setup\Repository\PositionRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PositionController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new PositionRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $positionForm = new PositionForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($positionForm);
        }
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchActiveRecord();
                $positionList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $positionList, 'error' => '']);
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
                $position = new Position();
                $position->exchangeArrayFromForm($this->form->getData());
                $position->positionId = ((int) Helper::getMaxId($this->adapter, Position::TABLE_NAME, Position::POSITION_ID)) + 1;
                $position->createdDt = Helper::getcurrentExpressionDate();
                $position->createdBy = $this->employeeId;
                $position->status = 'E';
                $this->repository->add($position);

                $this->flashmessenger()->addMessage("Position Successfully added!!!");
                return $this->redirect()->toRoute("position");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", null, true, true),
                    'messages' => $this->flashmessenger()->getMessages()
                        ]
                )
        );
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('position');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $position = new Position();
        if (!$request->isPost()) {
            $position->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($position);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $position->exchangeArrayFromForm($this->form->getData());
                $position->modifiedDt = Helper::getcurrentExpressionDate();
                $position->modifiedBy = $this->employeeId;
                $this->repository->edit($position, $id);
                $this->flashmessenger()->addMessage("Position Successfully Updated!!!");
                return $this->redirect()->toRoute("position");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", null, true, true),
                    'id' => $id
                        ]
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Position Successfully Deleted!!!");
        return $this->redirect()->toRoute('position');
    }

}
