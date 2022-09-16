<?php

namespace Cron\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class CronRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchAbsentOrLate() {
        $sql = "SELECT 
CASE WHEN
BB.IN_TIME IS NULL
THEN 'ABSENT'
WHEN
BB.IN_TIME IS NOT NULL
THEN 'LATE'
END AS ABS_LATE,
BB.*
FROM (SELECT  AD.EMPLOYEE_ID,
  E.FULL_NAME                       AS EMPLOYEE_NAME,
  TO_CHAR(AD.IN_TIME, 'HH:MI AM')   AS IN_TIME,
  TO_CHAR(S.START_TIME, 'HH:MI AM') AS START_TIME,
  TO_CHAR(AD.OUT_TIME, 'HH:MI AM')  AS OUT_TIME,
  TO_CHAR(S.END_TIME, 'HH:MI AM')   AS END_TIME,
  S.LATE_IN,
  (extract(hour FROM (S.START_TIME)) *60 + extract(minute FROM (S.START_TIME))) 
  -(extract(hour FROM (AD.IN_TIME)) *60 + extract(minute FROM (AD.IN_TIME))) + S.LATE_IN AS IN_MINUTES,
  AD.OVERALL_STATUS,
  AD.LATE_STATUS,
  E.EMAIL_OFFICIAL AS EMPLOYEE_MAIL,
  E.BRANCH_ID,
  B.BRANCH_MANAGER_ID,
  EE.FULL_NAME      AS MANAGER_NAME,
  EE.EMAIL_OFFICIAL AS MANAGER_MAIL,
  AD.ATTENDANCE_DT
FROM HRIS_ATTENDANCE_DETAIL AD
LEFT JOIN HRIS_EMPLOYEES E
ON (AD.EMPLOYEE_ID = E.EMPLOYEE_ID)
LEFT JOIN HRIS_BRANCHES B
ON (E.BRANCH_ID = B.BRANCH_ID)
LEFT JOIN HRIS_EMPLOYEES EE
ON (B.BRANCH_MANAGER_ID = EE.EMPLOYEE_ID)
LEFT JOIN HRIS_SHIFTS S
  ON (AD.SHIFT_ID        = S.SHIFT_ID)
WHERE AD.ATTENDANCE_DT = trunc(sysdate)
AND (AD.in_time is not null OR AD.OVERALL_STATUS = 'AB') 
AND E.STATUS = 'E' )  BB 
WHERE (BB.IN_MINUTES < 0 OR BB.OVERALL_STATUS='AB')";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function fetchMissedOrEarlyOut() {
        $sql = "SELECT DISTINCT AD.EMPLOYEE_ID,
  E.FULL_NAME                       AS EMPLOYEE_NAME,
  TO_CHAR(AD.IN_TIME, 'HH:MI AM')   AS IN_TIME,
  TO_CHAR(S.START_TIME, 'HH:MI AM') AS START_TIME,
  TO_CHAR(AD.OUT_TIME, 'HH:MI AM')  AS OUT_TIME,
  TO_CHAR(S.END_TIME, 'HH:MI AM')   AS END_TIME,
  AD.OVERALL_STATUS,
  AD.LATE_STATUS,
  E.EMAIL_OFFICIAL AS EMPLOYEE_MAIL,
  E.BRANCH_ID,
  B.BRANCH_MANAGER_ID,
  EE.FULL_NAME      AS MANAGER_NAME,
  EE.EMAIL_OFFICIAL AS MANAGER_MAIL,
  AD.ATTENDANCE_DT
FROM HRIS_ATTENDANCE_DETAIL AD
LEFT JOIN HRIS_EMPLOYEES E
ON (AD.EMPLOYEE_ID = E.EMPLOYEE_ID)
LEFT JOIN HRIS_BRANCHES B
ON (E.BRANCH_ID = B.BRANCH_ID)
LEFT JOIN HRIS_EMPLOYEES EE
ON (B.BRANCH_MANAGER_ID = EE.EMPLOYEE_ID)
LEFT JOIN HRIS_SHIFTS S
ON (AD.SHIFT_ID        = S.SHIFT_ID)
WHERE AD.ATTENDANCE_DT = trunc(sysdate-1)
AND AD.LATE_STATUS    IN ('E', 'B', 'X', 'Y')  
ORDER BY AD.EMPLOYEE_ID";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
