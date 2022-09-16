<?php
namespace Recruitment\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("VacancyStageForm")
 */
class VacancyStageForm {
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Stage Status"})
     * @Annotation\Attributes({ "id":"RecStageId","class":"form-control"})
     */
    public $RecStageId;    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Vacancy Ad Number"})
     * @Annotation\Attributes({ "id":"VacancyId","class":"form-control"})
     */
    public $VacancyId;    
     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remark"})
     * @Annotation\Attributes({ "id":"Remark", "class":"form-control" })
     */
    public $Remark;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
