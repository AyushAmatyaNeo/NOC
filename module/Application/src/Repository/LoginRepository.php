<?php

namespace Application\Repository;

use Application\Model\Model;
use Application\Repository\Hana\LoginRepository as Hana;
use Application\Repository\Oracle\LoginRepository as Oracle;
use Zend\Db\Adapter\AdapterInterface;

class LoginRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
//        $this->tableGateway = new TableGateway(UserSetup::TABLE_NAME, $adapter);
        $platformName = $adapter->getPlatform()->getName();
        switch ($platformName) {
            case "Oracle":
                $this->repository = new Oracle($adapter);
                break;
            case "odbc":
                $this->repository = new Hana($adapter);
                break;
        }
    }

    public function add(Model $model) {
        return $this->repository->add($model);
    }

    public function delete($id) {
        return $this->repository->delete($id);
    }

    public function edit(Model $model, $id) {
       return $this->repository->edit( $model, $id);
    }

    public function fetchAll() {
        return $this->repository->fetchAll();
    }

    public function fetchById($id) {
        return $this->repository->fetchById($id);
    }

    public function fetchByUserName($userName,$pwd=NULL) {
        return $this->repository->fetchByUserName($userName,$pwd=NULL);
    }

    public function updateByUserName($userName) {
        return $this->repository->updateByUserName($userName);
    }

    public function checkPasswordExpire($userName,$pwd=NULL) {
        return $this->repository->checkPasswordExpire($userName,$pwd=NULL);
    }

    public function getPwdByUserName($userName) {
        return $this->repository->getPwdByUserName($userName);
    }

    public function updatePwdByUserName($un, $pwd) {
        return $this->repository->updatePwdByUserName($un, $pwd);
    }

}
