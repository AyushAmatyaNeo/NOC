<?php

namespace DartaChalani\Controller;

error_reporting(0);

use Application\Custom\CustomViewModel;
use Application\Controller\HrisController;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use DartaChalani\Form\OfficesForm;
use Setup\Repository\DepartmentRepository;
use Setup\Model\Department;
use DartaChalani\Repository\DepartmentUsersRepository;
use DartaChalani\Model\DepartmentUsers;
use DartaChalani\Repository\ChalaniRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element;
use Exception;
use Zend\View\Model\JsonModel;

class DepartmentUsersController extends HrisController
{
    protected $departmentRepo;
    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(DepartmentUsersRepository::class);
        $this->departmentRepo = new DepartmentRepository($adapter);
        $this->chalaniRepo = new ChalaniRepository($adapter);
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->departmentRepo->fetchAll();
                $departmentList = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $departmentList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, []);
    }

    public function assignAction(){
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id');
        $empList = $this->chalaniRepo->getEmployeeList($id);
        $employeeList = [];
        foreach($empList as $e){
            array_push($employeeList, $e['EMPLOYEE_ID']);
        }
        if ($request->isPost()) {

            
            $postedData = $request->getPost();
                $deptUsrs = new DepartmentUsers();

                $employees = $postedData['employeeId'];
                $assignedDepartment = $this->repository->assignedDepartmentList();
                
                if($postedData['locationId']){
                    $this->repository->deleteByLocationId($postedData['locationId']);
                }else{
                    $this->repository->deleteByDepartmentId($id);
                }
                
                
                if($employees != null){
                    for ($i=0;$i<count($employees);$i++){
                        $deptUsrs->duId = ((int) Helper::getMaxId($this->adapter, "DC_DEPARTMENTS_USERS", "DU_ID")) + 1;
                        $deptUsrs->employeeId = $employees[$i];
                        $deptUsrs->status = 'E';
                        if($postedData['locationId']){
                            $deptUsrs->locationId = $postedData['locationId'];
                        }else{
                            $deptUsrs->departmentId = $postedData['departmentId'];
                        }
                        $deptUsrs->createdDt = Helper::getcurrentExpressionDate();
                        $deptUsrs->createdBy = $this->employeeId;
                        $deptUsrs->modifiedDt = Helper::getcurrentExpressionDate();
                        $deptUsrs->modifiedBy = $this->employeeId;
                        
                        $this->repository->add($deptUsrs);
                               
                    }
                }
                
                
                $this->flashmessenger()->addMessage("Users Successfully Assigned!!!");
                return $this->redirect()->toRoute("departmentusers");
        }
        $employee = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FULL_NAME"], ["DEPARTMENT_ID" => $id, "STATUS" => 'E'], "FIRST_NAME", "ASC", " ", FALSE, TRUE);
        $department = $this->departmentRepo->fetchById($id);
        $locations = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LOCATIONS", "LOCATION_ID", ["LOCATION_EDESC"], ["STATUS" => 'E'], "LOCATION_EDESC", "ASC", " ", FALSE, TRUE);
        return Helper::addFlashMessagesToArray($this, [
            'id' => $id,
            'employeeList' => $employeeList,
            'department' => $department,
            'customRenderer' => Helper::renderCustomView(),
            'employee' => $employee,
            'locations' => $locations,
        ]);
    }

    // public function addAction()
    // {
    //     $request = $this->getRequest();
        
    //     if ($request->isPost()) {
    //         $postedData = $request->getPost();
    //         $this->form->setData($postedData);
    //         if ($this->form->isValid()) {
    //             $offices = new Offices();
    //             $offices->exchangeArrayFromForm($this->form->getData());  //gets info from the form
    //             $offices->officeId = ((int) Helper::getMaxId($this->adapter, "DC_OFFICES", "OFFICE_ID")) + 1;
    //             $offices->createdDt = Helper::getcurrentExpressionDate();
    //             $offices->createdBy = $this->employeeId;
    //             $offices->responseFlag = $postedData['responseFlag'];
    //             $offices->modifiedDt = Helper::getcurrentExpressionDate();
    //             $offices->modifiedBy = $this->employeeId;
    //             $offices->status = 'E';
    //             $this->repository->add($offices);

    //             $this->flashmessenger()->addMessage("Office Successfully Added!!!");
    //             return $this->redirect()->toRoute("offices");
    //         }
    //     }


    //     return Helper::addFlashMessagesToArray($this, [
    //         'form' => $this->form,
    //         'customRenderer' => Helper::renderCustomView(),
    //     ]);
    // }

    // public function editAction() {
    //     $id = (int) $this->params()->fromRoute("id");
    //     $request = $this->getRequest();

    //     $offices = new Offices();
    //     if (!$request->isPost()) {
    //         $offices->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
    //         $this->form->bind($offices);
    //     } else {
    //         $this->form->setData($request->getPost());
    //         if ($this->form->isValid()) {
    //             $offices->exchangeArrayFromForm($this->form->getData());
    //             $offices->modifiedDt = Helper::getcurrentExpressionDate();
    //             $offices->modifiedBy = $this->employeeId;
    //             $offices->status = 'E';
    //             $this->repository->edit($offices, $id);
    //             $this->flashmessenger()->addMessage("Office Successfully Updated!!!");
    //             return $this->redirect()->toRoute("offices");
    //         }
    //     }
        
    //     return Helper::addFlashMessagesToArray($this, [
    //                 'form' => $this->form,
    //                 'id' => $id
    //     ]);
    // }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('offices');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Office Successfully Deleted!!!");
        return $this->redirect()->toRoute('offices');
    }

    public function getSearchAction() {
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

    // public function fileUploadAction() {
    //     $request = $this->getRequest();
    //     $responseData = [];
    //     $files = $request->getFiles()->toArray();
    //     try {
    //         if (sizeof($files) > 0) {
    //             $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
    //             $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
    //             $unique = Helper::generateUniqueName();
    //             $newFileName = $unique . "." . $ext;
    //             $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/dartachalani_docs/" . $newFileName);
    //             if (!$success) {
    //                 throw new Exception("Upload unsuccessful.");
    //             }
    //             $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
    //         }
    //     } catch (Exception $e) {
    //         $responseData = [
    //             "success" => false,
    //             "message" => $e->getMessage(),
    //             "traceAsString" => $e->getTraceAsString(),
    //             "line" => $e->getLine()
    //         ];
    //     }
    //     return new JsonModel($responseData);
    // }

    // public function pushFileLinkAction() {
    //     try {
    //         $request = $this->getRequest();
    //         $data = $request->getPost();
    //         return new JsonModel(['success' => true, 'data' => $data, 'message' => null]);
    //     } catch (Exception $e) {
    //         return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
    //     }
    // }

    public function getEmpListAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if($data['location_id']){
                $empList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FULL_NAME"], ["LOCATION_ID" => $data['location_id'], "STATUS" => 'E'], "FIRST_NAME", "ASC", " ", FALSE, TRUE);
                $assignedEmployees = $this->chalaniRepo->getLocationWiseEmployeeList($data['location_id']);
            }else{
                $empList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FULL_NAME"], ["DEPARTMENT_ID" => $data['deartment_id'], "STATUS" => 'E'], "FIRST_NAME", "ASC", " ", FALSE, TRUE);
                $assignedEmployees = $this->chalaniRepo->getEmployeeList($data['deartment_id']);
            }
            $assignedEmployeesList = [];
            foreach($assignedEmployees as $e){
                array_push($assignedEmployeesList, $e['EMPLOYEE_ID']);
            }
            $employee = [];
            $counter = 0;
            foreach($empList as $e => $a){
                $employee[$counter]['EMPLOYEE_ID'] = $e;
                $employee[$counter]['EMPLOYEE_NAME'] = $a;
                $counter++;
            }
            return new JsonModel(['success' => true, 'data' => $employee, 'assignedEmployeesList' =>$assignedEmployeesList, 'message' => null]);
        }
    }

}