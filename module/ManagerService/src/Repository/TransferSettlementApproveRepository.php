<?php
namespace ManagerService\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Exception;
use SelfService\Model\TransferSettlement;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\Helper;
use Application\Repository\HrisRepository;

class TransferSettlementApproveRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;
    protected $adapter;
 
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TransferSettlement::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function getAllWidStatus($id, $status) {
        
    }

    public function edit(Model $model, $id) {
        $temp = $model->getArrayCopyForDB();
        $this->tableGateway->update($temp, [TransferSettlement::TRANSFER_SETTLEMENT_ID => $id]);
    }

    public function fetchAll() {
        
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


    public function getPendingList() {
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
        where hts.status is not null and hts.status in ('RQ')
        ";

        return $this->rawQuery($sql);
    }

    public function getTotalNoOfAttachment($id, $serialNumber){
        $sql = "select count(*) from HRIS_TRANSFER_SETTLEMENT_FILES where JOB_HISTORY_ID = {$id} and serial_number = {$serialNumber}";
        return $this->rawQuery($sql);
    }

    public function linkTravelWithFiles($id = null)
    {   
        if($id){
        $sql="delete from hris_travel_files where travel_id = $id";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        }

        if (!empty($_POST['fileUploadList'])) {
            if ($id == null) {
                $filesList = $_POST['fileUploadList'];
                $filesList = implode(',', $filesList);
                $sql = "UPDATE hris_travel_files SET TRAVEL_ID = (SELECT reference_travel_id FROM HRIS_EMPLOYEE_TRAVEL_REQUEST where travel_id = (select max(travel_id) from HRIS_EMPLOYEE_TRAVEL_REQUEST))
                        WHERE FILE_ID IN($filesList)";
                $statement = $this->adapter->query($sql);
                $statement->execute();
            } else {
                $filesList = $_POST['fileUploadList'];
                $filesList = implode(',', $filesList);
                $sql = "UPDATE hris_travel_files SET TRAVEL_ID = $id
                        WHERE FILE_ID IN($filesList)";
                $statement = $this->adapter->query($sql);
                $statement->execute();
            }
        }
        $sql="delete from hris_travel_files where travel_id is null";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }

    public function getAllExpenseDetails($id,$serialNumber){
        $sql = "Select * from HRIS_TRANSFER_SETTLEMENT where job_history_id = {$id} and serial_number = {$serialNumber}";
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

    public function pullFilebyId($id, $serialNumber)
    {

        $boundedParams = [];
        $sql = "select FILE_IN_DIR_NAME, File_Name from hris_transfer_settlement_files where JOB_HISTORY_ID = {$id} and status = 'E' and serial_number = {$serialNumber}";
        // $boundedParams['id'] = $this->rawQuery("SELECT reference_travel_id FROM HRIS_EMPLOYEE_TRAVEL_REQUEST where travel_id = {$id}")[0]['REFERENCE_TRAVEL_ID'];
        return $this->rawQuery($sql);
    }

    public function pushFileLink($data, $userID)
    {
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $sql = "INSERT INTO HRIS_TRANSFER_SETTLEMENT_FILES(FILE_ID,JOB_HISTORY_ID, FILE_NAME,UPLOADED_DATE, FILE_IN_DIR_NAME, CREATED_DT, STATUS, CREATED_BY, SERIAL_NUMBER)
        VALUES((SELECT ifnull(max(file_ID), 0) + 1 FROM HRIS_TRANSFER_SETTLEMENT_FILES),null, '$fileName', current_date, '$fileInDir', current_date, 'E', $userID, null)";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        $sql = "SELECT * FROM HRIS_TRANSFER_SETTLEMENT_FILES WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM HRIS_TRANSFER_SETTLEMENT_FILES)";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($statement->execute());
        // $fileName = $data['fileName'];
        // $fileInDir = $data['filePath'];
        // $sql = "INSERT INTO DC_REGISTRATION_DOCS(FILE_ID,FILE_NAME,DOC_DATE, FILE_IN_DIR_NAME, REG_ID) VALUES((SELECT nvl(max(file_ID), 0) + 1 FROM DC_FILES), '$fileName', trunc(sysdate), '$fileInDir', null)";
        // $statement = $this->adapter->query($sql);
        // $statement->execute();
        // $sql = "SELECT * FROM DC_FILES WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM DC_FILES)";
        // $statement = $this->adapter->query($sql);
        // return Helper::extractDbData($statement->execute());
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

        if (isset($search['employees']) && $search['employees'] != null) {
              $condition .= " AND E.employee_id in ({$search['employees']}) ";
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
        // print_r($condition);die;


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
}
