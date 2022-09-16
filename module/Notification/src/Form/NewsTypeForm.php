<?php

namespace Notification\Form;

use Zend\Form\Annotation;

class NewsTypeForm {



    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"News Type"})
     * @Annotation\Attributes({"id":"newsTypeDesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     */
    public $newsTypeDesc;
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Upload"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"uploadFlag"})
     */
    public $uploadFlag;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Download"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"id":"downloadFlag"})
     */
    public $downloadFlag;

    

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
