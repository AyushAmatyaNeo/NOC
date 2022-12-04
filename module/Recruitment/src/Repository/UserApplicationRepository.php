<?php
namespace Recruitment\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Recruitment\Model\UserApplicationModel;
use Recruitment\Model\HrisRecApplicationStage;
use Zend\Db\Sql\Sql;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

class UserApplicationRepository extends HrisRepository{
    public function __construct(AdapterInterface $adapter, $tableName = null) 
    {
        parent::__construct($adapter, UserApplicationModel::TABLE_NAME);
        $this->recAppStageRepo = new TableGateway(HrisRecApplicationStage::TABLE_NAME, $adapter);
    }
    public function getFilteredRecords($search)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.APPLICATION_ID AS APPLICATION_ID"),
            new Expression("REC.USER_ID AS USER_ID"),
            new Expression("REC.AD_NO AS AD_NO"),
            new Expression("REC.REGISTRATION_NO AS REGISTRATION_NO"),
            new Expression("REC.STAGE_ID AS STAGE_ID"),
            new Expression("REC.APPLICATION_AMOUNT AS APPLICATION_AMOUNT"),
            //Vacancy_users
            new Expression("AU.FIRST_NAME AS FIRST_NAME"),
            new Expression("AU.MIDDLE_NAME AS MIDDLE_NAME"),
            new Expression("AU.LAST_NAME AS LAST_NAME"),
            new Expression("AU.MOBILE_NO AS MOBILE_NO"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),
            ], true);

        $select->from(['REC' => UserApplicationModel::TABLE_NAME])
                ->join(['AP' => 'HRIS_REC_APPLICATION_PERSONAL'],'AP.APPLICATION_ID=REC.APPLICATION_ID', 'USER_ID', 'left')
                ->join(['AU' => 'HRIS_REC_VACANCY_USERS'],'AU.USER_ID=REC.USER_ID', 'USER_ID', 'left')
                ->where(["REC.STATUS='D'"]);
        $select->order("REC.APPLICATION_ID ASC");
        $boundedParameter = [];
        
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function VacancyDataById($id){
        // print_r($id); die();
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            // new Expression("REC.APPLICATION_ID      AS APPLICATION_ID"),
            new Expression("REC.AD_NO                  AS AD_NO"),
            new Expression("OPN.OPENING_NO             AS OPENING_ID"),
            new Expression("bs_date(OPN.END_DATE)      AS END_DATE"),
            new Expression("OPN.END_DATE               AS END_DATE_AD"),
            new Expression("bs_date(OPN.EXTENDED_DATE) AS EXTENDED_DATE"),
            new Expression("OPN.EXTENDED_DATE          AS EXTENDED_DATE_AD"),
            new Expression("REC.SKILL_ID               AS SKILL_ID"),
            new Expression("REC.INCLUSION_ID           AS INCLUSION_ID"),
            new Expression("REC.VACANCY_TYPE           AS VACANCY_TYPE"),
            new Expression("LVL.FUNCTIONAL_LEVEL_NO    AS LEVEL_ID"),
            new Expression("SER.SERVICE_TYPE_NAME      AS SERVICE_TYPE_ID"),
            new Expression("SEV.SERVICE_EVENT_NAME     AS SERVICE_EVENTS_ID"),
            new Expression("DES.DESIGNATION_TITLE      AS POSITION_ID"),
            new Expression("REC.QUALIFICATION_ID       AS QUALIFICATION_ID"),
            new Expression("REC.EXPERIENCE             AS EXPERIENCE"),
            new Expression("DEP.DEPARTMENT_NAME        AS DEPARTMENT_ID"),
            new Expression("STG.STAGE_EDESC            AS STAGE_ID"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),      
            new Expression("REC.VACANCY_TYPE            AS VACANCY_TYPE"),
            new Expression("bs_date(OPN.START_DATE)    AS START_DATE"),
            new Expression("OPN.START_DATE            AS START_DATE_AD"),
            new Expression("REC.EXPERIENCE            AS EXPERIENCE"),     
            new Expression("APP.REGISTRATION_NO            AS REGISTRATION_NO"), 
            ], true);

        $select->from(['REC' => 'HRIS_REC_VACANCY'])
                ->join(['APP' => 'HRIS_REC_VACANCY_APPLICATION'],'REC.VACANCY_ID=APP.AD_NO', 'STATUS', 'left')
                ->join(['LVL' => 'HRIS_FUNCTIONAL_LEVELS'],'LVL.FUNCTIONAL_LEVEL_ID=REC.LEVEL_ID', 'STATUS', 'left')
                ->join(['SER' => 'HRIS_SERVICE_TYPES'],'SER.SERVICE_TYPE_ID=REC.SERVICE_TYPES_ID', 'STATUS', 'left')
                ->join(['SEV' => 'HRIS_REC_SERVICE_EVENTS_TYPES'],'SEV.SERVICE_EVENT_ID=REC.SERVICE_EVENTS_ID', 'STATUS','left')
                ->join(['OPN' => 'HRIS_REC_OPENINGS'],'OPN.OPENING_ID=REC.OPENING_ID', 'STATUS', 'left')
                ->join(['DEP' => 'HRIS_DEPARTMENTS'],'DEP.DEPARTMENT_ID=REC.DEPARTMENT_ID', 'status', 'left') 
                ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=REC.POSITION_ID', 'status', 'left')
                ->join(['VSTG' => 'HRIS_REC_VACANCY_STAGES'],'VSTG.VACANCY_ID=REC.VACANCY_ID', 'STATUS', 'left')
                ->join(['STG' => 'HRIS_REC_STAGES'],'STG.REC_STAGE_ID=VSTG.REC_STAGE_ID', 'STATUS', 'left')    
                ->where(["REC.STATUS='E'"]);
            if(!empty($id)){
                $select->Where("APP.APPLICATION_ID = $id");
            }
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        // print_r($statement->getSql()); die();
        return $result;
    }
    public function applicationData($search, $emp, $allow_stage){
        $vacancyIdsSql = "select vacancy_ids, access_as from HRIS_REC_EMPLOYEE_STAGE_PERMISSION where employee_id = $emp";
        $vacancyIds = $this->rawQuery($vacancyIdsSql);
        if($vacancyIds){
            $vacancyIdsCsv = $vacancyIds[0]['VACANCY_IDS'];

        }else{
            $vacancyIdsCsv = '0';
        }


        // print_r($vacancyIds);die;
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            // new Expression("REC.PERSONAL_ID            AS PERSONAL_ID"),
            new Expression("REC.APPLICATION_ID         AS APPLICATION_ID"),
            // new Expression("REC.USER_ID                AS USER_ID"),
            new Expression("UR.MARITAL_STATUS         AS MARITAL_STATUS"),
            new Expression("UR.EMPLOYMENT_STATUS      AS EMPLOYMENT_STATUS"),
            // new Expression("UR.EMPLOYMENT_INPUT      AS EMPLOYMENT_INPUT"),
            new Expression("UR.DISABILITY             AS DISABILITY"),
            // new Expression("UR.DISABILITY_INPUT       AS DISABILITY_INPUT"),
            new Expression("REC.SKILL_ID               AS SKILL_ID"),
            new Expression( "UR.RELIGION   AS  RELIGION "),      
            new Expression( "UR.RELIGION_INPUT   AS  RELIGION_INPUT "),      
            new Expression( "UR.REGION   AS  REGION "),      
            new Expression( "UR.REGION_INPUT   AS  REGION_INPUT "),      
            new Expression( "UR.ETHNIC_NAME   AS  ETHNIC_NAME "),      
            new Expression( "UR.ETHNIC_INPUT   AS  ETHNIC_INPUT "),      
            // new Expression( "UR.MOTHER_TONGUE   AS  MOTHER_TONGUE "),      
            new Expression( "UR.CITIZENSHIP_NO   AS  CITIZENSHIP_NO "),    
            new Expression( "UR.CTZ_ISSUE_DATE   AS  CTZ_ISSUE_DATE "),      
            new Expression( "HRD.DISTRICT_NAME   AS  CTZ_ISSUE_DISTRICT_ID "),   
            new Expression( "UR.DOB   AS  DOB "),      
            new Expression( "UR.AGE   AS  AGE "),      
            new Expression( "UR.PHONE_NO   AS  PHONE_NO "),      
            new Expression( "UR.GENDER_ID   AS  GENDER_ID "),        
            new Expression( "UR.SPOUSE_NAME   AS  SPOUSE_NAME "),      
            new Expression( "UR.FATHER_NAME   AS  FATHER_NAME "),      
            new Expression( "UR.GRANDFATHER_NAME   AS  GRANDFATHER_NAME "),      
            new Expression( "UR.MOTHER_NAME   AS  MOTHER_NAME "),
            new Expression("(CASE WHEN UR.IN_SERVICE= 'Y' THEN 'Yes' ELSE 'No' END) AS NOC"),

            // new Expression( "UR.SPOUSE_NATIONALITY   AS  SPOUSE_NATIONALITY "),      
            new Expression( "UR.PROFILE_STATUS   AS  PROFILE_STATUS "),
            new Expression("UN.FIRST_NAME    AS FIRST_NAME "),
            new Expression("UN.MIDDLE_NAME    AS MIDDLE_NAME "),
            new Expression("UN.LAST_NAME    AS LAST_NAME "),
            new Expression("UN.FIRST_NAME || ' ' || ifnull(UN.MIDDLE_NAME,'') || ' ' || UN.LAST_NAME    AS FULL_NAME "),
            new Expression("UN.MOBILE_NO    AS MOBILE_NO "),
            new Expression("UN.EMAIL_ID    AS EMAIL_ID "),
            new Expression("UN.USERNAME    AS USERNAME "),
            new Expression("VAC.AD_NO  AS AD_NO "),
            new Expression("UVA.REGISTRATION_NO    AS REGISTRATION_NO "),
            new Expression("VS.STAGE_EDESC   AS STAGE_ID "),
            new Expression("UVA.APPLICATION_AMOUNT AS APPLICATION_AMOUNT "),
            new Expression("SER.SERVICE_TYPE_NAME        AS SERVICE_TYPE_ID"),
            new Expression("SEV.SERVICE_EVENT_NAME      AS SERVICE_EVENTS_ID"),
            new Expression("DES.DESIGNATION_TITLE            AS POSITION_ID"),
            new Expression("DEP.DEPARTMENT_NAME          AS DEPARTMENT_ID"),
            new Expression("VAC.VACANCY_TYPE          AS VACANCY_TYPE"),
            new Expression("(select doc_path from HRIS_REC_APPLICATION_DOCUMENTS where application_id = REC.APPLICATION_ID and DOC_FOLDER = 'photograph') as PROFILE_IMG"),

            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),      
            // new Expression("HRIS_REC_PAYMENT_STATUS(UVA.PAYMENT_PAID,UVA.PAYMENT_VERIFIED) as PAYMENT_STATUS"),
            new Expression("(CASE WHEN UVA.PAYMENT_PAID = 'Y' THEN 'Paid' ELSE 'Unpaid' END) AS PAYMENT"),
            new Expression("(CASE WHEN UVA.PAYMENT_VERIFIED = 'Y' THEN 'Verified' ELSE 'Unverified' END) AS PAYMENT_STATUS"),
            new Expression("TO_INT(SUBSTR_BEFORE(VAC.AD_NO,'/')) as AD_NO_ORDER"),
            new Expression("UVA.CREATED_DATE as APPLIED_DATE_AD"),
            new Expression("BS_DATE(UVA.CREATED_DATE) as APPLIED_DATE_BS"),
            new Expression("HRIS_REC_MULTIPLE_AD_NO(UVA.APPLICATION_ID) as MULTIPLE_AD_NO"),
            new Expression("GET_AD_NO(VAC.OPENING_ID,VAC.VACANCY_NO) as VACANCY_AD_NO"),
            ], true);

            $select->from(['REC' => 'HRIS_REC_APPLICATION_PERSONAL'])
                ->join(['UR' => 'HRIS_REC_USERS_REGISTRATION'],'UR.USER_ID=REC.USER_ID', 'STATUS', 'left')
                ->join(['G' => 'HRIS_GENDERS'],'G.GENDER_ID=UR.GENDER_ID', 'STATUS', 'left')
                ->join(['UN' => 'HRIS_REC_VACANCY_USERS'],'UN.USER_ID=REC.USER_ID', 'RESET_STATUS', 'left')
                ->join(['UVA' => 'HRIS_REC_VACANCY_APPLICATION'],'UVA.APPLICATION_ID=REC.APPLICATION_ID', 'APPLICATION_ID', 'left')
                ->join(['HRD' => 'HRIS_DISTRICTS'],'HRD.DISTRICT_ID=UR.CTZ_ISSUE_DISTRICT_ID', 'ZONE_ID', 'left')
                ->join(['VAC' => 'HRIS_REC_VACANCY'],'VAC.VACANCY_ID=UVA.AD_NO', 'status', 'left')
                ->join(['VS' => 'HRIS_REC_STAGES'],'VS.REC_STAGE_ID=UVA.STAGE_ID', 'status', 'left')  
                ->join(['SER' => 'HRIS_SERVICE_TYPES'],'SER.SERVICE_TYPE_ID=VAC.SERVICE_TYPES_ID', 'STATUS', 'left')
                ->join(['SEV' => 'HRIS_REC_SERVICE_EVENTS_TYPES'],'SEV.SERVICE_EVENT_ID=VAC.SERVICE_EVENTS_ID', 'STATUS','left')  
                ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=VAC.POSITION_ID', 'status', 'left')  
                ->join(['DEP' => 'HRIS_DEPARTMENTS'],'DEP.DEPARTMENT_ID=VAC.DEPARTMENT_ID', 'status', 'left') 
                ->join(['ESP' => 'HRIS_REC_EMPLOYEE_STAGE_PERMISSION'],'ESP.EMPLOYEE_ID='.$emp, 'status', 'left') 
                ->where(["REC.STATUS='E'"])
                ->where(["UVA.APPLICATION_TYPE = 'OPEN' and VAC.vacancy_id in ($vacancyIdsCsv)"])
                ->where(["UVA.STAGE_ID IN ($allow_stage)"]);
                // ->where(["UVA.is_verified= (case when ESP.access_as = 'A' then 'Y' else 'N' end)"]);
                // ->where(["DOC.DOC_FOLDER = 'photograph'"]);        
            if (($search['OpeningNo'] != null)) {
                $select->where([
                    "VAC.OPENING_ID" => $search['OpeningNo']
                ]);
            }
            if (($search['adnumberId'] != null)) {
                $select->where([
                    "VAC.VACANCY_ID" => $search['adnumberId']
                ]);
            }
            if (($search['appliedadnumber'] != null)) {
                $appliedAdNoCondition = '( 1 = 2';
                foreach($search['appliedadnumber'] as $appliedAdNO){
                    $appliedAdNoCondition .= " OR (HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) = '{$appliedAdNO}' OR HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) like '% , '||'{$appliedAdNO}'||'%'  OR HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) like '{$appliedAdNO}'||' , %' OR HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) like '% , ' || '{$appliedAdNO}'||' , %')";
                }
                $appliedAdNoCondition .= ' )';
                $select->where([
                    $appliedAdNoCondition
                ]);
            }
            if (($search['department'] != null)) {
                $select->where([
                    "DEP.DEPARTMENT_ID" => $search['department']
                ]);
            }
            if (($search['designation'] != null)) {
                $select->where([
                    "DES.DESIGNATION_ID" => $search['designation']
                ]);
            }
            if (($search['gender'] != null)) {
                $select->where([
                    "G.GENDER_ID" => $search['gender']
                ]);
            }
            if (($search['stageId'] != null)) {
                $select->where([
                    "UVA.STAGE_ID" => $search['stageId']
                ]);
            }
            if (($search['vacancy_type'] != null)) {
                $select->where([
                    "VAC.VACANCY_TYPE" => $search['vacancy_type']
                ]);
            }
            if (($search['paymentPaid'] != null)) {
                $select->where([
                    "UVA.PAYMENT_PAID" => $search['paymentPaid']
                ]);
            }
            if (($search['paymentVerified'] != null)) {
                $select->where([
                    "UVA.PAYMENT_VERIFIED" => $search['paymentVerified']
                ]);
            }
        $select->order("AD_NO_ORDER ASC");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function applicationDataInternal($search, $emp, $allow_stage){
        $vacancyIdsSql = "select vacancy_ids from HRIS_REC_EMPLOYEE_STAGE_PERMISSION where employee_id = $emp";
        $vacancyIds = $this->rawQuery($vacancyIdsSql);
        if($vacancyIds){
            $vacancyIdsCsv = $vacancyIds[0]['VACANCY_IDS'];
        }else{
            $vacancyIdsCsv = '0';
        }
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            // new Expression("REC.PERSONAL_ID            AS PERSONAL_ID"),
            new Expression("REC.APPLICATION_ID         AS APPLICATION_ID"),
            // new Expression("REC.USER_ID                AS USER_ID"),
            new Expression("(CASE WHEN UR.MARITAL_STATUS= 'M' THEN 'Married' ELSE 'Unmarried' END) AS MARITAL_STATUS"), 
            new Expression("(CASE WHEN UR.EMPLOYEE_TYPE = 'R' THEN 'Regular' ELSE 'Contract' END) AS EMPLOYMENT_STATUS"),
            new Expression("UR.DISABLED_FLAG           AS DISABILITY"),
            // new Expression("UR.DISABILITY_INPUT       AS DISABILITY_INPUT"),
            // new Expression("REC.SKILL_ID               AS SKILL_ID"),
            // new Expression( "UR.RELIGION   AS  RELIGION "),      
            // new Expression( "UR.RELIGION_INPUT   AS  RELIGION_INPUT "),      
            // new Expression( "UR.REGION   AS  REGION "),      
            // new Expression( "UR.REGION_INPUT   AS  REGION_INPUT "),      
            // new Expression( "UR.ETHNIC_NAME   AS  ETHNIC_NAME "),      
            // new Expression( "UR.ETHNIC_INPUT   AS  ETHNIC_INPUT "),      
            // new Expression( "UR.MOTHER_TONGUE   AS  MOTHER_TONGUE "),      
            new Expression( "UR.ID_CITIZENSHIP_NO   AS  CITIZENSHIP_NO"),      
            new Expression( "UR.ID_CITIZENSHIP_ISSUE_DATE   AS  CTZ_ISSUE_DATE"),     
            new Expression( "UR.ID_CITIZENSHIP_ISSUE_PLACE  AS  CTZ_ISSUE_DISTRICT_ID "),      
            new Expression( "UR.BIRTH_DATE   AS  DOB "),      
            // new Expression( "UR.AGE   AS  AGE "),      
            // new Expression( "UR.PHONE_NO   AS  PHONE_NO "),      
            new Expression( "UR.GENDER_ID   AS  GENDER_ID "),        
            new Expression("UR.FIRST_NAME    AS FIRST_NAME "),
            new Expression("ifnull(UR.MIDDLE_NAME,'')    AS MIDDLE_NAME "),
            new Expression("UR.LAST_NAME    AS LAST_NAME "),
            new Expression("UR.FULL_NAME    AS FULL_NAME "),
            new Expression("UR.MOBILE_NO    AS MOBILE_NO "),
            new Expression( "UR.FAM_FATHER_NAME   AS  FATHER_NAME "),      
            new Expression( "UR.FAM_GRAND_FATHER_NAME   AS  GRANDFATHER_NAME "),      
            new Expression( "UR.FAM_MOTHER_NAME   AS  MOTHER_NAME "),
            // new Expression("UR.EMAIL_ID    AS EMAIL_ID "),
            // new Expression("UR.USERNAME    AS USERNAME "),
            new Expression("UR.EMPLOYEE_CODE        AS EMPLOYEE_CODE"),
            new Expression(" VAC.AD_NO  AS AD_NO "),
            new Expression(" REC.REGISTRATION_NO    AS REGISTRATION_NO "),
            new Expression(" VS.STAGE_EDESC   AS STAGE_ID "),
            new Expression(" REC.APPLICATION_AMOUNT AS APPLICATION_AMOUNT "),
            new Expression("SER.SERVICE_TYPE_NAME        AS SERVICE_TYPE_ID"),
            new Expression("SEV.SERVICE_EVENT_NAME      AS SERVICE_EVENTS_ID"),
            new Expression("DES.DESIGNATION_TITLE            AS POSITION_ID"),
            new Expression("DEP.DEPARTMENT_NAME          AS DEPARTMENT_ID"),
            new Expression("VAC.VACANCY_TYPE          AS VACANCY_TYPE"),
            new Expression("UD.DESIGNATION_TITLE         AS DESIGNATION_TITLE"),
            // DOCUMENT
            // new Expression(" DOC.DOC_PATH AS PROFILE_IMG "),
            new Expression("EF.FILE_PATH AS PROFILE_IMG"),
            
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"), 
            // new Expression("HRIS_REC_PAYMENT_STATUS(REC.PAYMENT_PAID,REC.PAYMENT_VERIFIED) as PAYMENT_STATUS"),
            new Expression("(CASE WHEN REC.PAYMENT_PAID = 'Y' THEN 'Paid' ELSE 'Unpaid' END) AS PAYMENT"),
            new Expression("(CASE WHEN REC.PAYMENT_VERIFIED = 'Y' THEN 'Verified' ELSE 'Unverified' END) AS PAYMENT_STATUS"),
            new Expression("TO_INT(SUBSTR_BEFORE(VAC.AD_NO,'/')) as AD_NO_ORDER "),
            new Expression("REC.CREATED_DATE as APPLIED_DATE_AD"),
            new Expression("BS_DATE(REC.CREATED_DATE) as APPLIED_DATE_BS"),
            new Expression("HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) as MULTIPLE_AD_NO"),
            new Expression("GET_AD_NO(VAC.OPENING_ID,VAC.VACANCY_NO) as VACANCY_AD_NO"),
            ], true);

        $select->from(['REC' => 'HRIS_REC_VACANCY_APPLICATION'])
                ->join(['HRUS' => 'HRIS_USERS'],'HRUS.USER_ID=REC.USER_ID', 'STATUS', 'left')
                ->join(['UR' => 'HRIS_EMPLOYEES'],'UR.EMPLOYEE_ID = HRUS.EMPLOYEE_ID', 'STATUS', 'left')
                ->join(['G' => 'HRIS_GENDERS'],'G.GENDER_ID = UR.GENDER_ID', 'STATUS', 'left')
                ->join(['UD' => 'HRIS_DESIGNATIONS'],'UD.DESIGNATION_ID = UR.DESIGNATION_ID', 'STATUS', 'left')
                // ->join(['UN' => 'HRIS_REC_VACANCY_USERS'],'UN.USER_ID=REC.USER_ID', 'RESET_STATUS', 'left')
                // ->join(['UVA' => 'HRIS_REC_VACANCY_APPLICATION'],'UVA.APPLICATION_ID=REC.APPLICATION_ID', 'APPLICATION_ID', 'left')
                // ->join(['HRD' => 'HRIS_DISTRICTS'],'HRD.DISTRICT_ID=UR.CTZ_ISSUE_DISTRICT_ID', 'status', 'left')
                // ->join(['DOC' => 'HRIS_REC_APPLICATION_DOCUMENTS'],'DOC.APPLICATION_ID=REC.APPLICATION_ID', 'USER_ID', 'left')
                ->join(['VAC' => 'HRIS_REC_VACANCY'],'VAC.VACANCY_ID=REC.AD_NO', 'status', 'left')
                ->join(['VS' => 'HRIS_REC_STAGES'],'VS.REC_STAGE_ID=REC.STAGE_ID', 'status', 'left')  
                ->join(['SER' => 'HRIS_SERVICE_TYPES'],'SER.SERVICE_TYPE_ID=VAC.SERVICE_TYPES_ID', 'STATUS', 'left')
                ->join(['SEV' => 'HRIS_REC_SERVICE_EVENTS_TYPES'],'SEV.SERVICE_EVENT_ID=VAC.SERVICE_EVENTS_ID', 'STATUS','left')  
                ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=VAC.POSITION_ID', 'status', 'left')
                ->join(['EF' => 'HRIS_EMPLOYEE_FILE'],'EF.FILE_CODE=UR.PROFILE_PICTURE_ID', 'STATUS', 'left')
                ->join(['DEP' => 'HRIS_DEPARTMENTS'],'DEP.DEPARTMENT_ID=VAC.DEPARTMENT_ID', 'status', 'left')
                ->join(['ESP' => 'HRIS_REC_EMPLOYEE_STAGE_PERMISSION'],'ESP.EMPLOYEE_ID='.$emp, 'status', 'left') 
                // ->where(["REC.is_verified= (case when ESP.access_as = 'A' then 'Y' else 'N' end)"]) 
                ->where(["REC.STAGE_ID IN ($allow_stage)"])
                ->where(["REC.STATUS='E' and VAC.vacancy_id in ($vacancyIdsCsv)"]);
                // ->where(["REC.APPLICATION_TYPE = 'INTERNAL' "]);
                
            if (($search['OpeningNo'] != null)) {
                $select->where([
                    "VAC.OPENING_ID" => $search['OpeningNo']
                ]);
            }
            if (($search['adnumberId'] != null)) {
                $select->where([
                    "VAC.VACANCY_ID" => $search['adnumberId']
                ]);
            }
            if (($search['appliedadnumber'] != null)) {
                $appliedAdNoCondition = '( 1 = 2';
                foreach($search['appliedadnumber'] as $appliedAdNO){
                    $appliedAdNoCondition .= " OR (HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) = '{$appliedAdNO}' OR HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) like '% , '||'{$appliedAdNO}'||'%'  OR HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) like '{$appliedAdNO}'||' , %' OR HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) like '% , ' || '{$appliedAdNO}'||' , %')";
                }
                $appliedAdNoCondition .= ' )';
                $select->where([
                    $appliedAdNoCondition
                ]);
            }
            if (($search['department'] != null)) {
                $select->where([
                    "DEP.DEPARTMENT_ID" => $search['department']
                ]);
            }
            if (($search['designation'] != null)) {
                $select->where([
                    "DES.DESIGNATION_ID" => $search['designation']
                ]);
            }
            if (($search['gender'] != null)) {
                $select->where([
                    "G.GENDER_ID" => $search['gender']
                ]);
            }
            if (($search['stageId'] != null)) {
                $select->where([
                    "REC.STAGE_ID" => $search['stageId']
                ]);
            }
            if (($search['vacancy_type'] != null)) {
                $select->where([
                    "VAC.VACANCY_TYPE" => $search['vacancy_type']
                ]);
            }
            if (($search['paymentPaid'] != null)) {
                $select->where([
                    "REC.PAYMENT_PAID" => $search['paymentPaid']
                ]);
            }
            if (($search['paymentVerified'] != null)) {
                $select->where([
                    "REC.PAYMENT_VERIFIED" => $search['paymentVerified']
                ]);
            }

        $select->order("AD_NO_ORDER ASC");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo('<pre>');print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        // print_r($statement->getSql()); die();
        return $result;
    }
    public function applicationDataById($id){
        // print_r($id); die();
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.USER_ID                AS USER_ID"),
            new Expression("REC.APPLICATION_ID         AS APPLICATION_ID"),
            new Expression("UR.MARITAL_STATUS          AS MARITAL_STATUS"),
            new Expression("UR.EMPLOYMENT_STATUS       AS EMPLOYMENT_STATUS"),
            new Expression("UR.EMPLOYMENT_INPUT        AS EMPLOYMENT_INPUT"),
            new Expression("UR.DISABILITY              AS DISABILITY"),
            new Expression("UR.DISABILITY_INPUT        AS DISABILITY_INPUT"),
            new Expression("REC.SKILL_ID                AS SKILL_ID"),
            new Expression("REC.INCLUSION_ID            AS INCLUSION_ID"),
            new Expression("REC.CANCELLED_INCLUSION_ID            AS CANCELLED_INCLUSION_ID"),
            new Expression("UR.RELIGION                 AS  RELIGION "),      
            new Expression("UR.RELIGION_INPUT           AS  RELIGION_INPUT "),      
            new Expression("UR.REGION                   AS  REGION "),      
            new Expression("UR.REGION_INPUT             AS  REGION_INPUT "),      
            new Expression("UR.ETHNIC_NAME              AS  ETHNIC_NAME "),      
            new Expression("UR.ETHNIC_INPUT             AS  ETHNIC_INPUT "),      
            new Expression("UR.MOTHER_TONGUE            AS  MOTHER_TONGUE "),      
            new Expression("UR.CITIZENSHIP_NO           AS  CITIZENSHIP_NO "),      
            new Expression("UR.CTZ_ISSUE_DATE           AS  CTZ_ISSUE_DATE "),      
            new Expression("HRD.DISTRICT_NAME           AS  CTZ_ISSUE_DISTRICT_ID "),
            new Expression("HRD.DISTRICT_ID             AS  CTZ_ISSUE_DISTRICT_O_ID"),     
            new Expression("UR.DOB                      AS  DOB "),
            new Expression("UR.DOB_AD                   AS  DOB_AD "),  
            new Expression("UR.AGE                      AS  AGE "),      
            new Expression("UR.PHONE_NO                 AS  PHONE_NO "),      
            new Expression("UR.GENDER_ID                AS  GENDER_ID "),      
            new Expression("UR.FATHER_NAME              AS  FATHER_NAME "),      
            new Expression("UR.FATHER_QUALIFICATION     AS  FATHER_QUALIFICATION "),      
            new Expression("UR.MOTHER_NAME              AS  MOTHER_NAME "),      
            new Expression("UR.MOTHER_QUALIFICATION     AS  MOTHER_QUALIFICATION "),      
            new Expression("UR.FM_OCCUPATION            AS  FM_OCCUPATION "),      
            new Expression("UR.FM_OCCUPATION_INPUT      AS  FM_OCCUPATION_INPUT "),      
            new Expression("UR.GRANDFATHER_NAME         AS  GRANDFATHER_NAME "),      
            new Expression("UR.GRANDFATHER_NATIONALITY  AS  GRANDFATHER_NATIONALITY "),      
            new Expression("UR.SPOUSE_NAME              AS  SPOUSE_NAME "),      
            new Expression("UR.SPOUSE_NATIONALITY       AS  SPOUSE_NATIONALITY "),      
            new Expression("UR.PROFILE_STATUS           AS  PROFILE_STATUS "),
            new Expression("UR.REGISTRATION_ID          AS  REGISTRATION_ID"),

            new Expression("UN.FIRST_NAME               AS FIRST_NAME "),
            new Expression("UN.MIDDLE_NAME              AS MIDDLE_NAME "),
            new Expression("UN.LAST_NAME                AS LAST_NAME "),
            new Expression("UN.MOBILE_NO                AS MOBILE_NO "),
            new Expression("UN.EMAIL_ID                 AS EMAIL_ID "),
            new Expression("UN.USERNAME                 AS USERNAME "),
            new Expression("UN.USER_ID                  AS USER_ID"),
            new Expression(" UVA.AD_NO                  AS AD_NO "),
            new Expression(" UVA.REGISTRATION_NO        AS REGISTRATION_NO "),
            new Expression(" STG.STAGE_EDESC            AS STAGE_ID "),
            new Expression(" STG.REC_STAGE_ID            AS REC_STAGE_ID "),
            new Expression(" UVA.REMARKS                AS STAGE_REMARKS "),
            new Expression(" UVA.APPLICATION_AMOUNT     AS APPLICATION_AMOUNT "),
            new Expression(" UVA.APPLICATION_TYPE       AS APPLICATION_TYPE "),
            new Expression(" UVA.AD_NO                  AS VACANCY_ID"),
            //Payment detail
            new Expression(" RPG.GATEWAY_COMPANY           AS PAYMENT_TYPE "),
            new Expression(" PAY.PAYMENT_AMOUNT            AS PAYMENT_NPR "),
            // DOCUMENT
            new Expression(" DOC.DOC_PATH               AS PROFILE_IMG "),
            new Expression(" DOC.DOC_FOLDER             AS DOC_FOLDER "),

            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),   
            new Expression("UVA.PAYMENT_PAID as PAYMENT_PAID"),            
            new Expression("UVA.PAYMENT_VERIFIED as PAYMENT_VERIFIED"), 
            new Expression("HRIS_REC_PAYMENT_STATUS(UVA.PAYMENT_PAID,UVA.PAYMENT_VERIFIED) as PAYMENT_STATUS"),
            new Expression(" bs_date(UVA.CREATED_DATE)  AS APPLIED_DATE"),
            new Expression("UVA.CREATED_DATE  AS APPLIED_DATE_AD"),
            new Expression("UR.IN_SERVICE as IS_NOC_EMPLOYEE"),
            new Expression("NOCDOC.DOC_PATH as NOC_DOC_PATH"),
            new Expression("HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) as MULTIPLE_AD_NO"),
            new Expression("GET_AD_NO(VAC.OPENING_ID,VAC.VACANCY_NO) as VACANCY_AD_NO"),
            ], true);

        $select->from(['REC' => 'HRIS_REC_APPLICATION_PERSONAL'])
                ->join(['UR' => 'HRIS_REC_USERS_REGISTRATION'],'UR.USER_ID=REC.USER_ID', 'STATUS', 'left')
                ->join(['UN' => 'HRIS_REC_VACANCY_USERS'],'UN.USER_ID=REC.USER_ID', 'RESET_STATUS', 'left')
                ->join(['UVA' => 'HRIS_REC_VACANCY_APPLICATION'],'UVA.APPLICATION_ID=REC.APPLICATION_ID', 'APPLICATION_ID', 'left')
                ->join(['STG' => 'HRIS_REC_STAGES'],'STG.REC_STAGE_ID=UVA.STAGE_ID', 'STATUS', 'left')
                ->join(['HRD' => 'HRIS_DISTRICTS'],'HRD.DISTRICT_ID=UR.CTZ_ISSUE_DISTRICT_ID', 'ZONE_ID', 'left')
                ->join(['DOC' => 'HRIS_REC_APPLICATION_DOCUMENTS'],'DOC.APPLICATION_ID=REC.APPLICATION_ID', 'DOC_TYPE', 'left')
                ->join(['PAY' => 'HRIS_REC_APPLICATION_PAYMENT'],'PAY.PAYMENT_ID=UVA.PAYMENT_ID', 'PAYMENT_REFERENCE_ID', 'left')
                ->join(['RPG' => 'hris_rec_payment_gateway'],'PAY.PAYMENT_GATEWAY_ID=RPG.ID', 'ID', 'left')
                ->join(['NOCDOC' => 'HRIS_REC_APPLICATION_DOCUMENTS'],"NOCDOC.user_id = UN.user_id and NOCDOC.doc_folder='in_service'", 'DOC_TYPE', 'left')
                ->join(['VAC' => 'HRIS_REC_VACANCY'],"VAC.vacancy_id = UVA.ad_no", 'STATUS', 'left')
                ->where(["REC.STATUS='E'"]);
                //->where(["DOC.DOC_FOLDER = 'photograph'"]);

        $select->Where("REC.APPLICATION_ID = $id");
        $select->order("REC.PERSONAL_ID ASC");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        // print_r($statement->getSql()); die();
        return $result;
    }
    public function applicationDataByIdInternal($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.USER_ID                AS USER_ID"),
            new Expression("REC.APPLICATION_ID         AS APPLICATION_ID"),
            new Expression("UR.MARITAL_STATUS          AS MARITAL_STATUS"),
            new Expression("REC.SKILL_ID                   AS SKILL_ID"),
            new Expression("REC.INCLUSION_ID               AS INCLUSION_ID"),
            new Expression("REC.CANCELLED_INCLUSION_ID               AS CANCELLED_INCLUSION_ID"),
            new Expression("UR.DISABLED_FLAG           AS DISABLED_FLAG"),
            new Expression("URREL.RELIGION_NAME        AS  RELIGION_NAME "),       
            new Expression("URETH.ETHNICITY             AS  ETHNICITY "),       
            new Expression("UR.ID_CITIZENSHIP_NO       AS  CITIZENSHIP_NO "), 
            new Expression("UR.EMPLOYEE_STATUS       AS  EMPLOYEE_STATUS"),   
            new Expression("UR.ID_CITIZENSHIP_ISSUE_DATE AS  CTZ_ISSUE_DATE "),  
            new Expression("UR.ID_CITIZENSHIP_ISSUE_PLACE    AS  CTZ_ISSUE_DISTRICT_ID "),  
            new Expression("UR.DISABLED_FLAG    AS  DISABLED_FLAG "),  
            new Expression("UR.BIRTH_DATE               AS  DOB "),
            new Expression("UR.MOBILE_NO                 AS  MOBILE_NO "),      
            new Expression("UR.GENDER_ID                AS  GENDER_ID "),      
            new Expression("UR.FAM_FATHER_NAME              AS  FAM_FATHER_NAME "),      
            new Expression("UR.FAM_FATHER_OCCUPATION     AS  FAM_FATHER_OCCUPATION "),      
            new Expression("UR.FAM_MOTHER_NAME              AS  FAM_MOTHER_NAME "),      
            new Expression("UR.FAM_MOTHER_OCCUPATION     AS  FAM_MOTHER_OCCUPATION "),      
            new Expression("UR.FAM_GRAND_FATHER_NAME         AS  FAM_GRAND_FATHER_NAME "),      
            new Expression("UR.FAM_SPOUSE_NAME              AS  FAM_SPOUSE_NAME "),     
            new Expression("UR.FIRST_NAME               AS FIRST_NAME "),
            new Expression("UR.MIDDLE_NAME              AS MIDDLE_NAME "),
            new Expression("UR.LAST_NAME                AS LAST_NAME "),
            new Expression("UR.TELEPHONE_NO             AS TELEPHONE_NO "),
            new Expression("UR.EMAIL_PERSONAL            AS EMAIL_PERSONAL"),
            new Expression("UR.EMAIL_OFFICIAL            AS EMAIL_OFFICIAL"),
            new Expression("case when UR.EMAIL_OFFICIAL is null then  UR.EMAIL_PERSONAL else  UR.EMAIL_OFFICIAL end  AS EMAIL_ID"),
            new Expression("UR.ADDR_PERM_WARD_NO            AS ADDR_PERM_WARD_NO"),
            new Expression("UR.ADDR_TEMP_WARD_NO            AS ADDR_TEMP_WARD_NO"),
            new Expression("UR.ADDR_PERM_STREET_ADDRESS            AS ADDR_PERM_STREET_ADDRESS"),
            new Expression("UR.ADDR_TEMP_STREET_ADDRESS            AS ADDR_TEMP_STREET_ADDRESS"),
            new Expression("EMUSER.USER_NAME             AS USER_NAME"),
            new Expression("PERMPROV.PROVINCE_NAME       AS PERM_PROVINCE_NAME"),
            new Expression("TEMPPROV.PROVINCE_NAME       AS TEMP_PROVINCE_NAME"),
            new Expression("PERMZO.ZONE_NAME       AS PERM_ZONE_NAME"),
            new Expression("TEMPZO.ZONE_NAME       AS TEMP_ZONE_NAME"),
            new Expression("PERMDIS.DISTRICT_NAME       AS PERM_DISTRICT_NAME"),
            new Expression("TEMPDIS.DISTRICT_NAME       AS TEMP_DISTRICT_NAME"),
            new Expression("PERMCOU.COUNTRY_NAME       AS TEMP_COUNTRY_NAME"),
            new Expression("TEMPCOU.COUNTRY_NAME       AS TEMP_COUNTRY_NAME"),
            new Expression(" UVA.AD_NO                  AS AD_NO "),
            new Expression(" UVA.REGISTRATION_NO        AS REGISTRATION_NO "),
            new Expression(" STG.STAGE_EDESC            AS STAGE_ID "),
            new Expression(" STG.REC_STAGE_ID            AS REC_STAGE_ID"),
            new Expression(" UVA.REMARKS                AS STAGE_REMARKS "),
            new Expression(" UVA.APPLICATION_AMOUNT     AS APPLICATION_AMOUNT "),
            new Expression("bs_date(UVA.CREATED_DATE)  AS APPLIED_DATE"),
            new Expression("UVA.CREATED_DATE  AS APPLIED_DATE_AD"),
            new Expression("UD.DESIGNATION_TITLE AS DESIGNATION_TITLE"),

            //Payment detail
            new Expression(" PAY.PAYMENT_TYPE           AS PAYMENT_TYPE "),
            new Expression(" PAY.PAYMENT_AMOUNT            AS PAYMENT_NPR "),
            // // DOCUMENT
            // new Expression(" DOC.DOC_PATH               AS PROFILE_IMG "),

            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),  
            new Expression("UVA.PAYMENT_PAID as PAYMENT_PAID"),  
            new Expression("UVA.PAYMENT_VERIFIED as PAYMENT_VERIFIED"),  
            new Expression("HRIS_REC_PAYMENT_STATUS(UVA.PAYMENT_PAID,
            UVA.PAYMENT_VERIFIED) as PAYMENT_STATUS"),  
            new Expression("RPG.GATEWAY_COMPANY AS PAYMENT_TYPE"),  
            new Expression("RPG.ID AS ID"),  
            new Expression("PAY.PAYMENT_REFERENCE_ID AS PAYMENT_REFERENCE_ID"),
            new Expression("POC.FILE_PATH AS PROFILE_IMG"),
            new Expression("HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) as MULTIPLE_AD_NO"),
            new Expression("GET_AD_NO(VAC.OPENING_ID,VAC.VACANCY_NO) as VACANCY_AD_NO"),
            ], true);

        $select->from(['REC' => 'HRIS_REC_APPLICATION_PERSONAL'])
                ->join(['EMUSER' => 'HRIS_USERS'],'EMUSER.USER_ID=REC.USER_ID', 'STATUS', 'left')
                ->join(['UR' => 'HRIS_EMPLOYEES'],'UR.EMPLOYEE_ID=EMUSER.EMPLOYEE_ID', 'STATUS', 'left')
                ->join(['UD' => 'HRIS_DESIGNATIONS'],'UD.DESIGNATION_ID=UR.DESIGNATION_ID', 'STATUS', 'left')
                ->join(['URREL' => 'HRIS_RELIGIONS'],'URREL.RELIGION_ID=UR.RELIGION_ID', 'STATUS', 'left')
                ->join(['URETH' => 'HRIS_ETHNICITIES'],'URETH.ETHNICITY_ID=UR.ETHNICITY_ID', 'STATUS', 'left')
                ->join(['UVA' => 'HRIS_REC_VACANCY_APPLICATION'],'UVA.APPLICATION_ID=REC.APPLICATION_ID', 'APPLICATION_ID', 'left')
                ->join(['STG' => 'HRIS_REC_STAGES'],'STG.REC_STAGE_ID=UVA.STAGE_ID', 'STATUS', 'left')
                ->join(['PAY' => 'HRIS_REC_APPLICATION_PAYMENT'],'PAY.APPLICATION_ID=REC.APPLICATION_ID', 'PAYMENT_REFERENCE_ID', 'left')
                ->join(['DOC' => 'HRIS_EMPLOYEE_FILE'],'DOC.EMPLOYEE_ID=UR.EMPLOYEE_ID', 'STATUS', 'left')
                ->join(['POC' => 'HRIS_EMPLOYEE_FILE'],'POC.FILE_CODE = UR.PROFILE_PICTURE_ID', 'STATUS', 'left')
                ->join(['PERMPROV' => 'HRIS_PROVINCES'],'PERMPROV.PROVINCE_ID=UR.ADDR_PERM_PROVINCE_ID', 'STATUS', 'left')
                ->join(['TEMPPROV' => 'HRIS_PROVINCES'],'TEMPPROV.PROVINCE_ID=UR.ADDR_TEMP_PROVINCE_ID', 'STATUS', 'left')
                ->join(['PERMZO' => 'HRIS_ZONES'],'PERMZO.ZONE_ID=UR.ADDR_PERM_ZONE_ID', 'STATUS', 'left')
                ->join(['TEMPZO' => 'HRIS_ZONES'],'TEMPZO.ZONE_ID=UR.ADDR_TEMP_ZONE_ID', 'STATUS', 'left')
                ->join(['PERMDIS' => 'HRIS_DISTRICTS'],'PERMDIS.DISTRICT_ID=UR.ADDR_PERM_DISTRICT_ID', 'STATUS', 'left')
                ->join(['TEMPDIS' => 'HRIS_DISTRICTS'],'TEMPDIS.DISTRICT_ID=UR.ADDR_TEMP_DISTRICT_ID', 'STATUS', 'left')
                ->join(['PERMCOU' => 'HRIS_COUNTRIES'],'PERMCOU.COUNTRY_ID=UR.ADDR_PERM_COUNTRY_ID', 'COUNTRY_CODE','left')
                ->join(['TEMPCOU' => 'HRIS_COUNTRIES'],'TEMPCOU.COUNTRY_ID=UR.ADDR_TEMP_COUNTRY_ID', 'COUNTRY_CODE', 'left')
                ->join(['RPG' => 'hris_rec_payment_gateway'],'PAY.PAYMENT_GATEWAY_ID=RPG.ID', 'ID', 'left')
                ->join(['VAC' => 'HRIS_REC_VACANCY'],"VAC.vacancy_id = UVA.ad_no", 'STATUS', 'left')
                ->where(["REC.STATUS='E'"]);

        $select->Where("REC.APPLICATION_ID = $id");
        $select->order("REC.PERSONAL_ID ASC");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        // print_r($statement->getSql()); die();
        return $result;
    }

    


    public function applicationSkillsbyId($id){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([                       
            new Expression("REC.SKILL_NAME AS SKILL_ID"),          
            ], true);
        $select->from(['REC' => 'HRIS_REC_SKILL']);
        $select->Where("REC.SKILL_ID = $id");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        // print_r($statement->getSql()); die();
        return $result;
    }
    public function applicationInclusionsbyId($id){
        $inclusionsId = explode(',', $id);
        
        foreach($inclusionsId as $inclusion_id){
            $sql = new Sql($this->adapter);
            $select = $sql->select();
            $select->columns([                       
                new Expression("REC.OPTION_EDESC AS INCLUSION_NAME"),          
                new Expression("REC.OPTION_ID AS INCLUSION_ID"),          
                ], true);
            $select->from(['REC' => 'HRIS_REC_OPTIONS']);
            $select->Where("REC.OPTION_ID = $inclusion_id");
            $boundedParameter = [];
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = Helper::extractDbData($statement->execute());
            $inclusion[] = $result[0];
        }
       return $inclusion;
    }
    public function applicationaddressById($id){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            // new Expression("REC.USER_ID                AS    USER_ID"),     
            // new Expression("REC.APPLICATION_ID         AS    APPLICATION_ID"),
            new Expression("REC.USERS_ADDRESS_ID        AS    ADDRESS_ID"),
            new Expression("PR.PROVINCE_ID              AS    PER_PROVINCE"),
            new Expression("PR.PROVINCE_NAME            AS    PER_PROVINCE_ID"),
            new Expression("PD.DISTRICT_ID              AS    PER_DISTRICT"),
            new Expression("PD.DISTRICT_NAME            AS    PER_DISTRICT_ID"),
            new Expression("PVDC.VDC_MUNICIPALITY_ID    AS    PER_VDC"),
            new Expression("PVDC.VDC_MUNICIPALITY_NAME  AS    PER_VDC_ID"),
            new Expression("REC.PER_WARD_NO             AS    PER_WARD_NO"),
            new Expression("REC.PER_TOLE                AS    PER_TOLE"),
            new Expression("MR.PROVINCE_ID              AS    MAIL_PROVINCE"),
            new Expression("MR.PROVINCE_NAME            AS    MAIL_PROVINCE_ID"),
            new Expression("MD.DISTRICT_ID              AS    MAIL_DISTRICT "),      
            new Expression("MD.DISTRICT_NAME            AS    MAIL_DISTRICT_ID "),      
            new Expression("MVDC.VDC_MUNICIPALITY_ID    AS    MAIL_VDC "),
            new Expression("MVDC.VDC_MUNICIPALITY_NAME  AS    MAIL_VDC_ID "),
            new Expression("REC.MAIL_WARD_NO            AS    MAIL_WARD_NO "),      
            new Expression("REC.MAIL_TOLE               AS    MAIL_TOLE "),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            ], true);

        $select->from(['REC' => 'HRIS_REC_USERS_ADDRESS'])
                ->join(['UR' => 'HRIS_REC_VACANCY_APPLICATION'],'UR.USER_ID=REC.USER_ID', 'STATUS', 'left')
                ->join(['PR' => 'HRIS_PROVINCES'],'PR.PROVINCE_ID=REC.PER_PROVINCE_ID', 'STATUS', 'left')
                ->join(['PD' => 'HRIS_DISTRICTS'],'PD.DISTRICT_ID=REC.PER_DISTRICT_ID', 'STATUS', 'left')
                ->join(['PVDC' => 'HRIS_VDC_MUNICIPALITIES'],'PVDC.VDC_MUNICIPALITY_ID=REC.PER_VDC_ID', 'STATUS', 'left')
                ->join(['MR' => 'HRIS_PROVINCES'],'MR.PROVINCE_ID=REC.MAIL_PROVINCE_ID', 'STATUS', 'left')
                ->join(['MD' => 'HRIS_DISTRICTS'],'MD.DISTRICT_ID=REC.MAIL_DISTRICT_ID', 'STATUS', 'left')
                ->join(['MVDC' => 'HRIS_VDC_MUNICIPALITIES'],'MVDC.VDC_MUNICIPALITY_ID=REC.MAIL_VDC_ID', 'STATUS', 'left')     
                ->where(["REC.STATUS='E'"]);
        $select->Where("UR.APPLICATION_ID = $id");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function applicationfamilyById($id){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("PR.PROVINCE_NAME                AS    PER_PROVINCE_ID"),
            new Expression("PD.DISTRICT_NAME                AS    PER_DISTRICT_ID"),
            new Expression("PVDC.VDC_MUNICIPALITY_NAME      AS    PER_VDC_ID"),
            new Expression("REC.PER_WARD_NO                 AS    PER_WARD_NO"),
            new Expression("REC.PER_TOLE                    AS    PER_TOLE"),
            new Expression("MR.PROVINCE_NAME                AS    MAIL_PROVINCE_ID"),
            new Expression("MD.DISTRICT_NAME                AS    MAIL_DISTRICT_ID "),      
            new Expression("MVDC.VDC_MUNICIPALITY_NAME      AS    MAIL_VDC_ID "),
            new Expression("REC.MAIL_WARD_NO                AS    MAIL_WARD_NO "),      
            new Expression("REC.MAIL_TOLE                   AS    MAIL_TOLE "),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            ], true);

        $select->from(['REC' => 'HRIS_REC_USERS_ADDRESS'])
                ->join(['UR' => 'HRIS_REC_VACANCY_APPLICATION'],'UR.USER_ID=REC.USER_ID', 'STATUS', 'left')
                ->join(['PR' => 'HRIS_PROVINCES'],'PR.PROVINCE_ID=REC.PER_PROVINCE_ID', 'STATUS', 'left')
                ->join(['PD' => 'HRIS_DISTRICTS'],'PD.DISTRICT_ID=REC.PER_DISTRICT_ID', 'STATUS', 'left')
                ->join(['PVDC' => 'HRIS_VDC_MUNICIPALITIES'],'PVDC.VDC_MUNICIPALITY_ID=REC.PER_VDC_ID', 'STATUS', 'left')
                ->join(['MR' => 'HRIS_PROVINCES'],'MR.PROVINCE_ID=REC.MAIL_PROVINCE_ID', 'STATUS', 'left')
                ->join(['MD' => 'HRIS_DISTRICTS'],'MD.DISTRICT_ID=REC.MAIL_DISTRICT_ID', 'STATUS', 'left')
                ->join(['MVDC' => 'HRIS_VDC_MUNICIPALITIES'],'MVDC.VDC_MUNICIPALITY_ID=REC.MAIL_VDC_ID', 'STATUS', 'left')     
                ->where(["REC.STATUS='E'"]);
        $select->Where("UR.APPLICATION_ID = $id");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function applicationEduById($id){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("REC.EDUCATION_ID            AS    EDUCATION_ID"),
            new Expression("REC.EDUCATION_INSTITUTE     AS    EDUCATION_INSTITUTE"),
            new Expression("REC.LEVEL_ID                AS    LEVEL_ID"),
            new Expression("REC.FACALTY                 AS    FACALTY"),
            new Expression("REC.RANK_TYPE               AS    RANK_TYPE"),
            new Expression("REC.RANK_VALUE              AS    RANK_VALUE"),
            new Expression("REC.UNIVERSITY_BOARD        AS    UNIVERSITY_BOARD"),
            new Expression("REC.MAJOR_SUBJECT           AS    MAJOR_SUBJECT "),      
            new Expression("REC.PASSED_YEAR             AS    PASSED_YEAR "),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            new Expression("HAU.academic_university_name as university_name"),            
            new Expression("HAD.academic_degree_name as level_name"),            
            new Expression("HAP.academic_program_name as faculty_name"),            
            ], true);

        $select->from(['REC' => 'HRIS_REC_APPLICATION_EDUCATION'])
                ->join(['HAU' => 'HRIS_ACADEMIC_UNIVERSITY'],'HAU.academic_university_id = REC.university_board', 'STATUS', 'left')
                ->join(['HAD' => 'HRIS_ACADEMIC_degrees'],'HAD.academic_degree_id = REC.level_id', 'STATUS', 'left')
                ->join(['HAP' => 'HRIS_ACADEMIC_programs'],'HAP.academic_program_id =  REC.FACALTY', 'STATUS', 'left')
                ->where(["REC.STATUS='E'"]);
        $select->Where("REC.APPLICATION_ID = $id");
        $select->Where("REC.APPLICATION_ID = $id");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function applicationExpById($id){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("EXP.EXPERIENCE_ID       AS   EXPERIENCE_ID"),
            new Expression("EXP.ORGANISATION_NAME       AS   ORGANISATION_NAME"),
            new Expression("EXP.POST_NAME               AS   POST_NAME"),      
            new Expression("EXP.SERVICE_NAME            AS   SERVICE_NAME"),
            new Expression("EXP.LEVEL_ID                AS   LEVEL_ID"),
            new Expression("EXP.EMPLOYEE_TYPE_ID        AS   EMPLOYEE_TYPE_ID"),
            new Expression("EXP.FROM_DATE               AS   FROM_DATE"),
            // new Expression("ad_date(EXP.FROM_DATE)      AS   FROM_DATE_AD"),
            new Expression("EXP.TO_DATE                 AS   TO_DATE"),            
            // new Expression("ad_date(EXP.TO_DATE)         AS   TO_DATE_AD"),             
            new Expression("(CASE WHEN EXP.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),
            new Expression("case when ( from_date is null or to_date is null ) then 0 else
            (days_between(ad_date(EXP.FROM_DATE),
           ad_date(EXP.TO_DATE))) end AS TOTAL_DAYS"),               
            ], true);

        $select->from(['EXP' => 'HRIS_REC_APPLICATION_EXPERIENCES'])    
                ->where(["EXP.STATUS='E'"]);
        $select->Where("EXP.APPLICATION_ID = $id");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function applicationTrById($id){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TR.TRAINING_ID         AS   TRAINING_ID"),
            new Expression("TR.TRAINING_NAME       AS   TRAINING_NAME"),
            new Expression("TR.CERTIFICATE         AS   CERTIFICATE"),      
            new Expression("TR.FROM_DATE           AS   FROM_DATE"),
            new Expression("TR.TO_DATE             AS   TO_DATE"),
            new Expression("TR.TOTAL_DAYS          AS   TOTAL_DAYS"),
            new Expression("TR.DESCRIPTION         AS   DESCRIPTION"),          
            new Expression("(CASE WHEN TR.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            ], true);

        $select->from(['TR' => 'HRIS_REC_APPLICATION_TRAININGS'])    
                ->where(["TR.STATUS='E'"]);
        $select->Where("TR.APPLICATION_ID = $id");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result;
    }

    public function applicationDocById($id){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("DOC.REC_DOC_ID           AS   REC_DOC_ID"),
            new Expression("DOC.USER_ID              AS   USER_ID"),
            new Expression("DOC.DOC_OLD_NAME         AS   DOC_OLD_NAME"),      
            new Expression("DOC.DOC_NEW_NAME         AS   DOC_NEW_NAME"),
            new Expression("DOC.DOC_PATH             AS   DOC_PATH"),
            new Expression("DOC.DOC_TYPE             AS   DOC_TYPE"),
            new Expression("DOC.DOC_FOLDER           AS   DOC_FOLDER"),          
            new Expression("(CASE WHEN DOC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            ], true);

        $select->from(['DOC' => 'HRIS_REC_APPLICATION_DOCUMENTS'])    
                ->where(["DOC.STATUS='E'"]);
        $select->Where("DOC.APPLICATION_ID = $id");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function registrationDocById($id){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("DOC.USER_ID              AS   USER_ID"),
            new Expression("DOC.DOC_OLD_NAME         AS   DOC_OLD_NAME"),      
            new Expression("DOC.DOC_NEW_NAME         AS   DOC_NEW_NAME"),
            new Expression("DOC.DOC_PATH             AS   DOC_PATH"),
            new Expression("DOC.DOC_TYPE             AS   DOC_TYPE"),
            new Expression("DOC.DOC_FOLDER           AS   DOC_FOLDER"),          
            new Expression("(CASE WHEN DOC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            ], true);

        $select->from(['DOC' => 'HRIS_REC_APPLICATION_DOCUMENTS'])    
                ->where(["DOC.STATUS='E'"]);
        $select->Where("DOC.USER_ID = $id");
        $select->Where("DOC.DOC_FOLDER IN ('ethnicity','disability')");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo('<pre>');print_r($boundedParameter);print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }

    public function manuallyStageId($stageId, $aid){

        $sql = "UPDATE HRIS_REC_VACANCY_APPLICATION SET STAGE_ID = $stageId where APPLICATION_ID = $aid";

        $statement = $this->adapter->query($sql); 
        $result1 = Helper::extractDbData($statement->execute());

    }

    public function manualStageId($stageId,$remarks, $aid, $selectedInclusion = null, $unselectedInclusion = null){

        $sql = "UPDATE HRIS_REC_VACANCY_APPLICATION SET STAGE_ID = $stageId , REMARKS = '$remarks' where APPLICATION_ID = $aid";
        // echo $sql; die;
        $statement = $this->adapter->query($sql); 
        $result1 = Helper::extractDbData($statement->execute());
        // var_dump($result1);
        if($selectedInclusion){
            $new = implode(', ',$selectedInclusion);
            $sql = "UPDATE HRIS_REC_APPLICATION_PERSONAL SET INCLUSION_ID = '$new' where APPLICATION_ID = $aid";
            // var_dump($sql); 
            $statement2 = $this->adapter->query($sql);
            $result = Helper::extractDbData($statement2->execute());
        }
        if($unselectedInclusion){
            $new = implode(', ',$unselectedInclusion);
            $sql = "UPDATE HRIS_REC_APPLICATION_PERSONAL SET cancelled_inclusion_id = ifnull(cancelled_inclusion_id||',$new','$new') where APPLICATION_ID = $aid";
            // var_dump($sql); 
            $statement2 = $this->adapter->query($sql);
            $result = Helper::extractDbData($statement2->execute());
        }
        
        // var_dump($result); die;
        // return $result;
    }

    public function updateSelectedUnselectedInclusion($aid, $selectedInclusion = null, $unselectedInclusion = null){

        // var_dump($result1);
        if($selectedInclusion){
            $sql = "UPDATE HRIS_REC_APPLICATION_PERSONAL SET INCLUSION_ID = {$selectedInclusion} where APPLICATION_ID = $aid";
            $statement2 = $this->adapter->query($sql);
            $result = Helper::extractDbData($statement2->execute());
        }
        if($unselectedInclusion){
            $sql = "UPDATE HRIS_REC_APPLICATION_PERSONAL SET cancelled_inclusion_id = {$unselectedInclusion} where APPLICATION_ID = $aid";
            var_dump($sql); 
            $statement2 = $this->adapter->query($sql);
            $result = Helper::extractDbData($statement2->execute());
        }

        // var_dump($result); die;
        return true;
    }
    // public function addRollNo($id)
    // {
    //     $sql = "SELECT ORDER_NO FROM HRIS_REC_STAGES WHERE REC_STAGE_ID = {$id}";
    //     $statement = $this->adapter->query($sql); 
    //     $result = Helper::extractDbData($statement->execute());
    //     if ($result[0]['ORDER_NO'] > 20) {

    //         $sql = "UPDATE HRIS_REC_APPLICATION_PERSONAL SET ROLL_NO = '$new' where APPLICATION_ID = $aid";
    //         $statement2 = $this->adapter->query($sql);
    //         $result = Helper::extractDbData($statement2->execute());
    //     }s
    // }
    public function getEmpName($id)
    {
        $sql = "SELECT * FROM HRIS_REC_VACANCY_APPLICATION WHERE APPLICATION_ID = {$id}";

        $statement = $this->adapter->query($sql); 
        $result = Helper::extractDbData($statement->execute());
        if ($result[0]['APPLICATION_TYPE'] == 'OPEN') {
            $sql = "SELECT HRIS_REC_VACANCY_USERS.FIRST_NAME FROM HRIS_REC_APPLICATION_PERSONAL LEFT JOIN HRIS_REC_VACANCY_USERS
            ON HRIS_REC_APPLICATION_PERSONAL.USER_ID = HRIS_REC_VACANCY_USERS.USER_ID WHERE APPLICATION_ID = {$id}";
             $statement = $this->adapter->query($sql); 
             $result = Helper::extractDbData($statement->execute());
             $uc = strtoupper(substr($result[0]['FIRST_NAME'],0,3));
             $rollNo = $uc.'-'.$id;
        } else {
            $sql = "SELECT HRIS_USERS.USER_NAME FROM HRIS_REC_APPLICATION_PERSONAL LEFT JOIN HRIS_USERS
            ON HRIS_REC_APPLICATION_PERSONAL.USER_ID = HRIS_USERS.USER_ID WHERE APPLICATION_ID = {$id}";
             $statement = $this->adapter->query($sql); 
             $result = Helper::extractDbData($statement->execute());
             $uc = strtoupper(substr($result[0]['USER_NAME'],0,3));
             $rollNo = $uc.'-'.$id;
        }
        $sql = "UPDATE HRIS_REC_APPLICATION_PERSONAL SET ROLL_NO = '$rollNo' where APPLICATION_ID = $id";
        $statement = $this->adapter->query($sql); 
        $result = Helper::extractDbData($statement->execute());
        return 'true';
    }
    public function applicationEduByIdInternal($id){
        $sql="select 
        HEQ.employee_id,
        HAP.academic_program_name as faculty,
        HAD.academic_degree_name as degree,
        HAU.academic_university_name as university,
        HAC.academic_course_name as course,
        HEQ.rank_type,
        HEQ.rank_value,
        HEQ.passed_yr
        from hris_employee_qualifications HEQ
        left join hris_academic_programs HAP on (HAP.academic_program_id = HEQ.academic_program_id)
        left join hris_academic_degrees HAD on (HAD.academic_degree_id = HEQ.academic_degree_id)
        left join hris_academic_university HAU on (HAU.academic_university_id = HEQ.academic_university_id)
        left join hris_academic_courses HAC on (HAC.academic_course_id = HEQ.academic_course_id)
        where HEQ.status='E' and employee_id in (select employee_id from hris_users where user_id in (select user_id from HRIS_REC_vacancy_application where application_id = $id))";
        $statement = $this->adapter->query($sql); 
        $result = Helper::extractDbData($statement->execute());
        // print_r($result);die;
        return $result;
    }

    public function addApplicationStageHistory(Model $model) 
    {
        $addData=$model->getArrayCopyForDB();
        $this->recAppStageRepo->insert($addData);
    }

    public function getApplicationStageHistory($applicationId){
        $sql="select 
                RAS.id, RAS.application_id, 
                RS.stage_edesc, 

                E.full_name as created_by,  

                to_char(RAS.created_date_time,'YYYY-MM-DD HH:MI:SS AM') as date_time,

                RAS.remarks_np from hris_rec_application_stage RAS
                
                left join hris_rec_stages RS on (RAS.stage_id = RS.rec_stage_id)
                left join hris_employees  E  on (E.employee_id = RAS.created_by)
                
                where RAS.application_id = $applicationId
        ";
        $statement = $this->adapter->query($sql); 
        $result = Helper::extractDbData($statement->execute());
        // print_r($result);die;
        return $result;
    }

    public function getUpdateById($table, $data, $where, $where_value) {

        $column = implode(',', array_keys($data));
        $value  = implode("','", array_values($data));

        $sql = "UPDATE {$table} SET ($column) = ('$value') WHERE {$where} = {$where_value}";

        $result = $this->rawQuery($sql);

        return true;
    }

    public function getAllRow($table)
    {
        $sql = "SELECT * FROM {$table}";

        $result = $this->rawQuery($sql);
        return $result;

    }

    public function getRowById($table, $where, $where_value) {

        if ($where_value) {

            $sql = "SELECT * FROM {$table} WHERE {$where} = {$where_value}";

            $statement = $this->adapter->query($sql);
            $result    = $statement->execute();

            return $result->current();

        }
        return false;
        
    }

    public function insertData($table, $data) {
        $key   = implode(',', array_keys($data));
        $value = implode("','", array_values($data));

        $sql   = "INSERT INTO {$table} ($key) VALUES ('$value')";

        $result = $this->rawQuery($sql);

        return true;
    } 

    public function applicationDataByIdInternalEdit($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.USER_ID                AS USER_ID"),
            new Expression("REC.APPLICATION_ID         AS APPLICATION_ID"),
            new Expression("UR.MARITAL_STATUS          AS MARITAL_STATUS"),
            new Expression("REC.SKILL_ID                   AS SKILL_ID"),
            new Expression("REC.INCLUSION_ID               AS INCLUSION_ID"),
            new Expression("REC.CANCELLED_INCLUSION_ID               AS CANCELLED_INCLUSION_ID"),
            new Expression("UR.DISABLED_FLAG           AS DISABLED_FLAG"),
            new Expression("URREL.RELIGION_NAME        AS  RELIGION_NAME "),       
            new Expression("URETH.ETHNICITY             AS  ETHNICITY "),       
            new Expression("UR.ID_CITIZENSHIP_NO       AS  CITIZENSHIP_NO "),
            new Expression("UR.EMPLOYEE_ID       AS  EMPLOYEE_ID "),
            new Expression("UR.EMPLOYEE_STATUS       AS  EMPLOYEE_STATUS"),   
            new Expression("UR.ID_CITIZENSHIP_ISSUE_DATE AS  CTZ_ISSUE_DATE "),  
            new Expression("UR.ID_CITIZENSHIP_ISSUE_PLACE    AS  CTZ_ISSUE_DISTRICT_ID "),  
            new Expression("UR.DISABLED_FLAG    AS  DISABLED_FLAG "),  
            new Expression("UR.BIRTH_DATE               AS  DOB "),
            new Expression("UR.PROFILE_PICTURE_ID              AS  PROFILE_PICTURE_ID "),
            new Expression("UR.MOBILE_NO                 AS  MOBILE_NO "),      
            new Expression("UR.GENDER_ID                AS  GENDER_ID "),      
            new Expression("UR.FAM_FATHER_NAME              AS  FAM_FATHER_NAME "),      
            new Expression("UR.FAM_FATHER_OCCUPATION     AS  FAM_FATHER_OCCUPATION "),      
            new Expression("UR.FAM_MOTHER_NAME              AS  FAM_MOTHER_NAME "),      
            new Expression("UR.FAM_MOTHER_OCCUPATION     AS  FAM_MOTHER_OCCUPATION "),      
            new Expression("UR.FAM_GRAND_FATHER_NAME         AS  FAM_GRAND_FATHER_NAME "),      
            new Expression("UR.FAM_SPOUSE_NAME              AS  FAM_SPOUSE_NAME "),     
            new Expression("UR.FIRST_NAME               AS FIRST_NAME "),
            new Expression("UR.MIDDLE_NAME              AS MIDDLE_NAME "),
            new Expression("UR.LAST_NAME                AS LAST_NAME "),
            new Expression("UR.TELEPHONE_NO             AS TELEPHONE_NO "),
            new Expression("UR.EMAIL_PERSONAL            AS EMAIL_PERSONAL"),
            new Expression("UR.EMAIL_OFFICIAL            AS EMAIL_OFFICIAL"),
            new Expression("UR.ADDR_PERM_PROVINCE_ID            AS ADDR_PERM_PROVINCE_ID"),
            new Expression("UR.ADDR_TEMP_PROVINCE_ID            AS ADDR_TEMP_PROVINCE_ID"),
            new Expression("UR.ADDR_PERM_DISTRICT_ID            AS ADDR_PERM_DISTRICT_ID"),
            new Expression("UR.ADDR_TEMP_DISTRICT_ID            AS ADDR_TEMP_DISTRICT_ID"),
            new Expression("UR.ADDR_PERM_ZONE_ID            AS ADDR_PERM_ZONE_ID"),
            new Expression("UR.ADDR_TEMP_ZONE_ID            AS ADDR_TEMP_ZONE_ID"),




            new Expression("case when UR.EMAIL_OFFICIAL is null then  UR.EMAIL_PERSONAL else  UR.EMAIL_OFFICIAL end  AS EMAIL_ID"),
            new Expression("UR.ADDR_PERM_WARD_NO            AS ADDR_PERM_WARD_NO"),
            new Expression("UR.ADDR_TEMP_WARD_NO            AS ADDR_TEMP_WARD_NO"),
            new Expression("UR.ADDR_PERM_STREET_ADDRESS            AS ADDR_PERM_STREET_ADDRESS"),
            new Expression("UR.ADDR_TEMP_STREET_ADDRESS            AS ADDR_TEMP_STREET_ADDRESS"),
            new Expression("EMUSER.USER_NAME             AS USER_NAME"),
            new Expression("PERMPROV.PROVINCE_NAME       AS PERM_PROVINCE_NAME"),
            new Expression("TEMPPROV.PROVINCE_NAME       AS TEMP_PROVINCE_NAME"),
            new Expression("PERMZO.ZONE_NAME       AS PERM_ZONE_NAME"),
            new Expression("TEMPZO.ZONE_NAME       AS TEMP_ZONE_NAME"),
            new Expression("PERMDIS.DISTRICT_NAME       AS PERM_DISTRICT_NAME"),
            new Expression("TEMPDIS.DISTRICT_NAME       AS TEMP_DISTRICT_NAME"),
            new Expression("PERMCOU.COUNTRY_NAME       AS TEMP_COUNTRY_NAME"),
            new Expression("TEMPCOU.COUNTRY_NAME       AS TEMP_COUNTRY_NAME"),
            new Expression(" UVA.AD_NO                  AS AD_NO "),
            new Expression(" UVA.REGISTRATION_NO        AS REGISTRATION_NO "),
            new Expression(" STG.STAGE_EDESC            AS STAGE_ID "),
            new Expression(" UVA.REMARKS                AS STAGE_REMARKS "),
            new Expression(" UVA.APPLICATION_AMOUNT     AS APPLICATION_AMOUNT "),
            new Expression("bs_date(UVA.CREATED_DATE)  AS APPLIED_DATE"),
            new Expression("UVA.CREATED_DATE  AS APPLIED_DATE_AD"),
            new Expression("UD.DESIGNATION_TITLE AS DESIGNATION_TITLE"),

            //Payment detail
            new Expression(" PAY.PAYMENT_TYPE           AS PAYMENT_TYPE "),
            new Expression(" PAY.PAYMENT_AMOUNT            AS PAYMENT_NPR "),
            // // DOCUMENT
            // new Expression(" DOC.DOC_PATH               AS PROFILE_IMG "),

            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),  
            new Expression("UVA.PAYMENT_PAID as PAYMENT_PAID"),  
            new Expression("UVA.PAYMENT_VERIFIED as PAYMENT_VERIFIED"),  
            new Expression("HRIS_REC_PAYMENT_STATUS(UVA.PAYMENT_PAID,
            UVA.PAYMENT_VERIFIED) as PAYMENT_STATUS"),  
            new Expression("RPG.GATEWAY_COMPANY AS PAYMENT_TYPE"),  
            new Expression("RPG.ID AS ID"),  
            new Expression("PAY.PAYMENT_REFERENCE_ID AS PAYMENT_REFERENCE_ID"),
            new Expression("POC.FILE_PATH AS PROFILE_IMG"),
            new Expression("HRIS_REC_MULTIPLE_AD_NO(REC.APPLICATION_ID) as MULTIPLE_AD_NO"),
            new Expression("GET_AD_NO(VAC.OPENING_ID,VAC.VACANCY_NO) as VACANCY_AD_NO"),
            ], true);

        $select->from(['REC' => 'HRIS_REC_APPLICATION_PERSONAL'])
                ->join(['EMUSER' => 'HRIS_USERS'],'EMUSER.USER_ID=REC.USER_ID', 'STATUS', 'left')
                ->join(['UR' => 'HRIS_EMPLOYEES'],'UR.EMPLOYEE_ID=EMUSER.EMPLOYEE_ID', 'STATUS', 'left')
                ->join(['UD' => 'HRIS_DESIGNATIONS'],'UD.DESIGNATION_ID=UR.DESIGNATION_ID', 'STATUS', 'left')
                ->join(['URREL' => 'HRIS_RELIGIONS'],'URREL.RELIGION_ID=UR.RELIGION_ID', 'STATUS', 'left')
                ->join(['URETH' => 'HRIS_ETHNICITIES'],'URETH.ETHNICITY_ID=UR.ETHNICITY_ID', 'STATUS', 'left')
                ->join(['UVA' => 'HRIS_REC_VACANCY_APPLICATION'],'UVA.APPLICATION_ID=REC.APPLICATION_ID', 'APPLICATION_ID', 'left')
                ->join(['STG' => 'HRIS_REC_STAGES'],'STG.REC_STAGE_ID=UVA.STAGE_ID', 'STATUS', 'left')
                ->join(['PAY' => 'HRIS_REC_APPLICATION_PAYMENT'],'PAY.APPLICATION_ID=REC.APPLICATION_ID', 'PAYMENT_REFERENCE_ID', 'left')
                ->join(['DOC' => 'HRIS_EMPLOYEE_FILE'],'DOC.EMPLOYEE_ID=UR.EMPLOYEE_ID', 'STATUS', 'left')
                ->join(['POC' => 'HRIS_EMPLOYEE_FILE'],'POC.FILE_CODE = UR.PROFILE_PICTURE_ID', 'STATUS', 'left')
                ->join(['PERMPROV' => 'HRIS_PROVINCES'],'PERMPROV.PROVINCE_ID=UR.ADDR_PERM_PROVINCE_ID', 'STATUS', 'left')
                ->join(['TEMPPROV' => 'HRIS_PROVINCES'],'TEMPPROV.PROVINCE_ID=UR.ADDR_TEMP_PROVINCE_ID', 'STATUS', 'left')
                ->join(['PERMZO' => 'HRIS_ZONES'],'PERMZO.ZONE_ID=UR.ADDR_PERM_ZONE_ID', 'STATUS', 'left')
                ->join(['TEMPZO' => 'HRIS_ZONES'],'TEMPZO.ZONE_ID=UR.ADDR_TEMP_ZONE_ID', 'STATUS', 'left')
                ->join(['PERMDIS' => 'HRIS_DISTRICTS'],'PERMDIS.DISTRICT_ID=UR.ADDR_PERM_DISTRICT_ID', 'STATUS', 'left')
                ->join(['TEMPDIS' => 'HRIS_DISTRICTS'],'TEMPDIS.DISTRICT_ID=UR.ADDR_TEMP_DISTRICT_ID', 'STATUS', 'left')
                ->join(['PERMCOU' => 'HRIS_COUNTRIES'],'PERMCOU.COUNTRY_ID=UR.ADDR_PERM_COUNTRY_ID', 'COUNTRY_CODE','left')
                ->join(['TEMPCOU' => 'HRIS_COUNTRIES'],'TEMPCOU.COUNTRY_ID=UR.ADDR_TEMP_COUNTRY_ID', 'COUNTRY_CODE', 'left')
                ->join(['RPG' => 'hris_rec_payment_gateway'],'PAY.PAYMENT_GATEWAY_ID=RPG.ID', 'ID', 'left')
                ->join(['VAC' => 'HRIS_REC_VACANCY'],"VAC.vacancy_id = UVA.ad_no", 'STATUS', 'left')
                ->where(["REC.STATUS='E'"]);

        $select->Where("REC.APPLICATION_ID = $id");
        $select->order("REC.PERSONAL_ID ASC");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        // print_r($statement->getSql()); die();
        return $result;
    }

    public function applicationEduByIdInternalEdit($id){
        $sql="select 
        HEQ.employee_id, HEQ.id as qualification_id,
        HAP.academic_program_name as faculty, HAP.academic_program_id,
        HAD.academic_degree_name as degree, HAD.academic_degree_id,
        HAU.academic_university_name as university, HAU.academic_university_id,
        HAC.academic_course_name as course, HAC.academic_course_id,
        HEQ.rank_type,
        HEQ.rank_value,
        HEQ.passed_yr
        from hris_employee_qualifications HEQ
        left join hris_academic_programs HAP on (HAP.academic_program_id = HEQ.academic_program_id)
        left join hris_academic_degrees HAD on (HAD.academic_degree_id = HEQ.academic_degree_id)
        left join hris_academic_university HAU on (HAU.academic_university_id = HEQ.academic_university_id)
        left join hris_academic_courses HAC on (HAC.academic_course_id = HEQ.academic_course_id)
        where HEQ.status='E' and employee_id in (select employee_id from hris_users where user_id in (select user_id from HRIS_REC_vacancy_application where application_id = $id))";
        $statement = $this->adapter->query($sql); 
        $result = Helper::extractDbData($statement->execute());
        // print_r($result);die;
        return $result;
    }

    public function applicationExpByIdInternalEdit($id){
        $sql="select 
        *
        from hris_employee_experiences HEQ
        where HEQ.status='E' and employee_id in (select employee_id from hris_users where user_id in (select user_id from HRIS_REC_vacancy_application where application_id = $id))";
        $statement = $this->adapter->query($sql); 
        $result = Helper::extractDbData($statement->execute());
        // print_r($result);die;
        return $result;
    }

    public function applicationTrEmpById($id){
        $sql="select 
        *
        from hris_employee_trainings HEQ
        where HEQ.status='E' and employee_id in (select employee_id from hris_users where user_id in (select user_id from HRIS_REC_vacancy_application where application_id = $id))";
        $statement = $this->adapter->query($sql); 
        $result = Helper::extractDbData($statement->execute());
        // print_r($result);die;
        return $result;
    }
}
