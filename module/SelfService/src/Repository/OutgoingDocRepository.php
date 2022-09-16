<?php
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Helper\Helper;
use Application\Repository\RepositoryInterface;
use Application\Repository\HrisRepository;
use DartaChalani\Model\Chalani;
use Setup\Model\HrEmployees;
use DartaChalani\Model\UserAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Application\Helper\EntityHelper;


class OutgoingDocRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Chalani::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        if(!$array['FROM_LOCATION_ID']){
            $array['FROM_LOCATION_ID'] = null;
        }
        $this->tableGateway->update($array, [Chalani::DISPATCH_DRAFT_ID => $id]);
    }

    public function fetchData($eid){

        // $sql = "SELECT
        // dcd.*,bs_date(dcd.draft_date) as DRAFT_MITI,
        //     ua.*,
        //     de.department_name,
        //     p.process_edesc
        // FROM
        //     dc_user_assign ua
        //     LEFT JOIN dc_processes p ON ( p.process_id = ua.process_id )
        //         left join dc_dispatch_draft dcd on (dcd.dispatch_draft_id = ua.dispatch_draft_id)
        //     LEFT JOIN hris_departments de ON ( de.department_id = ua.deptartment_id )
        //     where ua.employee_id = $eid";

        $sql = "select distinct dcd.*,bs_date(dcd.draft_date) as DRAFT_MITI,
        o.office_edesc,
        d.department_name,
        p.process_edesc,
        dcd.response_flag,
        dcd.description
        from dc_dispatch_draft dcd
        left join dc_offices o on (dcd.to_office_id = o.office_id)
        left join hris_departments d on (dcd.from_department_code = d.department_id)
        left join dc_processes p on (dcd.process_id = p.process_id) 
        left join dc_user_assign ua on (ua.dispatch_draft_id = dcd.dispatch_draft_id)
        left join dc_departments_users du on (du.department_id = dcd.from_department_code)
        left join HRIS_EMPLOYEES e on (e.employee_id= ua.employee_id)
        where ua.employee_id = {$eid}  or dcd.created_by = {$eid}
        and dcd.status='E'
        order by dcd.dispatch_draft_id desc";

        return $this->rawQuery($sql);
        // $sql = "select d.*, DE.DEPARTMENT_NAME, p.process_edesc from dc_dispatch_draft d left join dc_processes p on (p.process_id = d.process_id)
        // left join hris_departments de on(de.department_id = D.FROM_DEPARTMENT_CODE)
        // ";
        // return $this->rawQuery($sql);
    }


    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Chalani::class, [Chalani::DISPATCH_TEMP_CODE]), false);
            $select->where([Chalani::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->order([Chalani::DISPATCH_DRAFT_ID => Select::ORDER_ASCENDING]);
        });
    }

    public function fetchByEmployee($employeeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression(" distinct DC.DISPATCH_TEMP_CODE AS DISPATCH_TEMP_CODE"),
            new Expression("DC.DESCRIPTION AS DESCRIPTION"),
            new Expression("DC.DRAFT_DATE AS DRAFT_DATE"),
            new Expression("DC.DOCUMENT_DATE AS DOCUMENT_DATE"),
            new Expression("DC.REMARKS AS REMARKS"),
            new Expression("DC.RESPONSE_FLAG AS RESPONSE_FLAG"),
            new Expression("DC.DISPATCH_DRAFT_ID AS DISPATCH_DRAFT_ID"),
            new Expression("case when DC.process_id = 6 then 'Y' else 'N' end as approved")
        ], true);
        $select->from(['DC' => Chalani::TABLE_NAME])
        ->join(['U' => UserAssign::TABLE_NAME], "DC.DISPATCH_DRAFT_ID=U.DISPATCH_DRAFT_ID", ['DISPATCH_DRAFT_ID'])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.EMPLOYEE_ID=U.EMPLOYEE_ID", ['EMPLOYEE_ID']);
        $select->where(["E.STATUS" => 'E', "DC.STATUS" => 'E', "U.EMPLOYEE_ID" => $employeeId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    public function getCode($date, $locationId){
            $sql = "select letter_number from dc_dispatch where dispatch_id  = (select max(dispatch_id) from dc_dispatch where from_location_id = {$locationId} and status='E')";
		    $maxCode = $this->rawQuery($sql);

            $sql = "select location_code from hris_locations where location_id = {$locationId}";
            $locationCode = $this->rawQuery($sql)[0]['LOCATION_CODE'];
        
		$sql = "select fiscal_year_name from hris_fiscal_years where to_date('{$date}', 'DD-Mon-YYYY') between start_date and end_date";
		$ficalYear = $this->rawQuery($sql)[0]['FISCAL_YEAR_NAME'];
		$code = '';
		if(empty($maxCode[0]['LETTER_NUMBER'])){
			$code = '00001/'.$locationCode.'/'.$ficalYear;
		}
		else{
			$id = explode('/', $maxCode[0]['LETTER_NUMBER']);
			$intid = intval($id[0])+1;
			$intid = sprintf('%05d', $intid);
			$code = $intid.'/'.$locationCode.'/'.$ficalYear;
		}
		
		return $code;
}
    public function fetchById($id) {

        
        return $this->tableGateway->select(function(Select $select) use($id) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new Chalani(), [
                    'draftDt'
                ]), false);
            $select->where([Chalani::DISPATCH_DRAFT_ID => $id]);})->current();
            
    }

    public function delete($id) {
        $this->tableGateway->update([Chalani::STATUS => 'D'], [Chalani::DISPATCH_DRAFT_ID => $id]);
        
    }
    public function getEmployeeList($departmentId){
        $sql = "select distinct employee_id from dc_departments_users where department_id = $departmentId";
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
    public function sendNotification($model){
        $eid = $model->employeeId;
        $sql = "insert into HRIS_NOTIFICATION(message_id, message_datetime, message_title, message_desc, 
        message_from, message_to, status, expiry_time, route) values((select max(message_id)+1 from HRIS_NOTIFICATION),
        current_date, 'Chalani Forwarded', 'A chalani has been forwarded to your department.', 1, $eid, 'U', TO_DATE('2028-07-30', 'YYYY-MM-DD'), '{\"route\":\"outgoingdoc\",\"action\":\"index\"}')";
        $this->rawQuery($sql);
    }
    public function getPastValues($id){
        $sql = "select dispatch_draft_id, dispatch_temp_code, draft_date, from_department_code, to_office_id,status,created_by, created_dt, response_flag, description, remarks, file_path from dc_dispatch_draft where dispatch_draft_id=$id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    public function getAddProcessId(){
        $sql = "select process_id from dc_processes where process_start_flag = 'Y' and process_end_flag = 'N' and is_registration='N' and status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['PROCESS_ID'];
    }
    public function getDispatchProcessId(){
        $sql = "select process_id from dc_processes where process_start_flag = 'N' and process_end_flag = 'Y' and is_registration='N' and status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['PROCESS_ID'];
    }
    public function acknowledgeProcessId($id, $process){
        $sql =  "Update dc_dispatch_draft set process_id = $process where dispatch_draft_id=$id";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        return null;
    }
    public function editProcessId($id){
        $sql =  "Update dc_dispatch_draft set process_id = 7 where dispatch_draft_id=$id";
        $statement = $this->adapter->query($sql);
        $statement->execute();
        
        // $sql = "Update dc_user_assign set process_id = 7 where dispatch_draft_id=$id";
        // $statement = $this->adapter->query($sql);
        // $statement->execute();
        return null;
    }

    public function forward(Model $model) {
        $tableGateway = new TableGateway(UserAssign::TABLE_NAME, $this->adapter);
        $tableGateway->insert($model->getArrayCopyForDB());
    }

    public function getSearchResults($data) {
        $deptCondition = "";

        $responseFlagCondition = "";

        $descCondition = "";
        $processCondition = "";

        if($data['departmentId'] != null && $data['departmentId'] != -1 && $data['departmentId'] != " ") {
            $deptCondition = " AND d.FROM_DEPARTMENT_CODE = '{$data['departmentId']}' ";
        }
        if($data['processId'] != null && $data['processId'] != -1 && $data['processId'] != " ") {
            $processCondition = " AND d.PROCESS_ID = '{$data['processId']}' ";
        }
        if($data['responseFlag'] != null && $data['responseFlag'] != -1 && $data['responseFlag'] != " ") {
            $responseFlagCondition = " AND d.RESPONSE_FLAG = '{$data['responseFlag']}' ";
        }

        if($data['description'] != null && $data['description'] != -1 && $data['description'] != " ") {
            $descCondition = " AND d.DESCRIPTION like '%{$data['description']}%' ";
        }
        
        $sql = "select d.*,bs_date(d.draft_date) as DRAFT_MITI, DE.DEPARTMENT_NAME, p.process_edesc from dc_dispatch_draft d left join dc_processes p on (p.process_id = d.process_id)
        left join hris_departments de on(de.department_id = D.FROM_DEPARTMENT_CODE) where d.STATUS = 'E'
        {$deptCondition} {$descCondition} {$responseFlagCondition} {$processCondition}
        order by d.dispatch_draft_id desc";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function pushFileLink($data){ 
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $sql = "INSERT INTO HRIS_LEAVE_FILES(FILE_ID, FILE_NAME, FILE_IN_DIR_NAME, LEAVE_ID) VALUES((SELECT MAX(FILE_ID)+1 FROM HRIS_LEAVE_FILES), '$fileName', '$fileInDir', null)";
        $statement = $this->adapter->query($sql);
        $statement->execute(); 
        $sql = "SELECT * FROM HRIS_LEAVE_FILES WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM HRIS_LEAVE_FILES)";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($statement->execute());
    }

    public function getOutgoingbyId($data, $employeeId){

        $deptCondition = "";

        $responseFlagCondition = "";

        $descCondition = "";
        $processCondition = "";

        if($data['departmentId'] != null && $data['departmentId'] != -1 && $data['departmentId'] != " ") {
            $deptCondition = " AND d.FROM_DEPARTMENT_CODE = '{$data['departmentId']}' ";
        }
        if($data['processId'] != null && $data['processId'] != -1 && $data['processId'] != " ") {
            $processCondition = " AND d.PROCESS_ID = '{$data['processId']}' ";
        }
        if($data['responseFlag'] != null && $data['responseFlag'] != -1 && $data['responseFlag'] != " ") {
            $responseFlagCondition = " AND d.RESPONSE_FLAG = '{$data['responseFlag']}' ";
        }

        if($data['description'] != null && $data['description'] != -1 && $data['description'] != " ") {
            $descCondition = " AND d.DESCRIPTION like '%{$data['description']}%' ";
        }
        
        $sql = "select distinct d.*,L.location_edesc, bs_date(d.draft_date) as DRAFT_MITI, DE.DEPARTMENT_NAME, p.process_edesc from dc_dispatch_draft d left join dc_processes p on (p.process_id = d.process_id)
        left join hris_departments de on(de.department_id = D.FROM_DEPARTMENT_CODE) left join dc_user_assign ua on (ua.dispatch_draft_id = d.dispatch_draft_id)
        left join HRIS_EMPLOYEES e on (e.employee_id= ua.employee_id)
        left join HRIS_LOCATIONS L on (L.location_id = d.from_location_id) 
        where d.STATUS = 'E' and (ua.employee_id = $employeeId 
            or d.created_by = $employeeId)
        {$deptCondition} {$descCondition} {$responseFlagCondition} {$processCondition}
        order by d.dispatch_draft_id desc";
        // print_r($sql);die;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function geThisEmpDepId($empId){
        $sql = "select department_id from hris_employees where employee_id = $empId";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['DEPARTMENT_ID'];
    }

    public function fetchTableData($empId){
      
        $sql = "
        SELECT d.dispatch_code, d.letter_number, D.dispatch_ID,
        hrd.department_name, D.LETTER_Number,
        d.description,
        o.office_edesc,d.DISPATCH_DATE,bs_date(D.dispatch_date) as nepali_date,
        d.reg_id,
        d.response_flag,
        d.letter_ref_no, l.location_edesc FROM
        dc_dispatch d
        LEFT JOIN hris_departments   hrd
        ON ( d.from_department_code = hrd.department_id) 
        left join hris_locations l on (l.location_id = d.from_location_id)
        left join dc_offices o on(d.to_office_id = o.office_id) where d.status='E' 
        and d.from_location_id = (select location_id from hris_employees where employee_id = {$empId})
        order by d.dispatch_id desc";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        // print_r($sql);
        // die();
        return Helper::extractDbData($result);
    }

    public function fetchSearchData($data, $empId){
        $letterNoCondition = "";
        $fromDeptCondition = "";
        $descriptionCondition = "";
        $toOfficeCodeCondition = "";
        $toLocationCodeCondition="";

        if($data['letterNumber'] != null && $data['letterNumber'] != -1 && $data['letterNumber'] != " ") {
            $letterNoCondition = " AND d.LETTER_NUMBER like '%{$data['letterNumber']}%' ";
        }
        if($data['fromDepartment'] != null && $data['fromDepartment'] != -1 && $data['fromDepartment'] != " ") {
            $fromDeptCondition = " AND hrd.DEPARTMENT_ID = '{$data['fromDepartment']}' ";
        }
        if($data['descrip'] != null && $data['descrip'] != -1 && $data['descrip'] != " ") {
            $descriptionCondition = " AND d.DESCRIPTION like '%{$data['descrip']}%' ";
        }
        if($data['toOfficeCod'] != null && $data['toOfficeCod'] != -1 && $data['toOfficeCod'] != " ") {
            $toOfficeCodeCondition = " AND o.OFFICE_ID = '{$data['toOfficeCod']}' ";
        }
        if($data['toLocationCode'] != null && $data['toLocationCode'] != -1 && $data['toLocationCode'] != " ") {
            $toLocationCodeCondition = " AND l.LOCATION_ID = '{$data['toLocationCode']}' ";
        }

        $sql = "SELECT d.dispatch_id, d.dispatch_code, d.letter_number,
        hrd.department_name,
        d.description, l.location_edesc,
        o.office_edesc,d.reg_id,d.response_flag,d.DISPATCH_DATE,bs_date(D.dispatch_date) as nepali_date,
        d.letter_ref_no FROM
        dc_dispatch d
        LEFT JOIN hris_departments   hrd
        ON ( d.from_department_code = hrd.department_id) 
        left join hris_locations l on (l.location_id = d.from_location_id)
        left join dc_offices o 
        on(d.to_office_id = o.office_id)
        where 1=1 and d.status ='E' and d.from_location_id = (select location_id from hris_employees where employee_id = {$empId})
        {$letterNoCondition} {$fromDeptCondition} {$descriptionCondition} 
        {$toOfficeCodeCondition} {$toLocationCodeCondition}
        order by d.dispatch_id desc";
        // print_r($sql);
        // die();
        //$sql = "select dc.reg_id, dc.letter_ref_no, offc.office_edesc, dc.description, dc.sender_org, dept.department_name, emp.FULL_NAME, dc.response_flag  from DC_REGISTRATION dc LEFT join dc_offices offc on (dc.from_office_id = offc.office_id) LEFT join  hris_departments dept on (dc.department_id = dept.department_id) join hris_employees emp on (dc.receiver_name = emp.employee_id)";
        //$sql = "select * from DC_REGISTRATION  DC LEFT JOIN HRIS_DEPARTMENR D ON (DC.DEPARTMENT_ID = D.DEPARTMENT_ID) where 1=1 {$registrationNumCondition} {$senderOrgCondition} {$letterReferenceNumCondition} {$receivingDeptCondition} {$receiverNameCondition} {$responseFlagCondition} {$fromDateCondition} {$toDateCondition}";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    
}
