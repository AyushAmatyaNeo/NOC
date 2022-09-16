<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\MonthRepository;
use Application\Repository\RepositoryInterface;
use Customer\Model\ContractAttendanceModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ContractAttendanceRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(ContractAttendanceModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
//        $this->gateway->update($model->getArrayCopyForDB(), [CustomerContract::CONTRACT_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ContractAttendanceModel::class, NULL, [
                    ContractAttendanceModel::ATTENDANCE_DT,
                        ], [ContractAttendanceModel::IN_TIME, ContractAttendanceModel::OUT_TIME], NUll, NULL, 'CA', NULL, NUll, [ContractAttendanceModel::TOTAL_HOUR]
                ), false);

        $select->from(['CA' => ContractAttendanceModel::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "CA." . ContractAttendanceModel::EMPLOYEE_ID . "=E.EMPLOYEE_ID", ['FULL_NAME' => new Expression("INITCAP(E.FULL_NAME)")], 'left');



        $select->where(['CA.' . ContractAttendanceModel::CONTRACT_ID => $id]);
        $select->order("CA.ATTENDANCE_DT ASC");

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return $result;
    }

    public function updateImportAttendance(Model $model, $contractId, $monthId, $employeeId, $attendanceDate) {
        $updateArray = [
            ContractAttendanceModel::CONTRACT_ID => $contractId,
            ContractAttendanceModel::MONTH_CODE_ID => $monthId,
            ContractAttendanceModel::EMPLOYEE_ID => $employeeId,
            ContractAttendanceModel::ATTENDANCE_DT => $attendanceDate
        ];

        $tempArray = $model->getArrayCopyForDB();
//        if (!array_key_exists('IN_TIME', $tempArray) || !array_key_exists('OUT_TIME', $tempArray) || !array_key_exists('TOTAL_HOUR', $tempArray)) {
//            $tempArray['IN_TIME'] = null;
//            $tempArray['OUT_TIME'] = null;
//            $tempArray['TOTAL_HOUR'] = null;
//        }

        $this->gateway->update($tempArray, $updateArray);
    }

    public function fetchContractAttendanceMonthWise($id, $monthId) {

        $sql = "SELECT CA.CONTRACT_ID AS CONTRACT_ID,
                INITCAP(TO_CHAR(CA.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT,
                INITCAP(TO_CHAR(CA.IN_TIME, 'HH:MI AM')) AS IN_TIME,
                INITCAP(TO_CHAR(CA.OUT_TIME, 'HH:MI AM')) AS OUT_TIME, 
                NVL2(CA.TOTAL_HOUR,LPAD(TRUNC(CA.TOTAL_HOUR/60,0),2, 0)||':'||LPAD(MOD(CA.TOTAL_HOUR,60),2, 0),null) AS TOTAL_HOUR,
                CA.EMPLOYEE_ID AS EMPLOYEE_ID,
                NVL2(CA.NORMARL_HOUR,LPAD(TRUNC(CA.NORMARL_HOUR/60,0),2, 0)||':'||LPAD(MOD(CA.NORMARL_HOUR,60),2, 0),null) AS NORMARL_HOUR,
                NVL2(CA.PT_HOUR,LPAD(TRUNC(CA.PT_HOUR/60,0),2, 0)||':'||LPAD(MOD(CA.PT_HOUR,60),2, 0),null) AS PT_HOUR,
                NVL2(CA.OT_HOUR,LPAD(TRUNC(CA.OT_HOUR/60,0),2, 0)||':'||LPAD(MOD(CA.OT_HOUR,60),2, 0),null) AS OT_HOUR,
                CA.MONTH_CODE_ID AS MONTH_CODE_ID,
                (CASE CA.IS_ABSENT WHEN 'Y' THEN 'ABSENT' WHEN 'N' THEN 'PRESENT' ELSE '-' END) AS IS_ABSENT,
                (CASE CA.IS_SUBSTITUTE WHEN 'Y' THEN 'SUBSTITUTE' ELSE 'REGULAR' END) AS IS_SUBSTITUTE,
                INITCAP(SE.FULL_NAME) AS FULL_NAME,
                INITCAP(C.CONTRACT_NAME) AS CONTRACT_NAME 
                FROM HRIS_CUST_CONTRACT_ATTENDANCE CA 
                LEFT JOIN HRIS_SERVICE_EMPLOYEES SE ON CA.EMPLOYEE_ID=SE.EMPLOYEE_ID 
                LEFT JOIN HRIS_CUSTOMER_CONTRACT C ON C.CONTRACT_ID=CA.CONTRACT_ID 
                WHERE CA.CONTRACT_ID = {$id} AND CA.MONTH_CODE_ID = {$monthId} ORDER BY CA.ATTENDANCE_DT ASC";


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function deleteSubEmplooyee($conditon) {
        $this->gateway->delete($conditon);
    }

    public function getMonthList() {
        $sql = <<<EOT
            SELECT MC.MONTH_ID,MC.MONTH_EDESC FROM HRIS_FISCAL_YEARS FY
LEFT JOIN HRIS_MONTH_CODE MC  ON (FY.FISCAL_YEAR_ID=MC.FISCAL_YEAR_ID)
WHERE TRUNC(SYSDATE) BETWEEN START_DATE AND END_DATE ORDER BY MONTH_ID
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function populateEmpAttendance($monthId, $customerId) {
        $sql = "BEGIN HRIS_GARDU_ATTENDNACE_MONTHLY({$monthId},{$customerId});  END;";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }

    public function getCutomerEmpAttendnaceMonthly($monthId, $customerId, $locationId) {
        $monthDetails = EntityHelper::rawQueryResult($this->adapter, "select from_date,to_date,to_date-from_date+1 as daysCount from hris_month_code where month_id={$monthId}")->current();

        $fromDate = $monthDetails['FROM_DATE'];
        $toDate = $monthDetails['TO_DATE'];
        $daysCount = $monthDetails['DAYSCOUNT'];


        $pivotString = '';
        for ($i = 1; $i <= $daysCount; $i++) {
            if ($i != $daysCount) {
                $pivotString .= $i . ' AS ' . 'C' . $i . ', ';
            } else {
                $pivotString .= $i . ' AS ' . 'C' . $i;
            }
        }


        $sql = "
            select * from (
                select 
DT.NORMAL_HOUR AS DT_NORMAL_HOUR,
DT.OT_HOUR AS DT_OT_HOUR,  
E.FULL_NAME,CL.LOCATION_NAME,CC.CONTRACT_NAME,D.DESIGNATION_TITLE,
    D.FROM_DATE,D.DAY_COUNT,CE.CONTRACT_ID,CE.CUSTOMER_ID,
    CE.LOCATION_ID,CE.EMPLOYEE_ID,E.EMPLOYEE_CODE,CE.DESIGNATION_ID,CE.START_DATE,CE.END_DATE,CE.DUTY_TYPE_ID,
    TO_CHAR(CE.START_TIME, 'HH:MI AM') AS START_TIME,
    TO_CHAR(CE.END_TIME, 'HH:MI AM') AS END_TIME,
    CE.EMP_ASSIGN_ID,
    DT.DUTY_TYPE_NAME,
    CASE
          WHEN CA.STATUS IS NULL THEN 'Present'
          WHEN CA.STATUS='PR' THEN 'Present'
          WHEN CA.STATUS='AB' THEN 'Absent'
          WHEN CA.STATUS='LV' THEN 'Leave'
          WHEN CA.STATUS='DO' THEN 'DayOff'
          WHEN CA.STATUS='PH' THEN 'PublicHoliday'
        END
      AS STATUS,
      CASE  
            WHEN CA.NORMAL_HOUR IS NULL 
            THEN
            TO_CHAR(to_date(DT.NORMAL_HOUR*60,'sssss'),'hh24:mi')
            ELSE
            TO_CHAR(to_date(CA.NORMAL_HOUR*60,'sssss'),'hh24:mi')
            END AS NORMAL_HOUR,
            CASE  
            WHEN CA.OT_HOUR IS NULL 
            THEN
            TO_CHAR(to_date(DT.OT_HOUR*60,'sssss'),'hh24:mi')
            ELSE
            TO_CHAR(to_date(CA.OT_HOUR*60,'sssss'),'hh24:mi')
            END AS OT_HOUR,
      SE.FULL_NAME AS SUB_EMP_NAME
    from (SELECT   TO_DATE('{$fromDate}','DD-MON-YY') + ROWNUM -1  AS DATES,ROWNUM AS DAY_COUNT,TO_DATE('{$fromDate}','DD-MON-YY') AS FROM_DATE
        FROM dual d
        CONNECT BY  rownum <=  TO_DATE('{$toDate}','DD-MON-YY') -  TO_DATE('{$fromDate}','DD-MON-YY') + 1
     ) D
    LEFT JOIN HRIS_CONTRACT_EMP_ASSIGN CE on (1=1 and CE.status='E')
    LEFT JOIN HRIS_CONTRACT_EMP_ATTENDANCE CA ON (
            CE.CUSTOMER_ID=CA.CUSTOMER_ID AND
            CE.CONTRACT_ID=CA.CONTRACT_ID AND
            CE.EMPLOYEE_ID=CA.EMPLOYEE_ID AND
            CE.LOCATION_ID=CA.LOCATION_ID AND
            CE.DUTY_TYPE_ID=CA.DUTY_TYPE_ID AND
            CE.DESIGNATION_ID=CA.DESIGNATION_ID AND
            CE.EMP_ASSIGN_ID=CA.EMP_ASSIGN_ID AND
            CA.ATTENDANCE_DATE=D.DATES)
    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=CE.EMPLOYEE_ID)
    LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (CL.LOCATION_ID=CE.LOCATION_ID)
    LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (CC.CONTRACT_ID=CE.CONTRACT_ID)
    LEFT JOIN HRIS_DUTY_TYPE DT ON (DT.DUTY_TYPE_ID=CE.DUTY_TYPE_ID)
    LEFT JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=CE.DESIGNATION_ID)
     LEFT JOIN HRIS_EMPLOYEES SE ON (SE.EMPLOYEE_ID=CA.SUB_EMPLOYEE_ID)
    WHERE CE.CUSTOMER_ID={$customerId} and d.dates between CE.START_DATE and CE.END_DATE";
        if ($locationId) {
            $sql .= " AND CE.LOCATION_ID={$locationId}";
        }
        $sql .= ")PIVOT (
 MAX (STATUS) AS STATUS,
 MAX(NORMAL_HOUR) AS NORMAL_HOUR,
 MAX(OT_HOUR) AS OT_HOUR, 
MAX (SUB_EMP_NAME) AS SUB_EMP_NAME
FOR DAY_COUNT IN ({$pivotString})) 
            ORDER BY FULL_NAME ASC,DT_NORMAL_HOUR DESC,DT_OT_HOUR DESC
                ";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        $returnData['monthDetails'] = $monthDetails;
        $returnData['attendanceResult'] = Helper::extractDbData($result);

        return $returnData;
    }

    public function updateAttendance($sql) {
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function pullMonthlyBillCustomerWise($monthId, $customerId) {
        $monthDetails = $this->getMOnthDetails($monthId);

        $fromDate = $monthDetails['FROM_DATE'];
        $toDate = $monthDetails['TO_DATE'];


        $sql = "
           SELECT
 EMPLOYEE_ID,FULL_NAME,
 DESIGNATION_ID,DUTY_TYPE_ID,CONTRACT_ID,LOCATION_ID,START_TIME,END_TIME,
DESIGNATION_TITLE,DUTY_TYPE_NAME,LOCATION_NAME,RATE,DAYS_IN_MONTH,
COUNT(STATUS) AS PRESENT_DAYS
 from(select E.FULL_NAME,CL.LOCATION_NAME,CC.CONTRACT_NAME,D.DESIGNATION_TITLE,
    D.FROM_DATE,D.DAY_COUNT,D.DATES,
    CD.RATE,CD.DAYS_IN_MONTH,
    CE.CONTRACT_ID,CE.CUSTOMER_ID,
    CE.LOCATION_ID,CE.EMPLOYEE_ID,CE.DESIGNATION_ID,CE.START_DATE,CE.END_DATE,CE.DUTY_TYPE_ID,
    TO_CHAR(CE.START_TIME, 'HH:MI AM') AS START_TIME,
    TO_CHAR(CE.END_TIME, 'HH:MI AM') AS END_TIME,
    ID,
    DT.DUTY_TYPE_NAME,
     CASE  WHEN CA.STATUS IS NULL THEN 
    'PR'
    ELSE CA.STATUS END AS STATUS
    from (SELECT   TO_DATE('{$fromDate}','DD-MON-YY') + ROWNUM -1  AS DATES,ROWNUM AS DAY_COUNT,TO_DATE('{$fromDate}','DD-MON-YY') AS FROM_DATE
        FROM dual d
        CONNECT BY  rownum <=  TO_DATE('{$toDate}','DD-MON-YY') -  TO_DATE('{$fromDate}','DD-MON-YY') + 1
     ) D
    LEFT JOIN HRIS_CUST_CONTRACT_EMP CE on (1=1 and CE.status='E')
    LEFT JOIN HRIS_CONTRACT_EMP_ATTENDANCE CA ON (
            CE.CUSTOMER_ID=CA.CUSTOMER_ID AND
            CE.CONTRACT_ID=CA.CONTRACT_ID AND
            CE.EMPLOYEE_ID=CA.EMPLOYEE_ID AND
            CE.LOCATION_ID=CA.LOCATION_ID AND
            CE.DUTY_TYPE_ID=CA.DUTY_TYPE_ID AND
            CE.DESIGNATION_ID=CA.DESIGNATION_ID AND
            CE.START_TIME=CA.START_TIME AND
            CE.END_TIME=CA.END_TIME AND
            CA.ATTENDANCE_DATE=D.DATES)
    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=CE.EMPLOYEE_ID)
    LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (CL.LOCATION_ID=CE.LOCATION_ID)
    LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (CC.CONTRACT_ID=CE.CONTRACT_ID)
    LEFT JOIN HRIS_CUSTOMER_CONTRACT_DETAILS CD ON (CD.CONTRACT_ID=CE.CONTRACT_ID 
    AND CD.DESIGNATION_ID=CE.DESIGNATION_ID
    AND CD.DUTY_TYPE_ID=CE.DUTY_TYPE_ID
    AND CD.status='E')
    LEFT JOIN HRIS_DUTY_TYPE DT ON (DT.DUTY_TYPE_ID=CE.DUTY_TYPE_ID)
    LEFT JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=CE.DESIGNATION_ID)
     LEFT JOIN HRIS_EMPLOYEES SE ON (SE.EMPLOYEE_ID=CA.SUB_EMPLOYEE_ID)
    WHERE CE.CUSTOMER_ID={$customerId} and d.dates between CE.START_DATE and CE.END_DATE)
     WHERE STATUS='PR'
     GROUP BY EMPLOYEE_ID,FULL_NAME,
 DESIGNATION_ID,DUTY_TYPE_ID,CONTRACT_ID,LOCATION_ID,START_TIME,END_TIME,
 DESIGNATION_TITLE,DUTY_TYPE_NAME,LOCATION_NAME,RATE,DAYS_IN_MONTH
    union
    select   
EMPLOYEE_ID,FULL_NAME,
 DESIGNATION_ID,DUTY_TYPE_ID,CONTRACT_ID,LOCATION_ID,START_TIME,END_TIME,
DESIGNATION_TITLE,DUTY_TYPE_NAME,LOCATION_NAME,RATE,DAYS_IN_MONTH
,COUNT(STATUS) AS PRESENT_DAYS
from (select 
SE.FULL_NAME,CL.LOCATION_NAME,CC.CONTRACT_NAME,D.DESIGNATION_TITLE,
    CD.RATE,CD.DAYS_IN_MONTH,
    CE.CONTRACT_ID,CE.CUSTOMER_ID,
    CE.LOCATION_ID,CE.EMPLOYEE_ID,CE.DESIGNATION_ID,CE.START_DATE,CE.END_DATE,CE.DUTY_TYPE_ID,
    TO_CHAR(CE.START_TIME, 'HH:MI AM') AS START_TIME,
    TO_CHAR(CE.END_TIME, 'HH:MI AM') AS END_TIME,
    ID,
    DT.DUTY_TYPE_NAME,
    CA.STATUS
from HRIS_CONTRACT_EMP_ATTENDANCE CA
JOIN HRIS_CUST_CONTRACT_EMP CE ON(
CE.CUSTOMER_ID=CA.CUSTOMER_ID AND
            CE.CONTRACT_ID=CA.CONTRACT_ID AND
            CE.EMPLOYEE_ID=CA.EMPLOYEE_ID AND
            CE.LOCATION_ID=CA.LOCATION_ID AND
            CE.DUTY_TYPE_ID=CA.DUTY_TYPE_ID AND
            CE.DESIGNATION_ID=CA.DESIGNATION_ID AND
            CE.START_TIME=CA.START_TIME AND
            CE.END_TIME=CA.END_TIME)
LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=CA.EMPLOYEE_ID)
LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (CL.LOCATION_ID=CA.LOCATION_ID)
LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (CC.CONTRACT_ID=CA.CONTRACT_ID)
LEFT JOIN HRIS_CUSTOMER_CONTRACT_DETAILS CD ON (CD.CONTRACT_ID=CA.CONTRACT_ID 
AND CD.DESIGNATION_ID=CA.DESIGNATION_ID
AND CD.DUTY_TYPE_ID=CA.DUTY_TYPE_ID
AND CD.status='E')
LEFT JOIN HRIS_DUTY_TYPE DT ON (DT.DUTY_TYPE_ID=CA.DUTY_TYPE_ID)
LEFT JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=CA.DESIGNATION_ID)
LEFT JOIN HRIS_EMPLOYEES SE ON (SE.EMPLOYEE_ID=CA.SUB_EMPLOYEE_ID)
WHERE CA.CUSTOMER_ID={$customerId} and CA.ATTENDANCE_DATE between TO_DATE('{$fromDate}','DD-MON-YY') and TO_DATE('{$toDate}','DD-MON-YY')
)
WHERE STATUS='AB'
GROUP BY EMPLOYEE_ID,FULL_NAME,
 DESIGNATION_ID,DUTY_TYPE_ID,CONTRACT_ID,LOCATION_ID,START_TIME,END_TIME,
 DESIGNATION_TITLE,DUTY_TYPE_NAME,LOCATION_NAME,RATE,DAYS_IN_MONTH
    
    ";

//        echo $sql;
//        die();
//        $sql = "SELECT EMPLOYEE_ID,FULL_NAME,CONTRACT_ID,LOCATION_ID,LOCATION_NAME,DESIGNATION_ID,DESIGNATION_TITLE,RATE,COUNT(STATUS) AS PRESENT_DAYS 
//  FROM (select D.FROM_DATE,D.DAY_COUNT,D.DATES,
// CE.EMPLOYEE_ID,E.FULL_NAME,CE.CONTRACT_ID,CE.LOCATION_ID,CL.LOCATION_NAME,CD.SHIFT_ID,CD.DESIGNATION_ID,D.D.DESIGNATION_TITLE,CD.RATE,
// CASE  WHEN CA.STATUS IS NULL THEN 
// 'PR'
// ELSE CA.STATUS END AS STATUS
// from (SELECT   TO_DATE('{$fromDate}','DD-MON-YY') + ROWNUM -1  AS DATES,ROWNUM AS DAY_COUNT,TO_DATE('{$fromDate}','DD-MON-YY') AS FROM_DATE
//    FROM dual d
//    CONNECT BY  rownum <=  TO_DATE('{$toDate}','DD-MON-YY') -  TO_DATE('{$fromDate}','DD-MON-YY') + 1
// ) D
//   LEFT JOIN HRIS_CUST_CONTRACT_EMP CE on (1=1 and CE.status='E')
//    LEFT JOIN HRIS_CONTRACT_EMP_ATTENDANCE CA ON (CA.EMPLOYEE_ID=CE.EMPLOYEE_ID 
//    AND CA.LOCATION_ID=CE.LOCATION_ID AND CA.ATTENDANCE_DATE=D.DATES)
//    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=CE.EMPLOYEE_ID)
//    LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (CL.LOCATION_ID=CE.LOCATION_ID)
//    LEFT JOIN HRIS_CUSTOMER_CONTRACT_DETAILS CD ON (CD.CONTRACT_ID=CE.CONTRACT_ID AND CD.DESIGNATION_ID=CE.DESIGNATION_ID AND CD.status='E')
//    LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (CC.CONTRACT_ID=CD.CONTRACT_ID)
//    LEFT JOIN HRIS_SHIFTS S ON (S.SHIFT_ID=CD.SHIFT_ID)
//    LEFT JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=CD.DESIGNATION_ID)
//    WHERE CE.STATUS='E' AND CE.CUSTOMER_ID={$customerId} AND D.DATES BETWEEN CC.START_DATE AND CC.END_DATE )
//    WHERE STATUS='PR' GROUP BY EMPLOYEE_ID,FULL_NAME,CONTRACT_ID,LOCATION_ID,LOCATION_NAME,DESIGNATION_ID,DESIGNATION_TITLE,RATE";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getEmployeeListWithCode() {
        $sql = "select EMPLOYEE_ID,'('||EMPLOYEE_CODE||') '||FULL_NAME AS FULL_NAME ,retired_flag
            from  HRIS_EMPLOYEES where status='E' and RESIGNED_FLAG='N'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function pullAttendanceAbsentData($monthStartDate, $column, $customerId, $contractId, $employeeId, $locationId, $dutyTypeId, $designationId, $startTime, $endTime, $empAssignId) {

        $sql = "
            SELECT  (TO_DATE('{$monthStartDate}','DD-MON-YY') + $column -1) AS ATTENDNACE_DATE,
            CE.CUSTOMER_ID,CE.CONTRACT_ID,CE.EMPLOYEE_ID,CE.LOCATION_ID,
            CE.DUTY_TYPE_ID,CE.DESIGNATION_ID,
            INITCAP(TO_CHAR(CE.START_TIME, 'HH:MI AM')) AS START_TIME,
            INITCAP(TO_CHAR(CE.END_TIME, 'HH:MI AM')) AS END_TIME,
            CE.EMP_ASSIGN_ID,
            CA.SUB_EMPLOYEE_ID,CA.POSTING_TYPE,SE.FULL_NAME AS SUB_EMPLOYEE_NAME,
            TO_CHAR(to_date(D.NORMAL_HOUR*60,'sssss'),'hh24:mi') AS DUTY_NORMAL_HOUR,
            TO_CHAR(to_date(D.OT_HOUR*60,'sssss'),'hh24:mi') AS DUTY_OT_HOUR,
            CASE WHEN CA.STATUS  IS NULL 
            THEN 'PR'
            ELSE CA.STATUS
            END AS STATUS,
            CASE WHEN CA.IN_TIME IS NULL
            THEN TO_CHAR(CE.START_TIME, 'HH:MI AM')
            ELSE TO_CHAR(CA.IN_TIME, 'HH:MI AM')
            END AS IN_TIME,
            CASE WHEN CA.OUT_TIME IS NULL
            THEN TO_CHAR(CE.END_TIME, 'HH:MI AM')
            ELSE TO_CHAR(CA.OUT_TIME, 'HH:MI AM')
            END AS OUT_TIME,
            CASE  
            WHEN CA.NORMAL_HOUR IS NULL 
            THEN
            TO_CHAR(to_date(D.NORMAL_HOUR*60,'sssss'),'hh24:mi')
            ELSE
            TO_CHAR(to_date(CA.NORMAL_HOUR*60,'sssss'),'hh24:mi')
            END AS NORMAL_HOUR,
            CASE  
            WHEN CA.OT_HOUR IS NULL 
            THEN
            TO_CHAR(to_date(D.OT_HOUR*60,'sssss'),'hh24:mi')
            ELSE
            TO_CHAR(to_date(CA.OT_HOUR*60,'sssss'),'hh24:mi')
            END AS OT_HOUR,
            CASE WHEN CA.SUB_RATE IS NULL 
              THEN CE.MONTHLY_RATE
              ELSE CA.SUB_RATE
              END AS RATE,
              CASE WHEN CA.SUB_OT_RATE IS NULL 
              THEN CC.OT_RATE
              ELSE CA.SUB_OT_RATE
              END AS OT_RATE,
               CASE
                  WHEN CA.SUB_OT_TYPE IS NULL THEN CC.OT_TYPE
                  ELSE CA.SUB_OT_TYPE
                END
              AS OT_TYPE
            FROM HRIS_CONTRACT_EMP_ASSIGN CE
            LEFT JOIN HRIS_CONTRACT_EMP_ATTENDANCE CA ON (
            CE.CUSTOMER_ID=CA.CUSTOMER_ID AND
            CE.CONTRACT_ID=CA.CONTRACT_ID AND
            CE.EMPLOYEE_ID=CA.EMPLOYEE_ID AND
            CE.LOCATION_ID=CA.LOCATION_ID AND
            CE.DUTY_TYPE_ID=CA.DUTY_TYPE_ID AND
            CE.DESIGNATION_ID=CA.DESIGNATION_ID AND
            CE.EMP_ASSIGN_ID=CA.EMP_ASSIGN_ID AND
            CA.ATTENDANCE_DATE=TO_DATE('{$monthStartDate}','DD-MON-YY') + $column -1
            )
            LEFT JOIN HRIS_DUTY_TYPE D ON (D.DUTY_TYPE_ID=CE.DUTY_TYPE_ID)
            LEFT JOIN HRIS_EMPLOYEES SE ON(SE.EMPLOYEE_ID=CA.SUB_EMPLOYEE_ID)
            LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (CE.CONTRACT_ID=CC.CONTRACT_ID)
             WHERE  
                      CE.CUSTOMER_ID={$customerId}
                        AND CE.CONTRACT_ID={$contractId}
                            AND CE.EMPLOYEE_ID={$employeeId}
                                AND CE.LOCATION_ID={$locationId}
                                    AND CE.DUTY_TYPE_ID={$dutyTypeId}
                                        AND CE.DESIGNATION_ID={$designationId}
                                                AND CE.EMP_ASSIGN_ID='{$empAssignId}'

                ";


//        echo $sql;
//        die();


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function updateAttendanceData($attendanceDate, $customerId, $contractId, $employeeId, $locationId, $dutyTypeId, $designationId, $empAssignId, $status, $normalHour, $otHour, $subEmployeeId, $postingType, $rate, $otRate, $otType) {

        if ($subEmployeeId == '' or $subEmployeeId == null) {
            $subEmployeeString = "V_SUB_EMPLOYEE_ID NUMBER:=NULL;";
            $postingTypeString = "V_POSTING_TYPE CHAR(2 BYTE):=NULL;";
            $postingTypeString .= "V_RATE NUMBER :=NULL;";
            $postingTypeString .= "V_OT_RATE NUMBER :=NULL;";
            $postingTypeString .= "V_OT_TYPE CHAR(1 BYTE) :=NULL;";
        } else {
            $subEmployeeString = "V_SUB_EMPLOYEE_ID NUMBER:=" . $subEmployeeId . ";";
            $postingTypeString = "V_POSTING_TYPE CHAR(2 BYTE):='" . $postingType . "';";
            $postingTypeString .= "V_RATE NUMBER :=" . $rate . ";";
            $postingTypeString .= "V_OT_RATE NUMBER :=" . $otRate . ";";
            $postingTypeString .= "V_OT_TYPE CHAR(1 BYTE) :='" . $otType . "';";
        }


        $sql = "DECLARE
                V_CUSTOMER_ID NUMBER:={$customerId};
                V_CONTRACT_ID NUMBER:={$contractId};
                V_EMPLOYEE_ID NUMBER:={$employeeId};
                V_LOCATION_ID NUMBER:={$locationId};
                V_DUTY_TYPE_ID NUMBER:={$dutyTypeId};
                V_DESIGNATION_ID NUMBER:={$designationId};
                V_EMP_ASSIGN_ID NUMBER:={$empAssignId};
                V_ATTENDANCE_DATE DATE:=TO_DATE('{$attendanceDate}','DD-MON-YY');
                V_STATUS CHAR(2 BYTE):='{$status}';
                V_NORMAL_HOUR NUMBER:=TO_NUMBER(TO_CHAR(TO_DATE('{$normalHour}','hh24:mi'),'sssss'))/60;
                V_OT_HOUR NUMBER:=TO_NUMBER(TO_CHAR(TO_DATE('{$otHour}','hh24:mi'),'sssss'))/60;
                {$subEmployeeString}
                {$postingTypeString}
                V_ATTENDANCE_COUNT NUMBER;
                BEGIN
                SELECT COUNT(*) INTO V_ATTENDANCE_COUNT FROM HRIS_CONTRACT_EMP_ATTENDANCE WHERE 
                    CUSTOMER_ID=V_CUSTOMER_ID AND
                    CONTRACT_ID=V_CONTRACT_ID AND
                    EMPLOYEE_ID=V_EMPLOYEE_ID AND
                    LOCATION_ID=V_LOCATION_ID AND
                    DUTY_TYPE_ID=V_DUTY_TYPE_ID AND
                    DESIGNATION_ID=V_DESIGNATION_ID AND
                    EMP_ASSIGN_ID=V_EMP_ASSIGN_ID AND
                    ATTENDANCE_DATE=V_ATTENDANCE_DATE;
                    
                BEGIN
                IF(V_ATTENDANCE_COUNT=0)
                THEN
                INSERT INTO HRIS_CONTRACT_EMP_ATTENDANCE
                (CUSTOMER_ID,CONTRACT_ID,EMPLOYEE_ID,LOCATION_ID,DUTY_TYPE_ID,DESIGNATION_ID,ATTENDANCE_DATE,
                STATUS,
                EMP_ASSIGN_ID,
                SUB_EMPLOYEE_ID,
                POSTING_TYPE,
                NORMAL_HOUR,
                OT_HOUR,
                SUB_RATE,
                SUB_OT_RATE,
                SUB_OT_TYPE
                )
                VALUES
                (V_CUSTOMER_ID,V_CONTRACT_ID,V_EMPLOYEE_ID,V_LOCATION_ID,V_DUTY_TYPE_ID,V_DESIGNATION_ID,
                V_ATTENDANCE_DATE,
                V_STATUS,
                V_EMP_ASSIGN_ID,
                V_SUB_EMPLOYEE_ID,
                V_POSTING_TYPE,
                V_NORMAL_HOUR,
                V_OT_HOUR,
                V_RATE,
                V_OT_RATE,
                V_OT_TYPE
                );


                ELSE
                
                UPDATE HRIS_CONTRACT_EMP_ATTENDANCE
                SET 
                STATUS=V_STATUS,
                SUB_EMPLOYEE_ID=V_SUB_EMPLOYEE_ID,
                POSTING_TYPE=V_POSTING_TYPE,
                NORMAL_HOUR=V_NORMAL_HOUR,
                OT_HOUR=V_OT_HOUR,
                SUB_RATE=V_RATE,
                SUB_OT_RATE=V_OT_RATE,
                SUB_OT_TYPE=V_OT_TYPE
                WHERE 
                    CUSTOMER_ID=V_CUSTOMER_ID AND
                    CONTRACT_ID=V_CONTRACT_ID AND
                    EMPLOYEE_ID=V_EMPLOYEE_ID AND
                    LOCATION_ID=V_LOCATION_ID AND
                    DUTY_TYPE_ID=V_DUTY_TYPE_ID AND
                    DESIGNATION_ID=V_DESIGNATION_ID AND
                    EMP_ASSIGN_ID=V_EMP_ASSIGN_ID AND
                    ATTENDANCE_DATE=V_ATTENDANCE_DATE;
                
                END IF;

                END;
                        

END;";

//                ECHO $sql;
//                DIE();

        $statement = $this->adapter->query($sql);
        $statement->execute();

        $sql1 = "SELECT 
            CASE
          WHEN CA.STATUS IS NULL THEN 'Present'
          WHEN CA.STATUS='PR' THEN 'Present'
          WHEN CA.STATUS='AB' THEN 'Absent'
          WHEN CA.STATUS='LV' THEN 'Leave'
          WHEN CA.STATUS='DO' THEN 'DayOff'
          WHEN CA.STATUS='PH' THEN 'PublicHoliday'
        END
      AS STATUS,
      TO_CHAR(to_date(CA.NORMAL_HOUR*60,'sssss'),'hh24:mi') AS NORMAL_HOUR,
      TO_CHAR(to_date(CA.OT_HOUR*60,'sssss'),'hh24:mi') AS OT_HOUR,
            TO_CHAR(CA.IN_TIME, 'HH:MI AM') AS IN_TIME,
            TO_CHAR(CA.OUT_TIME, 'HH:MI AM') AS OUT_TIME,
            SE.FULL_NAME AS SUB_EMPLOYEE
    FROM HRIS_CONTRACT_EMP_ATTENDANCE CA 
    LEFT JOIN HRIS_EMPLOYEES SE ON (SE.EMPLOYEE_ID=CA.SUB_EMPLOYEE_ID)
    WHERE
                    CA.CUSTOMER_ID={$customerId} AND
                    CA.CONTRACT_ID={$contractId} AND
                    CA.EMPLOYEE_ID={$employeeId} AND
                    CA.LOCATION_ID={$locationId} AND
                    CA.DUTY_TYPE_ID={$dutyTypeId} AND
                    CA.DESIGNATION_ID={$designationId} AND
                    CA.EMP_ASSIGN_ID={$empAssignId} AND
                    CA.ATTENDANCE_DATE=TO_DATE('{$attendanceDate}','DD-MON-YY')";

                    

        $statement1 = $this->adapter->query($sql1);
        $result = $statement1->execute();
        return $result->current();
    }

    public function getMOnthDetails($monthId) {
        $monthDetails = EntityHelper::rawQueryResult($this->adapter, "select from_date,to_date,to_date-from_date+1 as daysCount from hris_month_code where month_id={$monthId}")->current();
        return $monthDetails;
    }

    public function getCutomerEmpAttendnaceReportMonthly($monthId, $customerId, $locationId) {
        $monthDetails = $this->getMOnthDetails($monthId);

        $fromDate = $monthDetails['FROM_DATE'];
        $toDate = $monthDetails['TO_DATE'];
        $daysCount = $monthDetails['DAYSCOUNT'];


        $pivotString = '';
        for ($i = 1; $i <= $daysCount; $i++) {
            if ($i != $daysCount) {
                $pivotString .= $i . ' AS ' . 'C' . $i . ', ';
            } else {
                $pivotString .= $i . ' AS ' . 'C' . $i;
            }
        }


        $sql = "select * from (
            
                select 


    (SELECT COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
WHERE STATUS='AB' AND EMP_ASSIGN_ID=CE.EMP_ASSIGN_ID AND (
ATTENDANCE_DATE BETWEEN 
TO_DATE('{$fromDate}','DD-MON-YY')
AND TO_DATE('{$toDate}','DD-MON-YY')
) )
+CASE WHEN CE.START_DATE>TO_DATE('{$fromDate}','DD-MON-YY')
THEN 
CE.START_DATE-TO_DATE('{$fromDate}','DD-MON-YY')
    ELSE
    0
END
+CASE WHEN CE.END_DATE<TO_DATE('{$toDate}','DD-MON-YY')
THEN 
TO_DATE('{$toDate}','DD-MON-YY')-CE.END_DATE
    ELSE
    0
END
AS ABSENT_DAYS,

(SELECT COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
WHERE STATUS='LV' AND EMP_ASSIGN_ID=CE.EMP_ASSIGN_ID AND (
ATTENDANCE_DATE BETWEEN 
TO_DATE('{$fromDate}','DD-MON-YY')
AND TO_DATE('{$toDate}','DD-MON-YY')
) )AS LEAVE,

(SELECT COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
WHERE STATUS='DO' AND EMP_ASSIGN_ID=CE.EMP_ASSIGN_ID AND (
ATTENDANCE_DATE BETWEEN 
TO_DATE('{$fromDate}','DD-MON-YY')
AND TO_DATE('{$toDate}','DD-MON-YY')
) )AS DAY_OFF,

(SELECT COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
WHERE STATUS='PH' AND EMP_ASSIGN_ID=CE.EMP_ASSIGN_ID AND (
ATTENDANCE_DATE BETWEEN 
TO_DATE('{$fromDate}','DD-MON-YY')
AND TO_DATE('{$toDate}','DD-MON-YY')
) )AS PAID_HOLIDAY,                

E.FULL_NAME,CL.LOCATION_NAME,CC.CONTRACT_NAME,D.DESIGNATION_TITLE,
    D.DAY_COUNT,CE.CONTRACT_ID,CE.CUSTOMER_ID,
    CE.LOCATION_ID,CE.EMPLOYEE_ID,E.EMPLOYEE_CODE,CE.DESIGNATION_ID,CE.DUTY_TYPE_ID,
    CE.EMP_ASSIGN_ID,
    DT.DUTY_TYPE_NAME,
    CASE
          WHEN CA.STATUS IS NULL THEN 'Present'
          WHEN CA.STATUS='PR' THEN 'Present'
          WHEN CA.STATUS='AB' THEN 'Absent'
          WHEN CA.STATUS='LV' THEN 'Leave'
          WHEN CA.STATUS='DO' THEN 'DayOff'
          WHEN CA.STATUS='PH' THEN 'PublicHoliday'
        END
      AS STATUS,
      CASE  
            WHEN CA.NORMAL_HOUR IS NULL 
            THEN
            TO_CHAR(to_date(DT.NORMAL_HOUR*60,'sssss'),'hh24:mi')
            ELSE
            TO_CHAR(to_date(CA.NORMAL_HOUR*60,'sssss'),'hh24:mi')
            END AS NORMAL_HOUR,
            CASE  
            WHEN CA.OT_HOUR IS NULL 
            THEN
            TO_CHAR(to_date(DT.OT_HOUR*60,'sssss'),'hh24:mi')
            ELSE
            TO_CHAR(to_date(CA.OT_HOUR*60,'sssss'),'hh24:mi')
            END AS OT_HOUR,
      SE.FULL_NAME AS SUB_EMP_NAME
    from (SELECT   TO_DATE('{$fromDate}','DD-MON-YY') + ROWNUM -1  AS DATES,ROWNUM AS DAY_COUNT,TO_DATE('{$fromDate}','DD-MON-YY') AS FROM_DATE
        FROM dual d
        CONNECT BY  rownum <=  TO_DATE('{$toDate}','DD-MON-YY') -  TO_DATE('{$fromDate}','DD-MON-YY') + 1
     ) D
    LEFT JOIN HRIS_CONTRACT_EMP_ASSIGN CE on (1=1 and CE.status='E')
    LEFT JOIN HRIS_CONTRACT_EMP_ATTENDANCE CA ON (
            CE.CUSTOMER_ID=CA.CUSTOMER_ID AND
            CE.CONTRACT_ID=CA.CONTRACT_ID AND
            CE.EMPLOYEE_ID=CA.EMPLOYEE_ID AND
            CE.LOCATION_ID=CA.LOCATION_ID AND
            CE.DUTY_TYPE_ID=CA.DUTY_TYPE_ID AND
            CE.DESIGNATION_ID=CA.DESIGNATION_ID AND
            CE.EMP_ASSIGN_ID=CA.EMP_ASSIGN_ID AND
            CA.ATTENDANCE_DATE=D.DATES)
    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=CE.EMPLOYEE_ID)
    LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (CL.LOCATION_ID=CE.LOCATION_ID)
    LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (CC.CONTRACT_ID=CE.CONTRACT_ID)
    LEFT JOIN HRIS_DUTY_TYPE DT ON (DT.DUTY_TYPE_ID=CE.DUTY_TYPE_ID)
    LEFT JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=CE.DESIGNATION_ID)
     LEFT JOIN HRIS_EMPLOYEES SE ON (SE.EMPLOYEE_ID=CA.SUB_EMPLOYEE_ID)
    WHERE CE.CUSTOMER_ID={$customerId} and d.dates between CE.START_DATE and CE.END_DATE";
        if ($locationId) {
            $sql .= " AND CE.LOCATION_ID={$locationId}";
        }

        $sql .= " UNION ALL
        
       SELECT
     
    (
    SELECT (TO_DATE('{$toDate}','DD-MON-YY')-TO_DATE('{$fromDate}','DD-MON-YY'))+1-COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
WHERE STATUS='AB' AND SUB_EMPLOYEE_ID=CA.SUB_EMPLOYEE_ID AND EMP_ASSIGN_ID=CA.EMP_ASSIGN_ID AND (
ATTENDANCE_DATE BETWEEN 
TO_DATE('{$fromDate}','DD-MON-YY')
AND TO_DATE('{$toDate}','DD-MON-YY')
)
    )AS ABSENT_DAYS,
    0 AS LEAVE,
    0 AS DAY_OFF,
    0 AS PAID_HOLIDAY,
       

        SE.FULL_NAME||' Sub For '||E.FULL_NAME AS FULL_NAME,
      CL.LOCATION_NAME,
      CC.CONTRACT_NAME,
      D.DESIGNATION_TITLE,
      (CA.ATTENDANCE_DATE-TO_DATE('{$fromDate}','DD-MON-YY')+1) AS DAY_COUNT,
      CA.CONTRACT_ID,
      CA.CUSTOMER_ID,
      CA.LOCATION_ID,
      CA.EMPLOYEE_ID,
      E.EMPLOYEE_CODE,
      CA.DESIGNATION_ID,
      CA.DUTY_TYPE_ID,
      CA.EMP_ASSIGN_ID,
      DT.DUTY_TYPE_NAME,
      'Present' AS STATUS,
      TO_CHAR(TO_DATE(CA.NORMAL_HOUR * 60,'sssss'),'hh24:mi') AS NORMAL_HOUR,
      TO_CHAR(TO_DATE(CA.OT_HOUR * 60,'sssss'),'hh24:mi') AS OT_HOUR,
      '' AS  SUB_EMP_NAME 
FROM  HRIS_CONTRACT_EMP_ATTENDANCE CA
 LEFT JOIN HRIS_EMPLOYEES E ON (
        E.EMPLOYEE_ID = CA.EMPLOYEE_ID
      )
      LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (
        CL.LOCATION_ID = CA.LOCATION_ID
      )
      LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (
        CC.CONTRACT_ID = CA.CONTRACT_ID
      )
      LEFT JOIN HRIS_DUTY_TYPE DT ON (
        DT.DUTY_TYPE_ID = CA.DUTY_TYPE_ID
      )
      LEFT JOIN HRIS_DESIGNATIONS D ON (
        D.DESIGNATION_ID = CA.DESIGNATION_ID
      )
      LEFT JOIN HRIS_EMPLOYEES SE ON (
        SE.EMPLOYEE_ID = CA.SUB_EMPLOYEE_ID
      )

WHERE CA.CUSTOMER_ID={$customerId} 
AND CA.STATUS='AB' 
AND CA.SUB_EMPLOYEE_ID IS NOT NULL
AND (CA.ATTENDANCE_DATE BETWEEN TO_DATE('{$fromDate}','DD-MON-YY') AND TO_DATE('{$toDate}','DD-MON-YY')

)";

        if ($locationId) {
            $sql .= " AND CA.LOCATION_ID={$locationId}";
        }

        $sql .= ")PIVOT (
 MAX (STATUS) AS STATUS,
 MAX(NORMAL_HOUR) AS NORMAL_HOUR,
 MAX(OT_HOUR) AS OT_HOUR, 
MAX (SUB_EMP_NAME) AS SUB_EMP_NAME
FOR DAY_COUNT IN ({$pivotString})) 
            
                ";




//        echo $sql;
//        die();
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        $returnData['monthDetails'] = $monthDetails;
        $returnData['attendanceResult'] = Helper::extractDbData($result);

        return $returnData;
    }

    public function fetchEmpWiseMonthlyReport($monthId) {
        $monthDetails = $this->getMOnthDetails($monthId);

        $fromDate = $monthDetails['FROM_DATE'];
        $toDate = $monthDetails['TO_DATE'];
        $daysCount = $monthDetails['DAYSCOUNT'];

        $sql = "
        select EMPLOYEE_CODE,
        (TO_DATE('{$toDate}','DD-MON-YY')-TO_DATE('{$fromDate}','DD-MON-YY'))
            +1-ABSENT_DAYS-LEAVE
        AS PRESENT_DAYS,
        EMP_ASSIGN_ID,
        FULL_NAME,
        CONTRACT_NAME,
        LOCATION_NAME,
        DESIGNATION_TITLE,
        DUTY_TYPE_NAME,
        ABSENT_DAYS,
        LEAVE,
        DAY_OFF,
        PAID_HOLIDAY,
        SUM(NORMAL_HOUR)/60 AS TOTAL_NORMAL_HOUR,
        SUM(OT_HOUR)/60 AS TOTAL_OT_HOUR
        from(
        SELECT (            
        SELECT COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
        WHERE STATUS='AB' AND EMP_ASSIGN_ID=CE.EMP_ASSIGN_ID AND (
        ATTENDANCE_DATE BETWEEN 
        TO_DATE('{$fromDate}','DD-MON-YY')
        AND TO_DATE('{$toDate}','DD-MON-YY')
        ) )
        +CASE WHEN CE.START_DATE>TO_DATE('{$fromDate}','DD-MON-YY')
        THEN 
        CE.START_DATE-TO_DATE('{$fromDate}','DD-MON-YY')
            ELSE
            0
        END
        +CASE WHEN CE.END_DATE<TO_DATE('{$toDate}','DD-MON-YY')
        THEN 
        TO_DATE('{$toDate}','DD-MON-YY')-CE.END_DATE
            ELSE
            0
        END

AS ABSENT_DAYS,

(SELECT COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
        WHERE STATUS='LV' AND EMP_ASSIGN_ID=CE.EMP_ASSIGN_ID AND (
        ATTENDANCE_DATE BETWEEN 
        TO_DATE('{$fromDate}','DD-MON-YY')
        AND TO_DATE('{$toDate}','DD-MON-YY')
        ) )AS LEAVE,
        
        (SELECT COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
        WHERE STATUS='DO' AND EMP_ASSIGN_ID=CE.EMP_ASSIGN_ID AND (
        ATTENDANCE_DATE BETWEEN 
        TO_DATE('{$fromDate}','DD-MON-YY')
        AND TO_DATE('{$toDate}','DD-MON-YY')
        ) )AS DAY_OFF,

        (SELECT COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
        WHERE STATUS='PH' AND EMP_ASSIGN_ID=CE.EMP_ASSIGN_ID AND (
        ATTENDANCE_DATE BETWEEN 
        TO_DATE('{$fromDate}','DD-MON-YY')
        AND TO_DATE('{$toDate}','DD-MON-YY')
        ) )AS PAID_HOLIDAY,                

        E.FULL_NAME,CL.LOCATION_NAME,CC.CONTRACT_NAME,D.DESIGNATION_TITLE,
            D.DAY_COUNT,CE.CONTRACT_ID,CE.CUSTOMER_ID,
            CE.LOCATION_ID,CE.EMPLOYEE_ID,E.EMPLOYEE_CODE,CE.DESIGNATION_ID,CE.DUTY_TYPE_ID,
            CE.EMP_ASSIGN_ID,
            DT.DUTY_TYPE_NAME,
            CASE
                  WHEN CA.STATUS IS NULL THEN 'Present'
                  WHEN CA.STATUS='PR' THEN 'Present'
                  WHEN CA.STATUS='AB' THEN 'Absent'
                  WHEN CA.STATUS='LV' THEN 'LEAVE'
                  WHEN CA.STATUS='DO' THEN 'DayOff'
                  WHEN CA.STATUS='PH' THEN 'PublicHoliday'
                END
              AS STATUS,
              CASE  
                    WHEN CA.NORMAL_HOUR IS NULL 
                    THEN
                    DT.NORMAL_HOUR
                    WHEN CA.NORMAL_HOUR IS NOT NULL AND  (CA.STATUS='AB' OR CA.STATUS='LV')
                    THEN
                    0
                    ELSE
                    CA.NORMAL_HOUR
                    END AS NORMAL_HOUR,
                    CASE  
                    WHEN CA.OT_HOUR IS NULL 
                    THEN
                    DT.OT_HOUR
                    WHEN CA.OT_HOUR IS NOT NULL AND  CA.STATUS='AB'
                    THEN
                    0
                    ELSE
                    CA.OT_HOUR
                    END AS OT_HOUR,
              SE.FULL_NAME AS SUB_EMP_NAME
            from (SELECT   TO_DATE('{$fromDate}','DD-MON-YY') + ROWNUM -1  AS DATES,ROWNUM AS DAY_COUNT,TO_DATE('{$fromDate}','DD-MON-YY') AS FROM_DATE
                FROM dual d
                CONNECT BY  rownum <=  TO_DATE('{$toDate}','DD-MON-YY') -  TO_DATE('{$fromDate}','DD-MON-YY') + 1
             ) D
            LEFT JOIN HRIS_CONTRACT_EMP_ASSIGN CE on (1=1 and CE.status='E')
            LEFT JOIN HRIS_CONTRACT_EMP_ATTENDANCE CA ON (
                    CE.CUSTOMER_ID=CA.CUSTOMER_ID AND
                    CE.CONTRACT_ID=CA.CONTRACT_ID AND
                    CE.EMPLOYEE_ID=CA.EMPLOYEE_ID AND
                    CE.LOCATION_ID=CA.LOCATION_ID AND
                    CE.DUTY_TYPE_ID=CA.DUTY_TYPE_ID AND
                    CE.DESIGNATION_ID=CA.DESIGNATION_ID AND
                    CE.EMP_ASSIGN_ID=CA.EMP_ASSIGN_ID AND
                    CA.ATTENDANCE_DATE=D.DATES)
            LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=CE.EMPLOYEE_ID)
            LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (CL.LOCATION_ID=CE.LOCATION_ID)
            LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (CC.CONTRACT_ID=CE.CONTRACT_ID)
            LEFT JOIN HRIS_DUTY_TYPE DT ON (DT.DUTY_TYPE_ID=CE.DUTY_TYPE_ID)
            LEFT JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=CE.DESIGNATION_ID)
             LEFT JOIN HRIS_EMPLOYEES SE ON (SE.EMPLOYEE_ID=CA.SUB_EMPLOYEE_ID)
             WHERE D.DATES BETWEEN CE.START_DATE and CE.END_DATE
     UNION   
 SELECT
     
    (     
SELECT (TO_DATE('{$toDate}','DD-MON-YY')-TO_DATE('{$fromDate}','DD-MON-YY'))+1-COUNT(STATUS) FROM HRIS_CONTRACT_EMP_ATTENDANCE
WHERE STATUS='AB' AND SUB_EMPLOYEE_ID=CA.SUB_EMPLOYEE_ID AND EMP_ASSIGN_ID=CA.EMP_ASSIGN_ID AND (
ATTENDANCE_DATE BETWEEN 
TO_DATE('{$fromDate}','DD-MON-YY')
AND TO_DATE('{$toDate}','DD-MON-YY')
)
    )AS ABSENT_DAYS,
    0 AS LEAVE,
    0 AS DAY_OFF,
    0 AS PAID_HOLIDAY,
       

        SE.FULL_NAME||' Sub For '||E.FULL_NAME AS FULL_NAME,
      CL.LOCATION_NAME,
      CC.CONTRACT_NAME,
      D.DESIGNATION_TITLE,
      (CA.ATTENDANCE_DATE-TO_DATE('{$fromDate}','DD-MON-YY')+1) AS DAY_COUNT,
      CA.CONTRACT_ID,
      CA.CUSTOMER_ID,
      CA.LOCATION_ID,
      CA.EMPLOYEE_ID,
      E.EMPLOYEE_CODE,
      CA.DESIGNATION_ID,
      CA.DUTY_TYPE_ID,
      CA.EMP_ASSIGN_ID,
      DT.DUTY_TYPE_NAME,
      'Present' AS STATUS,
      CA.NORMAL_HOUR AS NORMAL_HOUR,
      CA.OT_HOUR AS OT_HOUR,
      '' AS  SUB_EMP_NAME 
FROM  HRIS_CONTRACT_EMP_ATTENDANCE CA
 LEFT JOIN HRIS_EMPLOYEES E ON (
        E.EMPLOYEE_ID = CA.EMPLOYEE_ID
      )
      LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (
        CL.LOCATION_ID = CA.LOCATION_ID
      )
      LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (
        CC.CONTRACT_ID = CA.CONTRACT_ID
      )
      LEFT JOIN HRIS_DUTY_TYPE DT ON (
        DT.DUTY_TYPE_ID = CA.DUTY_TYPE_ID
      )
      LEFT JOIN HRIS_DESIGNATIONS D ON (
        D.DESIGNATION_ID = CA.DESIGNATION_ID
      )
      LEFT JOIN HRIS_EMPLOYEES SE ON (
        SE.EMPLOYEE_ID = CA.SUB_EMPLOYEE_ID
      )

WHERE  CA.STATUS='AB' 
AND CA.SUB_EMPLOYEE_ID IS NOT NULL
AND (CA.ATTENDANCE_DATE BETWEEN TO_DATE('{$fromDate}','DD-MON-YY') AND TO_DATE('{$toDate}','DD-MON-YY'))
                )
             GROUP BY EMPLOYEE_CODE,EMP_ASSIGN_ID,full_name,
        CONTRACT_NAME,
        LOCATION_NAME,
        DESIGNATION_TITLE,
        DUTY_TYPE_NAME,
        ABSENT_DAYS,
        DAY_OFF,
        LEAVE,
        PAID_HOLIDAY

                        ";

//echo $sql;
//die();

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();


        return Helper::extractDbData($result);
    }

}
