<?php

namespace DocumentRegistration\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("registration-form")
 */
class AddIncommingForm {

     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Registration Number"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"registrationTempCode","min":"0", "class":"form-control"})
     */
    public $registrationTempCode;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Registration Date:"})
     * @Annotation\Attributes({ "id":"registrationDate", "class":"form-control","placeholder":"Date in English"})
     */
    public $registrationDate;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Completion Date:"})
     * @Annotation\Attributes({ "id":"completionDate", "class":"form-control","placeholder":"Date in English"})
     */
    public $completionDate;


    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Receiving Letter Ref No:"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"form-receivingLetterReferenceNo", "class":"form-receivingLetterReferenceNo form-control"  })
     */
    public $receivingLetterReferenceNo;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"From Office:"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"fromOfficeId ", "class":"form-fromOfficeId form-control"  })
     */
    public $fromOfficeId;

     /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"form-processId ", "class":"form-processId form-control"  })
     */
    public $processId;

    /**
     * @Annotion\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Description:"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({"id":"description","class":"form-control", "type":"textarea"})
     */
    public $description;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Receiving Letter Ref Date:"})
     * @Annotation\Attributes({ "id":"receivingLetterReferenceDate", "class":"form-control","placeholder":"Date in English"})
     */
    public $receivingLetterReferenceDate;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Receiving Department: "})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"form-receivingDepartment", "class":"form-receivingDepartment form-control"  })
     */
    public $receivingDepartment;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Receiving Department: "})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"form-locationId", "class":"form-locationId form-control"  })
     */
    public $locationId;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Receiver Name:"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({ "id":"form-receiverName", "class":"form-receiverName form-control"  })
     */
    public $receiverName;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Receiving Date:"})
     * @Annotation\Attributes({ "id":"documentDate", "class":"form-control","placeholder":"Date in English" })
     */
    public $documentDate;



    // /**
    //  * @Annotion\Type("Zend\Form\Element\File")
    //  * @Annotation\Required(false)
    //  * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
    //  * @Annotation\Options({"label":"Files Upload:"})
    //  * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
    //  * @Annotation\Attributes({ "id":"file", "class":"form-fileUpload form-control", "type":"file","multiple"})
    //  */
    // public $filesUpload;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Is Response"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"responseFlag","value":"N"})
     */
    public $responseFlag;



    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Sampati Bibaran"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"choiceFlag","value":"N"})
     */
    public $choiceFlag;


    /**
     * @Annotion\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Fiscal Year:"})
     * @Annotation\Attributes({ "id":"sbFiscalYear", "class":"form-control","placeholder":"Date in English"})
     */
    public $sbFiscalYear;

    /**
     * @Annotion\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Fiscal Year:"})
     * @Annotation\Attributes({ "id":"employee", "class":"form-control","placeholder":"Employee Name"})
     */
    public $employeeId;


     /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Karya Sampadan"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"choiceFlagKS","value":"N"})
     */
    public $choiceFlagKS;


    /**
     * @Annotion\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Fiscal Year:"})
     * @Annotation\Attributes({ "id":"sbFiscalYear", "class":"form-control","placeholder":"Date in English"})
     */
    public $ksFiscalYear;

    /**
     * @Annotion\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Fiscal Year:"})
     * @Annotation\Attributes({ "id":"employee", "class":"form-control","placeholder":"Employee Name"})
     */
    public $empId;




    /**
     * @Annotion\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks:"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     * @Annotation\Attributes({"id":"remarks","class":"form-control", "type":"textarea"})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
}
