<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Form\ServiceEmployeeSetupForm;
use Customer\Model\ServiceEmployeeSetupModel;
use Customer\Repository\ServiceEmployeeSetupRepo;
use Exception;
use Setup\Model\Gender;
use Setup\Model\Zones;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class ServiceEmployeeSetup extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(ServiceEmployeeSetupRepo::class);
        $this->initializeForm(ServiceEmployeeSetupForm::class);
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
                $serviceEmployeeSetupModule = new ServiceEmployeeSetupModel();
                $serviceEmployeeSetupModule->exchangeArrayFromForm($this->form->getData());

                $serviceEmployeeSetupModule->employeeId = (int) Helper::getMaxId($this->adapter, ServiceEmployeeSetupModel::TABLE_NAME, ServiceEmployeeSetupModel::EMPLOYEE_ID) + 1;

                $serviceEmployeeSetupModule->createdBy = $this->employeeId;
                $serviceEmployeeSetupModule->status = 'E';
                $serviceEmployeeSetupModule->fullName = $serviceEmployeeSetupModule->firstName . ' ' . $serviceEmployeeSetupModule->middleName . ' ' . $serviceEmployeeSetupModule->lastName;
                $this->repository->add($serviceEmployeeSetupModule);
                $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("service-employee");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    "bloodGroups" => EntityHelper::getTableKVList($this->adapter, 'HRIS_BLOOD_GROUPS', 'BLOOD_GROUP_ID', ['BLOOD_GROUP_CODE'], NULL, NULL, TRUE),
                    "genders" => EntityHelper::getTableKVList($this->adapter, Gender::TABLE_NAME, Gender::GENDER_ID, [Gender::GENDER_NAME], null, null, true),
                    "zones" => EntityHelper::getTableKVList($this->adapter, Zones::TABLE_NAME, Zones::ZONE_ID, [Zones::ZONE_NAME], null, null, true),
                    'positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E'], "POSITION_NAME", "ASC", null, true, true),
                    'designations' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E'], "DESIGNATION_TITLE", "ASC", null, true, true),
                    'departments' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_NAME", "ASC", null, true, true),
                    'branches' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E'], "BRANCH_NAME", "ASC", null, true, true)
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('advance-setup');
        }
        $request = $this->getRequest();

        $serviceEmployeeSetupModule = new ServiceEmployeeSetupModel();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $serviceEmployeeSetupModule->exchangeArrayFromForm($this->form->getData());

                $serviceEmployeeSetupModule->fullName = $serviceEmployeeSetupModule->firstName . ' ' . $serviceEmployeeSetupModule->middleName . ' ' . $serviceEmployeeSetupModule->lastName;
                $serviceEmployeeSetupModule->modifiedDate = Helper::getcurrentExpressionDate();
                $serviceEmployeeSetupModule->modifiedBy = $this->employeeId;
                $this->repository->edit($serviceEmployeeSetupModule, $id);
                $this->flashmessenger()->addMessage("Successfully Updated!!!");
                return $this->redirect()->toRoute("service-employee");
            }
        }

        $serviceEmployeeSetupModule->exchangeArrayFromDB($this->repository->fetchById($id));
        $this->form->bind($serviceEmployeeSetupModule);
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'form' => $this->form,
                    "bloodGroups" => EntityHelper::getTableKVList($this->adapter, 'HRIS_BLOOD_GROUPS', 'BLOOD_GROUP_ID', ['BLOOD_GROUP_CODE'], NULL, NULL, TRUE),
                    "genders" => EntityHelper::getTableKVList($this->adapter, Gender::TABLE_NAME, Gender::GENDER_ID, [Gender::GENDER_NAME], null, null, true),
                    "zones" => EntityHelper::getTableKVList($this->adapter, Zones::TABLE_NAME, Zones::ZONE_ID, [Zones::ZONE_NAME], null, null, true),
                    'positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E'], "POSITION_NAME", "ASC", null, true, true),
                    'designations' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E'], "DESIGNATION_TITLE", "ASC", null, true, true),
                    'departments' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_NAME", "ASC", null, true, true),
                    'branches' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E'], "BRANCH_NAME", "ASC", null, true, true)
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id == -1) {
            return $this->redirect()->toRoute('department');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage(" deleted successfully.");
        return $this->redirect()->toRoute("service-employee");
    }

}
