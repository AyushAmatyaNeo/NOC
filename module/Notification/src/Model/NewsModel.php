<?php

namespace Notification\Model;

use Application\Model\Model;

class NewsModel extends Model {

    const TABLE_NAME = 'HRIS_NEWS';
    const NEWS_ID = 'NEWS_ID';
    const NEWS_DATE = 'NEWS_DATE';
    const NEWS_TYPE = 'NEWS_TYPE';
    const NEWS_TITLE = 'NEWS_TITLE';
    const NEWS_EDESC = 'NEWS_EDESC';
    const NEWS_LDESC = 'NEWS_LDESC';
    const REMARKS = 'REMARKS';
    const CREATED_BY = 'CREATED_BY';
    const CREATED_DT = 'CREATED_DT';
    const MODIFIED_BY = 'MODIFIED_BY';
    const MODIFIED_DT = 'MODIFIED_DT';
    const APPROVED_BY = 'APPROVED_BY';
    const APPROVED_DT = 'APPROVED_DT';
    const STATUS = 'STATUS';
    const NEWS_EXPIRY_DT = 'NEWS_EXPIRY_DT';

    public $newsId; 
    public $newsDate;
    public $newsType;
    public $newsTitle;
    public $newsEdesc;
    public $newsLdesc;
    public $remarks;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $approvedBy;
    public $approvedDt;
    public $status;
    public $newsExpiryDate;
    public $mappings = [
        'newsId' => self::NEWS_ID,
        'newsDate' => self::NEWS_DATE,
        'newsType' => self::NEWS_TYPE,
        'newsTitle' => self::NEWS_TITLE,
        'newsEdesc' => self::NEWS_EDESC,
        'newsLdesc' => self::NEWS_LDESC,
        'remarks' => self::REMARKS,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'approvedBy' => self::APPROVED_BY,
        'approvedDt' => self::APPROVED_DT,
        'status' => self::STATUS,
        'newsExpiryDate' => self::NEWS_EXPIRY_DT
    ];

}
