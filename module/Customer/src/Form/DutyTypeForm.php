<?php

namespace Customer\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("Holiday")
 */
class DutyTypeForm {

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Duty Type Name"})
     * @Annotation\Attributes({ "id":"dutyTypeName", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     */
    public $dutyTypeName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Normal Hour"})
     * @Annotation\Attributes({ "id":"normalHour", "data-format":"h:mm", "data-template":"hh : mm", "class":"form-control" })
     */
    public $normalHour;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Overtime Hour"})
     * @Annotation\Attributes({ "id":"otHour", "data-format":"h:mm", "data-template":"hh : mm", "class":"form-control" })
     */
    public $otHour;

    /**
     * @Annotion\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"512"}})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
