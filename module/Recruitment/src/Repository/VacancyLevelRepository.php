<?php
namespace Recruitment\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Zend\Db\Sql\Sql;
use Application\Helper\EntityHelper;
use Recruitment\Model\VacancyLevelModel;
use Zend\Db\Sql\Expression;

class VacancyLevelRepository extends HrisRepository
{
    public function __construct(AdapterInterface $adapter, $tableName = null) 
    {
        parent::__construct($adapter, VacancyLevelModel::TABLE_NAME);
    }
    public function add(Model $options_data) 
    {
        $addData=$options_data->getArrayCopyForDB();
        // echo '<pre>';   print_r($addData);die;
        $this->tableGateway->insert($addData);

    }

    public function getFilteredRecords($search)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("REC.VACANCY_LEVEL_ID AS VACANCY_LEVEL_ID"),
            new Expression("REC.FUNCTIONAL_LEVEL_ID AS FUNCTIONAL_LEVEL_ID"),
            new Expression("HD.DESIGNATION_TITLE AS POSITION_ID"),
            new Expression("REC.EFFECTIVE_DATE AS EFFECTIVE_DATE"),
            new Expression("REC.NORMAL_AMOUNT AS NORMAL_AMOUNT"),
            new Expression("REC.INCLUSION_AMOUNT AS INCLUSION_AMOUNT"),
            new Expression("REC.LATE_AMOUNT AS LATE_AMOUNT"),
            new Expression("REC.MIN_AGE AS MIN_AGE"),
            new Expression("REC.MAX_AGE AS MAX_AGE"),
            ], true);

        $select->from(['REC' => VacancyLevelModel::TABLE_NAME])
                ->join(['FL' => 'HRIS_FUNCTIONAL_LEVELS'],'FL.FUNCTIONAL_LEVEL_ID=REC.FUNCTIONAL_LEVEL_ID', 'FUNCTIONAL_LEVEL_NO', 'left')
                ->join(['HD' => 'HRIS_DESIGNATIONS'], 'HD.DESIGNATION_ID = REC.POSITION_ID', 'DESIGNATION_CODE','LEFT')
                ->where(["REC.STATUS='E'"]);

        if (($search['vacancylevel'] != null)) {
            $select->where("FUNCTIONAL_LEVEL_NO = '{$search['vacancylevel']}'");
        }
        $select->order("FL.FUNCTIONAL_LEVEL_NO ASC");
        $boundedParameter = [];
        
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo'<pre>'; print_r($statement); die;
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function fetchById($id)
    {
        $sql = new sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.VACANCY_LEVEL_ID AS VACANCY_LEVEL_ID"),
            new Expression("REC.FUNCTIONAL_LEVEL_ID AS FUNCTIONAL_LEVEL_ID"),
            new Expression("REC.OPENING_ID AS OPENING_ID"),
            new Expression("INITCAP(TO_CHAR(REC.EFFECTIVE_DATE, 'DD-MON-YYYY')) AS EFFECTIVE_DATE"),
            new Expression("REC.POSITION_ID AS POSITION_ID"),
            new Expression("REC.NORMAL_AMOUNT AS NORMAL_AMOUNT"),
            new Expression("REC.INCLUSION_AMOUNT AS INCLUSION_AMOUNT"),
            new Expression("REC.LATE_AMOUNT AS LATE_AMOUNT"),
            new Expression("REC.MIN_AGE  AS MIN_AGE"),
            new Expression("REC.MAX_AGE AS MAX_AGE"),

        ], true);

        $select->from(['REC' => VacancyLevelModel::TABLE_NAME]);
        $select->where(["REC.VACANCY_LEVEL_ID='{$id}'"]);
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result->current();
    }
    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        // echo'<pre>'; print_r($array); die;
        $this->tableGateway->update($array, [VacancyLevelModel::VACANCY_LEVEL_ID => $id]);
    }
    public function delete(Model $model ,$id)
    {
        $array = $model->getArrayCopyForDB();
        $deleted_dt = date('Y-m-d');
        $rawsql = "UPDATE HRIS_REC_VACANCY_LEVELS  SET STATUS = 'D', DELETED_BY = {$array['DELETED_BY']}, DELETED_DATE = '{$deleted_dt}' WHERE VACANCY_LEVEL_ID = {$id}";
        // echo '<pre>'; print_r($rawsql); die;
        return EntityHelper::rawQueryResult($this->adapter, $rawsql);
    }

    public function pullLevelData($id){
        $sql = new sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.FUNCTIONAL_LEVEL_ID AS FUNCTIONAL_LEVEL_ID"),
            new Expression("REC.FUNCTIONAL_LEVEL_NO AS FUNCTIONAL_LEVEL_NO"),
        ], true);
        $select->from(['REC' =>'HRIS_FUNCTIONAL_LEVELS']);
        $select->where(["REC.DESIGNATION_LID LIKE '%$id%'"]);
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo('<pre>');print_r($statement);die;
        $result = $statement->execute($boundedParameter);
        return $result;
    }
}