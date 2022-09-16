<?php

namespace Notification\Form;

use Zend\Form\Annotation;

class NewsForm { 

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"News Date"})
     * @Annotation\Attributes({"id":"newsDate","class":"form-control"})
     */
    public $newsDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"News Type"})
     * @Annotation\Attributes({ "id":"newsType","class":"form-control"})
     */
    public $newsType;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"News Title"})
     * @Annotation\Attributes({"id":"newsTitle","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     */
    public $newsTitle;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"News In English"})
     * @Annotation\Attributes({ "id":"newsEdesc", "class":"form-control form-reason" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"3000"}})
     */
    public $newsEdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"News In Nepali"})
     * @Annotation\Attributes({ "id":"newsLdesc", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"3000"}})
     */
    public $newsLdesc;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"400"}})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Expiry Date"})
     * @Annotation\Attributes({"id":"newsExpiryDate","class":"form-control"})
     */
    public $newsExpiryDate;

}
