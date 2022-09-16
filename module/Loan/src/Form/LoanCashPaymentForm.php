<?php
namespace Loan\Form;

use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("LoanClosing")
 */
 
class LoanCashPaymentForm{

     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Paid Amount (Principle)"})
     * @Annotation\Attributes({ "id":"principleAmount","min":"0", "readonly":"readonly", "class":"form-paidAmount form-control"})
     */
    public $principleAmount;
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Interest Deduction"})
     * @Annotation\Attributes({ "id":"interest","min":"0", "class":"form-interest form-control" })
     */
    public $interest;
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Paid Amount (Total)"})
     * @Annotation\Attributes({ "id":"paymentAmount","min":"0", "class":"form-totalPaid form-control" })
     */
    public $paymentAmount;
     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Paid Date"})
     * @Annotation\Attributes({ "id":"paidDate", "class":"form-loanDate form-control" })
     */
    public $paymentDate;
     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Receipt No."})
     * @Annotation\Attributes({ "id":"receiptNo", "class":"form-receiptNo form-control" })
     */
    public $receiptNo;    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"reason","class":"form-remarks form-control","style":" resize:none;   height: 50px; font-size:12px"})
     */
    public $remarks;
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"id":"submit","value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}