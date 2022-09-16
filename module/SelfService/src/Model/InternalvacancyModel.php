<?php

namespace SelfService\Model;

use Application\Model\Model;

class InternalvacancyModel extends Model {

    const TABLE_NAME = "HRIS_REC_VACANCY_APPLICATION";
    const APPLICATION_ID = "APPLICATION_ID";
    const USER_ID = "USER_ID";
    const AD_NO = "AD_NO"; 
    const REGISTRATION_NO = "REGISTRATION_NO"; 
    const STAGE_ID = "STAGE_ID"; 
    const APPLICATION_AMOUNT = "APPLICATION_AMOUNT"; 
    const STATUS = "STATUS";    
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const REMARKS = 'REMARKS';
    
    public $applicationId;
    public $userId;
    public $ad_no;
    public $registration_no;
    public $stage_id;
    public $application_amount;   
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;
    


    public $mappings = [
        'applicationId' => self:: APPLICATION_ID,
        'userId' => self:: USER_ID,
        'ad_no' => self:: AD_NO,
        'registration_no' => self:: REGISTRATION_NO,
        'stage_id' => self:: STAGE_ID,
        'application_amount' => self:: APPLICATION_AMOUNT,
        'remarks' => self:: REMARKS,
        'status' => self:: STATUS,
        'createdDt' => self:: CREATED_DT,       
        'modifiedDt' => self:: MODIFIED_DT,
    ];
}