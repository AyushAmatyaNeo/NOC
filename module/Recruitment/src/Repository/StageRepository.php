<?php
namespace Recruitment\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Recruitment\Model\StageModel;
use Recruitment\Model\EmployeeStagePermission;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;


class StageRepository extends HrisRepository {
    public function __construct(AdapterInterface $adapter, $tablename = null)
    {
        parent::__construct($adapter, StageModel::TABLE_NAME);
        $this->employeeStagePermissionGateway = new TableGateway(EmployeeStagePermission::TABLE_NAME, $adapter);
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
            new Expression("REC.REC_STAGE_ID AS REC_STAGE_ID"),
            new Expression("REC.STAGE_EDESC AS STAGE_EDESC"),
            new Expression("REC.STAGE_NDESC AS STAGE_NDESC"),
            new Expression("REC.ORDER_NO AS ORDER_NO"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),       
            ], true);

        $select->from(['REC' => StageModel::TABLE_NAME])   
                ->where(["REC.STATUS='E'"]);
            
        //     if (($search['stage'] != null)) {
        //     $select->where([
        //         "REC.STAGE_EDESC" => $search['stage']
        //     ]);
        // }

        //Another if
        if (($search['stage'] != null)) {
                $select->where("stage_edesc like '(%{$search['stage']}%)'");
            }

        $select->order("REC.ORDER_NO ASC");
        $boundedParameter = [];
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        // echo'<pre>'; print_r($result); die;
        return $result;
    }
    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        // echo'<pre>'; print_r($array); die;
        $this->tableGateway->update($array, [StageModel::REC_STAGE_ID => $id]);
    }
    public function fetchById($id)
    {
        $sql = new sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.REC_STAGE_ID AS REC_STAGE_ID"),
            new Expression("REC.STAGE_EDESC AS STAGE_EDESC"),
            new Expression("REC.STAGE_NDESC AS STAGE_NDESC"),
            new Expression("REC.ORDER_NO AS ORDER_NO"),

        ], true);

        $select->from(['REC' => StageModel::TABLE_NAME]);
        $select->where(["REC.REC_STAGE_ID='{$id}'"]);
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);

        return $result->current();
    }
    public function delete(Model $model ,$id)
    {
        $array = $model->getArrayCopyForDB();
        $deleted_dt = date('Y-m-d');
        $rawsql = "UPDATE HRIS_REC_STAGES  SET STATUS = 'D', DELETED_BY = {$array['DELETED_BY']}, DELETED_DT = '{$deleted_dt}' WHERE REC_STAGE_ID = {$id}";
        // echo '<pre>'; print_r($rawsql); die;
        return EntityHelper::rawQueryResult($this->adapter, $rawsql);
    }

    public function addEmployeeStagePermission(Model $model) 
    {
        $rawsql = "Delete from HRIS_REC_EMPLOYEE_STAGE_PERMISSION where employee_id = {$model->employeeId}";
        EntityHelper::rawQueryResult($this->adapter, $rawsql);
        $addData=$model->getArrayCopyForDB();
        $this->employeeStagePermissionGateway->insert($addData);
    }

}