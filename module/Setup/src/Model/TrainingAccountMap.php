<?php

namespace Setup\Model;

use Application\Model\Model;

class TrainingAccountMap extends Model {

    const TABLE_NAME = "NOC_TRAINING_EXPENSE_HEAD";
    const EXPENSE_HEAD_ID = "EXPENSE_HEAD_ID";
    const TRAINING_ID = "TRAINING_ID";
    const EXPENSE_NAME = "EXPENSE_NAME";
    const ACCOUNT_CODE = "ACCOUNT_CODE";

    public $expenseHeadId;
    public $expenseName;
    public $accountCode;

    public $mappings = [
        'expenseHeadId' => self::EXPENSE_HEAD_ID,
        'expenseName' => self::EXPENSE_NAME,
        'accountCode' => self::ACCOUNT_CODE
    ];

}
