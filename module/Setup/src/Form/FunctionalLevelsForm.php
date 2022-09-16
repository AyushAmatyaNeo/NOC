<?php

namespace Setup\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("functionalLevels")
 */
class FunctionalLevelsForm {

    /**
     * @Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Functional Level No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":255}})
     * @Annotation\Attributes({ "id":"functionalLevelNo", "class":"form-control" })
     */
    public $functionalLevelNo;

    /**
     * @Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Functional Level Edesc"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":255}})
     * @Annotation\Attributes({ "id":"functionalLevelEdesc", "class":"form-control" })
     */
    public $functionalLevelEdesc;

    /**
     * @Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Functional Level Ldesc"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1","max":255}})
     * @Annotation\Attributes({ "id":"functionalLevelLdesc", "class":"form-control" })
     */
    public $functionalLevelLdesc;

    /**
     * @Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Roles"})
     * @Annotation\Validator({"name":"StringLength"})
     * @Annotation\Attributes({"id":"form-roles","class":"form-roles form-control","style":"    height: 50px; font-size:12px"})
     */
    public $roles;

}
