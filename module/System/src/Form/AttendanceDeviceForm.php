<?php

namespace System\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("AttendanceDevice")
 */
class AttendanceDeviceForm {

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Device IP"})
     * @Annotation\Attributes({ "id":"deviceIp", "class":"form-control" })
     */
    public $deviceIp;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Device Company"})
     * @Annotation\Attributes({ "id":"deviceCompany", "class":" form-control" })
     */
    public $deviceCompany;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Device name"})
     * @Annotation\Attributes({ "id":"deviceName", "class":" form-control" })
     */
    public $deviceName;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Company"})
     * @Annotation\Attributes({ "id":"companyId","class":"form-control"})
     */
    public $companyId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Branch"})
     * @Annotation\Attributes({ "id":"branchId","class":"form-control"})
     */
    public $branchId;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Device Location"})
     * @Annotation\Attributes({ "id":"deviceLocation", "class":" form-control" })
     */
    public $deviceLocation;

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Active","checked_value":"Y","unchecked_value":"N"})
     * @Annotation\Attributes({"id":"isActive","class":"form-control"})
     */
    public $isActive;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"value_options":{"IN":"In","OUT":"Out","I/O":"In and Out"},"label":"Purpose"})
     * @Annotation\Attributes({ "id":"purpose","class":"form-control"})
     */
    public $purpose;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success","id":"btnSubmit"})
     */
    public $submit;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Branch Manager"})
     * @Annotation\Attributes({ "id":"branchManager","class":"form-control"})
     */
    public $branchManager;

}
