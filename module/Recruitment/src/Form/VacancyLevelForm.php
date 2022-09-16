<?php
namespace Recruitment\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("OpeningForm")
 */
class VacancyLevelForm {    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Level"})
     * @Annotation\Attributes({ "id":"FunctionalLevelId","class":"FunctionalLevelId form-control"})
     */
    public $FunctionalLevelId;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Opening Number"})
     * @Annotation\Attributes({ "id":"OpeningId","class":"form-control"})
     */
    public $OpeningId;
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Effective Date"})
     * @Annotation\Attributes({ "id":"EffectiveDate", "class":"EffectiveDate form-control" })
     */
    public $EffectiveDate;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Position"})
     * @Annotation\Attributes({ "id":"PositionId","class":"form-control"})
     */
    public $PositionId;
     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Normal Amount"})
     * @Annotation\Attributes({ "id":"NormalAmount", "class":"NormalAmount form-control" })
     */
    public $NormalAmount;
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Late Amount"})
     * @Annotation\Attributes({ "id":"LateAmount", "class":"LateAmount form-control" })
     */
    public $LateAmount;
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Inclusion Amount"})
     * @Annotation\Attributes({ "id":"InclusionAmount", "class":"InclusionAmount form-control" })
     */
    public $InclusionAmount;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Minimum Age"})
     * @Annotation\Attributes({ "id":"MinAge", "class":"MinAge form-control" })
     */
    public $MinAge;
     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Maximum Age"})
     * @Annotation\Attributes({ "id":"MaxAge", "class":"MaxAge form-control" })
     */
    public $MaxAge;       
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
