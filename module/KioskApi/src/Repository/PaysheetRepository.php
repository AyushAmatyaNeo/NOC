<?php

namespace KioskApi\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class PaysheetRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchPaysheet($employeeId) {
        $boundedParams = [];
        $sql = "
           SELECT TS.*,
P.PAY_TYPE_FLAG,
P.PAY_EDESC
FROM HRIS_SALARY_SHEET_DETAIL TS
LEFT JOIN HRIS_PAY_SETUP P
ON (TS.PAY_ID =P.PAY_ID)
WHERE P.INCLUDE_IN_SALARY='Y' AND TS.VAL >0
AND TS.SHEET_NO IN
(SELECT SHEET_NO FROM HRIS_SALARY_SHEET WHERE MONTH_ID =13
AND SALARY_TYPE_ID=1
) AND P.pay_type_flag!='V' 
AND EMPLOYEE_ID = :employeeId ORDER BY P.PRIORITY_INDEX
            ";

        $boundedParams['employeeId'] = $employeeId;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParams);
        return Helper::extractDbData($result);
    }

    public function fetchEmployeeDetail($employeeId) {
        $sql = "
        select he.employee_code,hd.* from HRIS_SALARY_SHEET_EMP_DETAIL hd 
        join hris_employees he on (he.employee_id=hd.employee_id) 
        where hd.employee_id={$employeeId} and MONTH_ID = 13
            ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
