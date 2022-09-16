<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\ServiceEventType;

class ServiceEventTypeRepository implements RepositoryInterface
{
    private $tableGateway;
    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway(ServiceEventType::TABLE_NAME,$adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array=$model->getArrayCopyForDB();
        unset($array[ServiceEventType::SERVICE_EVENT_TYPE_ID]);
        unset($array[ServiceEventType::CREATED_DT]);
        $this->tableGateway->update( $array,[ServiceEventType::SERVICE_EVENT_TYPE_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select([ServiceEventType::SERVICE_EVENT_TYPE_ID=>$id]);
        return $rowset->current();
    }
    public function fetchActiveRecord()
    {
        return  $rowset= $this->tableGateway->select([ServiceEventType::STATUS=>'E']);
    }

    public function delete($id)
    {
        $this->tableGateway->update([ServiceEventType::STATUS=>'D'],[ServiceEventType::SERVICE_EVENT_TYPE_ID=>$id]);
    }
}