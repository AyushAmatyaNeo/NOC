<?php

namespace Overtime\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use SelfService\Form\OvertimeRequestForm;
use SelfService\Model\Overtime;
use SelfService\Model\OvertimeDetail;
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Repository\OvertimeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class OvertimeApply extends AbstractActionController {

    private $form;
    private $adapter;
    private $overtimeRepository;
    private $overtimeDetailRepository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->overtimeRepository = new OvertimeRepository($adapter);
        $this->overtimeDetailRepository = new OvertimeDetailRepository($adapter);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new OvertimeRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        return $this->redirect()->toRoute("overtimeStatus");
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new Overtime();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);
            
            if ($this->form->isValid()) {
                $postDataArray = $postData->getArrayCopy();
                $model->exchangeArrayFromForm($this->form->getData());
                $overtimeDateRaw = $model->overtimeDate;
                $model->overtimeId = ((int) Helper::getMaxId($this->adapter, Overtime::TABLE_NAME, Overtime::OVERTIME_ID)) + 1;
                $model->employeeId = $postData['employeeId'];
                $model->requestedDate = Helper::getcurrentExpressionDate();
                //$model->status = 'RQ';
                $model->status = $postData['otStatus'];
                if($postData['otStatus'] == trim('AP')){
                    $model->approvedRemarks = $postData['remarks'];
                    $model->remarks = '';
                } else {
                    $model->remarks = $postData['remarks'];
                }
                
                $model->overtimeDate = Helper::getExpressionDate($model->overtimeDate);
                $model->allTotalHour = Helper::hoursToMinutes($postDataArray['allTotalHour']);
                $this->overtimeRepository->add($model);

                $overtimeDetailModel = new OvertimeDetail();
                
                for ($i = 0; $i < sizeof($postDataArray['startTime']); $i++) {
                    $startTime = $postDataArray['startTime'][$i];
                    $endTime = $postDataArray['endTime'][$i];
                    $totalHour = $postDataArray['totalHour'][$i];
                    $overtimeDetailModel->overtimeId = $model->overtimeId;
                    $overtimeDetailModel->detailId = ((int) Helper::getMaxId($this->adapter, OvertimeDetail::TABLE_NAME, OvertimeDetail::DETAIL_ID)) + 1;
                    
                    $overtimeStartTimeRaw = $startTime;
                    $overtimeEndTimeRaw = $endTime;

                    $overTimeDateTimeStart = $overtimeDateRaw . " " . $overtimeStartTimeRaw;
                    $overTimeDateTimeEnd = $overtimeDateRaw . " " . $overtimeEndTimeRaw;

                    $overtimeDetailModel->startTime = Helper::getExpressionDateTime($overTimeDateTimeStart);
                    $overtimeDetailModel->endTime = Helper::getExpressionDateTime($overTimeDateTimeEnd);
                    $overtimeDetailModel->totalHour = Helper::hoursToMinutes($totalHour);
                    $overtimeDetailModel->status = 'E';
                    $overtimeDetailModel->createdBy = $this->employeeId;
                    $overtimeDetailModel->createdDate = Helper::getcurrentExpressionDate();

                    $this->overtimeDetailRepository->add($overtimeDetailModel);
                }
                $this->flashmessenger()->addMessage("Overtime Request Successfully added!!!");
                return $this->redirect()->toRoute("overtimeStatus");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'otstatusOptions' => ['RQ' => 'Pending', 'AP' => 'Approved'],
//                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", false, true),
            'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => "E" , 'RETIRED_FLAG' => 'N'], "FULL_NAME", "ASC", " ", false, true),
        ]);
    }

    public function addRadAction(){
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new Overtime();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $postDataArray = $postData->getArrayCopy();
                $model->exchangeArrayFromForm($this->form->getData());
                $model->overtimeId = ((int) Helper::getMaxId($this->adapter, Overtime::TABLE_NAME, Overtime::OVERTIME_ID)) + 1;
                $model->employeeId = $postData['employeeId'];
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->allTotalHour = Helper::hoursToMinutes($postDataArray['allTotalHour']);
                $this->overtimeRepository->add($model);

                $overtimeDetailModel = new OvertimeDetail();
                for ($i = 0; $i < sizeof($postDataArray['startTime']); $i++) {
                    $startTime = $postDataArray['startTime'][$i];
                    $endTime = $postDataArray['endTime'][$i];
                    $totalHour = $postDataArray['totalHour'][$i];
                    $overtimeDetailModel->overtimeId = $model->overtimeId;
                    $overtimeDetailModel->detailId = ((int) Helper::getMaxId($this->adapter, OvertimeDetail::TABLE_NAME, OvertimeDetail::DETAIL_ID)) + 1;
                    $overtimeDetailModel->startTime = Helper::getExpressionTime($startTime);
                    $overtimeDetailModel->endTime = Helper::getExpressionTime($endTime);
                    $overtimeDetailModel->totalHour = Helper::hoursToMinutes($totalHour);
                    $overtimeDetailModel->status = 'E';
                    $overtimeDetailModel->createdBy = $this->employeeId;
                    $overtimeDetailModel->createdDate = Helper::getcurrentExpressionDate();
                    $this->overtimeDetailRepository->add($overtimeDetailModel);
                }
                $this->flashmessenger()->addMessage("Overtime Request Successfully added!!!");
                return $this->redirect()->toRoute("overtimeStatus");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
//                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", false, true),
            'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => "E" , 'RETIRED_FLAG' => 'N'], "FULL_NAME", "ASC", " ", false, true),
        ]);
    }

    public function attendanceDetailAction() {
        $date = date_format(date_create($_POST['date']), "d-M-y");
        $employeeId = $_POST['employeeId'];
        $result = $this->overtimeRepository->fetchAttendanceDetail($employeeId,$date);
        return new JSONModel(['data' => $result]);
    }

}
