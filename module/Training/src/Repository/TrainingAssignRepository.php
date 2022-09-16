<?php

namespace Training\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\HrEmployees;
use Setup\Model\Institute;
use Setup\Model\Training;
use Training\Model\TrainingAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;
use Application\Helper\EntityHelper;
use SelfService\Model\TrainingFeedback;

class TrainingAssignRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;
    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(TrainingAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
        $this->leaveReward($model->employeeId, $model->trainingId);
    }

    public function delete($id) {
        $this->tableGateway->update([TrainingAssign::STATUS => 'D'], [TrainingAssign::EMPLOYEE_ID => $id[0], TrainingAssign::TRAINING_ID => $id[1]]);
        $boundedParameter = [];
        
        $boundedParameter['id0'] = $id[0];
        $boundedParameter['id1'] = $id[1];
        $this->executeStatement("CALL  HRIS_TRAINING_LEAVE_REWARD(?,?);", $boundedParameter);
    }

    public function getDetailByEmployeeID($employeeId, $trainingId) {
        $boundedParams = [];
        $boundedParams['employeeId'] = $employeeId;
        $boundedParams['trainingId'] = $trainingId;

        
        $sql = "SELECT TA.TRAINING_ID AS TRAINING_ID, TA.EMPLOYEE_ID AS EMPLOYEE_ID, TA.STATUS AS STATUS, TA.REMARKS AS REMARKS, INITCAP(TO_CHAR(T.START_DATE, 'DD-MON-YYYY')) AS START_DATE, INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY')) AS END_DATE, T.TRAINING_ID AS TRAINING_ID, T.TRAINING_CODE AS TRAINING_CODE, T.DURATION AS DURATION, INITCAP(T.TRAINING_NAME) AS TRAINING_NAME, INITCAP(T.INSTRUCTOR_NAME) AS INSTRUCTOR_NAME, T.REMARKS AS REMARKS, T.TRAINING_TYPE AS TRAINING_TYPE, INITCAP(I.INSTITUTE_NAME) AS INSTITUTE_NAME, I.LOCATION AS LOCATION, I.EMAIL AS EMAIL, I.TELEPHONE AS TELEPHONE, INITCAP(E.FIRST_NAME) AS FIRST_NAME, INITCAP(E.MIDDLE_NAME) AS MIDDLE_NAME, INITCAP(E.LAST_NAME) AS LAST_NAME FROM HRIS_EMPLOYEE_TRAINING_ASSIGN  TA LEFT JOIN HRIS_TRAINING_MASTER_SETUP  T ON T.TRAINING_ID=TA.TRAINING_ID LEFT JOIN HRIS_INSTITUTE_MASTER_SETUP  I ON I.INSTITUTE_ID=T.INSTITUTE_ID LEFT JOIN HRIS_EMPLOYEES  E ON E.EMPLOYEE_ID=TA.EMPLOYEE_ID WHERE TA.EMPLOYEE_ID= ?  AND TA.TRAINING_ID= ?  AND TA.STATUS='E'";

        // echo"<pre>";print_r($employeeId);die;
        
        return $this->rawQuery($sql, $boundedParams);
        
    }

    public function getAllTrainingList($employeeId) {
        $boundedParams = [];
        $boundedParams['employeeId'] = $employeeId;
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TA.TRAINING_ID AS TRAINING_ID"),
            new Expression("TA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TA.STATUS AS STATUS"),
            new Expression("TA.ACCEPT_FLAG AS ACCEPT_FLAG"),
            new Expression("TA.REMARKS AS REMARKS"),
            new Expression("INITCAP(TO_CHAR(T." . Training::START_DATE . ", 'DD-MON-YYYY')) AS START_DATE_AD"),
            new Expression("BS_DATE((T." . Training::START_DATE . ")) AS START_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(T." . Training::END_DATE . ", 'DD-MON-YYYY')) AS END_DATE_AD"),
            new Expression("BS_DATE((T." . Training::END_DATE . ")) AS END_DATE_BS")
                ], true);
        $select->from(['TA' => TrainingAssign::TABLE_NAME]);
        $select->join(['T' => Training::TABLE_NAME], "T." . Training::TRAINING_ID . "=TA." . TrainingAssign::TRAINING_ID, [Training::TRAINING_ID, Training::TRAINING_CODE, Training::DURATION, "TRAINING_NAME" => new Expression("INITCAP(T.TRAINING_NAME)"), "INSTRUCTOR_NAME" => new Expression("INITCAP(T.INSTRUCTOR_NAME)"), Training::REMARKS, Training::TRAINING_TYPE], "left")
                ->join(['I' => Institute::TABLE_NAME], "I." . Institute::INSTITUTE_ID . "=T." . Training::INSTITUTE_ID, ["INSTITUTE_NAME" => new Expression("INITCAP(I.INSTITUTE_NAME)"), Institute::LOCATION, Institute::EMAIL, Institute::TELEPHONE], "left");

        $select->where([
            "TA.EMPLOYEE_ID " => $employeeId,
            "TA.STATUS = 'E'",
            
        ]);

        
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo"<pre>";print_r($statement->getSql());die;
        $result = $statement->execute($boundedParams);
        return $result;
    }

    public function getAllTrainingListAccepted($employeeId) {
        $boundedParams = [];
        $boundedParams['employeeId'] = $employeeId;
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TA.TRAINING_ID AS TRAINING_ID"),
            new Expression("TA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TA.STATUS AS STATUS"),
            new Expression("TA.ACCEPT_FLAG AS ACCEPT_FLAG"),
            new Expression("TA.REMARKS AS REMARKS"),
            new Expression("INITCAP(TO_CHAR(T." . Training::START_DATE . ", 'DD-MON-YYYY')) AS START_DATE_AD"),
            new Expression("BS_DATE((T." . Training::START_DATE . ")) AS START_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(T." . Training::END_DATE . ", 'DD-MON-YYYY')) AS END_DATE_AD"),
            new Expression("BS_DATE((T." . Training::END_DATE . ")) AS END_DATE_BS")
                ], true);
        $select->from(['TA' => TrainingAssign::TABLE_NAME]);
        $select->join(['T' => Training::TABLE_NAME], "T." . Training::TRAINING_ID . "=TA." . TrainingAssign::TRAINING_ID, [Training::TRAINING_ID, Training::TRAINING_CODE, Training::DURATION, "TRAINING_NAME" => new Expression("INITCAP(T.TRAINING_NAME)"), "INSTRUCTOR_NAME" => new Expression("INITCAP(T.INSTRUCTOR_NAME)"), Training::REMARKS, Training::TRAINING_TYPE], "left")
                ->join(['I' => Institute::TABLE_NAME], "I." . Institute::INSTITUTE_ID . "=T." . Training::INSTITUTE_ID, ["INSTITUTE_NAME" => new Expression("INITCAP(I.INSTITUTE_NAME)"), Institute::LOCATION, Institute::EMAIL, Institute::TELEPHONE], "left")
                ->join(['TF' => TrainingFeedback::TABLE_NAME], "TF." . TrainingFeedback::TRAINING_ID . "=T." . Training::TRAINING_ID, ["TRAINING_FEEDBACK" => new Expression("INITCAP(TF.TRAINING_FEEDBACK)")], "left");

        $select->where([
            "TA.EMPLOYEE_ID " => $employeeId ,
            "TA.STATUS = 'E'",
            "TA.ACCEPT_FLAG = 'Y'"
        ]);

        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParams);
        return $result;
    }

    public function getAllDetailByEmployeeID($employeeId, $trainingId) {
        // $boundedParams = [];
        // $boundedParams['employeeId'] = $employeeId;
        // $boundedParams['trainingId'] = $trainingId;

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TA.TRAINING_ID AS TRAINING_ID"),
            new Expression("TA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("TA.STATUS AS STATUS"),
            new Expression("TA.REMARKS AS REMARKS"),
            new Expression("INITCAP(TO_CHAR(T." . Training::START_DATE . ", 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(T." . Training::END_DATE . ", 'DD-MON-YYYY')) AS END_DATE")
                ], true);
        $select->from(['TA' => TrainingAssign::TABLE_NAME]);
        $select->join(['T' => Training::TABLE_NAME], "T." . Training::TRAINING_ID . "=TA." . TrainingAssign::TRAINING_ID, [Training::TRAINING_ID, Training::TRAINING_CODE, Training::DURATION, "TRAINING_NAME" => new Expression("INITCAP(T.TRAINING_NAME)"), "INSTRUCTOR_NAME" => new Expression("INITCAP(T.INSTRUCTOR_NAME)"), Training::REMARKS, Training::TRAINING_TYPE], "left")
                ->join(['I' => Institute::TABLE_NAME], "I." . Institute::INSTITUTE_ID . "=T." . Training::INSTITUTE_ID, ["INSTITUTE_NAME" => new Expression("INITCAP(I.INSTITUTE_NAME)")], "left");

        $select->where([
            "TA.EMPLOYEE_ID= $employeeId",
            "TA.TRAINING_ID= $trainingId"
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo"<pre>";print_r($statement->getSql());die;
        $result = $statement->execute();
        return $result;


        // $sql = "SELECT TA.TRAINING_ID AS TRAINING_ID, TA.EMPLOYEE_ID AS EMPLOYEE_ID, TA.STATUS AS STATUS, TA.REMARKS AS REMARKS, INITCAP(TO_CHAR(T.START_DATE, 'DD-MON-YYYY')) AS START_DATE, INITCAP(TO_CHAR(T.END_DATE, 'DD-MON-YYYY')) AS END_DATE, T.TRAINING_ID AS TRAINING_ID, T.TRAINING_CODE AS TRAINING_CODE, T.DURATION AS DURATION, INITCAP(T.TRAINING_NAME) AS TRAINING_NAME, INITCAP(T.INSTRUCTOR_NAME) AS INSTRUCTOR_NAME, T.REMARKS AS REMARKS, T.TRAINING_TYPE AS TRAINING_TYPE, INITCAP(I.INSTITUTE_NAME) AS INSTITUTE_NAME FROM HRIS_EMPLOYEE_TRAINING_ASSIGN  TA LEFT JOIN HRIS_TRAINING_MASTER_SETUP  T ON T.TRAINING_ID=TA.TRAINING_ID LEFT JOIN HRIS_INSTITUTE_MASTER_SETUP  I ON I.INSTITUTE_ID=T.INSTITUTE_ID WHERE TA.EMPLOYEE_ID= ? AND TA.TRAINING_ID= ?";
        // return $this->rawQuery($sql, $boundedParams);


    }

    public function filterRecords($search) {
//        $condition = "";
        $condition = EntityHelper::getSearchConditonBounded($search['companyId'], $search['branchId'], $search['departmentId'], $search['positionId'], $search['designationId'], $search['serviceTypeId'], $search['serviceEventTypeId'], $search['employeeTypeId'], $search['employeeId']);

        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $condition['parameter']);

        if (isset($search['trainingId']) && $search['trainingId'] != null && $search['trainingId'] != -1) {
            if (gettype($search['trainingId']) === 'array') {
//                $csv = "";
//                for ($i = 0; $i < sizeof($search['trainingId']); $i++) {
//                    if ($i == 0) {
//                        $csv = "'{$search['trainingId'][$i]}'";
//                    } else {
//                        $csv .= ",'{$search['trainingId'][$i]}'";
//                    }
//                }
//                $condition['sql'] .= "AND TA.TRAINING_ID IN ({$csv})";
            } else {
                $condition['sql'] .= " AND TA.TRAINING_ID IN (:trainingId)";
                $boundedParameter['trainingId'] = $search['trainingId'];
            }
        }
 
        $sql = "SELECT TA.TRAINING_ID,
                  TMS.TRAINING_CODE,
                  TMS.TRAINING_NAME,
                  TMS.TRAINING_TYPE,
                  E.EMPLOYEE_CODE AS EMPLOYEE_CODE,
                  (
                  CASE
                    WHEN TMS.TRAINING_TYPE = 'CC'
                    THEN 'Company Contribution'
                    ELSE 'Personal'
                  END)                                  AS TRAINING_TYPE_DETAIL,
                  TO_CHAR(TMS.START_DATE,'DD-MON-YYYY') AS START_DATE,
                  TO_CHAR(TMS.START_DATE,'DD-MON-YYYY') AS START_DATE_AD,
                  BS_DATE(TMS.START_DATE)               AS START_DATE_BS,
                  TO_CHAR(TMS.END_DATE,'DD-MON-YYYY')   AS END_DATE,
                  TO_CHAR(TMS.END_DATE,'DD-MON-YYYY')   AS END_DATE_AD,
                  BS_DATE(TMS.END_DATE)                 AS END_DATE_BS,
                  TA.EMPLOYEE_ID,
                  E.FULL_NAME AS EMPLOYEE_NAME,
                  'Y' AS ALLOW_VIEW,
                  'Y' AS ALLOW_DELETE
                FROM HRIS_EMPLOYEE_TRAINING_ASSIGN TA
                LEFT JOIN HRIS_TRAINING_MASTER_SETUP TMS
                ON (TA.TRAINING_ID= TMS.TRAINING_ID)
                LEFT JOIN HRIS_EMPLOYEES E
                ON (TA.EMPLOYEE_ID=E.EMPLOYEE_ID)
                WHERE 1=1 AND TA.STATUS='E' 
                {$condition['sql']} ORDER BY TMS.TRAINING_NAME,E.FULL_NAME";

        return $this->rawQuery($sql, $boundedParameter);
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [TrainingAssign::EMPLOYEE_ID => $id[0], TrainingAssign::TRAINING_ID => $id[1]]);
        $this->leaveReward($id[0], $id[1]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function checkEmployeeTraining(int $employeeId, Expression $date) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([TrainingAssign::TRAINING_ID]);
        $select->from(['TA' => TrainingAssign::TABLE_NAME])
                ->join(['T' => Training::TABLE_NAME], "TA." . TrainingAssign::TRAINING_ID . " = " . "T." . Training::TRAINING_ID, []);
        $select->where(["TA." . TrainingAssign::EMPLOYEE_ID . "=:employeeId"]);
        $select->where(["TA." . TrainingAssign::STATUS . "= 'E'"]);
        $select->where([$date->getExpression() . " BETWEEN " . "T." . Training::START_DATE . " AND T." . Training::END_DATE]);
        $boundedParams = [];
        $boundedParams['employeeId'] = $employeeId;
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParams);
        return $result->current();
    }
    
    public function leaveReward($employeeId,$trainingId){
        $boundedParams = [];
        $boundedParams['employeeId'] = $employeeId;
        $boundedParams['trainingId'] = $trainingId;
        $sql="  DECLARE V_EMPLOYEE_ID NUMBER(7,0):= ?;
                DECLARE V_TRAINING_ID NUMBER(7,0):= ?;
                DECLARE V_START_DATE DATE;
                DECLARE V_END_DATE DATE;
                DECLARE V_DURATION NUMBER;
                
                CALL
                SELECT START_DATE,END_DATE,DURATION
                INTO V_START_DATE,V_END_DATE,V_DURATION
                FROM HRIS_TRAINING_MASTER_SETUP WHERE TRAINING_ID=V_TRAINING_ID;

                DBMS_OUTPUT.PUT_LINE(V_START_DATE);
                DBMS_OUTPUT.PUT_LINE(V_END_DATE);
                DBMS_OUTPUT.PUT_LINE(V_DURATION);


                CALL
                DELETE  FROM  HRIS_EMP_TRAINING_ATTENDANCE WHERE
                TRAINING_ID=V_TRAINING_ID AND EMPLOYEE_ID=V_EMPLOYEE_ID;
                 FOR i IN 0..v_duration - 1 LOOP

                    DBMS_OUTPUT.PUT_LINE(V_START_DATE+i);
                 INSERT INTO HRIS_EMP_TRAINING_ATTENDANCE VALUES
                 (V_TRAINING_ID,V_EMPLOYEE_ID,V_START_DATE+i,'P');


                 END LOOP;

                 CALL
                 HRIS_REATTENDANCE(V_START_DATE,V_EMPLOYEE_ID,V_END_DATE);
                 HRIS_TRAINING_LEAVE_REWARD(V_EMPLOYEE_ID,V_TRAINING_ID);

                ";
//        $statement = $this->adapter->query($sql);

        // echo"<pre>";print_r($sql);die;
    return $this->rawQuery($sql, $boundedParams);
    }
    
    public function updateAcceptFlag($employeeId, $trainingId, $flag){
        $sql = "update HRIS_EMPLOYEE_TRAINING_ASSIGN set accept_flag = '".$flag."' where 
        employee_id = $employeeId and training_id = $trainingId";
        return $this->executeStatement($sql);
    }

}
