<?php

namespace LeaveManagement\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("LeaveDeduction")
 */ 
class LeaveDeductionForm {
 
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee"})
     * @Annotation\Attributes({ "id":"employeeId","class":"form-control"})
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Leave"})
     * @Annotation\Attributes({ "id":"leaveId","class":"form-control"})
     */
    public $leaveId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Deduction Date"})
     * @Annotation\Attributes({"id":"deductionDt", "class":"form-control" })
     */
    public $deductionDt;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Available Days"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"availableDays", "class":"form-control","readonly":"true"})
     */
    public $availableDays;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Options({"label":"No of Days"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"noOfDays", "class":"form-control", "min":"0","step":"0.5"})
     */
    public $noOfDays;


    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({"id":"form-remarks","class":"form-remarks form-control","style":"height: 50px; font-size:12px"})
     */
    public $remarks;


    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
