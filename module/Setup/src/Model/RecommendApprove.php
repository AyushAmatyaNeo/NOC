<?php

namespace Setup\Model;

use Application\Model\Model;

class RecommendApprove extends Model {

    const TABLE_NAME = "HRIS_RECOMMENDER_APPROVER";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const RECOMMEND_BY = "RECOMMEND_BY";
    const APPROVED_BY = "APPROVED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";
    
    
    const RECOMMENDER_VALUE = 2;
    const APPROVER_VALUE = 3;
    const BOTH_VALUE = 4;

    public $employeeId;
    public $recommendBy;
    public $approvedBy;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;
    public $mappings = [
        'employeeId' => self::EMPLOYEE_ID,
        'recommendBy' => self::RECOMMEND_BY,
        'approvedBy' => self::APPROVED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
    ];

}
