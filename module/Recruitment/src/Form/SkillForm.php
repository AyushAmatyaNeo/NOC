<?php
namespace Recruitment\Form;
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("Skill")
 */

Class SkillForm {
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Skill Name"})
    * @Annotation\Attributes({ "id":"SkillName","class":"SkillName form-control"})
     */
    public $SkillName;
     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Skill Code"})
    * @Annotation\Attributes({ "id":"SkillCode","class":"SkillCode form-control"})
     */
    public $SkillCode;
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Required Flag"})
     * @Annotation\Attributes({ "id":"RequiredFlag","class":"RequiredFlag form-control"})
     */
    public $RequiredFlag;
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Upload Flag"})
     * @Annotation\Attributes({ "id":"UploadFlag","class":"UploadFlag form-control"})
     */
    public $UploadFlag;
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
}