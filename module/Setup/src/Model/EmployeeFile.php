<?php

namespace Setup\Model;

use Application\Model\Model;

class EmployeeFile extends Model {

    const TABLE_NAME = "HRIS_EMPLOYEE_FILE";
    const FILE_CODE = "FILE_CODE";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const FILETYPE_CODE = "FILETYPE_CODE";
    const FILE_PATH = "FILE_PATH";
    const STATUS = "STATUS";
    const FILE_ID = "FILE_ID";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const REMARKS = "REMARKS";

    public $fileCode;
    public $employeeId;
    public $filetypeCode;
    public $filePath;
    public $status;
    public $fileId;
    public $createdDt;
    public $modifiedDt;
    public $remarks;
    public $mappings = [
        'fileCode' => self::FILE_CODE,
        'employeeId' => self::EMPLOYEE_ID,
        'filetypeCode' => self::FILETYPE_CODE,
        'filePath' => self::FILE_PATH,
        'status' => self::STATUS,
        'fileId' => self::FILE_ID,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'remarks' => self::REMARKS
    ];

}
