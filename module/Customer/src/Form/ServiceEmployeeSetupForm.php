<?php

namespace Customer\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("WagedEmployeeSetupForm")
 */

class ServiceEmployeeSetupForm {
    
    public $employeeId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Employee Code"})
     * @Annotation\Attributes({ "id":"form-employeeCode", "class":"form-employeeCode form-control" })
     */
    
       public $employeeCode;
       
        /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"First Name"})
     * @Annotation\Attributes({ "id":"firstName", "class":"form-control" })
     */
    
      public  $firstName;
      
      /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Middle Name"})
     * @Annotation\Attributes({ "id":"middleName", "class":"form-control" })
     */
    
       public $middleName;
       
      /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Last Name"})
     * @Annotation\Attributes({ "id":"lastName", "class":"form-control" })
     */
    
      public  $lastName;
      
      /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Full Name"})
     * @Annotation\Attributes({ "id":"fullName", "class":"form-control" })
     */
    
      public  $fullName;
      
      
      /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Gender"})
     * @Annotation\Attributes({ "id":"genderId","class":"form-control"})
     */
    
       public $genderId;
       
       /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Blood Group"})
     * @Annotation\Attributes({ "id":"bloodGroupId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    
       public $bloodGroupId;
       
       /**
     * @Annotation\Type("Application\Custom\FormElement\Telephone")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Telephone No"})
     * @Annotation\Attributes({ "id":"telephoneNo", "placeholder":"xxx-xxxxxxx", "pattern":"^\(?\d{2,3}\)?[- ]?\d{7}$", "class":"form-control","title"="Enter your mobile number(xx-xxxxxxx)"})
     */
    
       public $telephoneNo;
       
       /**
     * @Annotation\Type("Application\Custom\FormElement\Mobile")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mobile No"})
     * @Annotation\Attributes({ "id":"MobileNo", "placeholder":"xxx-xxx-xxxx", "class":"form-control" , "pattern"="^\(?\d{3}\)?[- ]?\d{3}[- ]?\d{4}$", "title"="Enter your mobile number(xxx-xxx-xxxx)"})
     */
    
       public $mobileNo;
       
       /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email"})
     * @Annotation\Attributes({ "id":"email", "class":"form-control" })
     */
    
       public $email;
       
       /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"50"}})
     * @Annotation\Attributes({ "id":"citizenshipNo", "class":"form-control" })
     */
    
       public $citizenshipNo;
       
       /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship Issue Date"})
     * @Annotation\Attributes({"class":"form-control","id":"citizenshipIssueDate" })
     */
    
      public  $citizenshipIssueDate;
      
      
      /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship Issue Place"})
     * @Annotation\Attributes({ "id":"citizenshipIssuePlace", "class":"form-control" })
     */
    
      public  $citizenshipIssuePlace;
      
       /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent Zone"})
     * @Annotation\Attributes({ "id":"permanentZoneId","class":"form-control"})
     */
    
      public  $permanentZoneId;
      
      /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":" Permanent District"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"permanentDistrictId","class":"form-control"})
     */
    
       public $permanentDistrictId;
       
       /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":" Temp Zone"})
     * @Annotation\Attributes({ "id":"temporaryZoneId","class":"form-control"})
     */
    
      public  $temporaryZoneId;
      
      /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":" Temp District"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"temporaryDistrictId","class":"form-control"})
     */
    
       public $temporaryDistrictId;
       
       /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
    
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Type","value_options":{"P":"Part Time","F":"Full Time"}})
     * @Annotation\Attributes({ "id":"employeeType","class":"form-control"})
     */
    public $employeeType;
    
     /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Account No"})
     * @Annotation\Attributes({ "id":"accountNo", "class":"form-control" })
     */
    public $accountNo;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Branch"})
     * @Annotation\Attributes({ "id":"branchId","class":"form-control"})
     */
    public $branchId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Department"})
     * @Annotation\Attributes({ "id":"$departmentId","class":"form-control"})
     */
    public $departmentId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Designation"})
     * @Annotation\Attributes({ "id":"designationId","class":"form-control"})
     */
    public $designationId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Position"})
     * @Annotation\Attributes({ "id":"positionId","class":"form-control"})
     */
    public $positionId;
    
    
    
    
    
}
