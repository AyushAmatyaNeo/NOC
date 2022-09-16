<?php
namespace Setup\Form;
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/11/16
 * Time: 1:07 PM
 */
use Application\Model\Model;
use Zend\Form\Annotation;

class HrEmployeesFormTabSix extends  Model{

    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Degree Acquired "})
     * @Annotation\Attributes({ "id":"academicDegreeId","class":"form-control"})
     */
    public $academicDegreeId;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"University/Board"})
     * @Annotation\Attributes({ "id":"academicUniversityId","class":"form-control"})
     */
    public $academicUniversityId;



        /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"School/College Name"})
     * @Annotation\Attributes({ "id":"academicSchoolId","class":"form-control"})
     */
    public $academicSchoolId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Faculty"})
     * @Annotation\Attributes({ "id":"academicProgramId","class":"form-control"})
     */
    public $academicProgramId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Course Name"})
     * @Annotation\Attributes({ "id":"academicCourseId","class":"form-control"})
     */
    public $academicCourseId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Rank Type"})
     * @Annotation\Attributes({ "id":"rankType","class":"form-control"})
     */
    public $rankType;

    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Rank Value"})
     * @Annotation\Attributes({ "id":"form-rankValue", "class":"rankValue-weight form-control" ,"size":"7"})
     */
    public $rankValue;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Passed Year"})
     * @Annotation\Attributes({"id":"form-passedYr","class":"form-passedYr form-control"})
     */
    public $passedYr;

    public $status;
    public $createdDt;
    public $modifiedDt;

    public $mappings=[
        'employeeId'=>'EMPLOYEE_ID',
        'academicDegreeId'=>'ACADEMIC_DEGREE_ID',
        'academicUniversityId'=>'ACADEMIC_UNIVERSITY_ID',
        'academicProgramId'=>'ACADEMIC_PROGRAM_ID',
        'academicCourseId'=>'ACADEMIC_COURSE_ID',
        'rankType'=>'RANK_TYPE',
        'rankValue'=>'RANK_VALUE',
        'passedYr'=>'PASSED_YR',
        'status'=>'STATUS',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT',
        'academicSchoolId'=>'ACADEMIC_SCHOOL_ID'
    ];
}

