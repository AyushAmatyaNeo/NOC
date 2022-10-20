<?php
namespace Recruitment\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("OptionsForm")
 */
class OptionsForm {    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Inclusion English"})
     * @Annotation\Attributes({ "id":"OptionsEdesc", "class":"OptionsEdesc form-control" })
     */
    public $OptionsEdesc;
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Inclusion Nepali "})
     * @Annotation\Attributes({ "id":"OptionsNdesc", "class":"OptionsNdesc form-control" })
     */
    public $OptionsNdesc;
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"Remarks", "class":"form-control" })
     */
    public $Remarks;
        
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
