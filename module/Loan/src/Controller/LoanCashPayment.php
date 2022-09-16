<?php
namespace Loan\Controller;

use Application\Controller\HrisController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Form\Annotation\AnnotationBuilder;
use Loan\Form\LoanCashPaymentForm;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Loan\Model\LoanCashPaymentModel;
use SelfService\Repository\LoanRequestRepository;
use Loan\Repository\LoanCashPaymentRepository;
use SelfService\Model\LoanRequest as LoanRequestModel;
use Setup\Model\Loan;
use ManagerService\Repository\LoanApproveRepository;

class LoanCashPayment extends HrisController{
    protected $form;
    protected $loanClosingRepository;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->loanRequesteRepository = new LoanRequestRepository($adapter);
        $this->initializeRepository(LoanCashPaymentRepository::class);
        $this->initializeForm(LoanCashPaymentForm::class);
    }
    
    public function indexAction() {
       return $this->redirect()->toRoute("loanStatus");
    }

    public function addAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id');
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model = new LoanCashPaymentModel();
                $model->exchangeArrayFromForm($this->form->getData());
                $model->id = ((int) Helper::getMaxId($this->adapter, LoanCashPaymentModel::TABLE_NAME, LoanCashPaymentModel::ID)) + 1;
                $model->paymentDate = Helper::getExpressionDate($model->paymentDate);
                $model->loanReqId = (int) $this->params()->fromRoute('id');
                $this->repository->add($model);
                $this->repository->editDetails($model->id);
                return $this->redirect()->toRoute("loanStatus");
            }
        }
        $empId = Helper::extractDbData($this->repository->getEmployeeByLoanRequestId($id))[0]['EMPLOYEE_ID'];
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'rate' => Helper::extractDbData($this->repository->getRateByLoanReqId($id))[0]['INTEREST_RATE'],
            'employee'=> EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"],["EMPLOYEE_ID"=>$empId,"STATUS"=>'E','RETIRED_FLAG'=>'N'],"FIRST_NAME","ASC"," ",FALSE,TRUE),
            'unpaidAmount'=>Helper::extractDbData($this->repository->getUnpaidAmount($id))[0]['UNPAID_AMOUNT']
        ]);
    }

    public function rectifyAction() {
        $this->initializeClosingForm();
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id');
        $model = new LoanCashPaymentModel();
        $paymentId = Helper::extractDbData($this->repository->getPaymentId($id))[0]["ID"];
        if ($request->isPost()) {
            $data = $request->getPost();
            $this->repository->rectify($paymentId, $data);
            $this->flashmessenger()->addMessage("Amount has been rectified successfully!!!");
            return $this->redirect()->toRoute("loanStatus");
        }  
        $detail = $this->repository->fetchById($paymentId);
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);
        $emp_id = $this->repository->getEmployeeByLoanRequestId($id);
        $emp_id = Helper::extractDbData($emp_id)[0]['EMPLOYEE_ID'];
        
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'rate' => Helper::extractDbData($this->repository->getRateByLoanReqId($id))[0]['INTEREST_RATE'],
            'employee'=> EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"],["EMPLOYEE_ID"=>$emp_id,"STATUS"=>'E','RETIRED_FLAG'=>'N'],"FIRST_NAME","ASC"," ",FALSE,TRUE),
            'unpaidAmount'=>Helper::extractDbData($this->repository->getUnpaidAmount($id))[0]['UNPAID_AMOUNT']
        ]);
    }
}