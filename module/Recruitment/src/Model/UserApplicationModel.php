<?php
namespace Recruitment\Model;

use Application\Model\Model;

class UserApplicationModel extends Model {
    
    const TABLE_NAME            =  "HRIS_REC_VACANCY_APPLICATION";
    const APPLICATION_ID        =  "APPLICATION_ID";
    const USER_ID               =  "USER_ID";
    const AD_NO                 =  "AD_NO";
    const REGISTRATION_NO       =  "REGISTRATION_NO";
    const STAGE_ID              =  "STAGE_ID";
    const APPLICATION_AMOUNT    =  "APPLICATION_AMOUNT";
    const APPLICATION_TYPE      = "APPLICATION_TYPE";
    const STATUS                =  "STATUS";
    const CREATED_BY            =  "CREATED_BY";
    const CREATED_DT            =  "CREATED_DT"; 
    const MODIFIED_BY           =  "MODIFIED_BY";
    const MODIFIED_DT           =  "MODIFIED_DT";
    const CHECKED_BY            =  "CHECKED_BY";
    const CHECKED_DT            =  "CHECKED_DT";
    const APPROVED_BY           =  "APPROVED_BY";
    const APPROVED_DT           =  "APPROVED_DT";
    const DELETED_BY            =  "DELETED_BY";
    const DELETED_DT            =  "DELETED_DT";


    public $ApplicationId;
    public $UserId;
    public $AdNo;
    public $RegistrationNo;
    public $StageId;
    public $ApplicationAmount;
    public $Status;
    public $CreatedBy;
    public $CreatedDt;
    public $ModifiedBy;
    public $ModifiedDt;
    public $CheckedBy;
    public $CheckedDt;
    public $ApprovedBy;
    public $ApprovedDt;
    public $DeletedBy;
    public $DeletedDt;
    public $ApplicationType;
   

    public $mappings = [
        'ApplicationId'     => self::APPLICATION_ID,
        'UserId'            => self::USER_ID,
        'AdNo'              => self::AD_NO,
        'RegistrationNo'    => self::REGISTRATION_NO,
        'StageId'           => self::STAGE_ID,
        'ApplicationAmount' => self::APPLICATION_AMOUNT,
        'Status'            => self::STATUS,
        'CreatedBy'         => self::CREATED_BY,
        'CreatedDt'         => self::CREATED_DT,
        'ModifiedBy'        => self::MODIFIED_BY,
        'ModifiedDt'        => self::MODIFIED_DT,
        'CheckedBy'         => self::CHECKED_BY,
        'CheckedDt'         => self::CHECKED_DT,
        'ApprovedBy'        => self::APPROVED_BY,
        'ApprovedDt'        => self::APPROVED_DT,
        'DeletedBy'         => self::DELETED_BY,
        'DeletedDt'         => self::DELETED_DT,
        'ApplicationType'  => self::APPLICATION_TYPE,
    ];
}