<?php

namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;

class ManagerReportRepo implements RepositoryInterface {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchAllEmployee($employeeId) {
        $sql = "SELECT RA.EMPLOYEE_ID, E.EMPLOYEE_CODE||'-'||E.FULL_NAME AS FULL_NAME
                FROM HRIS_RECOMMENDER_APPROVER  RA
                LEFT join HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=RA.EMPLOYEE_ID)
                  WHERE (RA.RECOMMEND_BY={$employeeId}
                  OR RA.APPROVED_BY    = {$employeeId})
                  AND E.STATUS = 'E'
                AND E.RETIRED_FLAG = 'N'";



        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        $list = [];
        $list[-1] = 'All Employee';
        foreach ($result as $data) {
            $list[$data['EMPLOYEE_ID']] = $data['FULL_NAME'];
        }
        return $list;
    }

    public function attendanceReport($currentEmployeeId, $fromDate, $toDate, $employeeId, $status, $missPunchOnly = false) {
        $fromDateCondition = "";
        $toDateCondition = "";
        $employeeCondition = '';
        $statusCondition = '';
        $missPunchOnlyCondition = '';
        if ($fromDate != null) {
            $fromDateCondition = " AND A.ATTENDANCE_DT>=TO_DATE('" . $fromDate . "','DD-MM-YYYY') ";
        }
        if ($toDate != null) {
            $toDateCondition = " AND A.ATTENDANCE_DT<=TO_DATE('" . $toDate . "','DD-MM-YYYY') ";
        }
        if ($employeeId != null) {
            $employeeCondition = " AND A.EMPLOYEE_ID ={$employeeId} ";
            if ($employeeId == -1) {
                $employeeCondition = " AND (RA.RECOMMEND_BY=$currentEmployeeId OR RA.APPROVED_BY = $currentEmployeeId)";
            }
        }
        if ($status == "A") {
            $statusCondition = "AND A.OVERALL_STATUS = 'AB'";
        }

        if ($status == "H") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'HD' OR A.OVERALL_STATUS = 'WH' ) ";
        }

        if ($status == "L") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'LV' OR A.OVERALL_STATUS = 'LP' ) ";
        }

        if ($status == "P") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'PR' OR A.OVERALL_STATUS = 'WD' OR A.OVERALL_STATUS = 'WH' OR A.OVERALL_STATUS = 'BA' OR A.OVERALL_STATUS = 'LA' OR A.OVERALL_STATUS = 'TP' OR A.OVERALL_STATUS = 'LP' OR A.OVERALL_STATUS = 'VP' ) ";
        }
        if ($status == "T") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'TN' OR A.OVERALL_STATUS = 'TP' ) ";
        }
        if ($status == "TVL") {
            $statusCondition = "AND (A.OVERALL_STATUS = 'TV' OR A.OVERALL_STATUS = 'VP' ) ";
        }
        if ($status == "WOH") {
            $statusCondition = "AND A.OVERALL_STATUS = 'WH'";
        }
        if ($status == "WOD") {
            $statusCondition = "AND A.OVERALL_STATUS = 'WD'";
        }
        if ($status == "LI") {
            $statusCondition = "AND (A.LATE_STATUS = 'L' OR A.LATE_STATUS = 'B' OR A.LATE_STATUS ='Y') ";
        }
        if ($status == "EO") {
            $statusCondition = "AND (A.LATE_STATUS = 'E' OR A.LATE_STATUS = 'B' ) ";
        }

        if ($missPunchOnly) {
            $missPunchOnlyCondition = "AND (A.LATE_STATUS = 'X' OR A.LATE_STATUS = 'Y' ) ";
        }

        $sql = "
                SELECT A.ID                                        AS ID,
                  E.FULL_NAME                                      AS FULL_NAME,
                  A.EMPLOYEE_ID                                    AS EMPLOYEE_ID,
                  E.EMPLOYEE_CODE                                    AS EMPLOYEE_CODE,
                  INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT,
                  BS_DATE(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT_N,
                  INITCAP(TO_CHAR(A.IN_TIME, 'HH:MI AM'))          AS IN_TIME,
                  INITCAP(TO_CHAR(A.OUT_TIME, 'HH:MI AM'))         AS OUT_TIME,
                  A.IN_REMARKS                                     AS IN_REMARKS,
                  A.OUT_REMARKS                                    AS OUT_REMARKS,
                  MIN_TO_HOUR(A.TOTAL_HOUR)                        AS TOTAL_HOUR,
                  A.LEAVE_ID                                       AS LEAVE_ID,
                  A.HOLIDAY_ID                                     AS HOLIDAY_ID,
                  A.TRAINING_ID                                    AS TRAINING_ID,
                  A.TRAVEL_ID                                      AS TRAVEL_ID,
                  A.SHIFT_ID                                       AS SHIFT_ID,
                  A.DAYOFF_FLAG                                    AS DAYOFF_FLAG,
                  A.LATE_STATUS                                    AS LATE_STATUS,
                  INITCAP(E.FIRST_NAME)                            AS FIRST_NAME,
                  INITCAP(E.MIDDLE_NAME)                           AS MIDDLE_NAME,
                  INITCAP(E.LAST_NAME)                             AS LAST_NAME,
                  H.HOLIDAY_ENAME                                  AS HOLIDAY_ENAME,
                  L.LEAVE_ENAME                                    AS LEAVE_ENAME,
                  T.TRAINING_NAME                                  AS TRAINING_NAME,
                  TVL.DESTINATION                                  AS TRAVEL_DESTINATION,
                  (
                  CASE
                    WHEN A.OVERALL_STATUS = 'DO'
                    THEN 'Day Off'
                    WHEN A.OVERALL_STATUS ='HD'
                    THEN 'On Holiday('
                      ||H.HOLIDAY_ENAME
                      ||')'
                    WHEN A.OVERALL_STATUS ='LV'
                    THEN 'On Leave('
                      ||L.LEAVE_ENAME
                      || ')'
                    WHEN A.OVERALL_STATUS ='TV'
                    THEN 'On Travel('
                      ||TVL.DESTINATION
                      ||')'
                    WHEN A.OVERALL_STATUS ='TN'
                    THEN 'On Training('
                      || (CASE WHEN A.TRAINING_TYPE = 'A' THEN T.TRAINING_NAME ELSE ETN.TITLE END)
                      ||')'
                    WHEN A.OVERALL_STATUS ='WD'
                    THEN 'Work On Dayoff'
                    WHEN A.OVERALL_STATUS ='WH'
                    THEN 'Work on Holiday('
                      ||H.HOLIDAY_ENAME
                      ||')'
                    WHEN A.OVERALL_STATUS ='LP'
                    THEN 'Work on Leave('
                      ||L.LEAVE_ENAME
                      ||')'
                    WHEN A.OVERALL_STATUS ='VP'
                    THEN 'Work on Travel('
                      ||TVL.DESTINATION
                      ||')'
                      ||LATE_STATUS_DESC(A.LATE_STATUS)
                    WHEN A.OVERALL_STATUS ='TP'
                    THEN 'Present('
                      ||T.TRAINING_NAME
                      ||')'
                      ||LATE_STATUS_DESC(A.LATE_STATUS)
                    WHEN A.OVERALL_STATUS ='PR'
                    THEN 'Present'
                      ||LATE_STATUS_DESC(A.LATE_STATUS)
                    WHEN A.OVERALL_STATUS ='AB'
                    THEN 'Absent'
                    WHEN A.OVERALL_STATUS ='BA'
                    THEN 'Present(Late In and Early Out)'
                    WHEN A.OVERALL_STATUS ='LA'
                    THEN 'Present(Third Day Late)'
                  END)AS STATUS,
                  SS.SHIFT_ENAME        AS SHIFT_NAME,
                  TO_CHAR(SS.START_TIME, 'HH:MI AM')   AS START_TIME,
                  TO_CHAR(SS.END_TIME, 'HH:MI AM')    AS END_TIME
                FROM HRIS_ATTENDANCE_DETAIL A
                LEFT JOIN HRIS_EMPLOYEES E
                ON A.EMPLOYEE_ID=E.EMPLOYEE_ID
                LEFT JOIN HRIS_HOLIDAY_MASTER_SETUP H
                ON A.HOLIDAY_ID=H.HOLIDAY_ID
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
                ON A.LEAVE_ID=L.LEAVE_ID
                LEFT JOIN HRIS_TRAINING_MASTER_SETUP T
                ON (A.TRAINING_ID=T.TRAINING_ID AND A.TRAINING_TYPE='A')
                LEFT JOIN HRIS_EMPLOYEE_TRAINING_REQUEST ETN
                ON (ETN.REQUEST_ID=A.TRAINING_ID AND A.TRAINING_TYPE ='R')
                LEFT JOIN HRIS_EMPLOYEE_TRAVEL_REQUEST TVL
                ON A.TRAVEL_ID      =TVL.TRAVEL_ID
                LEFT JOIN HRIS_RECOMMENDER_APPROVER  RA
                ON RA.EMPLOYEE_ID=E.EMPLOYEE_ID
                LEFT JOIN HRIS_SHIFTS SS ON (A.SHIFT_ID=SS.SHIFT_ID)
                WHERE 1=1 AND E.STATUS='E'
                {$employeeCondition}
                {$fromDateCondition}
                {$toDateCondition}
                {$statusCondition}
                {$missPunchOnlyCondition}
                ORDER BY A.ATTENDANCE_DT DESC,A.IN_TIME ASC
                ";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
