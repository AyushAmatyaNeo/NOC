<?php

namespace Setup\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("Loan")
 */
class LoanForm {


    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Loan Name"})
     * @Annotation\Attributes({ "id":"loanName", "class":"form-loanName form-control" })
     */
    public $loanName;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Loan Type:"})
     * @Annotation\Attributes({ "id":"loanType","class":"form-control"})
     */
    public $loanType;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Minimum Amount"})
     * @Annotation\Attributes({ "id":"minAmount","class":"form-control","step":"0.01","min":"0"})
     */
    public $minAmount;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Maximum Amount"})
     * @Annotation\Attributes({ "id":"maxAmount","class":"form-control","step":"0.01","min":"0"})
     */
    public $maxAmount;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Interest Rate(in %)"})
     * @Annotation\Attributes({ "id":"interestRate","class":"form-control","step":"0.01","min":"0"})
     */
    public $interestRate;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Re-Payment Amount(in %)"})
     * @Annotation\Attributes({ "id":"repaymentAmount", "class":"form-control","step":"0.01","min":"0" })
     */
    public $repaymentAmount;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Eligible Service Period (In Year)"})
     * @Annotation\Attributes({ "id":"eligibleServicePeriod", "class":"form-control","min":"0" })
     */
    public $eligibleServicePeriod;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Number of Times it can be taken"})
     * @Annotation\Attributes({ "id":"maxIssueTime", "class":"form-control", "min":"0" })
     */
    public $maxIssueTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Pay-back Period(in month)"})
     * @Annotation\Attributes({ "id":"repaymentPeriod", "class":"form-control","min":"0" })
     */
    public $repaymentPeriod;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"remarks","class":"form-remarks form-control","style":"height: 50px; font-size:12px"})
     */
    public $remarks;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Issued By:"})
     * @Annotation\Attributes({ "id":"issuedBy","class":"form-control"})
     */
    public $issuedBy;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Pay Id Interest:"})
     * @Annotation\Attributes({ "id":"payIdInt","class":"form-control"})
     */
    public $payIdInt;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Pay Id Amount:"})
     * @Annotation\Attributes({ "id":"payIdAmt","class":"form-control"})
     */
    public $payIdAmt;

     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Valid From"})
     * @Annotation\Attributes({"id":"validFrom", "class":"form-control" })
     */
    public $validFrom;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Valid Upto"})
     * @Annotation\Attributes({ "id":"validUpto","class":"form-control" })
     */
    public $validUpto;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Ledger Code"})
     * @Annotation\Attributes({ "id":"ledgerCode","class":"form-control" })
     */
    public $ledgerCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"DR Acc code"})
     * @Annotation\Attributes({ "id":"drAccCode","class":"form-control" })
     */
    public $drAccCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"CR Acc code"})
     * @Annotation\Attributes({ "id":"crAccCode","class":"form-control" })
     */
    public $crAccCode;

     /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
    
}
