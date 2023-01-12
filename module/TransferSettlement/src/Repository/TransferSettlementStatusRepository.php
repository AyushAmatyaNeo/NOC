<?php
namespace TransferSettlement\Repository;

use Application\Helper\EntityHelper;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use SelfService\Model\TransferSettlement;

class TransferSettlementStatusRepository extends HrisRepository {
    protected $tableGateway;
    protected $adapter;
    
    public function __construct(AdapterInterface $adapter, $tableName = null) {
      $this->adapter = $adapter;
      $this->tableGateway = new TableGateway(TransferSettlement::TABLE_NAME, $adapter);
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
        or EP.status is null))

          
          
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

  public function getStatusList($search) {
    $condition = "";
    $boundedParameter = [];
    if (isset($search['fromDate']) && $search['fromDate'] != null) {
      $boundedParameter['fromDate'] = $search['fromDate'];
        $condition .= " AND hjh.START_DATE=TO_DATE('{$search['fromDate']}','DD-MON-YYYY') ";
    }
    if (isset($search['toDate']) && $search['toDate'] != null) {
      $boundedParameter['toDate'] = $search['toDate'];
        $condition .= " AND hjh.EVENT_DATE=TO_DATE('{$search['toDate']}','DD-MON-YYYY') ";
    }

    if (isset($search['status']) && $search['status'] != null && $search['status'] != -1) {
        if (gettype($search['status']) === 'array') {
            $csv = "";
            for ($i = 0; $i < sizeof($search['status']); $i++) {
                if ($i == 0) {
                    $boundedParameter["status".$i] = $search['status'][$i];
                    $csv = "?".$i;
                } else {
                    $csv .= ",:status".$i; 
                    $boundedParameter["status".$i] = $search['status'][$i];
                }
            }
            $condition .= "AND hts.STATUS IN ({$csv})";
        } else {
            $condition .= "AND hts.STATUS IN ('{$search['status']}')";
            $boundedParameter['status'] = $search['status'];
        }
      }
      $sql = "select
      hjh.job_history_id,
      hjh.start_date,
      hts.serial_number,
      BS_DATE(hjh.start_date) as start_date_bs,
      hjh.end_date,
      hjh.event_date,
      BS_DATE(hjh.event_date) as event_date_bs,
      hb1.branch_name as from_branch,
      hb2.branch_name as to_branch ,
    hjh.to_branch_id,
      E.employee_code,
      E.full_name,
      leave_status_desc(hts.status) as status_detail,
      hts.status,
      hts.weight_req_amt + hts.yearly_setttlement_req_amt + hts.req_sum as req_sum,
      hts.weight_ap_amt + hts.yearly_setttlement_ap_amt + hts.ap_sum as ap_sum 
  from hris_job_history hjh 
  left join hris_branches hb1 on (hb1.branch_id = hjh.from_branch_id) 
  left join hris_branches hb2 on (hb2.branch_id = hjh.to_branch_id) 
  left join hris_employees E on (E.employee_id = hjh.employee_id) 
  left join (select
      job_history_id,
      employee_id,
      status,
      serial_number,
      ifnull(weight_req_amt,
    0) as weight_req_amt,
      ifnull(weight_ap_amt,
    0) as weight_ap_amt,
      ifnull(yearly_setttlement_ap_amt,
    0) as yearly_setttlement_ap_amt,
      ifnull(yearly_setttlement_req_amt,
    0) as yearly_setttlement_req_amt,
      sum(ifnull(total_tada_amt,
    0)) + sum(ifnull(plane_expense_req_amt,
    0)) + sum(ifnull(misc_expense_req_amt,
    0)) + sum(ifnull(vehicle_expense_req_amt,
    0)) as REQ_SUM,
      sum(ifnull(total_tada_amt,
    0)) + sum(ifnull(plane_expense_ap_amt,
    0)) + sum(ifnull(misc_expense_ap_amt,
    0)) + sum(ifnull(vehicle_expense_ap_amt,
    0)) as AP_SUM 
    from hris_transfer_settlement 
    group by job_history_id,
      employee_id,
      status,
    yearly_setttlement_req_amt,
      weight_req_amt,
    weight_ap_amt,
      yearly_setttlement_ap_amt,
      serial_number ) hts on (hts.job_history_id = hjh.job_history_id) 
  where hts.status is not null {$condition}
  ";
      // print_r($sql);die;
      return $this->rawQuery($sql);
}

  public function fetchByJobHistoryId($id, $serialNumber) {
    $sql = "select HTS.*, leave_status_desc(hts.status) as STATUS_DETAIL, HE.FULL_NAME,HE.EMPLOYEE_CODE,HD.DESIGNATION_TITLE,HE.SALARY,HL.LOCATION_EDESC,
  to_char(hts.from_date, 'DD-Mon-YYYY') as FROM_DATE_FORMATED,
    HE2.FULL_NAME as APPROVED_BY from HRIS_TRANSFER_SETTLEMENT HTS
    LEFT JOIN HRIS_EMPLOYEES HE ON (HE.EMPLOYEE_ID = HTS.EMPLOYEE_ID)
    LEFT JOIN HRIS_DESIGNATIONS HD ON (HD.DESIGNATION_ID = HE.DESIGNATION_ID)
    LEFT JOIN HRIS_LOCATIONS HL ON (HL.LOCATION_ID = HE.LOCATION_ID)
    LEFT JOIN HRIS_EMPLOYEES HE2 ON (HE2.EMPLOYEE_ID = HTS.APPROVED_BY)
    where HTS.job_history_id = {$id} and HTS.serial_number = {$serialNumber} ";
    return $this->rawQuery($sql);
  }

  public function getTransferDetails($id){
    $sql = "select JH.*, hris_check_availability_of_yearly_settlement(JH.job_history_id) as ELIGIBLE_FOR_SETTLEMENT_AMT,
    B.Branch_name as TO_BRANCH_NAME,
    C.company_name,
  L.LOCATION_EDESC from hris_job_history JH 
    left join hris_branches B on (B.branch_id = JH.to_branch_id) 
    left join hris_employees E on (E.employee_id = JH.employee_id) 
    left join hris_company C on (C.company_id = E.company_id)
    left join hris_locations L on (L.location_id = E.location_id)
    where JH.job_history_id = {$id}";
    return $this->rawQuery($sql)[0];
}

public function edit(Model $model, $id) {
  $temp = $model->getArrayCopyForDB();
  $this->tableGateway->update($temp, [TransferSettlement::TRANSFER_SETTLEMENT_ID => $id]);
}

public function getTotalNoOfAttachment($id, $serialNumber){
  $sql = "select count(*) from HRIS_TRANSFER_SETTLEMENT_FILES where JOB_HISTORY_ID = {$id} and serial_number = {$serialNumber}";
  return $this->rawQuery($sql);
}

public function fetchById($id) {
  $sql = new Sql($this->adapter);
  $employeeId = $search['employeeId'];
  $select = $sql->select();
  $select->columns([
      new Expression("JH.JOB_HISTORY_ID AS JOB_HISTORY_ID"),
      new Expression("INITCAP(TO_CHAR(JH.START_DATE, 'DD-MON-YYYY')) AS START_DATE_AD"),
      new Expression("BS_DATE(JH.START_DATE) AS START_DATE_BS"),
      new Expression("INITCAP(TO_CHAR(JH.END_DATE, 'DD-MON-YYYY')) AS END_DATE_AD"),
      new Expression("BS_DATE(JH.END_DATE) AS END_DATE_BS"),
      new Expression("INITCAP(TO_CHAR(JH.EVENT_DATE, 'DD-MON-YYYY')) AS EVENT_DATE_AD"),
      new Expression("BS_DATE(JH.EVENT_DATE) AS EVENT_DATE_BS"),
      new Expression ("case when  ((select count(*) from HRIS_TRANSFER_SETTLEMENT where job_history_id = JH.job_history_id and 
      status not in ('AP','C','R')) > 0) then
      'N' else 'Y' end as allow_add"),
      ], true);

  $select->from(['JH' => 'HRIS_JOB_HISTORY'])
      ->join(['E' => 'HRIS_EMPLOYEES'], 'E.EMPLOYEE_ID=JH.EMPLOYEE_ID', ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
      ->join(['B' => "HRIS_BRANCHES"], "B.BRANCH_ID=JH.TO_BRANCH_ID", ['TO_BRANCH' => new Expression("INITCAP(B.BRANCH_NAME)")], "left")
      ->join(['DP' => "HRIS_DEPARTMENTS"], "DP.DEPARTMENT_ID=JH.TO_DEPARTMENT_ID", ['TO_DEPARTMENT' => new Expression("INITCAP(DP.DEPARTMENT_NAME)")], "left")
      ->join(['DS' => "HRIS_DESIGNATIONS"], "DS.DESIGNATION_ID=JH.TO_DESIGNATION_ID", ['TO_DESIGNATION' => 'DESIGNATION_TITLE'], "left")
      ->join(['P' => "HRIS_POSITIONS"], "P.POSITION_ID=JH.TO_POSITION_ID", ['TO_POSITION' => new Expression("INITCAP(P.POSITION_NAME)")], "left");

  $select->where(["JH.JOB_HISTORY_ID" => $id]);
  $statement = $sql->prepareStatementForSqlObject($select);
  $result = $statement->execute();
  return $result->current();
}

}
