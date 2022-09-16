<?php

namespace Payroll\Model;

use Application\Model\Model;

class VarianceSetup extends Model {

    CONST TABLE_NAME = "HRIS_VARIANCE";
    CONST VARIANCE_ID = "VARIANCE_ID";
    CONST VARIANCE_NAME = "VARIANCE_NAME";
    CONST SHOW_DEFAULT = "SHOW_DEFAULT";
    CONST SHOW_DIFFERENCE = "SHOW_DIFFERENCE";
    CONST VARIABLE_TYPE = "VARIABLE_TYPE";
    CONST STATUS = "STATUS";
    CONST CREATED_DT = "CREATED_DT";
    CONST CREATED_BY = "CREATED_BY";
    CONST MODIFIED_BY = "MODIFIED_BY";
    CONST MODIFIED_DT = "MODIFIED_DT";
    CONST DELETED_DT = "DELETED_DT";
    CONST DELETED_BY = "DELETED_BY";
    CONST REMARKS = "REMARKS";
    CONST ORDER_NO = "ORDER_NO";
    CONST IS_SUM = "IS_SUM";
    CONST V_HEADS = "V_HEADS";

    public $varianceId;
    public $varianceName;
    public $showDefault;
    public $showDifference;
    public $variableType;
    public $status;
    public $createdDt;
    public $createdBy;
    public $modifiedBy;
    public $modifiedDt;
    public $deletedDt;
    public $deletedBy;
    public $remarks;
    public $orderNo;
    public $isSum;
    public $vHeads;
    
    public $mappings = [
        'varianceId' => self::VARIANCE_ID,
        'varianceName' => self::VARIANCE_NAME,
        'showDefault' => self::SHOW_DEFAULT,
        'showDifference' => self::SHOW_DIFFERENCE,
        'variableType' => self::VARIABLE_TYPE,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'deletedDt' => self::DELETED_DT,
        'deletedBy' => self::DELETED_BY,
        'remarks' => self::REMARKS,
        'orderNo' => self::ORDER_NO,
        'isSum' => self::IS_SUM,
        'vHeads' => self::V_HEADS
    ];

}
