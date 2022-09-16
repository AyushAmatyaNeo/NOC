<?php

namespace DartaChalani\Controller;

use Application\Custom\CustomViewModel;
use Application\Controller\HrisController;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use DartaChalani\Form\DartaChalaniForm;
use DartaChalani\Repository\ChalaniRepository;
use DartaChalani\Repository\DispatchRepo;
use DartaChalani\Repository\FinalDispatchRepo;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use DartaChalani\Model\Chalani;
use DartaChalani\Model\Processes;
use DartaChalani\Model\UserAssign;
use DartaChalani\Model\ChalaniFinal;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element;
use Exception;
use phpDocumentor\Reflection\Types\This;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class DartaChalani extends HrisController
{
    protected $userAssignForm;
    protected $dispatchRepo;
    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        parent::__construct($adapter, $storage);
        $this->initializeForm(DartaChalaniForm::class);
        $this->initializeRepository(ChalaniRepository::class);
        $this->dispatchRepo = new DispatchRepo($this->adapter);
    }

    public function acknowledgeAction(){
        $id = (int) $this->params()->fromRoute("id");
        $request = $this->getRequest();
        $depList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_ID", "ASC", "-");
        $ofcList = EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_OFFICES", "OFFICE_ID", ["OFFICE_EDESC"], ["STATUS" => 'E'], "OFFICE_ID", "ASC", " ", FALSE, TRUE);
        $chalani = new Chalani();
        if (!$request->isPost()) {
            $data = $this->repository->fetchById($id);
            $chalani->exchangeArrayFromDB($data->getArrayCopy());
            $select = new Element\Select('responseFlag');
            $select->setLabel('Response Flag');
            $select->setValueOptions(array(
                'N' => 'No', 'Y' => 'Yes'
            ));

            $this->form->add($select);
            $this->form->bind($chalani);
        } else {
            
            $chalaniFinal = new ChalaniFinal();
            $chalaniFinal->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->setData($request->getPost());

            // $this->form->bind($chalaniFinal);

            if ($this->form->isValid()) {
                $chalaniFinal->dispatchId = ((int) Helper::getMaxId($this->adapter, "DC_DISPATCH", "DISPATCH_ID")) + 1;
                $chalaniFinal->dispatchDt = $_POST['dispatchDt'];
                $chalaniFinal->createdBy = $this->employeeId;
                $chalaniFinal->createdDt = Helper::getcurrentExpressionDate();
                $dispatchDt = $chalaniFinal->dispatchDt;
                $chalaniFinal->dispatchDt = Helper::getExpressionDate($chalaniFinal->dispatchDt);
                $chalaniFinal->modifiedDt = Helper::getExpressionDateHana($chalaniFinal->modifiedDt);


                
                $chalaniFinal->dispatchCode = $this->repository->getDispatchProcessId();

                $chalaniFinal->letterNumber = $this->repository->getCode($dispatchDt, $chalaniFinal->fromLocationId);
                $finalDispatchRepo = new FinalDispatchRepo($this->adapter);
                $finalDispatchRepo->add($chalaniFinal);
                $this->repository->editProcessId($id);
                $this->flashmessenger()->addMessage("Document Successfully Dispatched!!!");
                return $this->redirect()->toRoute("dartachalani", ["action" => "dispatched"]);
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'selectedLetter' => $data['LETTER_REF_NO'],
            'letters' => EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_REGISTRATION", "REG_ID", ["DESCRIPTION"], ["RESPONSE_FLAG" => 'Y'], "REG_ID", "ASC", " ", FALSE, TRUE),
            'departments' => $depList,
            'selectedDepartment' => $data['FROM_DEPARTMENT_CODE'],
            'offices' => $ofcList,
            'selectedOffice' => $data['TO_OFFICE_ID']
        ]);
    }



    public function viewAction(){
        $id = (int) $this->params()->fromRoute("id");
        $request = $this->getRequest();
        $depList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_ID", "ASC", "-");
        $ofcList = EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_OFFICES", "OFFICE_ID", ["OFFICE_EDESC"], ["STATUS" => 'E'], "OFFICE_ID", "ASC", " ", FALSE, TRUE);
        $chalani = new Chalani();
        
        if (!$request->isPost()) {
            $data = $this->repository->fetchById($id);
            $chalani->exchangeArrayFromDB($data->getArrayCopy());
            //print_r($chalani);die;
            $select = new Element\Select('responseFlag');
            $select->setLabel('Response Flag');
            $select->setValueOptions(array(
                'N' => 'No', 'Y' => 'Yes'
            ));

            $this->form->add($select);
            $this->form->bind($chalani);
        } else {
            
            $chalaniFinal = new ChalaniFinal();
            $chalaniFinal->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->setData($request->getPost());
            // $this->form->bind($chalaniFinal);
            
            if ($this->form->isValid()) {
                $chalaniFinal->dispatchId = ((int) Helper::getMaxId($this->adapter, "DC_DISPATCH", "DISPATCH_ID")) + 1;
                $chalaniFinal->dispatchDt = $_POST['dispatchDt'];

                $chalaniFinal->dispatchCode = $this->repository->getDispatchProcessId();
                
                $chalaniFinal->letterNumber = $this->repository->getCode($chalaniFinal->dispatchDt, $chalaniFinal->fromLocationId);

                $finalDispatchRepo = new FinalDispatchRepo($this->adapter);
                $finalDispatchRepo->add($chalaniFinal);
                $this->repository->editProcessId($id);
                $this->flashmessenger()->addMessage("Document Successfully Dispatched!!!");
                return $this->redirect()->toRoute("dartachalani");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'departments' => $depList,
            'selectedDepartment' => $data['FROM_DEPARTMENT_CODE'],
            'letters' => EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_REGISTRATION", "REG_ID", ["DESCRIPTION"], ["RESPONSE_FLAG" => 'Y'], "REG_ID", "ASC", " ", FALSE, TRUE),
            'selectedLetter' => $data['LETTER_REF_NO'],
            'offices' => $ofcList,
            'selectedOffice' => $data['TO_OFFICE_ID'],
            'processId' => $chalani->processId,
            'toOtherOffice'=>$chalani->toOtherOffice,
            'processes' => EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_PROCESSES", "PROCESS_ID", ["PROCESS_EDESC"], ["STATUS" => 'E', "IS_REGISTRATION" => 'N'], "PROCESS_ID", "ASC", " ", FALSE, TRUE),
        ]);
    }

    public function getSearchResultsAction(){
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
    
            
            $recordList = $this->repository->getOutgoingbyId($data, $this->employeeId);
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
    public function indexAction()
    {
        $depList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_ID", "ASC", "-");
        $processList = EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_PROCESSES", "PROCESS_ID", ["PROCESS_EDESC"], "is_registration='N'", "PROCESS_ID", "ASC", "-");
        $locationList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_LOCATIONS', 'LOCATION_ID', ['LOCATION_EDESC'], "STATUS='E'", "LOCATION_EDESC", "ASC", null, true);
        $request = $this->getRequest();
        $endProcess = $this->repository->getDispatchProcess();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchData();
                $chalaniList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $chalaniList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return $this->stickFlashMessagesTo([
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'acl' => $this->acl,
            'employeeDetail' => $this->storageData['employee_detail'],
            'preference' => $this->preference,
            'department' => $depList,
            'endProcess' => $endProcess,
            'processes' => $processList,
            'locationList'=> $locationList,
        ]);
    }

    public function addAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $chalani = new Chalani();
                $chalani->exchangeArrayFromForm($this->form->getData());  //gets info from the form
                $HeadLocCode = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_CODE", ["LOCATION_ID"], ["LOCATION_CODE" => 'HOBBMH',"STATUS" => 'E'], "LOCATION_CODE", "ASC", " ", FALSE, TRUE);
                $chalani->fromLocationId = $postedData['locationId']?$postedData['locationId']:$HeadLocCode['HOBBMH'];
                $chalani->dispatchDraftId = ((int) Helper::getMaxId($this->adapter, "DC_DISPATCH_DRAFT", "DISPATCH_DRAFT_ID")) + 1;
                $chalani->dispatchTempCode = $chalani->dispatchDraftId;
                $chalani->draftDt = Helper::getExpressionDate($chalani->draftDt);
                $chalani->createdDt = Helper::getcurrentExpressionDate();
                $chalani->fromDepartmentCode = $postedData['departmentId'];
                $chalani->createdBy = $this->employeeId;
                $chalani->responseFlag = $postedData['responseFlag'];
                $chalani->modifiedDt = Helper::getcurrentExpressionDate();
                $chalani->deletedDt = Helper::getExpressionDate($chalani->deletedDt);
                $chalani->modifiedBy = $this->employeeId;
                $chalani->status = 'E';
                $chalani->processId = $this->repository->getAddProcessId();
                $chalani->letterRefNo = $postedData['letterRefNo'];
                $chalani->toOtherOffice=$postedData['toOtherOffice'];
                $chalani->filePath = !empty($postedData['fileUploadList']) ? $postedData['fileUploadList'] : '' ;
                $this->repository->add($chalani);
                $this->dispatchRepo->pushFileLink($chalani, $this->employeeId);
                $this->flashmessenger()->addMessage("Chalani Successfully Added!!!");
                return $this->redirect()->toRoute("dartachalani");
            }
        }

        $select = new Element\Select('responseFlag');
        $select->setLabel('Is this a dispatch for any prevailing registration');
        $select->setValueOptions(array(
            'N' => 'No', 'Y' => 'Yes'
        ));

        $locations = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_ID", ["LOCATION_EDESC"], ["STATUS" => 'E'], "LOCATION_EDESC", "ASC", " ", FALSE, TRUE);
        $departments = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_NAME", "ASC", " ", FALSE, TRUE);
        $departmentCodes = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_CODE"], ["STATUS" => 'E'], "DEPARTMENT_CODE", "ASC", " ", FALSE, TRUE);
        $naId = array_search ('NA', $departmentCodes);
        $departments[$naId] = 'Locations';
        $this->form->add($select);
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'letters' => EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_REGISTRATION", "REG_ID", ["DESCRIPTION"], ["RESPONSE_FLAG" => 'Y'], "REG_ID", "ASC", " ", FALSE, TRUE),
            'offices' => EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_OFFICES", "OFFICE_ID", ["OFFICE_EDESC"], ["STATUS" => 'E'], "OFFICE_ID", "ASC", " ", FALSE, TRUE),
            'departments' => $departments,
            'customRenderer' => Helper::renderCustomView(),
            'locations' => $locations,
        ]);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute("id");
        $request = $this->getRequest();
        $depList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_ID", "ASC", "-");
        $ofcList = EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_OFFICES", "OFFICE_ID", ["OFFICE_EDESC"], ["STATUS" => 'E'], "OFFICE_ID", "ASC", " ", FALSE, TRUE);
        $departmentCodes = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_CODE"], ["STATUS" => 'E'], "DEPARTMENT_CODE", "ASC", " ", FALSE, TRUE);
        $naId = array_search ('NA', $departmentCodes);
        $depList[$naId] = 'Locations';
        $chalani = new Chalani();
        if (!$request->isPost()) {
            $data = $this->repository->fetchById($id);
            $chalani->exchangeArrayFromDB($data->getArrayCopy());
            $select = new Element\Select('responseFlag');
            $select->setLabel('Response Flag');
            $select->setValueOptions(array(
                'N' => 'No', 'Y' => 'Yes'
            ));

            $this->form->add($select);
            $this->form->bind($chalani);
            
        } else {
            $postData = $request->getPost();
            $this->form->setData($request->getPost());
            $HeadLocCode = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_CODE", ["LOCATION_ID"], ["LOCATION_CODE" => 'HOBBMH',"STATUS" => 'E'], "LOCATION_CODE", "ASC", " ", FALSE, TRUE);
            if ($this->form->isValid()) {
                $pastValues= $this->repository->getPastValues($id);
                $chalani->exchangeArrayFromForm($this->form->getData());
                $chalani->dispatchTempCode = $pastValues[0]['DISPATCH_TEMP_CODE'];
                $chalani->fromLocationId = $postData['locationId']?$postData['locationId']:$HeadLocCode['HOBBMH'];
                $chalani->modifiedDt = Helper::getcurrentExpressionDate();
                $chalani->modifiedBy = $this->employeeId;
                $chalani->draftDt = Helper::getExpressionDate($chalani->draftDt);
                $chalani->documentDt = Helper::getExpressionDate($chalani->documentDt);
                $chalani->fromDepartmentCode=$_POST['departmentId'];
                
                if($postData['responseFlag'] == 'Y'){
                    $chalani->letterRefNo = $postData['letterRefNo'];
                }
                if(!$postData['fileUploadList']){
                    $chalani->filePath = '';
                }else if(!$postData['fileUploadList'][1]){
                    $chalani->filePath = $pastValues[0]['FILE_PATH'];
                }else{
                    $chalani->filePath = $postData['fileUploadList'];
                }
                
                $this->repository->edit($chalani, $id);
                $this->flashmessenger()->addMessage("Chalani Successfully Updated!!!");
                return $this->redirect()->toRoute("dartachalani");
            }
        }
        $locations = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_ID", ["LOCATION_EDESC"], ["STATUS" => 'E'], "LOCATION_EDESC", "ASC", " ", FALSE, TRUE);
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'departments' => $depList,
            'selectedDepartment' => $data['FROM_DEPARTMENT_CODE'],
            'letters' => EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_REGISTRATION", "REG_ID", ["DESCRIPTION"], ["RESPONSE_FLAG" => 'Y'], "REG_ID", "ASC", " ", FALSE, TRUE),
            'selectedLetter' => $data['LETTER_REF_NO'],
            'offices' => $ofcList,
            'selectedOffice' => $data['TO_OFFICE_ID'],
            'selectedLocation' => $data['FROM_LOCATION_ID'],
            'processId' => $chalani->processId,
            'toOtherOffice'=>$chalani->toOtherOffice,
            'fromLocation' => $chalani->fromLocationId,
            'locations' => $locations,
            'processes' => EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_PROCESSES", "PROCESS_ID", ["PROCESS_EDESC"], ["STATUS" => 'E', "IS_REGISTRATION" => 'N'], "PROCESS_ID", "ASC", " ", FALSE, TRUE),
        ]);

    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('dartachalani');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Chalani Successfully Deleted!!!");
        return $this->redirect()->toRoute('dartachalani');
    }

    public function forwardAction()
    {
        
        $id = (int) $this->params()->fromRoute("id");
        $request = $this->getRequest();
        $depList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_ID", "ASC", "-");
        $ofcList = EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_OFFICES", "OFFICE_ID", ["OFFICE_EDESC"], ["STATUS" => 'E'], "OFFICE_ID", "ASC", " ", FALSE, TRUE);
        
        $departmentCodes = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_CODE"], ["STATUS" => 'E'], "DEPARTMENT_CODE", "ASC", " ", FALSE, TRUE);
        $naId = array_search ('NA', $departmentCodes);
        $depList[$naId] = 'Locations';

        $chalani = new Chalani();

        // $processList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'dc_processes', 'PROCESS_ID', ['PROCESS_EDESC'], "is_registration='N'", "process_edesc", "ASC", null, true);

        if (!$request->isPost()) {
            $data = $this->repository->fetchById($id);
            $chalani->exchangeArrayFromDB($data->getArrayCopy());
            

            $select = new Element\Select('responseFlag');
            $select->setLabel('Response Flag');
            $select->setValueOptions(array(
                'N' => 'No', 'Y' => 'Yes'
            ));
            $this->form->add($select);
            $this->form->bind($chalani);
            
            
        } else {
            
            $postData = $request->getPost();
            
            $this->form->setData($postData);
            
            if ($this->form->isValid()) {
                $userAssign = new UserAssign();
                $chalani->exchangeArrayFromForm($this->form->getData());
                $HeadLocCode = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_CODE", ["LOCATION_ID"], ["LOCATION_CODE" => 'HOBBMH',"STATUS" => 'E'], "LOCATION_CODE", "ASC", " ", FALSE, TRUE);
                $chalani->fromLocationId = $postData['locationId']?$postData['locationId']:$HeadLocCode['HOBBMH'];
                $chalani->modifiedDt = Helper::getcurrentExpressionDate();
                $chalani->filePath = $postData['fileUploadList'];
                $chalani->modifiedBy = $this->employeeId;
                $chalani->processId = $postData['processId'];
                
                $pastValues= $this->repository->getPastValues($id);
                $chalani->dispatchDraftId = $pastValues[0]['DISPATCH_DRAFT_ID'];
                $chalani->dispatchTempCode = $pastValues[0]['DISPATCH_TEMP_CODE'];
                $chalani->draftDt = $pastValues[0]['DRAFT_DATE'];
                $chalani->fromDepartmentCode= $_POST['departmentId'];
                $chalani->toOfficeCode = $pastValues[0]['TO_OFFICE_ID'];
                $chalani->status = $pastValues[0]['STATUS'];
                $chalani->createdBy = $pastValues[0]['CREATED_BY'];
                $chalani->createdDt = $pastValues[0]['CREATED_DT'];
                $chalani->responseFlag = $pastValues[0]['RESPONSE_FLAG'];
                if(!$postData['fileUploadList']){
                    $chalani->filePath = '';
                }else if(!$postData['fileUploadList'][1]){
                    $chalani->filePath = $pastValues[0]['FILE_PATH'];
                }
                $this->repository->edit($chalani, $id);
                
                $userAssign->processId = $chalani->processId;
                
                if($userAssign->processId == $this->repository->getAddProcessId()){
                    $userAssign->assignId = ((int) Helper::getMaxId($this->adapter, "DC_USER_ASSIGN", "ASSIGN_ID")) + 1;
                    $userAssign->employeeId = $this->employeeId;
                    $userAssign->processId = $postData['processId'];
                    $userAssign->status = 'E';
                    $userAssign->dispatchDraftId = $id;
                    $userAssign->createdDt = Helper::getcurrentExpressionTime();
                    $userAssign->createdBy = $this->employeeId;
                    $this->repository->forward($userAssign);
                }
                else{
                    $userAssign->departmentId = $postData['departmentId'];
                    if($postData['locationId']){
                        $empList = $this->repository->getLocationWiseEmployeeList($chalani->fromLocationId);
                    }else{
                        $empList = $this->repository->getEmployeeList($userAssign->departmentId);
                    }
                    foreach($empList as $e){
                        $userAssign->assignId = ((int) Helper::getMaxId($this->adapter, "DC_USER_ASSIGN", "ASSIGN_ID")) + 1;
                        $userAssign->employeeId = $e['EMPLOYEE_ID'];
                        $userAssign->status = 'E';
                        $userAssign->processId = $postData['processId'];
                        $userAssign->location_id = $chalani->fromLocationId;
                        $userAssign->dispatchDraftId = $id;
                        $userAssign->createdDt = Helper::getcurrentExpressionDate();
                        $userAssign->createdBy = $this->employeeId;
                        $this->repository->forward($userAssign);
                        $this->repository->sendNotification($userAssign);
                    }
                }

                $chalani->dispatchDraftId = $id;
                if($chalani->filePath)
                {
                    $this->dispatchRepo->pushFileLink($chalani, $this->employeeId);
                }
                
                $this->flashmessenger()->addMessage("Chalani Successfully Forwarded!!!");
                return $this->redirect()->toRoute("dartachalani");
                
                
                
            }
        }
        $locations = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_ID", ["LOCATION_EDESC"], ["STATUS" => 'E'], "LOCATION_EDESC", "ASC", " ", FALSE, TRUE);
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'departments' => $depList,
            'selectedDepartment' => $data['FROM_DEPARTMENT_CODE'],
            'letters' => EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_REGISTRATION", "REG_ID", ["DESCRIPTION"], ["RESPONSE_FLAG" => 'Y'], "REG_ID", "ASC", " ", FALSE, TRUE),
            'selectedLetter' => $data['LETTER_REF_NO'],
            'offices' => $ofcList,
            'selectedOffice' => $data['TO_OFFICE_ID'],
            'locations' => $locations,
            'processes' => EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_PROCESSES", "PROCESS_ID", ["PROCESS_EDESC"], ["STATUS" => 'E', "IS_REGISTRATION" => 'N', "PROCESS_END_FLAG" => 'N', "PROCESS_START_FLAG" => 'N'], "PROCESS_ID", "ASC", " ", FALSE, TRUE),
            'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FULL_NAME"], ["DEPARTMENT_ID" => 73/*$deptId*/], "FIRST_NAME", "ASC", " ", FALSE, TRUE),
            
        ]);
    }

    public function getSearchAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $recordList = $this->repository->getSearchResults($data);

            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function fileUploadAction()
    {
        $request = $this->getRequest();
        $responseData = [];
        $files = $request->getFiles()->toArray();
        
        try {
            if (sizeof($files) > 0) {
                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'txt' || $ext == 'pdf' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext=='docx' || $ext=='odt' || $ext=='doc' ) {                    
                    $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                    $unique = Helper::generateUniqueName();
                    $newFileName = $unique . "." . $ext;
                    $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/dartachalani_docs/" . $newFileName);
                    if (!$success) {
                        throw new Exception("Upload unsuccessful.");
                    }
                    $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
                } else { 
                    throw new Exception("Upload unsuccessful.");

                }
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return new JsonModel($responseData);
    }

    public function pushFileLinkAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            return new JsonModel(['success' => true, 'data' => $data, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function dispatchedAction(){
        $departmentList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_DEPARTMENTS', 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], "STATUS='E'", "DEPARTMENT_NAME", "ASC", null, true);
        $officeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_OFFICES', 'OFFICE_ID', ['OFFICE_EDESC'], "STATUS='E'", "OFFICE_EDESC", "ASC", null, true);
        $locationList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_LOCATIONS', 'LOCATION_ID', ['LOCATION_EDESC'], "STATUS='E'", "LOCATION_EDESC", "ASC", null, true);
        return Helper::addFlashMessagesToArray($this, [
             'dept' => $departmentList,
             'officeList' => $officeList,
             'locationList' => $locationList,
        ]);
    }

    public function getTableDataAction(){
        try {
            $checkList = $this->dispatchRepo->fetchTableData();
            return new JsonModel([
                "success" => "true",
                "data" => $checkList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }

    }

    public function getSearchDataAction(){
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            //print_r($data);die;
        
           
            $searchList = $this->dispatchRepo->fetchSearchData($data);

            return new JsonModel([
                "success" => "true",
                "data" => $searchList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function dispatchDocAction(){
        $id = $this->params()->fromRoute('id');
        $dispatchDoc = new ChalaniFinal();
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $data = $this->dispatchRepo->fetchById($id);
        }
        else{
            $documentHistory = $this->dispatchRepo->getDocumentHistory($id);
            return new JsonModel([
                "success" => "true",
                "data" => $documentHistory,
                "message" => null
            ]);
        }
        $documentHistory = $this->dispatchRepo->getDocumentHistory($id);
        
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'documentHistory' => $documentHistory,
            'id' => $id,
            'fileId' => $data[0]['REG_ID'],
            'data' => $data
        ]);
    }

    public function pullFilebyIdAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $returnData = $this->dispatchRepo->pullFilebyId($data->id);
            return new JsonModel(['success' => true, 'data' => $returnData, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function pullDispatchFilebyIdAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $returnData = $this->dispatchRepo->pullDispatchFilebyId();
            return new JsonModel(['success' => true, 'data' => $returnData, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function pushDispatchedFileAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $this->dispatchRepo->linkDispatchFiles($data, $this->employeeId);
            return new JsonModel(['success' => true, 'data' => null, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function pullFilesbyIdAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $returnData = $this->dispatchRepo->pullAllFilesbyId($data['id']);
            return new JsonModel(['success' => true, 'data' => $returnData, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function getDepartmentCodeAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $code = '';
            if($data['deartment_id']){
                $departmentCode = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_CODE"], ["DEPARTMENT_ID" => $data['deartment_id'], "STATUS" => 'E'], "DEPARTMENT_CODE", "ASC", " ", FALSE, TRUE);
                $code = $departmentCode[$data['deartment_id']];
            }
            return new JsonModel(['success' => true, 'data' => $code, 'message' => null]);
        }
    }
    public function getOfficeCodeAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $code = '';
            if($data['office_id']){
                $officeCode = EntityHelper::getTableKVListWithSortOption($this->adapter, "DC_OFFICES", "OFFICE_ID", ["OFFICE_CODE"], ["OFFICE_ID" => $data['office_id'], "STATUS" => 'E'], "OFFICE_CODE", "ASC", " ", FALSE, TRUE);
                $code = $officeCode[$data['office_id']];
            }
            return new JsonModel(['success' => true, 'data' => $code, 'message' => null]);
        }
    }

}
