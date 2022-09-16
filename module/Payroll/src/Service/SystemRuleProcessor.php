<?php

namespace Payroll\Service;

use Application\Model\Months;
use Application\Repository\MonthRepository;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetDetailRepo;
use Setup\Repository\EmployeeRepository;

class SystemRuleProcessor {

    private $adapter;
    private $employeeId;
    private $employeeRepo;
    private $ruleDetailList;
    private $monthId;
    private $month;
    private $multiplicationFactor;
    private $prevSummedSSD;
    private $ruleId;
    private $ssdRepo;

    public function __construct($adapter, $employeeId, $ruleDetailList = null, int $monthId = null, int $ruleId = null) {
        $this->adapter = $adapter;
        $this->employeeId = $employeeId;
        $this->employeeRepo = new EmployeeRepository($adapter);
        $monthRepo = new MonthRepository($adapter);
        $this->ruleDetailList = $ruleDetailList;
        $this->monthId = $monthId;
        $this->month = new Months();
        $this->month->exchangeArrayFromDB((array) $monthRepo->fetchByMonthId($monthId));
        $this->multiplicationFactor = 12 - $this->month->fiscalYearMonthNo;

        $ssdRepo = new SalarySheetDetailRepo($adapter);
        $this->ssdRepo = $ssdRepo;
//        $prevSummedRaw = $ssdRepo->fetchPrevSumPayValue($employeeId, $this->month->fiscalYearId, $this->month->fiscalYearMonthNo);
//        $this->prevSummedSSD = $this->listValueToKV($prevSummedRaw, "PAY_ID", "PREV_SUM_VAL");
        $this->prevSummedSSD = [];
        $this->ruleId = $ruleId;
    }

    private function listValueToKV($list, $key, $value) {
        $output = [];
        foreach ($list as $item) {
            $output[$item[$key]] = $item[$value];
        }
        return $output;
    }

    public function processSystemRule($systemRule) {
        $processedValue = "";
        switch ($systemRule) {
//            "TOTAL_ANNUAL_AMOUNT"
            case PayrollGenerator::SYSTEM_RULE[0]:
                $addition = 0;
                $deductionWithLimit = 0;
                $deduction = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
                    if (in_array($ruleDetail['rule']['PAY_TYPE_FLAG'], ['A', 'D']) && ($ruleDetail['rule']['INCLUDE_IN_TAX'] == 'Y')) {
                        $past = 0;
                        if ($ruleDetail['rule']['INCLUDE_PAST_VALUE'] === 'Y') {
                            $past = (($this->multiplicationFactor == 11) ? 0 : (isset($this->prevSummedSSD[$ruleDetail['rule']['PAY_ID']]) ? $this->prevSummedSSD[$ruleDetail['rule']['PAY_ID']] : 0) );
                        }
                        $future = 0;
                        if ($ruleDetail['rule']['INCLUDE_FUTURE_VALUE'] === 'Y') {
                            $future = $ruleDetail['ruleValue'] * $this->multiplicationFactor;
                        }
                        $ruleValue = $past + $ruleDetail['ruleValue'] + $future;
                        switch ($ruleDetail['rule']['PAY_TYPE_FLAG']) {
                            case "A":
                                $addition = $addition + $ruleValue;
                                break;
                            case "D":
                                if ($ruleDetail['rule']['DEDUCTION_LIMIT_FLAG'] == 'Y') {
                                    $deductionWithLimit = $deductionWithLimit + $ruleValue;
                                } else {
                                    $deduction = $deduction + $ruleValue;
                                }
                                break;
                        }
                    }
                }
                $totalIncomeBy3 = $addition / 3;
                $eligibleDeduction = 300000;
                $minDeduction = min([$totalIncomeBy3, $deductionWithLimit, $eligibleDeduction]);
                $calculatedValue = $addition - ($minDeduction + $deduction);
                $processedValue = $calculatedValue;
                break;
//       TOTAL_AMOUNT
            case PayrollGenerator::SYSTEM_RULE[1]:
                $calculatedValue = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
                    if (in_array($ruleDetail['rule']['PAY_TYPE_FLAG'], ['A', 'D']) && ($ruleDetail['rule']['INCLUDE_IN_SALARY'] === 'Y')) {
                        $ruleValue = $ruleDetail['ruleValue'];
                    }
                    switch ($ruleDetail['rule']['PAY_TYPE_FLAG']) {
                        case "A":
                            $calculatedValue = $calculatedValue + $ruleValue;
                            break;
                        case "D":
                            $calculatedValue = $calculatedValue - $ruleValue;
                            break;
                    }
                }
                $processedValue = $calculatedValue;
                break;
//            SELF_PREV_TOTAL
            case PayrollGenerator::SYSTEM_RULE[2]:
                $processedValue = isset($this->prevSummedSSD[$this->ruleId]) ? $this->prevSummedSSD[$this->ruleId] : 0;
                break;
            case PayrollGenerator::SYSTEM_RULE[3]:
                $processedValue = $this->multiplicationFactor;
                break;
            //LOAN_AMT for soaltee loan variable
            case PayrollGenerator::SYSTEM_RULE[4]:
                $ruleRepo = new RulesRepository($this->adapter);
                $ruleDetails = $ruleRepo->fetchById($this->ruleId);
                $ruleFormula = $ruleDetails['FORMULA'];
                //$id=$this->getFistParamenters($systemRule, $ruleFormula, 9);
                $processedValue = $this->ssdRepo->fetchEmployeeLoanAmt($this->monthId, $this->employeeId, $this->ruleId);
                break;
            //LOAN_INT for soaltee loan variable
            case PayrollGenerator::SYSTEM_RULE[5]:
                $ruleRepo = new RulesRepository($this->adapter);
                $ruleDetails = $ruleRepo->fetchById($this->ruleId);
                $ruleFormula = $ruleDetails['FORMULA'];
               // $id=$this->getFistParamenters($systemRule, $ruleFormula, 9);
                $processedValue = $this->ssdRepo->fetchEmployeeLoanIntrestAmt($this->monthId, $this->employeeId, $this->ruleId);
                break;
            //PREVIOUS_TOTAL
            case PayrollGenerator::SYSTEM_RULE[6]:
                $ruleRepo = new RulesRepository($this->adapter);
                $ruleDetails = $ruleRepo->fetchById($this->ruleId);
                $ruleFormula = $ruleDetails['FORMULA'];
                $id=$this->getFistParamenters($systemRule, $ruleFormula, 9);
                $processedValue = $this->ssdRepo->fetchEmployeePreviousSum($this->monthId, $this->employeeId, $id);
                break;
            //PREVIOUS_MONTH_AMOUNT
            case PayrollGenerator::SYSTEM_RULE[7]:
                $ruleRepo = new RulesRepository($this->adapter);
                $ruleDetails = $ruleRepo->fetchById($this->ruleId);
                $ruleFormula = $ruleDetails['FORMULA'];
                $id=$this->getFistParamenters($systemRule, $ruleFormula, 9);
                $processedValue = $this->ssdRepo->fetchEmployeePreviousMonthAmount($this->monthId, $this->employeeId, $id);
                break;
            //EMPLOYEE_GRADE
            case PayrollGenerator::SYSTEM_RULE[8]:
                $ruleRepo = new RulesRepository($this->adapter);
                $ruleDetails = $ruleRepo->fetchById($this->ruleId);
                $ruleFormula = $ruleDetails['FORMULA'];
                $absentVariable=$this->getFistParamenters($systemRule, $ruleFormula, 9);
                $gradeDetails=$this->ssdRepo->fetchEmployeeGrade($this->monthId, $this->employeeId);
                
                if (empty($gradeDetails)) {
                    $gradeDetails['OPENING_GRADE'] = 0;
                    $gradeDetails['ADDITIONAL_GRADE'] = 0;
                    $gradeDetails['GRADE_VALUE'] = 0;
                    $gradeDetails['CUR_GRADE'] = 0;
                    $gradeDetails['NEW_GRADE'] = 0;
                    $gradeDetails['MONTH_DAYS'] = 0;
                    $gradeDetails['CUR_GRADE_DAYS'] = 0;
                    $gradeDetails['NEW_GRADE_DAYS'] = 0;
                }
                $processedValue=""; 
//                    $processedValue="eval( 'return "; 
                if ($gradeDetails['NEW_GRADE'] == 0 || $gradeDetails['CUR_GRADE'] == $gradeDetails['NEW_GRADE']) {
                    $processedValue .= "( (" . $gradeDetails['CUR_GRADE'] . '/' . $gradeDetails['MONTH_DAYS'] . ' ) *(' . $gradeDetails['CUR_GRADE_DAYS'] . '- ' . $absentVariable . " )  )";
                } else {
//                    $processedValue .= "( (" . $gradeDetails['CUR_GRADE'] . '/' . $gradeDetails['MONTH_DAYS'] . ' ) *(' . $gradeDetails['CUR_GRADE_DAYS'] . '-' . $absentVariable . ")  )";
//                    $processedValue .= " + ( (" . $gradeDetails['NEW_GRADE'] . '/' . $gradeDetails['MONTH_DAYS'] . ' ) *(' . $gradeDetails['NEW_GRADE_DAYS'] . '-' . $absentVariable . ")  )";
                    
                   
                    $processedValue .= "( (" . $gradeDetails['CUR_GRADE'] . '/' . $gradeDetails['MONTH_DAYS'] . ' ) *( ( '.$absentVariable.' <= '.$gradeDetails['NEW_GRADE_DAYS'] .')?'.$gradeDetails['CUR_GRADE_DAYS'].':(' . $gradeDetails['CUR_GRADE_DAYS'] . '+ ' . $gradeDetails['NEW_GRADE_DAYS'] .' - '.$absentVariable." )  )  )";
                    $processedValue .= " + ( (" . $gradeDetails['NEW_GRADE'] . '/' . $gradeDetails['MONTH_DAYS'] . ' ) *( ( '.$absentVariable.' <= '.$gradeDetails['NEW_GRADE_DAYS'] .')?( '.$gradeDetails['NEW_GRADE_DAYS'].' - '.$absentVariable." ):(0) )  )";
                }
//                    $processedValue.=";')"; 
                break;
				//       TOTAL_ADD
            case PayrollGenerator::SYSTEM_RULE[9]:
                $calculatedValue = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
					$ruleValue=0;
                    if (in_array($ruleDetail['rule']['PAY_TYPE_FLAG'], ['A']) && ($ruleDetail['rule']['INCLUDE_IN_SALARY'] === 'Y')) {
                        $ruleValue = $ruleDetail['ruleValue'];
                    }
                    $calculatedValue = $calculatedValue + $ruleValue;
                }
                $processedValue = $calculatedValue;
                break;
				//       TOTAL_DED
            case PayrollGenerator::SYSTEM_RULE[10]:
                $calculatedValue = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
					$ruleValue=0;
                    if (in_array($ruleDetail['rule']['PAY_TYPE_FLAG'], ['D']) && ($ruleDetail['rule']['INCLUDE_IN_SALARY'] === 'Y')) {
                        $ruleValue = $ruleDetail['ruleValue'];
                    }
                    $calculatedValue = $calculatedValue + $ruleValue;
                }
                $processedValue = $calculatedValue;
                break;
				//       GRATUITY_PER
            case PayrollGenerator::SYSTEM_RULE[11]:
                $processedValue=$this->ssdRepo->fetchEmployeeGratuityPercentage($this->monthId, $this->employeeId);
                break;
        }
        return $processedValue;
    }

    public function getTaxValue($ruleDetail) {
        if (in_array($ruleDetail['rule']['PAY_TYPE_FLAG'], ['A', 'D', 'T']) && ($ruleDetail['rule']['INCLUDE_IN_TAX'] === 'Y')) {
            $past = 0;
            if ($ruleDetail['rule']['INCLUDE_PAST_VALUE'] === 'Y') {
                $past = (($this->multiplicationFactor == 11) ? 0 : (isset($this->prevSummedSSD[$ruleDetail['rule']['PAY_ID']]) ? $this->prevSummedSSD[$ruleDetail['rule']['PAY_ID']] : 0));
            }
            $future = 0;
            if ($ruleDetail['rule']['INCLUDE_FUTURE_VALUE'] === 'Y') {
                $future = $ruleDetail['ruleValue'] * $this->multiplicationFactor;
            }
            return $past + $ruleDetail['ruleValue'] + $future;
        }
        return null;
    }

    public function getFistParamenters($key, $stringVal, $extraParam) {
        $endPos = strpos($stringVal, $key);
        $stringVal = substr($stringVal, $endPos + $extraParam);
        return trim($this->get_string_between($stringVal, "PARS", "PARE"));
    }

    function get_string_between($string, $start, $end) {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0)
            return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

}
