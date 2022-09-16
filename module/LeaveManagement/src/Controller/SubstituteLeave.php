<?php

namespace LeaveManagement\Controller;

use Application\Controller\HrisController;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use LeaveManagement\Form\LeaveMasterForm;
use LeaveManagement\Model\LeaveMaster;
use LeaveManagement\Repository\SubstituteLeaveRepository;
use Setup\Model\Company;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class SubstituteLeave extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(SubstituteLeaveRepository::class);
        $this->initializeForm(LeaveMasterForm::class);
    }

    public function indexAction() {
        $employees = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"], ['STATUS'=>'E'], "FULL_NAME", "ASC", "-", false, true, null);

        return $this->stickFlashMessagesTo([
            'employees'=>$employees,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function getAllSubstituteLeaveAction (){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $rawList = $this->repository->getFilteredRecords($data);
            $list = iterator_to_array($rawList, false);
            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        }
    }

    public function addAction(){
        $employees = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"], ['STATUS'=>'E'], "FULL_NAME", "ASC", "-", false, true, null);

        return $this->stickFlashMessagesTo([
            'employees'=>$employees,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function getWohWodListAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $rawList = $this->repository->getWohWodList($data);
            $list = iterator_to_array($rawList, false);
            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        }
    }

    public function classifyAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            if($data['btnAction'] == 'btnSubstituteLeave'){
                foreach($data['data'] as $d){
                    $this->repository->classifyAsSubstituteLeave($d);
                    $this->flashmessenger()->addMessage("Successfully classified as substitute leave.");
                }
            }else if ($data['btnAction'] == 'btnOvertime'){
                foreach($data['data'] as $d){
                    $this->repository->classifyAsOverTime($d);
                    $this->flashmessenger()->addMessage("Successfully classified as overtime.");
                }
            }
            return new JsonModel(['success' => true, 'error' => '']);
        }
    }

    public function getAllSubtituteLeaveDataAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $rawList = $this->repository->getAllSubtituteLeaveData($data);
            $list = iterator_to_array($rawList, false);
            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        }
    }
    

}
