<?php
namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Application\Model\HrisQuery;
use Exception;
use Setup\Form\DepartmentForm;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Department;
use Setup\Repository\DepartmentRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\View\Model\JsonModel;

class DepartmentController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage, DepartmentRepository $repository) {
        parent::__construct($adapter, $storage);
        $this->repository = $repository;
        $this->initializeForm(DepartmentForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $departmentList = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $departmentList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        // $result = $this->repository->jvTableFlag();
        // $displayJVFlag = Helper::extractDbData($result)[0]['JV_TABLE_FLAG'];
        // $this->acl['JV_FLAG'] = $displayJVFlag;
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    private function prepareForm($id = null) {
        $countryId = $this->form->get('countryId');
        $companyId = $this->form->get('companyId');
        $branchId = $this->form->get('branchId');
        $parentDepartment = $this->form->get('parentDepartment');

        $countryKV = HrisQuery::singleton()
            ->setAdapter($this->adapter)
            ->setTableName("HRIS_COUNTRIES")
            ->setColumnList(["COUNTRY_ID", "COUNTRY_NAME"])
            ->setKeyValue("COUNTRY_ID", "COUNTRY_NAME")
//            ->setIncludeEmptyRow(true)
            ->result();
        $companyKV = HrisQuery::singleton()
            ->setAdapter($this->adapter)
            ->setTableName(Company::TABLE_NAME)
            ->setColumnList([Company::COMPANY_ID, Company::COMPANY_NAME])
            ->setWhere([Company::STATUS => 'E'])
            ->setKeyValue(Company::COMPANY_ID, Company::COMPANY_NAME)
            ->setIncludeEmptyRow(true)
            ->result();
        $branchKV = HrisQuery::singleton()
            ->setAdapter($this->adapter)
            ->setTableName(Branch::TABLE_NAME)
            ->setColumnList([Branch::BRANCH_ID, Branch::BRANCH_NAME])
            ->setWhere([Branch::STATUS => 'E'])
            ->setKeyValue(Branch::BRANCH_ID, Branch::BRANCH_NAME)
            ->setIncludeEmptyRow(true)
            ->result();
        $depWhere = [Department::STATUS => 'E'];
        if ($id != null) {
            $depWhere[] = Department::DEPARTMENT_ID . " != {$id}";
        }
        $departmentKV = HrisQuery::singleton()
            ->setAdapter($this->adapter)
            ->setTableName(Department::TABLE_NAME)
            ->setColumnList([Department::DEPARTMENT_ID, Department::DEPARTMENT_NAME])
            ->setWhere($depWhere)
            ->setOrder([Department::DEPARTMENT_NAME => Select::ORDER_ASCENDING])
            ->setKeyValue(Department::DEPARTMENT_ID, Department::DEPARTMENT_NAME)
            ->setIncludeEmptyRow(true)
            ->result();

        $countryId->setValueOptions($countryKV);
        $companyId->setValueOptions($companyKV);
        $branchId->setValueOptions($branchKV);
        $parentDepartment->setValueOptions($departmentKV);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $department = new Department();
                $department->exchangeArrayFromForm($this->form->getData());
                $department->createdDt = Helper::getcurrentExpressionDate();
                $department->createdBy = $this->employeeId;
                $department->departmentId = ((int) Helper::getMaxId($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID")) + 1;
                $department->status = 'E';
                $this->repository->add($department);
                $this->flashmessenger()->addMessage("Department Successfully added!!!");
                return $this->redirect()->toRoute("department");
            }
        }
        $this->prepareForm();
        return ['form' => $this->form];
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('department');
        }
        $request = $this->getRequest();
        $department = new Department();
        $detail = $this->repository->fetchById($id)->getArrayCopy();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $department->exchangeArrayFromForm($this->form->getData());
                $department->modifiedDt = Helper::getcurrentExpressionDate();
                $department->modifiedBy = $this->employeeId;
                $this->repository->edit($department, $id);
                $this->flashmessenger()->addMessage("Department Successfully Updated!!!");
                return $this->redirect()->toRoute("department");
            }
        }
        $department->exchangeArrayFromDb($detail);
        $this->form->bind($department);
        $this->prepareForm($id);

        return ['form' => $this->form, 'id' => $id];
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('department');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Department Successfully Deleted!!!");
        return $this->redirect()->toRoute('department');
    }

    public function jvAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $deptId = (int) $this->params()->fromRoute("id");
                $result = $this->repository->fetchJvDetails($deptId);
                $details = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $details, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $deptId = (int) $this->params()->fromRoute("id");
        return Helper::addFlashMessagesToArray($this, [
            'acl' => $this->acl,
            'dept' => $deptId
        ]);
    }

    public function jvUpdateAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $this->repository->updateJv($data, $this->employeeId);
                return new JsonModel(['success' => true, 'data' => [], 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }
}
