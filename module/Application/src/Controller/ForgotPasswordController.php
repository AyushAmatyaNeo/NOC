<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use Exception;
use Zend\EventManager\EventManagerInterface;
use Application\Model\ForgotPassword;
use Zend\Db\Adapter\AdapterInterface;
use Application\Repository\ForgotPasswordRepository;
use System\Repository\UserSetupRepository;
use Application\Factory\ConfigInterface;
use Notification\Model\NotificationEvents;
use Notification\Controller\HeadNotification;
use Application\Custom\CustomViewModel;

class ForgotPasswordController extends AbstractActionController {

    private $adapter;
    private $repository;
    private $appConfig;

    public function __construct(AdapterInterface $adapter, ConfigInterface $appConfig) {
        $this->adapter = $adapter;
        $this->appConfig = $appConfig->getApplicationConfig();
        $this->repository = new ForgotPasswordRepository($adapter);
    }

    public function setEventManager(EventManagerInterface $events) {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/login');
        }, 100);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            switch ($postData->action) {
                case "checkCodeDetail":
                    $responseData = $this->checkCodeDetail($postData->data);
                    break;
                default:
                    $responseData = [
                        "success" => false
                    ];
                    break;
            }
            return new CustomViewModel($responseData);
        }
    }

    public function emailAction() {
        $userRepo = new UserSetupRepository($this->adapter);
        $result = $userRepo->fetchAll();
        $list = [];
        foreach ($result as $row) {
            $row['PASSWORD']='********';
            $row['ROLE_ID']='********';
            $row['FULL_NAME']='********';
            $row['ROLE_NAME']='********';
            array_push($list, $row);
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $username = $postData['username'];
			try {
                    $userDetail = $userRepo->fetchByUsername($username);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage('Invalid UserName');
                }
            
            if($userDetail!=null){
                $employeeId = $userDetail['EMPLOYEE_ID'];
                $code1 = Helper::generateUniqueName();
                $code = mt_rand(100000, 999999);
                $expiryDate = new \DateTime('now +1 day');
                $dt = $expiryDate->format('d-M-y h:i A');

                $detail = $this->repository->fetchByEmployeeId($employeeId);
                $forgotPasswordModel = new ForgotPassword();
                $forgotPasswordModel->employeeId = $employeeId;
                if ($detail == null) {
                    $forgotPasswordModel->code = $code;
                    $forgotPasswordModel->expiryDate = Helper::getExpressionDateTime($dt);
                    $this->repository->add($forgotPasswordModel);
                } else {
                    $forgotPasswordModel->code = $detail['CODE'];
                }
                try {
                    $forgotPasswordModel->expiryDate = $dt;
                    //error
                    // HeadNotification::pushNotification(NotificationEvents::FORGOT_PASSWORD, $forgotPasswordModel, $this->adapter);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
                $this->redirect()->toRoute("recover", [
                    'action' => "code",
                    'employeeId' => $employeeId
                ]);
            }else{
                $this->flashmessenger()->addMessage("There is no account registered for submitted username!!!");
                $this->redirect()->toRoute("recover", [
                    'action' => "email"
                ]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['userList' => $list]);
    }

    public function codeAction() {
        $request = $this->getRequest();
        $employeeId = $this->params()->fromRoute('employeeId');
        $detail = $this->repository->fetchByEmployeeId($employeeId);
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $this->redirect()->toRoute("recover", [
                'action' => "password",
                'employeeId' => $employeeId
            ]);
        }
        return Helper::addFlashMessagesToArray($this, [
                    "employeeId" => $employeeId,
                    "code" => $detail['CODE']
        ]);
    }

    public function passwordAction() {
        $employeeId = $this->params()->fromRoute('employeeId');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $userRepo = new UserSetupRepository($this->adapter);
            $encryptedPwd = Helper::encryptPassword($postData['password']);
            $userRepo->updateByEmpId($employeeId, $encryptedPwd);
            $this->flashmessenger()->addMessage("Your Password Successfully Reset!!!");
            $this->redirect()->toRoute("login");
        }
        return Helper::addFlashMessagesToArray($this, [
                    "employeeId" => $employeeId
        ]);
    }

    public function checkCodeDetail($data) {
        $employeeId = $data['employeeId'];
        $code = $data['code'];
        $detail = $this->repository->fetchByEmployeeId($employeeId);
        $detailCode = $detail['CODE'];
        $errorFlag = false;
        if ($code !== $detailCode) {
            $errorFlag = true;
            $msg = "* Enter Code doesn't match!!!";
        } else {
            $msg = "";
        }
        return [
            "success" => true,
            "data" => [
                "errorFlag" => $errorFlag,
                "msg" => $msg
            ]
        ];
    }

}
