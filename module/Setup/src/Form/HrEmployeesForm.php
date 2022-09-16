<?php
namespace Setup\Form;


use Zend\Form\Annotation;

class HrEmployeesForm
{
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Employee Code"})
     * @Annotation\Attributes({ "id":"form-employeeCode", "class":"form-employeeCode form-control" })
     */
    public $employeeCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Companies"})
     * @Annotation\Attributes({ "id":"companyId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $companyId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"First Name"})
     * @Annotation\Attributes({ "id":"form-firstName", "class":"form-control" })
     */
    public $firstName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":false})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Middle Name"})
     * @Annotation\Attributes({ "id":"form-middleName", "class":"form-control" })
     */
    public $middleName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Last Name"})
     * @Annotation\Attributes({ "id":"form-lastName", "class":"form-control" })
     */
    public $lastName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Name in Nepali"})
     * @Annotation\Attributes({ "id":"form-nameNepali", "class":"form-control" })
     */
    public $nameNepali;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Gender"})
     * @Annotation\Attributes({ "id":"genderId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $genderId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Birth Date"})
     * @Annotation\Attributes({ "id":"employeeBirthDate", "class":"form-control" })
     */
    public $birthDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Blood Group"})
     * @Annotation\Attributes({ "id":"bloodGroupId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $bloodGroupId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Religion"})
     * @Annotation\Attributes({ "id":"religionId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $religionId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Social Activity"})
     * @Annotation\Attributes({ "id":"socialActivity","class":"form-control" })
     */
    public $socialActivity;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Telephone No"})
     * @Annotation\Attributes({ "id":"telephoneNo", "class":"form-control" })
     */
    public $telephoneNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mobile No"})
     * @Annotation\Attributes({ "id":"mobileNo", "class":"form-control" })
     */
    public $mobileNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Extension Number"})
     * @Annotation\Attributes({ "id":"extensionNo", "class":"form-control" })
     */
    public $extensionNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email Official"})
     * @Annotation\Attributes({ "id":"emailOfficial", "class":"form-control" })
     */
    public $emailOfficial;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email Personal"})
     * @Annotation\Attributes({ "id":"emailPersonal", "class":"form-control" })
     */
    public $emailPersonal;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Social Network"})
     * @Annotation\Attributes({ "id":"socialNetwork", "class":"form-control" })
     */
    public $socialNetwork;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Emergency Contact Name"})
     * @Annotation\Attributes({ "id":"emergContactName", "class":"form-control" })
     */
    public $emergContactName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Emergency Contact No"})
     * @Annotation\Attributes({ "id":"emergContactNo", "class":"form-control" })
     */
    public $emergContactNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Emergency Contact Address"})
     * @Annotation\Attributes({ "id":"emergContactAddress", "class":"form-control" })
     */
    public $emergContactAddress;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Emergency Contact Relationship"})
     * @Annotation\Attributes({ "id":"emergContactRelationship", "class":"form-control" })
     */
    public $emergContactRelationship;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":" Permanent House No"})
     * @Annotation\Attributes({ "id":"addrPermHouseNo", "class":"form-control" })
     */
    public $addrPermHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":" Permanent Ward No"})
     * @Annotation\Attributes({ "id":"addrPermWardNo", "class":"form-control" })
     */
    public $addrPermWardNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":" Permanent Street Address"})
     * @Annotation\Attributes({ "id":"addrPermStreetAddress", "class":"form-control" })
     */
    public $addrPermStreetAddress;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent VDC or Municipality"})
     * @Annotation\Attributes({ "id":"addrPermVdcMunicipalityId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrPermVdcMunicipalityId;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent District"})
     * @Annotation\Attributes({ "id":"addrPermDistrictId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     *
     */
    public $addrPermDistrictId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent Zone"})
     * @Annotation\Attributes({ "id":"addrPermZoneId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrPermZoneId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Permanent Province"})
     * @Annotation\Attributes({ "id":"addrPermProvinceId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrPermProvinceId;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Temporary House No"})
     * @Annotation\Attributes({ "id":"addrTempHouseNo", "class":"form-control" })
     */
    public $addrTempHouseNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Temporary Ward No"})
     * @Annotation\Attributes({ "id":"addrTempWardNo", "class":"form-control" })
     */
    public $addrTempWardNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Temporary Street Address"})
     * @Annotation\Attributes({ "id":"addrTempStreetAddress", "class":"form-control" })
     */
    public $addrTempStreetAddress;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary VDC or Municipality"})
     * @Annotation\Attributes({ "id":"addrTempVdcMunicipality","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrTempVdcMunicipalityId;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary District"})
     * @Annotation\Attributes({ "id":"addrTempDistrictId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrTempDistrictId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary Zone"})
     * @Annotation\Attributes({ "id":"addrTempZoneId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrTempZoneId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Temporary Province"})
     * @Annotation\Attributes({ "id":"addrTempProvinceId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $addrTempProvinceId;


    /**
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
     * @Annotation\Attributes({ "id":"famFatherOccupation", "class":"form-control" })
     */
    public $famFatherOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mother Name"})
     * @Annotation\Attributes({ "id":"famMotherName", "class":"form-control" })
     */
    public $famMotherName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mother Occupation"})
     * @Annotation\Attributes({ "id":"famMotherOccupation", "class":"form-control" })
     */
    public $famMotherOccupation;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Grand Father Name"})
     * @Annotation\Attributes({ "id":"famGrandFatherName", "class":"form-control" })
     */
    public $famGrandFatherName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Grand Mother Name"})
     * @Annotation\Attributes({ "id":"famGrandMotherName", "class":"form-control" })
     */
    public $famGrandMotherName;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Maritual Status"})
     * @Annotation\Attributes({ "id":"maritualStatus","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $maritualStatus;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Name"})
     * @Annotation\Attributes({ "id":"famSpouseName", "class":"form-control" })
     */
    public $famSpouseName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Occupation"})
     * @Annotation\Attributes({ "id":"famSpouseOccupation", "class":"form-control" })
     */
    public $famSpouseOccupation;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Birth Date"})
     * @Annotation\Attributes({ "id":"famSpouseBirthDate", "class":"form-control" })
     */
    public $famSpouseBirthDate;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Spouse Wdding Anniversary"})
     * @Annotation\Attributes({ "id":"famSpouseWeddingAnniversary", "class":"form-control" })
     */
    public $famSpouseWeddingAnniversary;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Id Card No"})
     * @Annotation\Attributes({ "id":"idCardNo", "class":"form-control" })
     */
    public $idCardNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Id Lb rf"})
     * @Annotation\Attributes({ "id":"idLbrf", "class":"form-control" })
     */
    public $idLbrf;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Id Bar code"})
     * @Annotation\Attributes({ "id":"idBarCode", "class":"form-control" })
     */
    public $idBarCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Provident Fund No"})
     * @Annotation\Attributes({ "id":"idProvidentFundNo", "class":"form-control" })
     */
    public $idProvidentFundNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Driving License No"})
     * @Annotation\Attributes({ "id":"idDrivingLicenseNo", "class":"form-control" })
     */
    public $idDrivingLicenseNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Driving License Type"})
     * @Annotation\Attributes({ "id":"idDrivingLicenseType", "class":"form-control" })
     */
    public $idDrivingLicenseType;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Driving License Expiry"})
     * @Annotation\Attributes({ "id":"idDrivingLicenseExpiry", "class":"form-control" })
     */
    public $idDrivingLicenseExpiry;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Thumb Id"})
     * @Annotation\Attributes({ "id":"idThumbId", "class":"form-control" })
     */
    public $idThumbId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Pan No"})
     * @Annotation\Attributes({ "id":"idPanNo", "class":"form-control" })
     */
    public $idPanNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Account Id"})
     * @Annotation\Attributes({ "id":"idAccountId", "class":"form-control" })
     */
    public $idAccountId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Retirement No"})
     * @Annotation\Attributes({ "id":"idRetirementNo", "class":"form-control" })
     */
    public $idRetirementNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship No"})
     * @Annotation\Attributes({ "id":"idCitizenshipNo", "class":"form-control" })
     */
    public $idCitizenshipNo;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship Issue Date"})
     * @Annotation\Attributes({ "id":"idCitizenshipIssueDate", "class":"form-control" })
     */
    public $idCitizenshipIssueDate;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"CitizenshipIssue Place"})
     * @Annotation\Attributes({ "id":"idCitizenshipIssuePlace", "class":"form-control" })
     */
    public $idCitizenshipIssuePlace;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Passport No"})
     * @Annotation\Attributes({ "id":"idPassportNo", "class":"form-control" })
     */
    public $idPassportNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Passport Expiry"})
     * @Annotation\Attributes({ "id":"idPassportExpiry", "class":"form-control" })
     */
    public $idPassportExpiry;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Join Date"})
     * @Annotation\Attributes({ "id":"joinDate", "class":"form-control" })
     */
    public $joinDate;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Salary"})
     * @Annotation\Attributes({ "id":"salary", "class":"form-control" })
     */
    public $salary;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Salary PF"})
     * @Annotation\Attributes({ "id":"salaryPf", "class":"form-control" })
     */
    public $salaryPf;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Country"})
     * @Annotation\Attributes({ "id":"countryId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    public $countryId;


    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"id":"submit","value":"Submit","class":"btn btn-primary pull-right hidden"})
     */
    public $submit;

}