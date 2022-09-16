<?php

namespace Payroll\Model;

use Application\Model\Model;

class VariancePayhead extends Model {

    CONST TABLE_NAME = "HRIS_VARIANCE_PAYHEAD";
    CONST VARIANCE_ID = "VARIANCE_ID";
    CONST PAY_ID = "PAY_ID";

    public $varianceId;
    public $payId;
    
    public $mappings = [
        'varianceId' => self::VARIANCE_ID,
        'payId' => self::PAY_ID,
    ];

}
