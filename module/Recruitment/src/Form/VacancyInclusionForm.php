<?php
namespace Recruitment\Form;

use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("StageForm")
 */
class VacancyInclusionForm {
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Inclusion"})
     * @Annotation\Attributes({ "id":"InclusionId","class":"InclusionId form-control","multiple":"multiple"})
     */
    public $InclusionId;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Vacancy"})
     * @Annotation\Attributes({ "id":"VacancyId","class":"VacancyId form-control"})
     */
    public $VacancyId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
} 