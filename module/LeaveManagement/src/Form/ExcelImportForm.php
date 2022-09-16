<?php
namespace LeaveManagement\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("Excel Import")
 */
class ExcelImportForm
{    
    /**
     * @Annotation\Type("Zend\Form\Element\File")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Upload File"})
     * @Annotation\Attributes({ "id":"file","class":"form-control" })
     */
    public $file;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-default form-control i-search"})
     */
    public $submit;
    
}