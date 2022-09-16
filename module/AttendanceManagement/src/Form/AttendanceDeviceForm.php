<?php

namespace AttendanceManagement\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("FlatValue")
 */
class AttendanceDeviceForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"500"}})
     * @Annotation\Options({"label":"Device Name"})
     * @Annotation\Attributes({ "id":"deviceName","class":"form-control"})
     */
    public $deviceName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"500"}})
     * @Annotation\Options({"label":"Device IP"})
     * @Annotation\Attributes({ "id":"deviceIp","class":"form-control"})
     */
    public $deviceIp;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"500"}})
     * @Annotation\Options({"label":"Device Location Name"})
     * @Annotation\Attributes({ "id":"deviceLocation","class":"form-control"})
     */
    public $deviceLocation;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
