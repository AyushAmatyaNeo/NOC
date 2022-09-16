<?php
namespace Training\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;

class TrainingStatusRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, $tableName);
    }

    public function getTrainingRequestList($data) {
        $employeeId = $data['employeeId']; 
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $employeeTypeId = $data['employeeTypeId'];
        $requestStatusId = $data['requestStatusId'];
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];

        $searchCondition = $this->getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId);
        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);

        $statusCondition = "";
        $fromDateCondition = "";
        $toDateCondition = "";
        if ($requestStatusId != -1) {
            $statusCondition = " AND TR.STATUS =:requestStatusId";
            $boundedParameter['requestStatusId'] = $requestStatusId;
        }

        if ($fromDate != null) {
            $fromDateCondition = " AND ((TR.START_DATE>=TO_DATE(:fromDate,'DD-MM-YYYY')) OR (T.START_DATE>=TO_DATE(:fromDate,'DD-MM-YYYY')))";
            $boundedParameter['fromDate'] = $fromDate;
        }

        if ($toDate != null) {
            $toDateCondition = "AND ((TR.END_DATE<=TO_DATE(:toDate,'DD-MM-YYYY')) OR (T.END_DATE<=TO_DATE(:toDate,'DD-MM-YYYY')))";
            $boundedParameter['toDate'] = $toDate;
        }

        $sql = "SELECT TR.REQUEST_ID,
                  TR.EMPLOYEE_ID,
                  INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD,
                  BS_DATE(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_BS,
                  TR.APPROVED_BY,
                  TR.RECOMMENDED_BY,
                  TR.REMARKS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.DURATION
                    ELSE T.DURATION
                  END) AS DURATION ,
                  TR.DESCRIPTION,
                  INITCAP(
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.TITLE
                    ELSE T.TRAINING_NAME
                  END) AS TITLE,
                  TR.STATUS,
                  TR.TRAINING_ID,
                  TRAINING_TYPE_DESC(
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN TR.TRAINING_TYPE
                    ELSE T.TRAINING_TYPE
                  END) AS TRAINING_TYPE,
                  TR.RECOMMENDED_REMARKS,
                  TR.APPROVED_REMARKS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY'))
                    ELSE INITCAP(TO_CHAR(T.START_DATE, 'DD-MON-YYYY'))
                  END) AS START_DATE_AD,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN BS_DATE(TR.START_DATE)
                    ELSE BS_DATE(T.START_DATE)
                  END) AS START_DATE_BS,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY'))
                    ELSE INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY'))
                  END) AS END_DATE_AD,
                  (
                  CASE
                    WHEN TR.TRAINING_ID IS NULL
                    THEN BS_DATE(TR.END_DATE)
                    ELSE BS_DATE(T.END_DATE)
                  END)                                                            AS END_DATE_BS,
                  INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY'))            AS RECOMMENDED_DATE,
                  INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY'))               AS APPROVED_DATE,
                  INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY'))               AS MODIFIED_DATE,
                  INITCAP(E.FULL_NAME)                                            AS FULL_NAME,
                  INITCAP(T.TRAINING_NAME)                                        AS TRAINING_NAME,
                  RA.RECOMMEND_BY                                                 AS RECOMMENDER,
                  RA.APPROVED_BY                                                  AS APPROVER,
                  LEAVE_STATUS_DESC(TR.STATUS)                                    AS STATUS 
                FROM HRIS_EMPLOYEE_TRAINING_REQUEST TR
                LEFT OUTER JOIN HRIS_TRAINING_MASTER_SETUP T
                ON T.TRAINING_ID=TR.TRAINING_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=TR.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=TR.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=TR.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON TR.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID = RA.APPROVED_BY
                WHERE E.STATUS   ='E'
                AND (E1.STATUS   =
                  CASE
                    WHEN E1.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR E1.STATUS  IS NULL)
                AND (E2.STATUS =
                  CASE
                    WHEN E2.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR E2.STATUS    IS NULL)
                AND (RECM.STATUS =
                  CASE
                    WHEN RECM.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR RECM.STATUS  IS NULL)
                AND (APRV.STATUS =
                  CASE
                    WHEN APRV.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR APRV.STATUS   IS NULL)
                {$searchCondition['sql']}
                {$statusCondition}
                {$fromDateCondition}
                {$toDateCondition}
                ORDER BY TR.REQUESTED_DATE DESC";
                return $this->rawQuery($sql, $boundedParameter);
        // $statement = $this->adapter->query($sql);
        // $result = $statement->execute();
        // return $result;
    }
    
    public function fetchEmployeeTraining(){
        $sql="SELECT * FROM 
        HRIS_TRAINING_MASTER_SETUP TMS
        LEFT JOIN HRIS_EMPLOYEE_TRAINING_REQUEST TR ON (TR.TRAINING_ID=TMS.TRAINING_ID AND TR.EMPLOYEE_ID=700280)
        WHERE TR.STATUS IS NULL OR TR.STATUS!='AP'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    
}
