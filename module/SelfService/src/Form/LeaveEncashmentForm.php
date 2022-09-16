<?php

namespace SelfService\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("LeaveEncashment")
 */
class LeaveEncashmentForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name"})
     * @Annotation\Attributes({ "id":"employeeId","class":"form-control"})
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Leave Type"})
     * @Annotation\Attributes({ "id":"leaveId","class":"form-control"})
     */
    public $leaveId;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Number of Leave Days to Encash"})
     * @Annotation\Attributes({ "id":"requestedDays", "class":" form-control","min":"1"})
     */
    public $requestedDays;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Request Date"})
     * @Annotation\Attributes({ "id":"dateOfadvance", "class":"form-control" })
     */
    //public $dateOfadvance; //disable this

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"reason","class":"form-reason form-control","style":"height: 50px; font-size:12px"})
     */
    public $reason;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Recommeder Remarks"})
     * @Annotation\Attributes({"id":"recommendedRemarks","class":"form-reason form-control","style":"    height: 50px; font-size:12px"})
     */
    //public $recommendedRemarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Approved Remarks"})
     * @Annotation\Attributes({"id":"approvedRemarks","class":"form-reason form-control","style":"    height: 50px; font-size:12px"})
     */
    //public $approvedRemarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Monthly Deduction Percentage"})
     * @Annotation\Attributes({ "id":"deductionRate","class":"form-control","step":"0.01","min":"0","max":"100"})
     */
    //public $deductionRate;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":" Repayment Months"})
     * @Annotation\Attributes({ "id":"deductionIn","class":"form-control","min":"0"})
     */
    //public $deductionIn;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Recommender"})
     * @Annotation\Attributes({ "id":"overrideRecommenderId","class":"form-control"})
     */
    //public $overrideRecommenderId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Approver"})
     * @Annotation\Attributes({ "id":"overrideApproverId","class":"form-control"})
     */
    //public $overrideApproverId;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
