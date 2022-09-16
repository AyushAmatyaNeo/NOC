<?php

namespace Workforce\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Workforce\Repository\WorkforceRepo;
use Zend\Form\Element;
use Workforce\Model\HeadOfficeModel;
use Application\Custom\CustomViewModel;


class WorkforceController extends HrisController
{
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(WorkforceRepo::class);
    }
    public function indexAction()
    {
      
        $request = $this->getRequest();
        if($request->isPost()) {
            $data = $request->getPost();
            $current = $this->repository->getCurrent($data['position'],$data['group'],$data['service'], $data['designation'], $data['location'], $data['department']);
            return new JsonModel(['success'=> true, 'data'=> $current]);
        }
    }
    public function getPastValuesAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $pastValues = $this->repository->getPastValues($data['position'],$data['group'],$data['service'], $data['designation'], $data['location'], $data['department']);
            return new JsonModel(['success'=> true, 'data'=> $pastValues]);
        }
    }

    public function headOfficeAction(){
        $id = (int) $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $model = new HeadOfficeModel();

        if($request->isPost()) {
            $data = $request->getPost()->getArrayCopy();
            // print_r($data);die;
            $HeadLocId = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_CODE", ["LOCATION_ID"], ["LOCATION_CODE" => 'HOBBMH',"STATUS" => 'E'], "LOCATION_CODE", "ASC", " ", FALSE, TRUE);
            $model->locationId=$HeadLocId['HOBBMH'];          
            $model->companyId = 1;
            $model->departmentId=$data['department'];
            $this->repository->deletePastDataByDepartment($data['department']);

            for ($i = 0; $i < count($data['functionalLevelList']); $i++){
                $model->workforceId=((int) Helper::getMaxId($this->adapter, "HRIS_WORKFORCE", "WORKFORCE_ID")) + 1;
                $model->functionalLevelId = $data['functionalLevelList'][$i];
                $model->serviceTypeId = $data['service'][$i];
                $model->serviceId = $data['serviceGroup'][$i];
                $model->serviceSubgroupId = $data['serviceSubgroup'][$i];
                $model->quota = $data['quota'][$i];
                $model->createdDt=Helper::getcurrentExpressionDate();
                $model->status='E';
                // echo('<pre>');print_r($model);die;

                $this->repository->addHo($model); 
            }
            $this->flashmessenger()->addMessage("Successfully Added!!!");
            return $this->redirect()->toRoute("workforce", ["action" => "headOffice"]);
        }
        $serviceGroups = EntityHelper::getTableList($this->adapter, hris_services, ['SERVICE_ID' => "SERVICE_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS" => "E"]);
        $serviceSubgroupList = [];
        foreach($serviceGroups as $sg){
            $tempServiceGroup = EntityHelper::getTableList($this->adapter, 'HRIS_SERVICE_SUBGROUP', ['SERVICE_SUBGROUP_ID' => "SERVICE_SUBGROUP_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS = 'E' and (SERVICE_ID = {$sg['SERVICE_ID']} or service_id is null)"] );
            $serviceSubgroupList[$sg['SERVICE_ID']] = $tempServiceGroup;
        }
        // EntityHelper::getTableList($this->adapter, HRIS_SERVICE_SUBGROUP, ['SERVICE_SUBGROUP_ID' => "SERVICE_SUBGROUP_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS" => "E"]);
        // print_r(EntityHelper::getTableList($this->adapter, HRIS_SERVICE_SUBGROUP, ['SERVICE_SUBGROUP_ID' => "SERVICE_SUBGROUP_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS" => "E"]));die;
        return Helper::addFlashMessagesToArray($this, [
            'branchList' => EntityHelper::getTableList($this->adapter, hris_locations, ['LOCATION_ID' => "LOCATION_ID", 'LOCATION_EDESC'=>"LOCATION_EDESC"], ["STATUS" => "E"]),
            'functionalLevelList'=> EntityHelper::getTableList($this->adapter, HRIS_FUNCTIONAL_LEVELs, ['FUNCTIONAL_LEVEL_ID' => "FUNCTIONAL_LEVEL_ID", 'FUNCTIONAL_LEVEL_EDESC'=>"FUNCTIONAL_LEVEL_EDESC"], ["STATUS" => "E"]),
            'groupList' => EntityHelper::getTableList($this->adapter, hris_service_group, ['SERVICE_GROUP_ID' => "SERVICE_GROUP_ID", 'SERVICE_GROUP_NAME'=>"SERVICE_GROUP_NAME"], ["STATUS" => "E"]),
            'serviceList' => EntityHelper::getTableList($this->adapter, hris_service_types, ['SERVICE_TYPE_ID' => "SERVICE_TYPE_ID", 'SERVICE_TYPE_NAME'=>"SERVICE_TYPE_NAME"], ["STATUS" => "E"]),
            'designationList' => EntityHelper::getTableList($this->adapter, hris_designations, ['DESIGNATION_ID' => "DESIGNATION_ID", 'DESIGNATION_TITLE'=>"DESIGNATION_TITLE"], ["STATUS" => "E"]),
            'departmentList' => EntityHelper::getTableList($this->adapter, hris_departments, ['DEPARTMENT_ID' => "DEPARTMENT_ID", 'DEPARTMENT_NAME'=>"DEPARTMENT_NAME"], ["STATUS" => "E"]),
            'serviceGroupList' => $serviceGroups,
            'serviceSubgroupList' => $serviceSubgroupList,
            'id' => $id,
        ]);
    }

    public function getHoWorkForceDataByDepartmentAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $workForceData = $this->repository->getHoWorkForceDataByDepartment($data['departmentId']);
            return new JsonModel(['success'=> true, 'data'=> $workForceData]);
        }
    }


    public function getQuotaDataByDepartmentAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $quotaData = $this->repository->getQuotaDataByDepartment($data['departmentId']);
            return new JsonModel(['success'=> true, 'data'=> $quotaData]);
        }
    }


    public function branchOfficeAction(){
        $id = (int) $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $model = new HeadOfficeModel();

        if($request->isPost()) {
            $data = $request->getPost()->getArrayCopy();
            //  print_r($data);die;      
            $model->companyId = 1;
            $model->branchId=$data['branch'];
            $this->repository->deletePastDataByBranch($data['branch']);

            for ($i = 0; $i < count($data['functionalLevelList']); $i++){
                $model->workforceId=((int) Helper::getMaxId($this->adapter, "HRIS_WORKFORCE", "WORKFORCE_ID")) + 1;
                $model->functionalLevelId = $data['functionalLevelList'][$i];
                $model->serviceTypeId = $data['service'][$i];
                $model->serviceId = $data['serviceGroup'][$i];
                $model->serviceSubgroupId = $data['serviceSubgroup'][$i];
                $model->quota = $data['quota'][$i];
                $model->createdDt=Helper::getcurrentExpressionDate();
                $model->status='E';
                // echo('<pre>');print_r($model);die;

                $this->repository->addBranchOffice($model); 
            }
            $this->flashmessenger()->addMessage("Successfully Added!!!");
            return $this->redirect()->toRoute("workforce", ["action" => "branchOffice"]);
        }
        $serviceGroups = EntityHelper::getTableList($this->adapter, hris_services, ['SERVICE_ID' => "SERVICE_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS" => "E"]);
        $serviceSubgroupList = [];
        foreach($serviceGroups as $sg){
            $tempServiceGroup = EntityHelper::getTableList($this->adapter, 'HRIS_SERVICE_SUBGROUP', ['SERVICE_SUBGROUP_ID' => "SERVICE_SUBGROUP_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS = 'E' and (SERVICE_ID = {$sg['SERVICE_ID']} or service_id is null)"] );
            $serviceSubgroupList[$sg['SERVICE_ID']] = $tempServiceGroup;
        }
        return Helper::addFlashMessagesToArray($this, [
            'branchList' => EntityHelper::getTableList($this->adapter, hris_branches, ['BRANCH_ID' => "BRANCH_ID", 'BRANCH_NAME'=>"BRANCH_NAME"], ["STATUS" => "E"]),
            'functionalLevelList'=> EntityHelper::getTableList($this->adapter, HRIS_FUNCTIONAL_LEVELs, ['FUNCTIONAL_LEVEL_ID' => "FUNCTIONAL_LEVEL_ID", 'FUNCTIONAL_LEVEL_EDESC'=>"FUNCTIONAL_LEVEL_EDESC"], ["STATUS" => "E"]),
            'groupList' => EntityHelper::getTableList($this->adapter, hris_service_group, ['SERVICE_GROUP_ID' => "SERVICE_GROUP_ID", 'SERVICE_GROUP_NAME'=>"SERVICE_GROUP_NAME"], ["STATUS" => "E"]),
            'serviceList' => EntityHelper::getTableList($this->adapter, hris_service_types, ['SERVICE_TYPE_ID' => "SERVICE_TYPE_ID", 'SERVICE_TYPE_NAME'=>"SERVICE_TYPE_NAME"], ["STATUS" => "E"]),
            'designationList' => EntityHelper::getTableList($this->adapter, hris_designations, ['DESIGNATION_ID' => "DESIGNATION_ID", 'DESIGNATION_TITLE'=>"DESIGNATION_TITLE"], ["STATUS" => "E"]),
            'departmentList' => EntityHelper::getTableList($this->adapter, hris_departments, ['DEPARTMENT_ID' => "DEPARTMENT_ID", 'DEPARTMENT_NAME'=>"DEPARTMENT_NAME"], ["STATUS" => "E"]),
            'serviceGroupList' => EntityHelper::getTableList($this->adapter, hris_services, ['SERVICE_ID' => "SERVICE_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS" => "E"]),
            'serviceSubgroupList' => $serviceSubgroupList,
            'id'=>$id,
        ]);
    }


    public function getQuotaDataByBranchAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $quotaData = $this->repository->getQuotaDataByBranch($data['branchId']);
            return new JsonModel(['success'=> true, 'data'=> $quotaData]);
        }
    }

    
    public function getWorkForceDataByBranchAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            // print_r($dat);die;
            $workForceData = $this->repository->getWorkForceDataByBranch($data['branchId']);
            return new JsonModel(['success'=> true, 'data'=> $workForceData]);
        }
    }

  


    public function validateWorkForceAction(){
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $error = '';
                $postedData = $request->getPost();
                $functionalLevels = $postedData['functionalLevels'];
                $serviceTypes = $postedData['serviceTypes'];
                $serviceGroups = $postedData['serviceGroups'];
                $serviceSubGroups = $postedData['serviceSubGroups'];
                $dublicate = false;
                $functional_service = [];
                for($i=0;$i<count($functionalLevels);$i++){
                    $temp = $functionalLevels[$i].'-'.$serviceTypes[$i].'-'.$serviceGroups[$i].'-'.$serviceSubGroups[$i];
                    if(in_array($temp,$functional_service)){
                        $dublicate=true;
                    }
                    array_push($functional_service, $temp);
                }
                if($dublicate){
                    $error = 'Cannot enter same Functional Level, Service Type, Service Group and Service Sub Group';
                }
                return new CustomViewModel(['success' => true, 'data' => $error, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }


    public function locationOfficeAction(){
        $id = (int) $this->params()->fromRoute('id');
        // print_r($id);die;
        $request = $this->getRequest();
        $model = new HeadOfficeModel();

        if($request->isPost()) {
            $data = $request->getPost()->getArrayCopy();
            // print_r($data);die;      
            $model->companyId = 1;
            $model->locationId=$data['location'];
            // print_r($model);die;
            $this->repository->deletePastDataByLocation($data['location']);
        
            for ($i = 0; $i < count($data['functionalLevelList']); $i++){
                $model->workforceId=((int) Helper::getMaxId($this->adapter, "HRIS_WORKFORCE", "WORKFORCE_ID")) + 1;
                $model->functionalLevelId = $data['functionalLevelList'][$i];
                $model->serviceTypeId = $data['service'][$i];
                $model->serviceId = $data['serviceGroup'][$i];
                $model->serviceSubgroupId = $data['serviceSubgroup'][$i];
                $model->quota = $data['quota'][$i];
                $model->createdDt=Helper::getcurrentExpressionDate();
                $model->status='E';
                // echo('<pre>');print_r($model);die;

                $this->repository->addLocationOffice($model); 
            }
            $this->flashmessenger()->addMessage("Successfully Added!!!");
            return $this->redirect()->toRoute("workforce", ["action" => "locationOffice"]);
        }
        $serviceGroups = EntityHelper::getTableList($this->adapter, hris_services, ['SERVICE_ID' => "SERVICE_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS" => "E"]);
        $serviceSubgroupList = [];
        foreach($serviceGroups as $sg){
            $tempServiceGroup = EntityHelper::getTableList($this->adapter, 'HRIS_SERVICE_SUBGROUP', ['SERVICE_SUBGROUP_ID' => "SERVICE_SUBGROUP_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS = 'E' and (SERVICE_ID = {$sg['SERVICE_ID']} or service_id is null)"] );
            $serviceSubgroupList[$sg['SERVICE_ID']] = $tempServiceGroup;
        }
        return Helper::addFlashMessagesToArray($this, [
            'locationList' => EntityHelper::getTableList($this->adapter, hris_locations, ['LOCATION_ID' => "LOCATION_ID", 'LOCATION_EDESC'=>"LOCATION_EDESC"], ["STATUS" => "E"]),
            'functionalLevelList'=> EntityHelper::getTableList($this->adapter, HRIS_FUNCTIONAL_LEVELs, ['FUNCTIONAL_LEVEL_ID' => "FUNCTIONAL_LEVEL_ID", 'FUNCTIONAL_LEVEL_EDESC'=>"FUNCTIONAL_LEVEL_EDESC"], ["STATUS" => "E"]),
            'groupList' => EntityHelper::getTableList($this->adapter, hris_service_group, ['SERVICE_GROUP_ID' => "SERVICE_GROUP_ID", 'SERVICE_GROUP_NAME'=>"SERVICE_GROUP_NAME"], ["STATUS" => "E"]),
            'serviceList' => EntityHelper::getTableList($this->adapter, hris_service_types, ['SERVICE_TYPE_ID' => "SERVICE_TYPE_ID", 'SERVICE_TYPE_NAME'=>"SERVICE_TYPE_NAME"], ["STATUS" => "E"]),
            'designationList' => EntityHelper::getTableList($this->adapter, hris_designations, ['DESIGNATION_ID' => "DESIGNATION_ID", 'DESIGNATION_TITLE'=>"DESIGNATION_TITLE"], ["STATUS" => "E"]),
            'departmentList' => EntityHelper::getTableList($this->adapter, hris_departments, ['DEPARTMENT_ID' => "DEPARTMENT_ID", 'DEPARTMENT_NAME'=>"DEPARTMENT_NAME"], ["STATUS" => "E"]),
            'serviceGroupList' => EntityHelper::getTableList($this->adapter, hris_services, ['SERVICE_ID' => "SERVICE_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS" => "E"]),
            'serviceSubgroupList' => $serviceSubgroupList,
            'id' => $id,
        ]);


    }
    public function getQuotaDataByLocationAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $quotaData = $this->repository->getQuotaDataByLocation($data['locationId']);
            return new JsonModel(['success'=> true, 'data'=> $quotaData]);
        }
    }

    public function getWorkForceDataByLocationAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            // print_r($dat);die;
            $workForceData = $this->repository->getWorkForceDataByLocation($data['locationId']);
            return new JsonModel(['success'=> true, 'data'=> $workForceData]);
        }
    }

    public function getServiceSubGroupListAction (){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $serviceSubGroup = EntityHelper::getTableList($this->adapter, 'HRIS_SERVICE_SUBGROUP', ['SERVICE_SUBGROUP_ID' => "SERVICE_SUBGROUP_ID", 'SERVICE_NAME'=>"SERVICE_NAME"], ["STATUS = 'E' and (SERVICE_ID = {$data->serviceId} or service_id is null)"] );
            return new JsonModel(['success'=> true, 'data'=> $serviceSubGroup]);
        }else {
            throw new Exception("The request should be of type post");
        }
    }


}




