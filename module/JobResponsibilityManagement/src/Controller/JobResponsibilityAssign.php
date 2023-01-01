<?php
namespace JobResponsibilityManagement\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Application\Model\HrisQuery;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\View\Model\JsonModel;
use JobResponsibilityManagement\Repository\JobResponsibilityRepository;
use JobResponsibilityManagement\Model\JobResponsibility;
use JobResponsibilityManagement\Form\JobResponsibilityForm;
use JobResponsibilityManagement\Model\JobResponsibilityAssign as JRAModel;
use LeaveManagement\Model\LeaveMaster;
use Application\Helper\EntityHelper;

class JobResponsibilityAssign extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage, JobResponsibilityRepository $repository) {
        parent::__construct($adapter, $storage);
        $this->repository = $repository;
        $this->initializeForm(JobResponsibilityForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAllEmpJRAssign();
                $jobResponsibilityAssignList = Helper::extractDbData($result);

                foreach($jobResponsibilityAssignList as $key=>$jrl){
                    $jobResponsibilityAssignList[$key]['JOB_RES_NEP_NAME'] = base64_decode($jobResponsibilityAssignList[$key]['JOB_RES_NEP_NAME']);
                }
                return new JsonModel(['success' => true, 'data' => $jobResponsibilityAssignList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    public function addAction() {
        $leaveList = HrisQuery::singleton()
            ->setAdapter($this->adapter)
            ->setTableName(LeaveMaster::TABLE_NAME)
            ->setColumnList([LeaveMaster::LEAVE_ID, LeaveMaster::LEAVE_ENAME])
            ->setWhere([LeaveMaster::STATUS => 'E'])
            ->setOrder([LeaveMaster::VIEW_ORDER => Select::ORDER_ASCENDING,LeaveMaster::LEAVE_ENAME => Select::ORDER_ASCENDING])
            ->setKeyValue(LeaveMaster::LEAVE_ID, LeaveMaster::LEAVE_ENAME)
            ->result();
            
        $jobResponsibilityList = HrisQuery::singleton()
            ->setAdapter($this->adapter)
            ->setTableName(JobResponsibility::TABLE_NAME)
            ->setColumnList([JobResponsibility::ID, JobResponsibility::JOB_RES_ENG_NAME])
            ->setWhere([JobResponsibility::STATUS => 'E'])
            ->setKeyValue(JobResponsibility::ID, JobResponsibility::JOB_RES_ENG_NAME)
            ->result();
        $config = [
            'name' => 'leave',
            'id' => 'leaveId',
            'class' => 'form-control reset-field',
            'label' => 'Type'
        ];

        $configJobResForm = [
            'name' => 'jobResponsibility',
            'id' => 'jobResonsibilityId',
            'class' => 'form-control reset-field',
            'label' => 'Job Responsibility'
        ];
        $leaveSE = $this->getSelectElement($config, $leaveList);
        $jobResponsbilitySE = $this->getSelectElement($configJobResForm, $jobResponsibilityList);
        
        
         $leaveYearData = HrisQuery::singleton()
            ->setAdapter($this->adapter)
            ->setTableName('HRIS_LEAVE_YEARS')
            ->setColumnList(['LEAVE_YEAR_ID', 'LEAVE_YEAR_NAME'])
            ->setWhere(['STATUS' => 'E'])
            ->setOrder(['LEAVE_YEAR_ID' => Select::ORDER_DESCENDING])
            ->setKeyValue('LEAVE_YEAR_ID', 'LEAVE_YEAR_NAME')
            ->result();
         $leaveYearConfig = [
            'name' => 'leaveYear',
            'id' => 'leaveYear',
            'class' => 'form-control reset-field',
            'label' => 'Leave Year'
        ];
         $leaveYearSE = $this->getSelectElement($leaveYearConfig, $leaveYearData);

         $assignedByList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"], ["STATUS" => 'E'], "FULL_NAME", "ASC", " ", FALSE, TRUE);
// print_r($assignedByList);die;
        return [
            'leaveFormElement' => $leaveSE,
            'jobResponsibilityFormElement' => $jobResponsbilitySE,
            'leaveYearFormElement' => $leaveYearSE,
            'acl' => $this->acl,
            'employeeDetail' => $this->storageData['employee_detail'],
            'assignedByList' => $assignedByList
            ];
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('jobResponsibility');
        }
        $request = $this->getRequest();
        $model = new JobResponsibility();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->jobResNepName = base64_encode($model->jobResNepName);
                $model->jobResNepDescription = base64_encode($model->jobResNepDescription);
                $model->modifiedDt = Helper::getcurrentExpressionDate();
                $model->modifiedBy = $this->employeeId;
                $this->repository->edit($model, $id);

                $this->flashmessenger()->addMessage("Job Responsibility Successfully Updated!!!");
                return $this->redirect()->toRoute("jobResponsibility");
            }
        }
        $fetchData = $this->repository->fetchById($id)->getArrayCopy();
        $model->exchangeArrayFromDB($fetchData);
        $model->jobResNepName = base64_decode($model->jobResNepName);
        $model->jobResNepDescription = base64_decode($model->jobResNepDescription);
        $this->form->bind($model);
        return [
            'form' => $this->form,
            'id' => $id,
            'customRender' => Helper::renderCustomView(),
        ];
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("eid");
        if (!$id) {
            return $this->redirect()->toRoute('jobResponsibilityAssign');
        }
        $this->repository->deleteJRA($id);
        $this->flashmessenger()->addMessage("Job Responsibility Successfully Deleted!!!");
        return $this->redirect()->toRoute('jobResponsibilityAssign');
    }

    public function terminateAction() {
        $id = (int) $this->params()->fromRoute("eid");
        if (!$id) {
            return $this->redirect()->toRoute('jobResponsibilityAssign');
        }
        $this->repository->terminateJRA($id);
        $this->flashmessenger()->addMessage("Job Responsibility Successfully Trminated!!!");
        return $this->redirect()->toRoute('jobResponsibilityAssign');
    }

    public function pullEmployeeAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $temp = $this->repository->filter($data['locationId'], $data['departmentId'], $data['genderId'], $data['designationId'], $data['serviceTypeId'], $data['employeeId'], $data['companyId'], $data['positionId'], $data['employeeTypeId'], $data['leaveId'], $data['jobResId']);
// print_r($temp);die;
            $list = [];
            foreach ($temp as $item) {
                array_push($list, $item);
            }
            return new JsonModel([
                "success" => "true",
                "data" => $list
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function pushEmployeeJobResAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $model = new JRAModel();
            $model->id = ((int) Helper::getMaxId($this->adapter, "HRIS_EMPLOYEE_JOB_RESPONSIBILITY_ASSIGN", "ID")) + 1;
            $model->employeeId = $data['employeeId'];
            $model->jobResponsibilityId = $data['jobResId'];
            $model->status='E';
            $model->createdDt = Helper::getcurrentExpressionDate();
            $model->createdBy = $this->employeeId;
            $model->assignedBy = $data['assignedBy'];
            $model->startDate = Helper::getExpressionDate($data['startDate']);
            $result = $this->repository->addJRAssign($model);

            return new JsonModel(["success" => "true", "data" => null,]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
}
