<?php
namespace Setup\Model;

use Application\Model\Model;

class LoanRestriction extends Model{
    const TABLE_NAME = "HRIS_LOAN_RESTRICTIONS";
    const RESTRICTION_ID = "RESTRICTION_ID";
    const LOAN_ID = "LOAN_ID";
    const RESTRICTION_TYPE = "RESTRICTION_TYPE";
    const VALUE = "VALUE";
    const STATUS = "STATUS";
    const CREATED_DATE = "CREATED_DATE";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    
    public $restrictionId;
    public $loanId;
    public $restrictionType;
    public $value;
    public $status;
    public $createdBy;
    public $createdDate;
    public $modifiedDate;
    public $modifiedBy;
    
    public $mappings = [
        'restrictionId'=>self::RESTRICTION_ID,
        'restrictionType'=>self::RESTRICTION_TYPE,
        'loanId'=>self::LOAN_ID,
        'value'=>self::VALUE,
        'createdDate'=>self::CREATED_DATE,
        'createdBy'=>self::CREATED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'status'=>self::STATUS
    ];
}