<?php

namespace DartaChalani\Model;

use Application\Model\Model;

class Offices extends Model {

    const TABLE_NAME = "DC_OFFICES";
    const OFFICE_ID = "OFFICE_ID";
    const OFFICE_CODE = "OFFICE_CODE";
    const OFFICE_EDESC = "OFFICE_EDESC";
    const OFFICE_NDESC = "OFFICE_NDESC";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
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

    
    public $officeId;
    public $officeCode;
    public $officeEDESC;
    public $officeNDESC;
    public $remarks;
    public $status;
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

        'officeId' => self::OFFICE_ID,
        'officeCode' => self::OFFICE_CODE,
        'officeEDESC' => self::OFFICE_EDESC,
        'officeNDESC' => self::OFFICE_NDESC,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
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
