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
use DartaChalani\Model\ChalaniFinal;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql; 

class DispatchRepo extends HrisRepository {

    // protected $tableGateway;
    // protected $adapter;
    // public function __construct(AdapterInterface $adapter) {
    //     $this->adapter = $adapter;
        
    // }
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(ChalaniFinal::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }
    public function fetchTableData(){
      
        $sql = "
        SELECT d.dispatch_code, d.letter_number, D.dispatch_ID,
        hrd.department_name, D.LETTER_Number,
        d.description,
        CASE WHEN (d.to_office_id=(select office_id from dc_offices where office_code='D01'))
        then d.to_other_office else o.office_edesc end as office_edesc,d.DISPATCH_DATE,bs_date(D.dispatch_date) as nepali_date,
        d.reg_id,
        d.response_flag,
        d.letter_ref_no, l.location_edesc FROM
        dc_dispatch d
        LEFT JOIN hris_departments   hrd
        ON ( d.from_department_code = hrd.department_id) 
        left join hris_locations l on (l.location_id = d.from_location_id)
        left join dc_offices o on(d.to_office_id = o.office_id) where d.status='E' 
        order by d.dispatch_id desc";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        // print_r($sql);
        // die();
        return Helper::extractDbData($result);
    }

    public function fetchSearchData($data){
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
        CASE
        WHEN (d.to_office_id=(select office_id from dc_offices where office_code='D01'))
	    then d.to_other_office
        else 
        o.office_edesc
        end
        as office_edesc,
        d.reg_id,d.response_flag,d.DISPATCH_DATE,bs_date(D.dispatch_date) as nepali_date,
        d.letter_ref_no FROM
        dc_dispatch d 
        LEFT JOIN hris_departments   hrd
        ON ( d.from_department_code = hrd.department_id) 
        left join hris_locations l on (l.location_id = d.from_location_id)
        left join dc_offices o 
        on(d.to_office_id = o.office_id)
        where 1=1 and d.status ='E'
        {$letterNoCondition} {$fromDeptCondition} {$descriptionCondition} 
        {$toOfficeCodeCondition} {$toLocationCodeCondition}
        order by d.dispatch_id desc";
        //print_r($sql);
        // die();
        //$sql = "select dc.reg_id, dc.letter_ref_no, offc.office_edesc, dc.description, dc.sender_org, dept.department_name, emp.FULL_NAME, dc.response_flag  from DC_REGISTRATION dc LEFT join dc_offices offc on (dc.from_office_id = offc.office_id) LEFT join  hris_departments dept on (dc.department_id = dept.department_id) join hris_employees emp on (dc.receiver_name = emp.employee_id)";
        //$sql = "select * from DC_REGISTRATION  DC LEFT JOIN HRIS_DEPARTMENR D ON (DC.DEPARTMENT_ID = D.DEPARTMENT_ID) where 1=1 {$registrationNumCondition} {$senderOrgCondition} {$letterReferenceNumCondition} {$receivingDeptCondition} {$receiverNameCondition} {$responseFlagCondition} {$fromDateCondition} {$toDateCondition}";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    public function fetchById($id){
        $sql = "select d.reg_id, 
        d.letter_number, 
        hrd.department_name, 
        d.description,
        o.office_edesc,
        d.document_date,
        d.dispatch_date, 
        d.letter_ref_no, 
        case when d.response_flag='N' 
        then 'No' else 'Yes' end as response_flag 
        from dc_dispatch d left join hris_departments hrd on (d.from_department_code = hrd.department_id) 
        left join dc_offices o on (d.to_office_id = o.office_id) where d.dispatch_id = $id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        
        return Helper::extractDbData($result);
        // print_r($result); die;
    }

    public function pullFilebyId($id)
    {
        $boundedParams = [];
        $boundedParams['id'] = $id;
        $sql = "select FILE_PATH from DC_DISPATCH_DRAFT where DISPATCH_DRAFT_ID = ? limit 1";
        return $this->rawQuery($sql, $boundedParams);
    }
        // $sql = "select FILE_PATH from DC_DISPATCH_DRAFT where DISPATCH_DRAFT_ID = :id
        // and rownum=1";
        // $boundedParams['id'] = $id;
        // // print_r($id);
        // // echo '<br>';
        // // print_r($this->rawQuery($sql, $boundedParams));
        // // die();
        // return $this->rawQuery($sql, $boundedParams)[0];
    // }

    public function pushFileLink($data, $userID)
    {
        $draftId = $data->dispatchDraftId;
        $filePath = $data->filePath;
        $processId = $data->processId;
        $departmentId = $data->fromDepartmentCode;

        if($departmentId){
            $sql = "INSERT INTO DC_DISPATCH_DOCS(REG_DRAFT_ID,PROCESS_ID, FILE_PATH, USER_ID, DEPARTMENT_ID)
            VALUES( {$draftId},{$processId}, '{$filePath}',{$userID}, {$departmentId})";
            $statement = $this->adapter->query($sql);
        }else{
            $sql = "INSERT INTO DC_DISPATCH_DOCS(REG_DRAFT_ID,PROCESS_ID, FILE_PATH, USER_ID)
            VALUES( {$draftId},{$processId}, '{$filePath}',{$userID})";
            $statement = $this->adapter->query($sql);
        }
        $statement->execute();
        return;
    }
    
    public function getDocumentHistory($id){
        $sql = "(SELECT
        dc.dispatch_draft_id,
        e.full_name,
        to_char(dd.doc_date,'dd-mon-yyyy hh:mi am') doc_date,
        ifnull(dd.file_path, null) file_path,
        dp.process_edesc,
        hd.department_name
    FROM
        dc_dispatch_draft dc
        LEFT JOIN hris_departments hd ON ( dc.from_department_code = hd.department_id )
        LEFT JOIN dc_dispatch_docs dd ON ( dc.dispatch_draft_id = dd.reg_draft_id
                                           AND dd.process_id = 4 )
        LEFT JOIN hris_employees e ON ( e.employee_id = dc.created_by )
        LEFT JOIN dc_processes dp ON ( dp.process_id = dd.process_id )
    WHERE
        dc.dispatch_draft_id = (select dispatch_temp_code from dc_dispatch where dispatch_id = $id) )
        
        
    UNION ALL
    
    
    (
    
    
    SELECT
        distinct ua.dispatch_draft_id,
        e.full_name,
        to_char(dd.doc_date,'dd-mon-yyyy hh:mi am') doc_date,
        ifnull(dd.file_path, null) file_path,
        dp.process_edesc,
        hd.department_name
    FROM
        dc_user_assign ua
        LEFT JOIN dc_dispatch d ON ( ua.employee_id = d.created_by AND d.dispatch_temp_code = ua.dispatch_draft_id)
        LEFT JOIN hris_departments hd ON ( ua.deptartment_id = hd.department_id )
        LEFT JOIN dc_processes dp ON ( dp.process_id = ua.process_id )
        LEFT JOIN dc_dispatch_docs dd ON ( ua.dispatch_draft_id = dd.reg_draft_id
                                           AND (dd.process_id = 5 or dd.process_id = 6) AND dd.department_id = ua.deptartment_id )
        LEFT JOIN hris_employees e ON ( e.employee_id = dd.user_id )
    WHERE
        ua.status = 'E'
        
        AND ua.dispatch_draft_id = (select dispatch_temp_code from dc_dispatch where dispatch_id = $id) 
         order by doc_date desc)
    ";
        return $this->rawQuery($sql);
    }

    public function linkDispatchFiles($data, $employeeId){
        $fileName = $data['file'];
        $id = $data['id'];
        $oldfile = $data['fileName'];
        $sql = "INSERT INTO DC_DISPATCH_DOCS(FILE_ID, REG_DRAFT_ID,PROCESS_ID, FILE_PATH, USER_ID, FILE_NAME)
        VALUES( (select nvl(max(FILE_ID)+1, 1) from DC_DISPATCH_DOCS) , {$id}, 7, '{$fileName}', {$employeeId}, '{$oldfile}')";
        return $this->rawQuery($sql);
    }

    public function pullDispatchFilebyId(){
        $sql = "select * from DC_DISPATCH_DOCS where file_id = (select nvl(max(FILE_ID), 1) from DC_DISPATCH_DOCS)";
        return $this->rawQuery($sql);
    }

    public function pullAllFilesbyId($id)
    {
        $boundedParams = [];
        $boundedParams['id'] = $id;
        $sql = "select * from DC_DISPATCH where dispatch_id = ?";
        // print_r($id);die;
        return $this->rawQuery($sql, $boundedParams);
    }
}