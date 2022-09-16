<?php

namespace DocumentRegistration\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use DocumentRegistration\Repository\IncomingRepo;
use DocumentRegistration\Repository\FinalRegRepo;
use DocumentRegistration\Repository\UserAssignRepo;
use DocumentRegistration\Form\AddIncommingForm;
use DocumentRegistration\Model\IncommingDocument;
use DocumentRegistration\Model\UserAssignModel;
use DocumentRegistration\Model\FinalRegistrationTable;
use Zend\Form\Element;


class IncomingController extends HrisController
{

    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(IncomingRepo::class);
        $this->initializeForm(AddIncommingForm::class);
    } 
    public function indexAction()
    {

        $departmentList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_DEPARTMENTS', 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], "STATUS='E'", "DEPARTMENT_NAME", "ASC", null, true);
        $endProcess = $this->repository->getEndProcess();

        $organizationList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_OFFICES', 'OFFICE_ID', ['OFFICE_EDESC'], "STATUS='E'", "OFFICE_EDESC", "ASC", null, true);

        $locationList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_LOCATIONS', 'LOCATION_ID', ['LOCATION_EDESC'], "STATUS='E'", "LOCATION_EDESC", "ASC", null, true);

        return Helper::addFlashMessagesToArray($this, [
            'acl' => $this->acl,
            'dept' => $departmentList,
            'organizationList' => $organizationList,
            'locationList'=> $locationList,
            'endProcess' => $endProcess,
            ]);
        }

    // public function alert(){
    //     echo '<script>alert("Please use given extension file only")</script>';
    // }
    public function acknowledgeAction(){
        $id = $this->params()->fromRoute('id');
        $officeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_OFFICES', 'OFFICE_ID', ['OFFICE_EDESC'], "STATUS='E'", "OFFICE_EDESC", "ASC", null, true);
        $processList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'dc_processes', 'PROCESS_ID', ['PROCESS_EDESC'], "is_registration='Y'", "process_edesc", "ASC", null, true);
        $fiscalYear = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_FISCAL_YEARS', 'FISCAL_YEAR_ID', ['FISCAL_YEAR_NAME'], "STATUS='E'", "FISCAL_YEAR_NAME", "ASC", null, true);
        $employee = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FIRST_NAME", "ASC", null, true);
        
        
        $request = $this->getRequest();
        $addDocument = new IncommingDocument();
        if (!$request->isPost()) {
            $fromOfficeId = $addDocument->fromOfficeId;
            $addDocument->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            if ($addDocument->fromOfficeId != null){
                $addDocument->fromOfficeId=$this->repository->getFromOfficeName($id);
            }
            if ($addDocument->receiverName != null){
                $addDocument->receiverName=$this->repository->getReceiverName($id);
            }  
            if ($addDocument->receivingDepartment != null){
                $addDocument->receivingDepartment=$this->repository->getReceivingDepartment($id);
            } 
            if($addDocument->sbFiscalYear == NULL){
                $addDocument->choiceFlag = 'N';
            } else {
                $addDocument->choiceFlag = 'Y';
            }
            if($addDocument->ksFiscalYear == NULL){
                $addDocument->choiceFlagKS = 'N';
            } else {
                $addDocument->choiceFlagKS = 'Y';
            }
            $this->form->bind($addDocument);
        } else {
            $finalRegTable = new FinalRegistrationTable();
            $finalRegTable->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $finalRegTable->registrationID = ((int) Helper::getMaxId($this->adapter, "DC_REGISTRATION", "REG_ID")) + 1;
                $finalRegTable->registrationDate = Helper::getcurrentExpressionDate();
                $finalRegTable->receivingLetterReferenceDate = Helper::getExpressionDate($finalRegTable->receivingLetterReferenceDate);
                $finalRegTable->createdDt = Helper::getExpressionDateHana($finalRegTable->createdDt);
                $finalRegRepo = new FinalRegRepo($this->adapter);
                $finalRegRepo->add($finalRegTable);
                $this->repository->editProcessId($id);
                $this->flashmessenger()->addMessage("Incomming document Successfully Acknowledged!!!");
                return $this->redirect()->toRoute("incoming-document");
            }
        }
        
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'processList' => $processList,
            'processId' => $addDocument->processId,
            'fromOfficeId' => $fromOfficeId,
            'officeList' => $officeList,
            'fiscalYear' => $fiscalYear,
            'selectedfiscalYear' => $addDocument->sbFiscalYear,
            'selectedfiscalYearKS' => $addDocument->ksFiscalYear,
            'employee' => $employee,
            'selectedEmployee' => $addDocument->employeeId,
            'selectedEmployee2' => $addDocument->empId
            
        ]);
        
    }
    public function forwardAction()
    {
        $officeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_OFFICES', 'OFFICE_ID', ['OFFICE_EDESC'], "STATUS='E'", "OFFICE_EDESC", "ASC", null, true);

        $processList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'dc_processes', 'PROCESS_ID', ['PROCESS_EDESC'], ["is_registration='Y'", "PROCESS_END_FLAG='N'", "PROCESS_START_FLAG='N'", "STATUS='E'"],"process_edesc", "ASC", null, true);
        $departmentList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_DEPARTMENTS', 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], "STATUS='E'", "DEPARTMENT_NAME", "ASC", null, true);
        $departmentCodes = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_CODE"], ["STATUS" => 'E'], "DEPARTMENT_CODE", "ASC", " ", FALSE, TRUE);
        $naId = array_search ('NA', $departmentCodes);
        $departmentList[$naId] = 'Locations';
        $receiverList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FULL_NAME", "ASC", null, true);
        $fiscalYear = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_FISCAL_YEARS', 'FISCAL_YEAR_ID', ['FISCAL_YEAR_NAME'], "STATUS='E'", "FISCAL_YEAR_NAME", "ASC", null, true);
        $employee = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FIRST_NAME", "ASC", null, true);
        

        $id = $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $addDocument = new IncommingDocument();
        $userAssign = new UserAssignModel();
        
        if (!$request->isPost()) {
            $addDocument->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $fromOfficeId = $addDocument->fromOfficeId;
            if ($addDocument->fromOfficeId != null){
                $addDocument->fromOfficeId=$this->repository->getFromOfficeName($id);
            }
            if ($addDocument->receiverName != null){
                $addDocument->receiverName=$this->repository->getReceiverName($id);
            }  
            if ($addDocument->receivingDepartment != null){
                $addDocument->receivingDepartment=$this->repository->getReceivingDepartment($id);
            } 
            if($addDocument->sbFiscalYear == NULL){
                $addDocument->choiceFlag = 'N';
            } else {
                $addDocument->choiceFlag = 'Y';
            }
            if($addDocument->ksFiscalYear == NULL){
                $addDocument->choiceFlagKS = 'N';
            } else {
                $addDocument->choiceFlagKS = 'Y';
            }
            $this->form->bind($addDocument);
        } else {
            $HeadLocCode = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_CODE", ["LOCATION_ID"], ["LOCATION_CODE" => 'HOBBMH',"STATUS" => 'E'], "LOCATION_CODE", "ASC", " ", FALSE, TRUE);
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $addDocument->exchangeArrayFromForm($this->form->getData());
                $postData = $this->form->getData();
                $addDocument->locationId = $this->repository->getLocationId($id);;
                $addDocument->fromLocationId = $this->repository->getFromLocationId($id);
                $addDocument->modifiedDt = Helper::getcurrentExpressionDate();
                $addDocument->modifiedBy = $this->employeeId;
                $addDocument->fromOfficeId = $this->repository->getFromOfficeId($id);
                $addDocument->registrationDate = $this->repository->getRegistrationDate($id);
                $addDocument->documentDate = $this->repository->getDocumentDate($id);
                $addDocument->receivingLetterReferenceDate = $this->repository->getReceivingLetterReferenceDate($id);
                $addDocument->responseFlag = $this->repository->getResponseFlag($id);
                $addDocument->completionDate = $this->repository->getCompletionDate($id);
                if($postData['locationId']){
                    $employeeList = $this->repository->getLocationWiseEmployeeList($postData['locationId']);
                }else{
                    $employeeList = $this->repository->getEmployeeList($addDocument->receivingDepartment);
                }
                $userAssignRepo = new UserAssignRepo($this->adapter);
                foreach($employeeList as $e){
                    $userAssign->assign_id=((int) Helper::getMaxId($this->adapter, "DC_USER_ASSIGN", "ASSIGN_ID")) + 1;
                    if($addDocument->receivingDepartment){
                        $userAssign->department_id=$addDocument->receivingDepartment;
                    }else{
                        $userAssign->location_id=$addDocument->locationId;
                    }
                    $userAssign->process_id=$addDocument->processId;
                    $userAssign->status='E';
                    $userAssign->created_dt = Helper::getcurrentExpressionDateTime();
                    $userAssign->created_by = $this->employeeId;
                    $userAssign->employee_id = $e['EMPLOYEE_ID'];
                    $userAssign->reg_draft_id= $id;
                    $userAssign->remarks=$addDocument->remarks;
                    // echo"<pre>";print_r($userAssign);die;
                    $userAssignRepo->add($userAssign);
                    
                }
                $this->repository->edit($addDocument, $id);
                $this->flashmessenger()->addMessage("Incomming document Successfully Updated!!!");
                return $this->redirect()->toRoute("incoming-document");
            }
        }
        $locations = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_ID", ["LOCATION_EDESC"], ["STATUS" => 'E'], "LOCATION_EDESC", "ASC", " ", FALSE, TRUE);

        return Helper::addFlashMessagesToArray($this, [
            'departments' => EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_DEPARTMENTS', 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], "STATUS='E'", "DEPARTMENT_NAME", "ASC", null, true),
            'processList' => EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_PROCESSES', 'PROCESS_ID', ['PROCESS_EDESC'], ["STATUS='E'"], "PROCESS_EDESC", "ASC", null, true),
            'form' => $this->form,
            'id' => $id,
            'fromOffice' => $fromOfficeId,
            'customRenderer' => Helper::renderCustomView(),
            'officeList' => $officeList,
            'processList' => $processList,
            'departmentList' => $departmentList,
            'receiverList' => $receiverList,
            'fiscalYear' => $fiscalYear,
            'selectedfiscalYear' => $addDocument->sbFiscalYear,
            'selectedfiscalYearKS' => $addDocument->ksFiscalYear,
            'employee' => $employee,
            'selectedEmployee' => $addDocument->employeeId,
            'selectedEmployee2' => $addDocument->empId,
            'locations' => $locations,
            
        ]);
    }
          
    public function getAllIncomingDataAction()
    {
        try {
            $recordList = $this->repository->getIncoming();
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }


    public function getAllIncomingDatabyIdAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $recordList = $this->repository->getIncomingbyId($data);
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    
    public function addAction()
    {
        $officeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_OFFICES', 'OFFICE_ID', ['OFFICE_EDESC'], "STATUS='E'", "OFFICE_EDESC", "ASC", null, true);
        $processList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'dc_processes', 'PROCESS_ID', ['PROCESS_EDESC'], "is_registration='Y'", "process_edesc", "ASC", null, true);
        $departmentList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_DEPARTMENTS', 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], "STATUS='E'", "DEPARTMENT_NAME", "ASC", null, true);
        $receiverList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FULL_NAME", "ASC", null, true);
        $fiscalYear = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_FISCAL_YEARS', 'FISCAL_YEAR_ID', ['FISCAL_YEAR_NAME'], "STATUS='E'", "FISCAL_YEAR_NAME", "ASC", null, true);
        $employee = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FIRST_NAME", "ASC", null, true);
        
        
        $select = new Element\Select('responseFlag');
        $select->setLabel('Response Flag');
        $select->setValueOptions(array(
            'N' => 'N', 'Y' => 'Y',
        ));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $addDocument = new IncommingDocument();
                $addDocument->exchangeArrayFromForm($this->form->getData());
                $addDocument->fromOtherOffice =$request->getPost()->fromOtherOffice;
                $addDocument->registrationDraftID = ((int) Helper::getMaxId($this->adapter, "DC_REGISTRATION_DRAFT", "REG_DRAFT_ID")) + 1;
                $addDocument->status = 'E';
                $addDocument->processId = $this->repository->getAddProcessId();
                $addDocument->createdDt = Helper::getcurrentExpressionDate();
                $addDocument->createdBy = $this->employeeId;
                $registrationDateRaw = $addDocument->registrationDate;
                if($addDocument->registrationDate){
                    $addDocument->registrationDate = Helper::getExpressionDate($addDocument->registrationDate);
                }else{
                    $addDocument->registrationDate = Helper::getcurrentExpressionDate();
                }
                $addDocument->receivingLetterReferenceDate = Helper::getExpressionDate($addDocument->receivingLetterReferenceDate);
                $addDocument->documentDate = Helper::getExpressionDate($addDocument->documentDate);
                $addDocument->completionDate = Helper::getExpressionDate($addDocument->completionDate);

                

                if ($addDocument->sbFiscalYear == '----'){
                    $addDocument->sbFiscalYear = NULL;
                }

                if ($addDocument->employeeId == '----'){
                    $addDocument->employeeId = NULL;
                }

                if ($addDocument->ksFiscalYear == '----'){
                    $addDocument->ksFiscalYear = NULL;
                }

                if ($addDocument->empId == '----'){
                    $addDocument->empId = NULL;
                }
                
                
                $locationId = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['LOCATION_ID'], ["STATUS"=>'E', "EMPLOYEE_ID"=>$this->employeeId], "LOCATION_ID", "ASC", null, true);
                $departmentId = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['DEPARTMENT_ID'], ["STATUS"=>'E', "EMPLOYEE_ID"=>$this->employeeId], "DEPARTMENT_ID", "ASC", null, true);
                $addDocument->registrationTempCode = $this->repository->getCode($registrationDateRaw,$locationId[$this->employeeId]);
                $addDocument->receivingDepartment = $departmentId[$this->employeeId];
                $addDocument->locationId = $locationId[$this->employeeId];
                //print_r($addDocument);die;
                $this->repository->add($addDocument);
                $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("incoming-document");
                
                
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'customRenderer' => Helper::renderCustomView(),
            'officeList' => $officeList,
            'processList' => $processList,
            'departmentList' => $departmentList,
            'receiverList' => $receiverList,
            'fiscalYear' => $fiscalYear,
            'employee' => $employee
             
            // 'regTempCode' => $regTempCode, 
        ]);
    }

    // public function phpAlert(){
    //     return echo("hello");
    //     ///<script language="javascript">alert("hello")</script>';
    // }

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
                    $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/dc_documents/" . $newFileName);
                    // if ($success) {
                    //     $this->flashmessenger()->addMessage("Hello tori");
                        
                    // }
                    $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
                } else { 
                    throw new Exception("Upload unsuccessful.");
                    //$this->flashmessenger()->addMessage("Employee Successfully Deleted!!!");
                    // echo '<script>alert("Welcome to Geeks for Geeks")</script>';
                    // echo ("<script type='text/javascript'>alert('We welcome the New World');</script>");
                    // echo ('<script language="javascript">alert("hello")</script>');
                    ///============================== 
                }
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine(),
            ];
        }
        return new JsonModel($responseData);
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');

        $officeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_OFFICES', 'OFFICE_ID', ['OFFICE_EDESC'], "STATUS='E'", "OFFICE_EDESC", "ASC", null, true);
        $processList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'dc_processes', 'PROCESS_ID', ['PROCESS_EDESC'], "is_registration='Y'", "process_edesc", "ASC", null, true);
        $departmentList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_DEPARTMENTS', 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], "STATUS='E'", "DEPARTMENT_NAME", "ASC", null, true);
        $receiverList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FULL_NAME", "ASC", null, true);
        $fiscalYear = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_FISCAL_YEARS', 'FISCAL_YEAR_ID', ['FISCAL_YEAR_NAME'], "STATUS='E'", "FISCAL_YEAR_NAME", "ASC", null, true);
        $employee = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FIRST_NAME", "ASC", null, true);
        
        $request = $this->getRequest();
        $addDocument = new IncommingDocument();
        if (!$request->isPost()) {
            $addDocument->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $fromOfficeId = $addDocument->fromOfficeId;
            // print_r($addDocument);die;
            
            if ($addDocument->fromOfficeId != null){
                $addDocument->fromOfficeId=$this->repository->getFromOfficeName($id);
            }
            if ($addDocument->receiverName != null){
                $addDocument->receiverName=$this->repository->getReceiverName($id);
            }   
            if ($addDocument->receivingDepartment != null){
                $addDocument->receivingDepartment=$this->repository->getReceivingDepartment($id);
            }       
            if($addDocument->sbFiscalYear == NULL){
                $addDocument->choiceFlag = 'N';
            } else {
                $addDocument->choiceFlag = 'Y';
            }
            if($addDocument->ksFiscalYear == NULL){
                $addDocument->choiceFlagKS = 'N';
            } else {
                $addDocument->choiceFlagKS = 'Y';
            }
            $this->form->bind($addDocument);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $addDocument->exchangeArrayFromForm($this->form->getData());
                $addDocument->modifiedDt = Helper::getcurrentExpressionDate();
                $addDocument->modifiedBy = $this->employeeId;
                $addDocument->processId = $this->repository->getProcessId($id);
                $addDocument->receivingDepartment = $this->repository->getReceivingDepartmentID($id);
                $addDocument->receivingLetterReferenceDate = Helper::getExpressionDate($addDocument->receivingLetterReferenceDate);
                $addDocument->documentDate = Helper::getExpressionDate($addDocument->documentDate);
                $addDocument->registrationDate = Helper::getExpressionDate($addDocument->registrationDate);
                $addDocument->completionDate = Helper::getExpressionDate($addDocument->completionDate);



                if($addDocument->responseFlag=='N'){
                    $addDocument->completionDate=null;
                }



                if($addDocument->choiceFlag=='N'){
                    $addDocument->sbFiscalYear=null;
                    $addDocument->employeeId=null;
                }
                if($addDocument->choiceFlagKS=='N'){
                    $addDocument->ksFiscalYear=null;
                    $addDocument->empId=null;
                }
                

                $addDocument->choiceFlag = $addDocument->sbFiscalYear;
                $addDocument->choiceFlagKS = $addDocument->ksFiscalYear;
                
                if ($addDocument->sbFiscalYear == '----'){
                    $addDocument->sbFiscalYear = NULL;
                    $addDocument->choiceFlag = 'N';
                }

                if ($addDocument->employeeId == '----'){
                    $addDocument->employeeId = NULL;
                }
                
                
                
                if ($addDocument->ksFiscalYear == '----'){
                    $addDocument->ksFiscalYear = NULL;
                    $addDocument->choiceFlagKS = 'N';
                }

                if ($addDocument->empId == '----'){
                    $addDocument->empId = NULL;
                }


                $this->repository->edit($addDocument, $id);
                $this->flashmessenger()->addMessage("Incomming document Successfully Updated!!!");
                return $this->redirect()->toRoute("incoming-document");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'departmentId' => $addDocument->departmentId,
            'receiverId' => $addDocument->receiverId,
            'fromOfficeId' => $fromOfficeId,
            'processId' => $addDocument->processId,
            'id' => $id,
            'form' => $this->form,
            'customRenderer' => Helper::renderCustomView(),
            'officeList' => $officeList,
            'processList' => $processList,
            'departmentList' => $departmentList,
            'receiverList' => $receiverList,
            'fiscalYear' => $fiscalYear,
            'selectedfiscalYear' => $addDocument->sbFiscalYear,
            'selectedfiscalYearKS' => $addDocument->ksFiscalYear,
            'employee' => $employee,
            'selectedEmployee' => $addDocument->employeeId,
            'selectedEmployee2' => $addDocument->empId

        ]);
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');

        if (!$id) {
            return $this->redirect()->toRoute('incoming-document');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Incomming Document Successfully Deleted!!!");
        return $this->redirect()->toRoute('incoming-document');
    }
    public function deleteFileFromTableAction()
    {
        $id = $this->params()->fromRoute('id');
        $this->repository->updateFile($id);

        return;
    }
    public function pushDCFileLinkAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $userID = $this->employeeId;
            $returnData = $this->repository->pushFileLink($data, $userID);
            return new JsonModel(['success' => true, 'data' => $returnData[0], 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function viewAction(){
        $id = $this->params()->fromRoute('id');
        $officeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_OFFICES', 'OFFICE_ID', ['OFFICE_EDESC'], "STATUS='E'", "OFFICE_EDESC", "ASC", null, true);
        $processList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'dc_processes', 'PROCESS_ID', ['PROCESS_EDESC'], "is_registration='Y'", "process_edesc", "ASC", null, true);
        $fiscalYear = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_FISCAL_YEARS', 'FISCAL_YEAR_ID', ['FISCAL_YEAR_NAME'], "STATUS='E'", "FISCAL_YEAR_NAME", "ASC", null, true);
        $employee = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_EMPLOYEES', 'EMPLOYEE_ID', ['FULL_NAME'], "STATUS='E'", "FIRST_NAME", "ASC", null, true);
        

        $request = $this->getRequest();
        $addDocument = new IncommingDocument();
        if (!$request->isPost()) {
            $fromOfficeId = $addDocument->fromOfficeId;
            $addDocument->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            if ($addDocument->fromOfficeId != null){
                $addDocument->fromOfficeId=$this->repository->getFromOfficeName($id);
            }
            if ($addDocument->receiverName != null){
                $addDocument->receiverName=$this->repository->getReceiverName($id);
            }  
            if ($addDocument->receivingDepartment != null){
                $addDocument->receivingDepartment=$this->repository->getReceivingDepartment($id);
            } 
            if($addDocument->sbFiscalYear == NULL){
                $addDocument->choiceFlag = 'N';
            } else {
                $addDocument->choiceFlag = 'Y';
            }
            if($addDocument->ksFiscalYear == NULL){
                $addDocument->choiceFlagKS = 'N';
            } else {
                $addDocument->choiceFlagKS = 'Y';
            }
            $this->form->bind($addDocument);

        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $addDocument->exchangeArrayFromForm($this->form->getData());
                $addDocument->modifiedDt = Helper::getcurrentExpressionDate();
                $addDocument->modifiedBy = $this->employeeId;
                $this->repository->edit($addDocument, $id);
                $this->flashmessenger()->addMessage("Incomming document Successfully Updated!!!");
                return $this->redirect()->toRoute("incoming-document");
            }
        }
        
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'processList' => $processList,
            'processId' => $addDocument->processId,
            'fromOfficeId' => $fromOfficeId,
            'officeList' => $officeList,
            'fiscalYear' => $fiscalYear,
            'selectedfiscalYear' => $addDocument->sbFiscalYear,
            'selectedfiscalYearKS' => $addDocument->ksFiscalYear,
            'employee' => $employee,
            'selectedEmployee' => $addDocument->employeeId,
            'selectedEmployee2' => $addDocument->empId
            
        ]);
    }


    public function pullFilebyIdAction()
    {

        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $returnData = $this->repository->pullFilebyId($data->id)[0];

            return new JsonModel(['success' => true, 'data' => $returnData, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }


    public function sampatiBibaranAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $search = $request->getPost();
            $data = Helper::extractDbData($this->repository->getSampatiBibaranData($search));
            return new JsonModel([
                "success" => "true",
                "data" => $data
            ]);
        }
        return Helper::addFlashMessagesToArray($this, [    
            'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function sampatiBibaranSelfAction(){
        $employeeId = $this->employeeId;
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $data = Helper::extractDbData($this->repository->getSampatiBibaranDataSelf($employeeId));
            return new JsonModel([
                "success" => "true",
                "data" => $data
            ]);
        }
        return Helper::addFlashMessagesToArray($this, [
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            
        ]);
    }

    public function karyaSampadanAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $search = $request->getPost();
            $data = Helper::extractDbData($this->repository->getKaryaSampadanData($search));
            return new JsonModel([
                "success" => "true",
                "data" => $data
            ]);
        }
        return Helper::addFlashMessagesToArray($this, [    
            'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function karyaSampadanSelfAction(){
        $employeeId = $this->employeeId;
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $data = Helper::extractDbData($this->repository->getKaryaSampadanDataSelf($employeeId));
            return new JsonModel([
                "success" => "true",
                "data" => $data
            ]);
        }
        return Helper::addFlashMessagesToArray($this, [
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            
        ]);

        
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
