<?php
namespace SelfService\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\TravelRequest;
use SelfService\Model\TransferSettlement;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression; 
use Zend\Db\Sql\Sql;
use Application\Helper\Helper;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;

class TransferSettlementRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;
    protected $adapter;
 
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TransferSettlement::TABLE_NAME, $adapter);
    } 

    public function add(Model $model) {
        $addData=$model->getArrayCopyForDB();
        $this->tableGateway->insert($addData);
        $this->linkTransferWithFiles($addData['JOB_HISTORY_ID'],$addData['SERIAL_NUMBER']);
    }

    public function edit(Model $model, $id) {
        // echo("<pre>");print_r($model);die;
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [TravelRequest::TRAVEL_ID => $id]);
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

    public function getFilteredRecords(array $search) {
        
       
        // print_r($search);die;
        $condition = " ";
        if($search['startDate']){
            $condition = $condition. " and JH.Start_DATE = {$search['startDate']}";
        }

        if($search['endDate']){
            $condition = $condition. " and JH.END_DATE = {$search['endDate']}";
        }

        if($search['eventDate']){
            $condition = $condition. " and JH.EVENT_DATE = {$search['eventDate']}";
        }

        $sql= " select * from (
            SELECT
                JH.JOB_HISTORY_ID AS JOB_HISTORY_ID,
                INITCAP(TO_CHAR(JH.START_DATE,
                'DD-MON-YYYY')) AS START_DATE_AD,
                BS_DATE(JH.START_DATE) AS START_DATE_BS,
                INITCAP(TO_CHAR(JH.END_DATE,
                'DD-MON-YYYY')) AS END_DATE_AD,
                BS_DATE(JH.END_DATE) AS END_DATE_BS,
                INITCAP(TO_CHAR(JH.EVENT_DATE,
                'DD-MON-YYYY')) AS EVENT_DATE_AD,
                BS_DATE(JH.EVENT_DATE) AS EVENT_DATE_BS,
                hris_check_allow_add_transfer_settlement(JH.JOB_HISTORY_ID) as allow_add,
                (select
                max(serial_number) 
               from HRIS_TRANSFER_SETTLEMENT 
               where job_history_id = JH.job_history_id 
               and status not in ('C',
                'R')) as serial_number,
                INITCAP(E.FULL_NAME) AS FULL_NAME,
                INITCAP(B.BRANCH_NAME) AS TO_BRANCH,
                INITCAP(DP.DEPARTMENT_NAME) AS TO_DEPARTMENT,
                DS.DESIGNATION_TITLE AS TO_DESIGNATION,
                INITCAP(P.POSITION_NAME) AS TO_POSITION,
                INITCAP(L.LOCATION_EDESC) AS TO_LOCATION  
           FROM HRIS_JOB_HISTORY JH 
           LEFT JOIN HRIS_EMPLOYEES E ON E.EMPLOYEE_ID=JH.EMPLOYEE_ID 
           LEFT JOIN HRIS_BRANCHES B ON B.BRANCH_ID=JH.TO_BRANCH_ID 
           LEFT JOIN HRIS_DEPARTMENTS DP ON DP.DEPARTMENT_ID=JH.TO_DEPARTMENT_ID 
           LEFT JOIN HRIS_DESIGNATIONS DS ON DS.DESIGNATION_ID=JH.TO_DESIGNATION_ID 
           LEFT JOIN HRIS_POSITIONS P ON P.POSITION_ID=JH.TO_POSITION_ID
           LEFT JOIN HRIS_LOCATIONS L ON L.LOCATION_ID = JH.TO_LOCATION_ID 
           WHERE E.EMPLOYEE_ID = {$search['employeeId']} 
           AND JH.STATUS = 'E' 
           
            AND DAYS_BETWEEN(JH.EVENT_DATE,
	        CURRENT_DATE) >= 0   {$condition}
           ORDER BY JH.START_DATE DESC ) A where A.allow_add = 'Y'";
        //    AND DAYS_BETWEEN(JH.EVENT_DATE,
        //        CURRENT_DATE) <= 30 
            // echo('<pre>');print_r($sql);die;
        // print_r($sql);die;

        return $this->rawQuery($sql);
        

    }



    public function getFilteredRecordsExpense(array $search) {
        $condition = "";
        $boundedParameter = [];
        if (isset($search['startDate']) && $search['startDate'] != null) {
          $boundedParameter['startDate'] = $search['startDate'];
            $condition .= " AND hjh.START_DATE=TO_DATE('{$search['startDate']}','DD-MON-YYYY') ";
        }
        if (isset($search['eventDate']) && $search['eventDate'] != null) {
          $boundedParameter['eventDate'] = $search['eventDate'];
            $condition .= " AND hjh.EVENT_DATE=TO_DATE('{$search['eventDate']}','DD-MON-YYYY') ";
        }

        if (isset($search['statusId']) && $search['statusId'] != null && $search['statusId'] != -1) {
            if (gettype($search['statusId']) === 'array') {
                $csv = "";
                for ($i = 0; $i < sizeof($search['statusId']); $i++) {
                    if ($i == 0) {
                        $boundedParameter["statusId".$i] = $search['statusId'][$i];
                        $csv = "?".$i;
                    } else {
                        $csv .= ",:statusId".$i; 
                        $boundedParameter["statusId".$i] = $search['statusId'][$i];
                    }
                }
                $condition .= "AND hts.STATUS IN ({$csv})";
            } else {
                $condition .= "AND hts.STATUS IN ('{$search['statusId']}')";
                $boundedParameter['status'] = $search['statusId'];
            }
        }
        // print_r($condition);die;
        $employeeId = $search['employeeId'];
        $sql ="select
        hjh.job_history_id,
        hjh.start_date,
        hts.serial_number,
        BS_DATE(hjh.start_date) as start_date_bs,
        hjh.end_date,
        BS_DATE(hjh.end_date) as END_DATE_BS,
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
        where hts.status is not null and E.employee_id = {$employeeId} {$condition}
        ";
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
    }

    public function getClassIdFromEmpId($id){
        $sql1 = "select TRAVEL_ALLOWANCE_CLASS from hris_employees 
        where employee_id = {$id}";

        $sql2 = "select pcm.class_id from hris_position_class_map pcm
        left join hris_employees he on (pcm.position_id = he.functional_level_id)
        where he.employee_id = {$id}";
        if($this->rawQuery($sql1)[0]["TRAVEL_ALLOWANCE_CLASS"]){
            return $this->rawQuery($sql1)[0]["TRAVEL_ALLOWANCE_CLASS"];
        }else{
            return $this->rawQuery($sql2)[0]["CLASS_ID"];
        }
        
    }

    public function getCongifId($classId){
        $sql = "select config_id from HRIS_TRANSFER_SETTLEMENT_CONFIG where class_id = {$classId} and status ='E'";  
        return $this->rawQuery($sql)[0]["CONFIG_ID"];
    }

    public function getConfigDetails($configId){
        $sql = "select * from HRIS_TRANSFER_SETTLEMENT_CONFIG where config_id = {$configId} and status = 'E'";
        return $this->rawQuery($sql)[0];
    }

    public function linkTransferWithFiles($id, $serialNumber)
    {
        if (!empty($_POST['fileUploadList'])) {
            
                $filesList = $_POST['fileUploadList'];
                $filesList = implode(',', $filesList);
                $sql = "UPDATE hris_transfer_settlement_files SET JOB_HISTORY_ID = $id,
                SERIAL_NUMBER = $serialNumber
                        WHERE FILE_ID IN($filesList)";
                $statement = $this->adapter->query($sql);
                $statement->execute();
            
        }
    }

    public function pullFilebyId($id, $serialNumber)
    {

        $boundedParams = [];
        $sql = "select FILE_IN_DIR_NAME, File_Name from hris_transfer_settlement_files where JOB_HISTORY_ID = {$id} and status = 'E' and serial_number = {$serialNumber}";
        return $this->rawQuery($sql);
    }

    public function getTotalNoOfAttachment($id, $serialNumber){
        $sql = "select count(*) from HRIS_TRANSFER_SETTLEMENT_FILES where JOB_HISTORY_ID = {$id} and serial_number = {$serialNumber}";
        return $this->rawQuery($sql);
    }

    public function getTransferDetails($id){
        $sql = "select JH.*, hris_check_availability_of_yearly_settlement(JH.job_history_id) as ELIGIBLE_FOR_SETTLEMENT_AMT,
        B.Branch_name as TO_BRANCH_NAME,
        C.company_name,
        TL.location_EDESC as to_location_name,
	    L.LOCATION_EDESC from hris_job_history JH 
        left join hris_branches B on (B.branch_id = JH.to_branch_id) 
        left join hris_employees E on (E.employee_id = JH.employee_id) 
        left join hris_company C on (C.company_id = E.company_id)
        left join hris_locations L on (L.location_id = E.location_id)
        left join hris_locations TL on (TL.location_id = JH.to_location_id)
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
        //  print_r($sql);die;
        return $this->rawQuery($sql);
    }

    
    public function cancelTransferSettlement($jobHistoryId,$serialNumber) {
        $sql = "UPDATE HRIS_TRANSFER_SETTLEMENT SET STATUS = 'C' WHERE JOB_HISTORY_ID={$jobHistoryId} AND SERIAL_NUMBER = {$serialNumber}";
        return $this->rawQuery($sql);
    }

    public function delete($id){
        
    }

    public function getSerialNumber($id){
        $sql = "select ifnull(max(serial_number),0)+1 as SN from HRIS_TRANSFER_SETTLEMENT where job_history_id = {$id}";
        return $this->rawQuery($sql)[0]['SN'];
    }

    public function deletePreviousRecordsForEdit($jobHistoryId,$serialNumber){
        $sql = "delete from HRIS_TRANSFER_SETTLEMENT where job_history_id = {$jobHistoryId} and 
        serial_number = {$serialNumber}";
        return $this->rawQuery($sql);
    }

    public function deletePreviousFilesRecordsForEdit($jobHistoryId,$serialNumber){
        $sql = "delete from HRIS_TRANSFER_SETTLEMENT_FILES where job_history_id = {$jobHistoryId} and 
        serial_number = {$serialNumber}";
        return $this->rawQuery($sql);
    }

    public function checkAllowSelf($id){
        $sql = "select case when (ifnull(count(*),0)>=1) then 'N' else 'Y' end as allow_self from 
        HRIS_TRANSFER_SETTLEMENT where job_history_id = {$id} and for_family = 'N' and status not in ('C',
            'R')";
        return $this->rawQuery($sql)[0]['ALLOW_SELF'];
    }

    public function getRowNumber($id){
        $sql="select count(*)+1 as rowNUm from HRIS_TRANSFER_SETTLEMENT where job_history_id = {$id} and status not in ('R','C')";
        return $this->rawQuery($sql)[0]['ROWNUM'];
    }

}
