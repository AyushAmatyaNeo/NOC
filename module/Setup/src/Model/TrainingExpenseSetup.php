<?php

namespace Setup\Model;

use Application\Model\Model;

class TrainingExpenseSetup extends Model {

    const TABLE_NAME = "NOC_TRAINING_EXPENSE";
    const EXPENSE_ID = "EXPENSE_ID";
    const TRAINING_ID = "TRAINING_ID";
    const EXPENSE_HEAD_ID = "EXPENSE_HEAD_ID";
    const AMOUNT = "AMOUNT";
    const DESCRIPTION = "DESCRIPTION";
    const CREATED_DT = "CREATED_DT";
    const CREATED_BY = "CREATED_BY";
    const CHECKED_DATE = "CHECKED_DATE";
    const CHECKED_BY = "CHECKED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const STATUS = "STATUS";

    public $expenseId;
    public $trainingId;
    public $expenseHeadId;
    public $amount;
    public $description;
    public $createdDt;
    public $createdBy;
    public $checkedDate;
    public $checkedBy;
    public $modifiedDt;
    public $modifiedBy;
    public $approvedDate;
    public $approvedBy;
    public $status;

    public $mappings = [
        'expenseId' => self::EXPENSE_ID,
        'trainingId' => self::TRAINING_ID,
        'expenseHeadId' => self::EXPENSE_HEAD_ID,
        'amount' => self::AMOUNT,
        'description' => self::DESCRIPTION,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'checkedDate' => self::CHECKED_DATE,
        'checkedBy' => self::CHECKED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'approvedDate' => self::APPROVED_DATE,
        'approvedBy' => self::APPROVED_BY,
        'status' => self::STATUS
    ];

}
