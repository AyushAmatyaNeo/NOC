<?php
namespace Recruitment\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("OpeningForm")
 */
class UserApplicationForm {
    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Application Number"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"7"}})
     * @Annotation\Attributes({ "id":"ApplicationId", "class":"ApplicationId form-control" })
     */
    public $ApplicationId;
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}