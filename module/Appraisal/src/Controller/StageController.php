<?php

namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Appraisal\Repository\StageRepository;
use Appraisal\Form\StageForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;
use Appraisal\Model\Stage;
use Setup\Repository\EmployeeRepository;

class StageController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $userId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new StageRepository($adapter);
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
        $this->userId = $authService->getStorage()->read()['user_id'];
    }

    public function initializeForm() {
        $form = new StageForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    "stages" => $list
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $stage = new Stage();
                $stage->exchangeArrayFromForm($this->form->getData());
                $stage->createdDate = Helper::getcurrentExpressionDate();
                $stage->approvedDate = Helper::getcurrentExpressionDate();
                $stage->companyId = $employeeDetail['COMPANY_ID'];
                $stage->branchId = $employeeDetail['BRANCH_ID'];
                $stage->createdBy = $this->employeeId;
                $stage->stageId = ((int) Helper::getMaxId($this->adapter, "HRIS_APPRAISAL_STAGE", "STAGE_ID")) + 1;
                $stage->status = 'E';
                $this->repository->add($stage);
                $this->flashmessenger()->addMessage("Appraisal Stage Successfully added!!!");
                return $this->redirect()->toRoute("stage");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form
        ]);
    }

    public function editAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('stage');
        }
        $this->initializeForm();

        $request = $this->getRequest();
        $stage = new Stage();
        if (!$request->isPost()) {
            $stage->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($stage);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $stage->exchangeArrayFromForm($this->form->getData());
                $stage->modifiedDate = Helper::getcurrentExpressionDate();
                $stage->modifiedBy = $this->employeeId;
                $this->repository->edit($stage, $id);
                $this->flashmessenger()->addMessage("Appraisal Stage Successfully Updated!!!");
                return $this->redirect()->toRoute("stage");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
        ]);
    }

    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('stage');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Appraisal Stage Successfully Deleted!!!");
        return $this->redirect()->toRoute("stage");
    }

}
