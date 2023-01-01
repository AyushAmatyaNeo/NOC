<?php
namespace SelfService\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Recruitment\Model\RecruitmentPersonal;
use Recruitment\Model\RecruitmentVacancy;
use Symfony\Component\VarDumper\VarDumper;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use SelfService\Model\ApplicationDocuments;
use Setup\Model\EmployeeFile;
use Recruitment\Model\UserApplicationModel;  

class VacancyRepository extends HrisRepository{
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway('HRIS_REC_APPLICATION_EDUCATION',$adapter);
        $this->tableEmpEdu = new TableGateway('HRIS_EMPLOYEE_QUALIFICATIONS',$adapter);
        $this->projectTable = new TableGateway('HRIS_REC_VACANCY_APPLICATION',$adapter);
        $this->paymentTable = new TableGateway('HRIS_REC_APPLICATION_PAYMENT',$adapter);
        $this->PersonalTable = new TableGateway('HRIS_REC_APPLICATION_PERSONAL',$adapter);
        $this->documentTable = new TableGateway('HRIS_REC_APPLICATION_DOCUMENTS',$adapter);
        $this->documentEmployeeTable = new TableGateway('HRIS_EMPLOYEE_FILE', $adapter);
    }

    
    public function add(Model $model) {
        $array = $model->getArrayCopyForDB();
        // echo '<pre>'; print_r($array); die();
        $this->tableGateway->insert($array);        
    }
    
    public function getFilteredRecords($search, $empId) {        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.VACANCY_ID AS VACANCY_ID"),
            new Expression("REC.VACANCY_NO AS VACANCY_NO"),
            new Expression("OPN.OPENING_NO AS OPENING_ID"),
            new Expression("REC.VACANCY_TYPE AS VACANCY_TYPE"),
            new Expression("REC.LEVEL_ID AS LEVEL_ID"),
            new Expression("REC.VACANCY_RESERVATION_NO AS VACANCY_RESERVATION_NO"),
            new Expression("REC.AD_NO AS AD_NO"),
            new Expression("HD.DEPARTMENT_NAME AS DEPARTMENT_ID"),
            new Expression("HOP.SERVICE_TYPE_NAME AS SERVICE_TYPE_ID"),
            new Expression("DES.DESIGNATION_TITLE AS POSITION_ID"),
            new Expression("REC.REMARK AS REMARKS"),
            new Expression("HQ.ACADEMIC_DEGREE_NAME AS QUALIFICATION_ID"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),
            new Expression("HFL.FUNCTIONAL_LEVEL_EDESC AS FUNCTIONAL_LEVEL_EDESC"),
            ], true);

        $select->from(['REC' => RecruitmentVacancy::TABLE_NAME])
                // ->join(['HG' => 'HRIS_GENDERS'],'HG.GENDER_ID=REC.GENDER', 'GENDER_ID', 'left')
                ->join(['HQ' => 'HRIS_ACADEMIC_DEGREES'],'HQ.ACADEMIC_DEGREE_ID=REC.QUALIFICATION_ID', 'ACADEMIC_DEGREE_ID', 'left')
                ->join(['HD' => 'HRIS_DEPARTMENTS'],'HD.DEPARTMENT_ID=REC.DEPARTMENT_ID', 'DEPARTMENT_CODE', 'left')
                ->join(['HOP' => 'HRIS_SERVICE_TYPES'], 'HOP.SERVICE_TYPE_ID=REC.SERVICE_TYPES_ID', 'SERVICE_TYPE_ID', 'left')
                ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=REC.POSITION_ID', 'status', 'left') 
                ->join(['OPN' => 'HRIS_REC_OPENINGS'],'OPN.OPENING_ID=REC.OPENING_ID', 'status', 'left') 
                ->join(['HFL' => 'HRIS_FUNCTIONAL_LEVELS'],'HFL.FUNCTIONAL_LEVEL_ID=REC.LEVEL_ID', 'status', 'left') 
                
                // $select->where(["REC.VACANCY_ID" => $id]);
                
                ->where(["REC.STATUS='E' AND HOP.STATUS='E' AND REC.VACANCY_TYPE in ('INTERNAL_FORM',
                'INTERNAL_APPRAISAL')
                AND REC.LEVEL_ID in 
(select functional_level_id from hris_functional_levels where order_id in 
(select order_id - 1 from hris_functional_levels where functional_level_id in (
select functional_level_id from hris_employees where employee_id = $empId)))"]);

        if (($search['openingId'] != null)) {
            $select->where([
                "REC.OPENING_ID" => $search['openingId']
            ]);
        }
        if (($search['QualificationId'] != null)) {
            $select->where([
                "REC.QUALIFICATION_ID" => $search['QualificationId']
            ]);
        }
        if (($search['AdNo'] != null)) {
            $select->where([
                "REC.VACANCY_ID" => $search['AdNo']
            ]);
        }
        if (($search['DepartmentId'] != null)) {
            $select->where([
                "REC.DEPARTMENT_ID" => $search['DepartmentId']
            ]);
        }
        if (($search['positionId'] != null)) {
            $select->where([
                "REC.POSITION_ID" => $search['positionId']
            ]);
        }

        // $select->where([ "REC.LEVEL_ID in (select functional_level_id from hris_functional_levels where order_no in (select order_number - 1 from hris_functional_levels where functional_level_id in (select functional_level_id from hris_employees where employee_id = $empId))"]);

        $select->order("REC.VACANCY_ID ASC");
        $boundedParameter = [];
        
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo('<pre>');print_r($statement);die;

        // print_r ($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }

    public function fetchById($id) {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("REC.VACANCY_ID AS VACANCY_ID"),
            new Expression("REC.VACANCY_NO AS VACANCY_NO"),
            new Expression("REC.OPENING_ID AS OPENING_ID"),
            new Expression("REC.VACANCY_TYPE AS VACANCY_TYPE"),
            // new Expression("REC.VACANCY_TYPE AS VACANCY_TYPE"),/
            new Expression("REC.LEVEL_ID AS LEVEL_ID"),
            new Expression("REC.SKILL_ID AS SKILL_ID"),
            new Expression("REC.INCLUSION_ID AS INCLUSION_ID"),
            new Expression("REC.VACANCY_RESERVATION_NO AS VACANCY_RESERVATION_NO"),
            new Expression("REC.AD_NO AS AD_NO"),
            new Expression("REC.DEPARTMENT_ID AS DEPARTMENT_ID"),
            new Expression("REC.EXPERIENCE AS EXPERIENCE"),
            new Expression("REC.SERVICE_TYPES_ID AS SERVICE_TYPES_ID"),
            new Expression("REC.SERVICE_EVENTS_ID AS SERVICE_EVENTS_ID"),
            new Expression("REC.POSITION_ID AS POSITION_ID"),
            new Expression("REC.REMARK AS REMARK"),
            // new Expression("HI.INCLUSION_ID AS INCLUSION_ID"),
            new Expression("REC.QUALIFICATION_ID AS QUALIFICATION_ID"),
            // new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),
            ], true);

        $select->from(['REC' => RecruitmentVacancy::TABLE_NAME])
        ->join(['HI' => 'HRIS_REC_VACANCY_INCLUSION'],'HI.VACANCY_ID=REC.VACANCY_ID', 'VACANCY_INCLUSION_ID', 'left');


        $select->where(["REC.VACANCY_ID='{$id}'"]); //change to this if not working. remove below 2 line.
        // $select->where(["REC.VACANCY_ID" => $id]);
        // $select->order("REC.VACANCY_EDESC DESC");
        $boundedParameter = [];
      
        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute($boundedParameter);
        // print_r ($statement->getSql()); die();
        return $result->current();
    }
    
    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [RecruitmentVacancy::VACANCY_ID => $id]);
        // echo '<pre>';print_r($this->db->last_query());
        
    }
    public function PrevVacancyData($att){
        $sql = "select MAX(". $att .") AS ". $att ." from hris_rec_vacancy where vacancy_id = (select MAX(vacancy_id) from hris_rec_vacancy)";
        // echo $sql; die;
        $result =  $this->rawQuery($sql);
        return $result[0][$att];    
    }
    public function getAdNumber($openingId, $AdNo){
        $sql = "select vacancy_id from hris_rec_vacancy where opening_id = ". $openingId." and ad_no = '".$AdNo."'";
        $result =  $this->rawQuery($sql);
        return $result[0]['VACANCY_ID'];
    }
    public function InternalVacancyData($id){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("REC.VACANCY_ID AS VACANCY_ID"),
            new Expression("REC.VACANCY_NO AS VACANCY_NO"),
            new Expression("REC.OPENING_ID AS OPENING_ID"),
            new Expression("HO.OPENING_NO AS OPENING_NO"),
            new Expression("REC.VACANCY_TYPE AS VACANCY_TYPE"),
            new Expression("LVL.FUNCTIONAL_LEVEL_NO AS FUNCTIONAL_LEVEL_NO"),
            new Expression("REC.LEVEL_ID AS LEVEL_ID"),
            new Expression("REC.SKILL_ID AS SKILL_ID"),
            new Expression("REC.INCLUSION_ID AS INCLUSION_ID"),
            new Expression("REC.AD_NO AS AD_NO"),
            new Expression("DEP.DEPARTMENT_NAME AS DEPARTMENT_NAME"),
            new Expression("REC.DEPARTMENT_ID AS DEPARTMENT_ID"),
            new Expression("REC.EXPERIENCE AS EXPERIENCE"),
            new Expression("SER.SERVICE_TYPE_NAME AS SERVICE_TYPE_NAME"),
            new Expression("REC.SERVICE_TYPES_ID AS SERVICE_TYPES_ID"),
            new Expression("SEV.SERVICE_EVENT_NAME AS SERVICE_EVENT_NAME"),
            new Expression("REC.SERVICE_EVENTS_ID AS SERVICE_EVENTS_ID"),
            new Expression("DES.DESIGNATION_TITLE AS POSITION_NAME"),
            new Expression("REC.POSITION_ID AS POSITION_ID"),
            new Expression("REC.REMARK AS REMARK"),
            new Expression("DN.ACADEMIC_DEGREE_NAME AS QUALIFICATION"),
            new Expression("DN.ACADEMIC_DEGREE_CODE AS CODE"),
            ], true);

        $select->from(['REC' => RecruitmentVacancy::TABLE_NAME])
                ->join(['HI' => 'HRIS_REC_VACANCY_INCLUSION'],'HI.VACANCY_ID=REC.VACANCY_ID', 'VACANCY_INCLUSION_ID', 'left')
                ->join(['LVL' => 'HRIS_FUNCTIONAL_LEVELS'],'LVL.FUNCTIONAL_LEVEL_ID=REC.LEVEL_ID', 'STATUS', 'left')
                ->join(['SER' => 'HRIS_SERVICE_TYPES'],'SER.SERVICE_TYPE_ID=REC.SERVICE_TYPES_ID', 'STATUS', 'left')
                ->join(['SEV' => 'HRIS_REC_SERVICE_EVENTS_TYPES'],'SEV.SERVICE_EVENT_ID=REC.SERVICE_EVENTS_ID', 'STATUS','left')
                ->join(['DEP' => 'HRIS_DEPARTMENTS'],'DEP.DEPARTMENT_ID=REC.DEPARTMENT_ID', 'status', 'left')
                ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=REC.POSITION_ID', 'status', 'left')
                ->join(['DN' => 'HRIS_ACADEMIC_DEGREES'],'REC.QUALIFICATION_ID=DN.ACADEMIC_DEGREE_ID', 'status', 'left')
                ->join(['HO' => 'HRIS_REC_OPENINGS'],'HO.OPENING_ID=REC.OPENING_ID', 'status', 'left');

        $select->where(["REC.VACANCY_ID='{$id}'"]); //change to this if not working. remove below 2 line.
        $boundedParameter = [];      
        $statement = $sql->prepareStatementForSqlObject($select);        
        $result = $statement->execute($boundedParameter);
        // print_r ($result->current()); die();
        return $result->current();
    }
    public function academicCertificates($academicCode){
        
        $sql = new Sql($this->adapter);
        $query = "SELECT CER.ACADEMIC_DEGREE_NAME AS ACADEMIC_DEGREE_NAME FROM HRIS_ACADEMIC_DEGREES CER WHERE CER.STATUS='E' AND ACADEMIC_DEGREE_CODE <= '{$academicCode}'";
        $result = $this->rawQuery($query);
        // $select = $sql->select();
        // $select->columns([
        //     new Expression("CER.ACADEMIC_DEGREE_NAME AS ACADEMIC_DEGREE_NAME"),
        // ], true);
        // $select->from(['CER' => 'HRIS_ACADEMIC_DEGREES']);
        // $select->where(["CER.STATUS='E' AND ACADEMIC_DEGREE_CODE <= '{$academicCode}'"]);
        // $boundedParameter = [];      
        // $statement = $sql->prepareStatementForSqlObject($select);        
        // $result = $statement->execute($boundedParameter);
        // print_r ($result); die();
        return $result;
    }
    public function empData($eid){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("REC.EMPLOYEE_CODE AS EMPLOYEE_CODE"),
            new Expression("REC.FIRST_NAME AS FIRST_NAME"),
            new Expression("REC.MIDDLE_NAME AS MIDDLE_NAME"),
            new Expression("REC.LAST_NAME AS LAST_NAME"),
            new Expression("REC.DESIGNATION_ID AS DESIGNATION_ID"),
            new Expression("ADD.MAIL_TOLE AS MAIL_TOLE"),

            new Expression("REC.POSITION_ID AS POSITION_ID"),
            new Expression("LVL.FUNCTIONAL_LEVEL_EDESC AS FUNCTIONAL_LEVEL_NAME"),
            new Expression("REC.FUNCTIONAL_LEVEL_ID AS FUNCTIONAL_LEVEL_ID"),
            new Expression("DEP.DEPARTMENT_NAME AS DEPARTMENT_ID"),
            new Expression("SER.SERVICE_TYPE_NAME AS SERVICE_TYPE_NAME"),
            new Expression("REC.SERVICE_TYPE_ID AS SERVICE_TYPES_ID"),
            new Expression("SEV.SERVICE_EVENT_NAME AS SERVICE_EVENT_NAME"),
            new Expression("REC.SERVICE_EVENT_TYPE_ID AS SERVICE_EVENTS_ID"),
            new Expression("DES.DESIGNATION_TITLE AS DESIGNATION_TITLE"),
            new Expression("REC.DESIGNATION_ID AS DESIGNATION_ID"),
            new Expression("EF.FILE_PATH AS PROFILE_PATH"),
            new Expression("EF.FILE_CODE AS FILE_ID"),
            ], true);

        $select->from(['REC'  => 'HRIS_EMPLOYEES'])
                ->join(['SER' => 'HRIS_SERVICE_TYPES'],'SER.SERVICE_TYPE_ID=REC.SERVICE_TYPE_ID', 'STATUS', 'left')
                ->join(['SEV' => 'HRIS_REC_SERVICE_EVENTS_TYPES'],'SEV.SERVICE_EVENT_ID=REC.SERVICE_EVENT_TYPE_ID', 'STATUS','left')
                ->join(['DEP' => 'HRIS_DEPARTMENTS'],'DEP.DEPARTMENT_ID=REC.DEPARTMENT_ID', 'status', 'left')
                ->join(['LVL' => 'HRIS_FUNCTIONAL_LEVELS'],'LVL.FUNCTIONAL_LEVEL_ID=REC.FUNCTIONAL_LEVEL_ID', 'STATUS', 'left')
                ->join(['EF' => 'HRIS_EMPLOYEE_FILE'],'EF.FILE_CODE=REC.PROFILE_PICTURE_ID', 'STATUS', 'left')
                ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=REC.POSITION_ID', 'status', 'left')
                ->join(['ADD' => 'HRIS_REC_USERS_ADDRESS'],'ADD.USER_ID=REC.EMPLOYEE_ID', 'status', 'left');


        $select->where(["REC.EMPLOYEE_ID= $eid"]);
        $boundedParameter = [];      
        $statement = $sql->prepareStatementForSqlObject($select); 
        $result = $statement->execute($boundedParameter);
        return $result->current();
    }

    public function empEdu($eid){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("AP.ACADEMIC_PROGRAM_NAME AS ACADEMIC_PROGRAM_ID"),
            new Expression("ADE.ACADEMIC_DEGREE_NAME AS ACADEMIC_DEGREE_ID"),
            new Expression("ACS.ACADEMIC_COURSE_NAME AS ACADEMIC_COURSE_ID"),
            new Expression("REC.PASSED_YR AS PASSED_YEAR"),
            new Expression("UNI.ACADEMIC_UNIVERSITY_NAME AS ACADEMIC_UNIVERSITY_ID"),
            new Expression("REC.RANK_TYPE AS RANK_TYPE"),
            new Expression("REC.RANK_VALUE AS RANK_VALUE"),
            new Expression("REC.ID AS ID"),
            ], true);

        $select->from(['REC'  => 'HRIS_EMPLOYEE_QUALIFICATIONS'])
                ->join(['AP' => 'HRIS_ACADEMIC_PROGRAMS'],'AP.ACADEMIC_PROGRAM_ID=REC.ACADEMIC_PROGRAM_ID', 'STATUS', 'left')
                ->join(['UNI' => 'HRIS_ACADEMIC_UNIVERSITY'],'UNI.ACADEMIC_UNIVERSITY_ID=REC.ACADEMIC_UNIVERSITY_ID', 'STATUS','left')
                ->join(['ADE' => 'HRIS_ACADEMIC_DEGREES'],'ADE.ACADEMIC_DEGREE_ID=REC.ACADEMIC_DEGREE_ID', 'status', 'left')
                ->join(['ACS' => 'HRIS_ACADEMIC_COURSES'],'ACS.ACADEMIC_COURSE_ID=REC.ACADEMIC_COURSE_ID', 'STATUS', 'left');

        $select->where(["REC.EMPLOYEE_ID= $eid"]);
        $select->order("REC.ACADEMIC_PROGRAM_ID DESC");
        $boundedParameter = [];      
        $statement = $sql->prepareStatementForSqlObject($select); 
        $result = $statement->execute();
        return $result;
    }
    public function empjob($eid){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("REC.JOB_HISTORY_ID AS JOB_HISTORY_ID"),
            new Expression("REC.START_DATE AS START_DATE"),
            new Expression("REC.REMARKS AS REMARKS"),
            new Expression("DES.DESIGNATION_TITLE AS DESIGNATION_TITLE"),
            new Expression("POS.POSITION_NAME AS POSITION_NAME"),
            new Expression("FUN.FUNCTIONAL_LEVEL_EDESC AS FUNCTIONAL_LEVEL_EDESC"),
            new Expression("LOC.LOCATION_EDESC AS LOCATION_EDESC"),
            new Expression("BRA.BRANCH_NAME AS BRANCH_NAME"),
            new Expression("REC.END_DATE AS END_DATE"),
            new Expression("SER.SERVICE_TYPE_NAME AS SERVICE_TYPE_NAME"),
            new Expression("REC.SERVICE_EVENT_TYPE_ID AS SERVICE_TYPES_ID"),
            new Expression("REC.TO_BRANCH_ID AS TO_BRANCH_ID"),
            new Expression("DEP.DEPARTMENT_NAME AS DEPARTMENT_NAME"),

            ], true);

        $select->from(['REC'  => 'HRIS_JOB_HISTORY'])
                ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=REC.TO_DESIGNATION_ID', 'STATUS', 'left')
                ->join(['POS' => 'HRIS_POSITIONS'],'POS.POSITION_ID=REC.TO_POSITION_ID', 'STATUS', 'left')
                ->join(['SER' => 'HRIS_SERVICE_TYPES'],'SER.SERVICE_TYPE_ID=REC.TO_SERVICE_TYPE_ID', 'STATUS', 'left')
                ->join(['FUN' => 'HRIS_FUNCTIONAL_LEVELS'],'FUN.FUNCTIONAL_LEVEL_ID=REC.TO_FUNCTIONAL_LEVEL', 'status', 'left')
                ->join(['LOC' => 'HRIS_LOCATIONS'],'LOC.LOCATION_ID=REC.TO_LOCATION_ID', 'STATUS', 'left')
                ->join(['BRA' => 'HRIS_BRANCHES'],'BRA.BRANCH_ID=REC.TO_BRANCH_ID', 'status', 'left')
                ->join(['DEP' => 'HRIS_DEPARTMENTS'],'DEP.DEPARTMENT_ID=REC.TO_DEPARTMENT_ID', 'status', 'left');


        $select->where(["REC.EMPLOYEE_ID= $eid and
        REC.SERVICE_EVENT_TYPE_ID in (SELECT
        service_event_type_id 
       from HRIS_SERVICE_EVENT_TYPES 
       where service_event_type_code in ('APP',
       'PRO') )  "]);
        $select->order("REC.START_DATE DESC");
        $boundedParameter = [];      
        $statement = $sql->prepareStatementForSqlObject($select); 
        $result = $statement->execute();
        return $result;
    }
    public function eduDocuments($eid)
    {
        $sql = ("SELECT HRIS_EMPLOYEE_FILE.FILE_PATH, 
                HRIS_EMPLOYEE_FILE_SETUP.FILE_NAME  
                FROM HRIS_EMPLOYEE_FILE LEFT JOIN HRIS_EMPLOYEE_FILE_SETUP ON HRIS_EMPLOYEE_FILE.FILE_ID = HRIS_EMPLOYEE_FILE_SETUP.FILE_ID where HRIS_EMPLOYEE_FILE.EMPLOYEE_ID = {$eid} and HRIS_EMPLOYEE_FILE.status = 'E'");

        $result = $this->rawQuery($sql);
        return $result;

    }
    public function fileType($ext)
    {
        $upperExt = strtoupper($ext);
        
        $sql = ("SELECT HRIS_FILE_TYPE.FILETYPE_CODE FROM HRIS_FILE_TYPE where upper(NAME) LIKE '{$upperExt}'");
        $result = $this->rawQuery($sql);
        // var_dump($result); die;
        return $result;
    }
    public function fileSetId($fileName)
    {
        $sql = ("SELECT HRIS_EMPLOYEE_FILE_SETUP.FILE_ID FROM HRIS_EMPLOYEE_FILE_SETUP where FILE_NAME LIKE '{$fileName}'");
        $result = $this->rawQuery($sql);
        // var_dump($sql); die;
        return $result;

    }
    public function insertEmployeeDocuments($data)
    {
        // var_dump($data); die;
        $this->documentEmployeeTable->insert($data); 
    }
    public function getInclusions($uid, $type, $vid)
    {
    //    var_dump($uid, $type, $vid); die;
       $sql = ("SELECT * from hris_rec_vacancy_application WHERE USER_ID = {$uid} AND AD_NO = {$vid} AND APPLICATION_TYPE = '{$type}' and status='E'");
       $application = $this->rawQuery($sql);
    //    print_r($sql);die;
       $aid = $application[0]['APPLICATION_ID'];
       if($aid){
        $query = ("SELECT * FROM HRIS_REC_APPLICATION_PERSONAL where APPLICATION_ID = {$aid} AND STATUS = 'E'");
        //    print_r($query);die;
        $application_personal = $this->rawQuery($query);
        $result = [
            'application' => $application,
            'application_personal' => $application_personal,
            'aid' => $aid,
        ];
        return $result;
       }else{
        return;
       }
        
    }

    public function getInclusionsAll($application_type = 'open')
    {

        $sql = ("SELECT * FROM HRIS_REC_OPTIONS WHERE STATUS = 'E'");

        if (strtolower($application_type) !== 'open') {

            $application_type = "('Internal Competition','Internal-Appraisal','Open')";

            $sql = ("SELECT * FROM HRIS_REC_OPTIONS WHERE OPTION_EDESC NOT IN $application_type and status='E'");
        
        }
        
        $result = $this->rawQuery($sql);

        return $result;
    }

    public function getEmployeeInclusion($employeeId, $type)
    {

        // $sql = ("SELECT * FROM HRIS_REC_OPTIONS WHERE STATUS = 'E' AND EMPLOYEE_ID = {$empId}");

        if ($type == 'form')
        {
            $sql = ("SELECT * FROM HRIS_EMPLOYEES AS E 
                    LEFT JOIN HRIS_EMPLOYEE_FILE AS F ON E.INCLUSION_FORM_FILE_ID = F.FILE_CODE 
                    WHERE E.STATUS = 'E' AND E.EMPLOYEE_ID = {$employeeId}");
        } else {

            $sql = ("SELECT * FROM HRIS_EMPLOYEES AS E 
                    LEFT JOIN HRIS_EMPLOYEE_FILE AS F ON E.INCLUSION_APPRAISAL_FILE_ID = F.FILE_CODE 
                    WHERE E.STATUS = 'E' AND E.EMPLOYEE_ID = {$employeeId}");

        }

        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();

        return $result->current();

    }

     public function casLeaveEarlier($eid)
    {
        $sql = ("SELECT COUNT (HRIS_ATTENDANCE_DETAIL.ATTENDANCE_DT) as TOTALLEAVE from HRIS_ATTENDANCE_DETAIL WHERE OVERALL_STATUS = 'LV' AND EMPLOYEE_ID = {$eid} AND ATTENDANCE_DT < '2021-11-19'  and leave_id in 
        (select leave_id from hris_leave_master_setup where leave_code = 'EXTRLEV')");
        $result = $this->rawQuery($sql);
        // var_dump($sql); die;
        return $result;
    }
    public function casLeaveLater($eid)
    {
        $sql = ("SELECT COUNT (HRIS_ATTENDANCE_DETAIL.ATTENDANCE_DT) as TOTALLEAVE from HRIS_ATTENDANCE_DETAIL WHERE OVERALL_STATUS = 'LV' AND EMPLOYEE_ID = {$eid} AND ATTENDANCE_DT > '2021-11-19'  and leave_id in 
        (select leave_id from hris_leave_master_setup where leave_code = 'EXTRLEV')");
        $result = $this->rawQuery($sql);
        // var_dump($result); die;
        return $result;
    }
    public function userId($eid)
    {
        $sql = ("Select HRIS_USERS.USER_ID from HRIS_USERS WHERE employee_id = {$eid}");
        $result = $this->rawQuery($sql);
        // var_dump($result); die;
        return $result;
    }

    public function getAppliedStoredDocuments($aid, $uid)
    {
        // var_dump($aid, $uid); die;
        $sql = "SELECT * FROM HRIS_REC_APPLICATION_DOCUMENTS WHERE USER_ID = {$uid} AND APPLICATION_ID = {$aid} and status='E'";
        $result = $this->rawQuery($sql);
        // var_dump($sql); die;
        return $result;
    }
    public function checkVacancyStatus($v_id, $e_id)
    {
        $sql = "SELECT 
                    HRIS_REC_VACANCY_APPLICATION.APPLICATION_ID,
                    HRIS_REC_VACANCY_APPLICATION.APPLICATION_TYPE, 
                    HRIS_REC_VACANCY_APPLICATION.USER_ID, 
                    HRIS_REC_VACANCY_APPLICATION.APPLICATION_ID, 
                
                CASE WHEN
	                stage_id in (
                        SELECT rec_stage_id from hris_rec_stages where order_no >= (select order_no from hris_rec_stages where rec_stage_id = 8))
                    then 'Y' else 'N' END as ADMIN_CARD_GENERATED  from HRIS_REC_VACANCY_APPLICATION  WHERE USER_ID = {$e_id} AND AD_NO = {$v_id} and status='E'";

        // echo "<pre>"; print_r($sql); die;
        $result = $this->rawQuery($sql);
        // var_dump($sql); die;
        return $result;
    }
    public function getRegNo($id){
        $sql = "SELECT COUNT(APPLICATION_ID) AS APP_ID FROM HRIS_REC_VACANCY_APPLICATION WHERE AD_NO = {$id}";
        $result = $this->rawQuery($sql);
        return $result[0];
    }
    public function fetchInclusionById($id){
        if ($id > 0) {
            $sql = "SELECT OPTION_ID AS INCLUSION_ID, OPTION_EDESC, UPLOAD_FLAG FROM HRIS_REC_OPTIONS where OPTION_ID = $id";
            $result = $this->rawQuery($sql);
        }      
        return ($result[0]) ? $result[0] : false;
    }
    public function insertPersonal($data){
        // var_dump($data); die;
        $this->PersonalTable->insert($data); 
    }

    public function getMaxIds($id_name,$table)
    {
        $sql = "SELECT MAX($id_name) AS MAXID FROM {$table}";

        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();

        return $result->current();

        // $query = $this->db->query("SELECT MAX($id_name) AS MAXID FROM $table");
        // $result = $query->row_array();
        // return $result;
    }

    public function getRowId($table, $where, $where_value) {
        
        $result = '';
        if ($where_value) {

            $sql = "SELECT * FROM {$table} WHERE {$where} = {$where_value}";

            $statement = $this->adapter->query($sql);
            $result    = $statement->execute();

        }
        

        return ($result) ? $result->current() : false;
    }

    public function deleteRowId($table, $where, $where_value) {
        $sql = "DELETE FROM {$table} WHERE {$where} = {$where_value}";

        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();

        return true;
    }

    public function getUpdateById($table, $data, $where, $where_value) {

        $column = implode(',', array_keys($data));
        $value  = implode("','", array_values($data));

        $sql = "UPDATE {$table} SET ($column) = ('$value') WHERE {$where} = {$where_value}";

        $result = $this->rawQuery($sql);

        return true;
    }

    public function checkVacancyApplicationApplied($v_id, $e_id, $a_id = NULL)
    {
        if ($a_id !== NULL) {
            $sql = "SELECT * FROM HRIS_REC_VACANCY_APPLICATION A
                    LEFT JOIN HRIS_REC_APPLICATION_PERSONAL P ON P.APPLICATION_ID = A.APPLICATION_ID
                    WHERE A.APPLICATION_ID = {$a_id} AND A.USER_ID = {$e_id} AND A.AD_NO = {$v_id} AND A.STATUS='E'";
            $statement = $this->adapter->query($sql);
            $result    = $statement->execute();
            
            return $result->current();
        } 
        return false;
    }

    public function checkApplicationStages($stage_id) {
        /**
         * A : APPLICATION   V: VACANCY
         * */

        $sql = "SELECT * FROM HRIS_REC_STAGES WHERE REC_STAGE_ID = {$stage_id} AND VACANCY_APPLICATION = 'A'";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result->current();

    }


    public function checkVacancyApplicationByAidVid($a_id, $v_id)
    {
        $sql = "SELECT *  FROM HRIS_REC_VACANCY_APPLICATION
                WHERE APPLICATION_ID = {$a_id} AND AD_NO = {$v_id}";

        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();
        return $result->current();
    }

    public function checkVacancyApplicationByColumn($data) 
    {
        $key   = implode(',', array_keys($data));
        $value = implode("','", array_values($data));

        $sql = "SELECT *  FROM HRIS_REC_VACANCY_APPLICATION WHERE {$key} = '$value' ";

        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();
        return $result->current();
    }

    public function checkVacancyApplicationPaymentByColumn($data) 
    {
        $key   = implode(',', array_keys($data));
        $value = implode("','", array_values($data));

        $sql = "SELECT *  FROM HRIS_REC_APPLICATION_PAYMENT WHERE {$key} = '$value' ";

        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();
        return $result->current();
    }

    public function getPaymentGateway()
    {
        $sql    = "SELECT * FROM HRIS_REC_PAYMENT_GATEWAY WHERE STATUS = 1";
        $result = $this->rawQuery($sql);
        return $result;
    }

    public function getPaymentGatewayByWhere($data) 
    {
        $key   = implode(',', array_keys($data));
        $value = implode("','", array_values($data));

        $sql = "SELECT *  FROM HRIS_REC_PAYMENT_GATEWAY WHERE {$key} = '$value' ";

        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();
        return $result->current();
    }

    public function insertApplicationPayment($data){
        // echo '<pre>'; print_r($data); die;       
        $this->paymentTable->insert($data); 
        return true;
    }

    public function updateApplicationPayment($column, $update, $where) {

        $key   = implode(',', array_keys($where));
        $value = implode("','", array_values($where));

        $sql   = "UPDATE HRIS_REC_APPLICATION_PAYMENT SET ($column) = ('$update') WHERE {$key} = '$value'";
        $result= $this->rawQuery($sql);

        return true;
    }

    public function updateApplicationPaymentArray($update,  $where) {

        $column   = implode(',', array_keys($update));
        $data     = implode("','", array_values($update));

        $key   = implode(',', array_keys($where));
        $value = implode("','", array_values($where));

        $sql   = "UPDATE HRIS_REC_APPLICATION_PAYMENT SET ($column) = ('$data') WHERE {$key} = '$value'";
        
        $result= $this->rawQuery($sql);

        return true;
    }


    public function updateVacancyApplication($column, $update, $a_id, $v_id)
    {
        $sql   = "UPDATE HRIS_REC_VACANCY_APPLICATION SET ($column) = ('$update') WHERE APPLICATION_ID = {$a_id} AND AD_NO = {$v_id}";
        $result = $this->rawQuery($sql);

        return true;
    }

    public function updateVacancyApplicationArray($update, $a_id, $v_id)
    {
        $key   = implode(',', array_keys($update));
        $value = implode("','", array_values($update));

        $sql   = "UPDATE HRIS_REC_VACANCY_APPLICATION SET ($key) = ('$value') WHERE APPLICATION_ID = {$a_id} AND AD_NO = {$v_id}";
        $result = $this->rawQuery($sql);

        return true;
    }

    public function updatePersonal($data, $adNo, $uid)
    {
        $sql =  "SELECT * FROM HRIS_REC_VACANCY_APPLICATION WHERE AD_NO = {$adNo} AND USER_ID = {$uid} AND APPLICATION_TYPE = 'Internal-form'";
        $result = $this->rawQuery($sql);
        $app_id = $result[0]['APPLICATION_ID'];
        $query = "SELECT * FROM HRIS_REC_APPLICATION_PERSONAL WHERE APPLICATION_ID = {$app_id} AND USER_ID = {$uid}";
        $qResult = $this->rawQuery($query);
        $id = $qResult[0]['PERSONAL_ID'];

        $data['CREATED_DATE'] = $qResult[0]['CREATED_DATE'];
        $this->PersonalTable->update($data, [RecruitmentPersonal::PERSONAL_ID => $id]);
        return $app_id;
    }

    public function insertApplication($data){
        // echo '<pre>'; print_r($data); die;       
        $this->projectTable->insert($data); 
    }


    public function insertEdu($data){
        $this->tableGateway->insert($data); 
    }
    public function insertDocuments($data){
        $this->documentTable->insert($data); 
    }
    public function updateDocuments($data, $folder, $u_id, $v_id)
    {
        $sql = "SELECT * FROM HRIS_REC_APPLICATION_DOCUMENTS WHERE VACANCY_ID = {$v_id} AND USER_ID = {$u_id} AND DOC_FOLDER = '{$folder}' AND STATUS='E'";
        $result = $this->rawQuery($sql);

        $filePath =  __DIR__ . "/../../../../public/".$result[0]['DOC_PATH'].$result[0]['DOC_NEW_NAME'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $id =  $result[0]['REC_DOC_ID'];
        $this->documentTable->update($data, [ApplicationDocuments::REC_DOC_ID => $id]);
        return 'here';
    }
    public function updateEduDocuments($data, $fid, $eid)
    {
        $sql = "SELECT * FROM HRIS_EMPLOYEE_FILE WHERE FILE_ID = {$fid} AND EMPLOYEE_ID = {$eid} and status='E' ";
        $result = $this->rawQuery($sql);
        $id = $result[0]['FILE_CODE'];
        $data ['CREATED_DT'] = $result[0]['CREATED_DT'];
        $this->documentEmployeeTable->update($data, [EmployeeFile::FILE_CODE => $id]);
        $filePath =  __DIR__ . "/../../../../public/uploads/".$result[0]['FILE_PATH'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        return 'done';
    }
    public function updateApplication($data, $vid, $uid)
    {
        $sql = "SELECT * FROM HRIS_REC_VACANCY_APPLICATION WHERE USER_ID = {$uid} AND AD_NO = {$vid}";
        $result = $this->rawQuery($sql);
        $id = $result[0]['APPLICATION_ID'];
        $this->projectTable->update($data, [UserApplicationModel::APPLICATION_ID => $id]);
    }
    public function inclusionamount($level,$position){
        // var_dump($level,$position); die;
        $sql = ("SELECT ifnull(NORMAL_AMOUNT,0) as NORMAL_AMOUNT,ifnull(LATE_AMOUNT,0) as LATE_AMOUNT,ifnull(INCLUSION_AMOUNT,0) as INCLUSION_AMOUNT,END_DATE,EXTENDED_DATE FROM HRIS_REC_VACANCY_LEVELS NV
        LEFT JOIN HRIS_REC_OPENINGS HO ON NV.OPENING_ID = HO.OPENING_ID
        WHERE FUNCTIONAL_LEVEL_ID = {$level} AND POSITION_ID = {$position} AND NV.STATUS ='E' ORDER BY EFFECTIVE_DATE DESC");
        $result = $this->rawQuery($sql);
        return $result;
    }
    public function insertEmpEdu($data)
    {
        // var_dump($data); die;
        $this->tableEmpEdu->insert($data);  
    }

    public function inclusionAppliedCheck($id)
    {
        $sql = ("SELECT HRIS_EMPLOYEES.INCLUSION, HRIS_EMPLOYEES.JOIN_DATE,HRIS_FUNCTIONAL_LEVELS.FUNCTIONAL_LEVEL_NO, YEARS_BETWEEN(HRIS_EMPLOYEES.JOIN_DATE, current_date) as duration  FROM HRIS_EMPLOYEES LEFT JOIN HRIS_FUNCTIONAL_LEVELS ON HRIS_EMPLOYEES.FUNCTIONAL_LEVEL_ID = HRIS_FUNCTIONAL_LEVELS.FUNCTIONAL_LEVEL_ID WHERE HRIS_EMPLOYEES.EMPLOYEE_ID = {$id}");
        $result = $this->rawQuery($sql);
        return $result[0];
    }

    public function inclusionPromoCheck($id)
    {
        $q = ("SELECT HRIS_JOB_HISTORY.START_DATE,HRIS_FUNCTIONAL_LEVELS.FUNCTIONAL_LEVEL_NO FROM HRIS_JOB_HISTORY LEFT JOIN HRIS_FUNCTIONAL_LEVELS ON HRIS_JOB_HISTORY.TO_FUNCTIONAL_LEVEL = HRIS_FUNCTIONAL_LEVELS.FUNCTIONAL_LEVEL_ID WHERE EMPLOYEE_ID = {$id}");
        $result = $this->rawQuery($q);
        return $result;
    }
    
    public function updateProfilePic($fileDetail, $empId){
        $sql = "insert into hris_employee_file (file_code, file_path, status, created_dt, file_id) 
        values ((select ifnull(max(file_code),0) +1 from hris_employee_file), '{$fileDetail['newImageName']}', 'E', current_date, 0)";
        $result = $this->rawQuery($sql);

        $updateSql = "update hris_employees set PROFILE_PICTURE_ID = (select ifnull(max(file_code),0) from hris_employee_file) where employee_id = $empId";
        $result = $this->rawQuery($updateSql);

        return;
    }

    public function updateEmployeeInclusion($update, $empId){
        $key   = implode(',', array_keys($update));
        $value = implode("','", array_values($update));

        $sql   = "UPDATE HRIS_EMPLOYEES  SET ($key) = ('$value') WHERE EMPLOYEE_ID = {$empId}";

        
        $result = $this->rawQuery($sql);

        return true;
    }

    public function getOpening($openingId)
    {
        $sql = "SELECT *  FROM HRIS_REC_OPENINGS WHERE OPENING_ID = '$openingId' ";

        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();
        return $result->current();
    }

    public function removeEmployeeInclusion($empId, $fileId, $type, $inclusion_process)
    {

        if ($type == 'appraisal') {

            $sql = "BEGIN
                    UPDATE HRIS_EMPLOYEES SET (INCLUSION_APPRAISAL_USED, INCLUSION_APPRAISAL_USED_DATE, INCLUSION_ID_APPRAISAL, INCLUSION_APPRAISAL_FILE_ID, INCLUSION_APPRAISAL_FOR_YEAR, INCLUSION_USED_PROCESS) = ('N','NULL','NULL','NULL','NULL','".$inclusion_process."') WHERE EMPLOYEE_ID={$empId};
                    DELETE FROM HRIS_EMPLOYEE_FILE WHERE FILE_CODE = {$fileId};
                    END;";


        } else {

            $sql = "BEGIN
                    UPDATE HRIS_EMPLOYEES SET (INCLUSION_FORM_USED, INCLUSION_FORM_USED_DATE, INCLUSION_ID_FORM, INCLUSION_FORM_FILE_ID, INCLUSION_FORM_FOR_YEAR, INCLUSION_USED_PROCESS) = ('N','NULL','NULL','NULL','NULL','".$inclusion_process."') WHERE EMPLOYEE_ID={$empId};
                    DELETE FROM HRIS_EMPLOYEE_FILE WHERE FILE_CODE = {$fileId};
                    END;";
        }
        
        $result = $this->rawQuery($sql);

        return true;

    }

    public function admitCardVacancy($uid, $appid){

        $sql  = "SELECT 
                    AP.ROLL_NO,
                    OP.OPTION_EDESC, OP.OPTION_NDESC,
                    RV.AD_NO,
                    HUR.FIRST_NAME,HUR.MIDDLE_NAME,HUR.LAST_NAME,
                    HUR.ID_CITIZENSHIP_NO, HUR.ID_CITIZENSHIP_ISSUE_DATE,
                    VU.USER_ID,
                    HUR.EMPLOYEE_ID,

                    HD.DESIGNATION_TITLE,
                    HFL.FUNCTIONAL_LEVEL_EDESC,
                    HST.SERVICE_TYPE_NAME,
                    HET.SERVICE_EVENT_NAME,

                    FPV.VENUE_NAME AS FIRST_PAPER_VENUE,
                    
                    FPVA.START_TIME AS FIRST_START_TIME, 
                    FPVA.END_TIME   AS FIRST_END_TIME,
                    FPVA.EXAM_DATE  AS FIRST_EXAM_DATE,
                    FPVA.EXAM_TYPE  AS FIRST_EXAM_TYPE,
                    FPVA.VENUE_SETUP_ID AS FIRST_VENUE_SETUP_ID,

                    SPVA.START_TIME AS SECOND_START_TIME, 
                    SPVA.END_TIME   AS SECOND_END_TIME, 
                    SPVA.EXAM_DATE  AS SECOND_EXAM_DATE, 
                    SPVA.EXAM_TYPE  AS SECOND_EXAM_TYPE,
                    SPVA.VENUE_SETUP_ID AS SECOND_VENUE_SETUP_ID,

                    SPV.VENUE_NAME AS SECOND_PAPER_VENUE,

                    F.FILE_PATH AS PROFILE

                FROM HRIS_REC_VACANCY_APPLICATION AS NV

                LEFT JOIN HRIS_REC_VENUE_SETUP FPV ON FPV.VENUE_SETUP_ID = NV.FIRST_PAPER_VENUE_ID

                LEFT JOIN HRIS_REC_VENUE_ASSIGN FPVA ON (FPVA.VENUE_SETUP_ID = NV.FIRST_PAPER_VENUE_ID AND ({$appid} BETWEEN FPVA.START_INDEX AND FPVA.END_INDEX) AND FPVA.EXAM_TYPE = 'FIRST_PAPER')

                LEFT JOIN HRIS_REC_VENUE_SETUP SPV ON SPV.VENUE_SETUP_ID = NV.SECOND_PAPER_VENUE_ID

                LEFT JOIN HRIS_REC_VENUE_ASSIGN SPVA ON (SPVA.VENUE_SETUP_ID = NV.SECOND_PAPER_VENUE_ID AND ({$appid} BETWEEN SPVA.START_INDEX AND SPVA.END_INDEX) AND SPVA.EXAM_TYPE = 'SECOND_PAPER')

                LEFT JOIN HRIS_REC_VACANCY AS RV ON RV.VACANCY_ID = NV.AD_NO
                LEFT JOIN HRIS_REC_APPLICATION_PERSONAL AS AP ON AP.APPLICATION_ID = NV.APPLICATION_ID
                LEFT JOIN HRIS_REC_OPTIONS AS OP ON OP.OPTION_ID = AP.INCLUSION_ID
                LEFT JOIN HRIS_DESIGNATIONS AS HD ON RV.POSITION_ID = HD.DESIGNATION_ID
                LEFT JOIN HRIS_DEPARTMENTS AS HVD ON RV.DEPARTMENT_ID = HVD.DEPARTMENT_ID
                LEFT JOIN HRIS_SERVICE_TYPES AS HST ON HST.SERVICE_TYPE_ID = RV.SERVICE_TYPES_ID
                LEFT JOIN HRIS_REC_SERVICE_EVENTS_TYPES AS HET ON HET.SERVICE_EVENT_ID = RV.SERVICE_EVENTS_ID
                LEFT JOIN HRIS_FUNCTIONAL_LEVELS AS HFL ON HFL.FUNCTIONAL_LEVEL_ID = RV.LEVEL_ID
                LEFT JOIN HRIS_USERS AS VU ON VU.USER_ID = NV.USER_ID       
                LEFT JOIN HRIS_EMPLOYEES AS HUR ON HUR.EMPLOYEE_ID = VU.EMPLOYEE_ID
                LEFT JOIN HRIS_EMPLOYEE_FILE AS F ON F.FILE_CODE = HUR.PROFILE_PICTURE_ID

                WHERE NV.USER_ID = $uid AND NV.APPLICATION_ID = $appid ORDER BY NV.APPLICATION_ID";

        // $sql   = "SELECT *
        //           FROM HRIS_USERS AS U
        //           LEFT JOIN HRIS_EMPLOYEES AS E ON E.EMPLOYEE_ID = U.EMPLOYEE_ID
        //           LEFT JOIN HRIS_EMPLOYEE_FILE AS F ON F.FILE_CODE = E.PROFILE_PICTURE_ID
        //           LEFT JOIN HRIS_REC_VACANCY_APPLICATION AS A ON A.USER_ID = U.USER_ID
        //           LEFT JOIN HRIS_REC_VACANCY AS V ON V.VACANCY_ID = A.AD_NO
        //           LEFT JOIN HRIS_REC_APPLICATION_PERSONAL AS P ON P.APPLICATION_ID = A.APPLICATION_ID
        //           LEFT JOIN HRIS_DESIGNATIONS AS HD ON V.POSITION_ID = HD.DESIGNATION_ID
        //           LEFT JOIN HRIS_DEPARTMENTS AS HVD ON V.DEPARTMENT_ID = HVD.DEPARTMENT_ID
        //           LEFT JOIN HRIS_SERVICE_TYPES AS HST ON HST.SERVICE_TYPE_ID = V.SERVICE_TYPES_ID
        //           LEFT JOIN HRIS_REC_SERVICE_EVENTS_TYPES AS HET ON HET.SERVICE_EVENT_ID = V.SERVICE_EVENTS_ID
                  

        //           WHERE A.USER_ID = {$uid} AND A.APPLICATION_ID = {$appid}
        //          ";

        // $sql = "SELECT * FROM HRIS_USERS WHERE USER_ID = {$uid}";
        // echo "<pre>";
        // print_r($sql);
        // die;


        // $result = $this->rawQuery($sql);
        // return $result;
        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();
        return $result->current();        
    }

    public function admitCardDocument($uid,$appid){
        $query  = "SELECT * FROM HRIS_REC_APPLICATION_DOCUMENTS 
                   WHERE DOC_FOLDER NOT IN ('skills','certificates') AND USER_ID = {$uid} AND APPLICATION_ID = {$appid} 
                   ORDER BY REC_DOC_ID";
        $result = $this->rawQuery($query);
        return $result;
    }

    public function admitCardTerm()
    {
        $query  = "SELECT * FROM HRIS_REC_ADMIT_SETUP WHERE ADMIT_SETUP_ID = 1";
        $statement = $this->adapter->query($query);
        $result    = $statement->execute();
        return $result->current();  
    }


    // public function checkVacancyApplicationAdmitCardReady($v_id, $e_id, $a_id = NULL)
    // {
    //     if ($a_id !== NULL) {
    //         $sql = "SELECT * FROM HRIS_REC_APPLICATION_PERSONAL
    //                 WHERE APPLICATION_ID = {$a_id} AND USER_ID = {$e_id} AND AD_NO = {$v_id} AND STATUS='E'";
    //         $statement = $this->adapter->query($sql);
    //         $result    = $statement->execute();
            
    //         return $result->current();
    //     } 
    //     return false;
    // }

}   