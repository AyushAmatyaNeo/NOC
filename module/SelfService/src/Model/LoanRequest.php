<?php
namespace SelfService\Model;

use Application\Model\Model;

class LoanRequest extends Model{
    const TABLE_NAME = "HRIS_EMPLOYEE_LOAN_REQUEST";
    const LOAN_REQUEST_ID = "LOAN_REQUEST_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const REPAYMENT_MONTHS = "REPAYMENT_MONTHS";
    const LOAN_ID = "LOAN_ID";
    const REQUESTED_AMOUNT = "REQUESTED_AMOUNT";
    const REQUESTED_DATE = "REQUESTED_DATE";
    const LOAN_DATE = "LOAN_DATE";
    const REASON = "REASON";
    const STATUS = "STATUS";
    const APPROVED_AMOUNT = "APPROVED_AMOUNT";
    const INTEREST_RATE = "INTEREST_RATE";
    const RECOMMENDED_BY = "RECOMMENDED_BY";
    const RECOMMENDED_DATE = "RECOMMENDED_DATE";
    const RECOMMENDED_REMARKS = "RECOMMENDED_REMARKS";
    const APPROVED_BY = "APPROVED_BY";
    const APPROVED_DATE = "APPROVED_DATE";
    const APPROVED_REMARKS = "APPROVED_REMARKS";
    const DEDUCT_ON_SALARY = "DEDUCT_ON_SALARY";
    const FILE_PATH = "FILE_PATH";
    const EMPLOYEE_CODE = "EMPLOYEE_CODE";
    const BASIC_SALARY = "BASIC_SALARY";
    const BASIC_GRADE ="BASIC_GRADE";
    const NET_AMOUNT ="NET_AMOUNT";
    const SALARY_GRADE ="SALARY_GRADE";
    const APPLIED_LOAN ="APPLIED_LOAN";
    const PERIOD ="PERIOD";
    const EPF ="EPF";
    const INCOME_TAX ="INCOME_TAX";
    const SST ="SST";
    const CIT ="CIT";
    const EWF ="EWF";
    const LAND_LOAN ="LAND_LOAN";
    const MOTORCYCLE_LOAN ="MOTORCYCLE_LOAN";
    const HML ="HML";
    const SOCIAL_LOAN ="SOCIAL_LOAN";
    const VEHICLE_PURCHASE_LOAN ="VEHICLE_PURCHASE_LOAN";
    const MEDICAL_LOAN ="MEDICAL_LOAN";
    const CYCLE_LOAN ="CYCLE_LOAN";
    const EDUCATION_LOAN ="EDUCATION_LOAN";
    const FAMILY_INSURANCE_LOAN ="FAMILY_INSURANCE_LOAN";
    const MODERN_TECHNOLOGY ="MODERN_TECHNOLOGY";
    const REPAYMENT_INSTALLMENTS ="REPAYMENT_INSTALLMENTS";
    const MONTHLY_INSTALLMENT_AMOUNT ="MONTHLY_INSTALLMENT_AMOUNT";
    const MONTHLY_INSTALLMENT_RATE ="MONTHLY_INSTALLMENT_RATE";
     
    public $loanRequestId;
    public $employeeId;
    public $loanId;
    public $requestedDate;
    public $repaymentMonths;
    public $requestedAmount;
    public $loanDate;
    public $reason;
    public $status;
    public $approvedAmount;
    public $deductOnSalary;
    public $recommendedBy;
    public $recommendedDate;
    public $interestRate;
    public $recommendedRemarks;
    public $approvedBy;
    public $approvedDate;
    public $approvedRemarks;
    public $filePath;
    
    public $employeeCode;    
    public $employeeName;
    public $basicSalary;
    public $basicGrade;
    public $netAmnt;
    public $salaryGrade;
    public $appliedLoan;
    public $period;
    public $epf;
    public $incomeTax;
    public $sst;
    public $cit;
    public $ewf;
    public $landLoan;
    public $motorCycleLoan;
    public $hml;
    public $socialLoan;
    public $vehiclePurchaseLoan;
    public $medicalLoan;
    public $cycleLoan;
    public $educationLoan;
    public $familyInsuranceLoan;
    public $modernTechnology;
    public $repaymentInstallments;
    public $monthlyInstallmentAmount;
    public $monthlyInterestRate;
    
    public $mappings = [
        'loanRequestId'=> self::LOAN_REQUEST_ID,
        'employeeId'=> self::EMPLOYEE_ID,
        'loanId'=> self::LOAN_ID,
        'requestedAmount'=> self::REQUESTED_AMOUNT,
        'requestedDate'=>self::REQUESTED_DATE,
        'loanDate'=>self::LOAN_DATE,
        'reason'=>self::REASON,
        'status'=>self::STATUS,
        'repaymentMonths' => self::REPAYMENT_MONTHS,
        'approvedAmount'=>self::APPROVED_AMOUNT,
        'interestRate'=>self::INTEREST_RATE,
        'recommendedBy'=>self::RECOMMENDED_BY,
        'recommendedDate'=>self::RECOMMENDED_DATE,
        'recommendedRemarks'=>self::RECOMMENDED_REMARKS,
        'approvedBy'=>self::APPROVED_BY,
        'approvedDate'=>self::APPROVED_DATE,
        'approvedRemarks'=>self::APPROVED_REMARKS,
        'deductOnSalary'=>self::DEDUCT_ON_SALARY,
        'filePath'=>self::FILE_PATH,
        'employeeCode' =>self::EMPLOYEE_CODE,
        'basicSalary'=>self::BASIC_SALARY,
        'basicGrade'=>self::BASIC_GRADE,
        'netAmnt'=>self::NET_AMOUNT,
        'salaryGrade'=>self::SALARY_GRADE,
        'appliedLoan'=>self::APPLIED_LOAN,
        'period'=>self::PERIOD,
        'epf'=>self::EPF,
        'incomeTax'=>self::INCOME_TAX,
        'sst'=>self::SST,
        'cit'=>self::CIT,
        'ewf'=>self::EWF,
        'landLoan'=>self::LAND_LOAN,
        'motorCycleLoan'=>self::MOTORCYCLE_LOAN,
        'hml'=>self::HML,
        'socialLoan'=>self::SOCIAL_LOAN,
        'vehiclePurchaseLoan'=>self::VEHICLE_PURCHASE_LOAN,
        'medicalLoan'=>self::MEDICAL_LOAN,
        'cycleLoan'=>self::CYCLE_LOAN,
        'educationLoan'=>self::EDUCATION_LOAN,
        'familyInsuranceLoan'=>self::FAMILY_INSURANCE_LOAN,
        'modernTechnology'=>self::MODERN_TECHNOLOGY,
        'repaymentInstallments'=>self::REPAYMENT_INSTALLMENTS,
        'monthlyInstallmentAmount'=>self::MONTHLY_INSTALLMENT_AMOUNT,
        'monthlyInterestRate'=>self::MONTHLY_INSTALLMENT_RATE

    ];
    
}



        