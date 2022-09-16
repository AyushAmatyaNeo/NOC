<?php
namespace AttendanceManagement\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Repository\AttendanceStatusRepository;
use Exception;
use ManagerService\Repository\AttendanceApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\AttendanceRequestForm;
use SelfService\Model\AttendanceRequestModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class AttendanceStatus extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AttendanceStatusRepository::class);
        $this->initializeForm(AttendanceRequestForm::class);
    }

    public function indexAction() {
        $statusSE = $this->getStatusSelectElement(['name' => 'attendanceStatus', 'id' => 'attendanceRequestStatusId', "class" => "form-control reset-field", 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'attendanceStatus' => $statusSE,
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail'],
                    'preference' => $this->preference
        ]);
    }

    public function viewAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("attendancestatus");
        }
        $attendanceRequestRepository = new AttendanceApproveRepository($this->adapter);
        $detail = $attendanceRequestRepository->fetchById($id);

        $model = new AttendanceRequestModel();
        $employeeId = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];

        $status = $detail['STATUS'];
        $authApprover = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];

        try {
            if (!$request->isPost()) {
                $model->exchangeArrayFromDB($detail);
                $this->form->bind($model);
            } else {
                $getData = $request->getPost();
                $reason = $getData->approvedRemarks;
                $action = $getData->submit;

                $model->approvedDt = Helper::getcurrentExpressionDate();

                if ($action == "Approve") {
                    $model->status = "AP";
                    $this->flashmessenger()->addMessage("Attendance Request Approved!!!");
                } else if ($action == "Reject") {
                    $model->status = "R";
                    $this->flashmessenger()->addMessage("Attendance Request Rejected!!!");
                }
                $model->approvedBy = $this->employeeId;
                $model->approvedRemarks = $reason;
                $attendanceRequestRepository->edit($model, $id);
                return $this->redirect()->toRoute("attendancestatus");
            }
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'id' => $id,
                        'employeeName' => $employeeName,
                        'approver' => $authApprover,
                        'employeeId' => $employeeId,
                        'status' => $status,
                        'requestedDt' => $detail['REQUESTED_DT'],
                        'acl' => $this->acl
            ]);
        } catch (\Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'id' => $id,
                        'employeeName' => $employeeName,
                        'approver' => $authApprover,
                        'employeeId' => $employeeId,
                        'status' => $status,
                        'requestedDt' => $detail['REQUESTED_DT'],
                        'acl' => $this->acl
            ]);
        }
    }

    public function pullAttendanceRequestStatusListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $attendanceStatusRepository = new AttendanceStatusRepository($this->adapter);
            $recordList = $attendanceStatusRepository->getAttenReqList($data);
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "num" => count($recordList)
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function bulkAction() {
        $request = $this->getRequest();
        try {
            $postData = $request->getPost();
            if ($postData['status'] == 'Rejected' || $postData['status'] == 'Cancelled' || $postData['status'] == 'Approved') {
                
            } else {
                $this->makeDecision($postData['id'], $postData['action'] == "approve");
            }
            return new JsonModel(['success' => true, 'data' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function makeDecision($id, $approve, $remarks = null, $enableFlashNotification = false) {
        $model = new AttendanceRequestModel();
        $model->id = $id;
        $model->recommendedDate = Helper::getcurrentExpressionDate();
        $model->recommendedBy = $this->employeeId;
        $model->approvedRemarks = $remarks;
        $model->approvedDate = Helper::getcurrentExpressionDate();
        $model->approvedBy = $this->employeeId;
        $model->status = $approve ? "AP" : "R";
        $message = $approve ? "Attendance Request Approved" : "Attendance Request Rejected";
        $notificationEvent = $approve ? NotificationEvents::ATTENDANCE_APPROVE_ACCEPTED : NotificationEvents::ATTENDANCE_APPROVE_REJECTED;
        $attendanceRequestRepository = new AttendanceApproveRepository($this->adapter);
        $attendanceRequestRepository->edit($model, $id);
        if ($enableFlashNotification) {
            $this->flashmessenger()->addMessage($message);
        }
        try {
            HeadNotification::pushNotification($notificationEvent, $model, $this->adapter, $this);
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
        }
    }
}
