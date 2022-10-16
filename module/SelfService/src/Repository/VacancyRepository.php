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
                
                ->where(["REC.STATUS='E' AND HOP.STATUS='E' AND REC.VACANCY_TYPE = 'INTERNAL'
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
            new Expression("REC.RANK_VALUE AS RANK_VALUE")
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


        $select->where(["REC.EMPLOYEE_ID= $eid"]);
        $select->order("REC.START_DATE DESC");
        $boundedParameter = [];      
        $statement = $sql->prepareStatementForSqlObject($select); 
        // print_r ($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }
    public function eduDocuments($eid)
    {
        $sql = ("SELECT HRIS_EMPLOYEE_FILE.FILE_PATH, HRIS_EMPLOYEE_FILE_SETUP.FILE_NAME  FROM HRIS_EMPLOYEE_FILE LEFT JOIN HRIS_EMPLOYEE_FILE_SETUP ON HRIS_EMPLOYEE_FILE.FILE_ID = HRIS_EMPLOYEE_FILE_SETUP.FILE_ID where HRIS_EMPLOYEE_FILE.EMPLOYEE_ID = {$eid} and HRIS_EMPLOYEE_FILE.status = 'E'");
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
       $sql = ("SELECT * from hris_rec_vacancy_application WHERE USER_ID = {$uid} AND AD_NO = {$vid} AND APPLICATION_TYPE = '{$type}'");
       $application = $this->rawQuery($sql);
    //    print_r($sql);die;
       $aid = $application[0]['APPLICATION_ID'];
       $query = ("SELECT * FROM HRIS_REC_APPLICATION_PERSONAL where APPLICATION_ID = {$aid} AND STATUS = 'E'");
    //    print_r($query);die;
       $application_personal = $this->rawQuery($query);
       $result = [
           'application' => $application,
           'application_personal' => $application_personal,
           'aid' => $aid,
       ];
       return $result;
        
    }
    public function casLeaveEarlier($eid)
    {
        $sql = ("SELECT COUNT (HRIS_ATTENDANCE_DETAIL.ATTENDANCE_DT) as TOTALLEAVE from HRIS_ATTENDANCE_DETAIL WHERE OVERALL_STATUS = 'LV' AND EMPLOYEE_ID = {$eid} AND ATTENDANCE_DT < '2021-11-19'");
        $result = $this->rawQuery($sql);
        // var_dump($result); die;
        return $result;
    }
    public function casLeaveLater($eid)
    {
        $sql = ("SELECT COUNT (HRIS_ATTENDANCE_DETAIL.ATTENDANCE_DT) as TOTALLEAVE from HRIS_ATTENDANCE_DETAIL WHERE OVERALL_STATUS = 'LV' AND EMPLOYEE_ID = {$eid} AND ATTENDANCE_DT > '2021-11-19'");
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
        // var_dump($result); die;
        return $result;
    }
    public function checkVacancyStatus($v_id, $e_id)
    {
        $sql = "SELECT HRIS_REC_VACANCY_APPLICATION.APPLICATION_TYPE, HRIS_REC_VACANCY_APPLICATION.USER_ID  from HRIS_REC_VACANCY_APPLICATION WHERE USER_ID = {$e_id} AND AD_NO = {$v_id} and status='E'";
        $result = $this->rawQuery($sql);
        // var_dump($result); die;
        return $result;
    }
    public function getRegNo($id){
        $sql = "SELECT COUNT(APPLICATION_ID) AS APP_ID FROM HRIS_REC_VACANCY_APPLICATION WHERE AD_NO = {$id}";
        $result = $this->rawQuery($sql);
        return $result[0];
    }
    public function fetchInclusionById($id){
        $sql = "SELECT OPTION_ID AS INCLUSION_ID,OPTION_EDESC FROM HRIS_REC_OPTIONS where OPTION_ID = $id";
        $result = $this->rawQuery($sql);
        return $result[0];
    }
    public function insertPersonal($data){
        // var_dump($data); die;
        $this->PersonalTable->insert($data); 
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
        $sql = "SELECT * FROM HRIS_REC_APPLICATION_DOCUMENTS WHERE VACANCY_ID = {$v_id} AND USER_ID = {$u_id} AND DOC_FOLDER = '{$folder}'";
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
        $sql = "SELECT * FROM HRIS_EMPLOYEE_FILE WHERE FILE_ID = {$fid} AND EMPLOYEE_ID = {$eid} ";
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
        $sql = ("SELECT HRIS_EMPLOYEES.INCLUSION, HRIS_EMPLOYEES.JOIN_DATE,HRIS_FUNCTIONAL_LEVELS.FUNCTIONAL_LEVEL_NO  FROM HRIS_EMPLOYEES LEFT JOIN HRIS_FUNCTIONAL_LEVELS ON HRIS_EMPLOYEES.FUNCTIONAL_LEVEL_ID = HRIS_FUNCTIONAL_LEVELS.FUNCTIONAL_LEVEL_ID WHERE HRIS_EMPLOYEES.EMPLOYEE_ID = {$id}");
        $result = $this->rawQuery($sql);
        return $result[0];
    }
    public function inclusionPromoCheck($id)
    {
        $q = ("SELECT HRIS_JOB_HISTORY.START_DATE,HRIS_FUNCTIONAL_LEVELS.FUNCTIONAL_LEVEL_NO FROM HRIS_JOB_HISTORY LEFT JOIN HRIS_FUNCTIONAL_LEVELS ON HRIS_JOB_HISTORY.TO_FUNCTIONAL_LEVEL = HRIS_FUNCTIONAL_LEVELS.FUNCTIONAL_LEVEL_ID WHERE EMPLOYEE_ID = {$id}");
        $result = $this->rawQuery($q);
        return $result;
    }
}   