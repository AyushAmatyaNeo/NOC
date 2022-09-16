<?php

namespace DartaChalani\Model;

use Application\Model\Model;

class Processes extends Model {

    const TABLE_NAME = "DC_PROCESSES";
    const PROCESS_ID = "PROCESS_ID";
    const PROCESS_EDESC = "PROCESS_EDESC";
    const PROCESS_NDESC = "PROCESS_NDESC";
    const PROCESS_START_FLAG = "PROCESS_START_FLAG";
    const PROCESS_END_FLAG = "PROCESS_END_FLAG";
    const DC_PROCESS_RANK = "DC_PROCESS_RANK";
    const IS_REGISTRATION = "IS_REGISTRATION";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const CHECKED_BY = "CHECKED_BY";
    const CHECKED_DATE = "CHECKED_DATE";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";

    
    public $processId;
    public $processEDESC;
    public $processNDESC;
    public $processStartFlag;
    public $processEndFlag;
    public $processRank;
    public $isRegistration;
    public $status;
    public $remarks;
    public $createdBy;
    public $createdDt;
    public $checkedBy;
    public $checkedDt;
    public $approvedBy;
    public $approvedDt;
    public $modifiedBy;
    public $modifiedDt;
    public $deletedBy;
    public $deletedDt;
    public $mappings = [

        'processId' => self::PROCESS_ID,
        'processEDESC' => self::PROCESS_EDESC,
        'processNDESC' => self::PROCESS_NDESC,
        'processStartFlag' => self::PROCESS_START_FLAG,
        'processEndFlag' => self::PROCESS_END_FLAG,
        'processRank' => self::DC_PROCESS_RANK,
        'isRegistration' => self::IS_REGISTRATION,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'checkedBy' => self::CHECKED_BY,
        'checkedDt' => self::CHECKED_DATE,
        'approvedBy' => self::APPROVED_BY,
        'approvedDt' => self::APPROVED_DATE,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'deletedBy' => self::DELETED_BY,
        'deletedDt' => self::DELETED_DT
    ];

}
