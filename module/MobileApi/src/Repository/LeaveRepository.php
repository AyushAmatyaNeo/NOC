<?php

namespace MobileApi\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Model\LeaveAssign;
use LeaveManagement\Model\LeaveMaster;
use SelfService\Model\LeaveSubstitute;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LeaveRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function getApproval($employeeId, $id = null) {
        $idCondition = $id != null ? "AND LR.ID = {$id}" : "";
        $sql = "
                SELECT LR.ID,
                  LR.EMPLOYEE_ID,
                  INITCAP(E.FULL_NAME)                             AS FULL_NAME,
                  INITCAP(L.LEAVE_ENAME)                           AS LEAVE_ENAME,
                  INITCAP(TO_CHAR(LR.START_DATE, 'DD-MON-YYYY'))   AS START_DATE_AD,
                  BS_DATE(TO_CHAR(LR.START_DATE, 'DD-MON-YYYY'))   AS START_DATE_BS,
                  INITCAP(TO_CHAR(LR.END_DATE, 'DD-MON-YYYY'))     AS END_DATE_AD,
                  BS_DATE(TO_CHAR(LR.END_DATE, 'DD-MON-YYYY'))     AS END_DATE_BS,
                  INITCAP(TO_CHAR(LR.REQUESTED_DT, 'DD-MON-YYYY')) AS APPLIED_DATE_AD,
                  BS_DATE(TO_CHAR(LR.REQUESTED_DT, 'DD-MON-YYYY')) AS APPLIED_DATE_BS,
                  LEAVE_STATUS_DESC(LR.STATUS)                     AS STATUS,
                  LR.NO_OF_DAYS,
                  REC.EMPLOYEE_ID                                           AS RECOMMENDER_ID,
                  APP.EMPLOYEE_ID                                           AS APPROVER_ID,
                  REC.FULL_NAME                                             AS RECOMMENDER_NAME,
                  APP.FULL_NAME                                             AS APPROVER_NAME,
                  REC_APP_ROLE({$employeeId},RA.RECOMMEND_BY,RA.APPROVED_BY)      AS ROLE,
                  REC_APP_ROLE_NAME({$employeeId},RA.RECOMMEND_BY,RA.APPROVED_BY) AS YOUR_ROLE,
                  LS.APPROVED_FLAG                                          AS SUB_APPROVED_FLAG,
                  INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))         AS SUB_APPROVED_DATE,
                  LS.EMPLOYEE_ID                                            AS SUB_EMPLOYEE_ID,
                  SUB.FULL_NAME                                             AS SUB_EMPLOYEE_NAME
                FROM HRIS_EMPLOYEE_LEAVE_REQUEST LR
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID=LR.LEAVE_ID
                LEFT JOIN HRIS_RECOMMENDER_APPROVER RA
                ON LR.EMPLOYEE_ID=RA.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES E
                ON LR.EMPLOYEE_ID=E.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEES REC
                ON REC.EMPLOYEE_ID=RA.RECOMMEND_BY
                LEFT JOIN HRIS_EMPLOYEES APP
                ON APP.EMPLOYEE_ID=RA.APPROVED_BY
                LEFT JOIN HRIS_LEAVE_SUBSTITUTE LS
                ON LR.ID = LS.LEAVE_REQUEST_ID
                LEFT JOIN HRIS_EMPLOYEES SUB
                ON LS.EMPLOYEE_ID =SUB.EMPLOYEE_ID
                WHERE E.STATUS    ='E'
                AND E.RETIRED_FLAG='N'
                AND (LR.STATUS = 'RQ' AND RA.RECOMMEND_BY = {$employeeId} OR (LR.STATUS = 'AP' AND RA.APPROVED_BY = {$employeeId} OR (LR.STATUS='RC' AND RA.APPROVED_BY={$employeeId})))
                {$idCondition}
                ORDER BY LR.REQUESTED_DT DESC
";

    // print_r($sql);
//die();
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getSubstituteApprovalByEmpId($employeeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_BS"),
            new Expression("INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY')) AS APPROVED_DT"),
            new Expression("INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY')) AS RECOMMENDED_DT"),
            new Expression("LA.STATUS AS STATUS"),
            new Expression("LA.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LA.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("LA.REMARKS AS REMARKS"),
            new Expression("LA.NO_OF_DAYS AS NO_OF_DAYS"),
            new Expression("LA.ID AS ID"),
            new Expression("LA.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("LA.APPROVED_BY AS APPROVED_BY")
                ], true);

        $select->from(['LA' => LeaveApply::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")])
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression("INITCAP(L.LEAVE_ENAME)")])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=LA.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E1.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LA.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left")
                ->join(['LS' => "HRIS_LEAVE_SUBSTITUTE"], "LS.LEAVE_REQUEST_ID=LA.ID", ["SUB_EMPLOYEE_ID" => "EMPLOYEE_ID", "SUB_APPROVED_DATE_AD" => new Expression("INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))"), "SUB_APPROVED_DATE_BS" => new Expression("BS_DATE(LS.APPROVED_DATE)"), "SUB_APPROVED_FLAG" => "APPROVED_FLAG"], "left");

        $select->where([
            "L.STATUS='E'",
            "LS.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->order("LA.REQUESTED_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getSubstituteApprovalById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.START_DATE, 'DD-MON-YYYY')) AS FROM_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.END_DATE, 'DD-MON-YYYY')) AS TO_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_AD"),
            new Expression("BS_DATE(TO_CHAR(LA.REQUESTED_DT, 'DD-MON-YYYY')) AS REQUESTED_DT_BS"),
            new Expression("INITCAP(TO_CHAR(LA.APPROVED_DT, 'DD-MON-YYYY')) AS APPROVED_DT"),
            new Expression("INITCAP(TO_CHAR(LA.RECOMMENDED_DT, 'DD-MON-YYYY')) AS RECOMMENDED_DT"),
            new Expression("LA.STATUS AS STATUS"),
            new Expression("LA.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LA.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("LA.REMARKS AS REMARKS"),
            new Expression("LA.NO_OF_DAYS AS NO_OF_DAYS"),
            new Expression("LA.ID AS ID"),
            new Expression("LA.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("LA.APPROVED_BY AS APPROVED_BY")
                ], true);

        $select->from(['LA' => LeaveApply::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")])
                ->join(['L' => 'HRIS_LEAVE_MASTER_SETUP'], "L.LEAVE_ID=LA.LEAVE_ID", ['LEAVE_CODE', 'LEAVE_ENAME' => new Expression("INITCAP(L.LEAVE_ENAME)")])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=LA.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E1.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LA.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left")
                ->join(['LS' => "HRIS_LEAVE_SUBSTITUTE"], "LS.LEAVE_REQUEST_ID=LA.ID", ["SUB_EMPLOYEE_ID" => "EMPLOYEE_ID", "SUB_APPROVED_DATE_AD" => new Expression("INITCAP(TO_CHAR(LS.APPROVED_DATE, 'DD-MON-YYYY'))"), "SUB_APPROVED_DATE_BS" => new Expression("BS_DATE(LS.APPROVED_DATE)"), "SUB_APPROVED_FLAG" => "APPROVED_FLAG"], "left");

        $select->where([
            "L.STATUS='E'",
            "LA.ID=" . $id
        ]);
        $select->order("LA.REQUESTED_DT DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function updateSubstituteApproval(Model $model, $id) {
        $gateway = new TableGateway(LeaveSubstitute::TABLE_NAME, $this->adapter);
        $gateway->update($model->getArrayCopyForDB(), [LeaveSubstitute::LEAVE_REQUEST_ID => $id]);
    }

    public function getEmployeeLeave($employeeId) {
      //  $sql = new Sql($this->adapter);
      //  $select = $sql->select();
       // $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LeaveMaster::class, [LeaveMaster::LEAVE_ENAME], NULL, NULL, NULL, NULL, 'L', false), false);
       // $select->from(['L' => LeaveMaster::TABLE_NAME]);
       // $select->join(['LA' => LeaveAssign::TABLE_NAME], "LA." . LeaveAssign::LEAVE_ID . "=" . "L." . LeaveMaster::LEAVE_ID, [], 'left');
       // $select->where(["L.STATUS= 'E'"]);
       /// $select->where(["LA.EMPLOYEE_ID" => $employeeId]);
      //  $select->order([LeaveMaster::LEAVE_ENAME => Select::ORDER_ASCENDING]);
      //  $statement = $sql->prepareStatementForSqlObject($select);
	  
	  $sql="SELECT
     distinct l.leave_id AS leave_id,
    l.leave_code AS leave_code,
    initcap(l.leave_ename) AS leave_ename,
    l.leave_lname AS leave_lname,
    l.allow_halfday AS allow_halfday,
    l.default_days AS default_days,
    l.fiscal_year AS fiscal_year,
    l.carry_forward AS carry_forward,
    l.cashable AS cashable,
    l.created_dt AS created_dt,
    l.modified_dt AS modified_dt,
    l.status AS status,
    l.remarks AS remarks,
    l.created_by AS created_by,
    l.modified_by AS modified_by,
    l.paid AS paid,
    l.max_accumulate_days AS max_accumulate_days,
    l.is_substitute AS is_substitute,
    l.allow_grace_leave AS allow_grace_leave,
    l.is_monthly AS is_monthly,
    l.is_substitute_mandatory AS is_substitute_mandatory,
    l.assign_on_employee_setup AS assign_on_employee_setup,
    l.is_prodata_basis AS is_prodata_basis,
    l.enable_substitute AS enable_substitute,
    l.company_id AS company_id,
    l.branch_id AS branch_id,
    l.department_id AS department_id,
    l.designation_id AS designation_id,
    l.position_id AS position_id,
    l.service_type_id AS service_type_id,
    l.employee_type AS employee_type,
    l.gender_id AS gender_id,
    l.employee_id AS employee_id,
    l.day_off_as_leave AS day_off_as_leave,
    l.holiday_as_leave AS holiday_as_leave
FROM
    hris_leave_master_setup l
    LEFT JOIN hris_employee_leave_assign la ON la.leave_id = l.leave_id
WHERE
        l.status = 'E'
    AND
        la.employee_id ={$employeeId}
ORDER BY leave_ename ASC";
		$statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
