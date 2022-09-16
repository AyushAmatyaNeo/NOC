<?php

namespace Asset\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("AssetGroup")
 */

class GroupForm {

     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Asset Group NAME (in Eng.)"})
     * @Annotation\Attributes({"id":"assestGroupEdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $assestGroupEdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Asset Group NAME (in Nep.)"})
     * @Annotation\Attributes({"id":"assetGroupNdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    
    public $assetGroupNdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     */
    public $remarks;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;            

}
