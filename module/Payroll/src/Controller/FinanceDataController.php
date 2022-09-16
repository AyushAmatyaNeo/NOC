<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Zend\View\Model\JsonModel;
use Payroll\Repository\FinanceDataRepository;
use Zend\Http\Request;

class FinanceDataController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(FinanceDataRepository::class);
    }

    public function indexAction() {
        echo 'no action'; die;
    }
}