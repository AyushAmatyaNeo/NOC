<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/16
 * Time: 5:24 PM
 */
namespace Setup\Model;

use Application\Model\Model;

class AcademicCourse extends Model {
    const TABLE_NAME = "HRIS_ACADEMIC_COURSES";
    const ACADEMIC_COURSE_ID = "ACADEMIC_COURSE_ID";
    const ACADEMIC_COURSE_CODE = "ACADEMIC_COURSE_CODE";
    const ACADEMIC_COURSE_NAME = "ACADEMIC_COURSE_NAME";
    const ACADEMIC_PROGRAM_ID = "ACADEMIC_PROGRAM_ID";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $academicCourseId;
    public $academicCourseCode;
    public $academicCourseName;
    public $academicProgramId;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;

    public $mappings = [
        'academicCourseId'=>Self::ACADEMIC_COURSE_ID,
        'academicCourseCode'=>Self::ACADEMIC_COURSE_CODE,
        'academicCourseName'=>Self::ACADEMIC_COURSE_NAME,
        'academicProgramId'=>Self::ACADEMIC_PROGRAM_ID,
        'remarks'=>Self::REMARKS,
        'status'=> Self::STATUS,
        'createdDt'=> Self::CREATED_DT,
        'modifiedDt'=> Self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY
    ];
}