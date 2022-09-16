<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/13/16
 * Time: 3:05 PM
 */
namespace Setup\Model;

use Application\Model\Model;

class EmployeeQualification extends Model {
    const TABLE_NAME = "HRIS_EMPLOYEE_QUALIFICATIONS";
    const ID = "ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const ACADEMIC_DEGREE_ID = "ACADEMIC_DEGREE_ID";
    const ACADEMIC_UNIVERSITY_ID = "ACADEMIC_UNIVERSITY_ID";
    const ACADEMIC_PROGRAM_ID = "ACADEMIC_PROGRAM_ID";
    const ACADEMIC_COURSE_ID = "ACADEMIC_COURSE_ID";
    const RANK_TYPE = "RANK_TYPE";
    const RANK_VALUE = "RANK_VALUE";
    const PASSED_YR = "PASSED_YR";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
 

    public $id;
    public $employeeId;
    public $academicDegreeId;
    public $academicUniversityId;
    public $academicProgramId;
    public $academicCourseId;
    public $rankType;
    public $rankValue;
    public $passedYr;
    public $status;
    public $createdDt;
    public $modifiedDt;
    public $academicSchool;

    public $mappings = [
        'id'=>self::ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'academicDegreeId'=>self::ACADEMIC_DEGREE_ID,
        'academicUniversityId'=>self::ACADEMIC_UNIVERSITY_ID,
        'academicProgramId'=>self::ACADEMIC_PROGRAM_ID,
        'academicCourseId'=>self::ACADEMIC_COURSE_ID,
        'rankType'=>self::RANK_TYPE,
        'rankValue'=>self::RANK_VALUE,
        'passedYr'=>self::PASSED_YR,
        'status'=>self::STATUS,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt'=>self::MODIFIED_DT,
    ];
}