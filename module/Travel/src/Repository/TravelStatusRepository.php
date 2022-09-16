<?php
namespace Travel\Repository;

use Application\Helper\EntityHelper;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class TravelStatusRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, $tableName);
    }

    public function getFilteredRecord($search):array {
        $condition = "";
        $condition = EntityHelper::getSearchConditonBounded($search['companyId'], $search['branchId'], $search['departmentId'], $search['positionId'], $search['designationId'], $search['serviceTypeId'], $search['serviceEventTypeId'], $search['employeeTypeId'], $search['employeeId'], null, null, $search['functionalTypeId']);
        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $condition['parameter']);

        if (isset($search['fromDate']) && $search['fromDate'] != null) {
            $boundedParameter['fromDate'] = $search['fromDate'];
            $condition['sql'] .= " AND TR.FROM_DATE>=TO_DATE(?,'DD-MM-YYYY') ";
        }
        if (isset($search['fromDate']) && $search['toDate'] != null) {
            $boundedParameter['toDate'] = $search['toDate'];
            $condition['sql'] .= " AND TR.TO_DATE<=TO_DATE(?,'DD-MM-YYYY') ";
        }


        if (isset($search['status']) && $search['status'] != null && $search['status'] != -1) {
            if (gettype($search['status']) === 'array') {
                $csv = "";
                for ($i = 0; $i < sizeof($search['status']); $i++) {
                    if ($i == 0) {
                        $boundedParameter["status".$i] = $search['status'][$i];
                        $csv = "?".$i;
                    } else {
                        $boundedParameter["status".$i] = $search['status'][$i];
                        $csv .= ",?".$i;
                    }
                }
                $condition['sql'] .= "AND TR.STATUS IN ({$csv})";
            } else {
                $boundedParameter['status'] = $search['status'];
                $condition['sql'] .= "AND TR.STATUS IN (?)";
            }
        }
        
        if (isset($search['itnaryId']) && $search['itnaryId'] != null && $search['itnaryId'] != -1) {
            $boundedParameter['itnaryId'] = $search['itnaryId'];
            $condition['sql'] .= "AND TR.ITNARY_ID IN (?)";
        }
 
        $sql = "SELECT TR.TRAVEL_ID                        AS TRAVEL_ID,
        (select count(*) as count_expense from hris_employee_travel_request 
                  where requested_type='ep' and reference_travel_id = TR.TRAVEL_ID and status not in ('R','C'))
                  AS EXPENSE_COUNT,
                  TR.TRAVEL_CODE                           AS TRAVEL_CODE,
                  TR.ITNARY_ID                           AS ITNARY_ID,
                  CASE WHEN TR.ITNARY_ID IS  NOT NULL THEN 'Y' ELSE 'N' END AS ITNARY_CHECK,
                  TR.EMPLOYEE_ID                           AS EMPLOYEE_ID,
                  TR.HARDCOPY_SIGNED_FLAG                  AS HARDCOPY_SIGNED_FLAG,
                  (CASE WHEN TR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT,
                  E.EMPLOYEE_CODE                          AS EMPLOYEE_CODE,
                  E.FULL_NAME                              AS EMPLOYEE_NAME,
                  TO_CHAR(TR.REQUESTED_DATE,'DD-MON-YYYY') AS REQUESTED_DATE_AD,
                  BS_DATE(TR.REQUESTED_DATE)               AS REQUESTED_DATE_BS,
                  TO_CHAR(TR.FROM_DATE,'DD-MON-YYYY')      AS FROM_DATE_AD,
                  BS_DATE(TR.FROM_DATE)                    AS FROM_DATE_BS,
                  TO_CHAR(TR.TO_DATE,'DD-MON-YYYY')        AS TO_DATE_AD,
                  BS_DATE(TR.TO_DATE)                      AS TO_DATE_BS,
                  days_between(FROM_DATE,TO_DATE) + 1                AS DAYS,
                  TR.DESTINATION                           AS DESTINATION,
                  TR.DEPARTURE                             AS DEPARTURE,
                  TR.PURPOSE                               AS PURPOSE,
                  TR.VOUCHER_NO                            AS VOUCHER_NO,
                  TR.REQUESTED_TYPE                        AS REQUESTED_TYPE,
                  (
                  CASE
                    WHEN TR.REQUESTED_TYPE = 'ad'
                    THEN 'Advance'
                    ELSE 'Expense'
                  END)                                                            AS REQUESTED_TYPE_DETAIL,
                  IFNULL(TR.REQUESTED_AMOUNT,0)                                      AS REQUESTED_AMOUNT,
                  TR.TRANSPORT_TYPE                                               AS TRANSPORT_TYPE,
                  TR.TRANSPORT_TYPE_LIST                                               AS TRANSPORT_TYPE_LIST,
                  INITCAP(HRIS_GET_FULL_FORM(TR.TRANSPORT_TYPE,'TRANSPORT_TYPE')) AS TRANSPORT_TYPE_DETAIL,
                  TO_CHAR(TR.DEPARTURE_DATE)                                      AS DEPARTURE_DATE_AD,
                  BS_DATE(TR.DEPARTURE_DATE)                                      AS DEPARTURE_DATE_BS,
                  TO_CHAR(TR.RETURNED_DATE)                                       AS RETURNED_DATE_AD,
                  BS_DATE(TR.RETURNED_DATE)                                       AS RETURNED_DATE_BS,
                  TR.REMARKS                                                      AS REMARKS,
                  TR.STATUS                                                       AS STATUS,
                  LEAVE_STATUS_DESC(TR.STATUS) || ''                                    AS STATUS_DETAIL,
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
                FROM HRIS_EMPLOYEE_TRAVEL_REQUEST TR
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
                ON(RA.APPROVED_BY=RAA.EMPLOYEE_ID)
                WHERE 1          =1 {$condition['sql']}";
           
           $finalSql = $this->getPrefReportQuery($sql);

          //  echo('<pre>');print_r($finalSql);die;
           return $this->rawQuery($finalSql, $boundedParameter);     
         
    }

    public function notSettled(): array {
        $sql = "SELECT 
        (select travel_id from hris_employee_travel_request where reference_travel_id = TR.TRAVEL_ID
and status not in ('C','R')) as reference_travel_id,
TR.TRAVEL_ID                   AS TRAVEL_ID,
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
         IFNULL(TR.REQUESTED_AMOUNT,0) AS REQUESTED_AMOUNT,
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
        LEAVE_STATUS_DESC(TR.STATUS) || ''                                    AS STATUS_DETAIL,
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
      FROM (SELECT distinct AD.*,(CASE WHEN (EP.STATUS IS NULL or EP.STATUS in ('C','R')) THEN 'Not Applied' ELSE 'Not Approved' END) AS REASON
        FROM HRIS_EMPLOYEE_TRAVEL_REQUEST AD
        LEFT JOIN HRIS_EMPLOYEE_TRAVEL_REQUEST EP
        ON (AD.TRAVEL_ID        =EP.REFERENCE_TRAVEL_ID)
        WHERE AD.REQUESTED_TYPE ='ad'
        AND AD.STATUS           ='AP'
        AND ((EP.status not in (select case when count(distinct status)>0 then 'C' else '' end as status 
        from hris_employee_travel_request where reference_travel_id = AD.travel_id and status not in ('C', 'R')) and EP.status not in ('AP'))
        or EP.status is null)

          
          
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

        // echo '<pre>';print_r($sql);die;
        return $this->rawQuery($sql);
    }
    
    public function getSameDateApprovedStatus($employeeId, $fromDate, $toDate) {
      $boundedParameter = [];
      $boundedParameter['fromDate'] = $fromDate;
      $boundedParameter['toDate'] = $toDate;
      $boundedParameter['employeeId'] = $employeeId;
        $sql = "SELECT COUNT(*) as TRAVEL_COUNT
  FROM HRIS_EMPLOYEE_TRAVEL_REQUEST
  WHERE ((':fromDate' BETWEEN FROM_DATE AND TO_DATE)
  OR (':toDate' BETWEEN FROM_DATE AND TO_DATE))
  AND STATUS  IN ('AP','CP','CR')
  AND EMPLOYEE_ID = :employeeId
                ";

        $result = $this->rawQuery($sql, $boundedParameter)[0];
    }

    public function expenseClaim(): array {
      $sql = "SELECT DISTINCT 
      TR.TRAVEL_ID                   AS TRAVEL_ID,
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
        TR.REQUESTED_TYPE                   AS REQUESTED_TYPE,
        TR.VOUCHER_NO                       AS VOUCHER_NO,
  
        (CASE 
        WHEN TR.status = 'C' 
        THEN 'Cancelled'
        WHEN TR.status = 'AP'
        THEN 'Approved'
        WHEN TR.status = 'RQ'
        THEN 'Pending'
        Else 'NAN'
        END) as STATUS,
        tr.requested_amount as REQUESTED_AMOUNT,
  
        (case
        when tr.status = 'RQ'
        then 'Y'
        Else 'N'
        END) as ALLOW_EDIT
  
  FROM hris_travel_expense HTE
    left join hris_employee_travel_request TR on (tr.travel_id = HTE.travel_id)
    left join hris_employees E on (E.employee_id = tr.employee_id)
    where tr.travel_id is not null
   ";
      
       $statement = $this->adapter->query($sql);
       $result = $statement->execute();
       return Helper::extractDbData($result);
  }

  public function getTravelTypeDetail($travelTypeId){
    $sql="select INITCAP(HRIS_GET_FULL_FORM('$travelTypeId',
    'TRANSPORT_TYPE')) as detail from dummy";
    return $this->rawQuery($sql);
  }

  public function deleteApprovedTravel($travelId){
    $sql = "select count(*) as count_expense from hris_employee_travel_request where requested_type='ep' and reference_travel_id = $travelId and status not in ('R','C')";
    $statement = $this->adapter->query($sql);
    $result = $statement->execute()->current();
    $countExpense = $result['COUNT_EXPENSE'];
    if ($countExpense>0){
      return "Can't apply";
    }else{
      $sql="update hris_employee_travel_request set status='R' where travel_id = $travelId";
      $this->rawQuery($sql);
      $this->rawQuery("
                 DO
                    BEGIN
                    DECLARE V_FROM_DATE DATE;
                    DECLARE V_TO_DATE DATE;
                    DECLARE V_EMPLOYEE_ID NUMBER(7,0);
                        BEGIN
                          SELECT 
                            FROM_DATE,
                            TO_DATE,
                            EMPLOYEE_ID
                          INTO
                            V_FROM_DATE,
                            V_TO_DATE,
                            V_EMPLOYEE_ID
                          FROM 
                            HRIS_EMPLOYEE_TRAVEL_REQUEST
                          WHERE
                            TRAVEL_ID = $travelId;

                          HRIS_REATTENDANCE(V_FROM_DATE,V_EMPLOYEE_ID,V_TO_DATE);
                         END;
                    END;
                    ");
        return;
    }
  }
}
