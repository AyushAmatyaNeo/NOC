<?php

namespace Notification\Model;

use Application\Model\Model;

class NewsFile extends Model {

    const TABLE_NAME = "HRIS_NEWS_FILE";
    const NEWS_FILE_ID = "NEWS_FILE_ID";
    const NEWS_ID = "NEWS_ID";
    const FILE_PATH = "FILE_PATH";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const CREATED_BY="CREATED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const REMARKS = "REMARKS";
    const FILE_NAME = "FILE_NAME";

    public $newsFileId;
    public $newsId;
    public $filePath;
    public $status;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $remarks;
    public $fileName;
    public $mappings = [
        'newsFileId' => self::NEWS_FILE_ID,
        'newsId' => self::NEWS_ID,
        'filePath' => self::FILE_PATH,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'remarks' => self::REMARKS,
        'fileName' => self::FILE_NAME
    ];

}
