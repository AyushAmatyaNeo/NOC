<?php

namespace Grade\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Grade\Repository\GradeRepo;
//use Grade\Form\AddIncommingForm;
use Grade\Model\GradeModel;
//use Grade\Model\UserAssignModel;
//use Grade\Model\FinalRegistrationTable;
use Application\Repository\MonthRepository;
use Zend\Form\Element;


class GradeController extends HrisController
{

    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(GradeRepo::class);
        //$this->initializeForm(AddIncommingForm::class);
    }
    public function indexAction()
    {
        $employee_joining_dates = [];
        $emp_arr = [];
        $promotion_arr = [];        

        $curr_date = date('Y-m-d');
        $position_type = 'Non-Technical';

        $grade_rokka_arr = [];
        $grade_reward_arr = [];

        $employee_joining_dates =  $this->repository->getEmployeeJoiningDates();

        if(!empty($employee_joining_dates)) {
            foreach($employee_joining_dates as $employee_jd) {
                $grade_ceiling = [];     
                
                //Get the days diff
                $date_diff = strtotime($curr_date) - strtotime($employee_jd['JOIN_DATE']);
                $days_diff = round( $date_diff/ (60 * 60 * 24) );
                $accumulated_grade = floor($days_diff / 365);

                if(!empty($employee_jd['EMPLOYEE_ID'])) {
                    $grade_rokka = TRUE;
                    $grade_reward = TRUE;
                    $is_promoted = TRUE;

                    //Grade Rokka
                    $grade_rokka_arr = $this->repository->isGrageRokka($employee_jd['EMPLOYEE_ID']);
                    
                    if(empty($grade_rokka_arr)) {
                        $grade_rokka = FALSE;
                    }

                    //Grade Reward
                    $grade_reward_arr = $this->repository->isGradeReward($employee_jd['EMPLOYEE_ID']);

                    if(empty($grade_reward_arr)) {
                        $grade_reward = FALSE;
                    }

                    //Promotion
                    $promotion_arr = $this->repository->isEmployeePromoted($employee_jd['EMPLOYEE_ID']);

                    if(empty($promotion_arr)) {
                        $is_promoted = FALSE;
                    }

                    //get latest start date of grade rokka and grade reward

                }
                
                //die;

                //Next grade upgrade
                if(strtotime($curr_date) != strtotime($employee_jd['JOIN_DATE'])) {
                    $initial_date = strtotime($employee_jd['JOIN_DATE']);

                    $inc_yr = 1;
                    while(strtotime($curr_date) > 0) {
                        $yr = date('Y', $initial_date);
                        if(($yr % 4 == 0)  && (strtotime($curr_date) > $initial_date) && $inc_yr >= 2) {
                            $initial_date = $initial_date + ( 60 * 60 * 24 );
                        }
                        $initial_date = $initial_date + ( 365 * 60 * 60 * 24 );
                        if(strtotime($curr_date) < $initial_date) {
                            break;
                        }
                        $inc_yr++;
                    }
                }
                //next grade change update ends

                //Check Grade Ceiling
                if(!empty($employee_jd['POSITION_ID']) && !empty($employee_jd['FUNCTIONAL_LEVEL_EDESC'])) {
                    $emp_arr[$employee_jd['EMPLOYEE_ID']]['EMPLOYEE_ID'] = $employee_jd['EMPLOYEE_ID'];
                    $emp_arr[$employee_jd['EMPLOYEE_ID']]['JOIN_DATE'] = $employee_jd['JOIN_DATE'];
                    $emp_arr[$employee_jd['EMPLOYEE_ID']]['JOIN_DATE_BS'] = $employee_jd['JOIN_DATE_BS'];
                    $emp_arr[$employee_jd['EMPLOYEE_ID']]['POSITION_ID'] = $employee_jd['POSITION_ID'];
                    $emp_arr[$employee_jd['EMPLOYEE_ID']]['FUNCTIONAL_LEVEL_EDESC'] = $employee_jd['FUNCTIONAL_LEVEL_EDESC'];
                    $grade_ceiling = $this->repository->getGradeCeiling($position_type, $employee_jd['POSITION_ID'], $employee_jd['FUNCTIONAL_LEVEL_EDESC']); 
                    if(!empty($grade_ceiling)) {
                        $emp_arr[$employee_jd['EMPLOYEE_ID']]['GRADE_CEILING_NO'] = $grade_ceiling[0]['GRADE_CEILING_NO'];

                        if($accumulated_grade > $grade_ceiling[0]['GRADE_CEILING_NO']) {
                            $accumulated_grade = $grade_ceiling[0]['GRADE_CEILING_NO'];
                        }

                        $emp_arr[$employee_jd['EMPLOYEE_ID']]['YEARLY_ACCUMULATED_GRADE'] = $accumulated_grade;
                        $emp_arr[$employee_jd['EMPLOYEE_ID']]['NEXT_GRADE_UPGRADE'] = date('Y-m-d', $initial_date);
                    }
                }

            }
        }

        //UPDATE THE DATA
        if(!empty($emp_arr)) {
            $monthRepo = new MonthRepository($this->adapter);
            $fiscalYear = $monthRepo->getCurrentFiscalYear();
            $currFiscalYear = $fiscalYear['FISCAL_YEAR_ID'];
            
            $strCurrDate = strtotime($curr_date);

            foreach($emp_arr as $emp_arr_single) {
                if(array_key_exists('NEXT_GRADE_UPGRADE', $emp_arr_single) && !empty($emp_arr_single['NEXT_GRADE_UPGRADE'])) {
                     $is_today_date = strtotime($emp_arr_single['NEXT_GRADE_UPGRADE']);

                     if($strCurrDate == $is_today_date) {
                        $this->repository->updateGradeValue($emp_arr_single['EMPLOYEE_ID'], $emp_arr_single['YEARLY_ACCUMULATED_GRADE'], $emp_arr_single['NEXT_GRADE_UPGRADE'], $currFiscalYear);
                     }
                }
            }
        }


        echo '<pre>';print_r($emp_arr);die;

        /*$departmentList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_DEPARTMENTS', 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], "STATUS='E'", "DEPARTMENT_NAME", "ASC", null, true);
        $endProcess = $this->repository->getEndProcess();

        $organizationList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_OFFICES', 'OFFICE_ID', ['OFFICE_EDESC'], "STATUS='E'", "OFFICE_EDESC", "ASC", null, true);


        return Helper::addFlashMessagesToArray($this, [
            'acl' => $this->acl,
            'dept' => $departmentList,
            'organizationList' => $organizationList,
            'endProcess' => $endProcess,
            ]);*/
    }

  
    /*public function addAction()
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

                $addDocument->registrationDraftID = ((int) Helper::getMaxId($this->adapter, "DC_REGISTRATION_DRAFT", "REG_DRAFT_ID")) + 1;
                $addDocument->status = 'E';
                $addDocument->processId = $this->repository->getAddProcessId();
                $addDocument->createdDt = Helper::getcurrentExpressionDate();
                $addDocument->createdBy = $this->employeeId;
                $registrationDateRaw = $addDocument->registrationDate;
                $addDocument->registrationDate = Helper::getExpressionDate($addDocument->registrationDate);
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
                
                
                
                $addDocument->registrationTempCode = $this->repository->getCode($registrationDateRaw);
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
    }*/


    /*public function editAction()
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
    }*/

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
}
