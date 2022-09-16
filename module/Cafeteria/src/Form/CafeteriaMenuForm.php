<?php
namespace Cafeteria\Form;

use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("LoanRequest")
 */
 
class CafeteriaMenuForm{

     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Menu Name"})
     * @Annotation\Attributes({"id":"menuName","class":"menu-data form-remarks form-control","style":"})
     */
    public $menuName;
    
     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Quantity"})
     * @Annotation\Attributes({ "id":"quantity","min":"0", "class":"menu-data form-requestedAmount form-control"})
     */
    public $quantity;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Rate"})
     * @Annotation\Attributes({ "id":"rate","min":"0", "class":"menu-data form-requestedAmount form-control", "step":"0.01" })
     */
    public $rate;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"remarks","class":"menu-data form-remarks form-control","style":" height: 50px; font-size:12px"})
     */
    public $remarks;
    /**
     * @Annotation\Type("Zend\Form\Element\Button")
     * @Annotation\Attributes({"id":"submit","value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}