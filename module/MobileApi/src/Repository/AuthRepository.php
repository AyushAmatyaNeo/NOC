<?php

namespace MobileApi\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;

class AuthRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function getUserProfile($employeeId) {
        $sql = "
            SELECT E.EMPLOYEE_ID,E.ID_THUMB_ID,
              E.FULL_NAME,
              F.FILE_PATH
            FROM HRIS_EMPLOYEES E
            LEFT JOIN HRIS_EMPLOYEE_FILE F
            ON (E.PROFILE_PICTURE_ID=F.FILE_CODE)
            WHERE E.EMPLOYEE_ID={$employeeId}";
        $result = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $result->current();
    }

}
