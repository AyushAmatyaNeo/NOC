<?php

namespace Application\Model;

class Files extends Model {

    const TABLE_NAME = "HRIS_FILES";
    const FILE_ID = "FILE_ID";
    const FILE_NAME = "FILE_NAME";
    const FILE_IN_DIR_NAME = "FILE_IN_DIR_NAME";
    const UPLOADED_DATE = "UPLOADED_DATE";
    const UPLOADED_BY = "UPLOADED_BY";

    public $fileId;
    public $fileName;
    public $fileInDirName;
    public $uploadedDate;
    public $uploadedBy;
    public $mappings = [
        'fileId' => self::FILE_ID,
        'fileName' => self::FILE_NAME,
        'fileInDirName' => self::FILE_IN_DIR_NAME,
        'uploadedDate' => self::UPLOADED_DATE,
        'uploadedBy' => self::UPLOADED_BY,
    ];

}
