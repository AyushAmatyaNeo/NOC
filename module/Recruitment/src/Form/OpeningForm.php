<?php
namespace Recruitment\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("OpeningForm")
 */
class OpeningForm {    
    
    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Opening Number : सूचना संख्या"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     * @Annotation\Attributes({ "id":"OpeningNo", "class":"OpeningNo form-control" })
     */
    public $OpeningNo;
    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Total Reservation  : मांग पद संख्या"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"7"}})
     * @Annotation\Attributes({ "id":"ReservationNo", "class":"ReservationNo form-control" })
     */
    public $ReservationNo;
    /**
     * @Annotion\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Total Vacancy Number: खाली पद संख्या"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"7"}})
     * @Annotation\Attributes({ "id":"Vacancy_total_no", "class":"Vacancy_total_no form-control" })
     */
    public $Vacancy_total_no;
    
     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Start Date : सुरू मिति"})
     * @Annotation\Attributes({ "id":"Start_dt", "class":"Start_dt form-control" })
     */
    public $Start_dt;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Date : अन्त्य मिति"})
     * @Annotation\Attributes({ "id":"End_dt", "class":"End_dt form-control" })
     */
    public $End_dt;
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Extended Date : विस्तारित मिति"})
     * @Annotation\Attributes({ "id":"Extended_dt", "class":"Extended_dt form-control" })
     */
    public $Extended_dt;
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Instruction : निर्देश"})
     * @Annotation\Attributes({ "id":"Instruction_Edesc", "class":"Instruction_Edesc form-control" })
     */
    public $Instruction_Edesc;
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Opening Notes: नोट"})
     * @Annotation\Attributes({ "id":"Instruction_Ndesc", "class":"Instruction_Ndesc form-control" })
     */
    public $Instruction_Ndesc;
       
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
