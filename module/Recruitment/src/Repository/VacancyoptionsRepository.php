<?php
namespace Recruitment\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Recruitment\Model\Vacancyoptions;
use Zend\Db\Sql\Sql;
use Application\Helper\EntityHelper;
use Zend\Db\Sql\Expression;
use Application\Helper\Helper;
use Zend\Db\Sql\Where;

class VacancyoptionsRepository extends HrisRepository
{
    public function __construct(AdapterInterface $adapter, $tableName = null) 
    {
        parent::__construct($adapter, Vacancyoptions::TABLE_NAME);
    }
    public function getFilteredRecords($search)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.VACANCY_OPTION_ID AS VACANCY_OPTION_ID"),
            new Expression("HO.OPTION_EDESC AS OPTION_ID"),
            new Expression("REC.QUOTA AS QUOTA"),
            new Expression("REC.OPEN_INTERNAL AS OPEN_INTERNAL"),
            new Expression("REC.NORMAL_AMT AS NORMAL_AMT"),            
            new Expression("REC.LATE_AMT AS LATE_AMT"),
            new Expression("HV.AD_NO AS VACANCY_ID"),
            new Expression("REC.REMARKS AS REMARKS"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            ], true);

        $select->from(['REC' => Vacancyoptions::TABLE_NAME])
                ->join(['HV' => 'HRIS_REC_VACANCY'],'HV.VACANCY_ID=REC.VACANCY_ID', 'QUOTA_OPEN', 'left')
                ->join(['HO' => 'HRIS_REC_OPTIONS'],'HO.OPTION_ID=REC.OPTION_ID', 'OPTION_NDESC', 'left')           
                ->where(["REC.STATUS='E'"]);
            $select->Where("HV.STATUS = 'E'");

        $select->order("REC.VACANCY_OPTION_ID ASC");
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        // print_r($statement->getSql()); die();
        return $result;
    }
    public function add(Model $model) {
        $addData=$model->getArrayCopyForDB();
        $this->tableGateway->insert($addData);
    }
    public function getVacancyPositions()
    {
        $sql = "select v.vacancy_id, v.position_id, d.designation_title from hris_rec_vacancy v left join hris_designations d on (d.designation_id = v.position_id)
        where v.status = 'E'";
        return $this->rawQuery($sql);
    }
    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.VACANCY_OPTION_ID AS VACANCY_OPTION_ID"),
            new Expression("REC.VACANCY_ID AS VACANCY_ID"),
            // new Expression("HO.OPTION_EDESC AS OPTION_ID"),
            new Expression("REC.OPTION_ID AS OPTION_ID"),
            new Expression("REC.QUOTA AS QUOTA"),
            new Expression("REC.OPEN_INTERNAL AS OPEN_INTERNAL"),
            new Expression("REC.NORMAL_AMT AS NORMAL_AMT"),            
            new Expression("REC.LATE_AMT AS LATE_AMT"),
            // new Expression("REC.VACANCY_ID AS VACANCY_ID"),
            new Expression("REC.REMARKS AS REMARKS"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            ], true);

        $select->from(['REC' => Vacancyoptions::TABLE_NAME]);
                // ->join(['HO' => 'HRIS_REC_OPTIONS'],'HO.OPTION_ID=REC.OPTION_ID', 'OPTION_NDESC', 'left');

        $select->where(["REC.VACANCY_OPTION_ID='{$id}'"]);
        $boundedParameter = [];
      
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $result = $statement->execute($boundedParameter);
        // print_r ($statement); die();
        return $result->current();
    }

    public function fetchBydetails($id) 
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.VACANCY_OPTION_ID AS VACANCY_OPTION_ID"),
            new Expression("REC.VACANCY_ID AS VACANCY_ID"),
            // new Expression("HO.OPTION_EDESC AS OPTION_ID"),
            new Expression("HO.OPTION_ID AS OPTION_ID"),
            new Expression("REC.QUOTA AS QUOTA"),
            new Expression("REC.OPEN_INTERNAL AS OPEN_INTERNAL"),
            new Expression("REC.NORMAL_AMT AS NORMAL_AMT"),            
            new Expression("REC.LATE_AMT AS LATE_AMT"),
            // new Expression("REC.VACANCY_ID AS VACANCY_ID"),
            new Expression("REC.REMARKS AS REMARKS"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),            
            ], true);

        $select->from(['REC' => Vacancyoptions::TABLE_NAME])
                ->join(['HO' => 'HRIS_REC_OPTIONS'],'HO.OPTION_ID=REC.OPTION_ID', 'OPTION_NDESC', 'left');

        $select->where(["REC.VACANCY_OPTION_ID='{$id}'"]);
        $boundedParameter = [];
      
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $result = $statement->execute($boundedParameter);
        // print_r ($statement->getSql()); die();
        return $result->current();
    }
    public function alldataoptions()
    {
        $sql = "SELECT OPTION_ID,OPTION_EDESC from HRIS_REC_OPTIONS";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($statement->execute());

    }
    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Vacancyoptions::VACANCY_OPTION_ID => $id]);
    }
    public function delete(Model $model, $id) 
    {
        $array = $model->getArrayCopyForDB();
        $deleted_dt = date('Y-m-d');
        $rewardSql = "update HRIS_REC_VACANCY_OPTIONS SET status = 'D', Deleted_By = {$array["DELETED_BY"]} , Deleted_Dt = '{$deleted_dt}' WHERE VACANCY_OPTION_ID = {$id}";
        
        // print_r($rewardSql); die();
        return EntityHelper::rawQueryResult($this->adapter, $rewardSql);
    }
}