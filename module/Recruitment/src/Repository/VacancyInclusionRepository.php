<?php
namespace Recruitment\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Recruitment\Model\RecruitmentVacancy;   
use Application\Model\Model;
use Recruitment\Model\VacancyInclusionModel;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;


class VacancyInclusionRepository extends HrisRepository {
    public function __construct(AdapterInterface $adapter, $tablename = null)
    {
        parent::__construct($adapter, VacancyInclusionModel::TABLE_NAME);
    }
    public function add(Model $options_data) 
    {
        $addData=$options_data->getArrayCopyForDB();
        $this->tableGateway->insert($addData);
    }
    public function edit(Model $model, $id){
        $array = $model->getArrayCopyForDB();
        
        $this->tableGateway->update($array, [RecruitmentVacancy::VACANCY_ID => $id]);
        // echo '<pre>';print_r($this->db->last_query());
    }

    public function getFilteredRecords($search)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.VACANCY_INCLUSION_ID AS VACANCY_INCLUSION_ID"),
            new Expression("HO.OPTION_EDESC AS OPTION_EDESC"),
            new Expression("REC.VACANCY_ID AS VACANCY_ID"),
            new Expression("HV.AD_NO AS AD_NO"),
            // new Expression("HO.OPTION_EDESC AS INCLUSIO"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),       
            ], true);

        $select->from(['REC' => VacancyInclusionModel::TABLE_NAME])
                ->join(['HO' => 'HRIS_REC_OPTIONS'],'HO.OPTION_ID=REC.INCLUSION_ID', 'VACANCY_ID','LEFT')
                ->join(['HV' => 'HRIS_REC_VACANCY'],'HV.VACANCY_ID=REC.VACANCY_ID', 'VACANCY_ID','LEFT')
                ->where(["REC.STATUS='E'"]);
        //Another if
        // $select->order("REC.VACANCY_ID ASC");
        $boundedParameter = [];
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
                // echo '<pre>'; print_r($rawsql); die;

        // echo'<pre>'; print_r($result); die;
        return $result;
    }
    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("REC.VACANCY_INCLUSION_ID AS VACANCY_INCLUSION_ID"),
            new Expression("REC.INCLUSION_ID AS INCLUSION_ID"),
            new Expression("REC.VACANCY_ID AS VACANCY_ID"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),
            ], true);

        $select->from(['REC' => VacancyInclusionModel::TABLE_NAME]);

        $select->where(["REC.VACANCY_ID='{$id}' AND STATUS = 'E'"]);
        $statement = $sql->prepareStatementForSqlObject($select);
        // print_r ($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }
    public function delete($DeletedBy,$id)
    {      
        // $rawsql = "DELETE FROM HRIS_REC_VACANCY_INCLUSION WHERE VACANCY_INCLUSION_ID = {$id}";
        // echo '<pre>'; print_r($rawsql); die;
        $deleted_dt = date('Y-m-d');
        $rewardSql = "UPDATE HRIS_REC_VACANCY_INCLUSION SET status = 'D', Deleted_By = {$DeletedBy} , Deleted_Date = '{$deleted_dt}' WHERE VACANCY_ID = {$id}";
        
        // print_r($rewardSql); die();
        return EntityHelper::rawQueryResult($this->adapter, $rewardSql);
    }
}