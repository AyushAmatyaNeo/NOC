<?php

namespace WorkOnHoliday\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use HolidayManagement\Model\Holiday;
use ManagerService\Repository\HolidayWorkApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\WorkOnHolidayForm;
use SelfService\Model\WorkOnHoliday;
use SelfService\Repository\HolidayRepository;
use WorkOnHoliday\Repository\WorkOnHolidayStatusRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class WorkOnHolidayStatus extends HrisController {

    private $holidayWorkApproveRepository;
    private $workOnHolidayStatusRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->holidayWorkApproveRepository = new HolidayWorkApproveRepository($adapter);
        $this->workOnHolidayStatusRepository = new WorkOnHolidayStatusRepository($adapter);
        $this->initializeForm(WorkOnHolidayForm::class);
    }

    public function indexAction() {
        $holidayList = EntityHelper::getTableKVListWithSortOption($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], [Holiday::STATUS => 'E'], Holiday::HOLIDAY_ENAME, "ASC", NULL, [-1 => 'All Holiday'], TRUE);
        $holidaySE = $this->getSelectElement(['name' => 'holiday', 'id' => 'holidayId', 'class' => 'form-control reset-field', 'label' => 'Holiday'], $holidayList);
        $statusSE = $this->getStatusSelectElement(['name' => 'status', "id" => "requestStatusId", "class" => "form-control reset-field", 'label' => 'Status']);

        return $this->stickFlashMessagesTo([
            'holidays' => $holidaySE,
            'status' => $statusSE,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'preference' => $this->preference,
            'employeeDetail' => $this->storageData['employee_detail'],
            'acl' => $this->acl,
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("workOnHolidayStatus");
        }
        $workOnHolidayModel = new WorkOnHoliday();
        $request = $this->getRequest();
        $detail = $this->holidayWorkApproveRepository->fetchById($id);
        
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $recommApprove = $detail['RECOMMENDER_ID'] == $detail['APPROVER_ID'] ? 1 : 0;

        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];

        if (!$request->isPost()) {
            $workOnHolidayModel->exchangeArrayFromDB($detail);
            $this->form->bind($workOnHolidayModel);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $workOnHolidayModel->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $workOnHolidayModel->status = "R";
                $this->flashmessenger()->addMessage("Work on Holiday Request Rejected!!!");
            } else if ($action == "Approve") {
                try {
                    $this->wohAppAction($detail);
                    $this->flashmessenger()->addMessage("Work on Holiday Request Approved");
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage("Work on Holiday Request Approved but reward not given as position is not defined.");
                }
                $workOnHolidayModel->status = "AP";
            }
            $workOnHolidayModel->approvedBy = $this->employeeId;
            $workOnHolidayModel->approvedRemarks = $reason;
            $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);

            return $this->redirect()->toRoute("workOnHolidayStatus");
        }
        $holidays = $this->getHolidayList($requestedEmployeeID);
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'employeeId' => $employeeId,
            'employeeName' => $employeeName,
            'requestedDt' => $detail['REQUESTED_DATE'],
            'recommender' => $authRecommender,
            'approvedDT' => $detail['APPROVED_DATE'],
            'approver' => $authApprover,
            'status' => $status,
//                    'holidays' => $holidays["holidayKVList"],
//                    'holidayObjList' => $holidays["holidayList"],
            'customRenderer' => Helper::renderCustomView(),
            'recommApprove' => $recommApprove,
            'detail' => $detail,
            'acl' => $this->acl
        ]);
    }

    public function getHolidayList($employeeId) {
        $holidayRepo = new HolidayRepository($this->adapter);
        $holidayResult = $holidayRepo->selectAll($employeeId);
        $holidayList = [];
        $holidayObjList = [];
        foreach ($holidayResult as $holidayRow) {
            //$todayDate = new \DateTime();
            $holidayList[$holidayRow['HOLIDAY_ID']] = $holidayRow['HOLIDAY_ENAME'] . " (" . $holidayRow['START_DATE'] . " to " . $holidayRow['END_DATE'] . ")";
            $holidayObjList[$holidayRow['HOLIDAY_ID']] = $holidayRow;
        }
        return ['holidayKVList' => $holidayList, 'holidayList' => $holidayObjList];
    }

    private function wohAppAction($detail) {
        $this->holidayWorkApproveRepository->wohReward($detail['ID']);
    }

    public function pullHoliayWorkRequestStatusListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $holidayWorkStatusRepo = new WorkOnHolidayStatusRepository($this->adapter);
            $recordList = $holidayWorkStatusRepo->getWOHRequestList($data);
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function bulkAction() {
        $request = $this->getRequest();
        try {
            $postData = $request->getPost();
            if ($postData['super_power'] == 'true') {
                $this->makeSuperDecision($postData['id'], $postData['action'] == "approve");
            } else {
                if ($postData['status'] == 'Rejected' || $postData['status'] == 'Cancelled' || $postData['status'] == 'Approved') {

                } else {
                    $this->makeDecision($postData['id'], $postData['action'] == "approve");
                }
            }
            return new JsonModel(['success' => true, 'data' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function makeDecision($id, $approve, $remarks = null, $enableFlashNotification = false) {
        $model = new WorkOnHoliday();
        $model->id = $id;
        $model->recommendedDate = Helper::getcurrentExpressionDate();
        $model->recommendedBy = $this->employeeId;
        $model->approvedRemarks = $remarks;
        $model->approvedDate = Helper::getcurrentExpressionDate();
        $model->approvedBy = $this->employeeId;
        $model->status = $approve ? "AP" : "R";
        $message = $approve ? "WOH Request Approved" : "WOH Request Rejected";
        $notificationEvent = $approve ? NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED : NotificationEvents::WORKONHOLIDAY_APPROVE_REJECTED;
        $this->holidayWorkApproveRepository->edit($model, $id);
        if ($enableFlashNotification) {
            $this->flashmessenger()->addMessage($message);
        }
        try {
            HeadNotification::pushNotification($notificationEvent, $model, $this->adapter, $this);
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
        }
    }

    private function makeSuperDecision($id, $approve, $remarks = null, $enableFlashNotification = false) {
        $holidayWorkApproveRepository = new HolidayWorkApproveRepository($this->adapter);
        $detail = $holidayWorkApproveRepository->fetchById($id);
        if ($detail['STATUS'] == 'AP') {
            $model = new WorkOnHoliday();
            $model->id = $id;
            $model->recommendedDate = Helper::getcurrentExpressionDate();
            $model->recommendedBy = $this->employeeId;
            $model->approvedRemarks = $remarks;
            $model->approvedDate = Helper::getcurrentExpressionDate();
            $model->approvedBy = $this->employeeId;
            $model->status = $approve ? "AP" : "R";
            $message = $approve ? "WOH Request Approved" : "WOH Request Rejected";
            $notificationEvent = $approve ? NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED : NotificationEvents::WORKONHOLIDAY_APPROVE_REJECTED;
            $this->holidayWorkApproveRepository->edit($model, $id);
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

}
