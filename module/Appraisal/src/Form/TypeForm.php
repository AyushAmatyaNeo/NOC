<?php

namespace Appraisal\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("AppraisalType")
 */
class TypeForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Appraisal Type (in Eng.)"})
     * @Annotation\Attributes({"id":"appraisalTypeEdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     */
    public $appraisalTypeEdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Appraisal Type (in Nep.)"})
     * @Annotation\Attributes({"id":"appraisalTypeNdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"400"}})
     */
    public $appraisalTypeNdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Duration Type","value_options":{"A":"Annual","M":"Monthly"}})
     * @Annotation\Attributes({"id":"durationType"})
     */
    public $durationType;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
