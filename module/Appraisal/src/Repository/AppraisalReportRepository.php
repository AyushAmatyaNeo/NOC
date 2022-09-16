<?php

namespace Appraisal\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Appraisal\Model\AppraisalAnswer;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class AppraisalReportRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AppraisalAnswer::TABLE_NAME, $adapter);
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

    public function fetchFilterdData($fromDate, $toDate, $employeeId, $companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $appraisalId, $appraisalStageId, $reportType = null, $userId = null, $isMonthly = false) {
        $durationType = $isMonthly ? 'M' : 'A';
        if ($reportType != null && $reportType == "appraisalEvaluation") {
            $userFlag = "AND AQ.APPRAISER_FLAG='Y'";
            $userQuestionNum = " OR USER_QUESTION_NUM>0";
        } else if ($reportType != null && $reportType == "appraisalReview") {
            $userFlag = "AND AQ.REVIEWER_FLAG='Y'";
            $userQuestionNum = " OR USER_QUESTION_NUM>0";
        } else {
            $userFlag = "";
            $userQuestionNum = "";
        }

        $sql = "SELECT *
            FROM
              (SELECT A.APPRAISAL_ID                         AS APPRAISAL_ID,
                A.APPRAISAL_TYPE_ID                          AS APPRAISAL_TYPE_ID,
                A.STATUS                                     AS STATUS,
                A.APPRAISAL_CODE                             AS APPRAISAL_CODE,
                A.APPRAISAL_EDESC                            AS APPRAISAL_EDESC,
                A.REMARKS                                    AS REMARKS,
                A.KPI_SETTING                                AS KPI_SETTING,
                A.COMPETENCIES_SETTING                       AS COMPETENCIES_SETTING,
                INITCAP(TO_CHAR(A.START_DATE,'DD-MON-YYYY')) AS START_DATE,
                INITCAP(TO_CHAR(A.END_DATE,'DD-MON-YYYY'))   AS END_DATE,
                AA.APPRAISER_ID                              AS APPRAISER_ID,
                    APR.FULL_NAME                                AS APPRAISER_NAME,
                AA.ALT_REVIEWER_ID                           AS ALT_REVIEWER_ID,
                    AREV.FULL_NAME                               AS ALT_REVIEWER_NAME,
                AA.REVIEWER_ID                               AS REVIEWER_ID,
                    REV.FULL_NAME                                AS REVIEWER_NAME,
                AA.ALT_APPRAISER_ID                          AS ALT_APPRAISER_ID,
                    AREV.FULL_NAME                               AS ALT_APPRAISER_NAME,
                AA.CURRENT_STAGE_ID                          AS CURRENT_STAGE_ID,
                APS.ANNUAL_RATING_KPI                        AS ANNUAL_RATING_KPI,
                APS.ANNUAL_RATING_COMPETENCY                 AS ANNUAL_RATING_COMPETENCY,
                APS.APPRAISER_OVERALL_RATING                 AS APPRAISER_OVERALL_RATING,
                APS.REVIEWER_AGREE                           AS REVIEWER_AGREE,
                APS.APPRAISEE_AGREE                          AS APPRAISEE_AGREE,
                APS.SUPER_REVIEWER_AGREE                     AS SUPER_REVIEWER_AGREE,
                APS.APPRAISED_BY                             AS APPRAISED_BY,
                APS.REVIEWED_BY                              AS REVIEWED_BY,
                APS.DEFAULT_RATING                           AS DEFAULT_RATING,
                INITCAP(E.FULL_NAME)                         AS FULL_NAME,
                E.EMPLOYEE_CODE                              AS EMPLOYEE_CODE,
                E.EMPLOYEE_ID                                AS EMPLOYEE_ID,
                INITCAP(T.APPRAISAL_TYPE_EDESC)              AS APPRAISAL_TYPE_EDESC,
                INITCAP(S.STAGE_EDESC)                       AS STAGE_EDESC,
                S.STAGE_ID                                   AS STAGE_ID,
                AKPI.SNO                                     AS KPI_SNO,
                (SELECT COUNT(*)
                FROM HRIS_APPRAISAL_ANSWER APNS
                WHERE APNS.EMPLOYEE_ID = E.EMPLOYEE_ID
                AND APNS.APPRAISAL_ID  = A.APPRAISAL_ID
                ) AS ANSWER_NUM,
                (SELECT COUNT(*)
                FROM HRIS_APPRAISAL_KPI APKPI
                WHERE APKPI.APPRAISAL_ID = A.APPRAISAL_ID
                AND APKPI.EMPLOYEE_ID    =E.EMPLOYEE_ID
                ) AS KPI_ANS_NUM,
                (SELECT COUNT(*)
                FROM HRIS_APPRAISAL_COMPETENCY APCOM
                WHERE APCOM.APPRAISAL_ID = A.APPRAISAL_ID
                AND APCOM.EMPLOYEE_ID    =E.EMPLOYEE_ID
                )                  AS COM_ANS_NUM,
                (SELECT COUNT(*)
                FROM HRIS_APPRAISAL_KPI APKPI1
                WHERE APKPI1.APPRAISAL_ID = A.APPRAISAL_ID
                AND APKPI1.EMPLOYEE_ID    =E.EMPLOYEE_ID
                AND APKPI1.SELF_RATING IS NOT NULL
                )                  AS KPI_SELF_RATING_NUM,
                (SELECT COUNT(*) FROM HRIS_APPRAISAL_STAGE_QUESTIONS SQ
            LEFT JOIN HRIS_APPRAISAL_QUESTION AQ
            ON SQ.QUESTION_ID = AQ.QUESTION_ID
            WHERE SQ.STAGE_ID=AA.CURRENT_STAGE_ID AND AQ.STATUS='E' AND SQ.STATUS='E' " . $userFlag . ") AS USER_QUESTION_NUM,
                INITCAP(TO_CHAR(AKPI.APPROVED_DATE,'DD-MON-YYYY')) AS KPI_APPROVED_DATE
              FROM HRIS_APPRAISAL_SETUP A
              INNER JOIN HRIS_APPRAISAL_ASSIGN AA
              ON A.APPRAISAL_ID=AA.APPRAISAL_ID
              INNER JOIN HRIS_APPRAISAL_STATUS APS
              ON APS.APPRAISAL_ID=AA.APPRAISAL_ID
              AND APS.EMPLOYEE_ID=AA.EMPLOYEE_ID
              INNER JOIN HRIS_EMPLOYEES E
              ON E.EMPLOYEE_ID=AA.EMPLOYEE_ID
              INNER JOIN HRIS_APPRAISAL_TYPE T
              ON T.APPRAISAL_TYPE_ID=A.APPRAISAL_TYPE_ID
              INNER JOIN HRIS_APPRAISAL_STAGE S
              ON S.STAGE_ID =AA.CURRENT_STAGE_ID
              LEFT JOIN HRIS_APPRAISAL_KPI AKPI
              ON APS.APPRAISAL_ID  =AKPI.APPRAISAL_ID
              AND APS.EMPLOYEE_ID  =AKPI.EMPLOYEE_ID
              LEFT JOIN HRIS_EMPLOYEES APR
              ON APR.EMPLOYEE_ID = AA.APPRAISER_ID
              LEFT JOIN HRIS_EMPLOYEES AAPR
              ON AAPR.EMPLOYEE_ID = AA.ALT_APPRAISER_ID
              LEFT JOIN HRIS_EMPLOYEES REV
              ON REV.EMPLOYEE_ID = AA.REVIEWER_ID
              LEFT JOIN HRIS_EMPLOYEES AREV
              ON AREV.EMPLOYEE_ID = AA.ALT_REVIEWER_ID
              WHERE AA.STATUS      ='E'
              AND E.STATUS         ='E'
              AND T.STATUS         ='E'
              AND S.STATUS         ='E'
              AND (AKPI.SNO = (SELECT MIN(KPI.SNO)
                  FROM HRIS_APPRAISAL_KPI KPI
                  WHERE KPI.EMPLOYEE_ID = APS.EMPLOYEE_ID
                  AND KPI.APPRAISAL_ID  = APS.APPRAISAL_ID
                  ) OR AKPI.SNO IS NULL)
              AND T.DURATION_TYPE = '{$durationType}'
  ";
        if ($reportType != null && $reportType == "appraisalEvaluation") {
            $sql .= " AND ( AA.APPRAISER_ID  =" . $userId . " OR AA.ALT_APPRAISER_ID =" . $userId . " )";
        }
        if ($reportType != null && $reportType == "appraisalReview") {
            $sql .= " AND ( AA.REVIEWER_ID  =" . $userId . " OR AA.ALT_REVIEWER_ID =" . $userId . " )";
        }
        if ($reportType != null && $reportType == "appraisalFinalReview") {
            $sql .= " AND AA.SUPER_REVIEWER_ID  =" . $userId;
        }
        if ($employeeId != null && $employeeId != -1) {
            $sql .= " AND E.EMPLOYEE_ID=" . $employeeId;
        }
        if ($companyId != null && $companyId != -1) {
            $sql .= " AND E.COMPANY_ID=" . $companyId;
        }
        if ($branchId != null && $branchId != -1) {
            $sql .= " AND E.BRANCH_ID=" . $branchId;
        }
        if ($departmentId != null && $departmentId != -1) {
            $sql .= " AND E.DEPARTMENT_ID=" . $departmentId;
        }
        if ($designationId != null && $designationId != -1) {
            $sql .= " AND E.DESIGNATION_ID=" . $designationId;
        }
        if ($positionId != null && $positionId != -1) {
            $sql .= " AND E.POSITION_ID=" . $positionId;
        }
        if ($serviceTypeId != null && $serviceTypeId != -1) {
            $sql .= " AND E.SERVICE_TYPE_ID=" . $serviceTypeId;
        }
        if ($serviceEventTypeId != null && $serviceEventTypeId != -1) {
            $sql .= " AND E.SERVICE_EVENT_TYPE_ID=" . $serviceEventTypeId;
        }
        if ($appraisalId != null && $appraisalId != -1) {
            $sql .= " AND A.APPRAISAL_ID=" . $appraisalId;
        }
        if ($appraisalStageId != null && $appraisalStageId != -1) {
            $sql .= " AND S.STAGE_ID=" . $appraisalStageId;
        }
        if ($fromDate != null && $fromDate != "") {
            $sql .= " AND A.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }
        if ($toDate != null && $toDate != "") {
            $sql .= " AND A.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }
        $sql .= " ORDER BY A.START_DATE DESC,E.FIRST_NAME)";
        $sql .= "
            WHERE ( (COMPETENCIES_SETTING = (
              CASE
                WHEN ANSWER_NUM=0
                THEN ('Y')
              END )
            AND COM_ANS_NUM >0)
            OR (KPI_SETTING = (
              CASE
                WHEN ANSWER_NUM=0
                THEN ('Y')
              END)
            AND KPI_ANS_NUM>0)
            OR ANSWER_NUM  >0 " . $userQuestionNum . ")
";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function checkAppraiserQuestionOnStage($stageId) {
        $sql = "SELECT COUNT(*) as NUM FROM HRIS_APPRAISAL_STAGE_QUESTIONS SQ
                LEFT JOIN HRIS_APPRAISAL_QUESTION AQ
                ON SQ.QUESTION_ID = AQ.QUESTION_ID
                WHERE SQ.STAGE_ID=" . $stageId . " AND AQ.STATUS='E' AND SQ.STATUS='E' AND AQ.APPRAISER_FLAG='Y'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

}
