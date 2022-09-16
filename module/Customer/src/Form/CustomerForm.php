<?php

namespace Customer\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("Holiday")
 */
class CustomerForm {

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Customer Code"})
     * @Annotation\Attributes({ "id":"customerCode", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $customerCode;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Customer Ename"})
     * @Annotation\Attributes({ "id":"customerEname", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"150"}})
     */
    public $customerEname;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Customer Lname"})
     * @Annotation\Attributes({ "id":"customerLname", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"150"}})
     */
    public $customerLname;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Address"})
     * @Annotation\Attributes({ "id":"address", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"150"}})
     */
    public $address;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Phone No"})
     * @Annotation\Attributes({ "id":"phoneNo", "class":" form-control"})
     */
    public $phoneNo;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Contact Person Name"})
     * @Annotation\Attributes({ "id":"contactPersonName", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"150"}})
     */
    public $contactPersonName;

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

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"PAN NO"})
     * @Annotation\Attributes({ "id":"panNo", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"150"}})
     */
    public $panNo;

}
