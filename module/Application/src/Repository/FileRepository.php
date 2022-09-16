<?php

namespace Application\Repository;

use Application\Model\Files;
use Application\Model\Model;
use ArrayObject;
use Zend\Db\Adapter\AdapterInterface;

class FileRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter) {
        parent::__construct($adapter, Files::TABLE_NAME);
    }

    public function add(Model $model) {
        $data = $model->getArrayCopyForDB();
        $this->tableGateway->insert($data);
    }

    public function fetchById($id): ArrayObject {
        return $this->tableGateway->select([Files::FILE_ID => $id])->current();
    }

}
