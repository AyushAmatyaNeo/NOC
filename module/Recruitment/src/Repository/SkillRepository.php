<?php
namespace Recruitment\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Zend\Db\Sql\Sql;
use Application\Helper\EntityHelper;
use Recruitment\Model\SkillModel;
use Zend\Db\Sql\Expression;

Class SkillRepository extends HrisRepository{
    public function __construct(AdapterInterface $adapter, $tablename = null)
    {
        parent::__construct($adapter, SkillModel::TABLE_NAME);
    }
    public function add(Model $options_data) 
    {
        $addData=$options_data->getArrayCopyForDB();
        $this->tableGateway->insert($addData);
    }
    public function getFilteredRecords($search)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.SKILL_ID AS SKILL_ID"),
            new Expression("REC.SKILL_CODE AS SKILL_CODE"),
            new Expression("REC.SKILL_NAME AS SKILL_NAME"),
            new Expression("REC.REQUIRED_FLAG AS REQUIRED_FLAG"),
            new Expression("REC.UPLOAD_FLAG AS UPLOAD_FLAG"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),       
            ], true); 

        $select->from(['REC' => SkillModel::TABLE_NAME])   
                ->where(["REC.STATUS='E'"]);
            // if (($search['OptionEdesc'] != null)) {
            //     $select->where([
            //         "REC.OPTION_EDESC" => ucfirst($search['OptionEdesc'])
            //     ]);
            // }

        $select->order("REC.SKILL_ID ASC");
        $boundedParameter = [];
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        // echo'<pre>'; print_r($array); die;
        $this->tableGateway->update($array, [SkillModel::SKILL_ID => $id]);
    }
    public function fetchById($id)
    {
        $sql = new sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.SKILL_ID AS SKILL_ID"),
            new Expression("REC.SKILL_CODE AS SKILL_CODE"),
            new Expression("REC.SKILL_NAME AS SKILL_NAME"),
            new Expression("REC.REQUIRED_FLAG AS REQUIRED_FLAG"),
            new Expression("REC.UPLOAD_FLAG AS UPLOAD_FLAG"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),
        ], true);

        $select->from(['REC' => SkillModel::TABLE_NAME]);
        $select->where(["REC.SKILL_ID='{$id}'"]);
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);

        return $result->current();
    }
    public function delete(Model $model ,$id)
    {
        $array = $model->getArrayCopyForDB();
        $deleted_dt = date('Y-m-d');
        $rawsql = "UPDATE HRIS_REC_SKILL  SET STATUS = 'D', DELETED_BY = {$array['DELETED_BY']}, DELETED_DT = '{$deleted_dt}' WHERE SKILL_ID = {$id}";
        // echo '<pre>'; print_r($rawsql); die;
        return EntityHelper::rawQueryResult($this->adapter, $rawsql);
    }
}