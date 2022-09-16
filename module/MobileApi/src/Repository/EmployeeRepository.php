<?php

namespace MobileApi\Repository;

use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;

class EmployeeRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function getApproval($employeeId) {
        $sql = "
                SELECT E.EMPLOYEE_ID,
                  E.FULL_NAME
                FROM HRIS_EMPLOYEES E,
                  (SELECT EMPLOYEE_ID,
                    APPROVED_BY,
                    RECOMMEND_BY
                  FROM HRIS_RECOMMENDER_APPROVER
                  WHERE EMPLOYEE_ID ={$employeeId}
                  ) I
                WHERE E.STATUS         ='E'
                AND E.RETIRED_FLAG     ='N'
                AND E.EMPLOYEE_ID NOT IN (I.EMPLOYEE_ID,I.RECOMMEND_BY,I.APPROVED_BY)
";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
