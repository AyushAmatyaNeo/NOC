<?php

namespace System\Repository;

use Application\Model\Model;
use Application\Model\Preference;
use System\Repository\Hana\SystemSettingRepository as Hana;
use System\Repository\Oracle\SystemSettingRepository as Oracle;
use Zend\Db\Adapter\AdapterInterface;

class SystemSettingRepository {

    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        
        
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

    public function fetch() {
         return $this->repository->fetch();
    }

    public function edit(Model $model) {
         return $this->repository->edit($model);
    }

}
