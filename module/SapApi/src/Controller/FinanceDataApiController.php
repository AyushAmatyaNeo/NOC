<?php

namespace SapApi\Controller;

use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Http\Request;
use Zend\Authentication\Storage\StorageInterface;
use Application\Controller\HrisController;
use Zend\View\Model\JsonModel;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use SapApi\Repository\FinanceDataApiRepo;

class FinanceDataApiController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(FinanceDataApiRepo::class);
    }

    public function indexAction(){
        echo 'Welcome';
        die();
    }

    public function getHrisFinancialDataAction(){
        $request = $this->getRequest();
        $requestType = $request->getMethod();
        try{
            switch ($requestType) {
                case Request::METHOD_GET:
                    $data = Helper::extractDbData($this->repository->fetchHrisGlEntry());
                    break;

                default:
                throw new \Exception('Unavailable Request.');
            }
            
            return new JsonModel(['data' => $data, 'error' => null]);
        }
        catch(\Exception $e){
            return new JsonModel(['data' => null, 'error' => $e]);
        }
    }    
}
