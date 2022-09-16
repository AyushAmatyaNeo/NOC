<?php

namespace DocumentRegistration\Model;

use Application\Model\Model;

class IncommingDocument extends Model {

    const TABLE_NAME = "DC_REGISTRATION_DRAFT";
    const REG_DRAFT_ID = "REG_DRAFT_ID";
    const REG_TEMP_CODE = "REG_TEMP_CODE";
    const DRAFT_DATE = "DRAFT_DATE";
    const LETTER_REF_NO = "LETTER_REF_NO";
    const FROM_OFFICE_ID = "FROM_OFFICE_ID";
    const DESCRIPTION= "DESCRIPTION";
    const LETTER_REF_DATE = "LETTER_REF_DATE";
    const DEPARTMENT_ID = "DEPARTMENT_ID";
    const RECEIVER_NAME = "RECEIVER_NAME";
    const DOCUMENT_DATE = "DOCUMENT_DATE";
    const FILE_PATH = "FILE_PATH";
    const RESPONSE_FLAG = "RESPONSE_FLAG";
    const REMARKS = "REMARKS";
    const COMPLETION_DATE = "COMPLETION_DATE";
    const STATUS = "STATUS";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const CHECKED_BY = "CHECKED_BY";
    const CHECKED_DATE = "CHECKED_DATE";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const PROCESS_ID = 'PROCESS_ID';
    const SB_FISCAL_YR = 'SB_FISCAL_YR';
    const EMPLOYEE_ID = 'EMPLOYEE_ID';
    const KS_FISCAL_YR = 'KS_FISCAL_YR';
    const EMP_ID = 'EMP_ID';
    const FROM_LOCATION_ID = 'FROM_LOCATION_ID';
    const LOCATION_ID = 'LOCATION_ID';
    const FROM_OTHER_OFFICE = 'FROM_OTHER_OFFICE';

    public $registrationDraftID;
    public $registrationTempCode;
    public $registrationDate;
    public $receivingLetterReferenceNo;
    public $fromOfficeId;
    public $description;
    public $receivingLetterReferenceDate;
    public $departmentId;
    public $receivingDepartment;
    public $receiverName;
    public $documentDate;
    public $filesUpload;
    public $responseFlag;
    public $remarks;
    public $completionDate;
    public $status;
    public $createdBy;    
    public $createdDt;    
    public $checkedBy;    
    public $checkedDate;    
    public $approvedBy;
    public $approvedDate;
    public $processId;
    public $receiverId;
    public $sbFiscalYear;
    public $choiceFlag;
    public $employeeId;
    public $ksFiscalYear;
    public $choiceFlagKS;
    public $empId;
    public $fromLocationId;
    public $locationId;
    public $fromOtherOffice;


    public $mappings = [
        'registrationDraftID' => self:: REG_DRAFT_ID, 
        'registrationTempCode' => self:: REG_TEMP_CODE,
        'registrationDate' => self:: DRAFT_DATE,
        'receivingLetterReferenceNo' => self:: LETTER_REF_NO,
        'fromOfficeId' => self:: FROM_OFFICE_ID,
        'description' => self:: DESCRIPTION,
        'departmentId' => self:: DEPARTMENT_ID,
        'receiverId' => self:: RECEIVER_NAME,
        'receivingLetterReferenceDate' => self:: LETTER_REF_DATE,
        'receivingDepartment' => self:: DEPARTMENT_ID,
        'receiverName' => self:: RECEIVER_NAME,
        'documentDate' => self:: DOCUMENT_DATE,
        'filesUpload' => self:: FILE_PATH,
        'responseFlag' => self:: RESPONSE_FLAG,
        'remarks' => self:: REMARKS,
        'completionDate' => self:: COMPLETION_DATE,
        'status' => self:: STATUS,
        'createdBy' => self:: CREATED_BY,
        'createdDt' => self:: CREATED_DT,
        'checkedBy' => self:: CHECKED_BY,
        'checkedDate' => self:: CHECKED_DATE,
        'approvedBy' => self:: APPROVED_BY,
        'approvedDate' => self:: APPROVED_DATE,
        'processId' => self:: PROCESS_ID,
        'choiceFlag' => self:: SB_FISCAL_YR,
        'sbFiscalYear' => self:: SB_FISCAL_YR,
        'employeeId' => self:: EMPLOYEE_ID,
        'choiceFlagKS' => self:: KS_FISCAL_YR,
        'ksFiscalYear' => self:: KS_FISCAL_YR,
        'empId' => self:: EMP_ID,
        'fromLocationId' => self::FROM_LOCATION_ID,
        'locationId' => self::LOCATION_ID,
        'fromOtherOffice' =>self::FROM_OTHER_OFFICE,
        
    ];

}
