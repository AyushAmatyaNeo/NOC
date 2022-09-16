<?php

namespace DartaChalani\Model;

use Application\Model\Model;

class ChalaniFinal extends Model {

    const TABLE_NAME = "DC_DISPATCH";
    const DISPATCH_ID = "DISPATCH_ID";
    const DISPATCH_TEMP_CODE = "DISPATCH_TEMP_CODE";
    const DISPATCH_CODE = "DISPATCH_CODE";
    const LETTER_NUMBER = "LETTER_NUMBER";
    const FROM_DEPARTMENT_CODE = "FROM_DEPARTMENT_CODE";
    const DESCRIPTION = "DESCRIPTION";
    const TO_OFFICE_ID = "TO_OFFICE_ID";
    const DOCUMENT_DATE = "DOCUMENT_DATE";
    const FILE_PATH = "FILE_PATH";
    const REG_ID = "REG_ID";
    const DISPATCH_DATE = "DISPATCH_DATE";
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
    const FROM_LOCATION_ID = "FROM_LOCATION_ID";
    const TO_OTHER_OFFICE = "TO_OTHER_OFFICE"; 
    
    public $dispatchId;
    public $dispatchTempCode;
    public $dispatchCode;
    public $letterNumber;
    public $fromDepartmentCode;
    public $description;
    public $toOfficeCode;
    public $documentDt;
    public $filePath;
    public $regId;
    public $dispatchDt;
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
    public $fromLocationId;
    public $toOtherOffice;
    public $mappings = [

        
        'dispatchId' => self::DISPATCH_ID,
        'dispatchTempCode' => self::DISPATCH_TEMP_CODE,
        'dispatchCode' => self::DISPATCH_CODE,
        'letterNumber' => self::LETTER_NUMBER,
        'fromDepartmentCode' => self::FROM_DEPARTMENT_CODE,
        'description' => self::DESCRIPTION,
        'toOfficeCode' => self::TO_OFFICE_ID,
        'documentDt' => self::DOCUMENT_DATE,
        'filePath' => self::FILE_PATH,
        'regId' => self::REG_ID,
        'dispatchDt' => self::DISPATCH_DATE,
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
        'fromLocationId' => self::FROM_LOCATION_ID,
        'toOtherOffice' => self::TO_OTHER_OFFICE
    ];

}
