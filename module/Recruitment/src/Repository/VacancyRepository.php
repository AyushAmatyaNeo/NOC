<?php
namespace Recruitment\Repository;

use Application\Helper\EntityHelper;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Recruitment\Model\RecruitmentVacancy;
use Symfony\Component\VarDumper\VarDumper;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

class VacancyRepository extends HrisRepository{
    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, RecruitmentVacancy::TABLE_NAME);
    }

    
    public function add(Model $model) {
        $array = $model->getArrayCopyForDB();
        // echo '<pre>'; print_r($array); die();
        $this->tableGateway->insert($array);        
    }
    
    public function getFilteredRecords($search) {        
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
            ], true);

        $select->from(['REC' => RecruitmentVacancy::TABLE_NAME])
                // ->join(['HG' => 'HRIS_GENDERS'],'HG.GENDER_ID=REC.GENDER', 'GENDER_ID', 'left')
                ->join(['HQ' => 'HRIS_ACADEMIC_DEGREES'],'HQ.ACADEMIC_DEGREE_ID=REC.QUALIFICATION_ID', 'ACADEMIC_DEGREE_ID', 'left')
                ->join(['HD' => 'HRIS_DEPARTMENTS'],'HD.DEPARTMENT_ID=REC.DEPARTMENT_ID', 'DEPARTMENT_CODE', 'left')
                ->join(['HOP' => 'HRIS_SERVICE_TYPES'], 'HOP.SERVICE_TYPE_ID=REC.SERVICE_TYPES_ID', 'SERVICE_TYPE_ID', 'left')
                ->join(['DES' => 'HRIS_DESIGNATIONS'],'DES.DESIGNATION_ID=REC.POSITION_ID', 'status', 'left') 
                ->join(['OPN' => 'HRIS_REC_OPENINGS'],'OPN.OPENING_ID=REC.OPENING_ID', 'status', 'left') 
                
                // $select->where(["REC.VACANCY_ID" => $id]);
                
                ->where(["REC.STATUS='E' AND HOP.STATUS='E'"]);

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
        if (($search['vacancy_type'] != null)) {
            $select->where([
                "REC.VACANCY_TYPE" => $search['vacancy_type']
            ]);
        }

        $select->order("REC.VACANCY_ID ASC");
        $boundedParameter = [];
        
        $statement = $sql->prepareStatementForSqlObject($select);
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
    public function delete(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $deleted_dt = date('Y-m-d');
        $rewardSql = "update HRIS_REC_VACANCY SET status = 'D', Deleted_By = {$array["DELETED_BY"]} , Deleted_Dt = '{$deleted_dt}' WHERE VACANCY_ID = {$id}";
        
        // print_r($rewardSql); die();
        return EntityHelper::rawQueryResult($this->adapter, $rewardSql);
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
    public function getadno($ad_no){
        $sql = "select * from hris_rec_vacancy where AD_NO =  '$ad_no'";
        // echo $sql; die;
        $result =  $this->rawQuery($sql);
        if($result){
            return true;        
        }
        
        // print_r($result); die; 
        return false;
    }
    public function CheckVacancyno($id){
        $sql = "select count(opening_id) as vacancyno from hris_rec_vacancy where opening_id =  '$id' and status = 'E'";
        // echo $sql; die;
        $result =  $this->rawQuery($sql);
        return $result;
    }
    public function CheckReserNo($id){
        $sql = "select vacancy_reservation_no as reservation_no from hris_rec_vacancy where opening_id =  '$id' and status = 'E'";
        $result =  $this->rawQuery($sql);
        return $result;
    }
}   