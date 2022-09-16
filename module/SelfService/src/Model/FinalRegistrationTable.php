<?php

namespace SelfService\Model;

use Application\Model\Model;

class FinalRegistrationTable extends Model {

    const TABLE_NAME = "DC_REGISTRATION";
    const REG_ID = "REG_ID";
    const REG_TEMP_CODE = "REG_TEMP_CODE";
    const REG_DATE = "REG_DATE"; 
    const LETTER_REF_NO = "LETTER_REF_NO"; 
    const FROM_OFFICE_ID = "FROM_OFFICE_ID"; 
    const DESCRIPTION = "DESCRIPTION"; 
    const LETTER_REF_DATE = "LETTER_REF_DATE";
    const DEPARTMENT_ID = "DEPARTMENT_ID"; 
    const RECEIVER_NAME = "RECEIVER_NAME";
    const FILE_PATH = "FILE_PATH";
    const RESPONSE_FLAG = "RESPONSE_FLAG";
    const EST_RESPONSE_DATE = "EST_RESPONSE_DATE";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const APPROVED_BY = "APPROVED_BY"; 
    const APPROVED_DATE = "APPROVED_DATE"; 
    const MODIFIED_BY = "MODIFIED_BY";
    const CREATED_DT = "CREATED_DT";
    const CHECKED_BY = "CHECKED_BY";
    const CHECKED_DT = "CHECKED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const DELETED_BY = "DELETED_BY";
    const DELETED_DT = "DELETED_DT";

    public $registrationID;
    public $registrationTempCode;
    public $registrationDate;
    public $receivingLetterReferenceNo;
    public $fromOfficeId;
    public $description;
    public $receivingLetterReferenceDate;
    public $departmentId;
    public $receiverName;
    public $filePath;
    public $responseFlag;
    public $estResponseDate;
    public $remarks;
    public $status;
    public $createdBy;    
    public $createdDt;    
    public $checkedBy;    
    public $checkedDate;    
    public $approvedBy;
    public $approvedDate;
    public $modifiedBy;
    public $modifiedDt;
    public $deletedBy;
    public $deletedDt;


    public $mappings = [
        'registrationID' => self:: REG_ID,
        'registrationTempCode' => self:: REG_TEMP_CODE,
        'registrationDate' => self:: REG_DATE,
        'receivingLetterReferenceNo' => self:: LETTER_REF_NO,
        'fromOfficeId' => self:: FROM_OFFICE_ID,
        'description' => self:: DESCRIPTION,
        'receivingLetterReferenceDate' => self:: LETTER_REF_DATE,
        'departmentId' => self:: DEPARTMENT_ID,
        'receiverName' => self:: RECEIVER_NAME,
        'filePath' => self:: FILE_PATH,
        'responseFlag' => self:: RESPONSE_FLAG,
        'estResponseDate' => self:: EST_RESPONSE_DATE,
        'remarks' => self:: REMARKS,
        'status' => self:: STATUS,
        'createdBy' => self:: CREATED_BY,
        'createdDt' => self:: CREATED_DT,
        'checkedBy' => self:: CHECKED_BY,
        'checkedDate' => self:: CHECKED_DT,
        'approvedBy' => self:: APPROVED_BY,
        'approvedDate' => self:: APPROVED_DATE,
        'modifiedBy' => self:: MODIFIED_BY,
        'modifiedDt' => self:: MODIFIED_DT,
        'deletedBy' => self:: DELETED_BY,
        'deletedDt' => self:: DELETED_DT,
    ];

}
