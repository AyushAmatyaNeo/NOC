<?php
namespace Appraisal\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("AppraisalQuestion")
 */

class QuestionForm{
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Question Code"})
     * @Annotation\Attributes({"id":"questionCode","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     */
    public $questionCode;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Question Name (in Eng.)"})
     * @Annotation\Attributes({"id":"questionEdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     */
    public $questionEdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Question Name (in Nep.)"})
     * @Annotation\Attributes({"id":"questionNdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"400"}})
     */
    public $questionNdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Heading Name"})
     * @Annotation\Attributes({"id":"headingId","class":"form-control"})
     */
    public $headingId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Appraisee Flag","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"appraiseeFlag"})
     */
    public $appraiseeFlag;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Appraiser Flag","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"appraiserFlag"})
     */
    public $appraiserFlag;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Reviewer Flag","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"reviewerFlag"})
     */
    public $reviewerFlag;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Appraisee Rating","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"appraiseeRating"})
     */
    public $appraiseeRating;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Appraiser Rating","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"appraiserRating"})
     */
    public $appraiserRating;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Reviewer Rating","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"reviewerRating"})
     */
    public $reviewerRating;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Min. Value"})
     * @Annotation\Attributes({"id":"minValue","class":"form-control","min":"0","step":"0.01"})
     */
    public $minValue;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Max. Value"})
     * @Annotation\Attributes({"id":"maxValue","class":"form-control","min":"0","step":"0.01"})
     */
    public $maxValue;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Order No."})
     * @Annotation\Attributes({"id":"orderNo","class":"form-control","min":"0"})
     */
    public $orderNo;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Answer Type"})
     * @Annotation\Attributes({"id":"answerType","class":"form-control"})
     */
    public $answerType;
    
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
