<?php

namespace AutoNotification\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Repository\HrisRepository;

class AutoNotificationRepository extends HrisRepository{

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchCurrentNotifications(){
        $sql = "SELECT * FROM HRIS_AUTO_NOTIFICATION WHERE LAST_EXECUTED_ON IS NULL 
        OR (FREQUENCY = 'D' AND DAYS_BETWEEN(LAST_EXECUTED_ON, CURRENT_DATE) >= 1)";
        return $this->rawQuery($sql);
    }
}
