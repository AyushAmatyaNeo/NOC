<?php

namespace Notification\Model;

use Application\Model\Model;

class NewsTypeModel extends Model {
    const TABLE_NAME = 'HRIS_NEWS_TYPE';

    const NEWS_TYPE_ID = 'NEWS_TYPE_ID';
    const NEWS_TYPE_DESC = 'NEWS_TYPE_DESC';
    const UPLOAD_FLAG = 'UPLOAD_FLAG';
    const STATUS = 'STATUS';
    const CREATED_DT = 'CREATED_DT';
    const CREATED_BY = 'CREATED_BY';
    const MODIFIED_DT = 'MODIFIED_DT';
    const MODIFIED_BY = 'MODIFIED_BY';
    const DOWNLOAD_FLAG = 'DOWNLOAD_FLAG';
    

    public $newsTypeId;
    public $newsTypeDesc;
    public $uploadFlag;
    public $status;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $downloadFlag;

    public $mappings = [
    'newsTypeId' => self::NEWS_TYPE_ID,
    'newsTypeDesc' => self::NEWS_TYPE_DESC,
    'uploadFlag' => self::UPLOAD_FLAG,
    'status' => self::STATUS,
    'createdDt' => self::CREATED_DT,
    'createdBy' => self::CREATED_BY,
    'modifiedDt' => self::MODIFIED_DT,
    'modifiedBy' => self::MODIFIED_BY,
    'downloadFlag' => self::DOWNLOAD_FLAG
    ];
}
