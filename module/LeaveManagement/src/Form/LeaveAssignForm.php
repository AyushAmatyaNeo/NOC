<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/9/16
 * Time: 10:52 AM
 */

namespace LeaveManagement\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("LeaveAssign")
 */
class LeaveAssignForm
{

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Leave"})
     * @Annotation\Attributes({ "id":"leaveId","class":"form-control"})
     */
    public $leaveId;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Previous Year Balance"})
     * @Annotation\Required(false)
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"2"}})
     * @Annotation\Attributes({ "id":"previousYearBalance", "class":"form-control","min":"0","max":"99"  })
     */
    public $previousYearBalance;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Total Days"})
     * @Annotation\Required(false)
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"2"}})
     * @Annotation\Attributes({ "id":"totalDays", "class":"form-control","min":"0","max":"99"  })
     */
    public $totalDays;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Balance"})
     * @Annotation\Required(false)
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"2"}})
     * @Annotation\Attributes({ "id":"balance", "class":"form-control","min":"0","max":"99"  })
     */
    public $balance;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(true)
     * @Annotation\Options({"label":"Fiscal Year"})
     * @Annotation\Attributes({ "id":"fiscalYear", "class":"form-control" })
     */
    public $fiscalYear;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"remarks", "class":"form-control" })
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     */
    public $remarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success pull-right"})
     */
    public $submit;

}