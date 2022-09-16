<?php

namespace SelfService\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;

class PayslipPreviousRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, $tableName);
    }

    public function getPeriodList($companyCode) {
        $sql = "SELECT PERIOD_DT_CODE AS mcode,
                  DT_EDESC            AS mname
                FROM HR_PERIOD_DETAIL
                WHERE COMPANY_CODE=:companyCode
                AND BRANCH_CODE='{$companyCode}.01'
                ORDER BY to_number(PERIOD_DT_CODE)";

        $boundedParameter = [];
        $boundedParameter = ['companyCode'] = $companyCode;
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getArrearsList($companyCode) {
        $sql = "SELECT ARREARS_CODE,
                  ARREARS_DESC
                FROM HR_ARREARS_SETUP
                WHERE COMPANY_CODE='{$companyCode}'
                AND BRANCH_CODE='{$companyCode}.01'";
        
        $boundedParameter = [];
        $boundedParameter = ['companyCode'] = $companyCode;
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getPayslipDetail($companyCode, $employeeCode, $periodDtCode, $salaryType) {
      $sql = "SELECT 'R000' PAY_CODE,
                  'Calc Basic' PAY_EDESC,
                  'Calc Basic' PAY_NDESC,
                  CALC_BASIC AMOUNT,
                  'A' PAY_TYPE_FLAG,
                  0 PRIORITY_INDEX
                FROM HR_SALARY_SHEET_DETAIL
                WHERE 1          = 1
                AND SHEET_NO                =(SELECT HSS.SHEET_NO
                    FROM   HR_SALARY_SHEET HSS, HR_EMPLOYEE_SETUP HES, HR_SALARY_SHEET_DETAIL SSD                    
                    WHERE HSS.PERIOD_DT_CODE =:periodDtCode
                    AND HSS.COMPANY_CODE     =:companyCode
                    AND HSS.BRANCH_CODE      ='{$companyCode}.01'
                    AND HES.EMPLOYEE_CODE    =:employeeCode
                    AND HSS.SALARY_TYPE =:salaryType
                    AND HSS.CONFIRM_FLAG ='Y'
					AND SSD.SHEET_NO= HSS.SHEET_NO
                    AND SSD.EMPLOYEE_CODE= HES.EMPLOYEE_CODE
                    AND SSD.COMPANY_CODE= HSS.COMPANY_CODE
                    AND SSD.COMPANY_CODE= HES.COMPANY_CODE
					)
                AND EMPLOYEE_CODE         =:employeeCode
                AND COMPANY_CODE =:companyCode
                AND BRANCH_CODE  ='{$companyCode}.01'
                AND DELETED_FLAG ='N'
                UNION ALL
                SELECT A.PAY_CODE,
                  B.PAY_EDESC,
                  B.PAY_NDESC,
                  A.AMOUNT,
                  A.PAY_TYPE_FLAG,
                  B.PRIORITY_INDEX
                FROM HR_SALARY_PAY_DETAIL A,
                  HR_PAY_SETUP B
                WHERE 1                     = 1
                AND SHEET_NO                =(SELECT HSS.SHEET_NO
                    FROM   HR_SALARY_SHEET HSS, HR_EMPLOYEE_SETUP HES, HR_SALARY_SHEET_DETAIL SSD   
                    WHERE HSS.PERIOD_DT_CODE =:periodDtCode
                    AND HSS.COMPANY_CODE     =:companyCode
                    AND HSS.BRANCH_CODE      ='{$companyCode}.01'
                    AND HES.EMPLOYEE_CODE    =:employeeCode
                    AND HSS.SALARY_TYPE =:salaryType
                    AND HSS.CONFIRM_FLAG ='Y'
					AND SSD.SHEET_NO= HSS.SHEET_NO
                    AND SSD.EMPLOYEE_CODE= HES.EMPLOYEE_CODE
                    AND SSD.COMPANY_CODE= HSS.COMPANY_CODE
                    AND SSD.COMPANY_CODE= HES.COMPANY_CODE)
                AND A.EMPLOYEE_CODE         =:employeeCode
                AND A.COMPANY_CODE          =:companyCode
                AND A.BRANCH_CODE           ='{$companyCode}.01'
                AND A.DELETED_FLAG          ='N'
                AND A.PAY_CODE              =B.PAY_CODE
                AND A.COMPANY_CODE          =B.COMPANY_CODE
                AND A.BRANCH_CODE           = B.BRANCH_CODE
                AND A.PAY_TYPE_FLAG        IN ('A','D')
                AND B.INVISIBLE_ON_PAY_SLIP = 'N'
                ORDER BY PRIORITY_INDEX";
        
        $boundedParameter = [];
        $boundedParameter = ['companyCode'] = $companyCode;
        $boundedParameter = ['periodDtCode'] = $periodDtCode;
        $boundedParameter = ['employeeCode'] = $companyCode;
        $boundedParameter = ['salaryType'] = $salaryType;
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getSalarySheetDetail($companyCode, $employeeCode, $periodDtCode, $salaryType) {
       $sql = "SELECT HSSD.*,
                  E.EMPLOYEE_EDESC,
                  C.COMPANY_EDESC,
                  E.PF_NUMBER,
                  E.CIT_NUMBER,
                  E.PAN_NO,
                  (HSSD.GROSS_AMOUNT-HSSD.NET_AMOUNT) AS DEDUCTION_AMOUNT
                FROM HR_SALARY_SHEET_DETAIL HSSD
                JOIN HR_EMPLOYEE_SETUP E
                ON (HSSD.EMPLOYEE_CODE=E.EMPLOYEE_CODE)
                JOIN COMPANY_SETUP C
                ON(E.COMPANY_CODE   =C.COMPANY_CODE)
                WHERE HSSD.SHEET_NO =
                  (SELECT HSS.SHEET_NO
                  FROM HR_SALARY_SHEET HSS, HR_EMPLOYEE_SETUP HES, HR_SALARY_SHEET_DETAIL SSD
                   WHERE HSS.PERIOD_DT_CODE =:periodDtCode
                    AND HSS.COMPANY_CODE     =:companyCode
                    AND HSS.BRANCH_CODE      ='{$companyCode}.01'
                    AND HES.EMPLOYEE_CODE    =:employeeCode
                    AND HSS.SALARY_TYPE =:salaryType
					AND SSD.SHEET_NO= HSS.SHEET_NO
                    AND SSD.EMPLOYEE_CODE= HES.EMPLOYEE_CODE
                    AND SSD.COMPANY_CODE= HSS.COMPANY_CODE
                    AND SSD.COMPANY_CODE= HES.COMPANY_CODE
                  )
                AND HSSD.EMPLOYEE_CODE=:employeeCode 
                AND E.COMPANY_CODE=:companyCode";
        
        $boundedParameter = [];
        $boundedParameter = ['companyCode'] = $companyCode;
        $boundedParameter = ['periodDtCode'] = $periodDtCode;
        $boundedParameter = ['employeeCode'] = $companyCode;
        $boundedParameter = ['salaryType'] = $salaryType;
        return $this->rawQuery($sql, $boundedParameter);
    }

}
