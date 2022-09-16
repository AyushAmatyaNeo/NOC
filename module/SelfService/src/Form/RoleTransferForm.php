<?php
namespace SelfService\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("RoleTransfer")
 */
class RoleTransferForm
{
   
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Validator({"name":"NotEmpty"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Recommender"})
     * @Annotation\Attributes({ "id":"recommender","class":"form-control"})
     */
    public $recommender;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Approver"})
     * @Annotation\Attributes({ "id":"approver","class":"form-control"})
     */
    public $approver;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}
