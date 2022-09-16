<?php

namespace Notification\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Notification\Model\EmailTemplate;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class EmailTemplateRepo implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(EmailTemplate::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        return $this->tableGateway->update($model->getArrayCopyForDB(), [EmailTemplate::ID => $id]);
    }

    public function fetchAll() {
        $templates = $this->tableGateway->select();
        return Helper::extractDbData($templates, TRUE, EmailTemplate::ID);
    }

    public function fetchById($id) {
        return $this->tableGateway->select([EmailTemplate::ID => $id])->current();
    }

}
