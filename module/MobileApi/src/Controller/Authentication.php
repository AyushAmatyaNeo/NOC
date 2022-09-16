<?php

namespace MobileApi\Controller;

use Application\Factory\ConfigInterface;
use AttendanceManagement\Model\Attendance;
use AttendanceManagement\Repository\AttendanceRepository;
use MobileApi\Repository\AuthRepository;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class Authentication extends AbstractActionController {

    private $adapter;
    private $config;
    private $employeeId;

    public function __construct(AdapterInterface $adapter, ConfigInterface $config) {
        $this->adapter = $adapter;
        $this->config = $config->getApplicationConfig();
    }

    public function indexAction() {
        $request = $this->getRequest();
        $data = json_decode($request->getContent());
//        print_r( $data);
//        die();
        $temp = new CredentialTreatmentAdapter($this->adapter, 'HRIS_USERS', 'USER_NAME', 'FN_DECRYPT_PASSWORD(PASSWORD)');
        $temp->setIdentity($data->username)->setCredential($data->password);
        $result = $temp->authenticate();

        $response = ['success' => false, 'data' => null, 'message' => null];

        if ($result->isValid()) {
            $response['success'] = true;
            $resultRow = $temp->getResultRowObject();

            $authRepo = new AuthRepository($this->adapter);
            $userProfile = $authRepo->getUserProfile($resultRow->EMPLOYEE_ID);
            $userProfile['PROFILE_PICTURE_PATH'] = 'http://' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . '/uploads/' . (isset($userProfile['FILE_PATH']) ? $userProfile['FILE_PATH'] : $this->config['default-profile-picture']);
            $userProfile['USER_ID'] = $resultRow->USER_ID;
            $userProfile['ROLE_ID'] = $resultRow->ROLE_ID;

            $response['data'] = $userProfile;
            $this->employeeId=$resultRow->EMPLOYEE_ID;
            // condition check 
            
            if ($data->condition=="Y"){
            $this->attendanceInsert($data);
            }
        }

        foreach ($result->getMessages() as $message) {
            $response['message'] = $response['message'] . $message;
        }

        return new JsonModel($response);
    }
    
    
    public function attendanceInsert($data) {

        $attendanceModel = new Attendance();
        $attendanceModel->employeeId = $this->employeeId;
        $attendanceModel->attendanceDt = new Expression('TRUNC(SYSDATE)');
        $attendanceModel->attendanceFrom = 'HRIS AAP';
        $attendanceModel->attendanceTime =new Expression('SYSTIMESTAMP');
        $attendanceModel->location = $data->location;

        $attendanceRepositiry = new AttendanceRepository($this->adapter);

        
//         print_r($data);
//    print_r($attendanceModel);
//    die();
        return $attendanceRepositiry->add($attendanceModel);
    }
    
    
    

}
