<?php
namespace Recruitment\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("VenueAssignForm")
 */
class VenueAssignForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"venueAssignId" })
     */
    public $venueAssignId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Venue"})
     * @Annotation\Attributes({ "id":"venueSetupId","class":"form-control"})
     */
    public $venueSetupId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Exam Type"})
     * @Annotation\Attributes({ "id":"examType","class":"form-control"})
     */
    public $examType;

    /**
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Start Time"})
     * @Annotation\Attributes({ "id":"startTime", "class":"form-control", "type":"time"})
     */
    public $startTime;


    /**
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"End Time"})
     * @Annotation\Attributes({ "id":"endTime","class":"form-control", "type":"time"})
     */
    public $endTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Exam Date"})
     * @Annotation\Attributes({ "class":"form-control", "id":"nepali-datepicker"})
     */
    public $examDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Status"})
     * @Annotation\Attributes({ "id":"status","class":"form-control"})
     */
    public $status;   
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success", "id":"btn-submit"})
     */
    public $submit;

}