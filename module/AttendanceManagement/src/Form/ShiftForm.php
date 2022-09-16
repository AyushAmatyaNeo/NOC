<?php

namespace AttendanceManagement\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("Shift")
 */
class ShiftForm {

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Shift Ename"})
     * @Annotation\Attributes({ "id":"shiftEname", "class":"form-control" })
     */
    public $shiftEname;

    /**
     * @Annotion\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Shift Lname"})
     * @Annotation\Attributes({ "id":"shiftLname", "class":"form-control" })
     */
    public $shiftLname;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Start Time"})
     * @Annotation\Attributes({ "id":"startTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control" })
     */
    public $startTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Time"})
     * @Annotation\Attributes({ "id":"endTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control"})
     */
    public $endTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Half Day End Time"})
     * @Annotation\Attributes({ "id":"halfDayEndTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control"})
     */
    public $halfDayEndTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Half Time"})
     * @Annotation\Attributes({ "id":"halfTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control"})
     */
    public $halfTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Late In"})
     * @Annotation\Attributes({ "id":"lateIn","data-format":"H:mm", "data-template":"HH : mm",  "class":"form-control"})
     */
    public $lateIn;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Early Out"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"earlyOut","data-format":"H:mm", "data-template":"HH : mm", "class":"form-control"})
     */
    public $earlyOut;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"StartDate"})
     * @Annotation\Attributes({ "id":"startDate","class":"form-control" })
     */
    public $startDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"End Date"})
     * @Annotation\Attributes({ "id":"endDate","class":"form-control" })
     */
    public $endDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday One"})
     * @Annotation\Attributes({ "id":"weekDay1"})
     */
    public $weekday1;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Two"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay2", "class":"form-control"})
     */
    public $weekday2;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Three"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay3"})
     */
    public $weekday3;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Four"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay4"})
     */
    public $weekday4;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Five"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay5"})
     */
    public $weekday5;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Six"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay6"})
     */
    public $weekday6;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"N":"Normal","H":"Half Day","DAY_OFF":"Day Off"},"label":"Weekday Seven"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({ "id":"weekDay7"})
     */
    public $weekday7;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Current Shift"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"currentShift"})
     */
    public $currentShift;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Deduct Break Time"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"breakDeductFlag"})
     */
    public $breakDeductFlag;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"E":"Enabled","D":"Disabled"},"label":"Two Day Shift"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"twoDayShift"})
     */
    public $twoDayShift;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Default Shift"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"defaultShift"})
     */
    public $defaultShift;

    /**
     * @Annotion\Type("Zend\Form\Element\Textarea")
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

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Company"})
     * @Annotation\Attributes({ "id":"companyId","class":"form-control"})
     */
    public $companyId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Actual Working Hour"})
     * @Annotation\Attributes({ "id":"actualWorkingHr", "data-format":"h:mm", "data-template":"hh : mm", "class":"form-control" })
     */
    public $actualWorkingHr;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Total Working Hour"})
     * @Annotation\Attributes({ "id":"totalWorkingHr", "data-format":"h:mm", "data-template":"hh : mm", "class":"form-control" })
     */
    public $totalWorkingHr;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Grace Start Time"})
     * @Annotation\Attributes({ "id":"graceStartTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control" })
     */
    public $graceStartTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Grace End Time"})
     * @Annotation\Attributes({ "id":"graceEndTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control"})
     */
    public $graceEndTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Halfday In Time"})
     * @Annotation\Attributes({ "id":"halfDayInTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control"})
     */
    public $halfDayInTime;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Halfday Out Time"})
     * @Annotation\Attributes({ "id":"halfDayOutTime", "data-format":"h:mm a", "data-template":"hh : mm A", "class":"form-control"})
     */
    public $halfDayOutTime;
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Night Shift","value_options":{"Y":"Yes","N":"No"}})
     * @Annotation\Attributes({"id":"nightShift","class":"form-control","value":"N"})
     */
    public $nightShift;

}
