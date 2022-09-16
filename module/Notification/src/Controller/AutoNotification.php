<?php

namespace Notification\Controller;

use Application\Custom\CustomViewModel;
use Application\Factory\ConfigInterface;
use Application\Helper\Helper;
use Notification\Model\Notification;
use Notification\Repository\NotificationRepo;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Notification\Controller\HeadNotification;

class AutoNotification extends AbstractActionController {

    private $notiRepo;
    private $employeeId;
    private $adapter;
    private $config;

    public function __construct(AdapterInterface $adapter, ConfigInterface $config) {
        $this->adapter = $adapter;
        $this->config = $config;

        $this->notiRepo = new NotificationRepo($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $this->params()->fromRoute('id');
            if($id){
                // HeadNotification::haha();
                print_r($id);die;
            }else{
                print_r('pass notification head id');die;
            }
        } else {
            print_r('it is a get request');die;
        }
        return new CustomViewModel($response);
    }

    

}
