<?php

namespace Appraisal\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Model\Months;
use Application\Repository\RepositoryInterface;
use Appraisal\Model\AppraisalAssign;
use Appraisal\Model\AppraisalStatus;
use Appraisal\Model\Setup;
use Appraisal\Model\Stage;
use Appraisal\Model\Type;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class AppraisalAssignRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AppraisalAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[AppraisalAssign::CREATED_DATE]);
        unset($data[AppraisalAssign::STATUS]);
        $this->tableGateway->update($data, [AppraisalAssign::EMPLOYEE_ID => $id[0], AppraisalAssign::APPRAISAL_ID => $id[1]]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function getDetailByEmpAppraisalId($employeeId, $appraisalId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("AA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("AA.APPRAISAL_ID AS APPRAISAL_ID"),
            new Expression("AA.STATUS AS STATUS"),
            new Expression("AA.REVIEWER_ID AS REVIEWER_ID"),
            new Expression("AA.APPRAISER_ID AS APPRAISER_ID"),
            new Expression("AA.SUPER_REVIEWER_ID AS SUPER_REVIEWER_ID"),
            new Expression("AA.ALT_APPRAISER_ID AS ALT_APPRAISER_ID"),
            new Expression("AA.ALT_REVIEWER_ID AS ALT_REVIEWER_ID"),
            new Expression("AA.REMARKS AS REMARKS"),
            new Expression("AA.CURRENT_STAGE_ID AS CURRENT_STAGE_ID")
        ]);
        $select->from(["AA" => AppraisalAssign::TABLE_NAME])
                ->join(["A" => Setup::TABLE_NAME], "A." . Setup::APPRAISAL_ID . "=AA." . AppraisalAssign::APPRAISAL_ID, ["APPRAISAL_EDESC" => new Expression("INITCAP(A.APPRAISAL_EDESC)")], "left")
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=AA." . AppraisalAssign::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")], "left")
                ->join(['S' => Stage::TABLE_NAME], "S." . Stage::STAGE_ID . "=AA." . AppraisalAssign::CURRENT_STAGE_ID, ["STAGE_EDESC" => new Expression("INITCAP(S.STAGE_EDESC)"), "STAGE_ORDER_NO" => "ORDER_NO", "STAGE_ID"])
                ->join(['E1' => HrEmployees::TABLE_NAME], "E1." . HrEmployees::EMPLOYEE_ID . "=AA." . AppraisalAssign::REVIEWER_ID, ['FIRST_NAME_R' => new Expression("INITCAP(E1.FIRST_NAME)"), "MIDDLE_NAME_R" => new Expression("INITCAP(E1.MIDDLE_NAME)"), "LAST_NAME_R" => new Expression("INITCAP(E1.LAST_NAME)"), "RETIRED_R" => HrEmployees::RETIRED_FLAG, "STATUS_R" => HrEmployees::STATUS], "left")
                ->join(['E2' => HrEmployees::TABLE_NAME], "E2." . HrEmployees::EMPLOYEE_ID . "=AA." . AppraisalAssign::APPRAISER_ID, ['FIRST_NAME_A' => new Expression("INITCAP(E2.FIRST_NAME)"), "MIDDLE_NAME_A" => new Expression("INITCAP(E2.MIDDLE_NAME)"), "LAST_NAME_A" => new Expression("INITCAP(E2.LAST_NAME)"), "RETIRED_A" => HrEmployees::RETIRED_FLAG, "STATUS_A" => HrEmployees::STATUS], "left")
                ->join(['E3' => HrEmployees::TABLE_NAME], "E3." . HrEmployees::EMPLOYEE_ID . "=AA." . AppraisalAssign::ALT_REVIEWER_ID, ['FIRST_NAME_ALT_R' => new Expression("INITCAP(E3.FIRST_NAME)"), "MIDDLE_NAME_ALT_R" => new Expression("INITCAP(E3.MIDDLE_NAME)"), "LAST_NAME_ALT_R" => new Expression("INITCAP(E3.LAST_NAME)"), "RETIRED_ALT_R" => HrEmployees::RETIRED_FLAG, "STATUS_ALT_R" => HrEmployees::STATUS], "left")
                ->join(['E4' => HrEmployees::TABLE_NAME], "E4." . HrEmployees::EMPLOYEE_ID . "=AA." . AppraisalAssign::ALT_APPRAISER_ID, ['FIRST_NAME_ALT_A' => new Expression("INITCAP(E4.FIRST_NAME)"), "MIDDLE_NAME_ALT_A" => new Expression("INITCAP(E4.MIDDLE_NAME)"), "LAST_NAME_ALT_A" => new Expression("INITCAP(E4.LAST_NAME)"), "RETIRED_ALT_A" => HrEmployees::RETIRED_FLAG, "STATUS_ALT_A" => HrEmployees::STATUS], "left")
                ->join(['E5' => HrEmployees::TABLE_NAME], "E5." . HrEmployees::EMPLOYEE_ID . "=AA." . AppraisalAssign::SUPER_REVIEWER_ID, ['FIRST_NAME_SUPER_R' => new Expression("INITCAP(E5.FIRST_NAME)"), "MIDDLE_NAME_SUPER_R" => new Expression("INITCAP(E5.MIDDLE_NAME)"), "LAST_NAME_SUPER_R" => new Expression("INITCAP(E5.LAST_NAME)"), "RETIRED_SUPER_R" => HrEmployees::RETIRED_FLAG, "STATUS_SUPER_R" => HrEmployees::STATUS], "left");

        $select->where([
            "AA." . AppraisalAssign::APPRAISAL_ID . "=" . $appraisalId,
            "AA." . AppraisalAssign::EMPLOYEE_ID . "=" . $employeeId,
            "AA." . AppraisalAssign::STATUS . "='E'"
        ]);
        $select->order("E." . HrEmployees::FIRST_NAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchByEmployeeId($employeeId, $isMonthly = false) {
        $durationType = $isMonthly ? 'M' : 'A';
        $sql = "SELECT * FROM(
                SELECT A.APPRAISAL_ID                          AS APPRAISAL_ID,
                  A.APPRAISAL_TYPE_ID                          AS APPRAISAL_TYPE_ID,
                  A.STATUS                                     AS STATUS,
                  A.APPRAISAL_CODE                             AS APPRAISAL_CODE,
                  A.APPRAISAL_EDESC                            AS APPRAISAL_EDESC,
                  A.REMARKS                                    AS REMARKS,
                  INITCAP(TO_CHAR(A.START_DATE,'DD-MON-YYYY')) AS START_DATE,
                  INITCAP(TO_CHAR(A.END_DATE,'DD-MON-YYYY'))   AS END_DATE,
                  INITCAP(E.FIRST_NAME)                        AS FIRST_NAME,
                  INITCAP(E.MIDDLE_NAME)                       AS MIDDLE_NAME,
                  INITCAP(E.LAST_NAME)                         AS LAST_NAME,
                  E.EMPLOYEE_ID                                AS EMPLOYEE_ID,
                  INITCAP(T.APPRAISAL_TYPE_EDESC)              AS APPRAISAL_TYPE_EDESC,
                  INITCAP(S.STAGE_EDESC)                       AS STAGE_EDESC,
                  S.ORDER_NO                                   AS STAGE_ORDER_NO,
                  A.KPI_SETTING                                AS KPI_SETTING,
                  A.COMPETENCIES_SETTING                       AS COMPETENCIES_SETTING,
                  (SELECT COUNT(*) FROM HRIS_APPRAISAL_STAGE_QUESTIONS SQ
                LEFT JOIN HRIS_APPRAISAL_QUESTION AQ
                ON SQ.QUESTION_ID = AQ.QUESTION_ID
                WHERE SQ.STAGE_ID=AA.CURRENT_STAGE_ID AND AQ.STATUS='E' AND SQ.STATUS='E' AND AQ.APPRAISEE_FLAG='Y') AS APPRAISEE_QUESTION_NUM,
                (SELECT COUNT(*)
                    FROM HRIS_APPRAISAL_ANSWER APNS
                    WHERE APNS.EMPLOYEE_ID = E.EMPLOYEE_ID
                    AND APNS.APPRAISAL_ID  = A.APPRAISAL_ID
                    AND APNS.STATUS='E'
                    ) AS ANSWER_NUM
                FROM HRIS_APPRAISAL_SETUP A
                INNER JOIN HRIS_APPRAISAL_ASSIGN AA
                ON A.APPRAISAL_ID=AA.APPRAISAL_ID
                INNER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=AA.EMPLOYEE_ID
                INNER JOIN HRIS_APPRAISAL_TYPE T
                ON T.APPRAISAL_TYPE_ID=A.APPRAISAL_TYPE_ID
                INNER JOIN HRIS_APPRAISAL_STAGE S
                ON S.STAGE_ID       =AA.CURRENT_STAGE_ID
                WHERE AA.EMPLOYEE_ID=" . $employeeId . "
                AND AA.STATUS       ='E'
                AND E.STATUS        ='E'
                AND T.STATUS        ='E'
                AND S.STATUS        ='E'
                AND T.DURATION_TYPE ='{$durationType}'
                ORDER BY A.APPRAISAL_EDESC ASC)
                WHERE KPI_SETTING='Y' OR COMPETENCIES_SETTING='Y' OR APPRAISEE_QUESTION_NUM>0 OR ANSWER_NUM>0
";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getEmployeeAppraisalDetail($employeeId, $appraisalId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("A.APPRAISAL_ID AS APPRAISAL_ID"),
            new Expression("A.APPRAISAL_TYPE_ID AS APPRAISAL_TYPE_ID"),
            new Expression("A.STATUS AS STATUS"),
            new Expression("A.APPRAISAL_CODE AS APPRAISAL_CODE"),
            new Expression("A.APPRAISAL_EDESC AS APPRAISAL_EDESC"),
            new Expression("A.REMARKS AS REMARKS"),
            new Expression("A.KPI_SETTING AS KPI_SETTING"),
            new Expression("A.HR_FEEDBACK_ENABLE AS HR_FEEDBACK_ENABLE"),
            new Expression("A.COMPETENCIES_SETTING AS COMPETENCIES_SETTING"),
            new Expression("INITCAP(TO_CHAR(A.START_DATE,'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(A.END_DATE,'DD-MON-YYYY')) AS END_DATE"),
        ]);
        $select->from(["A" => Setup::TABLE_NAME])
                ->join(["AA" => AppraisalAssign::TABLE_NAME], "A." . Setup::APPRAISAL_ID . "=AA." . AppraisalAssign::APPRAISAL_ID, [AppraisalAssign::APPRAISAL_ID, AppraisalAssign::APPRAISER_ID, AppraisalAssign::REVIEWER_ID, AppraisalAssign::CURRENT_STAGE_ID, AppraisalAssign::ALT_APPRAISER_ID, AppraisalAssign::ALT_REVIEWER_ID, AppraisalAssign::SUPER_REVIEWER_ID])
                ->join(["APS" => AppraisalStatus::TABLE_NAME], "APS." . AppraisalStatus::APPRAISAL_ID . "=AA." . AppraisalAssign::APPRAISAL_ID . " AND APS." . AppraisalStatus::EMPLOYEE_ID . "=AA." . AppraisalAssign::EMPLOYEE_ID, [AppraisalStatus::ANNUAL_RATING_KPI, AppraisalStatus::ANNUAL_RATING_COMPETENCY, AppraisalStatus::APPRAISER_OVERALL_RATING, AppraisalStatus::REVIEWER_AGREE, AppraisalStatus::APPRAISEE_AGREE, AppraisalStatus::APPRAISED_BY, AppraisalStatus::REVIEWED_BY, AppraisalStatus::DEFAULT_RATING, AppraisalStatus::REVIEW_PERIOD, AppraisalStatus::PREVIOUS_REVIEW_PERIOD, AppraisalStatus::PREVIOUS_RATING, AppraisalStatus::SUPER_REVIEWER_AGREE,
                    AppraisalStatus::SUPER_REVIEWER_FEEDBACK,
                    AppraisalStatus::HR_FEEDBACK,
                    AppraisalStatus::HR_STRENGTH,
                    AppraisalStatus::HR_WEAKNESS,
                    AppraisalStatus::HR_AREAS_OF_IMPROVEMENT,
                    AppraisalStatus::HR_STEPS_FOR_IMPROVEMENT
                        ])
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=AA." . AppraisalAssign::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)"), HrEmployees::EMPLOYEE_ID])
                ->join(['T' => Type::TABLE_NAME], "T." . Type::APPRAISAL_TYPE_ID . "=A." . Setup::APPRAISAL_TYPE_ID, ["APPRAISAL_TYPE_EDESC" => new Expression("INITCAP(T.APPRAISAL_TYPE_EDESC)")])
                ->join(['S' => Stage::TABLE_NAME], "S." . Stage::STAGE_ID . "=AA." . AppraisalAssign::CURRENT_STAGE_ID, ["STAGE_EDESC" => new Expression("INITCAP(S.STAGE_EDESC)"), "STAGE_ORDER_NO" => "ORDER_NO", "STAGE_ID"])
                ->join(['E1' => HrEmployees::TABLE_NAME], "E1." . HrEmployees::EMPLOYEE_ID . "=AA." . AppraisalAssign::APPRAISER_ID, ["FIRST_NAME_A" => new Expression("INITCAP(E1.FIRST_NAME)"), "MIDDLE_NAME_A" => new Expression("INITCAP(E1.MIDDLE_NAME)"), "LAST_NAME_A" => new Expression("INITCAP(E1.LAST_NAME)"), "EMPLOYEE_ID_A" => HrEmployees::EMPLOYEE_ID], "left")
                ->join(['E2' => HrEmployees::TABLE_NAME], "E2." . HrEmployees::EMPLOYEE_ID . "=AA." . AppraisalAssign::REVIEWER_ID, ["FIRST_NAME_R" => new Expression("INITCAP(E2.FIRST_NAME)"), "MIDDLE_NAME_R" => new Expression("INITCAP(E2.MIDDLE_NAME)"), "LAST_NAME_R" => new Expression("INITCAP(E2.LAST_NAME)"), "EMPLOYEE_ID_R" => HrEmployees::EMPLOYEE_ID], "left")
                ->join(['DES1' => Designation::TABLE_NAME], "DES1." . Designation::DESIGNATION_ID . "=E1." . HrEmployees::DESIGNATION_ID, ["DESIGNATION_NAME_A" => new Expression("(DES1.DESIGNATION_TITLE)")], "left")
                ->join(['DES2' => Designation::TABLE_NAME], "DES2." . Designation::DESIGNATION_ID . "=E2." . HrEmployees::DESIGNATION_ID, ["DESIGNATION_NAME_R" => new Expression("(DES2.DESIGNATION_TITLE)")], "left")
                ->join(['POS1' => Position::TABLE_NAME], "POS1." . Position::POSITION_ID . "=E1." . HrEmployees::POSITION_ID, ["POSITION_NAME_A" => new Expression("(POS1.POSITION_NAME)")], "left")
                ->join(['POS2' => Position::TABLE_NAME], "POS2." . Position::POSITION_ID . "=E2." . HrEmployees::POSITION_ID, ["POSITION_NAME_R" => new Expression("(POS2.POSITION_NAME)")], "left")
                ->join(['MTH' => Months::TABLE_NAME],"MTH.FISCAL_YEAR_ID = A.FISCAL_YEAR_ID AND (MTH.FISCAL_YEAR_MONTH_NO = CASE WHEN A.FISCAL_YEAR_MONTH_NO IS NOT NULL THEN A.FISCAL_YEAR_MONTH_NO END OR 1=1)",["FROM_DATE"=>new Expression("INITCAP(TO_CHAR(MTH.FROM_DATE,'DD-MON-YYYY'))")]);

        $select->where([
            "AA." . AppraisalAssign::EMPLOYEE_ID . "=" . $employeeId,
            "AA." . AppraisalAssign::APPRAISAL_ID . "=" . $appraisalId,
            "AA." . AppraisalAssign::STATUS . "='E'",
            "E." . HrEmployees::STATUS . "='E'",
            "T." . Type::STATUS . "='E'",
            "S." . Stage::STATUS . "='E' AND
              (((E1.STATUS =
                CASE
                  WHEN E1.STATUS IS NOT NULL
                  THEN ('E')
                END
              OR E1.STATUS IS NULL)
              AND
              (E1.RETIRED_FLAG =
                CASE
                  WHEN E1.RETIRED_FLAG IS NOT NULL
                  THEN ('N')
                END
              OR E1.RETIRED_FLAG IS NULL))
            OR
              ((E2.STATUS =
                CASE
                  WHEN E2.STATUS IS NOT NULL
                  THEN ('E')
                END
              OR E2.STATUS IS NULL)
            AND
              (E2.RETIRED_FLAG =
                CASE
                  WHEN E2.RETIRED_FLAG IS NOT NULL
                  THEN ('N')
                END
              OR E2.RETIRED_FLAG IS NULL)))"
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function updateCurrentStageByAppId($stageId, $appraisalId, $employeeId = null) {
        if ($employeeId == null) {
            $empCase = [];
        } else {
            $empCase = [AppraisalAssign::EMPLOYEE_ID => $employeeId];
        }
        $this->tableGateway->update([AppraisalAssign::CURRENT_STAGE_ID => $stageId], array_merge([AppraisalAssign::APPRAISAL_ID => $appraisalId, AppraisalAssign::STATUS => 'E'], $empCase));
    }

    public function getDurationType($appraisalId) {
        $sql = "SELECT T.DURATION_TYPE
                FROM HRIS_APPRAISAL_SETUP S
                JOIN HRIS_APPRAISAL_TYPE T
                ON (S.APPRAISAL_TYPE_ID = T.APPRAISAL_TYPE_ID) WHERE S.APPRAISAL_ID= {$appraisalId}";
        $rowset = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rowset->current();
    }

}
