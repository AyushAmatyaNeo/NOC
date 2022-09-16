<?php

namespace Setup\Repository;

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
use Setup\Model\Insurance;

class InsuranceRepository implements RepositoryInterface
{

    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(Insurance::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        // print_r($model); die;
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        //print("hello"); die;
        $array = $model->getArrayCopyForDB();
        //print($array); die;
        $this->tableGateway->update($array, [Insurance::INSURANCE_ID => $id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Insurance::class, [Insurance::INSURANCE_ENAME]), false);
            $select->where([Insurance::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->order([Insurance::INSURANCE_ENAME => Select::ORDER_ASCENDING]);
        });
    }
    public function fetchById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Insurance::class, NULL, [
                    Insurance::ELIGIBLE_AFTER
                ], NULL, NULL, NULL, 'I'), false);

        $select->from(['I' => Insurance::TABLE_NAME]);
        $select->where([
            "I.INSURANCE_ID=" . $id
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function delete($id)
    {
        $this->tableGateway->update([Insurance::STATUS => 'D'], [Insurance::INSURANCE_ID => $id]);
        $sql = "update hris_insurance_setup set status='D' where INSURANCE_ID = $id";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }

    public function getInsuranceTableData(){
        $sql = "select hi.*,hs.service_type_name, case when hi.open='N' 
        then 'No' else 'Yes' end as is_open,
        case when hi.TYPE='FW' 
        then 'Falt Wise' else 'Salary Wise' end as v_type
        from hris_insurance_setup hi
         join hris_service_types hs on hi.SERVICE_TYPE_ID = hs.SERVICE_TYPE_ID where hi.status='E'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
}
