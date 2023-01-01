<?php
namespace Recruitment\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class UserAdvanceRepository extends HrisRepository{

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter);
    }

    public function getOpenData($search)
    {

        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns([
            new Expression("USER.FIRST_NAME || ' ' || ifnull(USER.MIDDLE_NAME,'') || ' ' || USER.LAST_NAME    AS FULL_NAME"),
            new Expression("APPLICATION.REGISTRATION_NO    AS REGISTRATION_NO"),
            new Expression("APPLICATION.APPLICATION_TYPE    AS VACANCY_TYPE"),
            new Expression("APPLICATION.APPLICATION_ID AS APPLICATION_ID"),
            new Expression("HRIS_REC_MULTIPLE_AD_NO(APPLICATION.APPLICATION_ID) as MULTIPLE_AD_NO"),
            new Expression("GET_AD_NO(VAC.OPENING_ID,VAC.VACANCY_NO) as VACANCY_AD_NO"),
            new Expression("OPENING.OPENING_NO as OPENING_NO"),
            new Expression("DES.DESIGNATION_TITLE         AS DESIGNATION"),
            new Expression("DEP.DEPARTMENT_NAME         AS DEPARTMENT"),
            new Expression("STAGE.STAGE_EDESC AS STAGE"),
        ], true);

        $select->from(['APPLICATION' => 'HRIS_REC_VACANCY_APPLICATION'])
               ->join(['USER' => 'HRIS_REC_VACANCY_USERS'],'USER.USER_ID=APPLICATION.USER_ID', 'RESET_STATUS', 'left')
               ->join(['UR' => 'HRIS_REC_USERS_REGISTRATION'],'UR.USER_ID=APPLICATION.USER_ID', 'STATUS', 'left')
               ->join(['G' => 'HRIS_GENDERS'],'G.GENDER_ID=UR.GENDER_ID', 'STATUS', 'left')
               ->join(['STAGE' => 'HRIS_REC_STAGES'], 'STAGE.REC_STAGE_ID=APPLICATION.STAGE_ID', 'status', 'left')
               ->join(['VAC' => 'HRIS_REC_VACANCY'],'VAC.VACANCY_ID=APPLICATION.AD_NO', 'status', 'left')
               ->join(['OPENING' => 'HRIS_REC_OPENINGS'],'OPENING.OPENING_ID=VAC.OPENING_ID', 'status', 'left')
               ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=VAC.POSITION_ID', 'status', 'left') 
               ->join(['DEP' => 'HRIS_DEPARTMENTS'],'DEP.DEPARTMENT_ID=VAC.DEPARTMENT_ID', 'status', 'left') 
               ->where(["APPLICATION.APPLICATION_TYPE = 'OPEN'"])
               ->where(["APPLICATION.IS_APPROVED = 'Y'"]);
        
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
                "APPLICATION.STAGE_ID" => $search['stageId']
            ]);
        }
        if (($search['vacancy_type'] != null)) {
            $select->where([
                "VAC.VACANCY_TYPE" => $search['vacancy_type']
            ]);
        }

        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }

    public function getInternalData($search)
    {

        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns([
            new Expression("USER.FIRST_NAME || ' ' || ifnull(USER.MIDDLE_NAME,'') || ' ' || USER.LAST_NAME    AS FULL_NAME"),
            new Expression("APPLICATION.REGISTRATION_NO    AS REGISTRATION_NO"),
            new Expression("APPLICATION.APPLICATION_TYPE    AS VACANCY_TYPE"),
            new Expression("APPLICATION.APPLICATION_ID AS APPLICATION_ID"),
            new Expression("HRIS_REC_MULTIPLE_AD_NO(APPLICATION.APPLICATION_ID) as MULTIPLE_AD_NO"),
            new Expression("GET_AD_NO(VAC.OPENING_ID,VAC.VACANCY_NO) as VACANCY_AD_NO"),
            new Expression("OPENING.OPENING_NO as OPENING_NO"),
            new Expression("DES.DESIGNATION_TITLE         AS DESIGNATION"),
            new Expression("DEP.DEPARTMENT_NAME         AS DEPARTMENT"),
            new Expression("STAGE.STAGE_EDESC AS STAGE"),
        ], true);

        $select->from(['APPLICATION' => 'HRIS_REC_VACANCY_APPLICATION'])
               ->join(['HRUS' => 'HRIS_USERS'],'HRUS.USER_ID=APPLICATION.USER_ID', 'STATUS', 'left')
               ->join(['UR' => 'HRIS_REC_USERS_REGISTRATION'],'UR.USER_ID=APPLICATION.USER_ID', 'STATUS', 'left')
               ->join(['G' => 'HRIS_GENDERS'],'G.GENDER_ID=UR.GENDER_ID', 'STATUS', 'left')
               ->join(['USER' => 'HRIS_EMPLOYEES'],'USER.EMPLOYEE_ID = HRUS.EMPLOYEE_ID', 'STATUS', 'left')
               ->join(['STAGE' => 'HRIS_REC_STAGES'], 'STAGE.REC_STAGE_ID=APPLICATION.STAGE_ID', 'status', 'left')
               ->join(['VAC' => 'HRIS_REC_VACANCY'],'VAC.VACANCY_ID=APPLICATION.AD_NO', 'status', 'left')
               ->join(['OPENING' => 'HRIS_REC_OPENINGS'],'OPENING.OPENING_ID=VAC.OPENING_ID', 'status', 'left')
               ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=VAC.POSITION_ID', 'status', 'left') 
               ->join(['DEP' => 'HRIS_DEPARTMENTS'],'DEP.DEPARTMENT_ID=VAC.DEPARTMENT_ID', 'status', 'left')  
               ->where(["APPLICATION.APPLICATION_TYPE != 'OPEN'"])
               ->where(["APPLICATION.IS_APPROVED = 'Y'"]);
        
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
                "APPLICATION.STAGE_ID" => $search['stageId']
            ]);
        }
        if (($search['vacancy_type'] != null)) {
            $select->where([
                "VAC.VACANCY_TYPE" => $search['vacancy_type']
            ]);
        }
        

        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r($statement->getSql()); die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }

}