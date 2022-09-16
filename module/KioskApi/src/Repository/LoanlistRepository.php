<?php

namespace KioskApi\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class LoanlistRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchLoanList($employeeId) {
        $boundedParams = [];
        $sql = "
            SELECT DISTINCT LR.LOAN_ID,
            E.FULL_NAME,
            LMS.LOAN_NAME
            FROM HRIS_EMPLOYEE_LOAN_REQUEST LR
            LEFT JOIN HRIS_EMPLOYEES E
            ON LR.EMPLOYEE_ID = E.EMPLOYEE_ID
            LEFT JOIN HRIS_LOAN_MASTER_SETUP LMS 
            ON LR.LOAN_ID = LMS.LOAN_ID
            WHERE LR.EMPLOYEE_ID = :employeeId
            AND LR.STATUS = 'AP'
            ";

//        $sql = "select * from HRIS_LOAN_PAYMENT_DETAIL WHERE LOAN_REQUEST_ID = 2 AND PAID_FLAG = 'Y' ";

        $boundedParams['employeeId'] = $employeeId;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParams);
        return Helper::extractDbData($result);
    }

}
