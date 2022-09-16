<?php

namespace Payroll\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\MonthlyValue;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\EntityHelper;
use Zend\Db\Sql\Select;

class MonthlyValueRepository implements RepositoryInterface
{
    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter=$adapter;
        $this->tableGateway = new TableGateway(MonthlyValue::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $this->tableGateway->update($model->getArrayCopyForDB(),[MonthlyValue::MTH_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function(Select $select){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(MonthlyValue::class,
                    [MonthlyValue::MTH_EDESC, MonthlyValue::MTH_LDESC]),false);
            $select->where([MonthlyValue::STATUS=>'E']);
        });
    }

    public function fetchById($id)
    {
        return $this->tableGateway->select(function(Select $select)use($id){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(MonthlyValue::class,
                    [MonthlyValue::MTH_EDESC, MonthlyValue::MTH_LDESC]),false);
            $select->where([MonthlyValue::MTH_ID=>$id]);
        })->current();
    }

    public function delete($id)
    {
        return $this->tableGateway->update([MonthlyValue::STATUS=>'D'],[MonthlyValue::MTH_ID=>$id]);
    }
}