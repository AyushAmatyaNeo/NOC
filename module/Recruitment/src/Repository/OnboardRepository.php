<?php
namespace Recruitment\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Recruitment\Model\UserApplicationModel;
use Zend\Db\Sql\Sql;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Setup\Model\HrEmployees;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Recruitment\Model\RecruitmentPersonal;

class OnboardRepository extends HrisRepository{
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->documentEmployeeTable = new TableGateway('HRIS_EMPLOYEES', $adapter);
        $this->documentEmployeeFileTable = new TableGateway('HRIS_EMPLOYEE_FILE', $adapter);
        $this->educationDocument = new TableGateway('HRIS_EMPLOYEE_QUALIFICATIONS', $adapter);
        $this->expDocument = new TableGateway('HRIS_EMPLOYEE_EXPERIENCES', $adapter);
        $this->empPersonal = new TableGateway('HRIS_REC_APPLICATION_PERSONAL',$adapter);
    }
    public function getAllOnstageData()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            // new Expression("REC.PERSONAL_ID            AS PERSONAL_ID"),
            new Expression("REC.APPLICATION_ID         AS APPLICATION_ID"),
            // new Expression("REC.USER_ID                AS USER_ID"),
            new Expression("UR.MARITAL_STATUS         AS MARITAL_STATUS"),
            new Expression("UR.EMPLOYMENT_STATUS     AS EMPLOYMENT_STATUS"),
            new Expression("UR.EMPLOYMENT_INPUT      AS EMPLOYMENT_INPUT"),
            new Expression("UR.DISABILITY             AS DISABILITY"),
            new Expression("UR.DISABILITY_INPUT       AS DISABILITY_INPUT"),
            new Expression("REC.SKILL_ID               AS SKILL_ID"),
            new Expression( "UR.RELIGION   AS  RELIGION "),      
            new Expression( "UR.RELIGION_INPUT   AS  RELIGION_INPUT "),      
            new Expression( "UR.REGION   AS  REGION "),      
            new Expression( "UR.REGION_INPUT   AS  REGION_INPUT "),      
            new Expression( "UR.ETHNIC_NAME   AS  ETHNIC_NAME "),      
            new Expression( "UR.ETHNIC_INPUT   AS  ETHNIC_INPUT "),      
            new Expression( "UR.MOTHER_TONGUE   AS  MOTHER_TONGUE "),      
            new Expression( "UR.CITIZENSHIP_NO   AS  CITIZENSHIP_NO "),      
            new Expression( "UR.CTZ_ISSUE_DATE   AS  CTZ_ISSUE_DATE "),      
            new Expression( "HRD.DISTRICT_NAME   AS  CTZ_ISSUE_DISTRICT_ID "),      
            new Expression( "UR.DOB   AS  DOB "),      
            new Expression( "UR.AGE   AS  AGE "),      
            new Expression( "UR.PHONE_NO   AS  PHONE_NO "),      
            new Expression( "UR.GENDER_ID   AS  GENDER_ID "),        
            new Expression( "UR.SPOUSE_NAME   AS  SPOUSE_NAME "),      
            new Expression( "UR.SPOUSE_NATIONALITY   AS  SPOUSE_NATIONALITY "),      
            new Expression( "UR.PROFILE_STATUS   AS  PROFILE_STATUS "),
            new Expression("UN.FIRST_NAME    AS FIRST_NAME "),
            new Expression("UN.MIDDLE_NAME    AS MIDDLE_NAME "),
            new Expression("UN.LAST_NAME    AS LAST_NAME "),
            new Expression("UN.MOBILE_NO    AS MOBILE_NO "),
            new Expression("UN.EMAIL_ID    AS EMAIL_ID "),
            new Expression("UN.USERNAME    AS USERNAME "),
            new Expression(" VAC.AD_NO  AS AD_NO "),
            new Expression(" UVA.REGISTRATION_NO    AS REGISTRATION_NO "),
            new Expression(" VS.STAGE_EDESC   AS STAGE_ID "),
            new Expression(" UVA.APPLICATION_AMOUNT AS APPLICATION_AMOUNT "),
            new Expression("SER.SERVICE_TYPE_NAME        AS SERVICE_TYPE_ID"),
            new Expression("SEV.SERVICE_EVENT_NAME      AS SERVICE_EVENTS_ID"),
            new Expression("DES.DESIGNATION_TITLE            AS POSITION_ID"),
            new Expression("DEP.DEPARTMENT_NAME          AS DEPARTMENT_ID"),
            new Expression("VAC.VACANCY_TYPE          AS VACANCY_TYPE"),
            // DOCUMENT
            new Expression(" DOC.DOC_PATH AS PROFILE_IMG "),

            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            ], true);

        $select->from(['REC' => 'HRIS_REC_APPLICATION_PERSONAL'])
                ->join(['UR' => 'HRIS_REC_USERS_REGISTRATION'],'UR.USER_ID=REC.USER_ID', 'STATUS', 'left')
                ->join(['UN' => 'HRIS_REC_VACANCY_USERS'],'UN.USER_ID=REC.USER_ID', 'RESET_STATUS', 'left')
                ->join(['UVA' => 'HRIS_REC_VACANCY_APPLICATION'],'UVA.APPLICATION_ID=REC.APPLICATION_ID', 'APPLICATION_ID', 'left')
                ->join(['HRD' => 'HRIS_DISTRICTS'],'HRD.DISTRICT_ID=UR.CTZ_ISSUE_DISTRICT_ID', 'ZONE_ID', 'left')
                ->join(['DOC' => 'HRIS_REC_APPLICATION_DOCUMENTS'],'DOC.APPLICATION_ID=REC.APPLICATION_ID', 'USER_ID', 'left')
                ->join(['VAC' => 'HRIS_REC_VACANCY'],'VAC.VACANCY_ID=UVA.AD_NO', 'status', 'left')
                ->join(['VS' => 'HRIS_REC_STAGES'],'VS.REC_STAGE_ID=UVA.STAGE_ID', 'status', 'left')  
                ->join(['SER' => 'HRIS_SERVICE_TYPES'],'SER.SERVICE_TYPE_ID=VAC.SERVICE_TYPES_ID', 'STATUS', 'left')
                ->join(['SEV' => 'HRIS_REC_SERVICE_EVENTS_TYPES'],'SEV.SERVICE_EVENT_ID=VAC.SERVICE_EVENTS_ID', 'STATUS','left')  
                ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=VAC.POSITION_ID', 'status', 'left')  
                ->join(['DEP' => 'HRIS_DEPARTMENTS'],'DEP.DEPARTMENT_ID=VAC.DEPARTMENT_ID', 'status', 'left') 
                ->where(["REC.STATUS='E'"])
                ->where(["DOC.DOC_FOLDER = 'photograph'"]);        
        $select->order("UVA.APPLICATION_ID ASC");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function tranferApplicants($id)
    {
        $sql =  "SELECT
        HRIS_REC_APPLICATION_PERSONAL.PERSONAL_ID,
        HRIS_REC_APPLICATION_PERSONAL.INCLUSION_ID,
        HRIS_REC_VACANCY_USERS.FIRST_NAME,
        HRIS_REC_VACANCY_USERS.MIDDLE_NAME,
        HRIS_REC_VACANCY_USERS.LAST_NAME,
        HRIS_REC_VACANCY_USERS.MOBILE_NO,
        HRIS_REC_VACANCY_USERS.EMAIL_ID,
        HUR.RELIGION,
        HUR.REGION,
        HUR.ETHNIC_NAME,
        HUR.MOTHER_TONGUE,
        HUR.CITIZENSHIP_NO,
        HUR.CTZ_ISSUE_DATE,
        HUR.CTZ_ISSUE_DISTRICT_ID,
        HUR.DOB,
        HUR.AGE,
        HUR.PHONE_NO,
        HUR.GENDER_ID,
        HUR.FATHER_NAME,
        HUR.FATHER_QUALIFICATION,
        HUR.MOTHER_NAME,
        HUR.FM_OCCUPATION,
        HUR.GRANDFATHER_NAME,
        HUR.GRANDFATHER_NATIONALITY,
        HUR.SPOUSE_NAME,
        HUR.SPOUSE_NATIONALITY,
        HUR.MARITAL_STATUS,
        HUR.DISABILITY,
        HUR.BLOOD_GROUP,
        HUA.PER_PROVINCE_ID,
       HUA.PER_DISTRICT_ID,
       HUA.PER_VDC_ID,
       HUA.PER_WARD_NO,
       HUA.PER_TOLE,
       HUA.MAIL_PROVINCE_ID,
       HUA.MAIL_DISTRICT_ID,
       HUA.MAIL_VDC_ID,
       HUA.MAIL_WARD_NO,
       HUA.MAIL_TOLE,
       HUA.MAIL_PROVINCE_ID,
       HUA.MAIL_HOUSE_NO,
       HUA.PER_HOUSE_NO,
       HRIS_REC_VACANCY_APPLICATION.APPLICATION_TYPE,
       HRIS_REC_VACANCY.SERVICE_TYPES_ID,HRIS_REC_VACANCY.DEPARTMENT_ID,HRIS_REC_VACANCY.POSITION_ID,HRIS_REC_VACANCY.LEVEL_ID,HRIS_REC_VACANCY.SERVICE_EVENTS_ID
       
   FROM HRIS_REC_APPLICATION_PERSONAL 
   LEFT JOIN HRIS_REC_VACANCY_APPLICATION ON HRIS_REC_APPLICATION_PERSONAL.APPLICATION_ID = HRIS_REC_VACANCY_APPLICATION.APPLICATION_ID 
   LEFT JOIN HRIS_REC_VACANCY_USERS ON HRIS_REC_APPLICATION_PERSONAL.USER_ID = HRIS_REC_VACANCY_USERS.USER_ID 
   LEFT JOIN HRIS_REC_USERS_REGISTRATION HUR ON HRIS_REC_APPLICATION_PERSONAL.USER_ID = HUR.USER_ID 
   LEFT JOIN HRIS_REC_USERS_ADDRESS HUA ON HRIS_REC_VACANCY_APPLICATION.USER_ID = HUA.USER_ID 
   LEFT JOIN HRIS_REC_VACANCY ON HRIS_REC_VACANCY_APPLICATION.AD_NO = HRIS_REC_VACANCY.VACANCY_ID 
   WHERE HRIS_REC_APPLICATION_PERSONAL.APPLICATION_ID = {$id} AND HRIS_REC_APPLICATION_PERSONAL.STATUS = 'E' ";
        $result =  $this->rawQuery($sql);
        $personal_id = $result[0]['PERSONAL_ID'];
       
        $code = substr($result[0]['FIRST_NAME'], 0, 3);
        $inc = 'Y';
        if ($result[0]['INCLUSION_ID'] != null) {
            $inc = 'N';
        }
        
        $array = [
            'EMPLOYEE_ID' => ((int) Helper::getMaxId($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID)) + 1,
            'COMPANY_ID' => 1,
            'EMPLOYEE_CODE' => rand(0,100000).$code,
            'FIRST_NAME' => $result[0]['FIRST_NAME'],
            'MIDDLE_NAME' => $result[0]['MIDDLE_NAME'],
            'LAST_NAME' => $result[0]['LAST_NAME'],
            'GENDER_ID' => $result[0]['GENDER_ID'],
            'BIRTH_DATE' => $result[0]['DOB'],
            'BLOOD_GROUP_ID' => $result[0]['BLOOD_GROUP'],
            'RELIGION_ID' => 1,
            'ETHNICITY_ID' => 9,
            'TELEPHONE_NO' => $result[0]['PHONE_NO'],
            'MOBILE_NO' => $result[0]['MOBILE_NO'],
            'EMAIL_PERSONAL' => $result[0]['EMAIL_ID'],
            'ADDR_PERM_HOUSE_NO' => $result[0]['PER_HOUSE_NO'],
            'ADDR_PERM_WARD_NO' => $result[0]['PER_WARD_NO'],
            'ADDR_PERM_STREET_ADDRESS' => $result[0]['PER_TOLE'],
            'ADDR_PERM_VDC_MUNICIPALITY_ID' => $result[0]['PER_VDC_ID'],
            'ADDR_TEMP_HOUSE_NO' => $result[0]['MAIL_HOUSE_NO'],
            'ADDR_TEMP_WARD_NO' => $result[0]['MAIL_WARD_NO'],
            'ADDR_TEMP_STREET_ADDRESS' => $result[0]['MAIL_TOLE'],
            'ADDR_TEMP_VDC_MUNICIPALITY_ID' => $result[0]['MAIL_VDC_ID'],
            'FAM_FATHER_NAME' => $result[0]['FATHER_NAME'],
            'FAM_MOTHER_NAME' => $result[0]['MOTHER_NAME'],
            'FAM_GRAND_FATHER_NAME' => $result[0]['GRANDFATHER_NAME'],
            'MARITAL_STATUS' => 'M',
            'FAM_SPOUSE_NAME' => $result[0]['SPOUSE_NAME'],
            'ID_CITIZENSHIP_NO' => $result[0]['CITIZENSHIP_NO'],
            'ID_CITIZENSHIP_ISSUE_DATE' => $result[0]['CTZ_ISSUE_DATE'],
            'ID_CITIZENSHIP_ISSUE_PLACE' => "XRDCTFGVHJBJKNM",
            'STATUS' => 'E',
            'SERVICE_EVENT_TYPE_ID' => $result[0]['SERVICE_EVENTS_ID'],
            'SERVICE_TYPE_ID' => $result[0]['SERVICE_TYPES_ID'],
            'POSITION_ID' => $result[0]['POSITION_ID'],
            'DESIGNATION_ID' => 10,
            'JOIN_DATE' => date('Y-m-d'),
            'DEPARTMENT_ID' => $result[0]['DEPARTMENT_ID'],
            'RETIRED_FLAG' => 'N',
            'CREATED_BY' => 195,
            'FULL_NAME' =>$result[0]['FIRST_NAME'].' '.$result[0]['MIDDLE_NAME'].' '.$result[0]['LAST_NAME'],
            'ADDR_PERM_DISTRICT_ID' => $result[0]['PER_DISTRICT_ID'],
            'ADDR_TEMP_DISTRICT_ID' => $result[0]['MAIL_DISTRICT_ID'],
            'FUNCTIONAL_LEVEL_ID' => $result[0]['LEVEL_ID'],
            'ACTING_FUNCTIONAL_LEVEL_ID' => $result[0]['LEVEL_ID'],
            'ACTING_POSITION_ID' => $result[0]['POSITION_ID'],
            'RECRUITMENT' => 'OPEN',

        ];
        $eid = $array['EMPLOYEE_ID'];
        $this->documentEmployeeTable->insert($array);
        $query = "SELECT * FROM HRIS_EMPLOYEE_FILE_SETUP";
        $fileSetups =  $this->rawQuery($query);
        $sql = "SELECT * FROM HRIS_REC_APPLICATION_DOCUMENTS WHERE APPLICATION_ID = {$id}";
        $result1 =  $this->rawQuery($sql);
        
        $ffb_id=[];
        $f_id = '';
        $p_id = '';
        foreach ($result1 as $value) {
            if ($value['DOC_FOLDER'] == 'nagrita_front' || $value['DOC_FOLDER'] == 'nagrita_back') {
                $data = [
                            'FILE_CODE' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1,
                            'EMPLOYEE_ID' => $array['EMPLOYEE_ID'],
                            'FILE_PATH' => $value['DOC_NEW_NAME'].'.'.$value['DOC_TYPE'],
                            'STATUS'    => 'E',
                            'CREATED_DT' =>  date('Y-m-d'),
                            'FILE_ID' => 1,
                           
                        ];
                $this->documentEmployeeFileTable->insert($data);
            }elseif ($value['DOC_FOLDER'] == 'photograph') {
                $data = array(
                            'FILE_CODE' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1,
                            'EMPLOYEE_ID' => $array['EMPLOYEE_ID'],
                            'FILE_PATH' => $value['DOC_NEW_NAME'].'.'.$value['DOC_TYPE'],
                            'STATUS'    => 'E',
                            'CREATED_DT' =>  date('Y-m-d'),
                            'FILE_ID' => 2,
                           );
                        $this->documentEmployeeFileTable->insert($data);
                        $p_id = $data['FILE_CODE'];
                        $pdata = array(
                            'PROFILE_PICTURE_ID' => $p_id,
                        );
                        $eid = $array['EMPLOYEE_ID'];
                        $this->documentEmployeeTable->update($pdata, [HrEmployees::EMPLOYEE_ID => $eid]);
            }else {
                foreach ($fileSetups as $filesetup) {
                    if ($filesetup['FILE_NAME'] == $value['DOC_FOLDER']) {
                        $f_id = $filesetup['FILE_ID'];
                        $data = [
                                    'FILE_CODE' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1,
                                    'EMPLOYEE_ID' => $array['EMPLOYEE_ID'],
                                    'FILETYPE_CODE' => $filesetup['FILE_TYPE_CODE'],
                                    'FILE_PATH' => $value['DOC_NEW_NAME'].'.'.$value['DOC_TYPE'],
                                    'STATUS'    => 'E',
                                    'CREATED_DT' =>  date('Y-m-d'),
                                    'FILE_ID' => $f_id,
                                   
                                ];
                                $this->documentEmployeeFileTable->insert($data);
                    }
                } 
            } 
           
        }
        $sql = "SELECT * FROM HRIS_REC_APPLICATION_EDUCATION WHERE APPLICATION_ID = {$id}";
        
        $result =  $this->rawQuery($sql);
        
        foreach ($result as $value) {
             $edudata = array(
                'ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_QUALIFICATIONS', 'ID')) + 1,
                'EMPLOYEE_ID' =>$array['EMPLOYEE_ID'],
                'ACADEMIC_DEGREE_ID' => $value['LEVEL_ID'],
                'ACADEMIC_PROGRAM_ID' => $value['MAJOR_SUBJECT'],
                'ACADEMIC_COURSE_ID' => $value['FACALTY'],
                'PASSED_YR' => $value['PASSED_YEAR'],
                'ACADEMIC_UNIVERSITY_ID' => $value['UNIVERSITY_BOARD'],
                'RANK_TYPE' => $value['RANK_TYPE'],
                'RANK_VALUE' => $value['RANK_VALUE'],
                'CREATED_DT' => date('Y-m-d'),
                'STATUS' => 'E',
             );

             $this->educationDocument->insert($edudata);
        }
            
        $sql = "SELECT * FROM HRIS_REC_APPLICATION_EXPERIENCES WHERE APPLICATION_ID = {$id}";
        $result =  $this->rawQuery($sql);
        foreach ($result as $value) {
            $expdata = array(
               'ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_EXPERIENCES', 'ID')) + 1,
                'EMPLOYEE_ID'=> $array['EMPLOYEE_ID'],
               'ORGANIZATION_NAME' => $value['ORGANISATION_NAME'],
               'POSITION' => $value['POST_NAME'],
               'FROM_DATE' => $value['FROM_DATE'],
               'TO_DATE' => $value['TO_DATE'],
               'CREATED_DATE' => date('Y-m-d'),
               'STATUS' => 'E',
            );

            $this->expDocument->insert($expdata);
       }

       $status = array(
           'STATUS' => 'D',
       );
       
       $this->empPersonal->update($status, [RecruitmentPersonal::PERSONAL_ID => $personal_id]);
       return true;
    }
}