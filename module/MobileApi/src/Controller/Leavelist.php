<?php

namespace MobileApi\Controller;

use Exception;
use MobileApi\Repository\LeavelistRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;



class Leavelist extends AbstractActionController {

    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
public function indexAction(){
//            echo 'Wel Come ';
//      die();

}

public function statusAction() {
//     echo'Welcome Status';
//      die();
      try{
       $request = $this->getRequest();
       
//        print_r($request);
//              die();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();
            
//              print_r($this->employeeId);
//              die();
            
            
            $requestType = $request->getMethod();
//            print_r($requestType);
//            die();
            
            $responseDate = [];
//           print_r($responseDate);
//           die();
            
             switch ($requestType) {
                case Request::METHOD_GET:
                   
                    $responseDate = $this->getStatus($this->employeeId);
					
	
                    break;
                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseDate, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }

      
}

private function getStatus($employeeId) {
        $StatusRepo = new LeavelistRepository($this->adapter);
        // $getdate=$StatusRepo-> getMonthDate();
         
        return $StatusRepo->fetchEmployeeLeaveList($employeeId );
    }

}
