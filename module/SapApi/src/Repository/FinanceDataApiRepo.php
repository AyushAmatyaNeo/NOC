<?php

namespace SapApi\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Application\Repository\HrisRepository;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class FinanceDataApiRepo extends HrisRepository{

    public function __construct(AdapterInterface $adapter) {
        parent::__construct($adapter);
    }

    public function fetchHrisGlEntry(){
        $sql = "select * from HRIS_GL_ENTRY where 1=1 and sync = 'N'";
        return $this->rawQuery($sql, $boundedParameters);
    }
}
