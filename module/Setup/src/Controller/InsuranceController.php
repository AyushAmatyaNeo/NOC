<?php

namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Setup\Form\InsuranceForm;
use Setup\Model\Insurance;
use Setup\Repository\InsuranceRepository;
use Zend\Db\Sql\Expression;

class InsuranceController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeForm(InsuranceForm::class); 
        $this->initializeRepository(InsuranceRepository::class);
    }

    public function indexAction() {
        return new ViewModel();
    }

    public function addAction() {
        $serviceList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_SERVICE_TYPES', 'SERVICE_TYPE_ID', ['SERVICE_TYPE_NAME'], "STATUS='E'", "SERVICE_TYPE_NAME", "ASC", null, true);
        $monthList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_MONTH_CODE', 'MONTH_ID', ['MONTH_EDESC'], "STATUS='E' and fiscal_year_id = (select fiscal_year_id from hris_fiscal_years where current_date between start_date and end_date)", "MONTH_EDESC", "ASC", null, true);
        $request = $this->getRequest();
        if ($request->isPost()) {
            // print_r($request->getPost()); die;
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $insurance = new Insurance();
                $insurance->exchangeArrayFromForm($this->form->getData());
                $insurance->insuranceId = ((int) Helper::getMaxId($this->adapter, "HRIS_INSURANCE_SETUP", "INSURANCE_ID")) + 1;
                $insurance->status = 'E';
                // if(insurance->type = 'SW'){
                //     $insurance->flatAmt = null;
                // }
                // $insurance->createdDt = Helper::getcurrentExpressionDateTime();
                $insurance->createdDt = Helper::getcurrentExpressionDate();
                $insurance->eligibleAfter = Helper::getExpressionDate($insurance->eligibleAfter);
                $insurance->createdBy = $this->employeeId;
                $this->repository->add($insurance);
                $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("insurance");
                
            }
        }
        
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'serviceList' => $serviceList,
            'monthList' => $monthList,
        ]);
    }
    public function getTableDataAction()
    {
        try {
            $recordList = $this->repository->getInsuranceTableData();
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function viewAction(){
        $id = $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $insurance = new Insurance();
        // print_r($insurance); die;
        if (!$request->isPost()) {
            $serviceList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_SERVICE_TYPES', 'SERVICE_TYPE_ID', ['SERVICE_TYPE_NAME'], "STATUS='E'", "SERVICE_TYPE_NAME", "ASC", null, true);
            $monthList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_MONTH_CODE', 'MONTH_ID', ['MONTH_EDESC'], "STATUS='E' and fiscal_year_id = (select fiscal_year_id from hris_fiscal_years where trunc(sysdate) between start_date and end_date)", "MONTH_EDESC", "ASC", null, true);
            // print_r("falksfksal"); die;
            $insurance->exchangeArrayFromDB($this->repository->fetchById($id));
            // print_r($insurance); die;
            $this->form->bind($insurance);
            
        } 
        // print_r("falksfksal"); die;
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'serviceTypeId' => $insurance->serviceType,
            'monthId' => $insurance->month,
            'serviceList' => $serviceList,
            'monthList' => $monthList
        ]);
    }

    public function editAction(){
        $id = $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $insurance = new Insurance();
        if (!$request->isPost()) {

            $serviceList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_SERVICE_TYPES', 'SERVICE_TYPE_ID', ['SERVICE_TYPE_NAME'], "STATUS='E'", "SERVICE_TYPE_NAME", "ASC", null, true);
            $monthList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_MONTH_CODE', 'MONTH_ID', ['MONTH_EDESC'], "STATUS='E' and fiscal_year_id = (select fiscal_year_id from hris_fiscal_years where trunc(sysdate) between start_date and end_date)", "MONTH_EDESC", "ASC", null, true);
            $insurance->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($insurance);
            // print_r($insurance->month); die;
        }
        else{
            // print_r($request->getPost()); die;

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $insurance = new Insurance();
                $insurance->exchangeArrayFromForm($this->form->getData());
                
                $insurance->insuranceId = $id ;
                $insurance->status = 'E';
                if($insurance->type == 'SW'){
                    $insurance->flatAmt = null;
                }else{
                    $insurance->month = null;
                }
                // $insurance->createdDt = Helper::getcurrentExpressionDateTime();
                $insurance->modifiedDt = Helper::getcurrentExpressionDate();
                $insurance->modifiedBy = $this->employeeId;
                $this->repository->edit($insurance, $id);
                $this->flashmessenger()->addMessage("Successfully Edited!!!");
                return $this->redirect()->toRoute("insurance");
                
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'serviceTypeId' => $insurance->serviceType,
            'monthId' => $insurance->month,
            'serviceList' => $serviceList,
            'monthList' => $monthList
        ]);
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');

        if (!$id) {
            return $this->redirect()->toRoute('insurance');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Successfully Deleted!!!");
        return $this->redirect()->toRoute('insurance');
    }

}
