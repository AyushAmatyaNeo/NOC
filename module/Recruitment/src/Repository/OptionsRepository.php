<?php
namespace Recruitment\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Zend\Db\Sql\Sql;
use Application\Helper\EntityHelper;
use Recruitment\Model\OptionsModel;
use Zend\Db\Sql\Expression;

class OptionsRepository extends HrisRepository
{
    public function __construct(AdapterInterface $adapter, $tablename = null)
    {
        parent::__construct($adapter, OptionsModel::TABLE_NAME);
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
            new Expression("REC.OPTION_ID AS OPTION_ID"),
            new Expression("REC.OPTION_EDESC AS OPTION_EDESC"),
            new Expression("REC.OPTION_NDESC AS OPTION_NDESC"),
            new Expression("REC.REMARKS AS REMARKS"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),       
            ], true);

        $select->from(['REC' => OptionsModel::TABLE_NAME])   
                ->where(["REC.STATUS='E'"]);
            if (($search['OptionEdesc'] != null)) {
                $select->where([
                    "REC.OPTION_EDESC" => ucfirst($search['OptionEdesc'])
                ]);
            }

        $select->order("REC.OPTION_ID ASC");
        $boundedParameter = [];
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);
        return $result;
    }
    public function fetchById($id)
    {
        $sql = new sql($this->adapter);
        $select = $sql->select();
        $select->columns([            
            new Expression("REC.OPTION_ID AS OPTION_ID"),
            new Expression("REC.OPTION_EDESC AS OPTION_EDESC"),
            new Expression("REC.OPTION_NDESC AS OPTION_NDESC"),
            new Expression("REC.UPLOAD_FLAG AS UPLOAD_FLAG"),
            new Expression("REC.REMARKS AS REMARKS"),
            new Expression("(CASE WHEN REC.STATUS= 'E' THEN 'ENABLE' ELSE 'DISABLE' END) AS STATUS"),
            // new Expression("REC.STATUS AS STATUS"),
        ], true);

        $select->from(['REC' => OptionsModel::TABLE_NAME]);
        $select->where(["REC.OPTION_ID='{$id}'"]);
        $boundedParameter = [];
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($boundedParameter);

        return $result->current();


    }
    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [OptionsModel::OPTION_ID => $id]);
        
    }

    public function delete(Model $model, $id) 
    {
        $array = $model->getArrayCopyForDB();
        $deleted_dt = date('Y-m-d');
        $rewardSql = "update HRIS_REC_OPTIONS SET status = 'D', Deleted_By = {$array["DELETED_BY"]} , Deleted_Dt = '{$deleted_dt}' WHERE OPTION_ID = {$id}";
        
        // print_r($rewardSql); die();
        return EntityHelper::rawQueryResult($this->adapter, $rewardSql);
    }
}