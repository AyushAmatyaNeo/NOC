<?php
namespace Setup\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("JobResponsibilityForm")
 */
class JobResponsibilityForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Job Responsibility Title (English)"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":255}})
     * @Annotation\Attributes({ "id":"jobResEngName", "class":"form-control" })
     */
    public $jobResEngName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Job Responsibility Title (Nepali)"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":255}})
     * @Annotation\Attributes({ "id":"jobResNepName", "class":"form-control" })
     */
    public $jobResNepName;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Description (English)"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":1000}})
     * @Annotation\Attributes({ "id":"jobResNepDescription",  "class":"form-control","style":"min-height: 150px; resize: none !important;" })
     */
    public $jobResEngDescription;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Description (Nepali)"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":1000}})
     * @Annotation\Attributes({ "id":"jobResNepDescription", "class":"form-control","style":"min-height: 150px; resize: none !important;" })
     */
    public $jobResNepDescription;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
