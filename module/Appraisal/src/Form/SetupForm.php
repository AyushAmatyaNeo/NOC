<?php
namespace Appraisal\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("AppraisalSetup")
 */

class SetupForm{
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Appraisal Name (in Eng.)"})
     * @Annotation\Attributes({"id":"appraisalEdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"100"}})
     */
    public $appraisalEdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Appraisal Name (in Nep.)"})
     * @Annotation\Attributes({"id":"appraisalNdesc","class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"400"}})
     */
    public $appraisalNdesc;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Appraisal Type"})
     * @Annotation\Attributes({"id":"appraisalTypeId","class":"form-control"})
     */
    public $appraisalTypeId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Start Date"})
     * @Annotation\Attributes({"id":"startDate","class":"form-control"})
     */
    public $startDate;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Date"})
     * @Annotation\Attributes({"id":"endDate","class":"form-control"})
     */
    public $endDate;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Current Stage Name"})
     * @Annotation\Attributes({"id":"currentStageId","class":"form-control"})
     */
    public $currentStageId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"KPI Setting","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"kpiSettting","value":"N"})
     */
    public $kpiSetting;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Competencies Setting","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"competenciesSetting","value":"N"})
     */
    public $competenciesSetting;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"HR Feedback Enable","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"hrFeedbackEnable","value":"N"})
     */
    public $hrFeedbackEnable;
    
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