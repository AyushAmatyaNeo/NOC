<?php

namespace ManagerService\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveApply;
use SelfService\Model\OvertimeClaim as OvertimeClaimModel;
use SelfService\Model\OvertimeClaimDetail;
use LeaveManagement\Model\LeaveAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class OvertimeClaimApproveRepository extends HrisRepository  implements RepositoryInterface {

//    private $adapter;
//    private $tableGateway;
    private $tableGatewayDetail;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(OvertimeClaimModel::TABLE_NAME, $adapter);
        $this->tableGatewayDetail = new TableGateway(OvertimeClaimDetail::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        // TODO: Implement add() method.
    }

    public function getAllRequest($id) {
        $sql = "
        SELECT
        OCR.OVERTIME_CLAIM_ID,
        OCR.EMPLOYEE_ID,
        E.FULL_NAME,
        E.EMPLOYEE_CODE,
        EA.FULL_NAME AS APPROVER,
        ER.FULL_NAME AS RECOMMENDER,
        M.MONTH_EDESC || '-' || M.YEAR AS MONTH_DESC ,
        OCR.TOTAL_REQ_OT_HOURS,
        OCR.TOTAL_APP_OT_HOURS,
        OCR.TOTAL_REQ_OT_DAYS, 
        OCR.TOTAL_APP_OT_DAYS,
        OCR.TOTAL_APP_GRAND_TOTAL_LEAVE,
        OCR.created_dt as requested_dt_ad,
        BS_DATE(OCR.created_dt) as requested_dt_bs,
        OCR.TOTAL_REQ_GRAND_TOTAL_LEAVE,
        LEAVE_STATUS_DESC(OCR.STATUS) AS STATUS,
        CASE WHEN OCR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END AS ALLOW_DELETE,
        rec_app_role({$id},  RA.RECOMMEND_BY, RA.APPROVED_BY) AS ROLE,
         rec_app_role_NAME({$id},  RA.RECOMMEND_BY, RA.APPROVED_BY) AS ROLE_NAME
    FROM HRIS_EMPLOYEE_OVERTIME_CLAIM_REQUEST OCR 
    LEFT JOIN HRIS_MONTH_CODE M ON (M.MONTH_ID = OCR.MONTH_ID)
    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID = OCR.EMPLOYEE_ID)
    LEFT JOIN HRIS_RECOMMENDER_APPROVER RA ON (RA.EMPLOYEE_ID = OCR.EMPLOYEE_ID)
    LEFT JOIN HRIS_EMPLOYEES ER ON (ER.EMPLOYEE_ID = RA.RECOMMEND_BY)
    LEFT JOIN HRIS_EMPLOYEES EA ON (EA.EMPLOYEE_ID = RA.APPROVED_BY)
    WHERE 1 =1
    AND ((OCR.STATUS='RQ' AND RA.RECOMMEND_BY = {$id})
    	OR (OCR.STATUS='RC' AND RA.APPROVED_BY = {$id}))";
      // echo('<pre>');print_r($sql);die;
      return $this->rawQuery($sql);
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        $this->tableGateway->update($temp, [OvertimeClaimModel::OVERTIME_CLAIM_ID => $id]);
        if($temp['STATUS']=='AP'){
          EntityHelper::rawQueryResult($this->adapter, "
          CALL HRIS_INSERT_OT_DAYS({$id});
          ");
        }
        return;
    }

    public function editDetail(Model $model, $id) {
      $temp = $model->getArrayCopyForDB();
      $this->tableGatewayDetail->update($temp, [OvertimeClaimDetail::OVERTIME_CLAIM_DETAIL_ID => $id]);
      return;
    }

    public function fetchAll() {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id) {
      $sql = "SELECT
      OCR.OVERTIME_CLAIM_ID,
      OCR.EMPLOYEE_ID,
      E.FULL_NAME,
      EA.FULL_NAME AS APPROVER,
      ER.FULL_NAME AS RECOMMENDER,
      M.MONTH_EDESC || '-' || M.YEAR AS MONTH_DESC ,
      OCR.TOTAL_REQ_OT_HOURS,
      OCR.TOTAL_REQ_SUBSTITUTE_LEAVE,
      OCR.TOTAL_REQ_DASHAIN_TIHAR_LEAVE,
      OCR.TOTAL_REQ_GRAND_TOTAL_LEAVE,
      OCR.TOTAL_REQ_LUNCH_ALLOWANCE,
      OCR.TOTAL_REQ_NIGHT_ALLOWANCE,
      OCR.TOTAL_REQ_LOCKING_ALLOWANCE,
      OCR.TOTAL_REQ_OT_DAYS,
      OCR.TOTAL_APP_OT_HOURS,
      OCR.TOTAL_APP_SUBSTITUTE_LEAVE,
      OCR.TOTAL_APP_DASHAIN_TIHAR_LEAVE,
      OCR.TOTAL_APP_GRAND_TOTAL_LEAVE,
      OCR.TOTAL_APP_LUNCH_ALLOWANCE,
      OCR.TOTAL_APP_NIGHT_ALLOWANCE,
      OCR.TOTAL_APP_LOCKING_ALLOWANCE,
      OCR.TOTAL_APP_OT_DAYS,
      OCR.APP_FESTIVE_OT_DAYS,
      OCR.GRAND_TOTAL_APP_OT_DAYS,
      OCR.created_dt as requested_dt_ad,
      BS_DATE(OCR.created_dt) as requested_dt_bs,
      OCR.TOTAL_APP_SUBSTITUTE_LEAVE,
      LEAVE_STATUS_DESC(OCR.STATUS) AS STATUS,
      CASE WHEN OCR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END AS ALLOW_DELETE 
  FROM HRIS_EMPLOYEE_OVERTIME_CLAIM_REQUEST OCR 
  LEFT JOIN HRIS_MONTH_CODE M ON (M.MONTH_ID = OCR.MONTH_ID)
  LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID = OCR.EMPLOYEE_ID)
  LEFT JOIN HRIS_RECOMMENDER_APPROVER RA ON (RA.EMPLOYEE_ID = OCR.EMPLOYEE_ID)
  LEFT JOIN HRIS_EMPLOYEES ER ON (ER.EMPLOYEE_ID = RA.RECOMMEND_BY)
  LEFT JOIN HRIS_EMPLOYEES EA ON (EA.EMPLOYEE_ID = RA.APPROVED_BY)
  WHERE OCR.OVERTIME_CLAIM_ID = {$id}";
      return $this->rawQuery($sql);
    }

    public function fetchSubDetailById($id){
      $sql = "SELECT 
      FL.functional_level_edesc,
L.location_edesc,
      OCD.OVERTIME_CLAIM_DETAIL_ID as ID,
          OCD.ATTENDANCE_DT,
          BS_DATE(OCD.ATTENDANCE_DT) as ATTENDANCE_DT_BS,
          OCD.CREATED_BY,
          OCD.CREATED_DT,
          OCD.DAY_CODE,
          case when HHM.HOLIDAY_ENAME is null then
       TO_CHAR(HAD.attendance_dt,'DY')
       ELSE
       TO_CHAR(HAD.attendance_dt,'DY') || ' (' 
       || HHM.HOLIDAY_ENAME || ')' END as DAY_DETAIL,
          E.EMPLOYEE_CODE,
          E.FULL_NAME,
          TO_NVARCHAR( OCD.IN_TIME, 'HH:MI AM') as IN_TIME,
          OCD.LEAVE_REWARD,
          OCD.LUNCH_ALLOWANCE,
		OCD.LOCKING_ALLOWANCE,
		OCD.NIGHT_ALLOWANCE,
		OCD.DASHAIN_TIHAR_LEAVE_REWARD,
		OCD.TOTAL_LEAVE_REWARD,
          OCD.MODIFIED_BY,
          OCD.MODIFIED_DT,
          OCD.OT_HOUR,
          TO_NVARCHAR( OCD.OUT_TIME, 'HH:MI AM') as OUT_TIME,
          OCD.OVERTIME_CLAIM_DETAIL_ID,
          OCD.OVERTIME_CLAIM_ID,
          OCD.STATUS,
          OCD.TOTAL_HOUR,
          OCD.TYPE_FLAG,
          OCD.CANCELED_BY_RA,
          OCD.OT_REMARKS,
            CASE WHEN OCD.CANCELED_BY_RA = 'Y' THEN 'CHECKED' ELSE '' END AS CANCEL_STATUS,
          CASE WHEN OCD.TYPE_FLAG = 'L' THEN 'CHECKED' ELSE '' END AS CHECKBOX_STATUS,
          case when (HHM.holiday_code in ('LP',
			'GP',
			'SAP',
			'DH1',
			'DH2',
      'TH1','TH2')) then 
			1
			when (HHM.holiday_code in ('BT',
			'NAW',
			'AST',
			'DAS')) then 
			2
			ELSE
			0
		END as BONUS_MULTI,
		case when (OCD.TYPE_FLAG = 'O' AND OCD.CANCELED_BY_RA = 'N' AND OCD.OT_HOUR >= 6)
          THEN 1 
          when (OCD.TYPE_FLAG = 'O' AND OCD.CANCELED_BY_RA = 'N' AND OCD.OT_HOUR < 6 AND OCD.OT_HOUR >= 4.5) 
          THEN 0.5
          ELSE
          0
          END AS OT_DAYS
          
      FROM HRIS_EMPLOYEE_OVERTIME_CLAIM_DETAIL OCD 
      LEFT JOIN HRIS_EMPLOYEE_OVERTIME_CLAIM_REQUEST OCR ON (OCR.OVERTIME_CLAIM_ID = OCD.OVERTIME_CLAIM_ID)
      LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID = OCR.EMPLOYEE_ID) 
      LEFT JOIN HRIS_ATTENDANCE_DETAIL HAD on (HAD.employee_id = E.employee_id and HAD.attendance_dt = OCD.ATTENDANCE_DT)
      LEFT JOIN HRIS_HOLIDAY_MASTER_SETUP HHM on (HHM.holiday_id = HAD.holiday_id)
      left join hris_functional_levels FL on (E.functional_level_id = FL.functional_level_id)
      left join hris_locations L on (L.location_id = E.location_id)
      WHERE OCD.OVERTIME_CLAIM_ID = {$id} order by OCD.attendance_dt";
      // echo('<pre>');print_r($sql);die;
      return $this->rawQuery($sql);
  }

    public function fetchAttachmentsById($id){
        $boundedParameter = [];
        $boundedParameter['id'] = $id;
      $sql = "SELECT * FROM HRIS_LEAVE_FILES WHERE LEAVE_ID = ?";
      $result = EntityHelper::rawQueryResult($this->adapter, $sql,$boundedParameter);
      return Helper::extractDbData($result);
    }

    public function assignedLeaveDetail($leaveId, $employeeId) {
        $result = $this->tableGatewayLeaveAssign->select(['EMPLOYEE_ID' => $employeeId, 'LEAVE_ID' => $leaveId]);
        return $result->current();
    }

    public function updateLeaveBalance($leaveId, $employeeId, $balance) {
        $this->tableGatewayLeaveAssign->update(["BALANCE" => $balance], ['LEAVE_ID' => $leaveId, 'EMPLOYEE_ID' => $employeeId]);
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }

    public function getAllCancelRequest($id) {
        $boundedParameter = [];
        $boundedParameter['id1'] = $id;
        $boundedParameter['id2'] = $id;
        $boundedParameter['id3'] = $id;
        $sql = "
                SELECT 
                  LA.ID                  AS ID,
                  LA.EMPLOYEE_ID,
                  E.EMPLOYEE_CODE AS EMPLOYEE_CODE,
                  INITCAP(E.FULL_NAME)   AS FULL_NAME,
                  INITCAP(L.LEAVE_ENAME) AS LEAVE_ENAME,
                  INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY'))   AS START_DATE_AD,
                  BS_DATE(LA.START_DATE)   AS START_DATE_BS,
                  INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))     AS END_DATE_AD,
                  BS_DATE(LA.END_DATE)     AS END_DATE_BS,
                  LA.NO_OF_DAYS,
                  INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS APPLIED_DATE_AD,
                  BS_DATE(LA.REQUESTED_DT) AS APPLIED_DATE_BS,
                  LA.HALF_DAY AS HALF_DAY,
                  (CASE WHEN (LA.HALF_DAY IS NULL OR LA.HALF_DAY = 'N') THEN 'Full Day' WHEN (LA.HALF_DAY = 'F') THEN 'First Half' ELSE 'Second Half' END) AS HALF_DAY_DETAIL,
                  LA.GRACE_PERIOD AS GRACE_PERIOD,
                  (CASE WHEN LA.GRACE_PERIOD = 'E' THEN 'Early' WHEN LA.GRACE_PERIOD = 'L' THEN 'Late' ELSE '-' END) AS GRACE_PERIOD_DETAIL,
                   LA.REMARKS AS REMARKS,                  
                  LA.STATUS                            AS STATUS,
                  LEAVE_STATUS_DESC(LA.STATUS) AS STATUS_DETAIL,
                  LA.RECOMMENDED_BY AS RECOMMENDED_BY,
                  INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY')) AS RECOMMENDED_DT,
                  LA.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS,
                  LA.APPROVED_BY AS APPROVED_BY,
                  INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY')) AS APPROVED_DT,
                  LA.APPROVED_REMARKS AS APPROVED_REMARKS,
                  RA.RECOMMEND_BY                                         AS RECOMMENDER,
                  RA.APPROVED_BY                                          AS APPROVER,
                  LS.APPROVED_FLAG                                        AS APPROVED_FLAG,
                  INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))       AS SUB_APPROVED_DATE,
                  LS.EMPLOYEE_ID                                          AS SUB_EMPLOYEE_ID,
                  REC_APP_ROLE(U.EMPLOYEE_ID,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.RECOMMENDER
                  WHEN ALR.R_A_ID IS NOT NULL THEN ALR.R_A_ID ELSE RA.RECOMMEND_BY END,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.APPROVER
                  WHEN ALA.R_A_ID IS NOT NULL THEN ALA.R_A_ID ELSE RA.APPROVED_BY END
                  )      AS ROLE,
                  REC_APP_ROLE_NAME(U.EMPLOYEE_ID,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.RECOMMENDER
                  WHEN ALR.R_A_ID IS NOT NULL THEN ALR.R_A_ID ELSE RA.RECOMMEND_BY END,
                  CASE WHEN L.ENABLE_OVERRIDE='Y'  THEN RAO.APPROVER
                  WHEN ALA.R_A_ID IS NOT NULL THEN ALA.R_A_ID ELSE RA.APPROVED_BY END
                  ) AS YOUR_ROLE,
                  CASE WHEN ( ALR.R_A_ID IS NOT NULL OR ALA.R_A_ID  IS NOT NULL ) THEN 'SECONDARY' ELSE 'PRIMARY' END AS PRI_SEC
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID=LA.LEAVE_ID
                LEFT JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=LA.RECOMMENDED_BY
                LEFT JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=LA.APPROVED_BY
                LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                ON E.EMPLOYEE_ID=RA.EMPLOYEE_ID
                LEFT JOIN HRIS_LEAVE_SUBSTITUTE LS
                ON LA.ID              = LS.LEAVE_REQUEST_ID
                LEFT JOIN HRIS_ALTERNATE_R_A ALR
                ON(ALR.R_A_FLAG='R' AND ALR.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALR.R_A_ID=?)
                LEFT JOIN HRIS_ALTERNATE_R_A ALA
                ON(ALA.R_A_FLAG='A' AND ALA.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALA.R_A_ID=?)
                -- CHANGES
                LEFT JOIN hris_rec_app_override RAO ON E.EMPLOYEE_ID=RAO.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES U
                ON(
                (
                (U.EMPLOYEE_ID   = RA.RECOMMEND_BY
                OR U.EMPLOYEE_ID   =RA.APPROVED_BY
                OR U.EMPLOYEE_ID   =ALR.R_A_ID
                OR U.EMPLOYEE_ID   =ALA.R_A_ID)
               AND L.ENABLE_OVERRIDE='N' )
               OR
               (
                (U.EMPLOYEE_ID   = RAO.recommender
                OR U.EMPLOYEE_ID   =RAO.approver
               ) AND L.ENABLE_OVERRIDE='Y' 
               )
               
                )
                
             -- CHANGES
                
                
                WHERE E.STATUS        ='E'
                AND E.RETIRED_FLAG    ='N'
                AND ((
                (
                (
                (RA.RECOMMEND_BY= U.EMPLOYEE_ID)
                OR(ALR.R_A_ID= U.EMPLOYEE_ID)
                AND L.ENABLE_OVERRIDE='N'
                ) OR (RAO.recommender=U.EMPLOYEE_ID AND L.ENABLE_OVERRIDE='Y' )
                )
                AND LA.STATUS IN ('CR')) 
                OR (
                (
                (
                (RA.APPROVED_BY= U.EMPLOYEE_ID)
                OR(ALA.R_A_ID= U.EMPLOYEE_ID)
                AND L.ENABLE_OVERRIDE='N'
                )
                OR ( RAO.APPROVER=U.EMPLOYEE_ID AND L.ENABLE_OVERRIDE='N' )
                )
                AND LA.STATUS IN ('CP')) )
                AND U.EMPLOYEE_ID=?
                AND (LS.APPROVED_FLAG =
                  CASE
                    WHEN LS.EMPLOYEE_ID IS NOT NULL
                    THEN ('Y')
                  END
                OR LS.EMPLOYEE_ID IS NULL
                OR LA.STATUS IN ('CP','CR'))
                ORDER BY LA.REQUESTED_DT DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result;
    }

    public function fetchByIdWithEmployeeId($id, $employeeId) {
        $boundedParameter = [];
        $boundedParameter['employeeId1'] = $employeeId;
        $boundedParameter['employeeId2'] = $employeeId;
        $boundedParameter['id'] = $id;
        $sql = "SELECT INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS START_DATE,
                  INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY'))    AS REQUESTED_DT,
                  INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY'))     AS APPROVED_DT,
                  LA.STATUS                                           AS STATUS,
                  LA.ID                                               AS ID,
                  LA.EMPLOYEE_ID                                      AS EMPLOYEE_ID,
                  INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY'))        AS END_DATE,
                  LA.NO_OF_DAYS                                       AS NO_OF_DAYS,
                  LA.HALF_DAY                                         AS HALF_DAY,
                  LA.EMPLOYEE_ID                                      AS EMPLOYEE_ID,
                  LA.LEAVE_ID                                         AS LEAVE_ID,
                  LA.REMARKS                                          AS REMARKS,
                  LA.RECOMMENDED_BY                                   AS RECOMMENDED_BY,
                  LA.APPROVED_BY                                      AS APPROVED_BY,
                  LA.RECOMMENDED_REMARKS                              AS RECOMMENDED_REMARKS,
                  LA.APPROVED_REMARKS                                 AS APPROVED_REMARKS,
                  LA.GRACE_PERIOD                                     AS GRACE_PERIOD,
                  L.PAID                                              AS PAID,
                  L.ALLOW_HALFDAY                                     AS ALLOW_HALFDAY,
                  LS.EMPLOYEE_ID                                      AS SUB_EMPLOYEE_ID,
                  INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))   AS SUB_APPROVED_DATE,
                  LS.REMARKS                                          AS SUB_REMARKS,
                  LS.APPROVED_FLAG                                    AS SUB_APPROVED_FLAG,
                  INITCAP(E.FULL_NAME)                                AS FULL_NAME,
                  INITCAP(E1.FULL_NAME)                               AS RECOMMENDED_BY_NAME,
                  INITCAP(E2.FULL_NAME)                               AS APPROVED_BY_NAME,
                  CASE when L.ENABLE_OVERRIDE = 'Y' then RAO.RECOMMENDER 
                  WHEN ALR.R_A_ID IS NOT NULL THEN ALR.R_A_ID ELSE  ra.recommend_by END AS recommender_id,
                  CASE when L.ENABLE_OVERRIDE = 'Y' then RAO.RECOMMENDER 
                  WHEN ALA.R_A_ID IS NOT NULL THEN ALA.R_A_ID ELSE  ra.approved_by END AS approver_id,
                  CASE when L.ENABLE_OVERRIDE = 'Y' then RAOR.FULL_NAME
                  WHEN ALR_E.FULL_NAME IS NOT NULL THEN ALR_E.FULL_NAME ELSE  recm.full_name END AS recommender_name,
                  CASE when L.ENABLE_OVERRIDE = 'Y' then RAOR.FULL_NAME
                  WHEN ALA_E.FULL_NAME IS NOT NULL THEN ALA_E.FULL_NAME ELSE  aprv.full_name END AS approver_name,
                  ELA.TOTAL_DAYS                                      AS TOTAL_DAYS,
                  ELA.BALANCE                                         AS BALANCE
                  ,CASE WHEN SUB_REF_ID IS NOT NULL THEN 
INITCAP(L.LEAVE_ENAME)||'('||SLR.SUB_NAME||')' END AS LEAVE_ENAME
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID=LA.LEAVE_ID
                LEFT JOIN HRIS_LEAVE_SUBSTITUTE LS
                ON LS.LEAVE_REQUEST_ID=LA.ID
                LEFT JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=LA.RECOMMENDED_BY
                LEFT JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=LA.APPROVED_BY
                LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                ON RA.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID=RA.RECOMMEND_BY
                LEFT JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID=RA.APPROVED_BY
                LEFT JOIN HRIS_ALTERNATE_R_A ALR ON(ALR.R_A_FLAG='R' AND ALR.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALR.R_A_ID=?)
                LEFT JOIN HRIS_ALTERNATE_R_A ALA ON(ALA.R_A_FLAG='A' AND ALA.EMPLOYEE_ID=LA.EMPLOYEE_ID AND ALA.R_A_ID=?)
                LEFT JOIN HRIS_EMPLOYEES ALR_E ON(ALR.R_A_ID=ALR_E.EMPLOYEE_ID)
                LEFT JOIN HRIS_EMPLOYEES ALA_E ON(ALA.R_A_ID=ALA_E.EMPLOYEE_ID)
                LEFT JOIN hris_rec_app_override RAO ON E.EMPLOYEE_ID = RAO.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES RAOR ON (RAO.RECOMMENDER = RAOR.EMPLOYEE_ID)
                LEFT JOIN HRIS_EMPLOYEES RAOA ON (RAO.RECOMMENDER = RAOA.EMPLOYEE_ID)
                LEFT JOIN 
                (SELECT 
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
                LEFT JOIN Hris_Holiday_Master_Setup H ON (WH.HOLIDAY_ID=H.HOLIDAY_ID)) SLR ON (SLR.ID=LA.SUB_REF_ID AND SLR.EMPLOYEE_ID=LA.EMPLOYEE_ID),
                  HRIS_LEAVE_MONTH_CODE MTH,
                  HRIS_EMPLOYEE_LEAVE_ASSIGN ELA
                WHERE LA.ID = ?
                AND LA.START_DATE BETWEEN MTH.FROM_DATE AND MTH.TO_DATE
                AND LA.EMPLOYEE_ID            =ELA.EMPLOYEE_ID
                AND LA.LEAVE_ID               =ELA.LEAVE_ID
                AND (ELA.FISCAL_YEAR_MONTH_NO =
                  CASE
                    WHEN l.is_monthly = 'Y' AND l.CARRY_FORWARD = 'N'  THEN mth.leave_year_month_no
                    WHEN l.is_monthly = 'Y' AND l.CARRY_FORWARD = 'Y'  THEN 
                    (SELECT LEAVE_YEAR_MONTH_NO FROM HRIS_LEAVE_MONTH_CODE
                    WHERE (
                    select 
                       case when current_date>max(to_date) then
                        max(to_date)
                        else 
                        current_date
                        end
                        from HRIS_LEAVE_MONTH_CODE
                    ) BETWEEN FROM_DATE AND TO_DATE)
                  END
                OR ELA.FISCAL_YEAR_MONTH_NO IS NULL)";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result->current();
    }
    
    public function getSameDateApprovedStatus($employeeId, $startDate, $endDate) {
         $boundedParameter = [];
         $boundedParameter['employeeId1'] = $employeeId;
         $startDateCondition = "TO_DATE('{$startDate}', 'DD-MON-YYYY')";
         $endDateCondition = "TO_DATE('{$endDate}', 'DD-MON-YYYY')";
        $sql = "SELECT COUNT(*) as LEAVE_COUNT
        FROM HRIS_EMPLOYEE_LEAVE_REQUEST
        WHERE (({$startDateCondition} BETWEEN START_DATE AND END_DATE)
        OR ({$endDateCondition} BETWEEN START_DATE AND END_DATE))
        AND STATUS  IN ('AP','CP','CR')
        AND EMPLOYEE_ID = ?
                ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParameter);
        return $result->current();
    }

    public function classifySubstituteLeave($overtimeClaimDetailId){
      EntityHelper::rawQueryResult($this->adapter, "
      CALL HRIS_CLASSIFY_SUBSTITUTE_LEAVE({$overtimeClaimDetailId});
      ");
      return;
    }

}
