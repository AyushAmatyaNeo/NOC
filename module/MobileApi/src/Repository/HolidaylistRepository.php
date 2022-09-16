<?php

namespace MobileApi\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class HolidaylistRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
   
    public function fetchEmployeeHolidayList($employeeId) {
        $sql = "
            select hms.HOLIDAY_ENAME,hms.START_DATE,hms.END_DATE from HRIS_EMPLOYEE_HOLIDAY heh join  HRIS_HOLIDAY_MASTER_SETUP hms on heh.holiday_id=hms.holiday_id where heh.employee_id={$employeeId} 
          ";
//        print_r($sql);
//die();
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
		return Helper::extractDbData($result);
    }

}