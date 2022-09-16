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
//use Setup\Model\Insurance;
use Insurance\Model\InsuranceDtl;

class InsuranceDtlRepository implements RepositoryInterface
{

    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(InsuranceDtl::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        // print_r($model); die;
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [InsuranceDtl::INSURANCE_DTL_ID => $id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Insurance::class, [InsuranceDtl::INSURANCE_ENAME]), false);
            $select->where([InsuranceDtl::STATUS => EntityHelper::STATUS_ENABLED]);
            //$select->order([InsuranceDtl::INSURANCE_ENAME => Select::ORDER_ASCENDING]);
        });
    }
    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(InsuranceDtl::class, NULL, [
           InsuranceDtl::PREMIUM_DT
                ], NULL, NULL, NULL, 'I'), false);

        $select->from(['I' => InsuranceDtl::TABLE_NAME]);
        $select->where([InsuranceDtl::INSURANCE_DTL_ID => $id]);
        // $select->where([
        //     "I.INSURANCE_ID=" . $id
        // ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    

    public function getInsuranceDtlTable(){
        $sql = "select ins.*, 
        i.INSURANCE_ENAME,
        e.FULL_NAME
        FROM
        HRIS_EMPLOYEE_INSURANCE_DTL ins
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
        $this->tableGateway->update([InsuranceDtl::STATUS => 'D', InsuranceDtl::DELETED_BY => $deletedBy, InsuranceDtl::DELETED_DT => $deletedDt], [InsuranceDtl::INSURANCE_DTL_ID => $id]);
        // $sql = "update hris_employee_insurance_dtl set STATUS='D') where INSURANCE_DTL_ID = $id";
        // $statement = $this->adapter->query($sql);
        // $statement->execute();
    }

    public function getInsuranceTable(){
        $sql = "select * from HRIS_EMPLOYEE_INSURANCE
        where status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
}
