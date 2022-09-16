<?php

namespace Medical\Model;

use Application\Model\Model;

class Medical extends Model {

    const TABLE_NAME = "HRIS_MEDICAL";
    const MEDICAL_ID = "MEDICAL_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const CLAIM_OF = "CLAIM_OF";
    const E_R_ID = "E_R_ID";
    const AGE = "AGE";
    const OPERATION_FLAG = "OPERATION_FLAG";
    const TRANSACTION_DT = "TRANSACTION_DT";
    const REQUESTED_AMT = "REQUESTED_AMT";
    const REQUESTED_BY = "REQUESTED_BY";
    const REQUESTED_DT = "REQUESTED_DT";
    const APPROVED_AMT = "APPROVED_AMT";
    const APPROVED_DT = "APPROVED_DT";
    const APPROVED_BY = "APPROVED_BY";
    const BILL_STATUS = "BILL_STATUS";
    const BANK_TRANSFER_DT = "BANK_TRANSFER_DT";
    const TRANSFERRED_BY = "TRANSFERRED_BY";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";
    
    
    
    public $medicalId;
    public $employeeId;
    public $claimOf;
    public $eRId;
    public $age;
    public $operationFlag;
    public $transactionDt;
    public $requestedAmt;
    public $requestedBy;
    public $requestedDt;
    public $approvedAmt;
    public $approvedDt;
    public $approvedBy;
    public $billStatus;
    public $bankTransferDt;
    public $transferedBy;
    public $status;
    public $remarks;
    public $deletedBy;
    public $deletedDt;
    

    public $mappings = [
        'medicalId' => self::MEDICAL_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'claimOf' => self::CLAIM_OF,
        'eRId' => self::E_R_ID,
        'age' => self::AGE,
        'operationFlag' => self::OPERATION_FLAG,
        'transactionDt' => self::TRANSACTION_DT,
        'transactionDt' => self::TRANSACTION_DT,
        'requestedAmt' => self::REQUESTED_AMT,
        'requestedBy' => self::REQUESTED_BY,
        'requestedDt' => self::REQUESTED_DT,
        'approvedAmt' => self::APPROVED_AMT,
        'approvedDt' => self::APPROVED_DT,
        'approvedBy' => self::APPROVED_BY,
        'billStatus' => self::BILL_STATUS,
        'bankTransferDt' => self::BANK_TRANSFER_DT,
        'transferedBy' => self::TRANSFERRED_BY,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'deletedBy' => self::DELETED_BY,
        'deletedDt' => self::DELETED_DT
    ];

}
