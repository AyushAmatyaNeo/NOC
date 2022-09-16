<?php

namespace MobileApi\Controller;

use AttendanceManagement\Model\Attendance as AttdModel;
use AttendanceManagement\Repository\AttendanceRepository;
use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class Attendance extends AbstractActionController {

    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
public function indexAction(){
            echo 'Wel Come ';
      die();

}
    public function insertAction() {
        try {
            $request = $this->getRequest();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();
            $requestType = $request->getMethod();
           
            $data = json_decode($request->getContent());
            $responseData = [];
            switch ($requestType) {
                case Request::METHOD_POST:
                    $responseData = $this->attendanceInsert($data);
                    break;

                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseData, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    private function attendanceInsert($data) {

        $attendanceModel = new AttdModel();
        $attendanceModel->employeeId = $this->employeeId;
        $attendanceModel->attendanceDt = new Expression('TRUNC(SYSDATE)');
        //$attendanceModel->attendanceFrom = $data->attendanceFrom;
        $attendanceModel->attendanceFrom = 'HRIS AAP';
        $attendanceModel->attendanceTime =new Expression('SYSTIMESTAMP');
        $attendanceModel->location = $data->location;

        $attendanceRepositiry = new AttendanceRepository($this->adapter);

        return $attendanceRepositiry->add($attendanceModel);
    }
    

}
