<?php
namespace Setup\Model;

use Application\Model\Model;

class Loan extends Model{
    const TABLE_NAME = "HRIS_LOAN_MASTER_SETUP";
    const LOAN_ID = "LOAN_ID";
    const LOAN_CODE = "LOAN_CODE";
    const LOAN_NAME = "LOAN_NAME";
    const MIN_AMOUNT = "MIN_AMOUNT";
    const MAX_AMOUNT = "MAX_AMOUNT";
    const INTEREST_RATE = "INTEREST_RATE";
    const REPAYMENT_AMOUNT = "REPAYMENT_AMOUNT";
    const REPAYMENT_PERIOD = "REPAYMENT_PERIOD";
    const VALID_FROM = "VALID_FROM";
    const VALID_UPTO = "VALID_UPTO";
    const REMARKS = "REMARKS";
    const CREATED_DATE = "CREATED_DATE";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const STATUS = "STATUS";
    const LOAN_TYPE = "LOAN_TYPE";
    const ISSUED_BY = "ISSUED_BY";
    const PAY_ID_INT = "PAY_ID_INT";
    const PAY_ID_AMT = "PAY_ID_AMT";
    const IS_RATE_FLEXIBLE = "IS_RATE_FLEXIBLE";
    const ELIGIBLE_SERVICE_PERIOD = "ELIGIBLE_SERVICE_PERIOD";
    const MAX_ISSUE_TIME = "MAX_ISSUE_TIME";
    const LEDGER_CODE = "LEDGER_CODE";
    const DR_ACC_CODE = "DR_ACC_CODE";
    const CR_ACC_CODE = "CR_ACC_CODE";
    
    public $loanId;
    public $payIdInt;
    public $payIdAmt;
    public $isRateFlexible;
    public $loanCode;
    public $loanName;
    public $minAmount;
    public $maxAmount;
    public $interestRate;
    public $repaymentAmount;
    public $repaymentPeriod;
    public $validFrom;
    public $validUpto;
    public $remarks;
    public $createdDate;
    public $createdBy;
    public $modifiedDate;
    public $modifiedBy;
    public $status;
    public $issuedBy;
    public $loanType;
    public $eligibleServicePeriod;
    public $maxIssueTime;
    public $ledgerCode;
    public $drAccCode;
    public $crAccCode;
    
    public $mappings =[
        'loanId'=>self::LOAN_ID,
        'payIdInt'=>self::PAY_ID_INT,
        'payIdAmt'=>self::PAY_ID_AMT,
        'isRateFlexible'=>self::IS_RATE_FLEXIBLE,
        'loanCode'=>self::LOAN_CODE,
        'loanName'=>self::LOAN_NAME,
        'minAmount'=>self::MIN_AMOUNT,
        'maxAmount'=>self::MAX_AMOUNT,
        'interestRate'=>self::INTEREST_RATE,
        'repaymentAmount'=>self::REPAYMENT_AMOUNT,
        'repaymentPeriod'=>self::REPAYMENT_PERIOD,
        'status'=>self::STATUS,
        'remarks'=>self::REMARKS,
        'validFrom'=>self::VALID_FROM,
        'validUpto'=>self::VALID_UPTO,
        'createdDate'=>self::CREATED_DATE,
        'createdBy'=>self::CREATED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'issuedBy' => self::ISSUED_BY,
        'loanType' => self::LOAN_TYPE,
        'eligibleServicePeriod' => self::ELIGIBLE_SERVICE_PERIOD,
        'maxIssueTime' => self::MAX_ISSUE_TIME,
        'ledgerCode' => self::LEDGER_CODE,
        'drAccCode' => self::DR_ACC_CODE,
        'crAccCode' => self::CR_ACC_CODE
    ];
}