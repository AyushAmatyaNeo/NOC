<?php
namespace Appraisal\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("AppraisalDefaultRating")
 */

class DefaultRatingForm{   
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Default Rating"})
     * @Annotation\Attributes({"id":"defaultValue","class":"form-control","min":"0","step":"0.01"})
     */
    public $defaultValue;
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Min Value"})
     * @Annotation\Attributes({"id":"minValue","class":"form-control","min":"0","step":"0.01"})
     */
    public $minValue;
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Max Value"})
     * @Annotation\Attributes({"id":"maxValue","class":"form-control","min":"0","step":"0.01"})
     */
    public $maxValue;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Appraisal Type"})
     * @Annotation\Attributes({"id":"appraisalTypeId","class":"form-control"})
     */
    public $appraisalTypeId;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Designations"})
     * @Annotation\Attributes({"id":"designationIds","class":"form-control","multiple":"multiple"})
     */
    public $designationIds;
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Positions"})
     * @Annotation\Attributes({"id":"positionId","class":"form-control","multiple":"multiple"})
     */
    public $positionIds;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;            
}