<?php

namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Custom\CustomViewModel;
use Recruitment\Form\RecruitmentVacancyForm;
use Recruitment\Repository\VacancyRepository;
use Recruitment\Model\OpeningVacancy;
use Recruitment\Model\RecruitmentVacancy as RecruitmentVacancyModel;
use Recruitment\Model\OptionsModel;
use Recruitment\Repository\VacancyStageRepository;
use Recruitment\Repository\VacancyInclusionRepository;
use Recruitment\Model\VacancyInclusionModel;
use Recruitment\Model\VacancyStageModel;
use Setup\Model\AcademicDegree;
use Setup\Model\Gender;
use Setup\Model\Designation;
use Setup\Model\Department;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Exception;
use Recruitment\Model\SkillModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class VacancyController extends HrisController
{

    protected $stageRepository;

    function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(VacancyRepository::class);
        $this->stageRepository = new VacancyStageRepository($this->adapter);
        $this->VacancyInclusionRepository = new VacancyInclusionRepository($this->adapter);
        $this->initializeForm(RecruitmentVacancyForm::class);
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
        // $statusSE = $this->getRecStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        // $GenderSE = $this->getRecGenderSelectElement(['name' => 'Gender', 'id' => 'Gender', 'class' => 'form-control reset-field', 'label' => 'Gender']);
        return $this->stickFlashMessagesTo([
            // 'status' => $statusSE,
            // 'Gender' => $GenderSE,
            'qualificationId' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_DEGREES',['ACADEMIC_DEGREE_ID','ACADEMIC_DEGREE_NAME'], ["STATUS" => "E"]),
            'adnumber'        => EntityHelper::getTableList($this->adapter, 'HRIS_REC_VACANCY',['VACANCY_ID','AD_NO'], ["STATUS" => "E"]),            
            'DepartmentId'    => EntityHelper::getTableList($this->adapter, 'HRIS_DEPARTMENTS',['DEPARTMENT_ID','DEPARTMENT_NAME'], ["STATUS" => "E"]),
            'positionId'      => EntityHelper::getTableList($this->adapter, 'HRIS_DESIGNATIONS',['DESIGNATION_ID','DESIGNATION_TITLE'], ["STATUS" => "E"]),
            'openingId'      => EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS',['OPENING_ID','OPENING_NO'], ["STATUS" => "E"]),
        ]);
    }
    public function addAction()
    {
        $request = $this->getRequest();
        $postData = $request->getPost();
        
        if ($request->isPost()) {
            $this->form->setData($request->getPost());           
            if ($this->form->isValid()) {
                $VacancyData = new RecruitmentVacancyModel();
                $VacancyData->exchangeArrayFromForm($this->form->getData());
               // Insert Vacancy table data
               $vacancyCount = count($postData['AdNo']);
               for($i = 0; $i < $vacancyCount; $i++){

                        $VacancyData->VacancyId         = ((int) Helper::getMaxId($this->adapter, RecruitmentVacancyModel::TABLE_NAME, RecruitmentVacancyModel::VACANCY_ID)) + 1;
                        $VacancyData->Vacancy_no         = (empty($postData['VacancyNo'][$i])) ? $this->repository->PrevVacancyData('VACANCY_NO') : $postData['VacancyNo'][$i];
                        $VacancyData->OpeningId         = $postData['OpeningId'];
                        $VacancyData->Vacancy_type      = $postData['Vacancy_type'];
                        $VacancyData->LevelId           = (empty($postData['LevelId'][$i])) ? $this->repository->PrevVacancyData('LEVEL_ID') : $postData['LevelId'][$i];
                        $VacancyData->ServiceTypesId    = (empty($postData['ServiceTypesId'][$i])) ? $this->repository->PrevVacancyData('SERVICE_TYPES_ID') : $postData['ServiceTypesId'][$i];  
                        $VacancyData->ServiceEventsId   = (empty($postData['ServiceEventsId'][$i])) ? $this->repository->PrevVacancyData('SERVICE_EVENTS_ID') : $postData['ServiceEventsId'][$i]; 
                        $VacancyData->PositionId        = (empty($postData['PositionId'][$i])) ? $this->repository->PrevVacancyData('POSITION_ID') : $postData['PositionId'][$i];
                        $VacancyData->AdNo              = $postData['AdNo'][$i];
                        $VacancyData->VacancyReservationNo   = $postData['VacancyReservationNo'][$i];
                        $VacancyData->SkillId           = implode(',', $postData['SkillId'][$i]);
                        $VacancyData->InclusionId       = implode(',', $postData['InclusionId'][$i]);                    
                        $VacancyData->QualificationId   = (empty($postData['QualificationId'][$i])) ? $this->repository->PrevVacancyData('QUALIFICATION_ID') : $postData['QualificationId'][$i];
                        $VacancyData->Experience        = (empty($postData['Experience'][$i])) ? $this->repository->PrevVacancyData('EXPERIENCE') : $postData['Experience'][$i];
                        $VacancyData->DepartmentId      = (empty($postData['DepartmentId'][$i])) ? $this->repository->PrevVacancyData('DEPARTMENT_ID') : $postData['DepartmentId'][$i];                                
                        $VacancyData->Remark            = $postData['Remark'][$i];
                        $VacancyData->CreatedBy = $this->employeeId;
                        $VacancyData->CreatedDt = Helper::getcurrentExpressionDate();
                        // echo '<pre>'; print_r($VacancyData); die;
                        $this->repository->add($VacancyData);
                        // Add Stage details
                        $Rec_stage = new VacancyStageModel();
                        $Rec_stage->RecVacancyStageId = ((int) Helper::getMaxId($this->adapter, VacancyStageModel::TABLE_NAME, VacancyStageModel::REC_VACANCY_STAGE_ID)) + 1;
                        $Rec_stage->RecStageId = '1';  // Stage id 1 = OPEN
                        $Rec_stage->VacancyId = ((int) Helper::getMaxId($this->adapter, RecruitmentVacancyModel::TABLE_NAME, RecruitmentVacancyModel::VACANCY_ID));
                        $Rec_stage->CreatedBy = $this->employeeId;
                        $Rec_stage->Status = 'E';
                        $Rec_stage->CreatedDt = Helper::getcurrentExpressionDate();
                        $this->stageRepository->add($Rec_stage);
                    }
                    // echo '<pre>'; print_r($VacancyData); die;
                    $this->flashmessenger()->addMessage("Vacancy Successfully added!!!");
                    return $this->redirect()->toRoute("vacancy");
                }
            }
        $ServiceEvents = EntityHelper::getTableList($this->adapter, 'HRIS_REC_SERVICE_EVENTS_TYPES', ['SERVICE_EVENT_ID','SERVICE_EVENT_NAME'], ['STATUS' => 'E']);
        $DepartmentList = EntityHelper::getTableList($this->adapter, Department::TABLE_NAME, ['DEPARTMENT_ID','DEPARTMENT_NAME'], ['STATUS' => 'E']);
        $QualificationList = EntityHelper::getTableList($this->adapter, AcademicDegree::TABLE_NAME, ['ACADEMIC_DEGREE_ID','ACADEMIC_DEGREE_NAME'], ['STATUS' => 'E']);
        $Positions = EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, ['DESIGNATION_ID','DESIGNATION_TITLE'], ['STATUS' => 'E']);
        $ServiceTypes = EntityHelper::getTableList($this->adapter, 'HRIS_SERVICE_TYPES', ['SERVICE_TYPE_ID','SERVICE_TYPE_NAME'], ['STATUS' => 'E']);
        $InclusionList = EntityHelper::getTableList($this->adapter, OptionsModel::TABLE_NAME, ['OPTION_ID','OPTION_EDESC'], ['STATUS' => 'E']);
        $LevelList = EntityHelper::getTableList($this->adapter, 'HRIS_FUNCTIONAL_LEVELS', ['FUNCTIONAL_LEVEL_ID','FUNCTIONAL_LEVEL_EDESC'], ['STATUS' => 'E']);
        $Skills = EntityHelper::getTableList($this->adapter, 'HRIS_REC_SKILL', ['SKILL_ID','SKILL_NAME'], ['STATUS' => 'E']);
        $OpeningVacancyNo = EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS', ['OPENING_ID','VACANCY_TOTAL_NO','RESERVATION_NO'], ['STATUS' => 'E']);
        // $Vacancy_types = array("OPEN" => "OPEN", "INTERNAL" => "INTERNAL");
        $Vacancy_types = array("OPEN" => "OPEN", "INTERNAL_FORM" => "INTERNAL-FORM","INTERNAL_APPRAISAL" => "INTERNAL-APPRAISAL", );
        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'ServiceEvents' => $ServiceEvents,
                    'DepartmentList' => $DepartmentList,
                    'QualificationList' => $QualificationList,
                    'Positions' => $Positions,
                    'ServiceTypes' => $ServiceTypes,
                    'InclusionList' => $InclusionList,
                    'LevelList'   => $LevelList,
                    'OpeningVacancyNo' => $OpeningVacancyNo,
                    'Skills'    => $Skills,
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    'messages' => $this->flashmessenger()->getMessages(),
                    'Vacancy_types' => $Vacancy_types
                ]
            )
        );
    }
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancy");
        }
        $detail = $this->repository->fetchById($id);
        $inclusion = Helper::extractDbData($this->VacancyInclusionRepository->fetchById($id));
        $skills =  explode(',', $detail['SKILL_ID']);
        $Inclusions =  explode(',', $detail['INCLUSION_ID']);
        $model = new RecruitmentVacancyModel();
        $model->exchangeArrayFromDB($detail);
        $model->SkillId = $skills;
        $model->InclusionId = $Inclusions;
        $this->form->bind($model);
        $Vacancy_types = array("OPEN" => "OPEN", "INTERNAL_FORM" => "INTERNAL-FORM","INTERNAL_APPRAISAL" => "INTERNAL-APPRAISAL");
        return Helper::addFlashMessagesToArray($this, [
            'id' => $id,
            'form' => $this->form,
            'detail' => $detail,
            'ServiceEvents' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_REC_SERVICE_EVENTS_TYPES', 'SERVICE_EVENT_ID', ['SERVICE_EVENT_NAME'], ["STATUS" => "E"], 'SERVICE_EVENT_NAME', "ASC", null, [null => '---'], true),
            'DepartmentList' => EntityHelper::getTableKVListWithSortOption($this->adapter,Department::TABLE_NAME, 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], ["STATUS" => "E"], 'DEPARTMENT_NAME', "ASC", null, [null => '---'], true),           
            'Positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"], Designation::DESIGNATION_TITLE, "ASC", null, [null => '---'], true),
            'ServiceTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_SERVICE_TYPES', 'SERVICE_TYPE_ID', ['SERVICE_TYPE_NAME'], ["STATUS" => "E"], 'SERVICE_TYPE_NAME', "ASC", null, [null => '---'], true),
            // 'InclusionList' => $InclusionList,
            'LevelList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_FUNCTIONAL_LEVELS', 'FUNCTIONAL_LEVEL_ID', ['FUNCTIONAL_LEVEL_EDESC'], ["STATUS" => "E"], 'FUNCTIONAL_LEVEL_EDESC', "ASC", null, [null => '---'], true),
            'QualificationList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_ACADEMIC_DEGREES', 'ACADEMIC_DEGREE_ID', ['ACADEMIC_DEGREE_NAME'], ["STATUS" => "E"], 'ACADEMIC_DEGREE_NAME', "ASC", null, [null => '---'], true),
            // 'OpeningVacancyNo' => $OpeningVacancyNo,
            'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
            'Vacancy_types' => $Vacancy_types,
            'InclusionList' => EntityHelper::getTableKVListWithSortOption($this->adapter, OptionsModel::TABLE_NAME, OptionsModel::OPTION_ID, [OptionsModel::OPTION_EDESC], ["STATUS" => "E"], OptionsModel::OPTION_EDESC, "ASC", null, [null => '---'], true),
            'Skills' => EntityHelper::getTableKVListWithSortOption($this->adapter, SkillModel::TABLE_NAME, SkillModel::SKILL_ID, [SkillModel::SKILL_NAME], ["STATUS" => "E"], SkillModel::SKILL_NAME, "ASC", null, [null => '---'], true),
        ]);
    }
    public function editAction()
    {
        $request = $this->getRequest();

        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancy");
        }
        if ($request->isPost()) {
            $vacancydata = new RecruitmentVacancyModel();
            $postData = $request->getPost();
            
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $vacancydata->exchangeArrayFromForm($this->form->getData());
                $vacancydata->SkillId = implode(',',$postData['SkillId']);
                $vacancydata->InclusionId = implode(',',$postData['InclusionId']);
                $vacancydata->ModifiedDt = Helper::getcurrentExpressionDate();
                $vacancydata->ModifiedBy = $this->employeeId;
                // echo '<pre>'; print_r($vacancydata); ; die;
                $this->repository->edit($vacancydata, $id);
               
                //Update Inclusion table
                $this->flashmessenger()->addMessage("Vacancy Successfully Edited!!!");
                return $this->redirect()->toRoute("vacancy");
            }
        }
        $detail = $this->repository->fetchById($id);
        $inclusion = Helper::extractDbData($this->VacancyInclusionRepository->fetchById($id));
        $inc =  explode(',', $detail['INCLUSION_ID']);   
        $skill = explode(',', $detail['SKILL_ID']);
        $model = new RecruitmentVacancyModel();
        $model->exchangeArrayFromDB($detail);
        $model->SkillId = $skill;
        $model->InclusionId = $inc;
        $this->form->bind($model);
        // echo '<pre>'; print_r($model); die();
        $Vacancy_types = array("OPEN" => "OPEN", "INTERNAL_FORM" => "INTERNAL-FORM","INTERNAL_APPRAISAL" => "INTERNAL-APPRAISAL", );
        $OpeningVacancyNo = EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS', ['OPENING_ID','VACANCY_TOTAL_NO','RESERVATION_NO'], ['STATUS' => 'E']);
        return Helper::addFlashMessagesToArray($this, [
            'id' => $id,
            'form' => $this->form,
            'detail' => $detail,
            'ServiceEvents' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_REC_SERVICE_EVENTS_TYPES', 'SERVICE_EVENT_ID', ['SERVICE_EVENT_NAME'], ["STATUS" => "E"], 'SERVICE_EVENT_NAME', "ASC", null, [null => '---'], true),
            'DepartmentList' => EntityHelper::getTableKVListWithSortOption($this->adapter,Department::TABLE_NAME, 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], ["STATUS" => "E"], 'DEPARTMENT_NAME', "ASC", null, [null => '---'], true),           
            'Positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"], Designation::DESIGNATION_TITLE, "ASC", null, [null => '---'], true),
            'ServiceTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_SERVICE_TYPES', 'SERVICE_TYPE_ID', ['SERVICE_TYPE_NAME'], ["STATUS" => "E"], 'SERVICE_TYPE_NAME', "ASC", null, [null => '---'], true),
            'LevelList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_FUNCTIONAL_LEVELS', 'FUNCTIONAL_LEVEL_ID', ['FUNCTIONAL_LEVEL_EDESC'], ["STATUS" => "E"], 'FUNCTIONAL_LEVEL_EDESC', "ASC", null, [null => '---'], true),
            'QualificationList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_ACADEMIC_DEGREES', 'ACADEMIC_DEGREE_ID', ['ACADEMIC_DEGREE_NAME'], ["STATUS" => "E"], 'ACADEMIC_DEGREE_NAME', "ASC", null, [null => '---'], true),
            'OpeningVacancyNo' => $OpeningVacancyNo,
            'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
            'Vacancy_types' => $Vacancy_types,
            'InclusionList' => EntityHelper::getTableKVListWithSortOption($this->adapter, OptionsModel::TABLE_NAME, OptionsModel::OPTION_ID, [OptionsModel::OPTION_EDESC], ["STATUS" => "E"], OptionsModel::OPTION_EDESC, "ASC", null, [null => '---'], true),
            'Skills' => EntityHelper::getTableKVListWithSortOption($this->adapter, SkillModel::TABLE_NAME, SkillModel::SKILL_ID, [SkillModel::SKILL_NAME], ["STATUS" => "E"], SkillModel::SKILL_NAME, "ASC", null, [null => '---'], true),
        ]);
    }
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('vacancy');
        }
        $detail = $this->repository->fetchById($id);
        $model = new RecruitmentVacancyModel();
        $model->exchangeArrayFromDB($detail);
        $model->DeletedBy = $this->employeeId;
        $model->DeletedDt = Helper::getcurrentExpressionDate();
        $this->repository->delete($model, $id);
        $this->flashmessenger()->addMessage("Vacancy Deleted Successfully");
        return $this->redirect()->toRoute('vacancy');
    }
    public function CheckVacancyNoAction() {
        try {
            $request = $this->getRequest();
            $postedData = $request->getPost();
            $rawList = $this->repository->CheckVacancyno($postedData['oid']);
            return new JsonModel(['success' => true, 'data' => $rawList[0]['VACANCYNO'], 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
    public function CheckReserNoAction() {
        try {
            $request = $this->getRequest();
            $postedData = $request->getPost();
            $rawList = $this->repository->CheckReserNo($postedData['oid']);
            foreach($rawList as $value){
                $total[] = $value['RESERVATION_NO'];
            }
            $total = array_sum($total);
            // echo '<pre>'; print_r($total); die;
            return new JsonModel(['success' => true, 'data' => $total, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
    public function getadnoAction(){
        $id = (int) $this->params()->fromRoute('id');
        $result = $this->repository->getadno($id);
        return json_encode($result);
    }
    public function uniqueCheckAction()
    {
        
        return new JsonModel(['success' => 'true' ]);

    }
   

}
