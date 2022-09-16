<?php

namespace Application\Helper;

use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;

class SessionHelper {

    public static function sessionCheck(MvcEvent $event) {
        $app = $event->getApplication();
        $auth = new AuthenticationService();
        $storage = $auth->getStorage()->read();
        $config = $app->getServiceManager()->get('config');

        if (isset($storage) && isset($storage['employee_id']) && isset($storage['employee_detail'])) {
            $employeeId = $storage['employee_id'];
            $registerAttendance = $storage['register_attendance'];
            $allowRegisterAttendance = $storage['allow_register_attendance'];
            $employeeDetail = $storage['employee_detail'];
            $employeeFilePath = $employeeDetail['EMPLOYEE_FILE_PATH'];
            $companyFilePath = $employeeDetail['COMPANY_FILE_PATH'];

            $event->getViewModel()->setVariable('selfEmployeeId', $employeeId);
            $event->getViewModel()->setVariable("employeeName", $employeeDetail['FIRST_NAME']);
            $event->getViewModel()->setVariable("companyName", $employeeDetail['COMPANY_NAME']);
            $event->getViewModel()->setVariable("companyAddress", $employeeDetail['COMPANY_ADDRESS']);
            $event->getViewModel()->setVariable("profilePictureUrl", isset($employeeFilePath) ? $employeeFilePath : $config['default-profile-picture']);
            $event->getViewModel()->setVariable("companyLogoUrl", isset($companyFilePath) ? $companyFilePath : $config['default-profile-picture']);
            $event->getViewModel()->setVariable("registerAttendance", $registerAttendance);
            $event->getViewModel()->setVariable("allowRegisterAttendance", $allowRegisterAttendance);
            $event->getViewModel()->setVariable("showAddressBook", $storage['preference']['showAddressBook'] == 'Y');
        }
    }

}
