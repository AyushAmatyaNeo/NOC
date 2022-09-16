<?php
namespace Loan\Model;

use Application\Model\Model;

class LoanCashPaymentModel extends Model{
    const TABLE_NAME = "HRIS_LOAN_CASH_PAYMENT";
    const ID = "ID";
    const PAYMENT_DATE = "PAYMENT_DATE";
    const LOAN_REQ_ID = "LOAN_REQ_ID";
    const PAYMENT_AMOUNT = "PAYMENT_AMOUNT";
    const PRINCIPLE_AMOUNT = "PRINCIPLE_AMOUNT";
    const INTEREST = "INTEREST";
    const RECEIPT_NO = "RECEIPT_NO";
    const REMARKS = "REMARKS";
     
    public $id;
    public $paymentDate;
    public $loanReqId;
    public $paymentAmount;
    public $principleAmount;
    public $interest;
    public $receiptNo;
    public $remarks;
     
    public $mappings = [
        'id'=> self::ID,
        'paymentDate'=> self::PAYMENT_DATE,
        'loanReqId'=> self::LOAN_REQ_ID,
        'paymentAmount'=> self::PAYMENT_AMOUNT,
        'principleAmount'=> self::PRINCIPLE_AMOUNT,
        'interest'=>self::INTEREST,
        'receiptNo'=>self::RECEIPT_NO,
        'remarks'=>self::REMARKS
    ];
}