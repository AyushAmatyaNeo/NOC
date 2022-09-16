<?php

namespace Application\Repository;


use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;

interface  RepositoryInterface {

    public function __construct(AdapterInterface $adapter);

    public function add(Model $model);
    public function edit(Model $model, $id);
    public function fetchAll();
    public function fetchById($id);
    public function delete($id);
}