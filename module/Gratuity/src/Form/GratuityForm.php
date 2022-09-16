<?php

namespace Gratuity\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("leaveApply")
 */ 
class GratuityForm {
 
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee"})
     * @Annotation\Attributes({ "id":"employeeId","class":"form-control"})
     */
    public $employeeId;

     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Extra Service Year"})
     * @Annotation\Attributes({ "id":"extraServiceYear","min":"0", "class":"form-extraServiceYear form-control","step":"0.1" })
     */
    public $extraServiceYear;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Retirement Date"})
     * @Annotation\Attributes({ "id":"form-retirementDate", "class":"form-retirementDate form-control" })
     */
    public $retirementDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Calculate","class":"btn btn-success"})
     */
    public $submit;

    

}
