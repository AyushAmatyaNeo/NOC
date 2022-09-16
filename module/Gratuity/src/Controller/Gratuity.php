<?php

namespace Gratuity\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Gratuity\Form\GratuityForm;
use Gratuity\Model\Gratuity as GratuityModel;
use Gratuity\Repository\GratuityRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Model\LeaveSubstitute;
use SelfService\Repository\LeaveRequestRepository;
use SelfService\Repository\LeaveSubstituteRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class Gratuity extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(GratuityRepository::class);
        $this->initializeForm(GratuityForm::class);
    }

    public function indexAction() {
        $locationList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_LOCATIONS', 'LOCATION_ID', ['LOCATION_EDESC'], "STATUS='E'", "LOCATION_EDESC", "ASC", null, true);

        return Helper::addFlashMessagesToArray($this, [
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'locationList'=> $locationList,

        ]);
        
    }


    public function addAction(){
        $employees = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"], "Employee_id not in (select distinct employee_id from hris_gratuity where status='E')", "FULL_NAME", "ASC", "-", false, true, null);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $employeeId = $postData['employeeId'];
            $extraServiceYear = $postData['extraServiceYear'];
            $retirementDate =$postData['retirementDate'];
            $this->repository->calculateGratuity($employeeId, $extraServiceYear, Helper::getExpressionDate($retirementDate));
            $this->flashmessenger()->addMessage("Successfully Calculated Gratuity!!!");
            return $this->redirect()->toRoute("gratuity");
        }
        return Helper::addFlashMessagesToArray($this, [
            'employees' => $employees,
            'form' => $this->form
        ]);


    }

    public function getAllGratuityDataAction (){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $rawList = $this->repository->getFilteredRecords($data);
            $list = iterator_to_array($rawList, false);
            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        }
    }

    public function viewAction(){
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("gratuity");
        }
        $detail = $this->repository->fetchGratuityDetails($id);
        $ar = $this->repository->fetchGratuityAmount($id);
        $salary_detail = $this->repository->fetchSalaryDetail($this->employeeId);
        $leave_detail = $this->repository->fetchLeaveDetail($id);
        // print_r($salary_detail);die;
        return Helper::addFlashMessagesToArray($this, [
            'detail' => $detail,
            'gratuity_detail' => $ar[0],
            'medical_detail' => $ar[1],
            'holiday_detail' => $ar[2],
            'days'=>$ar,
            'salary_detail' => $salary_detail,
            'monthly_leave'=>$leave_detail[0],
            'other_leave'=>$leave_detail[1]
        ]);
    }

    public function recalculateAction(){
        $id = (int) $this->params()->fromRoute('id');
        $request = $this->getRequest();
        if ($id === 0) {
            return $this->redirect()->toRoute("gratuity");
        }
        $detail = $this->repository->fetchRecalculateDetails($id);
        $gratuity = new GratuityModel();
        $gratuity->exchangeArrayFromDB($this->repository->fetchRecalculateDetails($id)->getArrayCopy());

        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $this->repository->deletePreviousData($id);
            $employeeId = $postData['employeeId'];
            $extraServiceYear = $postData['extraServiceYear'];
            $retirementDate =$postData['retirementDate'];
            $this->repository->calculateGratuity($employeeId, $extraServiceYear, Helper::getExpressionDate($retirementDate));
            $this->flashmessenger()->addMessage("Successfully Recalculated Gratuity!!!");
            return $this->redirect()->toRoute("gratuity");
        }

        $employees = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"], ['EMPLOYEE_ID' => $gratuity->employeeId], "FULL_NAME", "ASC", "-", false, true, null);
        return Helper::addFlashMessagesToArray($this, [
            'detail' => $detail,
            'form' => $this->form,
            'employees' =>$employees,
            'id' => $id,
        ]);
    }

    public function deleteGratuityAction(){
        $id = (int) $this->params()->fromRoute('id');
        $this->repository->deletePreviousData($id);
        $this->flashmessenger()->addMessage("Successfully Deleted Gratuity!!!");
        return $this->redirect()->toRoute("gratuity");
    }

}
