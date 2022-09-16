<?php
namespace DartaChalani\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("DartaChalaniForm")
 */
class DartaChalaniForm {

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Dispatch Temp Code"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"dispatchTempCode", "class":"form-dispatchTempCode form-control" })
     */
    public $dispatchTempCode;

        /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Y","N":"N"},"label":"Response Flag"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"responseFlag","value":"N"})
     */
    public $responseFlag;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Draft Date"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"draftDt", "class":"form-draftDt form-control"  })
     */
    public $draftDt;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Dispatch Date"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"dispatchDt", "class":"form-dispatchDt form-control"  })
     */
    public $dispatchDt;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Document Date"})
     * @Annotation\Attributes({ "id":"documentDt","class":"form-control"})
     */
    public $documentDt;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"From Department Code"})
     * @Annotation\Attributes({ "id":"fromDepartmentCode", "class":"form-control"})
     */
    public $fromDepartmentCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Recieving Office ID"})
     * @Annotation\Attributes({ "id":"toOfficeCode", "class":"form-control"})
     */
    public $toOfficeCode;
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Location ID"})
     * @Annotation\Attributes({ "id":"toLocationCode", "class":"form-control"})
     */

    public $toLocationCode;

    /**
     * @Annotion\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Description"})
     * @Annotation\Attributes({ "id":"description", "class":"form-control"})
     */
    public $description;


    /**
     * @Annotation\Type("Zend\Form\Element\File")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"File Upload"})
     * @Annotation\Attributes({"id":"filePath","class":"form-control"})
     */
    public $filePath;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"submit","class":"btn btn-success"})
     */
    public $submit;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({"id":"remarks","class":"form-control"})
     */
    public $remarks;



    
}
