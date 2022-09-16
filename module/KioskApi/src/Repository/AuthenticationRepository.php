<?php

namespace KioskApi\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class AuthenticationRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchEmployeeData($thumbId) {
        $boundedParams = [];
        $sql = "
            SELECT EMPLOYEE_ID,
            FULL_NAME 
            FROM HRIS_EMPLOYEES WHERE ID_THUMB_ID = :thumbId
            and status = 'E' and resigned_flag <> 'Y' and retired_flag <> 'Y'
            ";

        $boundedParams['thumbId'] = $thumbId;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParams);
        return Helper::extractDBData($result);
    }

    public function fetchWithAuthenticate($username, $password) {
        $boundedParams = [];
        $sql = "
            SELECT U.EMPLOYEE_ID, 
            E.FULL_NAME 
            FROM HRIS_USERS U 
            JOIN HRIS_EMPLOYEES E 
            ON (U.EMPLOYEE_ID = E.EMPLOYEE_ID) 
            WHERE U.USER_NAME = :username 
            AND FN_DECRYPT_PASSWORD(PASSWORD) = :password
            ";

        $boundedParams['username'] = $username;
        $boundedParams['password'] = $password;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParams);
        return Helper::extractDBData($result);
    }

}
