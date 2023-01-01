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

class JobResponsibilitySetup extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage, JobResponsibilityRepository $repository) {
        parent::__construct($adapter, $storage);
        $this->repository = $repository;
        $this->initializeForm(JobResponsibilityForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $jobResponsibilityList = Helper::extractDbData($result);
                foreach($jobResponsibilityList as $key=>$jrl){
                    $jobResponsibilityList[$key]['JOB_RES_NEP_DESCRIPTION'] = base64_decode($jobResponsibilityList[$key]['JOB_RES_NEP_DESCRIPTION']);
                    $jobResponsibilityList[$key]['JOB_RES_NEP_NAME'] = base64_decode($jobResponsibilityList[$key]['JOB_RES_NEP_NAME']);
                }
                return new JsonModel(['success' => true, 'data' => $jobResponsibilityList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model = new JobResponsibility();
                $model->exchangeArrayFromForm($this->form->getData());
                $model->jobResNepName = base64_encode($model->jobResNepName);
                $model->jobResNepDescription = base64_encode($model->jobResNepDescription);
                $model->createdDt = Helper::getcurrentExpressionDate();
                $model->createdBy = $this->employeeId;
                $model->id = ((int) Helper::getMaxId($this->adapter, "HRIS_JOB_RESPONSIBILITY", "ID")) + 1;
                $model->status = 'E';
                $this->repository->add($model);

                $this->flashmessenger()->addMessage("Job Responsibility Successfully added!!!");
                return $this->redirect()->toRoute("jobResponsibility");
            }
        }
        return [
            'form' => $this->form,
            'customRender' => Helper::renderCustomView(),
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
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('jobResponsibility');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Job Responsibility Successfully Deleted!!!");
        return $this->redirect()->toRoute('jobResponsibility');
    }
}
