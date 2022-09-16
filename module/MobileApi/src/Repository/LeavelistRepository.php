<?php

namespace MobileApi\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class LeavelistRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
//public function getMonthDate(){
//    $sql="SELECT * FROM HRIS_MONTH_CODE 
//WHERE TRUNC(SYSDATE) BETWEEN FROM_DATE and TO_DATE";
//     $statement = $this->adapter->query($sql);
//        $result = $statement->execute()->current();
//        return $result;
//}
   
    public function fetchEmployeeLeaveList($employeeId) {
        
        $sql = "
            SELECT * FROM (SELECT LA.LEAVE_ID,
                  LMS.LEAVE_CODE,
                  LMS.LEAVE_ENAME,
                  LA.PREVIOUS_YEAR_BAL,
                  LA.TOTAL_DAYS,
                  LA.BALANCE,
                  (SELECT SUM(ELR.NO_OF_DAYS/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END))
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                   LEFT JOIN (SELECT * FROM HRIS_LEAVE_YEARS  WHERE TRUNC(SYSDATE) BETWEEN START_DATE AND END_DATE ) LY ON (1=1)
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS     ='AP'
                   AND ELR.START_DATE BETWEEN LY.START_DATE AND LY.END_DATE
                  ) AS LEAVE_TAKEN,
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
                WHERE LA.EMPLOYEE_ID={$employeeId} AND LMS.STATUS ='E' AND LMS.IS_MONTHLY = 'N' ORDER BY LMS.LEAVE_ENAME ASC)
          ";
      // print_r($sql);
//die();
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
		return Helper::extractDbData($result);
    }


}
