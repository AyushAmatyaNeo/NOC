<?php

namespace Insurance\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\HrEmployees;
use Insurance\Model\InsuranceEmployee;

class InsuranceEmpRepository implements RepositoryInterface
{

    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(InsuranceEmployee::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {

        //print_r($model); die;
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [InsuranceEmployee::INSURANCE_EMP_ID => $id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Insurance::class, [InsuranceEmployee::INSURANCE_ENAME]), false);
            $select->where([InsuranceEmployee::STATUS => EntityHelper::STATUS_ENABLED]);
            //$select->order([InsuranceDtl::INSURANCE_ENAME => Select::ORDER_ASCENDING]);
        });
    }
    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(InsuranceEmployee::class, NULL, [
            InsuranceEmployee::INSURANCE_DT,
            InsuranceEmployee::MATURED_DT
                ], NULL, NULL, NULL, 'I'), false);

        $select->from(['I' => InsuranceEmployee::TABLE_NAME]);
        $select->where([InsuranceEmployee::INSURANCE_EMP_ID => $id]);
        // $select->where([
        //     "I.INSURANCE_ID=" . $id
        // ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    

    public function getInsuranceEmpTable(){
        $sql = "select ins.*, 
        i.INSURANCE_ENAME,
        e.FULL_NAME,
        case when ins.completed='N' 
        then 'No' else 'Yes' end as IS_COMPLETED
        FROM
        HRIS_EMPLOYEE_INSURANCE ins
        LEFT JOIN HRIS_INSURANCE_SETUP i
        ON (INs.INSURANCE_ID = I.INSURANCE_ID)
        LEFT JOIN HRIS_EMPLOYEES E ON (INs.EMPLOYEE_ID = E.EMPLOYEE_ID)
        where INs.status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
    
    public function delete($id)
    {
       
    }

    public function deleteById($id, $model)
    {
        $deletedBy = $model->deletedBy;
        $deletedDt = $model->deletedDt;
        $this->tableGateway->update([InsuranceEmployee::STATUS => 'D', InsuranceEmployee::DELETED_BY => $deletedBy, InsuranceEmployee::DELETED_DT => $deletedDt], [InsuranceEmployee::INSURANCE_EMP_ID => $id]);
        // $sql = "update hris_employee_insurance_dtl set STATUS='D') where INSURANCE_DTL_ID = $id";
        // $statement = $this->adapter->query($sql);
        // $statement->execute();
    }
  
}
