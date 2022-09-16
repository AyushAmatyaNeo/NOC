<?php

namespace DartaChalani\Model;

use Application\Model\Model;

class Chalani extends Model {

    const TABLE_NAME = "DC_DISPATCH_DRAFT";
    const DISPATCH_DRAFT_ID = "DISPATCH_DRAFT_ID";
    const DISPATCH_TEMP_CODE = "DISPATCH_TEMP_CODE";
    const DRAFT_DATE = "DRAFT_DATE";
    const FROM_DEPARTMENT_CODE = "FROM_DEPARTMENT_CODE";
    const DESCRIPTION = "DESCRIPTION";
    const TO_OFFICE_ID = "TO_OFFICE_ID";
    const DOCUMENT_DATE = "DOCUMENT_DATE";
    const FILE_PATH = "FILE_PATH";
    const REG_ID = "REG_ID";
    const COMPLETION_DATE = "COMPLETION_DATE";
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
    const LETTER_REF_NO = "LETTER_REF_NO";
    const RESPONSE_FLAG = "RESPONSE_FLAG";
    const PROCESS_ID = "PROCESS_ID";
    const FROM_LOCATION_ID = "FROM_LOCATION_ID";
    const TO_OTHER_OFFICE = "TO_OTHER_OFFICE"; 

    
    public $dispatchDraftId;
    public $dispatchTempCode;
    public $draftDt;
    public $fromDepartmentCode;
    public $description;
    public $toOfficeCode;
    public $documentDt;
    public $filePath;
    public $regId;
    public $completionDt;
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
    public $letterRefNo;
    public $responseFlag;
    public $processId;
    public $fromLocationId;
    public $toOtherOffice;
    public $mappings = [

        
        'dispatchDraftId' => self::DISPATCH_DRAFT_ID,
        'dispatchTempCode' => self::DISPATCH_TEMP_CODE,
        'draftDt' => self::DRAFT_DATE,
        'fromDepartmentCode' => self::FROM_DEPARTMENT_CODE,
        'description' => self::DESCRIPTION,
        'toOfficeCode' => self::TO_OFFICE_ID,
        'documentDt' => self::DOCUMENT_DATE,
        'filePath' => self::FILE_PATH,
        'regId' => self::REG_ID,
        'completionDt' => self::COMPLETION_DATE,
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
        'deletedDt' => self::DELETED_DT,
        'letterRefNo' => self::LETTER_REF_NO,
        'responseFlag' =>self::RESPONSE_FLAG,
        'processId' =>self::PROCESS_ID,
        'fromLocationId' => self::FROM_LOCATION_ID,
        'toOtherOffice' => self::TO_OTHER_OFFICE,

    ];

}
