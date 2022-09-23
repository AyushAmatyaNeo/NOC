<?php

namespace SelfService\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Model\LeaveAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LeaveRequestRepository extends HrisRepository implements RepositoryInterface {

//    private $tableGateway;
//    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveApply::TABLE_NAME, $adapter);
        $this->tableGatewayLeaveAssign = new TableGateway(LeaveAssign::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function pushFileLink($data) {
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $sql = "INSERT INTO HRIS_LEAVE_FILES(FILE_ID, FILE_NAME, FILE_IN_DIR_NAME, LEAVE_ID) VALUES((SELECT IFNULL(MAX(FILE_ID),0)+1 FROM HRIS_LEAVE_FILES), '$fileName', '$fileInDir', null)";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        $sql = "SELECT * FROM HRIS_LEAVE_FILES WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM HRIS_LEAVE_FILES)";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($statement->execute());
    }

    public function linkLeaveWithFiles() {
        if (!empty($_POST['fileUploadList'])) {
            $filesList = $_POST['fileUploadList'];
            $filesList = implode(',', $filesList);

            $sql = "UPDATE HRIS_LEAVE_FILES SET LEAVE_ID = (SELECT MAX(ID) FROM HRIS_EMPLOYEE_LEAVE_REQUEST) 
                    WHERE FILE_ID IN($filesList)";
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
        $boundedParameter = [];
        $date = "CURRENT_DATE";
        if ($startDate != null && $startDate!= '') {
            $date= "TO_DATE('{$startDate}', 'DD-MON-YYYY')";
        }
        $boundedParameter['leaveId1']=$leaveId;
        $boundedParameter['employeeId1']=$employeeId;
        $boundedParameter['leaveId2']=$leaveId;
        $boundedParameter['employeeId2']=$employeeId;
        $startDateCondition = "CURRENT_DATE";
        $boundedParameter['employeeId3']=$employeeId;
        $boundedParameter['leaveId3']=$leaveId;
        $sql = "SELECT LA.EMPLOYEE_ID       AS EMPLOYEE_ID,
                  LA.BALANCE - 
                (select 
                IFNULL(sum(
                case when half_day in ('F','S')
                then
                NO_OF_DAYS/2
                else
                no_of_days
                end
                ),0)
                from hris_employee_leave_request where status in ('RQ','RC') 
                and  leave_id=? and employee_id=?)                  AS BALANCE,
                  LA.FISCAL_YEAR            AS FISCAL_YEAR,
                  LA.FISCAL_YEAR_MONTH_NO   AS FISCAL_YEAR_MONTH_NO,
                  LA.LEAVE_ID               AS LEAVE_ID,
                  L.DOCUMENT_REQUIRED       AS DOCUMENT_REQUIRED,
                  L.DOCS_COMP_DAYS          AS DOCS_COMP_DAYS,
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
                  ,L.IS_SUBSTITUTE
                  ,L.APPLY_LIMIT
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                INNER JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID                =LA.LEAVE_ID
                 LEFT JOIN (
                SELECT CASE IFNULL(MIN(LEAVE_ID),'0') WHEN 0 
                THEN 'N'
                ELSE 'Y' 
                END AS BYPASS FROM HRIS_SUB_MAN_BYPASS 
                WHERE LEAVE_ID=? AND EMPLOYEE_ID=?
                ) LBP ON (1=1)
                LEFT JOIN (SELECT * FROM HRIS_LEAVE_YEARS WHERE 
                 {$date}  BETWEEN START_DATE AND END_DATE) LY  
                 ON(1=1)
                WHERE L.STATUS               ='E'
                AND LA.EMPLOYEE_ID           =?
                AND L.LEAVE_ID               =?
                AND (LA.FISCAL_YEAR_MONTH_NO =
                  CASE
                    WHEN (L.IS_MONTHLY='Y')
                    THEN
                      (SELECT LEAVE_YEAR_MONTH_NO
                      FROM HRIS_LEAVE_MONTH_CODE
                      WHERE 
                      (select 
                       case when {$date}>max(to_date) then
                        max(to_date)
                        else 
                        {$date}
                        end
                        from HRIS_LEAVE_MONTH_CODE) 
                      BETWEEN FROM_DATE AND TO_DATE
                      )
                  END
                OR LA.FISCAL_YEAR_MONTH_NO IS NULL ) 
                ";
        $statement = $this->adapter->query($sql);
        return $statement->execute($boundedParameter)->current();
    }

    //to get the leave list based on assigned employee id for select option
    public function getLeaveList($employeeId, $selfRequest='N') {
        $selfRequestCondition = "1=1";
        if($selfRequest == 'Y'){
            $selfRequestCondition = "L.HR_ONLY = 'N'";
        }
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['LA' => LeaveAssign::TABLE_NAME])
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression("INITCAP(L.LEAVE_ENAME)")]);
        $select->where([
            "L.STATUS='E'",
            "LA.EMPLOYEE_ID"=>$employeeId,
            $selfRequestCondition
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
            "ID=" . $id
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultset = $statement->execute();
        return $resultset->current();
    }

    public function delete($id) {
        $leaveStatus = $this->getLeaveFrontOrBack($id);
        $currentDate = Helper::getcurrentExpressionDate();
        $leaveStatusAction=$leaveStatus['CANCEL_ACTION'];
        $this->tableGateway->update([LeaveApply::STATUS => $leaveStatusAction, LeaveApply::MODIFIED_DT => $currentDate], [LeaveApply::ID => $id]);
            $boundedParameter = [];
            $boundedParameter['id']=$id;
            EntityHelper::rawQueryResult($this->adapter, "
                   DECLARE
                      V_ID HRIS_EMPLOYEE_LEAVE_REQUEST.ID%TYPE;
                      V_STATUS HRIS_EMPLOYEE_LEAVE_REQUEST.STATUS%TYPE;
                      V_START_DATE HRIS_EMPLOYEE_LEAVE_REQUEST.START_DATE%TYPE;
                      V_END_DATE HRIS_EMPLOYEE_LEAVE_REQUEST.END_DATE%TYPE;
                      V_EMPLOYEE_ID HRIS_EMPLOYEE_LEAVE_REQUEST.EMPLOYEE_ID%TYPE;
                    CALL
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
                      WHERE ID                                    = ?;
                      IF(V_STATUS IN ('AP','C') AND V_START_DATE <=TRUNC(SYSDATE)) THEN
                        HRIS_REATTENDANCE(V_START_DATE,V_EMPLOYEE_ID,V_END_DATE);
                      END IF;
    ",$boundedParameter);
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
        $leaveYear = $data['leaveYear'];
        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("LA.ID AS ID"),
            new Expression("LA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("LA.LEAVE_ID AS LEAVE_ID"),
            new Expression("LA.HARDCOPY_SIGNED_FLAG AS HARDCOPY_SIGNED_FLAG"),
            new Expression("INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_AD"),
            new Expression("BS_DATE(LA.START_DATE) AS FROM_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_AD"),
            new Expression("BS_DATE(LA.END_DATE) AS TO_DATE_BS"),
            new Expression("LA.HALF_DAY AS HALF_DAY"),
            new Expression("(CASE WHEN (LA.HALF_DAY IS NULL OR LA.HALF_DAY = 'N') THEN 'Full Day' WHEN (LA.HALF_DAY = 'F') THEN 'First Half' ELSE 'Second Half' END) AS HALF_DAY_DETAIL"),
            new Expression("LA.GRACE_PERIOD AS GRACE_PERIOD"),
            new Expression("(CASE WHEN LA.GRACE_PERIOD = 'E' THEN 'Early' WHEN LA.GRACE_PERIOD = 'L' THEN 'Late' ELSE '-' END) AS GRACE_PERIOD_DETAIL"),
            new Expression("LA.NO_OF_DAYS AS NO_OF_DAYS"),
            new Expression("INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_AD"),
            new Expression("BS_DATE(LA.REQUESTED_DT) AS REQUESTED_DT_BS"),
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
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression("CASE WHEN SUB_REF_ID IS NULL THEN 
                        INITCAP(L.LEAVE_ENAME)
                        ELSE
                        INITCAP(L.LEAVE_ENAME)||'('||SLR.SUB_NAME||')'
                        END")])
                ->join(['E' => 'HRIS_EMPLOYEES'], 'LA.EMPLOYEE_ID=E.EMPLOYEE_ID', ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LA.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['E3' => "HRIS_EMPLOYEES"], "E3.EMPLOYEE_ID=LA.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E3.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left")
                ->join(['SLR' => "(SELECT 
                                    WOD_ID AS ID
                                    ,LA.EMPLOYEE_ID
                                    ,NO_OF_DAYS
                                    ,WD.FROM_DATE||' - '||WD.TO_DATE AS SUB_NAME
                                    from 
                                    HRIS_EMPLOYEE_LEAVE_ADDITION LA
                                    JOIN Hris_Employee_Work_Dayoff WD ON (LA.WOD_ID=WD.ID)
                                    UNION
                                    SELECT 
                                    WOH_ID AS ID
                                    ,LA.EMPLOYEE_ID
                                    ,NO_OF_DAYS
                                    ,H.Holiday_Ename||'-'||WH.FROM_DATE||' - '||WH.TO_DATE AS SUB_NAME
                                    from 
                                    HRIS_EMPLOYEE_LEAVE_ADDITION LA
                                    JOIN Hris_Employee_Work_Holiday WH ON (LA.WOH_ID=WH.ID)
                                    LEFT JOIN Hris_Holiday_Master_Setup H ON (WH.HOLIDAY_ID=H.HOLIDAY_ID))"]
                    , "SLR.ID=LA.SUB_REF_ID AND SLR.EMPLOYEE_ID=LA.EMPLOYEE_ID", [], "left");
        
        if($leaveYear!=null){
            $select->where([
                "( L.STATUS ='E' OR L.OLD_LEAVE='Y' )",
                "L.LEAVE_YEAR" => $leaveYear,
                "1=1"
            ]);
        }else{
        $select->where([
            "L.STATUS='E'"
        ]);
        }
        
        $select->where([
            "E.EMPLOYEE_ID = {$employeeId}"
        ]);

        if ($leaveId != null && $leaveId != -1) {
            $select->where(["LA.LEAVE_ID = {$leaveId}"]);
        }
        if ($leaveRequestStatusId != -1) {
            $select->where(["LA.STATUS = {$leaveRequestStatusId} "]);
        }
        if ($leaveRequestStatusId != 'C') {
            $select->where([
                "DAYS_BETWEEN(LA.REQUESTED_DT, CURRENT_DATE) < (
                      CASE
                        WHEN LA.STATUS = 'C'
                        THEN 20
                        ELSE 1000
                      END)"
            ]);
        }

        if ($fromDate != null) {
            $select->where([
                "LA.START_DATE >= TO_DATE('{$fromDate}','DD-MON-YYYY')"
            ]);
        }
        if ($toDate != null) {
            $select->where([
                "LA.END_DATE <= TO_DATE('{$toDate}','DD-MON-YYYY')"
            ]);
        }
        $select->order("LA.REQUESTED_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchAvailableDays($fromDate, $toDate, $employeeId, $halfDay, $leaveId) {
        $boundedParameter = [];
        $boundedParameter['fromDate']="to_date('{$fromDate}','DD-MON-YYYY')";
        $fromDate = $boundedParameter['fromDate'];
        $boundedParameter['toDate']="to_date('{$toDate}','DD-MON-YYYY')";
        $toDate = $boundedParameter['toDate'];

        $rawResult = $this->rawQuery("SELECT HRIS_AVAILABLE_LEAVE_DAYS($fromDate,$toDate,$employeeId,$leaveId,'$halfDay') AS AVAILABLE_DAYS FROM DUMMY");
        return $rawResult[0];
        
    }

    public function validateLeaveRequest($fromDate, $toDate, $employeeId) {
        $boundedParameter = [];
        $boundedParameter['fromDate']="to_date('{$fromDate}','DD-MON-YYYY')";
        $fromDate = $boundedParameter['fromDate'];
        $boundedParameter['toDate']="to_date('{$toDate}','DD-MON-YYYY')";
        $toDate = $boundedParameter['toDate'];

        $rawResult = $this->rawQuery( "SELECT HRIS_VALIDATE_LEAVE_REQUEST($fromDate,$toDate,$employeeId) AS ERROR FROM DUMMY");
        return $rawResult[0];
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
        $boundedParameter = [];
        $boundedParameter['id']=$id;
        $sql = "SELECT START_DATE,CURRENT_DATE AS CURDATE,
            CASE WHEN
            STATUS IN ('RQ','RC') THEN 'NA'
            ELSE STATUS
            END
            AS LEAVE_STATUS,
            START_DATE-CURRENT_DATE AS DIFF,
                CASE  WHEN 
                (START_DATE-CURRENT_DATE)>0
                THEN
                'FD'
                ELSE
                'BD'
                END AS DATE_STATUS,
                CASE 
                WHEN STATUS IN ('RQ','RC') THEN
                'C'
                WHEN STATUS IN ('AP','CR') and (START_DATE-CURRENT_DATE<=0) THEN
                'CP'
                 WHEN STATUS IN ('AP','CR') and (START_DATE-CURRENT_DATE>0) THEN
                'C'
                ELSE
                STATUS
                END AS CANCEL_ACTION
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST WHERE ID=?";
        $statement = $this->adapter->query($sql);
        return $statement->execute($boundedParameter)->current();
    }

    public function getSubstituteList($leaveId, $employeeId,$maxSubDays=500) {
        $boundedParameter = [];
        $boundedParameter['maxSubDays1']=$maxSubDays;
        $boundedParameter['employeeId1']=$employeeId;
        $boundedParameter['leaveId1']=$leaveId;
        $boundedParameter['maxSubDays2']=$maxSubDays;
        $boundedParameter['employeeId2']=$employeeId;
        $boundedParameter['leaveId2']=$leaveId;
        $boundedParameter['employeeId3']=$employeeId;
        $boundedParameter['leaveId3']=$leaveId;
        
        $sql = " 
        SELECT 
sl.*
        ,lt.*
        ,sl.no_of_days -IFNULL(lt.Taken_Days,0)
        as AVAILABLE_DAYS        
FROM (select 
WOD_ID AS ID
,LA.EMPLOYEE_ID
,NO_OF_DAYS
,WD.FROM_DATE||' - '||WD.TO_DATE AS SUB_NAME
,WD.TO_DATE AS SUB_END_DATE
,ADD_DAYS(WD.TO_DATE,?) AS SUB_VALIDATE_DAYS
--,WD.* 
from 
HRIS_EMPLOYEE_LEAVE_ADDITION LA
JOIN Hris_Employee_Work_Dayoff WD ON (LA.WOD_ID=WD.ID) 
where 
LA.employee_id=?
and LA.leave_id=?
AND WD.STATUS='AP'
--AND WD.TO_DATE>TRUNC(SYSDATE-:maxSubDays)
UNION
select 
WOH_ID AS ID
,LA.EMPLOYEE_ID
,NO_OF_DAYS
,WH.FROM_DATE||' - '||WH.TO_DATE AS SUB_NAME
,WH.TO_DATE AS SUB_END_DATE
,ADD_DAYS(WH.TO_DATE, ?) AS SUB_VALIDATE_DAYS
--,WH.* 
from 
HRIS_EMPLOYEE_LEAVE_ADDITION LA
JOIN Hris_Employee_Work_Holiday WH ON (LA.WOH_ID=WH.ID) 
where 
LA.employee_id=?
and LA.leave_id=?
AND WH.STATUS='AP'
--AND WH.TO_DATE>TRUNC(SYSDATE-:maxSubDays)
) sl
left join (
SELECT Sub_Ref_Id,
SUM(
CASE 
WHEN HALF_DAY IN ('F','S')
THEN NO_OF_DAYS/2
ELSE NO_OF_DAYS
END) AS TAKEN_DAYS
FROM HRIS_EMPLOYEE_LEAVE_REQUEST
WHERE EMPLOYEE_ID=?
AND LEAVE_ID=?
AND STATUS IN ('AP','RQ','RC','CP','CR')
and Sub_Ref_Id is not null
 group by Sub_Ref_Id) lt on (lt.Sub_Ref_Id=sl.id)
            
            ";
        $statement = $this->adapter->query($sql);
        $result=$statement->execute($boundedParameter);
        return Helper::extractDbData($result);
    }
    
    public function cancelFromSubstitue($id) {
        $currentDate = Helper::getcurrentExpressionDate();
        $this->tableGateway->update([LeaveApply::STATUS => 'C', LeaveApply::MODIFIED_DT => $currentDate], [LeaveApply::ID => $id]);
    }
    
}
