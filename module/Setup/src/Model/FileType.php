<?php

namespace Setup\Model;

use Application\Model\Model;

class FileType extends Model {
    

    const TABLE_NAME = "HRIS_FILE_TYPE";
    const FILETYPE_CODE = "FILETYPE_CODE";
    const NAME = "NAME";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const REMARKS = "REMARKS";
    
    public $filetypeCode;
    public $name;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $remarks;
    public $mappings = [
        'filetypeCode' => self::FILETYPE_CODE,
        'name' => self::NAME,
        'status' => self::STATUS,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS,
    ];

}
