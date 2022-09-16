<?php

namespace MobileApi\Controller;

use Application\Factory\ConfigInterface;
use Application\Helper\Helper;
use Exception;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Repository\LeaveAssignRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use ManagerService\Repository\LeaveApproveRepository;
use MobileApi\Repository\LeaveRepository;
use Notification\Model\NotificationEvents;
use SelfService\Model\LeaveSubstitute;
use SelfService\Repository\LeaveRequestRepository;
use SelfService\Repository\LeaveSubstituteRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class Leave extends AbstractActionController {

    private $adapter;
    private $config;
    private $employeeId;

    public function __construct(AdapterInterface $adapter, ConfigInterface $config) {
        $this->adapter = $adapter;
        $this->config = $config->getApplicationConfig();
    }

    public function setupAction() {
        try {
            $request = $this->getRequest();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();

            $requestType = $request->getMethod();
//            $data = json_decode($request->getContent());
//            $id = $this->params()->fromRoute('id');
            $responseDate = [];

            switch ($requestType) {
                case Request::METHOD_POST:
                    throw new Exception("Unavailable Request.");
                    break;
                case Request::METHOD_GET:
                    $responseDate = $this->employeeLeaveGet($this->employeeId);
                    break;

                case Request::METHOD_PUT:
                    throw new Exception("Unavailable Request.");
                    break;

                case Request::METHOD_DELETE:
                    throw new Exception("Unavailable Request.");
                    break;

                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseDate, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function assignAction() {
        try {
            $request = $this->getRequest();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();

            $requestType = $request->getMethod();
//            $data = json_decode($request->getContent());
            $id = $this->params()->fromRoute('id');
            $responseDate = [];

            switch ($requestType) {
                case Request::METHOD_POST:
                    throw new Exception("Unavailable Request.");
                    break;
                case Request::METHOD_GET:
                    if (isset($id) && $id != null && $id != 0) {
                        $responseDate = $this->assignGetById($id, $this->employeeId);
                    } else {
                        throw new Exception("Leave Id should be defined.");
                    }
                    break;

                case Request::METHOD_PUT:
                    throw new Exception("Unavailable Request.");
                    break;

                case Request::METHOD_DELETE:
                    throw new Exception("Unavailable Request.");
                    break;

                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseDate, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function calculateDaysAction() {
        try {
            $request = $this->getRequest();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();

            $requestType = $request->getMethod();
            $data = json_decode($request->getContent());
            $responseDate = [];

            switch ($requestType) {
                case Request::METHOD_POST:
                    $responseDate = $this->calculateDays($data);
                    break;
                case Request::METHOD_GET:
                    throw new Exception("Unavailable Request.");
                    break;
                case Request::METHOD_PUT:
                    throw new Exception("Unavailable Request.");
                    break;

                case Request::METHOD_DELETE:
                    throw new Exception("Unavailable Request.");
                    break;

                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseDate, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function requestAction() {
        try {
            
            $request = $this->getRequest();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();
            $requestType = $request->getMethod();
            $data = json_decode($request->getContent());
            $id = $this->employeeId;
            $responseDate = [];

            switch ($requestType) {
                case Request::METHOD_POST:
                    $responseDate = $this->requestPost($data);
                case Request::METHOD_GET:
                    if (isset($id) && $id != null && $id != 0) {
                        $responseDate = $this->requestGetById($id);
                    } else {
                        $responseDate = $this->requestGet();
                    }
                    break;

                case Request::METHOD_PUT:
                    $responseDate = $this->requestPut($id, $data);
                    break;

                case Request::METHOD_DELETE:
                    $responseDate = $this->requestDelete($id);
                    break;

                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseDate, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function approvalAction() {
        try {          
//            echo'asdf';
//            die();
            $request = $this->getRequest();
//            print_r($request);
//            die();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();
//             print_r( $this->employeeId);
//            die();

            $requestType = $request->getMethod();
//            print_r($requestType);
//            die();
            
            $data = json_decode($request->getContent());
//            print_r($data);
//            die();
            $id = $this->params()->fromRoute('id');
//            print_r($id);
//            die();
            
            $responseDate = [];

            switch ($requestType) {
                case Request::METHOD_POST:
                    throw new Exception("Unavailable Request.");
                    break;
                case Request::METHOD_GET:
                    if (isset($id) && $id != null && $id != 0) {
                        $responseDate = $this->approvalGetById($id);
                    } else {
                        $responseDate = $this->approvalGet();
                    }
                    break;

                case Request::METHOD_PUT:
                    $responseDate = $this->approvalPut($id, $data);
                    break;

                case Request::METHOD_DELETE:
                    throw new Exception("Unavailable Request.");
                    break;

                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseDate, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function substituteApprovalAction() {
        try {
            $request = $this->getRequest();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();

            $requestType = $request->getMethod();
            $data = json_decode($request->getContent());
            $id = $this->params()->fromRoute('id');

            $responseDate = [];

            switch ($requestType) {
                case Request::METHOD_POST:
                    throw new Exception("Unavailable Request.");
                    break;
                case Request::METHOD_GET:
                    if (isset($id) && $id != null && $id != 0) {
                        $responseDate = $this->substitueApprovalGetById($id);
                    } else {
                        $responseDate = $this->substituteApprovalGet();
                    }
                    break;

                case Request::METHOD_PUT:
                    $responseDate = $this->substituteApprovalPut($id, $data);
                    break;

                case Request::METHOD_DELETE:
                    throw new Exception("Unavailable Request.");
                    break;

                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseDate, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    private function requestGet() {
        $request = new LeaveRequestRepository($this->adapter);
        $result = $request->fetchByEmpId($this->employeeId);

        return Helper::extractDbData($result);
    }

    private function requestGetById($id) {
        $request = new LeaveRequestRepository($this->adapter);
        $result = $request->selectAll($id);
        return Helper::extractDbData($result);
    }

    private function requestPost($data) {
        $leaveRequest = new LeaveApply();
        $leaveRequest->exchangeArrayFromDB((array) $data);

        $leaveRequest->id = (int) Helper::getMaxId($this->adapter, LeaveApply::TABLE_NAME, LeaveApply::ID) + 1;
        $leaveRequest->startDate = Helper::getExpressionDate($leaveRequest->startDate);
        $leaveRequest->endDate = Helper::getExpressionDate($leaveRequest->endDate);
        $leaveRequest->requestedDt = Helper::getcurrentExpressionDate();
        $leaveRequest->status = "RQ";

        $requestRepo = new LeaveRequestRepository($this->adapter);
        $requestRepo->add($leaveRequest);

        $leaveSubstitute = $data->SUBSTITUTE_EMPLOYEE_ID;

        if ($leaveSubstitute !== null && $leaveSubstitute !== "") {
            $leaveSubstituteModel = new LeaveSubstitute();
            $leaveSubstituteRepo = new LeaveSubstituteRepository($this->adapter);

            $leaveSubstituteModel->leaveRequestId = $leaveRequest->id;
            $leaveSubstituteModel->employeeId = $leaveSubstitute;
            $leaveSubstituteModel->createdBy = $leaveRequest->employeeId;
            $leaveSubstituteModel->createdDate = Helper::getcurrentExpressionDate();
            $leaveSubstituteModel->status = 'E';

            $leaveSubstituteRepo->add($leaveSubstituteModel);
//            HeadNotification::pushNotification(NotificationEvents::LEAVE_SUBSTITUTE_APPLIED, $leaveRequest, $this->adapter, $this);
        } else {
//            HeadNotification::pushNotification(NotificationEvents::LEAVE_APPLIED, $leaveRequest, $this->adapter, $this);
        }
        return null;
    }

    private function requestPut($id, $data) {
        
    }

    private function requestDelete($id) {
        
    }

    private function leaveGet() {
        $leaveRepo = new LeaveMasterRepository($this->adapter);
        $result = $leaveRepo->fetchAll();
        return Helper::extractDbData($result);
    }

    private function leaveGetById($id) {
        $leaveRepo = new LeaveMasterRepository($this->adapter);
        $result = $leaveRepo->fetchById($id);
        return [$result];
    }

    private function assignGetById($leaveId, $employeeId) {
        $assignRepo = new LeaveAssignRepository($this->adapter);
        $leave = $this->leaveGetById($leaveId);
        $fiscalYearMonthNo = null;
        if($leave[0]["IS_MONTHLY"] == 'Y'){
            $fiscalYearMonthNo = date('m');
        }
        $result = $assignRepo->filterByLeaveEmployeeId($leaveId, $employeeId, $fiscalYearMonthNo);
        return $result;
    }

    private function calculateDays($data) {
        $request = new LeaveRequestRepository($this->adapter);
        return $request->fetchAvailableDays($data->START_DATE, $data->END_DATE, $data->EMPLOYEE_ID,$data->HALF_DAY,$data->LEAVE_ID);
    }
    
    private function approvalGet() {
        $approvalRepository = new LeaveRepository($this->adapter);
        $list = $approvalRepository->getApproval($this->employeeId);
        return $list;
    }

    private function approvalGetById($id) {
        $approvalRepository = new LeaveRepository($this->adapter);
        $list = $approvalRepository->getApproval($this->employeeId, $id);
        return $list;
    }

    /*
     * {
     * "ROLE":2,3,4,
     * "EMPLOYEE_ID":1,
     * "REMARKS":"",
     * "ACTION":"Approve","Reject"
     * }
     */

    private function approvalPut($id, $data) {
        $leaveApply = new LeaveApply();
        $notificatinEvent = null;
        switch ($data->ROLE) {
            case 2:
                $leaveApply->recommendedDt = Helper::getcurrentExpressionDate();
                $leaveApply->status = $data->ACTION == "Approve" ? "RC" : "R";
                $leaveApply->recommendedBy = $data->EMPLOYEE_ID;
                $leaveApply->recommendedRemarks = $data->REMARKS;
                $notificatinEvent = ( $data->ACTION == "Approve") ? NotificationEvents::LEAVE_RECOMMEND_ACCEPTED : NotificationEvents::LEAVE_RECOMMEND_REJECTED;
                break;
            case 3:
                $leaveApply->approvedDt = Helper::getcurrentExpressionDate();
                $leaveApply->status = $data->ACTION == "Approve" ? "AP" : "R";
                $leaveApply->approvedBy = $data->EMPLOYEE_ID;
                $leaveApply->approvedRemarks = $data->REMARKS;
                $notificatinEvent = ( $data->ACTION == "Approve") ? NotificationEvents::LEAVE_APPROVE_ACCEPTED : NotificationEvents::LEAVE_APPROVE_REJECTED;
                break;
            case 4:
                $leaveApply->recommendedDt = Helper::getcurrentExpressionDate();
                $leaveApply->recommendedBy = $data->EMPLOYEE_ID;
                $leaveApply->approvedDt = Helper::getcurrentExpressionDate();
                $leaveApply->status = $data->ACTION == "Approve" ? "AP" : "R";
                $leaveApply->approvedBy = $data->EMPLOYEE_ID;
                $leaveApply->approvedRemarks = $data->REMARKS;
                $notificatinEvent = ( $data->ACTION == "Approve") ? NotificationEvents::LEAVE_APPROVE_ACCEPTED : NotificationEvents::LEAVE_APPROVE_REJECTED;
                break;
        }
        $approveRepo = new LeaveApproveRepository($this->adapter);
        $approveRepo->edit($leaveApply, $id);
//        HeadNotification::pushNotification($notificatinEvent, $leaveApply, $this->adapter, $this);
        return null;
    }

    private function substitueApprovalGetById($id) {
        $leaveRepo = new LeaveRepository($this->adapter);
        return [$leaveRepo->getSubstituteApprovalById($id)];
    }

    private function substituteApprovalGet() {
        $leaveRepo = new LeaveRepository($this->adapter);
        return $leaveRepo->getSubstituteApprovalByEmpId($this->employeeId);
    }

    /*
     * {"REMARKS":1000376,"ACTION":"Approve"}
     */

    private function substituteApprovalPut($id, $data) {
        $leaveSubstitute = new LeaveSubstitute();
        $leaveSubstitute->approvedDate = Helper::getcurrentExpressionDate();
        $leaveSubstitute->remarks = $data->REMARKS;
        $leaveSubstitute->approvedFlag = $data->ACTION == "Approve" ? "Y" : "N";
        $notificatinEvent = ( $data->ACTION == "Approve") ? NotificationEvents::LEAVE_SUBSTITUTE_ACCEPTED : NotificationEvents::LEAVE_SUBSTITUTE_REJECTED;

        $approveRepo = new LeaveRepository($this->adapter);
        $approveRepo->updateSubstituteApproval($leaveSubstitute, $id);
//        HeadNotification::pushNotification($notificatinEvent, $leaveApply, $this->adapter, $this);
        return null;
    }

    private function employeeLeaveGet($employeeId) {
        $leaveRepo = new LeaveRepository($this->adapter);
        return $leaveRepo->getEmployeeLeave($employeeId);
    }
    
    
    public function validateDaysAction() {
        try {
            $request = $this->getRequest();
            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();

            $requestType = $request->getMethod();
            $data = json_decode($request->getContent());
            $responseDate = [];

            switch ($requestType) {
                case Request::METHOD_POST:
                    $responseDate = $this->validateDays($data);
                    break;
                case Request::METHOD_GET:
                    throw new Exception("Unavailable Request.");
                    break;
                case Request::METHOD_PUT:
                    throw new Exception("Unavailable Request.");
                    break;

                case Request::METHOD_DELETE:
                    throw new Exception("Unavailable Request.");
                    break;

                default:
                    throw new Exception('the request  is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseDate, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
    
    
     private function validateDays($data) {
        $request = new LeaveRequestRepository($this->adapter);
        return $request->validateLeaveRequest($data->START_DATE, $data->END_DATE, $data->EMPLOYEE_ID);
    }

}
