<?php

namespace Advance\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("Advance Setup")
 */
class AdvanceSetupForm {

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Advance Code"})
     * @Annotation\Attributes({ "id":"advanceCode", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $advanceCode;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Advance Ename"})
     * @Annotation\Attributes({ "id":"advanceEname", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     */
    public $advanceEname;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Advance Lname"})
     * @Annotation\Attributes({ "id":"advanceLname", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     */
    public $advanceLname;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Allowed To"})
     * @Annotation\Attributes({ "id":"allowedTo","class":"form-control","options":{"ALL":"All","PER":"Permanent","PRO":"Probational","CON":"Contract"}})
     */
    public $allowedTo;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Next Advance Month Gap"})
     * @Annotation\Attributes({ "id":"allowedMonthGap","class":"form-control","min":"0"})
     */
    public $allowedMonthGap;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Allow Advance even last Advance Uncleared"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({"id":"allowUncleardAdvance","value":"N"})
     */
    public $allowUncleardAdvance;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Max Salary Rate"})
     * @Annotation\Attributes({ "id":"maxSalaryRate","class":"form-control","step":"0.01","min":"0","max":"100"})
     */
    public $maxSalaryRate;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Max Advance Months"})
     * @Annotation\Attributes({"id":"maxAdvanceMonth","class":"form-control","min":"0","max":"12"})
     */
    public $maxAdvanceMonth;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"S":"Salary","M":"Monthly"},"label":"Deduction Type"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"deductionType"})
     */
    public $deductionType;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Monthly Deduction Rate"})
     * @Annotation\Attributes({ "id":"deductionRate","class":"form-control","step":"0.01","min":"0","max":"100"})
     */
    public $deductionRate;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":" Max Deduction Month"})
     * @Annotation\Attributes({ "id":"deductionIn","class":"form-control","min":"0"})
     */
    public $deductionIn;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Allow Override Rate"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"allowOverrideRate","value":"N"})
     */
    public $allowOverrideRate;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Minimum Override Rate"})
     * @Annotation\Attributes({ "id":"minOverrideRate","class":"form-control","step":"0.01","min":"0","max":"100"})
     */
    public $minOverrideRate;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Allow Override Month"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"allowOverrideMonth","value":"N"})
     */
    public $allowOverrideMonth;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Maximum Override Month"})
     * @Annotation\Attributes({ "id":"maxOverrideMonth","class":"form-control","min":"0","max":"12"})
     */
    public $maxOverrideMonth;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Override Recommender"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"overrideRecommenderFlag","value":"N"})
     */
    public $overrideRecommenderFlag;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Override Approver"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"overrideApproverFlag","value":"N"})
     */
    public $overrideApproverFlag;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
