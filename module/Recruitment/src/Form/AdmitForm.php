<?php
namespace Recruitment\Form;
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("Admit")
 */

Class AdmitForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({ "id":"previous id","type":"hidden"})
     */
    public $AdmitSetupId;

	/**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Declaration Text "})
     * @Annotation\Attributes({ "id":"declaration-text","class":"summernote form-control"})
     */
    public $DeclarationText;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Terms & Condition"})
     * @Annotation\Attributes({ "id":"terms","class":"summernote form-control"})
     */
    public $Terms;

    /**
     * @Annotation\Type("Zend\Form\Element\File")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Upload Director Sign"})
     * @Annotation\Attributes({ "id":"file","class":"form-control", "onchange" : "showPreview(event)"})
     */
    public $File;



    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"submit","class":"btn btn-success"})
     */
    public $submit;


}