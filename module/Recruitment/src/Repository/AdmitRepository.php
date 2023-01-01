<?php
namespace Recruitment\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Zend\Db\Sql\Sql;
use Application\Helper\EntityHelper;
use Recruitment\Model\AdmitModel;
use Zend\Db\Sql\Expression;

Class AdmitRepository extends HrisRepository{
    public function __construct(AdapterInterface $adapter, $tablename = null)
    {
        parent::__construct($adapter, AdmitModel::TABLE_NAME);
    }
    public function add(Model $options_data) 
    {
        $addData=$options_data->getArrayCopyForDB();
        $this->tableGateway->insert($addData);
    }
    
    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [AdmitModel::ADMIT_SETUP_ID => $id]);
    }
    public function fetch()
    {
        $sql = "SELECT *  FROM HRIS_REC_ADMIT_SETUP WHERE ADMIT_SETUP_ID = 1";
        $statement = $this->adapter->query($sql);
        $result    = $statement->execute();
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