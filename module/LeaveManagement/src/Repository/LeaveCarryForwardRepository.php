<?php

namespace LeaveManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Repository\HrisRepository;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Model\LeaveAssign;
use LeaveManagement\Model\LeaveCarryForward;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LeaveCarryForwardRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;
    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveCarryForward::TABLE_NAME, $adapter);
        $this->tableGatewayLeaveAssign = new TableGateway(LeaveAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    
    public function fetchCarryForward($id)
    {
        $boundedParameter = [];
        $employeeCondition='';
        if(!empty($id)){
            $empData=$this->getBoundedForArray($id,'employeeId');
                $boundedParameter=array_merge($boundedParameter,$empData['parameter']);
                $employeeCondition = " AND HE.EMPLOYEE_ID IN ({$empData['sql']})";

        }
       $sql = "SELECT HE.ID AS ID, HS.LEAVE_ENAME AS LEAVE_ENAME,HR.FULL_NAME AS FULL_NAME,HE.ENCASH_DAYS,HE.CARRY_FORWARD_DAYS from HRIS_EMP_SELF_LEAVE_CLOSING HE join "
                       . " HRIS_LEAVE_MASTER_SETUP HS on (HE.LEAVE_ID=HS.LEAVE_ID) "
               . "join HRIS_EMPLOYEES HR ON(HE.EMPLOYEE_ID=HR.EMPLOYEE_ID) WHERE HS.STATUS='E' {$employeeCondition}";

        $statement = $this->adapter->query($sql);
        return $statement->execute($boundedParameter);
    }

   

   
    public function carryForward($data) {
        if(!empty($data)){
            $createdDate=$data -> createdDate;
            $carryforward=  $data->carryforward;
            $employeeId= $data->employeeId;
            $encashment= $data->encashment;
            $leaveId=$data->leaveId;

            $id=((int) Helper::getMaxId($this->adapter, 'HRIS_EMP_SELF_LEAVE_CLOSING', 'ID')) + 1;
            $sql = "DO
                    BEGIN
                        INSERT INTO HRIS_EMP_SELF_LEAVE_CLOSING 
                        (EMPLOYEE_ID,LEAVE_ID,ENCASH_DAYS,CARRY_FORWARD_DAYS,CREATED_DATE,STATUS,ID)
                        VALUES ({$employeeId} , {$leaveId} ,{$encashment},{$carryforward},CURRENT_DATE,'E',{$id}); 
                        HRIS_RECALCULATE_LEAVE( {$employeeId},{$leaveId});
                    END;";
            $statement = $this->adapter->query($sql);
            $statement->execute();

        }
    }

    
    public function add(Model $model) {
        
        $this->tableGateway->insert($model->getArrayCopyForDB());
        
        $this->linkLeaveWithFiles();
    }

    public function edit(Model $model, $id) {
        // TODO: Implement edit() method.
    }
    
    public function editCarryForward($data){
        if(!empty($data)){
            $carryforward=  $data['carryforward'];
            $encashment= $data['encashment'];
            $id=$data['id'];
            $dataIds = $this->getIds($id);
            $leaveId = $dataIds[0]['LEAVE_ID'];
            $employeeId = $dataIds[0]['EMPLOYEE_ID'];
           
//           $boundedParameter = [];
//        $boundedParameter['encashment'] = $encashment;
//        $boundedParameter['carryforward'] = $carryforward;
            $sql = "DO
                   BEGIN
                   UPDATE HRIS_EMP_SELF_LEAVE_CLOSING SET ENCASH_DAYS = {$encashment}, CARRY_FORWARD_DAYS = {$carryforward}, 
                       MODIFED_DT = CURRENT_DATE WHERE ID = {$id}; 
                   HRIS_RECALCULATE_LEAVE({$employeeId},{$leaveId});
                   END;";

            $statement = $this->adapter->query($sql);
            $statement->execute();
           
        }
    }
    
    public function getDetailsById($id){
        $sql = "SELECT HE.ID AS ID, HS.LEAVE_ENAME AS LEAVE_ENAME, HR.FULL_NAME AS FULL_NAME,HE.ENCASH_DAYS,HE.CARRY_FORWARD_DAYS 
                from HRIS_EMP_SELF_LEAVE_CLOSING HE 
                left join HRIS_LEAVE_MASTER_SETUP HS on (HE.LEAVE_ID=HS.LEAVE_ID) join HRIS_EMPLOYEES HR ON(HE.EMPLOYEE_ID=HR.EMPLOYEE_ID) WHERE ID = ? ";
        $boundedParameter = [];
        $boundedParameter['id'] = $id;

        return $this->rawQuery($sql, $boundedParameter);
    }
    
    public function deleteRecord($id) {
        $data = $this->getIds($id);
        
        $employeeId = $data[0]['EMPLOYEE_ID'];
        $leaveId= $data[0]['LEAVE_ID'];
        
        $sql = "BEGIN DELETE FROM HRIS_EMP_SELF_LEAVE_CLOSING WHERE ID = {$id}; 
           HRIS_RECALCULATE_LEAVE({$employeeId},{$leaveId});
                   END;";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }
    
    public function getBalance($id){
        
        $data = $this->getIds($id);
        
        $empId = $data[0]['EMPLOYEE_ID'];
        $leaveId= $data[0]['LEAVE_ID'];
        
        $sql = "select balance-(select
                IFNULL(
                sum(case when half_day='Y' then
                NO_OF_DAYS/2
                else
                NO_OF_DAYS
                end),0)
                from hris_employee_leave_request
                where employee_id={$empId} 
                and leave_id={$leaveId} and status in ('RQ','RC')) as balance
                 from HRIS_EMPLOYEE_LEAVE_ASSIGN where employee_id = $empId AND leave_id = $leaveId";

        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
    
    public function getIds($id){
        $sql = "select employee_id, leave_id from HRIS_EMP_SELF_LEAVE_CLOSING where id = ?";
        
        $boundedParameter = [];
        $boundedParameter['id'] = $id;

        return $this->rawQuery($sql, $boundedParameter);

    }
    

    public function fetchAll() {
        // TODO: Implement fetchAll() method.
    }

    //to get the all applied leave request list
    public function selectAll($employeeId) {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE"),
            new Expression("INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE"),
            new Expression("LA.STATUS AS STATUS"),
            new Expression("INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT"),
            new Expression("LA.NO_OF_DAYS AS NO_OF_DAYS"),
            new Expression("LA.ID AS ID"),
                ], true);

        $select->from(['LA' => LeaveApply::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression("INITCAP(L.LEAVE_ENAME)")]);

        $select->where([
            "L.STATUS='E'",
            "E.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->order("LA.REQUESTED_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    //to get the leave detail based on assigned employee id
    public function getLeaveDetail($employeeId, $leaveId, $startDate = null) {
        $date = "TRUNC(SYSDATE)";
        if ($startDate != null) {
            $date = "TO_DATE(:startDate,'DD-MON-YYYY')";
            $boundedParameter['startDate'] = $startDate;
        }
        $sql = "SELECT LA.EMPLOYEE_ID       AS EMPLOYEE_ID,
                  LA.BALANCE                AS BALANCE,
                  LA.FISCAL_YEAR            AS FISCAL_YEAR,
                  LA.FISCAL_YEAR_MONTH_NO   AS FISCAL_YEAR_MONTH_NO,
                  LA.LEAVE_ID               AS LEAVE_ID,
                  L.LEAVE_CODE              AS LEAVE_CODE,
                  INITCAP(L.LEAVE_ENAME)    AS LEAVE_ENAME,
                  L.ALLOW_HALFDAY           AS ALLOW_HALFDAY,
                  L.ALLOW_GRACE_LEAVE       AS ALLOW_GRACE_LEAVE,
                   CASE WHEN L.IS_SUBSTITUTE_MANDATORY='Y' AND LBP.BYPASS='N' THEN 
                  'Y'
                  ELSE
                  'N'
                  END AS IS_SUBSTITUTE_MANDATORY,
                  L.ENABLE_SUBSTITUTE       AS ENABLE_SUBSTITUTE
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                INNER JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID                =LA.LEAVE_ID
                 LEFT JOIN (
                SELECT CASE NVL(MIN(LEAVE_ID),'0') WHEN 0 
                THEN 'N'
                ELSE 'Y' 
                END AS BYPASS FROM HRIS_SUB_MAN_BYPASS 
                WHERE LEAVE_ID={$leaveId} AND EMPLOYEE_ID={$employeeId}
                ) LBP ON (1=1)
                LEFT JOIN (SELECT * FROM HRIS_LEAVE_YEARS WHERE 
                 TRUNC(SYSDATE)  BETWEEN START_DATE AND END_DATE) LY  
                 ON(1=1)
                WHERE L.STATUS               ='E'
                AND LA.EMPLOYEE_ID           =:employeeId
                AND L.LEAVE_ID               =:leaveId
                AND (LA.FISCAL_YEAR_MONTH_NO =
                  CASE
                    WHEN (L.IS_MONTHLY='Y')
                    THEN
                      (SELECT LEAVE_YEAR_MONTH_NO
                      FROM HRIS_LEAVE_MONTH_CODE
                      WHERE {$date} BETWEEN FROM_DATE AND TO_DATE
                      )
                  END
                OR LA.FISCAL_YEAR_MONTH_NO IS NULL ) 
                AND {$date} BETWEEN LY.START_DATE AND LY.END_DATE";

        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['leaveId'] = $leaveId;

        return $this->rawQuery($sql, $boundedParameter)[0];

        // $statement = $this->adapter->query($sql);
        // return $statement->execute()->current();
    }

    //to get the leave list based on assigned employee id for select option
    public function getLeaveList($employeeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['LA' => LeaveAssign::TABLE_NAME])
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression("INITCAP(L.LEAVE_ENAME)")]);
        $select->where([
            "L.STATUS='E'",
            "LA.EMPLOYEE_ID"=>$employeeId,
            "L.CARRY_FORWARD= 'Y'"
            
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);

        $resultset = $statement->execute();

        $entitiesArray = array();
        foreach ($resultset as $result) {
            $entitiesArray[$result['LEAVE_ID']] = $result['LEAVE_ENAME'];
        }
        return $entitiesArray;
    }

    public function fetchById($id) {

        // TODO: Implement fetchById() method.

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(LeaveApply::TABLE_NAME);
        $select->where([
            "ID=".$id
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultset = $statement->execute();
        return $resultset->current();
    }

    public function delete($id) {
        $leaveStatus = $this->getLeaveFrontOrBack($id);
        $currentDate = Helper::getcurrentExpressionDate();
        if ($leaveStatus['DATE_STATUS'] == 'BD' && $leaveStatus['LEAVE_STATUS'] == 'AP') {
            $this->tableGateway->update([LeaveApply::STATUS => 'CP', LeaveApply::MODIFIED_DT => $currentDate], [LeaveApply::ID => $id]);
            EntityHelper::rawQueryResult($this->adapter, "
                   DECLARE
                      V_ID HRIS_EMPLOYEE_LEAVE_REQUEST.ID%TYPE;
                      V_STATUS HRIS_EMPLOYEE_LEAVE_REQUEST.STATUS%TYPE;
                      V_START_DATE HRIS_EMPLOYEE_LEAVE_REQUEST.START_DATE%TYPE;
                      V_END_DATE HRIS_EMPLOYEE_LEAVE_REQUEST.END_DATE%TYPE;
                      V_EMPLOYEE_ID HRIS_EMPLOYEE_LEAVE_REQUEST.EMPLOYEE_ID%TYPE;
                    BEGIN
                      SELECT ID,
                        STATUS,
                        START_DATE,
                        END_DATE,
                        EMPLOYEE_ID
                      INTO V_ID,
                        V_STATUS,
                        V_START_DATE,
                        V_END_DATE,
                        V_EMPLOYEE_ID
                      FROM HRIS_EMPLOYEE_LEAVE_REQUEST
                      WHERE ID                                    = {$id};
                      IF(V_STATUS IN ('AP','C') AND V_START_DATE <=TRUNC(SYSDATE)) THEN
                        HRIS_REATTENDANCE(V_START_DATE,V_EMPLOYEE_ID,V_END_DATE);
                      END IF;
                    END;
    ");
        } else {
            $this->tableGateway->update([LeaveApply::STATUS => 'C', LeaveApply::MODIFIED_DT => $currentDate], [LeaveApply::ID => $id]);
        }
    }

    public function checkEmployeeLeave($employeeId, $date) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['L' => LeaveApply::TABLE_NAME]);
        $select->where(["L." . LeaveApply::EMPLOYEE_ID . "=$employeeId"]);
        $select->where([$date->getExpression() . " BETWEEN " . "L." . LeaveApply::START_DATE . " AND L." . LeaveApply::END_DATE]);
        $select->where(['L.' . LeaveApply::STATUS . " = " . "'AP'"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getfilterRecords($data) {
        $employeeId = $data['employeeId'];
        $leaveId = $data['leaveId'];
        $leaveRequestStatusId = $data['leaveRequestStatusId'];
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("LA.ID AS ID"),
            new Expression("LA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("LA.LEAVE_ID AS LEAVE_ID"),
            new Expression("INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_BS"),
            new Expression("LA.HALF_DAY AS HALF_DAY"),
            new Expression("(CASE WHEN (LA.HALF_DAY IS NULL OR LA.HALF_DAY = 'N') THEN 'Full Day' WHEN (LA.HALF_DAY = 'F') THEN 'First Half' ELSE 'Second Half' END) AS HALF_DAY_DETAIL"),
            new Expression("LA.GRACE_PERIOD AS GRACE_PERIOD"),
            new Expression("(CASE WHEN LA.GRACE_PERIOD = 'E' THEN 'Early' WHEN LA.GRACE_PERIOD = 'L' THEN 'Late' ELSE '-' END) AS GRACE_PERIOD_DETAIL"),
            new Expression("LA.NO_OF_DAYS AS NO_OF_DAYS"),
            new Expression("INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_BS"),
            new Expression("LA.REMARKS AS REMARKS"),
            new Expression("LA.STATUS AS STATUS"),
            new Expression("LEAVE_STATUS_DESC(LA.STATUS) AS STATUS_DETAIL"),
            new Expression("LA.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY')) AS RECOMMENDED_DT"),
            new Expression("LA.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LA.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY')) AS APPROVED_DT"),
            new Expression("LA.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("(CASE WHEN LA.STATUS = 'XX' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT"),
            new Expression("(CASE WHEN LA.STATUS IN ('RQ','RC','AP') THEN 'Y' ELSE 'N' END) AS ALLOW_DELETE"),
                ], true);

        $select->from(['LA' => LeaveApply::TABLE_NAME])
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression("INITCAP(L.LEAVE_ENAME)")])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'LA.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LA.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['E3' => "HRIS_EMPLOYEES"], "E3.EMPLOYEE_ID=LA.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E3.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");
        $select->where([
            "L.STATUS='E'",
            "E.EMPLOYEE_ID=" . $employeeId
        ]);

        if ($leaveId != null && $leaveId != -1) {
            $select->where(["LA.LEAVE_ID" => $leaveId]);
        }
        if ($leaveRequestStatusId != -1) {
            $select->where(["LA.STATUS" => $leaveRequestStatusId]);
        }
        if ($leaveRequestStatusId != 'C') {
            $select->where([
                "(TRUNC(SYSDATE)- LA.REQUESTED_DT) < (
                      CASE
                        WHEN LA.STATUS = 'C'
                        THEN 20
                        ELSE 365
                      END)"
            ]);
        }

        if ($fromDate != null) {
            $select->where("LA.START_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')");
        }
        if ($toDate != null) {
            $select->where(["LA.END_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')"]);
        }
        $select->order("LA.REQUESTED_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchAvailableDays($fromDate, $toDate, $employeeId, $halfDay, $leaveId) {
        $rawResult = EntityHelper::rawQueryResult($this->adapter, "SELECT HRIS_AVAILABLE_LEAVE_DAYS({$fromDate},{$toDate},{$employeeId},'{$leaveId}','{$halfDay}') AS AVAILABLE_DAYS FROM DUAL");
        return $rawResult->current();
    }

    public function validateLeaveRequest($fromDate, $toDate, $employeeId) {
        $rawResult = EntityHelper::rawQueryResult($this->adapter, "SELECT HRIS_VALIDATE_LEAVE_REQUEST({$fromDate},{$toDate},{$employeeId}) AS ERROR FROM DUAL");
        return $rawResult->current();
    }


    public function fetchByEmpId($employeeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['L' => LeaveApply::TABLE_NAME]);
        $select->where(["L." . LeaveApply::EMPLOYEE_ID . "=$employeeId"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
}
    public function getLeaveFrontOrBack($id) {
        $sql = "SELECT START_DATE,TRUNC(SYSDATE) AS CURDATE,
            CASE WHEN
            STATUS IN ('RQ','RC') THEN 'NA'
            ELSE STATUS
            END
            AS LEAVE_STATUS,
            START_DATE-TRUNC(SYSDATE) AS DIFF,
                CASE  WHEN 
                (START_DATE-TRUNC(SYSDATE))>0
                THEN
                'FD'
                ELSE
                'BD'
                END AS DATE_STATUS
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST WHERE ID=:id";

        $boundedParameter = [];
        $boundedParameter['id'] = $id;

        return $this->rawQuery($sql, $boundedParameter)[0];

        // $statement = $this->adapter->query($sql);
        // return $statement->execute()->current();
    }

}
