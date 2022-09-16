<?php
namespace AttendanceManagement\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Form\AttendanceByHrForm;
use AttendanceManagement\Model\Attendance;
use AttendanceManagement\Model\AttendanceDetail;
use SelfService\Model\AttendanceRequestModel;
use AttendanceManagement\Repository\AttendancePullApiRepository;
use AttendanceManagement\Repository\AttendanceRepository;
use AttendanceManagement\Repository\ShiftRepository;
use Exception;
use SelfService\Repository\AttendanceRepository as SelfServiceAttendanceRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Form\Element\Select;
use Zend\View\Model\JsonModel;

class AttendancePullApi extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeForm(AttendanceByHrForm::class);
        $this->initializeRepository(AttendancePullApiRepository::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        $deviceName = $this->repository->getDeviceName();
        $deviceNameList = Helper::extractDbData($deviceName);
        // print_r($deviceNameList);die;
        if ($request->isPost()) {
            $getData = $request->getPost();
            $fromDate = date("Y-m-d",strtotime($getData['fromDt']));
            $toDate = date("Y-m-d",strtotime($getData['endDt']));
            $deviceName = $getData['fromDevice'];
            if($deviceName == -1){
                $url = 'http://172.16.100.2:8090/attapi/attendanceBulkPullDevice.php?fromdate='.$fromDate.'&todate='.$toDate;
            }else{
                $url = 'http://172.16.100.2:8090/attapi/attendanceBulkPullDevice.php?fromdate='.$fromDate.'&todate='.$toDate.'&devicename='.$deviceName;
            }
            $response = file_get_contents($url);
            return new JsonModel(['success' => true, 'messg' => $response]);
        }
        // print_r($deviceNameList);die;
        return Helper::addFlashMessagesToArray($this, [
            'deviceNameList' => $deviceNameList,
        ]);
    }
}