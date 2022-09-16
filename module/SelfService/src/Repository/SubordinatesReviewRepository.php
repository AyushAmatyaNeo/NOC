<?php

namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;

class SubordinatesReviewRepository implements RepositoryInterface {
    
    private $adapter;

    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter=$adapter;
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

}
