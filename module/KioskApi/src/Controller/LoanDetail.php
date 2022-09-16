<?php

namespace KioskApi\Controller;

use Exception;
use KioskApi\Repository\KioskPrintRepo;
use KioskApi\Repository\LoanDetailRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class LoanDetail extends AbstractActionController
{

    private $adapter;
    private $employeeId;
    private $loanId;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function statusAction()
    {

        $responseData = [];
        try {
            $printRepository = new KioskPrintRepo($this->adapter);
            $request = $this->getRequest();

            $this->employeeId = $request->getHeader('EmployeeId')->getFieldValue();
//            $this->loanId = $request->getHeader('LoanId')->getFieldValue();

            $data = $request->getPost();
            $count = $printRepository->fetchCount($data, $this->employeeId)[0]['COUNT'];

            print_r($count);
            die();

            $printRepository->insertData($data, $this->employeeId);

            if ($count < 2) {
                $requestType = $request->getMethod();

                switch ($requestType) {
                    case Request::METHOD_POST:
                        $responseData = $this->getStatus($this->employeeId, $data['LoanId']);
                        if ($responseData == NULL) {
                            return new JsonModel(['success' => true, 'data' => $responseData, 'message' => 'No record found']);
                        }
                        break;
                    default :
                        throw new Exception('The request is unknown');
                }
                return new JsonModel(['success' => true, 'data' => $responseData, 'message' => $requestType]);
            } else {
                return new JsonModel(['success' => true, 'data' => null, 'message' => 'Print limit reached for current month']);
            }
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => $responseData, 'message' => $e->getMessage()]);
        }
    }

    public function getStatus($employeeId, $loanId)
    {
        $statusRepo = new LoanDetailRepository($this->adapter);

        return $statusRepo->fetchLoanDetail($employeeId, $loanId);
    }

}
