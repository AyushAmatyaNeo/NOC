<?php
namespace Cafeteria\Form;

use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("LoanRequest")
 */
 
class CafeteriaScheduleForm{

     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Time Name"})
     * @Annotation\Attributes({ "id":"timeName", "class":"time-data form-control" })
     */
    public $timeName;
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"remarks","class":"time-data remarks form-control","style":"    height: 50px; font-size:12px"})
     */
    public $remarks;
    /**
     * @Annotation\Type("Zend\Form\Element\Button")
     * @Annotation\Attributes({"id":"submit","value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}