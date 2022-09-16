<?php

namespace Asset\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Asset\Form\GroupForm;
use Asset\Model\Group;
use Asset\Repository\GroupRepository;
use Exception;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class GroupController extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new GroupRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $form = new GroupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $assetGroupList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $assetGroupList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {
        ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this);
        $this->initializeForm();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {

                $group = new Group();
                $group->exchangeArrayFromForm($this->form->getData());
                $group->createdBy = $this->employeeId;
                $group->createdDate = Helper::getcurrentExpressionDate();
                $group->approveDate = Helper::getcurrentExpressionDate();
                $group->companyId = $employeeDetail['COMPANY_ID'];
                $group->branchId = $employeeDetail['BRANCH_ID'];
                $group->assetGroupId = ((int) Helper::getMaxId($this->adapter, $group::TABLE_NAME, $group::ASSET_GROUP_ID)) + 1;
                $group->status = 'E';
                $this->repository->add($group);
                $this->flashmessenger()->addMessage("Asset Group Successfully added!!!");
                return $this->redirect()->toRoute("assetGroup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form
        ]);
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this)) {
            return;
        };
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('assetGroup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Asset Group Successfully Deleted!!!");
        return $this->redirect()->toRoute("assetGroup");
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('assetGroup');
        }
        $this->initializeForm();

        $request = $this->getRequest();
        $group = new Group();
        if (!$request->isPost()) {
            $group->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($group);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $group->exchangeArrayFromForm($this->form->getData());

                $group->modifiedDate = Helper::getcurrentExpressionDate();
                $group->modifiedBy = $this->employeeId;

                $this->repository->edit($group, $id);
                $this->flashmessenger()->addMessage("Asset Group Successfully Updated!!!");
                return $this->redirect()->toRoute("assetGroup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id
        ]);
    }

}
