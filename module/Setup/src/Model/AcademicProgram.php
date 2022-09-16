<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/16
 * Time: 5:19 PM
 */
namespace Setup\Model;

use Application\Model\Model;

class AcademicProgram extends Model {
    const TABLE_NAME = "HRIS_ACADEMIC_PROGRAMS";
    const ACADEMIC_PROGRAM_ID = "ACADEMIC_PROGRAM_ID";
    const ACADEMIC_PROGRAM_CODE = "ACADEMIC_PROGRAM_CODE";
    const ACADEMIC_PROGRAM_NAME = "ACADEMIC_PROGRAM_NAME";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $academicProgramId;
    public $academicProgramCode;
    public $academicProgramName;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;

    public $mappings = [
        'academicProgramId'=>Self::ACADEMIC_PROGRAM_ID,
        'academicProgramCode'=>Self::ACADEMIC_PROGRAM_CODE,
        'academicProgramName'=>Self::ACADEMIC_PROGRAM_NAME,
        'remarks'=>Self::REMARKS,
        'status'=>Self::STATUS,
        'createdDt'=>Self::CREATED_DT,
        'modifiedDt'=>Self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
    ];
}