<?php
namespace Recruitment\Controller;


use Application\Controller\HrisController;
use Recruitment\Form\StageForm;
use Recruitment\Model\StageModel;
use Recruitment\Model\EmployeeStagePermission;
use Recruitment\Repository\StageRepository;
use Application\Helper\EntityHelper;
use zend\Db\Adapter\AdapterInterface;
use zend\Authentication\Storage\StorageInterface;
use Application\Helper\Helper;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Exception;

class StageController extends HrisController {
    function __construct(AdapterInterface $adapter,StorageInterface $storge)
    {
        parent::__construct($adapter, $storge);
        $this->initializeRepository(StageRepository::class);
        $this->initializeForm(StageForm::class);
    }

    public function indexAction()
    {

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $rawList = $this->repository->getFilteredRecords($data);
                $list = iterator_to_array($rawList, false);                        
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getRecStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        
        // $GenderSE = $this->getRecGenderSelectElement(['name' => 'Gender', 'id' => 'Gender', 'class' => 'form-control reset-field', 'label' => 'Gender']);
        
        return $this->stickFlashMessagesTo([
            'status' => $statusSE,
            
        ]);
    }
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            // echo '<pre>'; print_r($this->form->setData($request->getPost())); die(); 
            if ($this->form->isValid()) 
            {
                $Stage_data = new StageModel();
                
                
                $Stage_data->exchangeArrayFromForm($this->form->getData());
                $Stage_data->RecStageId = ((int) Helper::getMaxId($this->adapter, StageModel::TABLE_NAME, StageModel::REC_STAGE_ID)) + 1;
                $Stage_data->CreatedBy = $this->employeeId;
                $Stage_data->CreatedDt = Helper::getcurrentExpressionDate();
                $Stage_data->IsFinal  = 'N';
                $Stage_data->Status = 'E'; 

                // echo '<pre>'; print_r($Stage_data); die();               
                $this->repository->add($Stage_data);
                $this->flashmessenger()->addMessage("Stage Data Successfully added!!");
                return $this->redirect()->toRoute("stage");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                    $this, [
                        'customRenderer' => Helper::renderCustomView(),
                        'form' => $this->form,
                    ]
                )
        );
        
    }
    public function editAction()
    {
        $id = (int) $this->params()->fromroute('id');
        if( $id === 0)
        {
            return $this->redirect()->toRoute('stage');
        }
            $request = $this->getRequest();
            if($request->isPost())
            {
                $stage_data = new StageModel();
                $postedData = $request->getpost();
                $this->form->setdata($postedData);
                if($this->form->isValid())
                {
                    $stage_data->exchangeArrayFromForm($this->form->getData());
                    $stage_data->ModifiedDt = Helper::getcurrentExpressionDate();
                    $stage_data->ModifiedBy = $this->employeeId; 
                    $this->repository->edit($stage_data, $id);
                    $this->flashmessenger()->addMessage("Stage Edited Successfully!");
                    return $this->redirect()->toRoute('stage');

                }
            }
        $details = $this->repository->fetchById($id);
        // echo '<pre>'; print_r($stage_data); die;
        $model = new StageModel();
        $model->exchangeArrayFromDB($details);
        $this->form->bind($model);

        return Helper::addFlashMessagesToArray($this, [
                'form' => $this->form,
                'details' => $details,
        ]);

    }
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute("stage");
        }
        $details = $this->repository->fetchById($id);
        $model = new StageModel();
        $model->exchangeArrayFromDB($details);
        $this->form->bind($model);

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'details' => $details,
        ]);
    } 
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute('stage');
        }
        $details = $this->repository->fetchByid($id);
        $model = new StageModel();
        $model->exchangeArrayFromDB($details);
        $model->DeletedBy = $this->employeeId;
        $model->DeletedDt = Helper::getcurrentExpressionDate();
        // echo '<pre>'; print_r($model); die;
        $this->repository->delete($model, $id);
        $this->flashmessenger()->addMessage('Stage Deleted!');
        return $this->redirect()->toRoute('stage');
    }

    public function assignEmployeeStagesAction(){
        $employees = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => 'E'], "FIRST_NAME", "ASC", " ", FALSE, TRUE);
        $stages = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_REC_STAGES", "REC_STAGE_ID", ["STAGE_EDESC"], ["STATUS" => 'E',"VACANCY_APPLICATION" => 'A'], "ORDER_NO", "ASC", " ", FALSE, TRUE);
        $vacancyList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_REC_VACANCY", "VACANCY_ID", ["AD_NO"], ["STATUS" => 'E'], "case 
        when right(left(AD_NO,2),1)='/'
        then left(AD_NO,1)
        when right(left(AD_NO,3),1)='/'
        then left(AD_NO,2)
        when right(left(AD_NO,4),1)='/'
        then left(AD_NO,3)
        when right(left(AD_NO,5),1)='/'
        then left(AD_NO,4)
        else 9999
    end", "ASC", " ", FALSE, TRUE);
        $request = $this->getRequest();
        if($request->isPost())
        {
            $postedData = $request->getpost();
            $model = new EmployeeStagePermission();
            $model->id = ((int) Helper::getMaxId($this->adapter, EmployeeStagePermission::TABLE_NAME, EmployeeStagePermission::ID)) + 1;
            $model->employeeId = $postedData['employeeId'];
            $model->stageIds = implode(',',$postedData['stageId']);
            $model->vacancyIds = implode(',',$postedData['vacancyId']);
            $model->createdDt = Helper::getcurrentExpressionDate();
            $model->status = 'E';
            $model->createdBy = $this->employeeId;
            $model->accessAs = $postedData['accessAs'];
            // echo('<pre>');print_r($model);die;
            // print_r($postedData);die;
            $this->repository->addEmployeeStagePermission($model);
            $this->flashmessenger()->addMessage("Employee Stage Permission added sucessfully!!!");
            return $this->redirect()->toRoute("stage",['action' => 'assignEmployeeStages']);
        }
        return $this->stickFlashMessagesTo([
            'employees' => $employees,
            'stages' => $stages,
            'vacancyList' => $vacancyList,
        ]);
    }

    public function getEmpStageListAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if($data['employeeId']){
                $stageList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_REC_EMPLOYEE_STAGE_PERMISSION", "EMPLOYEE_ID", ["STAGE_IDS"], ["EMPLOYEE_ID" => $data['employeeId']], "ID", "ASC", " ", FALSE, TRUE);
                $vacancyList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_REC_EMPLOYEE_STAGE_PERMISSION", "EMPLOYEE_ID", ["VACANCY_IDS"], ["EMPLOYEE_ID" => $data['employeeId']], "ID", "ASC", " ", FALSE, TRUE);
                $accessAsList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_REC_EMPLOYEE_STAGE_PERMISSION", "EMPLOYEE_ID", ["ACCESS_AS"], ["EMPLOYEE_ID" => $data['employeeId']], "ID", "ASC", " ", FALSE, TRUE);
            }
            $stages = [];
            $vacancies = [];
            $accessAs = '';
            if($accessAsList[$data['employeeId']]){
                $accessAs = $accessAsList[$data['employeeId']];
            }
            if($stageList[$data['employeeId']]){
                $stages = explode(',',$stageList[$data['employeeId']]);
            }
            if($vacancyList[$data['employeeId']]){
                $vacancies = explode(',',$vacancyList[$data['employeeId']]);
            }
            return new JsonModel(['success' => true, 'data' => '', 'stageIds' =>$stages, 'vacancyIds'=>$vacancies, 'accessAs' => $accessAs, 'message' => null]);
        }
    }
}