<?php

namespace MobileApi\Repository;

use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;

class NotificationRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function getNotification($employeeId) {
        $sql = "
                SELECT N.MESSAGE_ID,
                  TO_CHAR(N.MESSAGE_DATETIME,'DD-MON-YYYY') AS MESSAGE_DATETIME,
                  N.MESSAGE_TITLE,
                  N.MESSAGE_DESC,
                  N.MESSAGE_FROM,
                  F.FULL_NAME AS MESSAGE_FROM_NAME,
                  N.MESSAGE_TO,
                  NOTIFICATION_STATUS_DESC(N.STATUS) AS STATUS
                FROM HRIS_NOTIFICATION N
                LEFT JOIN HRIS_EMPLOYEES F
                ON (N.MESSAGE_FROM    =F.EMPLOYEE_ID)
                WHERE TRUNC(SYSDATE) <= N.EXPIRY_TIME
                AND N.MESSAGE_TO      ={$employeeId}
";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
