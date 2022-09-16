<?php
namespace Recruitment\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("VacancyoptionsForm")
 */
class VacancyoptionsForm {    
    
    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Quota"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"7"}})
     * @Annotation\Attributes({ "id":"Quota", "class":"Quota form-control" })
     */
    public $Quota;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Open / Internal"})
     * @Annotation\Attributes({ "id":"OpenInternal", "class":"form-control" })
     */
    public $OpenInternal;
    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Normal Amount"})
     * @Annotation\Attributes({ "id":"NormalAmt", "class":"NormalAmt form-control" })
     */
    public $NormalAmt;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Vacancy Ad List"})
     * @Annotation\Attributes({ "id":"VacancyId","class":"form-control"})
     */
    public $VacancyId;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Option List"})
     * @Annotation\Attributes({ "id":"OptionId","class":"form-control"})
     */
    public $OptionId;
    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Late Amount"})
     * @Annotation\Attributes({ "id":"LateAmt", "class":"LateAmt form-control" })
     */
    public $LateAmt;
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"REMARKS"})
     * @Annotation\Attributes({ "id":"Remarks", "class":"form-control" })
     */
    public $Remarks;
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
