<?php
namespace Asset\Form;

use Zend\Form\Annotation;

class IssueForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Issue Date"})
     * @Annotation\Attributes({"id":"issueDate","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $issueDate;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Asset Name"})
     * @Annotation\Attributes({ "id":"assetId","class":"form-control"})
     */
    public $assetId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Quantity"})
     * @Annotation\Attributes({"id":"quantity","class":"form-control"})
     */
    public $quantity;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee"})
     * @Annotation\Attributes({ "id":"employeeId","class":"form-control"})
     */
    public $employeeId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Request Date"})
     * @Annotation\Attributes({"id":"requestDate","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $requestDate;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Returnable","checked_value":"Y","unchecked_value":"N"})
     * @Annotation\Attributes({"id":"returnable","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $returnable;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Return Date"})
     * @Annotation\Attributes({"id":"returnDate","class":"form-control","disabled":"true"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $returnDate;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Purpose"})
     * @Annotation\Attributes({"id":"purposeTA","class":"form-control"})
     */
    public $purpose;
    
    
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
     * @Annotation\Attributes({"id":"IssueSubmitBtn","value":"Submit","class":"btn btn-success"})
     */
    public $submit;   
    
    
    //    public $sno;
    
//    public $returned;
//    public $returnedDate;
    
    
    
    
    
    
}
