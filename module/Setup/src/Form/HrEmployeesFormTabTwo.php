<?php

/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 8/31/16
 * Time: 11:48 AM
 */

namespace Setup\Form;

use Application\Model\Model;
use Zend\Form\Annotation;

class HrEmployeesFormTabTwo extends Model {

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Father Name"})
     * @Annotation\Attributes({ "id":"famFatherName", "class":"form-control" })
     */
    public $famFatherName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Father Occupation"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"famFatherOccupation", "class":"form-control" })
     */
    public $famFatherOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Mother Name"})
     * @Annotation\Attributes({ "id":"famMotherName", "class":"form-control" })
     */
    public $famMotherName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mother Occupation"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"famMotherOccupation", "class":"form-control" })
     */
    public $famMotherOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Grand Father Name"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"famGrandFatherName", "class":"form-control" })
     */
    public $famGrandFatherName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Grand Mother Name"})
     * @Annotation\Attributes({ "id":"famGrandMotherName", "class":"form-control" })
     */
    public $famGrandMotherName;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Maritual Status"})
     * @Annotation\Attributes({ "id":"maritualStatus","class":"form-control"})
     */
    public $maritualStatus;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Name"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"famSpouseName", "class":"form-control" })
     */
    public $famSpouseName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Occupation"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"famSpouseOccupation", "class":"form-control" })
     */
    public $famSpouseOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Spouse Birth Date"})
     * @Annotation\Attributes({ "class":"form-control","id":"famSpouseBirthDate"})
     */
    public $famSpouseBirthDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Wedding Anniversary"})
     * @Annotation\Attributes({ "class":"form-control","id":"famSpouseWeddingAnniversary" })
     * @Annotation\Required(false)
     */
    public $famSpouseWeddingAnniversary;
    public $modifiedBy;
    public $modifiedDt;
    public $mappings = [
        'famFatherName' => 'FAM_FATHER_NAME',
        'famFatherOccupation' => 'FAM_FATHER_OCCUPATION',
        'famMotherName' => 'FAM_MOTHER_NAME',
        'famMotherOccupation' => 'FAM_MOTHER_OCCUPATION',
        'famGrandFatherName' => 'FAM_GRAND_FATHER_NAME',
        'famGrandMotherName' => 'FAM_GRAND_MOTHER_NAME',
        'maritualStatus' => 'MARITAL_STATUS',
        'famSpouseName' => 'FAM_SPOUSE_NAME',
        'famSpouseOccupation' => 'FAM_SPOUSE_OCCUPATION',
        'famSpouseBirthDate' => 'FAM_SPOUSE_BIRTH_DATE',
        'famSpouseWeddingAnniversary' => 'FAM_SPOUSE_WEDDING_ANNIVERSARY',
        'modifiedBy' => 'MODIFIED_BY',
        'modifiedDt' => 'MODIFIED_DT',
    ];

}
