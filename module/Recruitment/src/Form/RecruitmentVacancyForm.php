<?php
namespace Recruitment\Form;

use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("RecruitmentVacancy")
 */
class RecruitmentVacancyForm {
     protected $uniqueFields;
     
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Openings Number"})
     * @Annotation\Attributes({ "id":"OpeningId","class":"form-control"})
     */
    public $OpeningId;
     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"S.No"})
    * @Annotation\Attributes({ "id":"Vacancy_no","class":"form-control"})
     */
    public $Vacancy_no;
     /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"false","label":"Vacancy Type"})
     * @Annotation\Attributes({ "id":"Vacancy_type", "class":"Vacancy_type form-control" })
     */
    public $Vacancy_type;
     /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Level"})
     * @Annotation\Attributes({ "id":"LevelId", "class":"LevelId form-control" })
     */
    public $LevelId;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Service Types"})
     * @Annotation\Attributes({ "id":"ServiceTypesId","class":"form-control"})
     */
    public $ServiceTypesId;
    
     /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Position"})
     * @Annotation\Attributes({ "id":"PositionId","class":"form-control"})
     */
    public $PositionId;
     /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Qualification"})
     * @Annotation\Attributes({ "id":"QualificationId","class":"form-control"})
     */
    public $QualificationId;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Department"})
     * @Annotation\Attributes({ "id":"DepartmentId","class":"form-control"})
     */
    public $DepartmentId;
     
    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Reservation Number"})
     * @Annotation\Attributes({ "id":"VacancyReservationNo", "class":"VacancyReservationNo form-control" })
     */
    public $VacancyReservationNo;
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Vacancy Notes:"})
     * @Annotation\Attributes({ "id":"Remark", "class":"form-control" })
     */
    public $Remark;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Service group"})
     * @Annotation\Attributes({ "id":"ServiceEventsId","class":"form-control"})
     */
    public $ServiceEventsId;

    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Experience"})
     * @Annotation\Attributes({ "id":"Experience", "class":"Experience form-control" })
     */
    public $Experience;
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Ad Number"})
     * @Annotation\Attributes({ "id":"AdNo", "class":"AdNo form-control" })
     */
    public $AdNo;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Inclusion","unique":"true"})
     * @Annotation\Attributes({ "id":"InclusionId","class":"form-control","multiple":"multiple"})
     */
    
    public $InclusionId;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Skills"})
     * @Annotation\Attributes({ "id":"SkillId","class":"form-control","multiple":"multiple"})
     */
    public $SkillId;
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
