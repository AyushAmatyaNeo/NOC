<?php

namespace Customer\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("CustomerContract")
 * @Annotation\Attributes({"id":"customerContract"})
 */
class CustomerContractFrom {

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Customer"})
     * @Annotation\Attributes({ "id":"customerId","class":"form-control"})
     */
    public $customerId;
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Contract Name"})
     * @Annotation\Attributes({ "id":"contractName", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     */
    public $contractName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"StartDate"})
     * @Annotation\Attributes({ "id":"startDate","class":"form-control" })
     */
    public $startDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Date"})
     * @Annotation\Attributes({ "id":"endDate","class":"form-control" })
     */
    public $endDate;
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Billing Month"})
     * @Annotation\Attributes({ "id":"billingMonth","class":"form-control","options":{"N":"NEPALI","E":"ENGLISH"}})
     */
    
    public $billingMonth;



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
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"OT Rate"})
     * @Annotation\Attributes({ "id":"otRate", "class":" form-control","min":"1","step":"0.01"})
     */
    public $otRate;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"OT Type"})
     * @Annotation\Attributes({ "id":"otType","class":"form-control","options":{"H":"Hourly Rate","P":"Percent"}})
     */
    public $otType;
    
    

}
