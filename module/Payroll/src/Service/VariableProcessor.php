<?php

namespace Payroll\Service;

use Payroll\Repository\PayrollRepository;
use Setup\Repository\EmployeeRepository;

class VariableProcessor {

    private $adapter;
    private $employeeId;
    private $employeeRepo;
    private $monthId;
    private $sheetNo;
    private $payrollRepo;

    public function __construct($adapter, $employeeId, int $monthId, int $sheetNo) {
        $this->adapter = $adapter;
        $this->employeeId = $employeeId;
        $this->monthId = $monthId;
        $this->sheetNo = $sheetNo;
        $this->employeeRepo = new EmployeeRepository($adapter);
        $this->payrollRepo = new PayrollRepository($this->adapter);
    }

    public function processVariable($variable) {
        $processedValue = "";
        switch ($variable) {
            /*
             * BASIC_SALARY
             */
            case PayrollGenerator::VARIABLES[0]:
                $processedValue = $this->payrollRepo->fetchBasicSalary($this->employeeId, $this->sheetNo);
                break;
            /*
             * MONTH_DAYS
             */
            case PayrollGenerator::VARIABLES[1]:
                $processedValue = $this->payrollRepo->getMonthDays($this->employeeId, $this->sheetNo);
                break;
            /*
             * PRESENT_DAYS
             */
            case PayrollGenerator::VARIABLES[2]:
                $processedValue = $this->payrollRepo->getPresentDays($this->employeeId, $this->sheetNo);
                break;
            /*
             * ABSENT_DAYS
             */
            case PayrollGenerator::VARIABLES[3]:
                $processedValue = $this->payrollRepo->getAbsentDays($this->employeeId, $this->sheetNo);
                break;
            /*
             * PAID_LEAVES
             */
            case PayrollGenerator::VARIABLES[4]:
                $processedValue = $this->payrollRepo->getPaidLeaves($this->employeeId, $this->sheetNo);
                break;
            /*
             * UNPAID_LEAVES
             */
            case PayrollGenerator::VARIABLES[5]:
                $processedValue = $this->payrollRepo->getUnpaidLeaves($this->employeeId, $this->sheetNo);
                break;
            /*
             * DAY_OFFS
             */
            case PayrollGenerator::VARIABLES[6]:
                $processedValue = $this->payrollRepo->getDayoffs($this->employeeId, $this->sheetNo);
                break;
            /*
             * HOLIDAYS
             */
            case PayrollGenerator::VARIABLES[7]:
                $processedValue = $this->payrollRepo->getHolidays($this->employeeId, $this->sheetNo);
                break;
            /*
             * DAYS_FROM_JOIN_DATE
             */
            case PayrollGenerator::VARIABLES[8]:
                $processedValue = $this->payrollRepo->getDaysFromJoinDate($this->employeeId, $this->sheetNo);
                break;
            /*
             * DAYS_FROM_PERMANENT_DATE
             */
            case PayrollGenerator::VARIABLES[9]:
                $processedValue = $this->payrollRepo->getDaysFromPermanentDate($this->employeeId, $this->monthId);
                break;
            /*
             * IS_MALE
             */
            case PayrollGenerator::VARIABLES[10]:
                $processedValue = $this->payrollRepo->isMale($this->employeeId, $this->sheetNo);
                break;
            /*
             * IS_FEMALE
             */
            case PayrollGenerator::VARIABLES[11]:
                $processedValue = $this->payrollRepo->isFemale($this->employeeId, $this->sheetNo);
                break;
            /*
             * IS_MARRIED
             */
            case PayrollGenerator::VARIABLES[12]:
                $processedValue = $this->payrollRepo->isMarried($this->employeeId, $this->sheetNo);
                break;
            /*
             * IS_PERMANENT
             */
            case PayrollGenerator::VARIABLES[13]:
                $processedValue = $this->payrollRepo->isPermanent($this->employeeId, $this->sheetNo);
                break;
            /*
             * IS_PROBATION
             */
            case PayrollGenerator::VARIABLES[14]:
                $processedValue = $this->payrollRepo->isProbation($this->employeeId, $this->monthId);
                break;
            /*
             * IS_CONTRACT
             */
            case PayrollGenerator::VARIABLES[15]:
                $processedValue = $this->payrollRepo->isContract($this->employeeId, $this->monthId);
                break;
            /*
             * IS_TEMPORARY
             */
            case PayrollGenerator::VARIABLES[16]:
                $processedValue = $this->payrollRepo->isTemporary($this->employeeId, $this->monthId);
                break;
            /*
             * TOTAL_DAYS_TO_PAY
             */
            case PayrollGenerator::VARIABLES[17]:
                $processedValue = $this->payrollRepo->getWorkedDays($this->employeeId, $this->sheetNo);
                break;
            /*
             * BRANCH_ALLOWANCE
             */
            case PayrollGenerator::VARIABLES[18]:
                $processedValue = $this->payrollRepo->getBranchAllowance($this->employeeId);
                break;
             /*
             * MONTH
             */
            case PayrollGenerator::VARIABLES[19]:
                $processedValue = $this->payrollRepo->getMonthNo($this->monthId);
                break;
            
                 break;
            /*
             * BRANCH_ID
             */
            case PayrollGenerator::VARIABLES[20]:
                $processedValue = $this->payrollRepo->getBranch($this->employeeId);
                break;
            /*
             * Cafe Meal Previous
             */
            case PayrollGenerator::VARIABLES[21]:
                $processedValue = $this->payrollRepo->getCafeMealPrevious($this->employeeId,$this->monthId);
                break;
            /*
             * cafe Meal Current
             */
            case PayrollGenerator::VARIABLES[22]:
                $processedValue = $this->payrollRepo->getCafeMealCurrent($this->employeeId,$this->monthId);
                break;
            /*
             * PAYROLL_EMPLOYEE_TYPE
             */
            case PayrollGenerator::VARIABLES[23]:
                $processedValue = $this->payrollRepo->getPayEmpType($this->employeeId);
                break;
            /*
             * EMPLOYEE_SERVICE_ID
             */
            case PayrollGenerator::VARIABLES[24]:
                $processedValue = $this->payrollRepo->getEmployeeServiceId($this->employeeId,$this->sheetNo);
                break;
            /*
             * SALARY_PF
             */
            case PayrollGenerator::VARIABLES[25]:
                $processedValue = $this->payrollRepo->getserviceTypePf($this->employeeId,$this->sheetNo);
                break;
            /*
             * IS_DISABLE_PERSON
             */
            case PayrollGenerator::VARIABLES[26]:
                $processedValue = $this->payrollRepo->getDisablePersonFlag($this->employeeId);
                break;
            /*
             * PREVIOUS_MONTH_DAYS
             */
            case PayrollGenerator::VARIABLES[27]:
                $processedValue = $this->payrollRepo->getPreviousMonthDays($this->monthId);
                break;
                break;
            /*
             * BRANCH_ALLOWANCE_REBATE
             */
            case PayrollGenerator::VARIABLES[28]:
                $processedValue = $this->payrollRepo->getBranchAllowanceRebate($this->employeeId);
                break;
            /*
             * IS_REMOTE_BRANCH
             */
            case PayrollGenerator::VARIABLES[29]:
                $processedValue = $this->payrollRepo->getRemoteBranch($this->employeeId);
                break;
            /*
             * AGE
             */
            case PayrollGenerator::VARIABLES[30]:
                $processedValue = $this->payrollRepo->getAge($this->employeeId);
                break;

            /*
             * FUNCTIONAL_LEVEL_EDESC
             */
            case PayrollGenerator::VARIABLES[31]:
                $processedValue = $this->payrollRepo->getFunctionalLevel($this->employeeId);
                break;

             /*
             * MOTORCYCLE_LOAN
             */
            case PayrollGenerator::VARIABLES[32]:
                $processedValue = $this->payrollRepo->getMotorcycleLoan($this->employeeId);
                break;    

             /*
             * POSITION_ID
             */
            case PayrollGenerator::VARIABLES[33]:
                $processedValue = $this->payrollRepo->getPositionID($this->employeeId);
                break;    
            
            /*
             * ACTING FUNCTIONAL lEVEL ID
             */
            case PayrollGenerator::VARIABLES[34]:
                $processedValue = $this->payrollRepo->getActingFunctionalID($this->employeeId);
                break; 

            /*
             * GRADE FUNCTIONAL lEVEL EDESC
             */
            case PayrollGenerator::VARIABLES[35]:
                $processedValue = $this->payrollRepo->getGradeFunctionalLevel($this->employeeId);
                break;

            /*
             * EMPLOYEE TYPE CONTRACT
             */
            case PayrollGenerator::VARIABLES[36]:
                $processedValue = $this->payrollRepo->getEmployeeTypeContract($this->employeeId);
                break; 
                
            /*
             * ACTING POSITION ID
             */
            case PayrollGenerator::VARIABLES[37]:
                $processedValue = $this->payrollRepo->getActingPositionID($this->employeeId);
                break;   
                
            /*
             * TOTAL TRAVEL DAYS
             */
            case PayrollGenerator::VARIABLES[38]:
                $processedValue = $this->payrollRepo->getTotalTravelDays($this->employeeId, $this->monthId);
                break;      
            
            /*
             * TRAVEL DAY OFF
             */
            case PayrollGenerator::VARIABLES[39]:
                $processedValue = $this->payrollRepo->getTravelDayOff($this->employeeId, $this->monthId);
                break; 

            /*
             * LEAVE ENCASHED DAYS
             */
            case PayrollGenerator::VARIABLES[40]:
                $processedValue = $this->payrollRepo->getHouseLeaveEncashedDays($this->employeeId, $this->monthId);
                break;

            
            /*
             * WORK_ON_DAY_OFF
             */
            case PayrollGenerator::VARIABLES[41]:
                $processedValue = $this->payrollRepo->getWorkonDayOff($this->employeeId, $this->monthId);
                break;

            /*
             * WORK_ON_HOLIDAY
             */
            case PayrollGenerator::VARIABLES[42]:
                $processedValue = $this->payrollRepo->getWorkonHoliday($this->employeeId, $this->monthId);
                break;

            /*
             * KHAJA_DAYS
             */
            case PayrollGenerator::VARIABLES[43]:
                $processedValue = $this->payrollRepo->getKhajaDays($this->employeeId, $this->monthId);
                break;

            /*
             * OVERTIME_LUNCH_ALLOWANCE
             */
            case PayrollGenerator::VARIABLES[44]:
                $processedValue = $this->payrollRepo->getOvertimeLunchAllowance($this->employeeId, $this->monthId);
                break;

            /*
             * OVERTIME_NIGHT_ALLOWANCE
             */
            case PayrollGenerator::VARIABLES[45]:
                $processedValue = $this->payrollRepo->getOvertimeNightAllowance($this->employeeId, $this->monthId);
                break;
            
            /*
             * OVERTIME_LUNCH_ALLOWANCE
             */
            case PayrollGenerator::VARIABLES[46]:
                $processedValue = $this->payrollRepo->getOvertimeLockingAllowance($this->employeeId, $this->monthId);
                break;

            /*
             * OVERTIME_ANNUAL_TAXABLE_INCOME
             */
            case PayrollGenerator::VARIABLES[47]:
                $processedValue = $this->payrollRepo->getOvertimeAnnualTaxableIncome($this->employeeId, $this->monthId);
                break;

            /*
             * TOTAL_MONTHLY_TAXABLE_INCOME
             */
            case PayrollGenerator::VARIABLES[48]:
                $processedValue = $this->payrollRepo->getTotalMonthlyTaxableIncome($this->employeeId, $this->monthId);
                break;

            /*
             * LOAN_TEST
             */
            case PayrollGenerator::VARIABLES[49]:
                $processedValue = $this->payrollRepo->getLoanFinalTest($this->employeeId);
                break;


            default:
                break;
        }

        return $processedValue;
    }

}
