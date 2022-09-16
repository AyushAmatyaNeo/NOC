<?php

namespace LeaveManagement\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("leaveApply")
 */ 
class LeaveCarryForwardForm {
 
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
     * @Annotation\Options({"label":"No of Days"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"noOfDays", "class":"form-control","readonly":"true"})
     */
    public $noOfDays;



    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Available Days"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"availableDays", "class":"form-control","readonly":"true"})
     */
    public $availableDays;

       /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Total Days For Carry Forward"})
     * @Annotation\Attributes({ "id":"form-totalCarryForwardDays", "class":"form-totalCarryForwardDays form-control","min":"0","step":"0.5" })
     */
    public $totalCarryForwardDays;

     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Total Days For leave encashment"})
     * @Annotation\Attributes({ "id":"form-totalEncashDays", "class":"form-totalEncashDays form-control","min":"0","step":"0.5" })
     */
    public $totalEncashDays;
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
