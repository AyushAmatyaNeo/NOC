<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\HrisRepository;
use Exception;
use Zend\Db\Adapter\AdapterInterface;

class PayrollRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, $tableName);
    }

    public function fetchBasicSalary($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "
                SELECT IFNULL(SALARY,0) AS SALARY
                FROM HRIS_SALARY_SHEET_EMP_DETAIL
                WHERE EMPLOYEE_ID=?
                AND SHEET_NO = ?
                ";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['SALARY'];
    }

    public function getMonthDays($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['sheetNo'] = $sheetNo;
        $boundedParameter['employeeId'] = $employeeId;
        
        $sql = "
            SELECT TOTAL_DAYS AS MONTH_DAYS
            FROM HRIS_SALARY_SHEET_EMP_DETAIL
            WHERE SHEET_NO=? AND EMPLOYEE_ID=?
                ";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['MONTH_DAYS'];
    }

    public function getPresentDays($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT PRESENT AS PRESENT_DAYS
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['PRESENT_DAYS'];
    }

    public function getAbsentDays($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT ABSENT AS ABSENT_DAYS
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['ABSENT_DAYS'];
    }

    public function getPaidLeaves($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT PAID_LEAVE AS PAID_LEAVE
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['PAID_LEAVE'];
    }

    public function getUnpaidLeaves($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT UNPAID_LEAVE AS UNPAID_LEAVE
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['UNPAID_LEAVE'];
    }

    public function getDayoffs($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT DAYOFF AS DAYOFF
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['DAYOFF'];
    }

    public function getHolidays($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT HOLIDAY
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['HOLIDAY'];
    }

    public function getDaysFromJoinDate($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT DAYS_BETWEEN((JOIN_DATE),(START_DATE))+1  AS DAYS_FROM_JOIN_DATE
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['DAYS_FROM_JOIN_DATE'];
    }

    public function getDaysFromPermanentDate($employeeId, $monthId) {
        $boundedParameter = [];
          $boundedParameter['monthId'] = $monthId;
        $boundedParameter['employeeId'] = $employeeId;
      
       /* $sql = "
                SELECT DAYS_BETWEEN ((M.FROM_DATE),(PERMANENT_DATE)) AS DAYS_FROM_PERMANENT_DATE
                FROM HRIS_EMPLOYEES E,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID=?
                  ) M WHERE E.EMPLOYEE_ID=?
                "; */

          $sql = "
                SELECT DAYS_BETWEEN ((PERMANENT_DATE),(M.FROM_DATE)) AS DAYS_FROM_PERMANENT_DATE
                FROM HRIS_EMPLOYEES E,
                  (SELECT FROM_DATE,TO_DATE FROM HRIS_MONTH_CODE WHERE MONTH_ID=?
                  ) M WHERE E.EMPLOYEE_ID=?
                "; 
        $rawResult = $this->rawQuery($sql, $boundedParameter);
        return $rawResult[0]['DAYS_FROM_PERMANENT_DATE'];
    }

    public function isMale($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT (CASE WHEN GENDER_CODE = 'M' THEN 1 ELSE 0 END) AS IS_MALE
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['IS_MALE'];
    }

    public function isFemale($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT (CASE WHEN GENDER_CODE = 'F' THEN 1 ELSE 0 END) AS IS_FEMALE
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['IS_FEMALE'];
    }

    public function isMarried($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT (CASE WHEN MARITAL_STATUS = 'M' THEN 1 ELSE 0 END) AS IS_MARRIED
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['IS_MARRIED'];
    }

    public function isPermanent($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT (
                  CASE
                    WHEN (PERMANENT_FLAG ='Y'
                    AND ( PERMANENT_DATE IS NULL OR PERMANENT_DATE <= START_DATE))
                    THEN 1
                    ELSE 0
                  END) AS IS_PERMANENT
                FROM HRIS_SALARY_SHEET_EMP_DETAIL
                WHERE EMPLOYEE_ID = ?
                AND SHEET_NO      = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['IS_PERMANENT'];
    }

    public function isProbation($employeeId, $monthId) {
        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $boundedParameter['employeeId'] = $employeeId;
        
        $sql = "
                SELECT (
                  CASE
                    WHEN TO_SERVICE_TYPE_ID =2
                    THEN 1
                    ELSE 0
                  END) AS IS_PERMANENT
                FROM
                  (SELECT *
                  FROM
                    (SELECT JH.*
                    FROM HRIS_JOB_HISTORY JH,
                      (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = ?
                      ) M
                    WHERE JH.EMPLOYEE_ID = ?
                    AND JH.START_DATE   <= M.FROM_DATE
                    ORDER BY JH.START_DATE DESC
                    )
                  LIMIT 1
                  )           
                ";
        $rawResult = $this->rawQuery($sql, $boundedParameter);
        $result = $rawResult[0];
        if ($result == null) {
            return 0;
        }
        return $result['IS_PERMANENT'];
    }

    public function isContract($employeeId, $monthId) {
        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $boundedParameter['employeeId'] = $employeeId;
        
        $sql = "
                SELECT (
                  CASE
                    WHEN TYPE ='CONTRACT'
                    THEN 1
                    ELSE 0
                  END) AS IS_PERMANENT
                FROM
                  (SELECT *
                  FROM
                    (SELECT JH.*,ST.TYPE
                    FROM HRIS_JOB_HISTORY JH
                     left join Hris_Service_Types ST ON (ST.SERVICE_TYPE_ID=JH.TO_SERVICE_TYPE_ID),
                      (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = ?
                      ) M
                    WHERE JH.EMPLOYEE_ID = ?
                    AND JH.START_DATE   <= M.FROM_DATE
                    ORDER BY JH.START_DATE DESC
                    )
                  LIMIT 1
                  )           
                ";
        $rawResult = $this->rawQuery($sql, $boundedParameter);
        $result = $rawResult[0];
        if ($result == null) {
            return 0;
        }
        return $result['IS_PERMANENT'];
    }

    public function isTemporary($employeeId, $monthId) {
        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $boundedParameter['employeeId'] = $employeeId;
        
        $sql = "
                SELECT (
                  CASE
                    WHEN TO_SERVICE_TYPE_ID =4
                    THEN 1
                    ELSE 0
                  END) AS IS_PERMANENT
                FROM
                  (SELECT *
                  FROM
                    (SELECT JH.*
                    FROM HRIS_JOB_HISTORY JH,
                      (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID = ?
                      ) M
                    WHERE JH.EMPLOYEE_ID = ?
                    AND JH.START_DATE   <= M.FROM_DATE
                    ORDER BY JH.START_DATE DESC
                    )
                 LIMIT 1
                  )           
                ";
        $rawResult = $this->rawQuery($sql, $boundedParameter);
        $result = $rawResult[0];
        if ($result == null) {
            return 0;
        }
        return $result['IS_PERMANENT'];
    }

    public function getWorkedDays($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "SELECT PRESENT+DAYOFF+HOLIDAY+PAID_LEAVE+TRAVEL+TRAINING AS WORKED_DAYS
                FROM HRIS_SALARY_SHEET_EMP_DETAIL WHERE EMPLOYEE_ID = ? AND SHEET_NO = ?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['WORKED_DAYS'];
    }

    public function fetchEmployeeList() {
        $sql = "
                SELECT E.EMPLOYEE_ID, E.GROUP_ID,
                  E.EMPLOYEE_CODE || '-' || CONCAT(CONCAT(CONCAT(INITCAP(TRIM(E.FIRST_NAME)),' '),
                  CASE
                    WHEN E.MIDDLE_NAME IS NOT NULL
                    THEN CONCAT(INITCAP(TRIM(E.MIDDLE_NAME)), ' ')
                    ELSE ''
                  END ),INITCAP(TRIM(E.LAST_NAME))) AS FULL_NAME
                FROM HRIS_EMPLOYEES E
                WHERE E.JOIN_DATE <= CURRENT_DATE
                AND E.RETIRED_FLAG ='N'
                AND IS_ADMIN       ='N'
                AND STATUS         ='E'
                ";
        $employeeListRaw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($employeeListRaw);
    }

    public function getBranchAllowance($employeeId) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $sql = "SELECT ALLOWANCE FROM HRIS_BRANCHES WHERE 
                BRANCH_ID=(SELECT  BRANCH_ID FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID=?)";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['ALLOWANCE'];
    }

    public function getMonthNo($monthId) {
        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $sql = "select FISCAL_YEAR_MONTH_NO from hris_month_code where month_id=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('Result not found.');
        }
        return $resultList[0]['FISCAL_YEAR_ID'];
    }

    
     public function getBranch($employeeId) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $sql = "SELECT BRANCH_ID FROM HRIS_EMPLOYEES WHERE  EMPLOYEE_ID=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['BRANCH_ID'];
    }

    public function getCafeMealPrevious($employeeId, $monthId){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['monthId'] = $monthId;
        $sql = "select case
when sum(total_amount) is not null
then sum(total_amount)
else 0 END as AMT
 from (SELECT
    hcms.menu_name AS menu_name,
    e.employee_id AS employee_id,
    e.full_name AS full_name,
    SUM(held.quantity) AS quantity,
    SUM(held.total_amount) AS total_amount
FROM
    hris_cafeteria_log_detail held
    JOIN hris_employees e ON (
        e.employee_id = held.employee_id
    )
    JOIN hris_cafeteria_menu_setup hcms ON (
        held.menu_code = hcms.menu_id
    )
    left join (select * from 
(
select to_char( add_months (from_date,-1),'DD-Mon-YY') as from_date
, to_char( add_months (to_date,-1),'DD-Mon-YY') as to_date
from hris_month_code where month_id=:monthId
)) mc on (1=1)
WHERE
held.log_date BETWEEN mc.from_date AND mc.to_date and 
e.employee_id=:employeeId
GROUP BY
    hcms.menu_name,
    e.employee_id,
    e.full_name)";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['AMT'];
    }
    
    public function getCafeMealCurrent($employeeId, $monthId){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['monthId'] = $monthId;
        $sql = "select case
when sum(total_amount) is not null
then sum(total_amount)
else 0 END as AMT
 from (SELECT
    hcms.menu_name AS menu_name,
    e.employee_id AS employee_id,
    e.full_name AS full_name,
    SUM(held.quantity) AS quantity,
    SUM(held.total_amount) AS total_amount
FROM
    hris_cafeteria_log_detail held
    JOIN hris_employees e ON (
        e.employee_id = held.employee_id
    )
    JOIN hris_cafeteria_menu_setup hcms ON (
        held.menu_code = hcms.menu_id
    )
    left join hris_month_code mc on (month_id=:monthId)
WHERE
held.log_date BETWEEN mc.from_date AND mc.to_date and 
e.employee_id=:employeeId
GROUP BY
    hcms.menu_name,
    e.employee_id,
    e.full_name)";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['AMT'];
    }
    
    
    public function getPayEmpType($employeeId){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $sql = "SELECT PAY_EMP_TYPE FROM HRIS_EMPLOYEES WHERE  EMPLOYEE_ID=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['PAY_EMP_TYPE'];
    }
    
     public function getEmployeeServiceId($employeeId, $sheetNo){
        $boundedParameter = [];
         $boundedParameter['sheetNo'] = $sheetNo;
        $boundedParameter['employeeId'] = $employeeId;
       
        $sql = "SELECT SERVICE_TYPE_ID AS SERVICE_TYPE_ID
            FROM HRIS_SALARY_SHEET_EMP_DETAIL
            WHERE SHEET_NO= ? AND EMPLOYEE_ID=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['SERVICE_TYPE_ID'];
    }
    
    public function getserviceTypePf($employeeId, $sheetNo){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
//        $boundedParameter['sheetNo'] = $sheetNo;
        $sql = "select CASE WHEN 
E.SERVICE_TYPE_ID IS NOT NULL 
THEN S.PF_ELIGIBLE
ELSE 
'N'
END AS PF_ELIGIBLE
FROM hris_employees E
LEFT JOIN HRIS_SERVICE_TYPES S ON (E.SERVICE_TYPE_ID=S.SERVICE_TYPE_ID)
WHERE E.EMPLOYEE_ID=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['PF_ELIGIBLE'];
    }
    
    public function getDisablePersonFlag($employeeId){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $sql = "SELECT 
CASE WHEN
DISABLED_FLAG ='Y'
THEN
1
ELSE
0
END AS DISABLED_FLAG FROM HRIS_EMPLOYEES WHERE  EMPLOYEE_ID=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['DISABLED_FLAG'];
    }
    
    public function getPreviousMonthDays($monthId){
        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $sql = "SELECT 
    DAYS_BETWEEN(FROM_DATE,TO_DATE) +1 as PRE_MONTH_DAYS
        FROM HRIS_MONTH_CODE  where MONTH_ID=(?-1)";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['PRE_MONTH_DAYS'];
        
    }
    
    public function getBranchAllowanceRebate($employeeId){
         $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $sql = "SELECT IFNULL(ALLOWANCE_REBATE,0) AS ALLOWANCE_REBATE FROM HRIS_BRANCHES WHERE 
                BRANCH_ID=(SELECT  BRANCH_ID FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID=?)";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['ALLOWANCE_REBATE'];
    }
    
    public function getRemoteBranch($employeeId){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $sql = "SELECT IS_REMOTE FROM HRIS_BRANCHES WHERE 
                BRANCH_ID=(SELECT  BRANCH_ID FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID=?)";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['IS_REMOTE'];
    }

    public function getAge($employeeId){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $sql = "SELECT 
                TO_CHAR(BIRTH_DATE, 'yyyy-MON-dd')           AS BIRTH_DATE,
                (months_between(BIRTH_DATE,CURRENT_DATE)/12) AS AGE
                FROM HRIS_EMPLOYEES 
                WHERE EMPLOYEE_ID=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        return $resultList[0]['AGE'];
    }

    public function getFunctionalLevel($employeeId){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $acting_functional_level = 0;

        $sql = "SELECT FUNCTIONAL_LEVEL_ID, FUNCTIONAL_LEVEL_EDESC AS FUNCTIONAL_LEVEL FROM HRIS_FUNCTIONAL_LEVELS where FUNCTIONAL_LEVEL_ID = (SELECT FUNCTIONAL_LEVEL_ID FROM HRIS_EMPLOYEES where EMPLOYEE_ID = ? )";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }
        $acting_functional_level = $this->getActingFunctionalID($employeeId);

        if($acting_functional_level >= 1 ) {
            return $acting_functional_level;
        } else {
            if($resultList[0]['FUNCTIONAL_LEVEL'] >= 1) {
                return $resultList[0]['FUNCTIONAL_LEVEL'];
            } else {
                //return 0;
                if(trim($resultList[0]['FUNCTIONAL_LEVEL']) == 'I'){
                    return 101;
                } else if (trim($resultList[0]['FUNCTIONAL_LEVEL']) == 'II') {
                    return 102;
                } else if (trim($resultList[0]['FUNCTIONAL_LEVEL']) == 'III') {
                    return 103;
                } else if (trim($resultList[0]['FUNCTIONAL_LEVEL']) == 'IV') {
                    return 104;
                } else if (trim($resultList[0]['FUNCTIONAL_LEVEL']) == 'V') {
                    return 105;
                } else {
                    return 0;
                }
                
            }
        }       
    }

    public function getGradeFunctionalLevel($employeeId){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;

        $sql = "SELECT FUNCTIONAL_LEVEL_ID, FUNCTIONAL_LEVEL_EDESC AS GRADE_FUNCTIONAL_LEVEL FROM HRIS_FUNCTIONAL_LEVELS where FUNCTIONAL_LEVEL_ID = (SELECT FUNCTIONAL_LEVEL_ID FROM HRIS_EMPLOYEES where EMPLOYEE_ID = ? )";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }

        if($resultList[0]['GRADE_FUNCTIONAL_LEVEL'] >= 1) {
            return $resultList[0]['GRADE_FUNCTIONAL_LEVEL'];
        } else {
            //return 0;
            if(trim($resultList[0]['GRADE_FUNCTIONAL_LEVEL']) == 'I'){
                return 101;
            } else if (trim($resultList[0]['GRADE_FUNCTIONAL_LEVEL']) == 'II') {
                return 102;
            } else if (trim($resultList[0]['GRADE_FUNCTIONAL_LEVEL']) == 'III') {
                return 103;
            } else if (trim($resultList[0]['GRADE_FUNCTIONAL_LEVEL']) == 'IV') {
                return 104;
            } else if (trim($resultList[0]['GRADE_FUNCTIONAL_LEVEL']) == 'V') {
                return 105;
            } else {
                return 0;
            }            
        }
    }

    public function getPositionID($employeeId){ 
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;

        $sql = "SELECT POSITION_ID FROM HRIS_EMPLOYEES WHERE STATUS='E' AND EMPLOYEE_ID=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }

        if($resultList[0]['POSITION_ID'] >= 1) {
            return $resultList[0]['POSITION_ID'];
        } else {
            return 0;
        }
    }

    public function getActingPositionID($employeeId){ 
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;

        $sql = "SELECT ACTING_POSITION_ID FROM HRIS_EMPLOYEES WHERE STATUS='E' AND EMPLOYEE_ID=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }

        if($resultList[0]['ACTING_POSITION_ID'] >= 1) {
            return $resultList[0]['ACTING_POSITION_ID'];
        } else {
            return 0;
        }
    }

    public function getActingFunctionalID($employeeId){ 
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;

        //$sql = "SELECT ACTING_FUNCTIONAL_LEVEL_ID FROM HRIS_EMPLOYEES WHERE STATUS='E' AND EMPLOYEE_ID=?";
        $sql = "SELECT FUNCTIONAL_LEVEL_EDESC AS ACTING_FUNCTIONAL_LEVEL_ID FROM HRIS_FUNCTIONAL_LEVELS WHERE FUNCTIONAL_LEVEL_ID IN (SELECT ACTING_FUNCTIONAL_LEVEL_ID FROM HRIS_EMPLOYEES WHERE STATUS='E' AND EMPLOYEE_ID=?)";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        
        if(empty($resultList) == 1){
            return 0;
        }

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }

        if($resultList[0]['ACTING_FUNCTIONAL_LEVEL_ID'] >= 1) {
            return $resultList[0]['ACTING_FUNCTIONAL_LEVEL_ID'];
        } else {
            if(trim($resultList[0]['ACTING_FUNCTIONAL_LEVEL_ID']) == 'I'){
                return 101;
            } else if (trim($resultList[0]['ACTING_FUNCTIONAL_LEVEL_ID']) == 'II') {
                return 102;
            } else if (trim($resultList[0]['ACTING_FUNCTIONAL_LEVEL_ID']) == 'III') {
                return 103;
            } else if (trim($resultList[0]['ACTING_FUNCTIONAL_LEVEL_ID']) == 'IV') {
                return 104;
            } else if (trim($resultList[0]['ACTING_FUNCTIONAL_LEVEL_ID']) == 'V') {
                return 105;
            } else {
                return 0;
            }
        }

    }

    public function getEmployeeTypeContract($employeeId) {
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;

        $sql = "SELECT (CASE WHEN EMPLOYEE_TYPE = 'C' THEN 1 ELSE 0 END) AS EMPLOYEE_TYPE FROM HRIS_EMPLOYEES WHERE STATUS='E' AND EMPLOYEE_ID=?";
        $resultList = $this->rawQuery($sql, $boundedParameter);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }

        return $resultList[0]['EMPLOYEE_TYPE'];
    }

    public function getTotalTravelDays($employeeId, $monthId) {

        $sql = "select count(attendance_dt) as TOTAL_TRAVEL_DAYS from hris_attendance_detail where overall_status='TV' and employee_id= {$employeeId}
        and attendance_dt between (select from_date from hris_month_code where month_id = {$monthId}) 
        and (select to_date from hris_month_code where month_id = {$monthId})";

        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }

        if($resultList[0]['TOTAL_TRAVEL_DAYS'] >= 1) {
              return $resultList[0]['TOTAL_TRAVEL_DAYS'];
        } else {
            return 0;
        }
    }

    public function getTravelDayOff($employeeId, $monthId) {
        $sql = "select count(attendance_dt) as TRAVEL_DAY_OFF from hris_attendance_detail where overall_status='DO' and employee_id={$employeeId} 
        and attendance_dt between (select from_date from hris_month_code where month_id = {$monthId}) 
        and (select to_date from hris_month_code where month_id = {$monthId})";

        //$resultList = $this->rawQuery($sql, $boundedParameter);
        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }

        if($resultList[0]['TRAVEL_DAY_OFF'] >= 1) {
            return $resultList[0]['TRAVEL_DAY_OFF'];
        } else {
            return 0;
        }
    }

    public function getWorkonDayOff($employeeId, $monthId) {
        $sql = "select count(attendance_dt) as WORK_ON_DAY_OFF from hris_attendance_detail where overall_status='WD' and employee_id={$employeeId} 
        and attendance_dt between (select from_date from hris_month_code where month_id = {$monthId}) 
        and (select to_date from hris_month_code where month_id = {$monthId})";

        //$resultList = $this->rawQuery($sql, $boundedParameter);
        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }

        if($resultList[0]['WORK_ON_DAY_OFF'] >= 1) {
            return $resultList[0]['WORK_ON_DAY_OFF'];
        } else {
            return 0;
        }
    }

    public function getWorkonHoliday($employeeId, $monthId) {
        $sql = "select count(attendance_dt) as WORK_ON_HOLIDAY from hris_attendance_detail where overall_status='WH' and employee_id={$employeeId} 
        and attendance_dt between (select from_date from hris_month_code where month_id = {$monthId}) 
        and (select to_date from hris_month_code where month_id = {$monthId})";

        //$resultList = $this->rawQuery($sql, $boundedParameter);
        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }

        if($resultList[0]['WORK_ON_HOLIDAY'] >= 1) {
            return $resultList[0]['WORK_ON_HOLIDAY'];
        } else {
            return 0;
        }
    }

    public function getKhajaDays($employeeId, $monthId){
        $sql = "select count(attendance_dt) as KHAJA_DAYS from hris_attendance_detail where
         (in_time is not null or out_time is not null or sp_id = (select sp_id from hris_special_attendance_setup where sp_code = 'HK'))
        and employee_id={$employeeId} 
        and attendance_dt between (select from_date from hris_month_code where month_id = {$monthId}) 
        and (select to_date from hris_month_code where month_id = {$monthId})";

        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }else{
            return $resultList[0]['KHAJA_DAYS'];

        }
        
    }

    public function getMotorcycleLoan($employeeId){
        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['loadId'] = 4;

        $sql = "SELECT HLPD.AMOUNT AS MOTORCYCLE_LOAN, HLPD.LOAN_REQUEST_ID, HELR.LOAN_REQUEST_ID FROM HRIS_EMPLOYEE_LOAN_REQUEST AS HELR 
        INNER JOIN HRIS_LOAN_PAYMENT_DETAIL AS HLPD
        ON HLPD.LOAN_REQUEST_ID = HELR.LOAN_REQUEST_ID AND HELR.EMPLOYEE_ID=? AND HELR.LOAN_ID=? AND HLPD.PAID_FLAG='N'
        AND CURRENT_DATE BETWEEN HLPD.FROM_DATE and HLPD.TO_DATE";

        $resultList = $this->rawQuery($sql, $boundedParameter);
        // if (!(sizeof($resultList) == 1)) {
        //     throw new Exception('No Report Found.');
        // }

        if(!empty($resultList)) {
            //return $resultList[0]['MOTORCYCLE_LOAN'];
            $motorcyle_loan = 0;
            $motorcyle_loan = $this->getMonthlyInstallment($resultList[0]['LOAN_REQUEST_ID']);
             return round($motorcyle_loan, 2);
        } else {
            return 0;
        }
    }

    public function getMonthlyInstallment($loadRequestID){
        $boundedParameter = [];
        $boundedParameter['loanRequestID1'] = $loadRequestID;
        $boundedParameter['loanRequestID2'] = $loadRequestID;
        $sql = "SELECT SUM(INTEREST_AMOUNT)/ (select repayment_months from HRIS_EMPLOYEE_LOAN_REQUEST where loan_request_id = ?) as MONTHLY_INSTALLMENT from HRIS_LOAN_PAYMENT_DETAIL where loan_request_id = ?";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        if(!empty($resultList)) {
            return $resultList[0]['MONTHLY_INSTALLMENT'];
        } else {
            return 0;
        }

    }

    public function getHouseLeaveEncashedDays($employeeId, $monthId) {
        $fiscal_year_id = $this->getCurrentFiscalYearID();
        if($fiscal_year_id !=0 ) {
            $boundedParameter = [];
            $boundedParameter['employeeId'] = $employeeId;
            //$sql = "SELECT REQUESTED_DAYS_TO_ENCASH FROM HRIS_LEAVE_ENCASHMENT WHERE FISCAL_YEAR_ID = {$fiscal_year_id} AND EMPLOYEE_ID=? AND LEAVE_ID=2";
            $sql = "SELECT REQUESTED_DAYS_TO_ENCASH FROM HRIS_LEAVE_ENCASHMENT WHERE FISCAL_YEAR_ID = {$fiscal_year_id} AND EMPLOYEE_ID=? AND LEAVE_ID=2 AND MONTH_NO = (SELECT MONTH_NO FROM HRIS_MONTH_CODE WHERE MONTH_ID = {$monthId})";
            
            $resultList = $this->rawQuery($sql, $boundedParameter);

            /*if (!(sizeof($resultList) == 1)) {
                throw new Exception('No Report Found.');
            }*/

            if(empty($resultList) == 1){
                return 0;
            } else {
                return $resultList[0]['REQUESTED_DAYS_TO_ENCASH'];
            }
        }else {
            return 0;
        }
        
    }

    public function getCurrentFiscalYearID(){
        
        $sql = "SELECT FISCAL_YEAR_ID FROM HRIS_MONTH_CODE WHERE CURRENT_DATE BETWEEN FROM_DATE AND TO_DATE";
        $resultList = $this->rawQuery($sql);

        //echo '<pre>';print_r($resultList);die;

        if(empty($resultList) == 1){
            return 0;
        } else {
            return $resultList[0]['FISCAL_YEAR_ID'];
        }

    }

    public function getLoanTest($employeeId, $monthId){
        return 100;
    }

    public function getOvertimeLunchAllowance($employeeId, $monthId){

        $sql = "select total_app_lunch_allowance as OVERTIME_LUNCH_ALLOWANCE from hris_employee_overtime_claim_request where employee_id = {$employeeId} and month_id = {$monthId} and status = 'AP'";

        // print_r($sql);die;
        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }else{
            return $resultList[0]['OVERTIME_LUNCH_ALLOWANCE'];

        }
    }

    public function getOvertimeNightAllowance($employeeId, $monthId){

        $sql = "select total_app_night_allowance as OVERTIME_NIGHT_ALLOWANCE from hris_employee_overtime_claim_request where employee_id = {$employeeId} and month_id = {$monthId} and status = 'AP'";

        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }else{
            return $resultList[0]['OVERTIME_NIGHT_ALLOWANCE'];

        }
    }

    public function getOvertimeLockingAllowance($employeeId, $monthId){

        $sql = "select total_app_locking_allowance as OVERTIME_LOCKING_ALLOWANCE from hris_employee_overtime_claim_request where employee_id = {$employeeId} and month_id = {$monthId} and status = 'AP' ";

        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }else{
            return $resultList[0]['OVERTIME_LOCKING_ALLOWANCE'];

        }
    }

    public function getOvertimeAnnualTaxableIncome($employeeId, $monthId){

        $sql = "SELECT ifnull(VAL,0) AS OVERTIME_ANNUAL_TAXABLE_INCOME FROM HRIS_SALARY_SHEET_DETAIL WHERE PAY_ID = 88
                AND SHEET_NO IN (SELECT SHEET_NO FROM HRIS_SALARY_SHEET WHERE MONTH_ID = {$monthId} AND SALARY_TYPE_ID = 1) 
                AND EMPLOYEE_ID = {$employeeId}";

        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            return 0;
        }else{
            return $resultList[0]['OVERTIME_ANNUAL_TAXABLE_INCOME'];

        }

    }

    public function getTotalMonthlyTaxableIncome($employeeId, $monthId){

        $sql = "SELECT VAL AS TOTAL_MONTHLY_TAXABLE_INCOME FROM HRIS_SALARY_SHEET_DETAIL WHERE PAY_ID = 78
                AND SHEET_NO IN (SELECT SHEET_NO FROM HRIS_SALARY_SHEET WHERE MONTH_ID = {$monthId} AND SALARY_TYPE_ID = 1) 
                AND EMPLOYEE_ID = {$employeeId}";

        $resultList = $this->rawQuery($sql);

        if (!(sizeof($resultList) == 1)) {
            throw new Exception('No Report Found.');
        }else{
            return $resultList[0]['TOTAL_MONTHLY_TAXABLE_INCOME'];

        }

    }

    
}
