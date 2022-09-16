<?php
namespace Setup\Form;


use Application\Model\Model;
use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("HrEmployeesFormTabEight")
 */

class HrEmployeesFormTabEight extends Model{
    
    
    public $employeeId;
    
    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Training Heading"})
     * @Annotation\Attributes({ "id":"trainingName", "class":"form-control" })
     */
    
    public $trainingName;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Remarks"})
     * @Annotation\Attributes({ "id":"description", "class":"form-control" })
     */
    public $description;
    
    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"From"})
     * @Annotation\Attributes({"class":"form-control","id":"trafromDate" })
     */
    public $fromDate;
    
     /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"To"})
     * @Annotation\Attributes({"class":"form-control","id":"tratoDate" })
     */
    public $toDate;

    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Total"})
     * @Annotation\Attributes({"class":"form-control","id":"totalDays" })
     */
    public $totalDays;

    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Location"})
     * @Annotation\Attributes({"class":"form-control","id":"location" })
     */
    public $location;

    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Delivered By"})
     * @Annotation\Attributes({"class":"form-control","id":"deliveredBy" })
     */
    public $deliveredBy;

    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Funding/Amount"})
     * @Annotation\Attributes({"class":"form-control","id":"funding" })
     */
    public $funding;

    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Certificate Awarded"})
     * @Annotation\Attributes({"class":"form-control","id":"certification" })
     */
    public $certification;
    
    public $id;
    public $remarks;
    public $companyId;
    public $branchId;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
    public $approved;
    public $approvedBy;
    public $approvedDate;
    public $status;
    
    public $mappings=[
        'id'=>'ID',
        'employeeId'=>'EMPLOYEE_ID',
        'trainingName'=>'TRAINING_NAME',
        'description'=>'DESCRIPTION',
        'fromDate'=>'FROM_DATE',
        'toDate'=>'TO_DATE',
        'remarks'=>'REMARKS',
        'companyId'=>'COMPANY_ID',
        'branchId'=>'BRANCH_ID',
        'createdBy'=>'CREATED_BY',
        'createdDate'=>'CREATED_DATE',
        'modifiedBy'=>'MODIFIED_BY',
        'modifiedDate'=>'MODIFIED_DATE',
        'approved'=>'APPROVED',
        'approvedBy'=>'APPROVED_BY',
        'approvedDate'=>'APPROVED_DATE',
        'status'=>'STATUS'
    ];
    
    
}

