<?php

namespace KioskApi\Controller;

use Exception;
use KioskApi\Repository\KioskPrintRepo;
use KioskApi\Repository\PaysheetRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class Paysheet extends AbstractActionController
{

    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function statusAction()
    {
        $responseData = [];
        $employeeDetail = [];
        $printRepository = new KioskPrintRepo($this->adapter);
        try {
            $request = $this->getRequest();

            $this->employeeId = $request->getHeader('EmployeeId')->getFieldValue();
            $data = $request->getPost();
            $count = $printRepository->fetchCount($data, $this->employeeId)[0]['COUNT'];

//            print_r($count);
//            die();

            $printRepository->insertData($data, $this->employeeId);
            if ($count < 7) {
                $requestType = $request->getMethod();

                switch ($requestType) {
                    case Request::METHOD_POST:
                        $responseData = $this->getSalaryDetail($this->employeeId);
                        $employeeDetail = $this->fetchEmployeeData($this->employeeId);
                        if ($responseData == NULL) {
                            return new JsonModel(['success' => true, 'salaryDetail' => $responseData, 'employeeDetail' => $employeeDetail, 'message' => 'No record found']);
                        }
                        break;
                    default :
                        throw new Exception('The request is unknown');
                }
                return new JsonModel(['success' => true, 'salaryDetail' => $responseData, 'employeeDetail' => $employeeDetail, 'message' => $requestType]);
            } else {
                return new JsonModel(['success' => true, 'salaryDetail' => null, 'employeeDetail' => null, 'message' => 'Print limit reached for current month']);
            }
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'salaryDetail' => $responseData, 'employeeDetail' => $employeeDetail, 'message' => $e->getMessage()]);
        }
    }

    public function getSalaryDetail($employeeId)
    {
        $statusRepo = new PaysheetRepository($this->adapter);

        return $statusRepo->fetchPaysheet($employeeId);
    }

    public function fetchEmployeeData($employeeId)
    {
        $statusRepo = new PaysheetRepository($this->adapter);

        return $statusRepo->fetchEmployeeDetail($employeeId);
    }

}
