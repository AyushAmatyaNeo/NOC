<?php

namespace LeaveManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use LeaveManagement\Model\LeaveAssign;
use LeaveManagement\Model\LeaveMaster;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\Helper;
use Zend\Db\Sql\Select;
use LeaveManagement\Model\LeaveMonths;
use Application\Repository\HrisRepository;

class LeaveBalanceRepository extends HrisRepository {

    protected $adapter;
    protected $tableGateway;
    protected $leaveTableGateway;
    protected $employeeTableGateway;
    protected $leaveMonthTableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(LeaveAssign::TABLE_NAME, $adapter);
        $this->leaveTableGateway = new TableGateway(LeaveMaster::TABLE_NAME, $adapter);
        $this->employeeTableGateway = new TableGateway("HRIS_EMPLOYEES", $adapter);
        $this->leaveMonthTableGateway = new TableGateway("HRIS_LEAVE_MONTH_CODE", $adapter);
    }
    
    

    public function getAllLeave($isMonthly = false, $leaveId = null,$leaveYear=null) {
        $boundedParameter = [];
        $leaveCondition = " AND REPORT_SHOW_DEFAULT='Y'";

        if($leaveYear!=null){
            $boundedParameter['leaveYear'] = $leaveYear;
            $leaveYearStatusCondition="( ( STATUS ='E' OR OLD_LEAVE='Y' ) AND LEAVE_YEAR= ? )";
        }else{
            $leaveYearStatusCondition="STATUS ='E'";
        }
        if($leaveId != null && $leaveId != ''){
            $leaveData=$this->getBoundedForArray($leaveId,'leaveId');
            $boundedParameter=array_merge($boundedParameter,$leaveData['parameter']);
            $leaveCondition = " and leave_id in ({$leaveData['sql']})";
        }
        $condition = $isMonthly ? " AND IS_MONTHLY = 'Y' " : "  AND IS_MONTHLY = 'N' ";
        $sql = "SELECT LEAVE_ID,INITCAP(LEAVE_ENAME) AS LEAVE_ENAME FROM HRIS_LEAVE_MASTER_SETUP WHERE {$leaveYearStatusCondition} {$condition} {$leaveCondition} ORDER BY VIEW_ORDER,LEAVE_ENAME";
        $statement = $this->adapter->query($sql);
        return $statement->execute($boundedParameter);
    }

    public function getAllEmployee($emplyoeeId, $companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], NULL, NULL, NULL, NULL, 'E'), false);

        $select->from(['E' => "HRIS_EMPLOYEES"]);

        $select->where([
            "E.STATUS='E'"
        ]);

        if ($serviceEventTypeId == 5 || $serviceEventTypeId == 8 || $serviceEventTypeId == 14) {
            $select->where(["E.RETIRED_FLAG='Y'"]);
        } else {
            $select->where(["E.RETIRED_FLAG='N'"]);
        }


        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $select->where([
                "E.EMPLOYEE_TYPE= '{$employeeTypeId}'"
            ]);
        }

        if ($emplyoeeId != -1) {
            $select->where([
                "E.EMPLOYEE_ID=" . $emplyoeeId
            ]);
        }
        if ($companyId != -1) {
            $select->where([
                "E.COMPANY_ID=" . $companyId
            ]);
        }
        if ($branchId != -1) {
            $select->where([
                "E.BRANCH_ID=" . $branchId
            ]);
        }
        if ($departmentId != -1) {
            $select->where([
                "E.DEPARTMENT_ID=" . $departmentId
            ]);
        }
        if ($designationId != -1) {
            $select->where([
                "E.DESIGNATION_ID=" . $designationId
            ]);
        }
        if ($positionId != -1) {
            $select->where([
                "E.POSITION_ID=" . $positionId
            ]);
        }
        if ($serviceTypeId != -1) {
            $select->where([
                "E.SERVICE_TYPE_ID=" . $serviceTypeId
            ]);
        }
        if ($serviceEventTypeId != -1) {
            $select->where([
                "E.SERVICE_EVENT_TYPE_ID=" . $serviceEventTypeId
            ]);
        }
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function getByEmpIdLeaveId($employeeId, $leaveId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("LA.TOTAL_DAYS AS TOTAL_DAYS"),
            new Expression("LA.BALANCE AS BALANCE"),
            new Expression("LA.LEAVE_ID AS LEAVE_ID"),
            new Expression("LA.EMPLOYEE_ID AS EMPLOYEE_ID"),
                ], true);

        $select->from(['LA' => LeaveAssign::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['FIRST_NAME' => new Expression('INITCAP(E.FIRST_NAME)'), 'MIDDLE_NAME' => new Expression('INITCAP(E.MIDDLE_NAME)'), 'LAST_NAME' => new Expression('INITCAP(E.LAST_NAME)'), 'SERVICE_EVENT_TYPE_ID'], "left")
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression('INITCAP(L.LEAVE_ENAME)')], "left");

        $select->where([
            "L.STATUS='E'",
            "E.STATUS='E'",
            "LA.EMPLOYEE_ID=" . $employeeId,
            "LA.LEAVE_ID=" . $leaveId
        ]);
        $select->order(['L.LEAVE_ID']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getOnlyCarryForwardedRecord() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("LA.BALANCE AS BALANCE"),
            new Expression("LA.TOTAL_DAYS AS TOTAL_DAYS"),
            new Expression("LA.PREVIOUS_YEAR_BAL AS PREVIOUS_YEAR_BAL"),
            new Expression("LA.LEAVE_ID AS LEAVE_ID"),
            new Expression("LA.EMPLOYEE_ID AS EMPLOYEE_ID")
                ], true);

        $select->from(['LA' => LeaveAssign::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['FIRST_NAME' => new Expression('INITCAP(E.FIRST_NAME)'), 'MIDDLE_NAME' => new Expression('INITCAP(E.MIDDLE_NAME)'), 'LAST_NAME' => new Expression('INITCAP(E.LAST_NAME)')], "left")
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression('INITCAP(LEAVE_ENAME)')], "left");

        $select->where([
            "L.STATUS='E'",
            "E.STATUS='E'",
            "E.RETIRED_FLAG='N'",
            "L.CARRY_FORWARD='Y'"
        ]);
        $select->order(['LA.EMPLOYEE_ID']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();


        $record = [];
        foreach ($result as $row) {
            array_push($record, [
                'EMPLOYEE_ID' => $row['EMPLOYEE_ID'],
                'LEAVE_ID' => $row['LEAVE_ID'],
                'PREVIOUS_YEAR_BAL' => $row['PREVIOUS_YEAR_BAL'],
                'TOTAL_DAYS' => $row['TOTAL_DAYS'],
                'BALANCE' => $row['BALANCE'],
            ]);
        }
        return $record;
    }

    public function getPivotedList($searchQuery, $isMonthly = false) {
        $boundedParameter = [];
        $leaveId = $searchQuery['leaveId'];
        $leaveYear = $searchQuery['leaveYear'];
        $leaveCondition = "";
//        $monthlyCondition = "";
        $leaveData=[];

        if($leaveId != null && $leaveId != ''){
            $leaveId = implode($leaveId, ',');
            $leaveCondition .= "$leaveId";
        } else {
            $condition = $isMonthly ? " AND IS_MONTHLY = 'Y' " : " AND IS_MONTHLY = 'N' ";
            $boundedParameter['leaveYear'] = $leaveYear;
            $leaveYearStatusCondition = "( ( STATUS ='E' OR OLD_LEAVE='Y' ) AND LEAVE_YEAR= ?)";
            $rawlist = $this->rawQuery("SELECT STRING_AGG(LEAVE_ID,','ORDER BY LEAVE_ID) as list FROM HRIS_LEAVE_MASTER_SETUP WHERE {$leaveYearStatusCondition} {$condition} ",$boundedParameter);
            $leaveCondition .= $rawlist[0]['LIST'];
        }

        $searchCondition = EntityHelper::getSearchConditonHana($searchQuery['companyId'], $searchQuery['branchId'], $searchQuery['departmentId'], $searchQuery['positionId'], $searchQuery['designationId'], $searchQuery['serviceTypeId'], $searchQuery['serviceEventTypeId'], $searchQuery['employeeTypeId'], $searchQuery['employeeId'], $searchQuery['genderId'], $searchQuery['locationId'], $searchQuery['functionalTypeId']);

        $sql = "call leave_balance('{$searchCondition}', 'MAX', 'LEAVE_ID', '{$leaveCondition}')";
        return $this->rawQuery($sql);
    }

    private function fetchLeaveAsDbArray($isMonthly = false, $leaveId = '',$leaveYear=null) {
        $condition = $isMonthly ? " AND IS_MONTHLY = 'Y' " : " AND IS_MONTHLY = 'N' ";
        $leaveCondition = "";
        $boundedParameter = [];

        if($leaveYear!=null){
            $boundedParameter['leaveYear']=$leaveYear;
            $leaveYearStatusCondition="( ( STATUS ='E' OR OLD_LEAVE='Y' ) AND LEAVE_YEAR= ?)";
        }else{
            $leaveYearStatusCondition="STATUS ='E'";
        }
        if($leaveId != null && $leaveId != ''){
            if (gettype($leaveId) === "array") {
                $boundedParameter=array_merge($boundedParameter,$leaveId['parameter']);
                $leaveCondition .= " AND LEAVE_ID IN ({$leaveId['sql']}) ";
            } else {
                $leaveCondition .= " AND LEAVE_ID IN ($leaveId) ";
            }
        }
    $rawList = EntityHelper::rawQueryResult($this->adapter, "SELECT LEAVE_ID FROM HRIS_LEAVE_MASTER_SETUP WHERE {$leaveYearStatusCondition} {$condition} {$leaveCondition}",$boundedParameter);
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['LEAVE_ID']} AS L{$row['LEAVE_ID']}";
            } else {
                $dbArray .= "{$row['LEAVE_ID']} AS L{$row['LEAVE_ID']},";
            }
        }
        return $dbArray;
    }

    public function getPivotedListBetnDates($searchQuery, $isMonthly = false) {
        $boundedParameter=[];
        $orderByString = EntityHelper::getOrderBy('E.FULL_NAME ASC', null, 'E.SENIORITY_LEVEL', 'P.LEVEL_NO', 'E.JOIN_DATE', 'DES.ORDER_NO', 'E.FULL_NAME');
        $searchCondition = EntityHelper::getSearchConditonBounded($searchQuery['companyId'], $searchQuery['branchId'], $searchQuery['departmentId'], $searchQuery['positionId'], $searchQuery['designationId'], $searchQuery['serviceTypeId'], $searchQuery['serviceEventTypeId'], $searchQuery['employeeTypeId'], $searchQuery['employeeId'], $searchQuery['genderId'], $searchQuery['locationId'], $searchQuery['functionalTypeId']);
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);
        
        $leaveYear = $searchQuery['leaveYear'];
        $leaveArrayDb = $this->fetchLeaveAsDbArray($isMonthly, null,$leaveYear);
        $boundedParameter['fromDate']=$searchQuery['fromDate'];
        $boundedParameter['toDate']=$searchQuery['toDate'];
        $fromDate = "to_date(?)";
        $toDate = "to_date(?)";

        $sql = "SELECT LB.*,E.FULL_NAME,
            E.EMPLOYEE_CODE AS EMPLOYEE_CODE,
            (D.DEPARTMENT_NAME)                                        AS DEPARTMENT_NAME,
            (DES.DESIGNATION_TITLE)                                    AS DESIGNATION_TITLE,
            (P.POSITION_NAME)                                          AS POSITION_NAME,
            FUNT.FUNCTIONAL_TYPE_EDESC                                        AS FUNCTIONAL_TYPE_EDESC
            FROM (SELECT * FROM
(SELECT la.employee_id,
    la.leave_id,
    LTBD.lEAVE_TAKEN_BETWEEN_DATES AS TAKEN,
    case when ltad.leave_taken_after_dates is not null
    then 
    la.balance+ltad.leave_taken_after_dates
    else
    la.balance
    end AS CALCULATED_BALANCE
              FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
              LEFT JOIN 
              (SELECT 
EMPLOYEE_ID,LEAVE_ID
,SUM(CASE WHEN HALF_DAY='F' OR HALF_DAY='S' THEN 0.5 ELSE 1 END) AS lEAVE_TAKEN_BETWEEN_DATES 
FROM (SELECT * FROM HRIS_EMPLOYEE_LEAVE_REQUEST WHERE STATUS IN ('AP','CP','CR')) LR
  JOIN 
(SELECT   {$fromDate} + ROWNUM -1  AS DATES
    FROM dual d
    CONNECT BY  rownum <=  {$toDate} -  {$fromDate} + 1) ADT ON (ADT.DATES Between START_DATE AND END_DATE)
    WHERE  ADT.DATES BETWEEN  {$fromDate} AND {$toDate}
   GROUP BY EMPLOYEE_ID,LEAVE_ID) LTBD ON (LTBD.LEAVE_ID=LA.LEAVE_ID AND LTBD.EMPLOYEE_ID=LA.EMPLOYEE_ID)
   LEFT JOIN (
   select EMPLOYEE_ID,LEAVE_ID
,SUM(CASE WHEN HALF_DAY='F' OR HALF_DAY='S' THEN leave_days/0.5 ELSE leave_days END) AS lEAVE_TAKEN_AFTER_DATES 
from (SELECT EMPLOYEE_ID,LEAVE_ID,START_DATE,END_DATE,NO_OF_DAYS,HALF_DAY,
CASE WHEN
    half_day = 'F'
OR
    half_day = 'S'
THEN (end_date - {$toDate})/2
ELSE end_date - {$toDate}
END as leave_days 
    FROM HRIS_EMPLOYEE_LEAVE_REQUEST WHERE STATUS IN ('AP','CP','CR') 
    AND END_DATE>{$toDate}
    AND START_DATE<={$toDate}
        UNION ALL
        SELECT
    EMPLOYEE_ID,LEAVE_ID,START_DATE,END_DATE,NO_OF_DAYS,
HALF_DAY,
CASE
WHEN
    half_day = 'F'
OR
    half_day = 'S'
THEN no_of_days/2
ELSE no_of_days
END as leave_days
    FROM HRIS_EMPLOYEE_LEAVE_REQUEST WHERE STATUS IN ('AP','CP','CR') 
    AND START_DATE>{$toDate}
    )
    GROUP BY EMPLOYEE_ID,LEAVE_ID) LTAD ON (LTAD.LEAVE_ID=LA.LEAVE_ID AND LTAD.EMPLOYEE_ID=LA.EMPLOYEE_ID)
    )PIVOT (MAX(TAKEN) AS TAKEN, MAX(CALCULATED_BALANCE) AS BALANCE 
    FOR LEAVE_ID
    IN ({$leaveArrayDb}) )
    )LB LEFT JOIN HRIS_EMPLOYEES E ON (LB.EMPLOYEE_ID=E.EMPLOYEE_ID)
    LEFT JOIN HRIS_DEPARTMENTS D
    ON E.DEPARTMENT_ID=D.DEPARTMENT_ID
    LEFT JOIN HRIS_DESIGNATIONS DES
    ON E.DESIGNATION_ID=DES.DESIGNATION_ID
    LEFT JOIN HRIS_POSITIONS P
    ON E.POSITION_ID=P.POSITION_ID
    LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT
    ON E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID
    WHERE E.STATUS='E' {$searchCondition['sql']} {$orderByString}";
//    echo $sql;
//    die();
        return EntityHelper::rawQueryResult($this->adapter, $sql,$boundedParameter);
    }

    public function getLeaveTypes() {

        $sql = " SELECT DISTINCT ELA.LEAVE_ID,
  LMS.LEAVE_ENAME
FROM HRIS_EMPLOYEE_LEAVE_ADDITION ELA
LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
ON (ELA.LEAVE_ID = LMS.LEAVE_ID)
where LMS.STATUS = 'E' ";

        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function fetchLeaveAddition($searchQuery) {

        $fromDate = $searchQuery['fromDate'];
        $toDate = $searchQuery['toDate'];
        $leaveId = $searchQuery['leave_id'];
        
        $leaveCondition = " ";
        
        $boundedParameter = [];
        if($leaveId != null){
        $boundedParameter['leaveId']=$leaveId[0];
            $leaveCondition = " AND ELA.LEAVE_ID = ? ";
        }
        
//        $fromCondition = "";
//        $toCondition = "";
//        
//        if($fromDate != null){
//            $fromCondition = " AND ";
//        }

        $searchCondition = EntityHelper::getSearchConditonBounded($searchQuery['companyId'], $searchQuery['branchId'], $searchQuery['departmentId'], $searchQuery['positionId'], $searchQuery['designationId'], $searchQuery['serviceTypeId'], $searchQuery['serviceEventTypeId'], $searchQuery['employeeTypeId'], $searchQuery['employeeId']);

        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);

        $sql = " SELECT E.EMPLOYEE_CODE, E.FULL_NAME, D.DEPARTMENT_NAME, B.BRANCH_NAME, LMS.LEAVE_ENAME, ELA.*,
  CASE
    WHEN ELA.WOD_ID IS NOT NULL
    THEN WD.FROM_DATE ||' - '|| WD.TO_DATE
    WHEN ELA.WOH_ID IS NOT NULL
    THEN WH.FROM_DATE ||' - '|| WH.TO_DATE
    WHEN ELA.TRAVEL_ID IS NOT NULL
    THEN T.FROM_DATE ||' - '|| T.TO_DATE
    WHEN ELA.TRAINING_ID IS NOT NULL
    THEN TR.START_DATE ||' - '|| TR.END_DATE
  END as LEAVE_DATE
FROM HRIS_EMPLOYEE_LEAVE_ADDITION ELA
LEFT JOIN HRIS_EMPLOYEE_WORK_DAYOFF WD
ON (ELA.WOD_ID = WD.ID)
LEFT JOIN HRIS_EMPLOYEE_WORK_HOLIDAY WH
ON (ELA.WOH_ID = WH.ID)
LEFT JOIN HRIS_EMPLOYEE_TRAVEL_REQUEST T
ON (ELA.TRAVEL_ID = T.TRAVEL_ID)
LEFT JOIN HRIS_EMPLOYEE_TRAINING_REQUEST TR
ON (ELA.TRAINING_ID = TR.REQUEST_ID)
LEFT JOIN HRIS_EMPLOYEES E
ON (ELA.EMPLOYEE_ID = E.EMPLOYEE_ID)
LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
ON (ELA.LEAVE_ID = LMS.LEAVE_ID)
LEFT JOIN HRIS_DEPARTMENTS D
ON (E.DEPARTMENT_ID = D.DEPARTMENT_ID)
LEFT JOIN HRIS_BRANCHES B
ON (E.BRANCH_ID = B.BRANCH_ID)
where 1=1  {$leaveCondition} {$searchCondition['sql']} and E.STATUS = 'E' 
";

        return $this->rawQuery($sql, $boundedParameter);
        // $statement = $this->adapter->query($sql);
        // $result = $statement->execute();

        // return Helper::extractDbData($result);
    }

    public function fetchLeaveYearMonth() {
        $rowset = $this->leaveMonthTableGateway->select(function (Select $select) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new LeaveMonths(), [
                        'fromDate',
                        'toDate',
                    ]), false);

            $select->where([LeaveMonths::STATUS => 'E']);
            $select->order("LEAVE_YEAR_MONTH_NO ASC");
        });
        return $rowset;
    }

    public function getCurrentLeaveMonth() {
        $sql = <<<EOT
            SELECT MONTH_ID,
              LEAVE_YEAR_ID,
              LEAVE_YEAR_MONTH_NO,
              YEAR,
              MONTH_NO,
              MONTH_EDESC,
              MONTH_NDESC,
              FROM_DATE,
              INITCAP(TO_CHAR(FROM_DATE,'DD-MON-YYYY')) AS FROM_DATE_AD,
              BS_DATE(FROM_DATE) AS FROM_DATE_BS,
              TO_DATE ,
              INITCAP(TO_CHAR(TO_DATE,'DD-MON-YYYY')) AS TO_DATE_AD,
              BS_DATE(TO_DATE) AS TO_DATE_BS
            FROM HRIS_LEAVE_MONTH_CODE
            WHERE (select 
            case when current_date>max(to_date) then max(to_date) else current_date
            end
            from HRIS_LEAVE_MONTH_CODE) BETWEEN FROM_DATE AND TO_DATE
EOT;
// echo('<pre>');print_r($sql);die;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

}
