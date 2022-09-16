<?php

namespace Asset\Model;

use Application\Model\Model;

class Issue extends Model {
    const TABLE_NAME = 'HRIS_ASSET_ISSUE';
    const ISSUE_ID = 'ISSUE_ID';
    const ISSUE_DATE = 'ISSUE_DATE';
    const ASSET_ID = 'ASSET_ID';
    const SNO = 'SNO';
    const EMPLOYEE_ID = 'EMPLOYEE_ID';
    const QUANTITY = 'QUANTITY';
    const REQUEST_DATE = 'REQUEST_DATE';
    const RETURN_DATE = 'RETURN_DATE';
    const PURPOSE = 'PURPOSE';
    const RETURNABLE = 'RETURNABLE';
    const AUTHORIZED_BY = 'AUTHORIZED_BY';
    const RETURNED = 'RETURNED';
    const RETURNED_DATE = 'RETURNED_DATE';
    const REMARKS = 'REMARKS';
    const COMPANY_ID = 'COMPANY_ID';
    const BRANCH_ID = 'BRANCH_ID';
    const CREATED_BY = 'CREATED_BY';
    const CREATED_DATE = 'CREATED_DATE';
    const MODIFIED_BY = 'MODIFIED_BY';
    const MODIFIED_DATE = 'MODIFIED_DATE';
    const APPROVED = 'APPROVED';
    const APPROVED_BY = 'APPROVED_BY';
    const APPROVED_DATE = 'APPROVED_DATE';
    const STATUS = 'STATUS';

    public $issueId;
    public $issueDate;
    public $assetId;
    public $sno;
    public $employeeId;
    public $quantity;
    public $requestDate;
    public $returnDate;
    public $purpose;
    public $returnable;
    public $autorizedBy;
    public $returned;
    public $returnedDate;
    public $remarks;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $approved;
    public $approveBy;
    public $approvedDate;
    public $status;


    public $mappings = [
    'issueId'=>self::ISSUE_ID,
    'issueDate'=>self::ISSUE_DATE,
    'assetId'=>self::ASSET_ID,
    'sno'=>self::SNO,
    'employeeId'=>self::EMPLOYEE_ID,
    'quantity'=>self::QUANTITY,
    'requestDate'=>self::REQUEST_DATE,
    'returnDate'=>self::RETURN_DATE,
    'purpose'=>self::PURPOSE,
    'returnable'=>self::RETURNABLE,
    'autorizedBy'=>self::AUTHORIZED_BY,
    'returned'=>self::RETURNED,
    'returnedDate'=>self::RETURNED_DATE,
    'remarks'=>self::REMARKS,
    'companyId'=>self::COMPANY_ID,
    'branchId'=>self::BRANCH_ID,
    'createdBy'=>self::CREATED_BY,
    'createdDate'=>self::CREATED_DATE,
    'modifiedBy'=>self::MODIFIED_BY,
    'modifiedDate'=>self::MODIFIED_DATE,
    'approved'=>self::APPROVED,
    'approveBy'=>self::APPROVED_BY,
    'approvedDate'=>self::APPROVED_DATE,
    'status'=>self::STATUS
    ];
}
