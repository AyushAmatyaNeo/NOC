<?php

namespace Application\Controller;

use Application\Repository\ApiRepository;
use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ApiAttendanceController extends AbstractRestfulController {

    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
		die();
        $this->adapter = $adapter;
        $this->repository = new ApiRepository($adapter);
    }

    public function indexAction() {
        print "Welcome";
        exit;
    }

    public function dailyAction() {
        try {
            $request = $this->getRequest();
            $requestType = $request->getMethod();
            $data = [];
            switch ($requestType) {

                case Request::METHOD_GET:
                    $year = $this->params()->fromRoute('year');
                    $month = $this->params()->fromRoute('month');
                    $day = $this->params()->fromRoute('day');
                    $employeeCode = $this->params()->fromRoute('employeeCode');

                    if ($year == NULL || $month == NULL || $day == NULL) {
                        throw new Exception(" year month and day must be passed");
                    }

                    if ( !is_numeric($year) || !is_numeric($month) || !is_numeric($day) || !checkdate($month, $day, $year)) {
                        throw new Exception("Please provide a valid YYYY/MM/DD date value");
                    }

                    $date = $year . '-' . $month . '-' . $day;
                    $data = $this->repository->fetchAttendance($date,$employeeCode);

                    break;

                default:
                    throw new Exception('Unknown request');
            }
            
            if(empty($data)){
                $data='No record found';
            }
            return new JsonModel(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

}
