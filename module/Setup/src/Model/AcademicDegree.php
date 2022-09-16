<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/16
 * Time: 5:04 PM
 */
namespace Setup\Model;

use Application\Model\Model;

class AcademicDegree extends Model{
    const TABLE_NAME = "HRIS_ACADEMIC_DEGREES";
    const ACADEMIC_DEGREE_ID = "ACADEMIC_DEGREE_ID";
    const ACADEMIC_DEGREE_CODE = "ACADEMIC_DEGREE_CODE";
    const ACADEMIC_DEGREE_NAME = "ACADEMIC_DEGREE_NAME";
    const WEIGHT = "WEIGHT";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $academicDegreeId;
    public $academicDegreeCode;
    public $academicDegreeName;
    public $weight;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;

    public $mappings =[
        'academicDegreeId'=>Self::ACADEMIC_DEGREE_ID,
        'academicDegreeCode'=>Self::ACADEMIC_DEGREE_CODE,
        'academicDegreeName'=>Self::ACADEMIC_DEGREE_NAME,
        'weight' => Self::WEIGHT,
        'remarks' => Self::REMARKS,
        'status' => Self::STATUS,
        'createdDt' => Self::CREATED_DT,
        'modifiedDt' => Self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY,
    ];

}