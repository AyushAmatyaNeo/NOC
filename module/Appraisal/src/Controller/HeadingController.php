<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Appraisal\Repository\HeadingRepository;
use Appraisal\Model\Heading;
use Appraisal\Form\HeadingForm;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Appraisal\Model\Type;
use Setup\Repository\EmployeeRepository;
use Application\Helper\EntityHelper;

class HeadingController extends AbstractActionController{
    private $adapter;
    private $repository;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $this->repository = new HeadingRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        $this->userId = $auth->getStorage()->read()['user_id'];
    }
    public function initializeForm(){
        $form = new HeadingForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }
    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach($result as $row){
            array_push($list, $row);
        }
       // print_r($list); die();
        return Helper::addFlashMessagesToArray($this, [
           'headings'=>$list 
        ]);
    }
    public function addAction(){
        $this->initializeForm();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        
        $appraisalTypes = EntityHelper::getTableKVListWithSortOption($this->adapter, Type::TABLE_NAME, Type::APPRAISAL_TYPE_ID, [Type::APPRAISAL_TYPE_EDESC], ["STATUS" => "E"], "APPRAISAL_TYPE_EDESC", "ASC",NULL,FALSE,TRUE);
        $request = $this->getRequest();
        if($request->isPost()){
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $heading = new Heading();
                $heading->exchangeArrayFromForm($this->form->getData());
                if ($heading->appraisalTypeId == 0) {
                    unset($heading->appraisalTypeId);
                }
                $heading->createdDate = Helper::getcurrentExpressionDate();
                $heading->approvedDate = Helper::getcurrentExpressionDate();
                $heading->createdBy = $this->employeeId;
                $heading->companyId = $employeeDetail['COMPANY_ID'];
                $heading->branchId = $employeeDetail['BRANCH_ID'];
                $heading->headingId = ((int) Helper::getMaxId($this->adapter, "HRIS_APPRAISAL_HEADING", "HEADING_ID")) + 1;
                $heading->status = 'E';
                $this->repository->add($heading);
                $this->flashmessenger()->addMessage("Appraisal Heading Successfully added!!!");
                return $this->redirect()->toRoute("heading");
            }
        }
        return Helper::addFlashMessagesToArray($this, 
                [
                    'form'=>$this->form,
                    'appraisalTypes'=>$appraisalTypes
                ]);
    }
    public function editAction(){
        $id = $this->params()->fromRoute('id');
        if($id==0){
            $this->redirect()->toRoute('heading');
        }
        $this->initializeForm();
        
        $request = $this->getRequest();
        $heading= new Heading();
        if(!$request->isPost()){
            $heading->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($heading);
        }else{
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $heading->exchangeArrayFromForm($this->form->getData());
                if ($heading->appraisalTypeId == 0) {
                    unset($heading->appraisalTypeId);
                }
                $heading->modifiedDate = Helper::getcurrentExpressionDate();
                $heading->modifiedBy = $this->employeeId;
                $this->repository->edit($heading, $id);
                $this->flashmessenger()->addMessage("Appraisal Heading Successfully Updated!!!");
                return $this->redirect()->toRoute("heading");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=>$this->form,
            'id'=>$id,
            'appraisalTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter, Type::TABLE_NAME, Type::APPRAISAL_TYPE_ID, [Type::APPRAISAL_TYPE_EDESC], ["STATUS" => "E"], "APPRAISAL_TYPE_EDESC", "ASC",NULL,FALSE,TRUE)
        ]);
    }
    public function deleteAction(){
        $id = $this->params()->fromRoute('id');
        if($id==0){
            $this->redirect()->toRoute('heading');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Appraisal Heading Successfully Deleted!!!");
        return $this->redirect()->toRoute("heading");
    }
}
