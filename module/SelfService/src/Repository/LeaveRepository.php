<?php

namespace SelfService\Repository;

use Application\Repository\HrisRepository;
use LeaveManagement\Model\LeaveAssign;
use Traversable;
use Zend\Db\Adapter\AdapterInterface;

class LeaveRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = LeaveAssign::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    function selectAll($employeeId, $data=null): Traversable {
      if($data['fiscalYearMonthCode']){
        $leaveMonthNo = $data['fiscalYearMonthCode'];
      }else{
        $leaveMonthNo = 'select leave_year_month_no from hris_leave_month_code where current_date between
        from_date and to_date';
      }
        $boundedParameter = [];
        $boundedParameter['employeeId0'] = $employeeId;
        $boundedParameter['employeeId1'] = $employeeId;
        $sql = "SELECT *, ifnull(PREVIOUS_YEAR_BAL,0)+ifnull(TOTAL_DAYS,0)-ifnull(LEAVE_TAKEN,0)-ifnull(ENCASHED,0)+ifnull(LEAVE_ADDED,0)-ifnull(LEAVE_DEDUCTED,0) as BALANCE FROM (SELECT LA.LEAVE_ID,
                  LMS.LEAVE_CODE,
                  LMS.LEAVE_ENAME,
                  LA.PREVIOUS_YEAR_BAL,
                  LA.TOTAL_DAYS,
                  --LA.BALANCE,
                  LA.fiscal_year_month_no,
                  (SELECT SUM(ELR.NO_OF_DAYS/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END))
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                   LEFT JOIN (SELECT * FROM HRIS_LEAVE_YEARS  WHERE CURRENT_DATE BETWEEN START_DATE AND END_DATE ) LY ON (1=1)
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS IN ('AP','CP','CR')
                   AND ELR.START_DATE BETWEEN LY.START_DATE AND LY.END_DATE
                  ) AS LEAVE_TAKEN,
                  (SELECT SUM(HS.ENCASH_DAYS)
                  FROM HRIS_EMP_SELF_LEAVE_CLOSING HS
                  WHERE HS.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND HS.LEAVE_ID     =LA.LEAVE_ID
                  ) AS ENCASHED,
                  (SELECT SUM(EPD.NO_OF_DAYS)
                  FROM HRIS_EMPLOYEE_PENALTY_DAYS EPD
                  WHERE EPD.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND EPD.LEAVE_ID     =LA.LEAVE_ID
                  ) AS LEAVE_DEDUCTED,
                  (SELECT SUM(ELA.NO_OF_DAYS)
                  FROM HRIS_EMPLOYEE_LEAVE_ADDITION ELA
                  WHERE ELA.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELA.LEAVE_ID     =LA.LEAVE_ID
                  ) AS LEAVE_ADDED
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LA.LEAVE_ID     =LMS.LEAVE_ID)
                WHERE LA.EMPLOYEE_ID=? AND LMS.STATUS ='E' AND LMS.IS_MONTHLY = 'N' ORDER BY LMS.LEAVE_ENAME ASC)
                
                UNION
	
	SELECT
	 * , ifnull(PREVIOUS_YEAR_BAL,0)+ifnull(TOTAL_DAYS,0)-ifnull(LEAVE_TAKEN,0)-ifnull(ENCASHED,0)+ifnull(LEAVE_ADDED,0)-ifnull(LEAVE_DEDUCTED,0) as BALANCE
FROM (SELECT
	 LA.LEAVE_ID,
	 LMS.LEAVE_CODE,
	 LMS.LEAVE_ENAME,
	 LA.PREVIOUS_YEAR_BAL,
	 LA.TOTAL_DAYS,
	 --LA.BALANCE,
	 LA.fiscal_year_month_no,
	 (SELECT SUM(ELR.NO_OF_DAYS/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END))
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS     ='AP'
                  AND ELR.START_DATE <= MTH.TO_DATE
                  ) AS LEAVE_TAKEN,
	 (SELECT
	 SUM(HS.ENCASH_DAYS) 
		FROM HRIS_EMP_SELF_LEAVE_CLOSING HS 
		WHERE HS.EMPLOYEE_ID=LA.EMPLOYEE_ID 
		AND HS.LEAVE_ID =LA.LEAVE_ID ) AS ENCASHED,
	 (SELECT
	 SUM(EPD.NO_OF_DAYS) 
		FROM HRIS_EMPLOYEE_PENALTY_DAYS EPD 
		WHERE EPD.EMPLOYEE_ID=LA.EMPLOYEE_ID 
		AND EPD.LEAVE_ID =LA.LEAVE_ID ) AS LEAVE_DEDUCTED,
	 (SELECT
	 SUM(ELA.NO_OF_DAYS) 
		FROM HRIS_EMPLOYEE_LEAVE_ADDITION ELA 
		WHERE ELA.EMPLOYEE_ID=LA.EMPLOYEE_ID 
		AND ELA.LEAVE_ID =LA.LEAVE_ID ) AS LEAVE_ADDED 
	FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA 
	LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS ON (LA.LEAVE_ID =LMS.LEAVE_ID) 
	LEFT JOIN (SELECT * FROM HRIS_LEAVE_MONTH_CODE WHERE 
                LEAVE_YEAR_ID=(SELECT LEAVE_YEAR_ID FROM HRIS_LEAVE_YEARS 
                WHERE CURRENT_DATE BETWEEN START_DATE AND END_DATE)) MTH
                 ON (MTH.LEAVE_YEAR_MONTH_NO= LA.FISCAL_YEAR_MONTH_NO)
	WHERE LA.EMPLOYEE_ID=? 
	AND LMS.STATUS ='E' 
	AND LMS.IS_MONTHLY = 'Y' 
	ORDER BY LMS.LEAVE_ENAME ASC)
	where fiscal_year_month_no in ($leaveMonthNo)";
        
        
        // print_r($sql);die;
        $statement = $this->adapter->query($sql);
        return $statement->execute($boundedParameter);
    }

    function monthlyLeaveStatus($employeeId, $fiscalYearMonthNo) {
        $boundedParameter = [];
        $boundedParameter['employeeId1'] = $employeeId;
        $boundedParameter['fiscalYearMonthNo1'] = $fiscalYearMonthNo;
        $boundedParameter['employeeId2'] = $employeeId;
        $boundedParameter['fiscalYearMonthNo2'] = $fiscalYearMonthNo;
        
        $sql = "SELECT * FROM (SELECT LA.LEAVE_ID,
                  LMS.LEAVE_CODE,
                  LMS.LEAVE_ENAME,
                  LA.TOTAL_DAYS,
                  LA.BALANCE,
                  (SELECT SUM(ELR.NO_OF_DAYS/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END))
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS     ='AP'
                  AND ELR.START_DATE BETWEEN MTH.FROM_DATE AND MTH.TO_DATE
                  ) AS LEAVE_TAKEN
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LA.LEAVE_ID =LMS.LEAVE_ID)
                LEFT JOIN (SELECT * FROM HRIS_LEAVE_MONTH_CODE WHERE 
                LEAVE_YEAR_ID=(SELECT LEAVE_YEAR_ID FROM HRIS_LEAVE_YEARS 
                WHERE CURRENT_DATE BETWEEN START_DATE AND END_DATE)) MTH
                ON (MTH.LEAVE_YEAR_MONTH_NO= LA.FISCAL_YEAR_MONTH_NO)
                WHERE LA.EMPLOYEE_ID        = ?
                AND LA.FISCAL_YEAR_MONTH_NO = ?
                AND LMS.STATUS              ='E'
                AND LMS.IS_MONTHLY          = 'Y'
                AND LMS.CARRY_FORWARD          = 'N'
                ORDER BY LMS.LEAVE_ENAME ASC)
                UNION ALL
                SELECT * FROM (SELECT LA.LEAVE_ID,
                  LMS.LEAVE_CODE,
                  LMS.LEAVE_ENAME,
                  LA.TOTAL_DAYS,
                  LA.BALANCE,
                  (SELECT SUM(ELR.NO_OF_DAYS/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END))
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS     ='AP'
                  AND ELR.START_DATE <= MTH.TO_DATE
                  ) AS LEAVE_TAKEN
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LA.LEAVE_ID =LMS.LEAVE_ID)
                LEFT JOIN (SELECT * FROM HRIS_LEAVE_MONTH_CODE WHERE 
                LEAVE_YEAR_ID=(SELECT LEAVE_YEAR_ID FROM HRIS_LEAVE_YEARS 
                WHERE CURRENT_DATE BETWEEN START_DATE AND END_DATE)) MTH
                ON (MTH.LEAVE_YEAR_MONTH_NO= LA.FISCAL_YEAR_MONTH_NO)
                WHERE LA.EMPLOYEE_ID        = ?
                AND LA.FISCAL_YEAR_MONTH_NO = ?
                AND LMS.STATUS              ='E'
                AND LMS.IS_MONTHLY          = 'Y'
                AND LMS.CARRY_FORWARD          = 'Y'
                ORDER BY LMS.LEAVE_ENAME ASC) ";

        
        $statement = $this->adapter->query($sql);
        return $statement->execute($boundedParameter);
    }

}
