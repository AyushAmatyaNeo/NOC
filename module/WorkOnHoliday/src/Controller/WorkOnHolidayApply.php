<?php

namespace WorkOnHoliday\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use HolidayManagement\Model\Holiday;
use SelfService\Form\WorkOnHolidayForm;
use SelfService\Model\WorkOnHoliday;
use SelfService\Repository\WorkOnHolidayRepository;
use WorkOnHoliday\Repository\WorkOnHolidayStatusRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Controller\HrisController;

class WorkOnHolidayApply extends HrisController {

//    private $form;
//    private $adapter;
//    private $workOnHolidayRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->adapter = $adapter;
        $this->workOnHolidayRepository = new WorkOnHolidayRepository($adapter);
    }

//    public function initializeForm() {
//        $builder = new AnnotationBuilder();
//        $form = new WorkOnHolidayForm();
//        $this->form = $builder->createForm($form);
//    }

    public function indexAction() {
        return $this->redirect()->toRoute("workOnHolidayStatus");
    }

    public function addAction() {
        $this->initializeForm(WorkOnHolidayForm::class);
        $request = $this->getRequest();

        $model = new WorkOnHoliday();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->id = ((int) Helper::getMaxId($this->adapter, WorkOnHoliday::TABLE_NAME, WorkOnHoliday::ID)) + 1;
                $model->requestedDate = Helper::getcurrentExpressionDate();
//                $model->status = 'RQ';
                $model->status = ($postData['applyStatus'] == 'AP') ? 'AP' : 'RQ';
                $model->fromDate = Helper::getExpressionDate($model->fromDate);
                $model->toDate = Helper::getExpressionDate($model->toDate);
                $model->modifiedDate = Helper::getExpressionDate($model->modifiedDate);
                $model->recommendedDate = Helper::getExpressionDate($model->recommendedDate);
                $model->approvedDate = Helper::getExpressionDate($model->approvedDate);
                // echo"<pre>";print_r($model);die;
                $this->workOnHolidayRepository->add($model);
                $this->flashmessenger()->addMessage("Work on Holiday Request Successfully added!!!");
                return $this->redirect()->toRoute("workOnHolidayStatus");
            }
        }
        
        $applyOptionValues = [
            'RQ' => 'Pending',
            'AP' => 'Approved'
        ];
        $applyOption = $this->getSelectElement(['name' => 'applyStatus', 'id' => 'applyStatus', 'class' => 'form-control', 'label' => 'Type'], $applyOptionValues);

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'applyOption' => $applyOption,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", "-", false, true, $this->employeeId),
                    'holidays' => EntityHelper::getTableKVListWithSortOption($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], ["STATUS" => 'E'], "HOLIDAY_ENAME", "ASC", null, false, true)
        ]);
    }

    public function pullHolidaysForEmployeeAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $employeeId = $data['employeeId'];
            $holidayRepo = new WorkOnHolidayStatusRepository($this->adapter);
            $holidayResult = Helper::extractDbData($holidayRepo->getAttendedHolidayList($employeeId));

            return new JsonModel(['success' => true, 'data' => $holidayResult, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
