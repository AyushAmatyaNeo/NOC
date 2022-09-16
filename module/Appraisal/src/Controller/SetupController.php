<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Appraisal\Model\Setup;
use Appraisal\Form\SetupForm;
use Appraisal\Repository\SetupRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Authentication\AuthenticationService;
use Appraisal\Model\Type;
use Appraisal\Model\Stage;
use Setup\Repository\EmployeeRepository;
use Appraisal\Repository\AppraisalAssignRepository;

class SetupController extends AbstractActionController{
    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->repository = new SetupRepository($adapter);
        $this->adapter = $adapter;
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
    }
    public function initializeForm(){
        $form = new SetupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }
    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach($result as $row){
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['appraisals'=>$list]);
    }
    public function addAction(){
        $this->initializeForm();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        $request = $this->getRequest();
        $appraisalTypes = EntityHelper::getTableKVListWithSortOption($this->adapter, Type::TABLE_NAME, Type::APPRAISAL_TYPE_ID, [Type::APPRAISAL_TYPE_EDESC], ["STATUS" => "E"], Type::APPRAISAL_TYPE_EDESC, "ASC",NULL,FALSE,TRUE);
        $stages = EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::STAGE_EDESC], ["STATUS" => "E"], Stage::STAGE_EDESC, "ASC",NULL,FALSE,TRUE);
        if($request->isPost()){
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $appraisalModel = new Setup();
                $appraisalModel->exchangeArrayFromForm($this->form->getData());
                $appraisalModel->createdDate = Helper::getcurrentExpressionDate();
                $appraisalModel->approvedDate = Helper::getcurrentExpressionDate();
                $appraisalModel->createdBy = $this->employeeId;
                $appraisalModel->companyId = $employeeDetail['COMPANY_ID'];
                $appraisalModel->branchId = $employeeDetail['BRANCH_ID'];
                $appraisalModel->appraisalId = ((int) Helper::getMaxId($this->adapter,Setup::TABLE_NAME, Setup::APPRAISAL_ID)) + 1;
                $appraisalModel->status = 'E';
                //print_r($appraisalModel); die();
                $this->repository->add($appraisalModel);
                $this->flashmessenger()->addMessage("Appraisal Detail Successfully added!!!");
                return $this->redirect()->toRoute("detailSetup"); 
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=>$this->form,
            'appraisalTypes'=>$appraisalTypes,
            'appraisalStages'=>$stages,
            'customRender'=>Helper::renderCustomView()
        ]);
    }
    public function editAction(){
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $id = $this->params()->fromRoute('id');
        if($id===0){
            $this->redirect()->toRoute('detailSetup');
        }
        $this->initializeForm();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        $appraisalTypes = EntityHelper::getTableKVListWithSortOption($this->adapter, Type::TABLE_NAME, Type::APPRAISAL_TYPE_ID, [Type::APPRAISAL_TYPE_EDESC], ["STATUS" => "E"], Type::APPRAISAL_TYPE_EDESC, "ASC",NULL,FALSE,TRUE);
        $stages = EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::STAGE_EDESC], ["STATUS" => "E"], Stage::STAGE_EDESC, "ASC",NULL,FALSE,TRUE);
        $request = $this->getRequest();
        $appraisalModel = new Setup();
        if(!$request->isPost()){
            $appraisalModel->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($appraisalModel);
        }else{
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $appraisalModel->exchangeArrayFromForm($this->form->getData());
                $appraisalModel->modifiedBy = $this->employeeId;
                $appraisalModel->modifiedDate = Helper::getcurrentExpressionDate();
                $this->repository->edit($appraisalModel, $id);
                $appraisalAssignRepo->updateCurrentStageByAppId($appraisalModel->currentStageId,$id);
                $this->flashmessenger()->addMessage("Appraial Detail Successfully Updated!!!");
                $this->redirect()->toRoute('detailSetup');
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=>$this->form,
            'appraisalTypes'=>$appraisalTypes,
            'appraisalStages'=>$stages,
            'customRender'=>Helper::renderCustomView(),
            'id'=>$id
        ]);        
    }
    public function deleteAction(){
        $id = $this->params()->fromRoute('id');
        if($id===0){
            $this->redirect()->toRoute('detailSetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Appraisal Detail Successfully Deleted!!!");
        $this->redirect()->toRoute('detailSetup');
    }
}