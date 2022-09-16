<?php

namespace DocumentRegistration\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use DocumentRegistration\Model\IncommingDocument;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql; 

class IncomingRepo extends HrisRepository implements RepositoryInterface
{

    protected $tableGateway;
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(IncommingDocument::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    //----------- Fetching data into select field ----------------------------
    // public function fetchSenderOrgs(){
    //     $sql = "select distinct sender_org from DC_REGISTRATION";
    //     return $this->rawQuery($sql);
    // }
    // public function fetchReceivingDept(){
    //     $sql = "select distinct hr.department_name from DC_REGISTRATION dc left join hris_departments hr on (dc.department_id = hr.department_id)";
    //     // $sql = "select distinct department_name from hris_department";
    //     return $this->rawQuery($sql);
    // }
    // public function fetchResponse(){
    //     $sql = "select distinct RESPONSE_FLAG from DC_REGISTRATION";
    //     return $this->rawQuery($sql);
    // }
    //---------------------------------------------------------------------------

    //--------------------------------- Fetching Data into table -----------------------------------------

    public function updateFile($id)
    {

        $sql = "Update dc_registration_docs set reg_draft_id = null where reg_draft_id =$id";
        $statement = $this->adapter->query($sql);
        $file_id = $statement->execute();
    }
    public function editProcessId($id){
        $sql =  "Update dc_registration_draft set process_id = (select process_id from dc_processes where process_start_flag = 'N' and process_end_flag = 'Y' and is_registration='Y' and status='E') where reg_draft_id=$id";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        
        $sql = "Update dc_user_assign set process_id = (select process_id from dc_processes where process_start_flag = 'N' and process_end_flag = 'Y' and is_registration='Y' and status='E') where reg_draft_id=$id";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        return null;
    }
    public function getRegTempCode(){
        $sql = "select max(reg_draft_id)as max from dc_registration_draft";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    public function add(Model $model)
    {
        // echo'<pre>';print_r($model->getArrayCopyForDB());die;
        $this->tableGateway->insert($model->getArrayCopyForDB());
        $this->linkDCRegistrationWithFiles();
    }
    public function checkId($reg_temp_code){
        $sql = "select * from dc_registration_draft where reg_temp_code = $reg_temp_code and status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [IncommingDocument::REG_DRAFT_ID => $id]);
        $this->linkDCRegistrationWithFiles($id);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(IncommingDocument::class, [IncommingDocument::BRANCH_NAME]), false);
            $select->where([IncommingDocument::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->order([IncommingDocument::BRANCH_WNAME => Select::ORDER_ASCENDING]);
        });
    }
    public function getCode($date, $locationId){
        // print_r ($date);die();
		$sql = "select reg_temp_code from dc_registration_draft where reg_draft_id  = (select max(reg_draft_id) from dc_registration_draft where location_id = {$locationId} and status='E')";
		$maxCode = $this->rawQuery($sql);

        $sql = "select location_code from hris_locations where location_id = {$locationId}";
        $locationCode = $this->rawQuery($sql)[0]['LOCATION_CODE'];

		$sql = "select fiscal_year_name from hris_fiscal_years where to_date('{$date}', 'DD-MON-YYYY') between start_date and end_date";
		$ficalYear = $this->rawQuery($sql)[0]['FISCAL_YEAR_NAME'];
		$code = '';
		if(empty($maxCode[0]['REG_TEMP_CODE'])){
			$code = '00001/'.$locationCode.'/'.$ficalYear;
		}
		else{
			$id = explode('/', $maxCode[0]['REG_TEMP_CODE']);
			$intid = intval($id[0])+1;
			$intid = sprintf('%05d', $intid);
			$code = $intid.'/'.$locationCode.'/'.$ficalYear;
		}
		return $code;
}

    public function fetchById($id)
    {

        // $sql = new Sql($this->adapter);
        // $select = $sql->select();
        // $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(IncommingDocument::class, NULL, [
        //     IncommingDocument::DRAFT_DATE,
        //     IncommingDocument::LETTER_REF_DATE,
        //     IncommingDocument::DOCUMENT_DATE,
        //         ]), false);


        // $select->from(['LA' => LeaveApply::TABLE_NAME])
        //     ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=LA.EMPLOYEE_ID", ['FIRST_NAME' => new Expression('INITCAP(E.FIRST_NAME)'), 'MIDDLE_NAME' => new Expression('INITCAP(E.MIDDLE_NAME)'), 'LAST_NAME' => new Expression('INITCAP(E.LAST_NAME)')], "left")
        //     ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=LA.RECOMMENDED_BY", ['FN1' => new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")], "left")
        //     ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LA.APPROVED_BY", ['FN2' => new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")], "left");

        // $select->where([
        //     IncommingDocument::REG_DRAFT_ID . $id
        // ]);

        // $statement = $sql->prepareStatementForSqlObject($select);
        // $result = $statement->execute();
        // return $result->current();


        return $this->tableGateway->select(function(Select $select) use($id) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new IncommingDocument(), [
                        'registrationDate', 'receivingLetterReferenceDate', 'documentDate', 'completionDate'
                    ]), false);
            $select->where([IncommingDocument::REG_DRAFT_ID => $id]);
        })->current();
    }
    public function getEmployeeList($departmentId){
        $sql = "select employee_id from dc_departments_users where department_id = $departmentId";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    public function getLocationWiseEmployeeList($id){
        $sql = "select employee_id from dc_departments_users where location_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    public function getReceivingDepartmentID($id){
        $sql = "select department_id from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['DEPARTMENT_ID'];
    }
    public function getAddProcessId(){
        $sql = "select process_id from dc_processes where process_start_flag = 'Y' and process_end_flag = 'N' and is_registration='Y' and status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['PROCESS_ID'];
    }
    public function getEndProcess(){
        $sql = "select process_edesc from dc_processes where process_start_flag = 'N' and process_end_flag = 'Y' and is_registration='Y' and status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['PROCESS_EDESC'];
    }
    public function getDocumentDate($id){
        $sql = "select document_date from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['DOCUMENT_DATE'];
    }
    public function getRegistrationDate($id){
        $sql = "select draft_date from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['DRAFT_DATE'];
    }
    public function getFromOfficeId($id){
        $sql = "select from_office_id from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['FROM_OFFICE_ID'];
    }
    public function getFromLocationId($id){
        $sql = "select from_location_id from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['FROM_LOCATION_ID'];
    }
    public function getLocationId($id){
        $sql = "select location_id from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['LOCATION_ID'];
    }
    public function getReceivingLetterReferenceDate($id){
        $sql = "select letter_ref_date from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['LETTER_REF_DATE'];
    }
    public function getResponseFlag($id){
        $sql = "select response_flag from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['RESPONSE_FLAG'];
    }
    public function getProcessId($id){
        $sql = "select process_id from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['PROCESS_ID'];
    }
    public function getFromOfficeName($id)
    {
        $sql = "select f.office_edesc from dc_offices f join dc_registration_draft dc on (f.office_id = dc.from_office_id) where dc.reg_draft_id =$id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['OFFICE_EDESC'];
    }
    public function getCompletionDate($id){
        $sql = "select completion_date from dc_registration_draft where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['COMPLETION_DATE'];
    }
    public function getReceiverName($id)
    {
        $sql = "select e.full_name from hris_employees e join dc_registration_draft dc on (e.employee_id = dc.receiver_name) where dc.reg_draft_id =$id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['FULL_NAME'];
    }

    public function getReceivingDepartment($id)
    {
        $sql = "select d.department_name from hris_departments d join dc_registration_draft dc on (d.department_id = dc.department_id) where dc.reg_draft_id =$id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['DEPARTMENT_NAME'];
    }

    public function delete($id)
    {
        // $this->tableGateway->update([IncommingDocument::STATUS => 'D'], [IncommingDocument::REG_DRAFT_ID => $id]);
        $sql = "call HRIS_DELETE_REGISTRATION_PROC($id)";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        
        $sql = "update dc_user_assign set status='D' where reg_draft_id = $id";
        $statement = $this->adapter->query($sql);
        $statement->execute();

    }

    public function pushFileLink($data, $userID)
    {
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $sql = "INSERT INTO DC_REGISTRATION_DOCS(FILE_ID,REG_DRAFT_ID,DOC_DATE, FILE_IN_DIR_NAME,USER_ID)
        VALUES((SELECT ifnull(max(file_ID), 0) + 1 FROM DC_REGISTRATION_DOCS),null, current_date, '$fileInDir','$userID')";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        $sql = "SELECT file_id, file_in_dir_name FROM DC_REGISTRATION_DOCS WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM DC_REGISTRATION_DOCS)";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($this->rawQuery($sql));
    }

    public function linkDCRegistrationWithFiles($id = null)
    {
        if (!empty($_POST['fileUploadList'])) {
            if ($id == null) {
                $filesList = $_POST['fileUploadList'];
                $filesList = implode(',', $filesList);
                $sql = "UPDATE DC_REGISTRATION_DOCS SET REG_DRAFT_ID = (SELECT MAX(REG_DRAFT_ID) FROM DC_REGISTRATION_DRAFT)
                        WHERE FILE_ID IN($filesList)";
                $statement = $this->adapter->query($sql);
                $statement->execute();
            } else {
                $filesList = $_POST['fileUploadList'];
                $filesList = implode(',', $filesList);
                $sql = "UPDATE DC_REGISTRATION_DOCS SET REG_DRAFT_ID = $id
                        WHERE FILE_ID IN($filesList)";
                $statement = $this->adapter->query($sql);
                $statement->execute();
            }
        }
    }

    public function pullFilebyId($id)
    {
        $boundedParams = [];

        $boundedParams['id'] = $id;
        $sql = "select FILE_IN_DIR_NAME, File_Name from DC_REGISTRATION_DOCS where REG_DRAFT_ID = ?";

        return $this->rawQuery($sql, $boundedParams);
    }

    public function getIncoming()
    {
        $sql = "select  DISTINCT dc.reg_temp_code,bs_date(dc.LETTER_REF_DATE) as LETTER_REF_MITI, dc.reg_draft_id,
        CASE WHEN (dc.from_other_office is null) then o.office_edesc else dc.from_other_office end as office_edesc,
       e.full_name,
       dc.letter_ref_no,dc.LETTER_REF_DATE, dc.COMPLETION_DATE,
       d.department_name,
       p.process_edesc,
       l.location_edesc,
       dc.response_flag,dc.DOCUMENT_DATE,
       dc.description
       from dc_registration_draft dc
       left join dc_offices o on (dc.from_office_id = o.office_id)
       left join hris_employees e on (dc.receiver_name = e.employee_id)
       left join hris_departments d on (dc.department_id = d.department_id)
       left join hris_locations l on (dc.location_id = l.location_id)
       left join dc_processes p on (dc.process_id = p.process_id) where dc.status='E'
       order by dc.reg_draft_id desc
       ";
       
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    //------------------------------------------------------------------------------------------


    //---------------------------------- Fetching Searched Data into table ---------------------------------------------------
    public function getIncomingbyId($data) {
      
        //================ Searching Empty field function ======================
        $registrationNumCondition = "";
        $senderOrgCondition = "";
        $letterReferenceNumCondition ="";
        $receivingDeptCondition = "";
        $toLocationCodeCondition="";
        //$receiverNameCondition = "";
        //$responseFlagCondition = "";
        $fromDateCondition = "";
        $toDateCondition = "";
        //$descCondition = "";
        if($data['registrationNum'] != null && $data['registrationNum'] != -1 && $data['registrationNum'] != " ") {
            $registrationNumCondition = " AND dc.REG_TEMP_CODE like '%{$data['registrationNum']}%'";
        } 

        if($data['senderOrg'] != null && $data['senderOrg'] != -1 && $data['senderOrg'] != " ") {
            $senderOrgCondition = " AND o.OFFICE_ID = '{$data['senderOrg']}' ";
        }

        if($data['letterReferenceNum'] != null && $data['letterReferenceNum'] != -1 && $data['letterReferenceNum'] != " ") {
            $letterReferenceNumCondition = " AND dc.LETTER_REF_NO like '%{$data['letterReferenceNum']}%' ";
        }
        if($data['receivingDept'] != null && $data['receivingDept'] != -1 && $data['receivingDept'] != " ") {
            $receivingDeptCondition = " AND dept.DEPARTMENT_ID = '{$data['receivingDept']}' ";
        }

        if($data['toLocationCode'] != null && $data['toLocationCode'] != -1 && $data['toLocationCode'] != " ") {
            $toLocationCodeCondition = " AND loc.LOCATION_ID  = '{$data['toLocationCode']}' ";
        }

        // if($data['receiverName'] != null && $data['receiverName'] != -1 && $data['receiverName'] != " ") {
        //     $receiverNameCondition = " AND EMP.FULL_NAME = '{$data['receiverName']}' ";
        // }
        // if($data['responseFlag'] != null && $data['responseFlag'] != -1 && $data['responseFlag'] != " ") {
        //     $responseFlagCondition = " AND DC.RESPONSE_FLAG = '{$data['responseFlag']}' ";
        // }
        if($data['fromDate'] != null && $data['fromDate'] != -1 && $data['fromDate'] != " ") {
            $fromDateCondition = " AND dc.LETTER_REF_DATE >= TO_DATE('{$data['fromDate']}','DD-MON-YYYY') ";
        }
        if($data['toDate'] != null && $data['toDate'] != -1 && $data['toDate'] != " ") {
            $toDateCondition = " AND dc.LETTER_REF_DATE <= TO_DATE('{$data['toDate']}','DD-MON-YYYY') ";
        }
        
        // if($data['desc'] != null && $data['desc'] != -1 && $data['desc'] != " ") {
        //     $descCondition = " AND DC.DESCRIPTION = '{$data['desc']}' ";
        // }
        
        $sql = "select dc.reg_temp_code,
        dc.reg_draft_id, bs_date(dc.LETTER_REF_DATE) as LETTER_REF_MITI,
        dc.letter_ref_no,dc.LETTER_REF_DATE, dc.COMPLETION_DATE,
        CASE WHEN (dc.from_other_office is null) then o.office_edesc else dc.from_other_office end as office_edesc, 
        dept.department_name,
        loc.location_edesc,
        p.process_edesc,
        dc.response_flag,dc.DOCUMENT_DATE,
        dc.description 
        from dc_registration_draft dc 
        LEFT join dc_offices o on (dc.from_office_id = o.office_id) 
        LEFT join  hris_departments dept on (dc.department_id = dept.department_id) 
        LEFT join dc_processes p on (dc.process_id = p.process_id)
        left join hris_locations loc on (loc.location_id = dc.location_id)
        where 1=1 and DC.status ='E'
        {$registrationNumCondition} {$senderOrgCondition} {$letterReferenceNumCondition} 
        {$toLocationCodeCondition}
        {$receivingDeptCondition} {$fromDateCondition} {$toDateCondition}
        order by dc.reg_draft_id desc";
        
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        //print_r($sql);die;
        return Helper::extractDbData($result);
       
    }
    //---------------------------------------------------------------------------------------------------------------------
    public function getRequestbyId($id)
    {
        $sql = "select * from DC_REGISTRATION where reg_id = $id";
        return $this->rawQuery($sql);
    }

    public function getSampatiBibaranData($by){

        $boundedParameter=[];
        $condition = EntityHelper::getSearchConditonBounded($by['companyId'], $by['branchId'], $by['departmentId'], $by['positionId'], $by['designationId'], $by['serviceTypeId'], $by['serviceEventTypeId'], $by['employeeTypeId'], $by['employeeId'], $by['genderId'], $by['locationId'], $by['functionalTypeId']);
        $boundedParameter=array_merge($boundedParameter,$condition['parameter']);

        $sql = "select
        drd.*,
        e.full_name,
        hfy.fiscal_year_name 
        from dc_registration_draft drd 
        left join hris_employees e on (drd.employee_id = e.employee_id)
        left join hris_fiscal_years hfy on (drd.sb_fiscal_yr = hfy.fiscal_year_id)
        where sb_fiscal_yr is NOT NULL and drd.status='E' {$condition['sql']}
        ;";
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getSampatiBibaranDataSelf($employeeId){
        $sql = "select
        drd.*,
        he.full_name,
        hfy.fiscal_year_name 
        from dc_registration_draft drd 
        left join hris_employees he on (drd.employee_id = he.employee_id)
        left join hris_fiscal_years hfy on (drd.sb_fiscal_yr = hfy.fiscal_year_id)
        where sb_fiscal_yr is NOT NULL
        and drd.employee_id = {$employeeId} and drd.status='E'
        ;";
        return $this->rawQuery($sql);
    }

    public function getKaryaSampadanData($by){

        $boundedParameter=[];
        $condition = EntityHelper::getSearchConditonBounded($by['companyId'], $by['branchId'], $by['departmentId'], $by['positionId'], $by['designationId'], $by['serviceTypeId'], $by['serviceEventTypeId'], $by['employeeTypeId'], $by['employeeId'], $by['genderId'], $by['locationId'], $by['functionalTypeId']);
        $boundedParameter=array_merge($boundedParameter,$condition['parameter']);

        $sql = "select
        drd.*,
        e.full_name,
        hfy.fiscal_year_name 
        from dc_registration_draft drd 
        left join hris_employees e on (drd.emp_id = e.employee_id)
        left join hris_fiscal_years hfy on (drd.ks_fiscal_yr = hfy.fiscal_year_id)
        where ks_fiscal_yr is NOT NULL and drd.status='E' {$condition['sql']}
        ;";
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getKaryaSampadanDataSelf($employeeId){
        $sql = "select
        drd.*,
        he.full_name,
        hfy.fiscal_year_name 
        from dc_registration_draft drd 
        left join hris_employees he on (drd.emp_id = he.employee_id)
        left join hris_fiscal_years hfy on (drd.ks_fiscal_yr = hfy.fiscal_year_id)
        where ks_fiscal_yr is NOT NULL
        and drd.emp_id = {$employeeId} and drd.status='E'
        ;";
        return $this->rawQuery($sql);
    }

    
    
}
