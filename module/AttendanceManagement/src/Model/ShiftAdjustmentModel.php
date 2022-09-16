<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AttendanceManagement\Model;

use Application\Model\Model;


class ShiftAdjustmentModel extends Model {
    
    
    const TABLE_NAME = "HRIS_SHIFT_ADJUSTMENT";
    
    const ADJUSTMENT_ID = "ADJUSTMENT_ID";
    const START_TIME = "START_TIME";
    const END_TIME = "END_TIME";
    const ADJUSTMENT_START_DATE = "ADJUSTMENT_START_DATE";
    const ADJUSTMENT_END_DATE = "ADJUSTMENT_END_DATE";
    const CREATED_DT = "CREATED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    
    
    public $adjustmentId;
    public $startTime;
    public $endTime;
    public $adjustmentStartDate;
    public $adjustmentEndDate;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    
    
    public $mappings = [
        'adjustmentId'=> self::ADJUSTMENT_ID,
        'startTime'=>self::START_TIME,
        'endTime'=>self::END_TIME,
        'adjustmentStartDate'=>self::ADJUSTMENT_START_DATE,
        'adjustmentEndDate'=>self::ADJUSTMENT_END_DATE,
        'createdDt'=>self::CREATED_DT,
        'createdBy'=>self::CREATED_BY,
        'modifiedDt'=>self::MODIFIED_DT,
        'modifiedBy'=>self::MODIFIED_BY
    ];
    
    
}
