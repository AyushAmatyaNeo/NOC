<?php

namespace Setup\Model;

use Application\Model\Model;

class FileSetup extends Model {

    const TABLE_NAME = "HRIS_EMPLOYEE_FILE_SETUP";
    const FILE_ID = "FILE_ID";
    const FILE_NAME = "FILE_NAME";
    const FILE_TYPE_CODE = "FILE_TYPE_CODE";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const MODIFIED_BY = "MODIFIED_BY";

    public $fileId;
    public $fileName;
    public $fileType;
    public $status;
    public $createdDt;
    public $createdBy;
    public $modifiedDt;
    public $modifiedBy;
    public $mappings = [
        'fileId' => self::FILE_ID,
        'fileName' => self::FILE_NAME,
        'fileType' => self::FILE_TYPE_CODE,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'modifiedBy' => self::MODIFIED_BY
    ];

}
