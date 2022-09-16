<?php
namespace SelfService\Model;

use Application\Model\Model;

class ApplicationDocuments extends Model{
    const TABLE_NAME = "HRIS_REC_APPLICATION_DOCUMENTS";
    
    const REC_DOC_ID = "REC_DOC_ID";
    const APPLICATION_ID = "APPLICATION_ID";
    const VACANCY_ID = "VACANCY_ID";
    const USER_ID = "USER_ID";
    const DOC_OLD_NAME = "DOC_OLD_NAME";
    const DOC_NEW_NAME = "DOC_NEW_NAME";
    const DOC_PATH = "DOC_PATH";
    const DOC_TYPE = "DOC_TYPE";
    const DOC_FOLDER = "DOC_FOLDER";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const STATUS = "STATUS";
    
    public $recdocid;
    public $applicationid;
    public $vacancyid;
    public $userid;
    public $docoldname;
    public $docnewname;
    public $docpath;
    public $doctype;
    public $docfolder;
    public $createdDate;
    public $modifiedDate;
    public $status;
    
    public $mappings = [
        'recdocid'=>self::REC_DOC_ID,
        'applicationid' => self::APPLICATION_ID,
        'vacancyid'=>self::VACANCY_ID,
        'userid'=>self::USER_ID,
        'docoldname'=>self::DOC_OLD_NAME,
        'docnewname'=>self::DOC_NEW_NAME,
        'docpath'=>self::DOC_PATH,
        'doctype'=>self::DOC_TYPE,
        'docfolder'=>self::DOC_FOLDER,
        'createdDate'=>self::CREATED_DATE,
        'modifiedDate'=>self::MODIFIED_DATE,
        'status'=>self::STATUS
    ];
}
