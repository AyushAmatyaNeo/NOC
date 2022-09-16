<?php
namespace Loan\Controller;

use Application\Controller\HrisController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Form\LoanRequestForm;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use SelfService\Repository\LoanRequestRepository;
use SelfService\Model\LoanRequest as LoanRequestModel;
use Setup\Model\Loan;
use ManagerService\Repository\LoanApproveRepository;
use Payroll\Model\FinanceData;
use Payroll\Repository\FinanceDataRepository;
use Exception;

class LoanApply extends HrisController{
    protected $loanRequesteRepository;
    protected $loanApproveRepository;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->loanApproveRepository = new LoanApproveRepository($adapter);
        $this->loanRequesteRepository = new LoanRequestRepository($adapter);
        $this->initializeForm(LoanRequestForm::class);
    }
    
    public function indexAction() {
       return $this->redirect()->toRoute("loanStatus");
    }

    public function addAction() {
        $request = $this->getRequest();
        $model = new LoanRequestModel();  

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                try{
                    $model->exchangeArrayFromForm($this->form->getData());
                    $model->loanRequestId = ((int) Helper::getMaxId($this->adapter, LoanRequestModel::TABLE_NAME, LoanRequestModel::LOAN_REQUEST_ID)) + 1;
                    $model->requestedDate = Helper::getcurrentExpressionDate();
                    $model->loanDate = Helper::getExpressionDate($model->loanDate);
                    $model->status = 'AP';
                    $model->createdBy = $this->employeeId;
                    $model->createdDate = Helper::getCurrentExpressionDate();
                    $model->approvedBy = $this->employeeId;
                    $model->approvedDate = Helper::getCurrentExpressionDate();
                    $model->deductOnSalary = 'Y';
                    $this->loanRequesteRepository->add($model);
                    $this->loanApproveRepository->addToDetails($model->loanRequestId);
                    $this->financialImpact($model);
                    $this->flashmessenger()->addMessage("Loan Request Successfully added!!!");
                    return $this->redirect()->toRoute("loanStatus");
                }
                catch(Exception $e){
                    throw new Exception('An error occured. '.$e);
                }
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'rateDetails' => Helper::extractDbData($this->loanRequesteRepository->getLoanDetails()),
                    'employees'=> EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"],["STATUS"=>'E','RETIRED_FLAG'=>'N'],"FIRST_NAME","ASC"," ",FALSE,TRUE, $this->employeeId),
                    'loans' => EntityHelper::getTableKVListWithSortOption($this->adapter, Loan::TABLE_NAME, Loan::LOAN_ID, [Loan::LOAN_NAME], [Loan::STATUS => "E"], Loan::LOAN_ID, "ASC",NULL,FALSE,TRUE),
        ]);
    }

    public function financialImpact($loanModel){
        $model = new FinanceData();
        $financeDataRepo = new FinanceDataRepository($this->adapter);
        $model->financeDataId = (int) Helper::getMaxId($this->adapter, FinanceData::TABLE_NAME, FinanceData::FINANCE_DATA_ID) + 1;
        $model->moduleCode = 'LN';
        $model->masterId = $loanModel->loanId;
        $model->requestId = $loanModel->loanRequestId;
        $model->amount = $loanModel->requestedAmount;
        $financeDataRepo->add($model);
        $financeDataRepo->financialDataGlEntry($model->financeDataId);
    }
}