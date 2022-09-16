<?php

namespace MobileApi\Controller;

use Exception;
use MobileApi\Repository\NotificationRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class Notification extends AbstractActionController {

    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function setupAction() {
        try {
            $request = $this->getRequest();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();

            $requestType = $request->getMethod();
            $responseDate = [];

            switch ($requestType) {
                case Request::METHOD_GET:
                    $responseDate = $this->getNotification($this->employeeId);
                    break;
                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseDate, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    private function getNotification($employeeId) {
        $notificationRepo = new NotificationRepository($this->adapter);
        return $notificationRepo->getNotification($employeeId);
    }

}
