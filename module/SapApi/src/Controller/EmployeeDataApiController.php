<?php

namespace SapApi\Controller;

use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Http\Request;
use Application\Controller\HrisController;
use Zend\Authentication\Storage\StorageInterface;
use Zend\View\Model\JsonModel;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use SapApi\Repository\EmployeeDataApiRepo;

class EmployeeDataApiController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(EmployeeDataApiRepo::class);
    }

    public function indexAction(){
        echo 'Welcome';
        die();
    }

    public function getEmployeeDataAction(){
        $request = $this->getRequest();
        $requestType = $request->getMethod();
        try{
            switch ($requestType) {
                case Request::METHOD_GET:
                    $data = Helper::extractDbData($this->repository->getAllEmployeeData());
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
