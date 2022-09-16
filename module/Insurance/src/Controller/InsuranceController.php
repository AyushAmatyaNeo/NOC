<?php 
namespace Insurance\Controller;

use Application\Controller\HrisController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Insurance\Form\InsuranceEmployeeForm;
use Insurance\Form\InsuranceEmployeeDTLForm;
use Insurance\Model\InsuranceDtl;
use Insurance\Model\InsuranceEmployee;
use Zend\Db\Sql\Expression;
use Zend\Form\Annotation\AnnotationBuilder;
use Exception;
use Insurance\Repository\InsuranceDtlRepository;
use Insurance\Repository\InsuranceEmpRepository;
class InsuranceController extends HrisController{

    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        parent::__construct($adapter, $storage);
        // $this->initializeRepository(IncomingRepo::class);
        //$this->initializeForm(InsuranceEmployeeForm::class);
        $this->initializeForm(InsuranceEmployeeDTLForm::class);
        $this->initializeRepository(InsuranceDtlRepository::class);

    }
    public function indexAction()
    {
        return new ViewModel();
    }

    public function insuranceEmployeeAction(){
        $repo = new InsuranceEmpRepository($this->adapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $recordList = $repo->getInsuranceEmpTable();
                return new JsonModel([
                    "success" => "true",
                    "data" => $recordList,
                    "message" => null
                ]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }
        else{
            return new ViewModel();
        }
    }
    public function getEmployeeTableAction(){
        $this->initializeRepository(InsuranceEmpRepository::class);
        try {
            $recordList = $this->repository->getInsuranceEmpTable();
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
    public function deleteEmpAction()
    {
        $this->initializeRepository(InsuranceEmpRepository::class);
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('insurance-emp');
        }
        $insuranceEmp = new InsuranceEmployee();
        $insuranceEmp->deletedDt = Helper::getcurrentExpressionDate();
        $insuranceEmp->deletedBy = $this->employeeId;
        $this->repository->deleteById($id, $insuranceEmp);
        
        //$this->$insuranceDtl->createdDt = new Expression("SYSDATE");
        $this->flashmessenger()->addMessage("Successfully Deleted!!!");
        return $this->redirect()->toRoute('insurance-emp', array(
            'controller' => 'InsuranceController',
            'action' =>  'insuranceEmployee'
        ));
    }
    public function editEmpAction(){
        $id = $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $insuranceEmp = new InsuranceEmployee();
        $this->initializeForm(InsuranceEmployeeForm::class);
        $this->initializeRepository(InsuranceEmpRepository::class);
        if (!$request->isPost()) {
            $employeeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FULL_NAME", "ASC", null, true);
            $insuranceList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_INSURANCE_SETUP', 'INSURANCE_ID', ['INSURANCE_ENAME'], "STATUS='E'", "INSURANCE_ENAME", "ASC", null, true);
            $insuranceEmp->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($insuranceEmp);
        }
        else{
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $insuranceEmp = new InsuranceEmployee();
                $insuranceEmp->exchangeArrayFromForm($this->form->getData());
                
                // $insuranceDtl->insuranceId = ((int) Helper::getMaxId($this->adapter, "HRIS_INSURANCE_SETUP", "INSURANCE_ID")) + 1;
                $insuranceEmp->status = 'E';
                
                // $insurance->createdDt = Helper::getcurrentExpressionDateTime();
                $insuranceEmp->modifiedDt = Helper::getcurrentExpressionDate();
                $insuranceEmp->modifiedBy = $this->employeeId;
                $insuranceEmp->insuranceDt = Helper::getExpressionDate($insuranceEmp->insuranceDt);
                $insuranceEmp->maturedDt = Helper::getExpressionDate($insuranceEmp->maturedDt);
                $this->repository->edit($insuranceEmp, $id);
                $this->flashmessenger()->addMessage("Successfully Edited!!!");
                return $this->redirect()->toRoute('insurance-emp', array(
                    'controller' => 'InsuranceController',
                    'action' =>  'insuranceEmployee'
                ));
                
            }
        }
        // print_r($insuranceList); die;
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'employee' => $insuranceEmp->employeeId,
            'insurance' => $insuranceEmp->insuranceId,
            'employeeList' => $employeeList,
            'insuranceList' => $insuranceList
        ]);
    }
    public function addInsEmpAction(){
        $this->initializeForm(InsuranceEmployeeForm::class);
        $this->initializeRepository(InsuranceEmpRepository::class);
        $employeeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FULL_NAME", "ASC", null, true);
        $insuranceList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_INSURANCE_SETUP', 'INSURANCE_ID', ['INSURANCE_ENAME'], "STATUS='E'", "INSURANCE_ENAME", "ASC", null, true);
        $request = $this->getRequest();

        if ($request->isPost()) {
            // print_r($request->getPost()); die;
            $this->form->setData($request->getPost());
           
            if ($this->form->isValid()) {
                $insuranceEmp = new InsuranceEmployee();
                $insuranceEmp->exchangeArrayFromForm($this->form->getData());
                $insuranceEmp->insuranceEmpId = ((int) Helper::getMaxId($this->adapter, "HRIS_EMPLOYEE_INSURANCE", "INSURANCE_EMP_ID")) + 1;
                $insuranceEmp->status = 'E';
                // if(insurance->type = 'SW'){
                //     $insurance->flatAmt = null;
                // }
                // $insurance->createdDt = Helper::getcurrentExpressionDateTime();
                $insuranceEmp->createdDt = Helper::getcurrentExpressionDate();
                $insuranceEmp->createdBy = $this->employeeId;
                $insuranceEmp->insuranceDt = Helper::getExpressionDate($insuranceEmp->insuranceDt);
                $insuranceEmp->maturedDt = Helper::getExpressionDate($insuranceEmp->maturedDt);
                $insuranceEmp->modifiedDt = Helper::getExpressionDate($insuranceEmp->modifiedDt);
                $insuranceEmp->deletedDt = Helper::getExpressionDate($insuranceEmp->deletedDt);
                $insuranceEmp->checkedDt = Helper::getExpressionDate($insuranceEmp->checkedDt);
                $insuranceEmp->approvedDt = Helper::getExpressionDate($insuranceEmp->approvedDt);
                $this->repository->add($insuranceEmp);
                $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute('insurance-emp', array(
                    'controller' => 'InsuranceController',
                    'action' =>  'insuranceEmployee'
                ));
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'employeeList' => $employeeList,
            'insuranceList' => $insuranceList,
        ]);   
    }


    public function employeeDtlAction(){
        return new ViewModel();
    }
    public function getTableDataAction(){
        try {
            $recordList = $this->repository->getInsuranceDtlTable();
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
    public function deleteDtlAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('insurance-emp-dtl');
        }
        $insuranceDtl = new InsuranceDtl();
        $insuranceDtl->deletedDt = Helper::getcurrentExpressionDate();
        $insuranceDtl->deletedBy = $this->employeeId;
        $this->repository->deleteById($id, $insuranceDtl);
        
        //$this->$insuranceDtl->createdDt = new Expression("SYSDATE");
        $this->flashmessenger()->addMessage("Successfully Deleted!!!");
        return $this->redirect()->toRoute('insurance-emp-dtl');
    }
    public function editDtlAction(){
        $id = $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $insuranceDtl = new InsuranceDtl();
        if (!$request->isPost()) {
            // $insuranceDtl = new InsuranceDtl();
            // $insuranceDtl->deletedDt = new Expression("SYSDATE");;
            // $insuranceDtl->deletedBy = $this->employeeId;
            $employeeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FULL_NAME", "ASC", null, true);
            $insuranceList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_INSURANCE_SETUP', 'INSURANCE_ID', ['INSURANCE_ENAME'], "STATUS='E'", "INSURANCE_ENAME", "ASC", null, true);
            $insuranceDtl->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($insuranceDtl);
        }
        else{
            // print_r($request->getPost()); die;

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $insuranceDtl = new InsuranceDtl();
                $insuranceDtl->exchangeArrayFromForm($this->form->getData());
                
                // $insuranceDtl->insuranceId = ((int) Helper::getMaxId($this->adapter, "HRIS_INSURANCE_SETUP", "INSURANCE_ID")) + 1;
                $insuranceDtl->status = 'E';
                
                // $insurance->createdDt = Helper::getcurrentExpressionDateTime();
                $insuranceDtl->modifiedDt = Helper::getcurrentExpressionDate();
                $insuranceDtl->modifiedBy = $this->employeeId;
                $insuranceDtl->premiumDt = Helper::getExpressionDate($insuranceDtl->premiumDt);
                // echo"<pre>";print_r($insuranceDtl); die;
                $this->repository->edit($insuranceDtl, $id);
                $this->flashmessenger()->addMessage("Successfully Edited!!!");
                return $this->redirect()->toRoute("insurance-emp-dtl");
                
            }
        }
        // print_r($insuranceList); die;
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'employee' => $insuranceDtl->employeeId,
            'insurance' => $insuranceDtl->insuranceId,
            'employeeList' => $employeeList,
            'insuranceList' => $insuranceList
        ]);
    }
    public function addInsDtlAction(){
        //print_r("ok"); die;
        $employeeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FULL_NAME", "ASC", null, true);
        $insuranceList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_INSURANCE_SETUP', 'INSURANCE_ID', ['INSURANCE_ENAME'], "STATUS='E'", "INSURANCE_ENAME", "ASC", null, true);
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            // print_r($this->form); die;
            //print_r($request->getPost());die;
            //print_r("ok"); die;
            if ($this->form->isValid()) {
                // print_r("ok"); die;
                $insuranceDtl = new InsuranceDtl();
                $insuranceDtl->exchangeArrayFromForm($this->form->getData());
                $insuranceDtl->insuranceDtlId= ((int) Helper::getMaxId($this->adapter, "HRIS_EMPLOYEE_INSURANCE_DTL", "INSURANCE_DTL_ID")) + 1;
                $insuranceDtl->status = 'E';
                $insuranceDtl->createdDt = Helper::getcurrentExpressionDate();
                $insuranceDtl->createdBy = $this->employeeId;
                $insuranceDtl->premiumDt = Helper::getExpressionDate($insuranceDtl->premiumDt);
                // echo"<pre>";print_r($insuranceDtl);die;
                $this->repository->add($insuranceDtl);
                $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("insurance-emp-dtl");
                
            }
            // print_r("fail"); die;
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'employeeList' => $employeeList,
            'insuranceList' => $insuranceList,
        ]);   
    }

    
}
?>