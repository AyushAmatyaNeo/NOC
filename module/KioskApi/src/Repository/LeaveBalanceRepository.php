<?php

namespace KioskApi\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class LeaveBalanceRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchLeaveDetails($employeeId) {
        $boundedParams = [];
        $sql = "
            SELECT LA.EMPLOYEE_ID,
            E.FULL_NAME, 
            LA.LEAVE_ID, 
            LMS.LEAVE_ENAME AS LEAVE_NAME,  
            LA.TOTAL_DAYS, 
            LA.BALANCE AS REMAINING_BALANCE
            FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA 
            LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS 
            ON LA.LEAVE_ID = LMS.LEAVE_ID 
            LEFT JOIN HRIS_EMPLOYEES E 
            ON LA.EMPLOYEE_ID = E.EMPLOYEE_ID 
            WHERE LA.EMPLOYEE_ID = :employeeId
            ORDER BY LA.LEAVE_ID
            ";

        $boundedParams['employeeId'] = $employeeId;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParams);
        return Helper::extractDbData($result);
    }

}
