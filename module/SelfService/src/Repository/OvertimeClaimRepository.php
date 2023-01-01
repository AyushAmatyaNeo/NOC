<?php

namespace SelfService\Repository;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\Overtime;
use SelfService\Model\OvertimeClaim;
use SelfService\Model\OvertimeClaimDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;

class OvertimeClaimRepository extends HrisRepository implements RepositoryInterface {

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(OvertimeClaim::TABLE_NAME, $adapter);
        $this->overtimeDetailTableGateway = new TableGateway(OvertimeClaimDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
        return;
    }

    public function addOvertimeClaimDetail(Model $model) {
        $this->overtimeDetailTableGateway->insert($model->getArrayCopyForDB());
        return;
    }

    public function delete($id) {
        $currentDate = Helper::getcurrentExpressionDate();
        $this->tableGateway->update([OvertimeClaim::STATUS => 'C', OvertimeClaim::MODIFIED_DT => $currentDate], [OvertimeClaim::OVERTIME_CLAIM_ID => $id]);
        $this->overtimeDetailTableGateway->update([OvertimeClaimDetail::STATUS => 'C', OvertimeClaimDetail::MODIFIED_DT => $currentDate], [OvertimeClaimDetail::OVERTIME_CLAIM_ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
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
        OCR.TOTAL_APP_OT_HOURS,
        OCR.APP_FESTIVE_OT_DAYS,
        OCR.GRAND_TOTAL_APP_OT_DAYS,
        OCR.TOTAL_REQ_SUBSTITUTE_LEAVE,
        OCR.created_dt as requested_dt_ad,
        OCR.TOTAL_APP_OT_DAYS,
        OCR.TOTAL_APP_LUNCH_ALLOWANCE,
        OCR.TOTAL_APP_NIGHT_ALLOWANCE,
        OCR.TOTAL_APP_LOCKING_ALLOWANCE,
        OCR.TOTAL_APP_DASHAIN_TIHAR_LEAVE,
        OCR.TOTAL_APP_GRAND_TOTAL_LEAVE,
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
            HB.BRANCH_NAME,
            FL.FUNCTIONAL_LEVEL_EDESC,
            TO_NVARCHAR( OCD.IN_TIME, 'HH:MI AM') as IN_TIME,
            OCD.LEAVE_REWARD,
            OCD.MODIFIED_BY,
            OCD.MODIFIED_DT,
            OCD.OT_HOUR,
            TO_NVARCHAR( OCD.OUT_TIME, 'HH:MI AM') as OUT_TIME,
            OCD.OVERTIME_CLAIM_DETAIL_ID,
            OCD.OVERTIME_CLAIM_ID,
            OCD.STATUS,
            OCD.TOTAL_HOUR,
            OCD.OT_REMARKS,
            OCD.TYPE_FLAG,
            OCD.CANCELED_BY_RA,
            OCD.LUNCH_ALLOWANCE,
            OCD.NIGHT_ALLOWANCE,
            OCD.LOCKING_ALLOWANCE,
            CASE WHEN OCD.CANCELED_BY_RA = 'Y' THEN 'CHECKED' ELSE '' END AS CANCEL_STATUS,
            CASE WHEN OCD.TYPE_FLAG = 'L' THEN 'CHECKED' ELSE '' END AS CHECKBOX_STATUS
        FROM HRIS_EMPLOYEE_OVERTIME_CLAIM_DETAIL OCD 
        LEFT JOIN HRIS_EMPLOYEE_OVERTIME_CLAIM_REQUEST OCR ON (OCR.OVERTIME_CLAIM_ID = OCD.OVERTIME_CLAIM_ID)
        LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID = OCR.EMPLOYEE_ID) 
        LEFT JOIN HRIS_BRANCHES HB ON (HB.BRANCH_ID = E.BRANCH_ID)
        LEFT JOIN HRIS_FUNCTIONAL_LEVELS FL ON (FL.FUNCTIONAL_LEVEL_ID = E.FUNCTIONAL_LEVEL_ID)
        LEFT JOIN HRIS_ATTENDANCE_DETAIL HAD on (HAD.employee_id = E.employee_id and HAD.attendance_dt = OCD.ATTENDANCE_DT)
        LEFT JOIN HRIS_HOLIDAY_MASTER_SETUP HHM on (HHM.holiday_id = HAD.holiday_id)
        WHERE OCD.OVERTIME_CLAIM_ID = {$id}  order by OCD.attendance_dt
        ";
        // print_r($sql);die;
        return $this->rawQuery($sql);
    }

    public function fetchByIdForOTDet($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("OT.OVERTIME_ID AS OVERTIME_ID"),
            new Expression("OT.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("INITCAP(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE"),
            new Expression("INITCAP(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE_AD"),
            new Expression("BS_DATE((OT.OVERTIME_DATE)) AS OVERTIME_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD"),
            new Expression("BS_DATE((OT.REQUESTED_DATE)) AS REQUESTED_DATE_BS"),
            new Expression("OT.DESCRIPTION AS DESCRIPTION"),
            new Expression("OT.MODIFIED_DATE AS MODIFIED_DATE"),
            new Expression("OT.REMARKS AS REMARKS"),
            new Expression("OT.TOTAL_HOUR AS TOTAL_HOUR"),
            new Expression("MIN_TO_HOUR(OT.TOTAL_HOUR) AS TOTAL_HOUR_DETAIL"),
            new Expression("OT.STATUS AS STATUS"),
            new Expression("LEAVE_STATUS_DESC(OT.STATUS) AS STATUS_DETAIL"),
            new Expression("OT.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(OT.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("OT.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("OT.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(OT.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("OT.APPROVED_REMARKS AS APPROVED_REMARKS")
        ]);
        $select->from(['OT' => Overtime::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=OT.EMPLOYEE_ID", ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=OT.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E1.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=OT.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=OT.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");

        $select->where(["OT.OVERTIME_ID" => $id]);
        $select->order("OT.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchStartEndTime($otID) {
        $finalResult = [];
        if(isset($otID) && !empty($otID)) {
            $sql = "SELECT TO_CHAR(START_TIME, 'HH:MI AM') AS START_TIME_FORMAT, TO_CHAR(END_TIME, 'HH:MI AM') AS END_TIME_FORMAT FROM HRIS_OVERTIME_DETAIL WHERE OVERTIME_ID = {$otID} AND STATUS = 'E' ";
            $statement = $this->adapter->query($sql);
            $result = $statement->execute();
            $finalResult = $result->current();
        }
        return $finalResult;
    }

    public function getAllByEmployeeId($employeeId, $overtimeDate = null, $status = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("OT.OVERTIME_ID AS OVERTIME_ID"),
            new Expression("OT.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("INITCAP(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(OT.OVERTIME_DATE, 'DD-MON-YYYY')) AS OVERTIME_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(OT.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_BS"),
            new Expression("OT.DESCRIPTION AS IN_DESCRIPTION"),
            new Expression("OT.REMARKS AS REMARKS"),
            new Expression("OT.TOTAL_HOUR AS TOTAL_MIN"),
            new Expression("TRUNC(OT.TOTAL_HOUR/60,2) AS TOTAL_HOUR"),
            new Expression("MIN_TO_HOUR(OT.TOTAL_HOUR) AS TOTAL_HOUR_DETAIL"),
            new Expression("LEAVE_STATUS_DESC(OT.STATUS) AS STATUS"),
            new Expression("OT.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(OT.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("OT.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("OT.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(OT.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("OT.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("(CASE WHEN OT.STATUS = 'RQ' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT"),
            new Expression("(CASE WHEN OT.STATUS IN ('RQ','RC','AP') THEN 'Y' ELSE 'N' END) AS ALLOW_DELETE"),
                ], true);
        $select->from(['OT' => Overtime::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=OT.EMPLOYEE_ID", ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=OT.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E1.FULL_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=OT.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
                ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=OT.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
                ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
                ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");

        $select->where(["E.EMPLOYEE_ID" => $employeeId]);
        if ($overtimeDate != null) {
            $select->where([
                "OT." . Overtime::OVERTIME_DATE . "=TO_DATE('" . $overtimeDate . "','DD-MON-YYYY')"
            ]);
        }
        if ($status != null && $status != -1) {
            $select->where([
                "OT." . Overtime::STATUS . "='" . $status . "'"
            ]);
        }
        $select->where([
            "(TRUNC(SYSDATE)- OT.REQUESTED_DATE) < (
                      CASE
                        WHEN OT.STATUS = 'C'
                        THEN 20
                        ELSE 365
                      END)"
        ]);
        $select->order("OT.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function getAllOTDataByEmployeeId($employeeId) {
        $sql = "SELECT
                    OCR.OVERTIME_CLAIM_ID,
                    OCR.EMPLOYEE_ID,
                    M.MONTH_EDESC || '-' || M.YEAR AS MONTH_DESC ,
                    OCR.TOTAL_REQ_OT_HOURS,
                    OCR.TOTAL_REQ_GRAND_TOTAL_LEAVE,
                    OCR.TOTAL_APP_OT_HOURS,
                    OCR.TOTAL_APP_GRAND_TOTAL_LEAVE,
                    LEAVE_STATUS_DESC(OCR.STATUS) AS STATUS,
                    CASE WHEN OCR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END AS ALLOW_DELETE 
                FROM HRIS_EMPLOYEE_OVERTIME_CLAIM_REQUEST OCR 
                LEFT JOIN HRIS_MONTH_CODE M ON (M.MONTH_ID = OCR.MONTH_ID)
                WHERE OCR.employee_id = {$employeeId}";
        return $this->rawQuery($sql);
    }

    public function executeProcedure($overtimeDate) {
        $dbAdapter = $this->tableGateway->getAdapter();
        $stmt = $dbAdapter->createStatement();
        $stmt->prepare("CALL HRIS_OVERTIME_AUTOMATION(TRUNC(TO_DATE('" . $overtimeDate . "','DD-MON-YYYY')))");
        $stmt->execute();
    }

    public function fetchAttendanceDetail($employeeId, $date) {
        $sql = "SELECT 
        TO_CHAR(IN_TIME, 'HH:MI AM')   AS IN_TIME,
        TO_CHAR(OUT_TIME, 'HH:MI AM')  AS OUT_TIME,
        TOTAL_HOUR,
        TOTAL_HOUR - 480 as OT_MINUTES
        FROM HRIS_ATTENDANCE_DETAIL 
        WHERE EMPLOYEE_ID = :employeeId 
        and ATTENDANCE_DT = TO_DATE(:date, 'DD-MON-YY')";

        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['date'] = $date;
        return $this->rawQuery($sql, $boundedParameter);
    }

    /**
    * Method to find fiscal years
    *
    * @method getAllYearsFromFiscalYears
    * @param fyID, monthNo
    * @return result
    */
    public function getAllYearsFromFiscalYears($fyID, $monthNo){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(array('FISCAL_YEAR_ID', 'YEAR'));
        $select->from('HRIS_MONTH_CODE');

        $select->where(["FISCAL_YEAR_ID" => $fyID, "MONTH_NO" => $monthNo, "STATUS" => 'E']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

       return $result;
    }

    /**
    * Finds all Monts of the Year
    *
    * @method getOTReadMonths
    */
    public function getOTReadMonths() {
        $resultArr = [];
        $resultFinal = [];
        $sql = "SELECT DISTINCT MONTH_NO, MONTH_EDESC FROM HRIS_MONTH_CODE ORDER BY MONTH_NO ASC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $resultArr = Helper::extractDbData($result);

        if(!empty($resultArr)) {
            foreach($resultArr as $resultSingle) {
                $resultFinal[$resultSingle['MONTH_NO']] = $resultSingle['MONTH_EDESC'];
            }
        }

        return $resultFinal;
    }

    /**
    * Method to get fiscal year name from current fiscal year id
    *
    * @method getCurrentFiscalYears
    */
    public function getCurrentFiscalYears($fyID) {
        $resultArr = [];
        $resultFinal = [];

        if(isset($fyID) && !empty($fyID)) {
            $sql = "SELECT DISTINCT FISCAL_YEAR_ID, FISCAL_YEAR_NAME FROM HRIS_FISCAL_YEARS WHERE FISCAL_YEAR_ID = {$fyID}";
            $statement = $this->adapter->query($sql);
            $result = $statement->execute();
            $resultArr = Helper::extractDbData($result);

            if(!empty($resultArr)) {
                $resultFinal[$resultArr[0]['FISCAL_YEAR_ID']] = $resultArr[0]['FISCAL_YEAR_NAME'];
            }
        }

        return $resultFinal;
    }


    /**
    * Method to find months from month code
    *
    * @method getMonthsFromMonth
    * @param none
    * @return result
    */
    public function getMonthsFromMonth($fyID, $monthNo){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(array('MONTH_EDESC'));
        $select->from('HRIS_MONTH_CODE');
        $select->where(["FISCAL_YEAR_ID" => $fyID, "MONTH_NO" => $monthNo, "STATUS" => 'E']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }

    /**
    * Get Functional level Id from EmployeeID
    *
    * @method fetchFCLevelByEmployeeID
    * @param employeeId
    * @return result->current
    */
    public function fetchFCLevelByEmployeeID($employeeId) {
        if(isset($employeeId)){
            $sql = new Sql($this->adapter);
            $select = $sql->select();
            $select->columns(array('FUNCTIONAL_LEVEL_ID'));
            $select->from('HRIS_EMPLOYEES');
            $select->where(["EMPLOYEE_ID" => $employeeId]);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
        }
        return $result->current();
    }

    /**
    * Get Employee overtime data
    *
    * @method fetchOTDByFunctionalLevelId
    * @param functional_level_id, startDate, endDate
    * @return result
    */
    public function fetchEmpOTDetails($functional_level_id, $startDate, $endDate) {
        if(isset($functional_level_id)){
            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns([
                new Expression("HO.OVERTIME_ID AS OVERTIME_ID"),
                new Expression("HO.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("BS_DATE(TO_CHAR(HO.OVERTIME_DATE, 'YYYY/MM/DD')) AS OVERTIME_DATE_FORMATTED"),
                new Expression("HO.OVERTIME_DATE AS OVERTIME_DATE_AD"),
                new Expression("HO.DESCRIPTION AS DESCRIPTION"),
                new Expression("HO.TOTAL_HOUR AS TOTAL_HOUR"),
                new Expression("HO.STATUS AS OVERTIME_STATUS"),
                new Expression("HO.APPROVED_BY AS APPROVED_BY"),
                new Expression("HO.APPROVED_REMARKS AS APPROVED_REMARKS"),
                new Expression("HE.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HE.DESIGNATION_ID AS DESIGNATION_ID"),
                new Expression("HE.FUNCTIONAL_LEVEL_ID AS FUNCTIONAL_LEVEL_ID"),
                new Expression("HE.FUNCTIONAL_TYPE_ID AS FUNCTIONAL_TYPE_ID"),
                new Expression("HE.FULL_NAME AS EMPLOYEE_NAME"),
                new Expression("HD.DESIGNATION_ID AS DESIGNATION_ID"),
                new Expression("HD.DESIGNATION_TITLE AS DESIGNATION_TITLE"),
                new Expression("HOD.OVERTIME_ID AS OVERTIME_ID"),
                new Expression("HOD.STATUS AS OVERTIME_DET_STATUS"),
                new Expression("TO_CHAR(HOD.START_TIME, 'HH:MI AM') AS START_TIME_FORMATTED"),
                new Expression("TO_CHAR(HOD.END_TIME, 'HH:MI AM') AS END_TIME_FORMATTED")
            ], true);

            $select->from(['HO' => "HRIS_OVERTIME"])

            ->join(['HE' => "HRIS_EMPLOYEES"], "HO.EMPLOYEE_ID = HE.EMPLOYEE_ID")
            ->join(['HOD' => "HRIS_OVERTIME_DETAIL"], "HO.OVERTIME_ID = HOD.OVERTIME_ID")
            ->join(['HD' => "HRIS_DESIGNATIONS"], "HE.DESIGNATION_ID = HD.DESIGNATION_ID");

            $select->where(["HOD.STATUS = 'E' AND HO.OVERTIME_DATE BETWEEN TO_DATE('{$startDate}', 'YYYY-MM-DD') AND TO_DATE('{$endDate}', 'YYYY-MM-DD') AND HO.STATUS = 'AP'"]);

            $select->order("HO.OVERTIME_DATE ASC");

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
        }
        return $result;
    }

    /**
    * Read Employee Overtime Data after caluclate
    *
    * @method fetchEmpOTDetailsRead
    * @param fyID, monthNo
    * @return result
    */
    public function fetchEmpOTDetailsRead($fyID, $monthNo) {
        if(isset($fyID) && !empty($fyID) && isset($monthNo) && !empty($monthNo)) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns([
                new Expression("HOT.OT_ID AS OT_ID"),
                new Expression("HOT.FISCAL_YEAR_ID AS FISCAL_YEAR_ID"),
                new Expression("HOT.MONTH_NO AS MONTH_NO"),
                new Expression("HOTD.OT_ID AS OT_ID"),
                new Expression("HOTD.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HOTD.APPROVED_REMARKS AS APPROVED_REMARKS"),
                new Expression("HOTD.DESIGNATION_ID AS DESIGNATION_ID"),
                new Expression("BS_DATE(TO_CHAR(HOTD.OTDATE, 'YYYY/MM/DD')) AS OVERTIME_DATE_FORMATTED"),
                new Expression("TO_CHAR(HOTD.IN_TIME, 'HH:MI AM') AS START_TIME_FORMATTED"),
                new Expression("TO_CHAR(HOTD.OUT_TIME, 'HH:MI AM') AS END_TIME_FORMATTED"),
                new Expression("HOTD.RAW_OT AS RAW_OT"),
                new Expression("HOTD.CALC AS CALC_OT"),
                new Expression("HOTD.SATTA_BIDA AS REP_LEAVE"),
                new Expression("HOTD.SATTA_BIDA_AMOUNT AS REP_LEAVE_AMOUNT"),
                new Expression("HOTD.KHAJA_KARCHA AS LUNCH_ALLOWANCE"),
                new Expression("HOTD.KHAJA_KHARCHA_10PM AS EXTRA_LUNCH_ALLOWANCE"),
                new Expression("HOTD.RATRI_VATTA AS NIGHT_TIME_ALLOWANCE"),
                new Expression("HOTD.APPROVED_BY AS APPROVED_BY"),
                new Expression("HOTD.STATUS AS STATUS"),
                new Expression("HOTD.OVERTIME_ID AS OVERTIME_ID"),
                new Expression("HE.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HE.DESIGNATION_ID AS DESIGNATION_ID"),
                new Expression("HE.FULL_NAME AS EMPLOYEE_NAME"),
                new Expression("HD.DESIGNATION_ID AS DESIGNATION_ID"),
                new Expression("HD.DESIGNATION_TITLE AS DESIGNATION_TITLE")
            ], true);

            $select->from(['HOT' => "HRIS_OT"])

            ->join(['HOTD' => "HRIS_OT_DETAIL"], "HOT.OT_ID = HOTD.OT_ID")
            ->join(['HE' => "HRIS_EMPLOYEES"], "HOTD.EMPLOYEE_ID = HE.EMPLOYEE_ID")
            ->join(['HD' => "HRIS_DESIGNATIONS"], "HOTD.DESIGNATION_ID = HD.DESIGNATION_ID");

            $select->where(["HOTD.STATUS = 'AP' AND IS_CALCULATED = 'Y' AND HOT.FISCAL_YEAR_ID = {$fyID} AND HOT.MONTH_NO = {$monthNo}"]);

            $select->order("HOTD.OTDATE ASC");

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();

        }
        return $result;
    }

    /**
    * Read Employee Overtime Data after caluclate
    *
    * @method fetchEmpOTDetailsReadFilter
    * @param data
    * @return result
    */
    public function fetchEmpOTDetailsReadFilter($data) {
        $fyID = $data['yearId'];
        $monthNo = (int) $data['monthId'];
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];

        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, null, $employeeId, null, null, null);

        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);

        if(isset($fyID) && !empty($fyID) && isset($monthNo) && !empty($monthNo)) {

            $sql = "SELECT 
                HOT.OT_ID AS OT_ID,
                HOT.FISCAL_YEAR_ID AS FISCAL_YEAR_ID,
                HOT.MONTH_NO AS MONTH_NO,
                HOTD.OT_ID AS OT_ID,
                HOTD.EMPLOYEE_ID AS EMPLOYEE_ID,
                HOTD.APPROVED_REMARKS AS APPROVED_REMARKS,
                HOTD.DESIGNATION_ID AS DESIGNATION_ID,
                BS_DATE(TO_CHAR(HOTD.OTDATE, 'YYYY/MM/DD')) AS OVERTIME_DATE_FORMATTED,
                TO_CHAR(HOTD.IN_TIME, 'HH:MI AM') AS START_TIME_FORMATTED,
                TO_CHAR(HOTD.OUT_TIME, 'HH:MI AM') AS END_TIME_FORMATTED,
                HOTD.RAW_OT AS RAW_OT,
                HOTD.CALC AS CALC_OT,
                HOTD.SATTA_BIDA AS REP_LEAVE,
                HOTD.SATTA_BIDA_AMOUNT AS REP_LEAVE_AMOUNT,
                HOTD.KHAJA_KARCHA AS LUNCH_ALLOWANCE,
                HOTD.KHAJA_KHARCHA_10PM AS EXTRA_LUNCH_ALLOWANCE,
                HOTD.RATRI_VATTA AS NIGHT_TIME_ALLOWANCE,
                HOTD.APPROVED_BY AS APPROVED_BY,
                HOTD.STATUS AS STATUS,
                HOTD.OVERTIME_ID AS OVERTIME_ID,
                E.EMPLOYEE_ID AS EMPLOYEE_ID,
                E.DESIGNATION_ID AS DESIGNATION_ID,
                E.FULL_NAME AS EMPLOYEE_NAME,
                HD.DESIGNATION_ID AS DESIGNATION_ID,
                HD.DESIGNATION_TITLE AS DESIGNATION_TITLE

            FROM HRIS_OT HOT
            INNER JOIN HRIS_OT_DETAIL HOTD
            ON HOT.OT_ID = HOTD.OT_ID
            INNER JOIN HRIS_EMPLOYEES E
            ON HOTD.EMPLOYEE_ID = E.EMPLOYEE_ID
            INNER JOIN HRIS_DESIGNATIONS HD
            ON HOTD.DESIGNATION_ID = HD.DESIGNATION_ID

            WHERE HOTD.STATUS = 'AP' AND IS_CALCULATED = 'Y' AND HOT.FISCAL_YEAR_ID = {$fyID} AND HOT.MONTH_NO = {$monthNo} {$searchCondition['sql']}

            ORDER BY HOTD.OTDATE ASC";

            $statement = $this->adapter->query($sql);
            $result = $statement->execute($boundedParameter);
        }

        return $result;
    }



    /**
    * Method to find if the Overtime is already calculated or not
    *
    * @method isOTAlreadyCalulated
    * @param fyID, monthNo
    * @return is_alreadyCalc
    */
    public function isOTAlreadyCalulated($fyID, $monthNo) {        
        if(isset($fyID) && !empty($fyID) && isset($monthNo) && !empty($monthNo)) {
            $sql = "SELECT IS_CALCULATED FROM HRIS_OT WHERE FISCAL_YEAR_ID = {$fyID} AND MONTH_NO = {$monthNo}";
            $statement = $this->adapter->query($sql);
            $result = $statement->execute();
            $finalResult = $result->current();
            if(!empty($finalResult)) {
                $is_alreadyCalc = trim($finalResult['IS_CALCULATED']);
            } else {
                $is_alreadyCalc = 'N';
            }
        }
        return $is_alreadyCalc;
    }

    /**
    * Method to get Previous Month Date
    *
    * @method getPreviousMonDate
    * @param fyID, monthNum
    * @return fy_id
    */
    public function getPreviousMonDate($fyID, $monthNum) {
        $preMonthArr = [];
        if(isset($fyID) && !empty($fyID) && isset($monthNum) && !empty($monthNum)) {
            if($monthNum == (int) 4) {
                $sql = "SELECT FISCAL_YEAR_ID FROM HRIS_MONTH_CODE WHERE FISCAL_YEAR_ID < {$fyID} AND STATUS = 'E' ORDER BY FISCAL_YEAR_ID DESC";
                $statement = $this->adapter->query($sql);
                $result = $statement->execute();
                $finalResult = $result->current();
                if(!empty($finalResult)) {
                    $fy_id = (int) $finalResult['FISCAL_YEAR_ID'];
                    $month_num = $monthNum - 1;
                }
            } else if($monthNum == (int) 1) {
                $fy_id = $fyID;
                $month_num = 12;
            } else {
                $fy_id = $fyID;
                $month_num = $monthNum - 1;
            }
        }
        $preMonthArr = $this->getMonthDateRange($fy_id, $month_num);
        return $preMonthArr;
    }

    /**
    * Gets month range AD starting and ending date on the basis of BS date
    *
    * @method getMonthDateRange
    * @param fyID, monthNum
    * @return PreMonth
    *
    */
    public function getMonthDateRange($fyID, $monthNum) {
        $PreMonth = [];
        if(isset($fyID) && !empty($fyID) && isset($monthNum) && !empty($monthNum)) {
            $sql = "SELECT FISCAL_YEAR_ID, MONTH_ID, MONTH_EDESC, FROM_DATE, TO_DATE, YEAR, MONTH_NO FROM HRIS_MONTH_CODE WHERE FISCAL_YEAR_ID = {$fyID} AND MONTH_NO = {$monthNum}";

            $statement = $this->adapter->query($sql);
            $result = $statement->execute();
            $finalResult = $result->current();
            $PreMonth['from_dt'] = $finalResult['FROM_DATE'];
            $PreMonth['to_dt'] = $finalResult['TO_DATE'];
            $PreMonth['fiscal_year_id'] = $finalResult['FISCAL_YEAR_ID'];
            $PreMonth['year'] = $finalResult['YEAR'];
            $PreMonth['month_no'] = $finalResult['MONTH_NO'];
        }
        return $PreMonth;
    }

    /**
    * Get Nepali month date range from AD
    * Note: It is not used yet, Can use it whenever needed.
    * @method getNepaliMonthDateRange
    * @param fyID
    * @return rawQuery
    */
    public function getNepaliMonthDateRange($fyID){
        if(isset($fyID) && !empty($fyID)) {
            $sql = "SELECT CS.AD_DATE MONTH_START, ADD_DAYS(CS.AD_DATE, CS.DAYS_NO-1) MONTH_END, FN_BS_MONTH(SUBSTR(CS.BS_MONTH,-2,2)) MONTH_NAME FROM HRIS_FISCAL_YEARS FY, CALENDAR_SETUP CS WHERE CS.AD_DATE BETWEEN FY.START_DATE AND FY.END_DATE AND FY.FISCAL_YEAR_NAME = '{$fyID}' ORDER BY CS.AD_DATE";
            $statement = $this->adapter->query($sql);
            $result = $statement->execute();
            $resultArr = Helper::extractDbData($result);
            return $resultArr;
        }
    }

    /**
    * Get Whether shift is Summer or Winter
    *
    * @method isSummer
    * @param overtimeDate
    * @return flg_summer
    */
    public function isSummer($overtimeDate){
        $flg_summer = 0;
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(array('SHIFT_ENAME', 'START_DATE', 'END_DATE'));
        $select->from('HRIS_SHIFTS');
        $select->where(["SHIFT_ENAME = TRIM('Summer') AND STATUS = 'E' AND '{$overtimeDate}' BETWEEN START_DATE AND END_DATE "]);
        
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        $finalResult = $result->current();

        $chk_summer = $finalResult['SHIFT_ENAME'];

        if(trim($finalResult['SHIFT_ENAME']) == 'Summer') {
            $flg_summer = 1;
        }
        return $flg_summer;
    }

    /**
    * Find whether overtime date is Holiday or not
    *
    * @method isHoliday
    * @param empID, overtimeDate, currFYID
    * @return holiday_name
    */
    public function isHoliday($empID, $overtimeDate, $currFYID) {
        $holiday_name = '';

        if(isset($empID) && isset($overtimeDate)){
            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns([
                new Expression("HEH.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HEH.HOLIDAY_ID AS HOLIDAY_ID"),
                new Expression("HHMS.HOLIDAY_ID AS HOLIDAY_ID"),
                new Expression("HFY.FISCAL_YEAR_ID AS FISCAL_YEAR_ID"),
                new Expression("HHMS.HOLIDAY_ENAME AS HOLIDAY_ENAME"),
                new Expression("HHMS.ASSIGN_ON_EMPLOYEE_SETUP AS ASSIGN_ON_EMPLOYEE_SETUP"),
                new Expression("HHMS.STATUS AS STATUS"),
                new Expression("HHMS.START_DATE AS START_DATE"),
                new Expression("HHMS.END_DATE AS END_DATE"),
                new Expression("HHMS.FISCAL_YEAR AS FISCAL_YEAR")
            ], true);

            $select->from(['HEH' => "HRIS_EMPLOYEE_HOLIDAY"])

            ->join(['HHMS' => "HRIS_HOLIDAY_MASTER_SETUP"], "HEH.HOLIDAY_ID = HHMS.HOLIDAY_ID")
            ->join(['HFY' => "HRIS_FISCAL_YEARS"], "HHMS.FISCAL_YEAR = HFY.FISCAL_YEAR_ID");

            $select->where(["HHMS.STATUS = 'E' AND HHMS.ASSIGN_ON_EMPLOYEE_SETUP = 'Y' AND HEH.EMPLOYEE_ID = '{$empID}' AND HHMS.FISCAL_YEAR = '{$currFYID}' AND to_date('{$overtimeDate}', 'YYYY-MM-DD') BETWEEN HHMS.START_DATE AND HHMS.END_DATE"]);

            $statement = $sql->prepareStatementForSqlObject($select);

            $result = $statement->execute();
            $finalResultArr = Helper::extractDbData($result);
            $holiday_name = $finalResultArr[0]['HOLIDAY_ENAME'];
        }
        return $holiday_name;
    }

    /**
    * Fetch the Designation level of the employee
    *
    * @method fetchDesignationLevel
    * @param empID
    * @return resultArr
    */
    public function fetchDesignationLevel($empID) {
        $resultArr = [];
        if(isset($empID)) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();


            $select->columns([
                new Expression("HE.FUNCTIONAL_LEVEL_ID AS FUNCTIONAL_LEVEL_ID"),
                new Expression("HFL.FUNCTIONAL_LEVEL_EDESC AS FUNCTIONAL_LEVEL_EDESC"),
                new Expression("HFL.FUNCTIONAL_LEVEL_ID AS FUNCTIONAL_LEVEL_ID")
            ], true);

            $select->from(['HE' => "HRIS_EMPLOYEES"])
            ->join(['HFL' => "HRIS_FUNCTIONAL_LEVELS"], "HE.FUNCTIONAL_LEVEL_ID = HFL.FUNCTIONAL_LEVEL_ID");

            $select->where(["HE.STATUS = 'E' AND HE.EMPLOYEE_ID={$empID}"]);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();

            $resultArr = $result->current();
        }
        return $resultArr;
    }

    /**
    * Find if the employee works on the Fuel station
    *
    * @method isFuelStationWorker
    * @param empID
    * @return is_AFS
    */
    public function isFuelStationWorker($empID) {
        $loc_code = '';
        $result = [];
        if(isset($empID) && !empty($empID)) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns([
                new Expression("HE.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HE.LOCATION_ID AS LOCATION_ID"),                
                new Expression("HL.LOCATION_ID AS LOCATION_ID"),
                new Expression("HL.LOCATION_CODE AS LOCATION_CODE")
            ], true);

            $select->from(['HE' => "HRIS_EMPLOYEES"])
            ->join(['HL' => "HRIS_LOCATIONS"], "HE.LOCATION_ID = HL.LOCATION_ID");

            $select->where(["HE.EMPLOYEE_ID = '{$empID}' AND HL.LOCATION_CODE LIKE 'AFS%'"]);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();

            $result = $result->current();

            if(!empty($result)){
                $loc_code = $result['LOCATION_CODE']; 
            }
        }
        return $loc_code;
    }


    /**
    * Find if the employee works on the Fuel Deposit
    *
    * @method isFuelDepositWorker
    * @param empID
    * @return is_FDW
    */
    public function isFuelDepositWorker($empID) {
        $is_FDW = FALSE;
        $result = [];
        if(isset($empID) && !empty($empID)) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns([
                new Expression("HE.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HE.LOCATION_ID AS LOCATION_ID"),                
                new Expression("HL.LOCATION_ID AS LOCATION_ID"),
                new Expression("HL.LOCATION_CODE AS LOCATION_CODE")
            ], true);

            $select->from(['HE' => "HRIS_EMPLOYEES"])
            ->join(['HL' => "HRIS_LOCATIONS"], "HE.LOCATION_ID = HL.LOCATION_ID");

            $select->where(["HE.EMPLOYEE_ID = '{$empID}' AND HL.LOCATION_CODE LIKE 'FDO%'"]);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();

            $result = $result->current();

            if(!empty($result)){
                $is_FDW = TRUE;
            }
        }
        return $is_FDW;
    }

    /**
    * Check whether the employee is on leave or not
    *
    * @method isOnLeave
    * @param empID, overtimeDate
    * @return 
    */
    public function isOnLeave($empID, $overtimeDate){
        $is_onleave = 0;
        if(isset($empID) && isset($overtimeDate)){
            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns([
                new Expression("HELA.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HELR.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HELR.START_DATE AS START_DATE"),
                new Expression("HELR.END_DATE AS END_DATE"),
                new Expression("HELR.STATUS AS STATUS")
            ], true);

            $select->from(['HELA' => "HRIS_EMPLOYEE_LEAVE_ASSIGN"])
            ->join(['HELR' => "HRIS_EMPLOYEE_LEAVE_REQUEST"], "HELR.EMPLOYEE_ID = HELA.EMPLOYEE_ID AND HELR.LEAVE_ID = HELA.LEAVE_ID");

            $select->where(["HELA.EMPLOYEE_ID = '{$empID}' AND HELR.STATUS = 'AP' AND TO_DATE('{$overtimeDate}', 'YYYY-MM-DD') BETWEEN HELR.START_DATE AND HELR.END_DATE"]);

            $statement = $sql->prepareStatementForSqlObject($select);

            $result = $statement->execute();
            $result = $result->current();

            if(!empty($result)) {
                $is_onleave = 1;
            }
        }
        return $is_onleave;
    }

    /**
    * @method isHalfDay
    * @param empID, overtimeDate
    * @return
    * Not Used Yet
    */
    public function isHalfDay($empID, $overtimeDate) {

    }

    /**
    * Find the overtime date is Day Off or Not
    * 
    * @method DayOff
    * @param empID, overtimeDate
    * @return is_dayoff
    * Note: This method is written to check employee whether it is in day off or not while requesting overtime
    */
    public function isDayOff($empID, $overtimeDate) {
        $finalResult = [];
        $is_dayoff = 0;

        if(isset($empID) && isset($overtimeDate) && !empty($empID) && !empty($overtimeDate)) {
          $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->from(['HS' => "HRIS_ATTENDANCE_DETAIL"]);
            $select->where(["EMPLOYEE_ID = {$empID} AND OVERALL_STATUS = 'DO' AND ATTENDANCE_DT = TO_DATE('{$overtimeDate}', 'YYYY-MM-DD')"]);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $finalResult = $result->current();
            if(!empty($finalResult)) {
                $is_dayoff = 1;
            }
        }
        return $is_dayoff;
    }

    /**
    * Method to find if the employee is on Shift on the Overtime Date passed.
    *
    * @method isEmployeeShift
    * @param empID, overtimeDate
    * @return is_emp_shift
    * 
    */
    public function isEmployeeShift($empID, $overtimeDate) {
        $is_emp_shift = 0;
        if(isset($empID) && !empty($empID)) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns([
                new Expression("HS.SHIFT_ID AS SHIFT_ID"),
                new Expression("HS.START_DATE AS START_DATE"),
                new Expression("HS.END_DATE AS END_DATE"),
                new Expression("HS.WEEKDAY1 AS WEEKDAY1"),
                new Expression("HS.WEEKDAY2 AS WEEKDAY2"),
                new Expression("HS.WEEKDAY3 AS WEEKDAY3"),
                new Expression("HS.WEEKDAY4 AS WEEKDAY4"),
                new Expression("HS.WEEKDAY5 AS WEEKDAY5"),
                new Expression("HS.WEEKDAY6 AS WEEKDAY6"),
                new Expression("HS.WEEKDAY7 AS WEEKDAY7"),
                new Expression("HS.STATUS AS STATUS"),
                new Expression("HESA.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HESA.SHIFT_ID AS SHIFT_ID"),
                new Expression("HESA.STATUS AS STATUS"),
                new Expression("HESA.START_DATE AS START_DATE"),
                new Expression("HESA.END_DATE AS END_DATE")
            ], true);

            $select->from(['HS' => "HRIS_SHIFTS"])

            ->join(['HESA' => "HRIS_EMPLOYEE_SHIFT_ASSIGN"], "HS.SHIFT_ID = HESA.SHIFT_ID");

            $select->where(["HESA.EMPLOYEE_ID = '{$empID}' AND HS.STATUS = 'E' AND TO_DATE('{$overtimeDate}', 'YYYY-MM-DD') BETWEEN HESA.START_DATE AND HESA.END_DATE"]);

            $statement = $sql->prepareStatementForSqlObject($select);

            $result = $statement->execute();
            $result = $result->current();

            if(!empty($result)){
                $is_emp_shift = 1;
            }

        }
        return $is_emp_shift;
    }

    /**
    * Method to find if the employee is on Day off or half day
    *
    * @method isDayOffHalfDay
    * @param empID, overtimeDate
    * @return is_ondayoff
    * Note: This method is written to use in validation when requesting overtime.
    * Not Used Yet
    */
    public function isDayOffHalfDay($empID, $overtimeDate) {
        $resultArr = [];
        if(isset($empID) && !empty($empID)) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns([
                new Expression("HS.SHIFT_ID AS SHIFT_ID"),
                new Expression("HS.START_DATE AS START_DATE"),
                new Expression("HS.END_DATE AS END_DATE"),
                new Expression("HS.WEEKDAY1 AS WEEKDAY1"),
                new Expression("HS.WEEKDAY2 AS WEEKDAY2"),
                new Expression("HS.WEEKDAY3 AS WEEKDAY3"),
                new Expression("HS.WEEKDAY4 AS WEEKDAY4"),
                new Expression("HS.WEEKDAY5 AS WEEKDAY5"),
                new Expression("HS.WEEKDAY6 AS WEEKDAY6"),
                new Expression("HS.WEEKDAY7 AS WEEKDAY7"),
                new Expression("HS.STATUS AS STATUS"),
                new Expression("HESA.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HESA.SHIFT_ID AS SHIFT_ID"),
                new Expression("HESA.STATUS AS STATUS"),
                new Expression("HESA.START_DATE AS START_DATE"),
                new Expression("HESA.END_DATE AS END_DATE")
            ], true);

            $select->from(['HS' => "HRIS_SHIFTS"])

            ->join(['HESA' => "HRIS_EMPLOYEE_SHIFT_ASSIGN"], "HS.SHIFT_ID = HESA.SHIFT_ID");

            $select->where(["HESA.EMPLOYEE_ID = '{$empID}' AND HS.STATUS = 'E' AND TO_DATE('{$overtimeDate}', 'YYYY-MM-DD') BETWEEN HESA.START_DATE AND HESA.END_DATE"]);

            $statement = $sql->prepareStatementForSqlObject($select);

            $result = $statement->execute();
            $result = $result->current();

            if(!empty($result)) {
                $resultArr['WEEKDAY1'] = $result['WEEKDAY1'];
                $resultArr['WEEKDAY2'] = $result['WEEKDAY2'];
                $resultArr['WEEKDAY3'] = $result['WEEKDAY3'];
                $resultArr['WEEKDAY4'] = $result['WEEKDAY4'];
                $resultArr['WEEKDAY5'] = $result['WEEKDAY5'];
                $resultArr['WEEKDAY6'] = $result['WEEKDAY6'];
                $resultArr['WEEKDAY7'] = $result['WEEKDAY7'];
            }

        }
        return $resultArr;
    }

    /**
    * Calculate totalMonthdays
    * 
    * @method calcTotalMonDays
    * @param fyID, monthNo
    * @return totalMonDays
    *
    */
    public function calcTotalMonDays($fyID, $monthNo) {
        $totalMonDays = 30;

        if(isset($fyID) && isset($monthNo) && !empty($fyID) && !empty($monthNo)){
            $sql = "SELECT MONTH_EDESC, FROM_DATE, TO_DATE, MONTH_NO, DAYS_BETWEEN(FROM_DATE, TO_DATE) AS TOTAL_DAYS from HRIS_MONTH_CODE where FISCAL_YEAR_ID = {$fyID} AND MONTH_NO = {$monthNo}";

            $statement = $this->adapter->query($sql);
            $result = $statement->execute();
            $finalResult = $result->current();

            if(!empty($finalResult)) {
                $totalMonDays = (int) $finalResult['TOTAL_DAYS'] + 1;
            }
        }
        return $totalMonDays;
    }


    /**
    * Calculate Salary and Grade of the Employee
    *
    * @method CalculateSalaryGrade
    * @param empID, totalDaysCnt, fyID
    * @return totSalGd
    */
    public function CalculateSalaryGrade($empID, $totalDaysCnt, $fyID) {
        $totSalGd = 0;
        if(isset($empID)) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns([
                new Expression("HE.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HE.STATUS AS STATUS"),
                new Expression("HFVS.FLAT_ID AS FLAT_ID"),
                new Expression("HFVS.FLAT_CODE AS FLAT_CODE"),
                new Expression("HFVS.FLAT_EDESC AS FLAT_EDESC"),
                new Expression("HFVS.STATUS AS HFVSTATUS"),
                new Expression("HFVS.ASSIGN_TYPE AS ASSIGN_TYPE"),
                new Expression("HFVD.FLAT_ID AS FLAT_ID"),
                new Expression("HFVD.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HFVD.FLAT_VALUE AS FLAT_VALUE"),
                new Expression("HFVD.FISCAL_YEAR_ID AS FISCAL_YEAR_ID")
            ], true);

            $select->from(['HE' => "HRIS_EMPLOYEES"])
            ->join(['HFVD' => "HRIS_FLAT_VALUE_DETAIL"], "HE.EMPLOYEE_ID = HFVD.EMPLOYEE_ID")
            ->join(['HFVS' => "HRIS_FLAT_VALUE_SETUP"], "HFVD.FLAT_ID = HFVS.FLAT_ID");

            $select->where(["HE.EMPLOYEE_ID = {$empID} AND HE.STATUS = 'E' AND HFVS.ASSIGN_TYPE = 'E' AND HFVS.STATUS = 'E' AND HFVD.FISCAL_YEAR_ID = {$fyID}"]);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $resultArr = Helper::extractDbData($result);

            if(!empty($resultArr)) {
                foreach($resultArr as $resultSingle) {
                    if($resultSingle['FLAT_CODE'] == trim('SAL')){
                        $res_salary = $resultSingle['FLAT_VALUE'];
                    }

                    if($resultSingle['FLAT_CODE'] == trim('GRD')){
                        $res_grade = $resultSingle['FLAT_VALUE'];
                    }
                }
                $totSalGd = $res_salary + $res_grade;
                $totSalGd = $totSalGd/ (int) $totalDaysCnt;
            }
        }
        return $totSalGd;
    }

    /**
    * Calculate Overtime multiple and Leave multiple of an employee
    *
    * @method CalculateDashainTiharOMLM
    * @param empID, overtimeDate, fyID
    * @return totSalGd
    */
    public function CalculateDashainTiharOMLM($empID, $overtimeDate, $fyID) {
        $resultArr = [];
        if(isset($empID)) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();

             $select->columns([
                new Expression("HE.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HE.STATUS AS STATUS"),
                new Expression("HEH.EMPLOYEE_ID AS EMPLOYEE_ID"),
                new Expression("HEH.HOLIDAY_ID AS HOLIDAY_ID"),
                new Expression("HHMS.HOLIDAY_ID AS HOLIDAY_ID"),
                new Expression("HHMS.START_DATE AS START_DATE"),
                new Expression("HHMS.END_DATE AS END_DATE"),
                new Expression("HHMS.FISCAL_YEAR AS FISCAL_YEAR"),
                new Expression("TO_NUMBER(HHMS.OVERTIME_MULTIPLE) AS OVERTIME_MULTIPLE"),
                new Expression("TO_NUMBER(HHMS.LEAVE_MULTIPLE) AS LEAVE_MULTIPLE")
            ], true);

            $select->from(['HE' => "HRIS_EMPLOYEES"])
            ->join(['HEH' => "HRIS_EMPLOYEE_HOLIDAY"], "HE.EMPLOYEE_ID = HEH.EMPLOYEE_ID")
            ->join(['HHMS' => "HRIS_HOLIDAY_MASTER_SETUP"], "HEH.HOLIDAY_ID = HHMS.HOLIDAY_ID");

            $select->where(["HE.EMPLOYEE_ID = {$empID} AND HE.STATUS = 'E' AND HHMS.STATUS = 'E' AND HHMS.FISCAL_YEAR = {$fyID} AND TO_DATE('{$overtimeDate}', 'YYYY-MM-DD') BETWEEN  HHMS.START_DATE AND HHMS.END_DATE AND HHMS.OVERTIME_MULTIPLE<>1 AND HHMS.LEAVE_MULTIPLE<>1"]);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $resultArr = Helper::extractDbData($result);
        }
        return $resultArr;
    }

    /* DELETE OVERTIME DATA FOR TEST PURPOSE ONLY */
    public function delOTDetailData() {
         $sql = "TRUNCATE table HRIS_OT_DETAIL";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return 1;
    }

    public function delOTData(){
         $sql = "DELETE FROM HRIS_OT WHERE OT_ID IN (SELECT OT_ID FROM HRIS_OT)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return 1;
    }

    public function getAllOvertimeDetail($data){
        $monthId = $data['monthId'];
        $empId = $data['empId'];

        $sql = "select
        *,
        case when DAY_CODE <> 'H' OR OT_HOUR_POINT < 4.5 
   then 'disabled' 
   else null 
   end as CHECKBOX_STATUS,
        round(TOTAL_HOUR_POINT,
        2) as TOTAL_HOUR,
        round(OT_HOUR_POINT,
        2) as OT_HOUR,
        case when (OT_HOUR_POINT < 6 and DAY_CODE = 'H') then
        0.5 when (OT_HOUR_POINT >= 6 and DAY_CODE = 'H') then
        1 else
        0 end as LEAVE_REWARD,
CASE WHEN  (ELIGIBLE_LOCKING = 'Y' AND OUT_TIME > '20:10:00') THEN
        300 ELSE
        0 END AS LOCKING_ALLOWANCE	
   from ( select
        E.Employee_code,
        case when E.employee_id in 
        (select employee_id from hris_flat_value_detail
where flat_id in
(select flat_id from hris_flat_value_setup where flat_code='ELA')
and flat_value = 1) then 'Y' else 'N' END as ELIGIBLE_LOCKING,
        E.full_name,
        hd.designation_title,
        hl.location_edesc,
        hf.functional_level_edesc,
        had.attendance_dt,
        hs.shift_id,
        case when (had.overall_status = 'WH' 
           or had.overall_status = 'WD') 
       then 'H' when (WEEKDAY (TO_DATE (had.attendance_dt,
        'YYYY-MM-DD'))) = 4 
       then 'F' 
       else 'R' 
       end as day_code,
       case when HHM.HOLIDAY_ENAME is null then
       TO_CHAR(had.attendance_dt,'DY')
       ELSE
       TO_CHAR(had.attendance_dt,'DY') || ' (' 
       || HHM.HOLIDAY_ENAME || ')' END as DAY_DETAIL,
        BS_DATE(had.attendance_dt) as ATTENDANCE_DATE_BS,
        to_char(had.in_time,
        'HH24:MI:SS') as IN_TIME,
        to_char(had.out_time,
        'HH24:MI:SS') as out_time,
        seconds_between(had.in_time,
        had.out_time)/3600 as TOTAL_HOUR_POINT,
        case when (had.overall_status = 'WH' 
           or had.overall_status = 'WD') 
       then (seconds_between(had.in_time,
        had.out_time)/60)/60 
		 when (((WEEKDAY (TO_DATE (had.attendance_dt,
        'YYYY-MM-DD'))) = 4 ) and {$monthId}=83)
       then (seconds_between(had.in_time,
        had.out_time)/60 - hs.total_working_hr)/60 
        when (((WEEKDAY (TO_DATE (had.attendance_dt,
        'YYYY-MM-DD'))) = 4 ) and {$monthId}=83)
       then (seconds_between(had.in_time,
        had.out_time)/60 - hs.total_working_hr)/60
		when (WEEKDAY (TO_DATE (had.attendance_dt,
        'YYYY-MM-DD'))) = 4 
       then (seconds_between(had.in_time,
        had.out_time)/60 - 300)/60 
       else (seconds_between(had.in_time,
        had.out_time)/60 - hs.total_working_hr)/60 
       end as OT_HOUR_POINT,
       HRIS_GET_NIGHT_ALLOWANCE(to_char(had.out_time, 'HH:MI:SS AM'), E.employee_id) as NIGHT_ALLOWANCE,
       case when (HHM.holiday_code in ('LP',
			'GP',
			'SAP',
			'DH1',
			'DH2',
            'TH1','TH2')) then 
            case when E.employee_id in (select employee_id from hris_employees where location_id = 18) then
            2
            else 
            1
            end 
			when (HHM.holiday_code in ('BT',
			'NAW',
			'AST',
			'DAS')) then 
            case when E.employee_id in (select employee_id from hris_employees where location_id = 18) then
            3
            else 
            2
            end
		else 0
		END as bonus_multi,
        case when E.employee_id in (select employee_id from hris_employees where location_id = 18) then
            'Y'
            else 
            'N'
            end as manual_zero
       from hris_attendance_detail had 
       left join hris_employees E on (E.employee_id=had.employee_id) 
       left join hris_designations hd on (hd.designation_id=E.designation_id) 
       left join hris_locations hl on (hl.location_id=E.location_id) 
       left join hris_functional_levels hf on (hf.functional_level_id=E.functional_level_id) 
       left join hris_shifts hs on (hs.shift_id = had.shift_id) 
       left join hris_holiday_master_setup HHM on (HHM.holiday_id = had.holiday_id)
       where had.attendance_dt between (select
        from_date 
           from hris_month_code 
           where month_id = {$monthId}) 
       and (select
        to_date 
           from hris_month_code 
           where month_id = {$monthId}) 
       and E.employee_id ={$empId} 
       order by E.full_name,
        had.attendance_dt asc ) 
   where TOTAL_HOUR_POINT is not null 
   and (OT_HOUR_POINT > 2 OR OUT_TIME > '20:15:00')
   and attendance_dt not in (select attendance_dt from HRIS_EMPLOYEE_OVERTIME_CLAIM_DETAIL where overtime_claim_id in 
   (select overtime_claim_id from HRIS_EMPLOYEE_OVERTIME_CLAIM_REQUEST where employee_id = {$empId}) and status not in ('R','C'))
   order by attendance_dt";
//    echo('<pre>');print_r($sql);die;
        return $this->rawQuery($sql);
    }

    public function getHolidayDetails($monthId){
        $sql = "select start_date, holiday_code from hris_holiday_master_setup
        where start_date between 
        (select start_date from hris_fiscal_years
        where fiscal_year_id = (select fiscal_year_id from hris_month_code where month_id = $monthId)) and 
        (select end_date from hris_fiscal_years
        where fiscal_year_id = (select fiscal_year_id from hris_month_code where month_id = $monthId))
        and holiday_code is not null and status='E'";
        return $this->rawQuery($sql);
    }

    /* DELETE OVERTIME DATA FOR TEST PURPOSE ENDS */

}
