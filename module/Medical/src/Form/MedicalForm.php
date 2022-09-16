<?php

namespace Medical\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("Medical")
 */
class MedicalForm {
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name"})
     * @Annotation\Attributes({ "id":"employeeId","class":"form-control"})
     */
    public $employeeId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"S":"Self","D":"Dependent"},"label":"Claim Of"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"claimOf","value":"S"})
     */
    
    public $claimOf;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Dependant"})
     * @Annotation\Attributes({ "id":"eRId","class":"form-control"})
     */
    public $eRId;
    
    
    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Age"})
     * @Annotation\Attributes({ "id":"age", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     */
    public $age;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Operation"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"operationFlag","value":"N"})
     */
    public $operationFlag;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Transaction Date"})
     * @Annotation\Attributes({ "id":"transactionDt", "class":"form-control" })
     */
    public $transactionDt;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Request Amt"})
     * @Annotation\Attributes({ "id":"requestedAmt","readonly"="readonly","class":"form-control","min":"0","step":"0.01"})
     */
    public $requestedAmt;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Approved Amt"})
     * @Annotation\Attributes({ "id":"approvedAmt","class":"form-control","min":"0","step":"0.01"})
     */
    public $approvedAmt;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"remarks","class":"form-reason form-control","style":"    height: 50px; font-size:12px"})
     */
    public $remarks;
    
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
