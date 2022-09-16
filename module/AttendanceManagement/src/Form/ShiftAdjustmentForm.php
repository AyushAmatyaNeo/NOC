<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AttendanceManagement\Form;

use Zend\Form\Annotation;
/**
 * Description of ShiftAdjustment
 *
 * @author root
 */
class ShiftAdjustmentForm {
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Start Time"})
     * @Annotation\Attributes({ "id":"startTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control" })
     */
    public $startTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Time"})
     * @Annotation\Attributes({ "id":"endTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control"})
     */
    public $endTime;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"StartDate"})
     * @Annotation\Attributes({ "id":"adjustmentStartDate","class":"form-control" })
     */
    public $adjustmentStartDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Date"})
     * @Annotation\Attributes({ "id":"adjustmentEndDate","class":"form-control" })
     */
    public $adjustmentEndDate;
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
    
    
}
