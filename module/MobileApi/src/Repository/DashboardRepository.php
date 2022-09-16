<?php

namespace MobileApi\Repository;

use Zend\Db\Adapter\AdapterInterface;

class DashboardRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
public function getMonthDate(){
    $sql="SELECT * FROM HRIS_MONTH_CODE 
WHERE TRUNC(SYSDATE) BETWEEN FROM_DATE and TO_DATE";
     $statement = $this->adapter->query($sql);
        $result = $statement->execute()->current();
        return $result;
}
   
    public function fetchEmployeeDashboardDetail($employeeId, $startDate, $endDate) {
        
        $sql = "
            SELECT EMPLOYEE_TBL.*,
              LATE_ATTEN_TBL.\"'L'\"+LATE_ATTEN_TBL.\"'B'\"+LATE_ATTEN_TBL.\"'Y'\" LATE_IN,
              LATE_ATTEN_TBL.\"'E'\"+LATE_ATTEN_TBL.\"'B'\" EARLY_OUT,
              LATE_ATTEN_TBL.\"'X'\"+LATE_ATTEN_TBL.\"'Y'\" MISSED_PUNCH,
              ATTEN_TBL.\"'PR'\"    +ATTEN_TBL.\"'WD'\"+ATTEN_TBL.\"'WH'\"+ ATTEN_TBL.\"'TP'\"+ ATTEN_TBL.\"'LP'\"+ATTEN_TBL.\"'VP'\" PRESENT_DAY,
              ATTEN_TBL.\"'AB'\"    +ATTEN_TBL.\"'BA'\"+ATTEN_TBL.\"'LA'\" ABSENT_DAY,
              ATTEN_TBL.\"'LV'\" LEAVE,
              ATTEN_TBL.\"'WH'\" WOH,
              ATTEN_TBL.\"'TV'\" TOUR,
              ATTEN_TBL.\"'TN'\"+ATTEN_TBL.\"'TP'\" TRAINING,
              CHECK_TIME.IN_TIME,
              CHECK_TIME.OUT_TIME
              
            FROM
              (SELECT EMP.EMPLOYEE_ID,
                EMP.FULL_NAME,
                EMP.GENDER_ID,
                EMP.COMPANY_ID,
                EMP.BRANCH_ID,
                EMP.EMAIL_OFFICIAL,
                EMP.EMAIL_PERSONAL,
                TO_CHAR(EMP.JOIN_DATE, 'DD-MON-YYYY') JOIN_DATE,
                TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE) / 12)                                        AS SERVICE_YEARS,
                TRUNC(MOD(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE), 12))                                    AS SERVICE_MONTHS,
                TRUNC(SYSDATE) - ADD_MONTHS(EMP.JOIN_DATE, TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE))) AS SERVICE_DAYS,
                DSG.DESIGNATION_TITLE,
                EFL.FILE_PATH
              FROM HRIS_EMPLOYEES EMP,
                HRIS_DESIGNATIONS DSG,
                HRIS_EMPLOYEE_FILE EFL
              WHERE EMP.DESIGNATION_ID   = DSG.DESIGNATION_ID(+)
              AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
              AND EMP.RETIRED_FLAG       = 'N'
              AND EMP.EMPLOYEE_ID        = {$employeeId}
              ) EMPLOYEE_TBL
            LEFT JOIN
              (SELECT *
              FROM
                (SELECT EMPLOYEE_ID,
                  OVERALL_STATUS
                FROM HRIS_ATTENDANCE_DETAIL
                WHERE (ATTENDANCE_DT BETWEEN ('{$startDate}') AND ('{$endDate}'))
                ) PIVOT (COUNT(OVERALL_STATUS) FOR OVERALL_STATUS IN ('DO','HD','LV','TV','TN','PR','AB','WD','WH','BA','LA','TP','LP','VP'))
              ) ATTEN_TBL
            ON (EMPLOYEE_TBL.EMPLOYEE_ID = ATTEN_TBL.EMPLOYEE_ID)
            LEFT JOIN
              (SELECT *
              FROM
                (SELECT EMPLOYEE_ID,
                  LATE_STATUS
                FROM HRIS_ATTENDANCE_DETAIL
                WHERE (ATTENDANCE_DT BETWEEN ('{$startDate}') AND ('{$endDate}'))
                ) PIVOT (COUNT(LATE_STATUS) FOR LATE_STATUS IN ('L','E','B','N','X','Y'))
              ) LATE_ATTEN_TBL
            ON (EMPLOYEE_TBL.EMPLOYEE_ID = LATE_ATTEN_TBL.EMPLOYEE_ID)
            LEFT JOIN
              (SELECT EMPLOYEE_ID,
              INITCAP(TO_CHAR(IN_TIME, 'HH:MI AM')) AS IN_TIME,
              INITCAP(TO_CHAR(OUT_TIME, 'HH:MI AM')) AS OUT_TIME
              FROM HRIS_ATTENDANCE_DETAIL WHERE ATTENDANCE_DT=TRUNC(SYSDATE)
              ) CHECK_TIME
            ON (EMPLOYEE_TBL.EMPLOYEE_ID = CHECK_TIME.EMPLOYEE_ID)
          ";
//        print_r($sql);
//die();
        $statement = $this->adapter->query($sql);
        $result = $statement->execute()->current();
        return $result;
    }


}
