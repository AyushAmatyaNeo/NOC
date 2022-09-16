<?php
namespace SelfService\Model;

use Application\Model\Model;

class LoanEmiDetail extends Model{
    const TABLE_NAME = "HRIS_EMPLOYEE_EMI_DETAIL";
    const EMI_ID = "EMI_ID";
    const LOAN_REQUEST_ID = "LOAN_REQUEST_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const REPAYMENT_INSTALLMENTS ="REPAYMENT_INSTALLMENTS";
    const LOAN_AMOUNT = "LOAN_AMOUNT";
    const INSTALLMENT = "INSTALLMENT";
    const INTEREST = "INTEREST";
    const PRINCIPAL_REPAID = "PRINCIPAL_REPAID";
    const REMAINING_PRINCIPAL = "REMAINING_PRINCIPAL";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";

    
    public $emiId;
    public $loanRequestId;
    public $employeeId;
    public $repaymentInstallments;
    public $loanAmount;
    public $installment;
    public $interest;
    public $principalRepaid;
    public $remainingPrincipal;
    public $status;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;

    
    public $mappings = [
         'emiId'=> self::EMI_ID,
         'loanRequestId'=> self::LOAN_REQUEST_ID,
         'employeeId'=> self::EMPLOYEE_ID,
         'repaymentInstallments'=>self::REPAYMENT_INSTALLMENTS,
         'loanAmount'=>self::LOAN_AMOUNT,
         'installment'=>self::INSTALLMENT,
         'interest'=>self::INTEREST,
         'principalRepaid'=>self::PRINCIPAL_REPAID,
         'remainingPrincipal'=>self::REMAINING_PRINCIPAL,
         'status'=>self::STATUS,
         'createdDt'=>self::CREATED_DT,
         'createdBy'=>self::CREATED_BY,
         'modifiedDt'=>self::MODIFIED_DT,
         'modifiedBy'=>self::MODIFIED_BY,

    ];
    
}



        