<?php
namespace Application\Helper;

use Application\Helper\Helper;
use DateTime;
use Setup\Repository\AdvanceRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\LoanRepository;
use Setup\Repository\LoanRestrictionRepository;
use Zend\Db\Adapter\AdapterInterface;

class LoanAdvanceHelper{
    
    public static function getLoanList(AdapterInterface $adapter,$employeeId){
        $employeeId = $employeeId;
        $loanRepo = new LoanRepository($adapter);
        $loanRestrictionRepo = new LoanRestrictionRepository($adapter);
        $employeeRepo = new EmployeeRepository($adapter);
        
        $employeeDetail = $employeeRepo->fetchById($employeeId);
        
        $position = $employeeDetail['POSITION_ID'];
        $serviceType = $employeeDetail['SERVICE_TYPE_ID'];
        $designation = $employeeDetail['DESIGNATION_ID'];
        
        $salary = (int)$employeeDetail['SALARY'];
        if($employeeDetail['JOIN_DATE']==null){
            $joinDate = new DateTime();
        }else{
            $joinDate = DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $employeeDetail['JOIN_DATE']);
        }
        
        $currentDate = new DateTime();
        
        $different = date_diff($joinDate,$currentDate);
        $yr = $different->format('%y');
        $mn = $different->format('%m');
        $days = $different->format('%d');
        $mnPercentage = 0.083;
        
        $mnInPer = $mn * $mnPercentage;
        $totalYr =(double)($yr+$mnInPer);
        $actTotalYr = round($totalYr,2);

        $loanList = $loanRepo->fetchActiveRecord();
        
        $loanResultList = [];
        $loanResultList[null] = "   - - - Please Select - - -";
        foreach($loanList as $loanRow){
            $loanId = $loanRow['LOAN_ID'];
            $restrictionDtl = $loanRestrictionRepo->getByLoanId($loanId);
            $positionList = explode(",",$restrictionDtl['position']);
            $serviceTypeList =  explode(",",$restrictionDtl['serviceType']);
            $designationList = explode(",",$restrictionDtl['designation']);
            $salaryRange =  explode(",",$restrictionDtl['salaryRange']);
            
            if(count($salaryRange)>1){
                $salaryFrom = (int)$salaryRange[0];
            }else{
                $salaryFrom="";
            }
            if(count($salaryRange)==2){
               $salaryTo = (int)$salaryRange[1];
            }
            if(count($salaryRange)==1 ||  count($salaryRange)!=2){
                $salaryTo = "";
            }
            
            $workingPeriod =  explode(",",$restrictionDtl['workingPeriod']);
            if(count($workingPeriod)>1){
                $workingPeriodFrom = (int)$workingPeriod[0];
            }else{
                $workingPeriodFrom="";
            }
            if(count($workingPeriod)==2){
               $workingPeriodTo = (int)$workingPeriod[1];
            }
            if(count($workingPeriod)==1 ||  count($workingPeriod)!=2){
                $workingPeriodTo = "";
            }
            if(!$loanRow['ELIGIBLE_PERIOD']){
                $loanRow['ELIGIBLE_PERIOD'] = 0;
            }
            if($actTotalYr>=$loanRow['ELIGIBLE_PERIOD'] && !in_array($position,$positionList) && !in_array($serviceType, $serviceTypeList) && !in_array($designation,$designationList)  && ($salary>=($salaryFrom?$salaryFrom:$salary) && $salary<=($salaryTo?$salaryTo:$salary)) && ($actTotalYr>=($workingPeriodFrom?$workingPeriodFrom:$actTotalYr) && $actTotalYr<=($workingPeriodTo?$workingPeriodTo:$actTotalYr))){
                $loanResultList[$loanRow['LOAN_ID']] = $loanRow['LOAN_NAME'];
            }
        }
        return $loanResultList;
    }
    
    public static function getAdvanceList(AdapterInterface $adapter,$employeeId){
        $employeeId = $employeeId;
        $employeeRepo = new EmployeeRepository($adapter);
        
        $advanceRepo = new AdvanceRepository($adapter);
        $employeeDetail = $employeeRepo->fetchById($employeeId);
        $salary = $employeeDetail['SALARY'];
        
        $advanceResult = $advanceRepo->fetchActiveRecord();
        $advanceList = [];
        
        foreach($advanceResult as $advanceRow){
            $minSalaryAmt = $advanceRow['MIN_SALARY_AMT'];
            $maxSalaryAmt = $advanceRow['MAX_SALARY_AMT'];
            
            if($salary>=$minSalaryAmt && $salary<=$maxSalaryAmt){
                $advanceList[$advanceRow['ADVANCE_ID']] = $advanceRow['ADVANCE_NAME'];
            }
        }
        return $advanceList;
    }
}