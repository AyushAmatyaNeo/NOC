<?php

namespace SelfService\Repository;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\TrainingRequest;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class TrainingRequestRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TrainingRequest::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
        $new = $model->getArrayCopyForDB();
        if($model->status == 'AP') {
            EntityHelper::rawQueryResult($this->adapter, "
                CALL
                    HRIS_REATTENDANCE({$new['START_DATE']->getExpression()},{$new['EMPLOYEE_ID']},{$new['END_DATE']->getExpression()});
                ");
        }
    }

    public function delete($id) {
        $currentDate = Helper::getcurrentExpressionDate();
        $this->tableGateway->update([TrainingRequest::STATUS => 'C', TrainingRequest::MODIFIED_DATE => $currentDate], [TrainingRequest::REQUEST_ID => $id]);
        $rewardSql = "
        DO BEGIN
            DECLARE V_TRAINING_ID DECIMAL;
            DECLARE V_START_DATE DATE;
            DECLARE V_END_DATE DATE;
            DECLARE V_EMPLOYEE_ID DECIMAL;
            DECLARE V_STATUS VARCHAR(2);
            DECLARE V_REQUEST_ID DECIMAL:= {$id};
            DECLARE V_ASSIGNED CHAR(1);
            DECLARE V_DURATION INTEGER;
            DECLARE i INTEGER;
            
        BEGIN
          SELECT TRAINING_ID,
            START_DATE,
            END_DATE,
            EMPLOYEE_ID,
            STATUS,
            DURATION
          INTO V_TRAINING_ID,
            V_START_DATE,
            V_END_DATE,
            V_EMPLOYEE_ID,
            V_STATUS,
            V_DURATION
          FROM HRIS_EMPLOYEE_TRAINING_REQUEST
          WHERE REQUEST_ID =V_REQUEST_ID;
          
          IF V_TRAINING_ID IS NOT NULL THEN
            SELECT (
              CASE
                WHEN COUNT(*)>0
                THEN 'Y'
                ELSE 'N'
              END)
            INTO V_ASSIGNED
            FROM HRIS_EMPLOYEE_TRAINING_ASSIGN
            WHERE TRAINING_ID = V_TRAINING_ID
            AND EMPLOYEE_ID   =V_EMPLOYEE_ID;
            END IF;
           IF V_ASSIGNED    ='N' AND V_STATUS='AP' THEN
              INSERT
              INTO HRIS_EMPLOYEE_TRAINING_ASSIGN
                (
                  TRAINING_ID,
                  EMPLOYEE_ID,
                  STATUS,
                  CREATED_DATE,
                  CREATED_BY
                )
                VALUES
                (
                  V_TRAINING_ID,
                  V_EMPLOYEE_ID,
                  'E',
                  CURRENT_DATE,
                  V_EMPLOYEE_ID
                );
                
            END IF;
          -- TO DELETE IF ASSIGNED
            IF(V_ASSIGNED ='Y' AND V_STATUS='C')
            THEN
            DELETE FROM HRIS_EMPLOYEE_TRAINING_ASSIGN WHERE 
            TRAINING_ID=V_TRAINING_ID AND EMPLOYEE_ID=V_EMPLOYEE_ID;
            END IF;
            IF V_STATUS IN ('AP','C') AND  days_between(current_date, v_start_date)<1 THEN
                HRIS_REATTENDANCE(V_START_DATE,V_EMPLOYEE_ID,V_END_DATE);
            END IF;
             BEGIN
        DELETE  FROM  HRIS_EMP_TRAINING_ATTENDANCE WHERE
        TRAINING_ID=V_TRAINING_ID AND EMPLOYEE_ID=V_EMPLOYEE_ID;
        END;
        
        FOR i IN 0..V_DURATION - 1 DO

            --DBMS_OUTPUT.PUT_LINE(V_START_DATE+i);
         INSERT INTO HRIS_EMP_TRAINING_ATTENDANCE VALUES
         (V_TRAINING_ID,V_EMPLOYEE_ID,add_days(V_START_DATE,i),'P');
        END FOR;
         
           HRIS_TRAINING_LEAVE_REWARD(V_EMPLOYEE_ID,V_TRAINING_ID);
         
     END;
     END;";

            // echo"<pre>";print_r($rewardSql);die;
        EntityHelper::rawQueryResult($this->adapter, $rewardSql);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TR.REQUEST_ID AS REQUEST_ID"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD"),
            new Expression("BS_DATE((TR.REQUESTED_DATE)) AS REQUESTED_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE_AD"),
            new Expression("BS_DATE((TR.START_DATE)) AS START_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE_AD"),
            new Expression("BS_DATE((TR.END_DATE)) AS END_DATE_BS"),
            new Expression("TR.TRAINING_ID AS TRAINING_ID"),
            new Expression("TR.TITLE AS TITLE"),
            new Expression("TR.DESCRIPTION AS DESCRIPTION"),
            new Expression("TR.DURATION AS DURATION"),
            new Expression("TR.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("(CASE WHEN TR.TRAINING_TYPE = 'CC' THEN 'Company Contribution' ELSE 'Personal' END) AS TRAINING_TYPE_DETAIL"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("LEAVE_STATUS_DESC(TR.STATUS) AS STATUS_DETAIL"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"),
            new Expression("(CASE WHEN TR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT"),
            new Expression("(CASE WHEN TR.STATUS IN ('RQ','RC','AP') THEN 'Y' ELSE 'N' END) AS ALLOW_DELETE"),
                ], true);

        $select->from(['TR' => TrainingRequest::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'E.EMPLOYEE_ID=TR.EMPLOYEE_ID', ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['E3' => "HRIS_EMPLOYEES"], "E3.EMPLOYEE_ID=TR.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E3.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=TR.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");

        $select->where(["TR.REQUEST_ID" => $id]);
        $select->order(["TR.REQUESTED_DATE" => Select::ORDER_DESCENDING]);
        $statement = $sql->prepareStatementForSqlObject($select);
        
        // echo"<pre>";print_r($statement->getSql());die;
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllByEmployeeId($employeeId): array {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TR.REQUEST_ID AS REQUEST_ID"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD"),
            new Expression("BS_DATE((TR.REQUESTED_DATE)) AS REQUESTED_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE_AD"),
            new Expression("BS_DATE((TR.START_DATE)) AS START_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE_AD"),
            new Expression("BS_DATE((TR.END_DATE)) AS END_DATE_BS"),
            new Expression("TR.TRAINING_ID AS TRAINING_ID"),
            new Expression("TR.TITLE AS TITLE"),
            new Expression("TR.DESCRIPTION AS DESCRIPTION"),
            new Expression("TR.DURATION AS DURATION"),
            new Expression("TR.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("(CASE WHEN TR.TRAINING_TYPE = 'CC' THEN 'Company Contribution' ELSE 'Personal' END) AS TRAINING_TYPE_DETAIL"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("LEAVE_STATUS_DESC(TR.STATUS) AS STATUS_DETAIL"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"),
            new Expression("(CASE WHEN TR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT"),
            new Expression("(CASE WHEN TR.STATUS IN ('RQ','RC','AP') THEN 'Y' ELSE 'N' END) AS ALLOW_DELETE"),
                ], true);

        $select->from(['TR' => TrainingRequest::TABLE_NAME])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'E.EMPLOYEE_ID=TR.EMPLOYEE_ID', ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['E3' => "HRIS_EMPLOYEES"], "E3.EMPLOYEE_ID=TR.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E3.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=TR.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");
        $select->where([
            "E.EMPLOYEE_ID" => $employeeId
        ]);
        $select->order("TR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo"<pre>";print_r($statement->getSql());die;
        $result = $statement->execute();
        return iterator_to_array($result, false);
    }

}
