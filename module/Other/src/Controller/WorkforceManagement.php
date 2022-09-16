<?php

namespace Other\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class WorkforceManagement extends AbstractActionController {

    private $adapter;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

}
