<?php
namespace Travel\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\EntityHelper;

class TravelItnaryRepository extends HrisRepository implements RepositoryInterface {
    
    Private $tableGatewayItnaryMembers;
    Private $tableGatewayItnaryDetails;

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, 'HRIS_TRAVEL_ITNARY');
        $this->tableGatewayItnaryMembers = new TableGateway('HRIS_ITNARY_MEMBERS', $adapter);
        $this->tableGatewayItnaryDetails = new TableGateway('HRIS_ITNARY_DETAILS', $adapter);
    }

    public function getFilteredRecord($search):array {
//        $condition = "";
//        $condition = EntityHelper::getSearchConditon($search['companyId'], $search['branchId'], $search['departmentId'], $search['positionId'], $search['designationId'], $search['serviceTypeId'], $search['serviceEventTypeId'], $search['employeeTypeId'], $search['employeeId'], null, null, $search['functionalTypeId']);
//        if (isset($search['fromDate']) && $search['fromDate'] != null) {
//            $condition .= " AND TR.FROM_DATE>=TO_DATE('{$search['fromDate']}','DD-MM-YYYY') ";
//        }
//        if (isset($search['fromDate']) && $search['toDate'] != null) {
//            $condition .= " AND TR.TO_DATE<=TO_DATE('{$search['toDate']}','DD-MM-YYYY') ";
//        }
//
//
//        if (isset($search['status']) && $search['status'] != null && $search['status'] != -1) {
//            if (gettype($search['status']) === 'array') {
//                $csv = "";
//                for ($i = 0; $i < sizeof($search['status']); $i++) {
//                    if ($i == 0) {
//                        $csv = "'{$search['status'][$i]}'";
//                    } else {
//                        $csv .= ",'{$search['status'][$i]}'";
//                    }
//                }
//                $condition .= "AND TR.STATUS IN ({$csv})";
//            } else {
//                $condition .= "AND TR.STATUS IN ('{$search['status']}')";
//            }
//        }
 
//        $sql = "SELECT TR.TRAVEL_ID                        AS TRAVEL_ID,
//                  TR.TRAVEL_CODE                           AS TRAVEL_CODE,
//                  TR.EMPLOYEE_ID                           AS EMPLOYEE_ID,
//                  TR.HARDCOPY_SIGNED_FLAG                  AS HARDCOPY_SIGNED_FLAG,
//                  (CASE WHEN TR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT,
//                  E.EMPLOYEE_CODE                          AS EMPLOYEE_CODE,
//                  E.FULL_NAME                              AS EMPLOYEE_NAME,
//                  TO_CHAR(TR.REQUESTED_DATE,'DD-MON-YYYY') AS REQUESTED_DATE_AD,
//                  BS_DATE(TR.REQUESTED_DATE)               AS REQUESTED_DATE_BS,
//                  TO_CHAR(TR.FROM_DATE,'DD-MON-YYYY')      AS FROM_DATE_AD,
//                  BS_DATE(TR.FROM_DATE)                    AS FROM_DATE_BS,
//                  TO_CHAR(TR.TO_DATE,'DD-MON-YYYY')        AS TO_DATE_AD,
//                  BS_DATE(TR.TO_DATE)                      AS TO_DATE_BS,
//                  TR.DESTINATION                           AS DESTINATION,
//                  TR.DEPARTURE                             AS DEPARTURE,
//                  TR.PURPOSE                               AS PURPOSE,
//                  TR.VOUCHER_NO                            AS VOUCHER_NO,
//                  TR.REQUESTED_TYPE                        AS REQUESTED_TYPE,
//                  (
//                  CASE
//                    WHEN TR.REQUESTED_TYPE = 'ad'
//                    THEN 'Advance'
//                    ELSE 'Expense'
//                  END)                                                            AS REQUESTED_TYPE_DETAIL,
//                  NVL(TR.REQUESTED_AMOUNT,0)                                      AS REQUESTED_AMOUNT,
//                  TR.TRANSPORT_TYPE                                               AS TRANSPORT_TYPE,
//                  INITCAP(HRIS_GET_FULL_FORM(TR.TRANSPORT_TYPE,'TRANSPORT_TYPE')) AS TRANSPORT_TYPE_DETAIL,
//                  TO_CHAR(TR.DEPARTURE_DATE)                                      AS DEPARTURE_DATE_AD,
//                  BS_DATE(TR.DEPARTURE_DATE)                                      AS DEPARTURE_DATE_BS,
//                  TO_CHAR(TR.RETURNED_DATE)                                       AS RETURNED_DATE_AD,
//                  BS_DATE(TR.RETURNED_DATE)                                       AS RETURNED_DATE_BS,
//                  TR.REMARKS                                                      AS REMARKS,
//                  TR.STATUS                                                       AS STATUS,
//                  LEAVE_STATUS_DESC(TR.STATUS)                                    AS STATUS_DETAIL,
//                  TR.RECOMMENDED_BY                                               AS RECOMMENDED_BY,
//                  RE.FULL_NAME                                                    AS RECOMMENDED_BY_NAME,
//                  TO_CHAR(TR.RECOMMENDED_DATE)                                    AS RECOMMENDED_DATE_AD,
//                  BS_DATE(TR.RECOMMENDED_DATE)                                    AS RECOMMENDED_DATE_BS,
//                  TR.RECOMMENDED_REMARKS                                          AS RECOMMENDED_REMARKS,
//                  TR.APPROVED_BY                                                  AS APPROVED_BY,
//                  AE.FULL_NAME                                                    AS APPROVED_BY_NAME,
//                  TO_CHAR(TR.APPROVED_DATE)                                       AS APPROVED_DATE_AD,
//                  BS_DATE(TR.APPROVED_DATE)                                       AS APPROVED_DATE_BS,
//                  TR.APPROVED_REMARKS                                             AS APPROVED_REMARKS,
//                  RAR.EMPLOYEE_ID                                                 AS RECOMMENDER_ID,
//                  RAR.FULL_NAME                                                   AS RECOMMENDER_NAME,
//                  RAA.EMPLOYEE_ID                                                 AS APPROVER_ID,
//                  RAA.FULL_NAME                                                   AS APPROVER_NAME
//                FROM HRIS_EMPLOYEE_TRAVEL_REQUEST TR
//                LEFT JOIN HRIS_EMPLOYEES E
//                ON (E.EMPLOYEE_ID =TR.EMPLOYEE_ID)
//                LEFT JOIN HRIS_EMPLOYEES RE
//                ON(RE.EMPLOYEE_ID =TR.RECOMMENDED_BY)
//                LEFT JOIN HRIS_EMPLOYEES AE
//                ON (AE.EMPLOYEE_ID =TR.APPROVED_BY)
//                LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
//                ON (RA.EMPLOYEE_ID=TR.EMPLOYEE_ID)
//                LEFT JOIN HRIS_EMPLOYEES RAR
//                ON (RA.RECOMMEND_BY=RAR.EMPLOYEE_ID)
//                LEFT JOIN HRIS_EMPLOYEES RAA
//                ON(RA.APPROVED_BY=RAA.EMPLOYEE_ID)
//                WHERE 1          =1 {$condition}";
        
        $boundedParameter = [];
        $condition = "";
        if (isset($search['fromDate']) && $search['fromDate'] != null) {
            $condition .= " AND TI.FROM_DT>=TO_DATE(:fromDate,'DD-MM-YYYY') ";
            $boundedParameter['fromDate'] = $search['fromDate'];
        }
        if (isset($search['fromDate']) && $search['toDate'] != null) {
            $condition .= " AND TI.TO_DT<=TO_DATE(:toDate,'DD-MM-YYYY') ";
            $boundedParameter['toDate'] = $search['toDate'];
        }
        $boundedParameter['employeeId'] = $search['employeeId'];
        
        $sql="
            select 
            LEAVE_STATUS_DESC(tr.status) as TRAVEL_STATUS,
            (case when tr.status in('C', 'R') then 'N' else 'Y' end) ALLOW_DELETE,
            tr.REQUESTED_AMOUNT,
TI.*,IMD.EMPLOYEE_ID_LIST,IMD.FULL_NAME_LIST
,TT.TRANSPORT_NAME AS TRANSPORT_TYPE_FULL_FORM
from HRIS_TRAVEL_ITNARY TI
LEFT JOIN HRIS_TRANSPORT_TYPES TT ON (TI.TRANSPORT_TYPE=TT.TRANSPORT_CODE)
LEFT JOIN hris_employee_travel_request tr on (tr.employee_id = :employeeId
    and tr.itnary_id = ti.itnary_id)
LEFT JOIN (
SELECT 
IM.ITNARY_ID,
LISTAGG(IM.EMPLOYEE_ID, ',') WITHIN GROUP (ORDER BY IME.FULL_NAME) AS EMPLOYEE_ID_LIST,
LISTAGG(IME.EMPLOYEE_CODE||'-'||IME.FULL_NAME, ','||rpad(' ',4,' ')) WITHIN GROUP (ORDER BY IME.FULL_NAME) AS FULL_NAME_LIST
FROM HRIS_ITNARY_MEMBERS IM
JOIN HRIS_EMPLOYEES IME ON (IM.EMPLOYEE_ID=IME.EMPLOYEE_ID )
GROUP BY IM.ITNARY_ID ) IMD ON (IMD.ITNARY_ID=TI.ITNARY_ID)
WHERE (TI.CREATED_BY=:employeeId or IMD.ITNARY_ID in(select ITNARY_ID from
hris_itnary_members where EMPLOYEE_ID = :employeeId)) {$condition} 
";

        $finalSql = $this->getPrefReportQuery($sql);
        return $this->rawQuery($finalSql, $boundedParameter);
    }

    public function notSettled(): array {
        $sql = "SELECT TR.TRAVEL_ID                   AS TRAVEL_ID,
                  TR.TRAVEL_CODE                      AS TRAVEL_CODE,
                  TR.EMPLOYEE_ID                      AS EMPLOYEE_ID,
                  E.EMPLOYEE_CODE                      AS EMPLOYEE_CODE,
                  E.FULL_NAME                         AS EMPLOYEE_NAME,
                  TO_CHAR(TR.REQUESTED_DATE,'DD-MON-YYYY') AS REQUESTED_DATE_AD,
                  BS_DATE(TR.REQUESTED_DATE)               AS REQUESTED_DATE_BS,
                  TO_CHAR(TR.FROM_DATE,'DD-MON-YYYY') AS FROM_DATE_AD,
                  BS_DATE(TR.FROM_DATE)               AS FROM_DATE_BS,
                  TO_CHAR(TR.TO_DATE,'DD-MON-YYYY')   AS TO_DATE_AD,
                  BS_DATE(TR.TO_DATE)                 AS TO_DATE_BS,
                  TR.DESTINATION                      AS DESTINATION,
                  TR.DEPARTURE                        AS DEPARTURE,
                  TR.PURPOSE                          AS PURPOSE,
                  TR.REASON                           AS REASON,
                  TR.REQUESTED_TYPE                   AS REQUESTED_TYPE,
                  TR.VOUCHER_NO                       AS VOUCHER_NO,
                   NVL(TR.REQUESTED_AMOUNT,0) AS REQUESTED_AMOUNT,
                  TR.TRANSPORT_TYPE          AS TRANSPORT_TYPE,
                  (
                  CASE
                    WHEN TR.TRANSPORT_TYPE = 'AP'
                    THEN 'Aeroplane'
                    WHEN TR.TRANSPORT_TYPE = 'OV'
                    THEN 'Office Vehicles'
                    WHEN TR.TRANSPORT_TYPE = 'TI'
                    THEN 'Taxi'
                    WHEN TR.TRANSPORT_TYPE = 'BS'
                    THEN 'Bus'
                    WHEN TR.TRANSPORT_TYPE = 'OF'
                    THEN 'On Foot'
                  END)                                                            AS TRANSPORT_TYPE_DETAIL,
                  TO_CHAR(TR.DEPARTURE_DATE)                                      AS DEPARTURE_DATE_AD,
                  BS_DATE(TR.DEPARTURE_DATE)                                      AS DEPARTURE_DATE_BS,
                  TO_CHAR(TR.RETURNED_DATE)                                       AS RETURNED_DATE_AD,
                  BS_DATE(TR.RETURNED_DATE)                                       AS RETURNED_DATE_BS,
                  TR.REMARKS                                                      AS REMARKS,
                  TR.STATUS                                                       AS STATUS,
                  LEAVE_STATUS_DESC(TR.STATUS)                                    AS STATUS_DETAIL,
                  TR.RECOMMENDED_BY                                               AS RECOMMENDED_BY,
                  RE.FULL_NAME                                                    AS RECOMMENDED_BY_NAME,
                  TO_CHAR(TR.RECOMMENDED_DATE)                                    AS RECOMMENDED_DATE_AD,
                  BS_DATE(TR.RECOMMENDED_DATE)                                    AS RECOMMENDED_DATE_BS,
                  TR.RECOMMENDED_REMARKS                                          AS RECOMMENDED_REMARKS,
                  TR.APPROVED_BY                                                  AS APPROVED_BY,
                  AE.FULL_NAME                                                    AS APPROVED_BY_NAME,
                  TO_CHAR(TR.APPROVED_DATE)                                       AS APPROVED_DATE_AD,
                  BS_DATE(TR.APPROVED_DATE)                                       AS APPROVED_DATE_BS,
                  TR.APPROVED_REMARKS                                             AS APPROVED_REMARKS,
                  RAR.EMPLOYEE_ID                                                 AS RECOMMENDER_ID,
                  RAR.FULL_NAME                                                   AS RECOMMENDER_NAME,
                  RAA.EMPLOYEE_ID                                                 AS APPROVER_ID,
                  RAA.FULL_NAME                                                   AS APPROVER_NAME
                FROM (SELECT AD.*,(CASE WHEN EP.STATUS IS NULL THEN 'Not Applied' ELSE 'Not Approved' END) AS REASON
                  FROM HRIS_EMPLOYEE_TRAVEL_REQUEST AD
                  LEFT JOIN HRIS_EMPLOYEE_TRAVEL_REQUEST EP
                  ON (AD.TRAVEL_ID        =EP.REFERENCE_TRAVEL_ID)
                  WHERE AD.REQUESTED_TYPE ='ad'
                  AND AD.STATUS           ='AP'
                  AND (TRUNC(
                    CASE
                      WHEN AD.REQUESTED_DATE IS NULL
                      THEN SYSDATE
                      ELSE AD.REQUESTED_DATE
                    END)- TRUNC(AD.TO_DATE))>7
                  AND (EP.STATUS            =
                    CASE
                      WHEN EP.STATUS IS NOT NULL
                      THEN 'AP'
                    END
                  OR NULL IS NULL )
                  ) TR
                LEFT JOIN HRIS_EMPLOYEES E
                ON (E.EMPLOYEE_ID =TR.EMPLOYEE_ID)
                LEFT JOIN HRIS_EMPLOYEES RE
                ON(RE.EMPLOYEE_ID =TR.RECOMMENDED_BY)
                LEFT JOIN HRIS_EMPLOYEES AE
                ON (AE.EMPLOYEE_ID =TR.APPROVED_BY)
                LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                ON (RA.EMPLOYEE_ID=TR.EMPLOYEE_ID)
                LEFT JOIN HRIS_EMPLOYEES RAR
                ON (RA.RECOMMEND_BY=RAR.EMPLOYEE_ID)
                LEFT JOIN HRIS_EMPLOYEES RAA
                ON(RA.APPROVED_BY=RAA.EMPLOYEE_ID) ORDER BY TR.REQUESTED_DATE DESC";
        return $this->rawQuery($sql);
    }
    
    public function getSameDateApprovedStatus($employeeId, $fromDate, $toDate) {
        $sql = "SELECT COUNT(*) as TRAVEL_COUNT
  FROM HRIS_EMPLOYEE_TRAVEL_REQUEST
  WHERE ((:fromDate BETWEEN FROM_DATE AND TO_DATE)
  OR (:toDate BETWEEN FROM_DATE AND TO_DATE))
  AND STATUS  IN ('AP','CP','CR')
  AND EMPLOYEE_ID = :employeeId
                ";

        $boundedParameter = [];
        $boundedParameter['id'] = $id;
        $boundedParameter['fromDate'] = $fromDate;
        $boundedParameter['toDate'] = $toDate;

        return $this->rawQuery($sql, $boundedParameter)[0];     
        // $statement = $this->adapter->query($sql);
        // $result = $statement->execute();
        // return $result->current();
    }

    public function add(Model $model) {
        $addData=$model->getArrayCopyForDB();
        $this->tableGateway->insert($addData);
    }
    
    public function addItnaryMembers(Model $model) {
        $addData=$model->getArrayCopyForDB();
        $this->tableGatewayItnaryMembers->insert($addData);
    }
    
    public function addItnaryDetails(Model $model) {
        $addData=$model->getArrayCopyForDB();
        $this->tableGatewayItnaryDetails->insert($addData);
    }
    
    public function fetchItnary($id, $employeeId = null){
         $sql = "
             SELECT  
ITNARY_ID,
ITNARY_CODE,
EMPLOYEE_ID,
INITCAP(TO_CHAR(FROM_DT, 'DD-MON-YYYY')) AS FROM_DT,
INITCAP(TO_CHAR(TO_DT, 'DD-MON-YYYY')) AS TO_DT,
NO_OF_DAYS,
PURPOSE,
(select requested_amount from hris_employee_travel_request where itnary_id = :id
    and employee_id = :employeeId) float_money,
TRANSPORT_TYPE,
TOTAL_DAYS,
REMARKS,
LOCKED_FLAG,
STATUS,
CREATED_BY,
CREATED_DT,
MODIFIED_BY,
MODIFIED_DT,
DELETED_BY,
DELETED_DT               
FROM HRIS_TRAVEL_ITNARY
WHERE ITNARY_ID=:id
                ";

        $boundedParameter = [];
        $boundedParameter['id'] = $id;
        $boundedParameter['employeeId'] = $employeeId;

        return $this->rawQuery($sql, $boundedParameter)[0];

        // $statement = $this->adapter->query($sql);
        // $result = $statement->execute();
        // return $result->current();
        
    }
    
    public function fetchItnaryMembers($id){
        $sql =  "
             SELECT IM.*,E. FULL_NAME,
LEAVE_STATUS_DESC(TR.STATUS) AS ITNARY_STATUS
FROM 
HRIS_ITNARY_MEMBERS IM
LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=IM.EMPLOYEE_ID)
LEFT JOIN HRIS_EMPLOYEE_TRAVEL_REQUEST TR ON (TR.EMPLOYEE_ID=IM.EMPLOYEE_ID AND TR.ITNARY_ID=IM.ITNARY_ID)
WHERE IM.ITNARY_ID=:id
                ";
        
        $boundedParameter = [];
        $boundedParameter['id'] = $id;

        return $this->rawQuery($sql, $boundedParameter);
        // $statement = $this->adapter->query($sql);
        // $result = $statement->execute();
        // return Helper::extractDbData($result);
        
    }
    
    public function fetchItnaryDetails($id){
        $sql = "
           SELECT 
 ITD.*,
 TT.TRANSPORT_NAME
 ,TO_CHAR(ITD.DEPARTURE_DT,'DD-MON-YYYY') AS DEPARTURE_DT_AD
 ,TO_CHAR(ITD.ARRIVE_DT,'DD-MON-YYYY') AS ARRIVE_DT_AD
 FROM 
HRIS_ITNARY_DETAILS ITD
LEFT JOIN  HRIS_TRANSPORT_TYPES TT ON (TT.TRANSPORT_CODE=ITD.TRANSPORT_TYPE)
WHERE ITD.ITNARY_ID=:id ORDER BY ITD.SNO asc
                ";

        $boundedParameter = [];
        $boundedParameter['id'] = $id;

        return $this->rawQuery($sql, $boundedParameter);
        // $statement = $this->adapter->query($sql);
        // $result = $statement->execute();
        // return Helper::extractDbData($result);
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function cancel($id, $employeeId){
      $sql = "UPDATE HRIS_EMPLOYEE_TRAVEL_REQUEST SET STATUS = 'C' WHERE EMPLOYEE_ID = :employeeId AND ITNARY_ID = :id";
      $boundedParameter = [];
      $boundedParameter['id'] = $id;
      $boundedParameter['employeeId'] = $employeeId;

      $this->rawQuery($sql, $boundedParameter);
    }

}
