<?php
namespace DartaChalani\Repository;

use Application\Model\Model;
use Application\Helper\Helper;
use Application\Repository\RepositoryInterface;
use Application\Repository\HrisRepository;
use DartaChalani\Model\Chalani;
use DartaChalani\Model\UserAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Application\Helper\EntityHelper;


class ChalaniRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Chalani::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
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
    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Chalani::DISPATCH_DRAFT_ID => $id]);
    }

    public function fetchData(){
        $sql = "select d.*,bs_date(d.draft_date) as DRAFT_MITI, DE.DEPARTMENT_NAME, p.process_edesc,
        L.location_edesc from dc_dispatch_draft d left join dc_processes p on (p.process_id = d.process_id)
        left join hris_departments de on(de.department_id = D.FROM_DEPARTMENT_CODE) 
        left join hris_locations L on (L.location_id = d.from_location_id) where d.STATUS = 'E'
        order by d.dispatch_draft_id desc
        ";
        return $this->rawQuery($sql);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Chalani::class, [Chalani::DISPATCH_TEMP_CODE]), false);
            $select->where([Chalani::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->order([Chalani::DISPATCH_TEMP_CODE => Select::ORDER_ASCENDING]);
        });
    }

    public function fetchById($id) {
        return $this->tableGateway->select(function(Select $select) use($id) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new Chalani(), [
                        'documentDt', 'draftDt'
                    ]), false);
            $select->where([Chalani::DISPATCH_DRAFT_ID => $id]);})->current();
    }

    public function delete($id) {
        $this->tableGateway->update([Chalani::STATUS => 'D'], [Chalani::DISPATCH_DRAFT_ID => $id]);
        
    }

    public function forward(Model $model) {
        $tableGateway = new TableGateway(UserAssign::TABLE_NAME, $this->adapter);
        $tableGateway->insert($model->getArrayCopyForDB());
    }

    public function ack(Model $model) {
        $tableGateway = new TableGateway(UserAssign::TABLE_NAME, $this->adapter);
        $tableGateway->insert($model->getArrayCopyForDB());
    }

    public function getSearchResults($data) {
       
        $sql = "select bs_date(dc.draft_date) as DRAFT_MITI, DC.RESPONSE_FLAG, DC.DESCRIPTION, DC.DOCUMENT_DATE, DC.DISPATCH_DRAFT_ID, 
            DC.DISPATCH_TEMP_CODE, DC.LETTER_REF_NO, DC.REMARKS from DC_DISPATCH_DRAFT DC 
                left join HRIS_DEPARTMENTS D on (DC.FROM_DEPARTMENT_CODE = D.DEPARTMENT_ID)
                where DC.status = 'E'";
              
        if($data['departmentId'] != -1){
            $sql.=" and D.DEPARTMENT_ID = {$data['departmentId']}";
        }

        if ($data['letterNumber'] != null || $data['letterNumber'] != ''){
            $sql.=" and DC.LETTER_NUMBER = {$data['letterNumber']}";
        }

        if ($data['description'] != null || $data['description'] != ''){
            $sql.=" and DC.DESCRIPTION = '{$data['description']}'";   
        }

        if ($data['toOfficeCode'] != null || $data['toOfficeCode'] != ''){
            $sql.=" and DC.TO_OFFICE_ID = '{$data['toOfficeCode']}'";
        }

        if ($data['responseFlag'] != null || $data['responseFlag'] != ''){
            $sql.=" and DC.RESPONSE_FLAG = '{$data['responseFlag']}'";
        }
        
    
        return $this->rawQuery($sql);
      

    }
    public function getCode($date, $locationId){
        $sql = "select letter_number from dc_dispatch where dispatch_id  = (select max(dispatch_id) from dc_dispatch where from_location_id = {$locationId} and status = 'E')";
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
    // public function pushFileLink($data){ 
    //     $fileName = $data['fileName'];
    //     $fileInDir = $data['filePath'];
    //     $sql = "INSERT INTO HRIS_LEAVE_FILES(FILE_ID, FILE_NAME, FILE_IN_DIR_NAME, LEAVE_ID) VALUES((SELECT MAX(FILE_ID)+1 FROM HRIS_LEAVE_FILES), '$fileName', '$fileInDir', null)";
    //     $statement = $this->adapter->query($sql);
    //     $statement->execute(); 
    //     $sql = "SELECT * FROM HRIS_LEAVE_FILES WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM HRIS_LEAVE_FILES)";
    //     $statement = $this->adapter->query($sql);
    //     return Helper::extractDbData($statement->execute());
    // }

    
    
    public function getEmployeeList($departmentId){
        $sql = "select distinct employee_id from dc_departments_users where department_id = $departmentId";
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
    public function getDispatchProcess(){
        $sql = "select process_edesc from dc_processes where process_start_flag = 'N' and process_end_flag = 'Y' and is_registration='N' and status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['PROCESS_EDESC'];
    }
    public function getDispatchProcessId(){
        $sql = "select process_id from dc_processes where process_start_flag = 'N' and process_end_flag = 'Y' and is_registration='N' and status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result)[0]['PROCESS_ID'];
    }
    public function getPastValues($id){
        $sql = "select dispatch_draft_id, dispatch_temp_code, draft_date, from_department_code, to_office_id,status,created_by, created_dt, response_flag, file_path from dc_dispatch_draft where dispatch_draft_id=$id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    // public function getEmployeeList(){
    //     $sql = "SELECT EMPLOYEE_ID, full_name, department_id FROM HRIS_EMPLOYEES where status = 'E'";
    //     $statement = $this->adapter->query($sql);
    //     $result = Helper::extractDbData($statement->execute());

    //     $employeeList = [];

    //     foreach ($result as $allEmployee) {
            
    //         $tempId = $allEmployee['DEPARTMENT_ID'];
    //         (!array_key_exists($tempId, $employeeList)) ?
    //         $employeeList[$tempId][0] = $allEmployee :
    //         array_push($employeeList[$tempId], $allEmployee);
    //     }

    //     return $employeeList;
    // }
    public function getOutgoingbyId($data){
        $deptCondition = "";

        $responseFlagCondition = "";

        $descCondition = "";
        $processCondition = "";
        $locationCodeCondition="";

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

        if($data['toLocationCode'] != null && $data['toLocationCode'] != -1 && $data['toLocationCode'] != " ") {
            $locationCodeCondition = " AND l.LOCATION_ID = {$data['toLocationCode']} ";
        }
        
        $sql = "select d.*,l.location_edesc,bs_date(d.draft_date) as DRAFT_MITI, DE.DEPARTMENT_NAME, p.process_edesc from dc_dispatch_draft d left join dc_processes p on (p.process_id = d.process_id)
        left join hris_departments de on(de.department_id = D.FROM_DEPARTMENT_CODE)
        left join hris_locations l on (l.location_id = d.from_location_id) where d.STATUS = 'E'
        {$deptCondition} {$descCondition} {$responseFlagCondition} {$processCondition} {$locationCodeCondition}
        order by d.dispatch_draft_id desc";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    public function sendNotification($model){
        $eid = $model->employeeId;
        // $sql = "insert into HRIS_NOTIFICATION(message_id, message_datetime, message_title, message_desc, 
        // message_from, message_to, status, expiry_time, route) values((select max(message_id)+1 from HRIS_NOTIFICATION),
        // trunc(sysdate), 'Chalani Forwarded', 'A chalani has been forwarded to your department.', 1, $eid, 'U', '30-JUL-28', '{\"route\":\"outgoingdoc\",\"action\":\"index\"}')";
        // $this->rawQuery($sql);
    }

    public function getFilePath($id){
        
        $sql = "select file_path from DC_DISPATCH_DRAFT where DISPATCH_DRAFT_ID = {$id}";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getLocationWiseEmployeeList($id){
        $sql = "select distinct employee_id from dc_departments_users where location_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
}
