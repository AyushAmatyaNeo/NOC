<?php
namespace SelfService\Form;

use Zend\Form\Annotation;
/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("LoanRequest")
 */
 
class LoanRequestForm{
    
    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name"})
     * @Annotation\Attributes({ "id":"employeeId","class":"form-control"})
     */
    public $employeeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Code:"})
     * @Annotation\Attributes({ "id":"employeeCode","class":"form-control", "readonly":"readonly"})
     */
    public $employeeCode;    

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Employee Name:"})
     * @Annotation\Attributes({ "id":"employeeName","class":"form-control", "readonly":"readonly"})
     */
    public $employeeName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Basic Salary:"})
     * @Annotation\Attributes({ "id":"basicSalary","class":"form-control", "readonly":"readonly"})
     */
    public $basicSalary;

        /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Basic Grade:"})
     * @Annotation\Attributes({ "id":"basicGrade","class":"form-control", "readonly":"readonly"})
     */
    public $basicGrade;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"25% net amount:"})
     * @Annotation\Attributes({ "id":"netAmnt","class":"form-control", "readonly":"readonly"})
     */
    public $netAmnt;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Total salary and Grade"})
     * @Annotation\Attributes({ "id":"salaryGrade","class":"form-control", "readonly":"readonly"})
     */
    public $salaryGrade;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Loan Name"})
     * @Annotation\Attributes({ "id":"loanId","class":"form-control"})
     */
    public $loanId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Fiscal Years"})
     * @Annotation\Attributes({ "id":"fiscalYearId","class":"form-control"})
     */
    public $fiscalYearId;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Month"})
     * @Annotation\Attributes({ "id":"monthId","class":"form-control"})
     */
    public $monthId;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Applied Loan"})
     * @Annotation\Attributes({ "id":"appliedLoan","min":"0", "class":"form-control appliedLoan","step":"0.01", "readonly":"readonly" })
     */
    public $appliedLoan;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Period"})
     * @Annotation\Attributes({ "id":"period","min":"0", "class":"form-period form-control","step":"0.01", "readonly":"readonly" })
     */
    public $period;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"EPF Deduction:"})
     * @Annotation\Attributes({ "id":"epf","min":"0", "class":"form-epf form-control","step":"0.01", "readonly":"readonly" })
     */
    public $epf;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Income Tax Deduction"})
     * @Annotation\Attributes({ "id":"incomeTax","min":"0", "class":"form-incomeTax form-control","step":"0.01", "readonly":"readonly" })
     */
    public $incomeTax;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Social Security Tax Deduction"})
     * @Annotation\Attributes({ "id":"sst","min":"0", "class":"form-sst form-control","step":"0.01", "readonly":"readonly" })
     */
    public $sst;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"CIT Deduction"})
     * @Annotation\Attributes({ "id":"cit","min":"0", "class":"form-cit form-control","step":"0.01" })
     */
    public $cit;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"EWF Location Deduction"})
     * @Annotation\Attributes({ "id":"ewf","min":"0", "class":"form-ewf form-control","step":"0.01", "readonly":"readonly" })
     */
    public $ewf;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Land and Housing Loan"})
     * @Annotation\Attributes({ "id":"landLoan","min":"0", "class":"form-landLoan form-control","step":"0.01", "readonly":"readonly" })
     */
    public $landLoan;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Motorcycle Loan"})
     * @Annotation\Attributes({ "id":"motorCycleLoan","min":"0", "class":"form-motorCycleLoan form-control","step":"0.01", "readonly":"readonly" })
     */
    public $motorCycleLoan;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"House Maintenance Loan"})
     * @Annotation\Attributes({ "id":"hml","min":"0", "class":"form-hml form-control","step":"0.01", "readonly":"readonly" })
     */
    public $hml;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Social Loan"})
     * @Annotation\Attributes({ "id":"socialLoan","min":"0", "class":"form-socialLoan form-control","step":"0.01", "readonly":"readonly" })
     */
    public $socialLoan;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Vehicle Purchase Loan"})
     * @Annotation\Attributes({ "id":"vehiclePurchaseLoan","min":"0", "class":"form-vehiclePurchaseLoan form-control","step":"0.01", "readonly":"readonly" })
     */
    public $vehiclePurchaseLoan;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Medical Loan"})
     * @Annotation\Attributes({ "id":"medicalLoan","min":"0", "class":"form-medicalLoan form-control","step":"0.01", "readonly":"readonly" })
     */
    public $medicalLoan;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Cycle Loan"})
     * @Annotation\Attributes({ "id":"cycleLoan","min":"0", "class":"form-cycleLoan form-control","step":"0.01", "readonly":"readonly" })
     */
    public $cycleLoan;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Education Loan"})
     * @Annotation\Attributes({ "id":"educationLoan","min":"0", "class":"form-educationLoan form-control","step":"0.01", "readonly":"readonly" })
     */
    public $educationLoan;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Family Insurance Loan"})
     * @Annotation\Attributes({ "id":"familyInsuranceLoan","min":"0", "class":"form-familyInsuranceLoan form-control","step":"0.01", "readonly":"readonly" })
     */
    public $familyInsuranceLoan;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Modern Technology Loan"})
     * @Annotation\Attributes({ "id":"modernTechnology","min":"0", "class":"form-modernTechnology form-control","step":"0.01", "readonly":"readonly" })
     */
    public $modernTechnology;



     /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Loan Date"})
     * @Annotation\Attributes({ "id":"loanDate", "class":"form-loanDate form-control" })
     */
    public $loanDate;
   
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Reason"})
     * @Annotation\Attributes({"id":"reason","class":"form-reason form-control","style":"    height: 50px; font-size:12px"})
     */
    public $reason;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Reason for action"})
     * @Annotation\Attributes({"id":"recommendedRemarks","class":"form-reason form-control","style":"    height: 50px; font-size:12px"})
     */
    public $recommendedRemarks;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Reason for action"})
     * @Annotation\Attributes({"id":"approvedRemarks","class":"form-reason form-control","style":"    height: 50px; font-size:12px"})
     */
    public $approvedRemarks;

     /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Repayment Installments"})
     * @Annotation\Attributes({ "id":"repaymentInstallments","class":"form-control repaymentInstallments", "readonly":"readonly"})
     */
    public $repaymentInstallments;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Interest Rate"})
     * @Annotation\Attributes({ "id":"interestRate","class":"form-control interestRate", "readonly":"readonly"})
     */
    public $interestRate;

    /**
     * @Annotation\Type("Zend\Form\Element\File")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"File Upload"})
     * @Annotation\Attributes({"id":"filePath","class":"form-control"})
     */
    public $filePath;


    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Monthly Installment Rate"})
     * @Annotation\Attributes({ "id":"monthlyInstallmentAmount","min":"0", "class":"form-monthlyInstallmentAmount form-control","step":"0.01", "readonly":"readonly" })
     */
    public $monthlyInstallmentAmount;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Monthly Interest Rate"})
     * @Annotation\Attributes({ "id":"monthlyInterestRate","min":"0", "class":"form-monthlyInterestRate form-control","step":"0.01", "readonly":"readonly" })
     */
    public $monthlyInterestRate;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"id":"submitBtn", "value":"Submit","class":"btn btn-success"})
     */
    public $submit;

}