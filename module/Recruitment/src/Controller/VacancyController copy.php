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

        $statusSE = $this->getRecStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        $GenderSE = $this->getRecGenderSelectElement(['name' => 'Gender', 'id' => 'Gender', 'class' => 'form-control reset-field', 'label' => 'Gender']);
        $DepartmentSE = $this->getRecDepartmentSelectElement(['name' => 'Department', 'id' => 'DepartmentId', 'class' => 'form-control reset-field', 'label' => 'Department']);
        $statusSelectElement = EntityHelper::getQualificationStatusSelectElement();
        return $this->stickFlashMessagesTo([
            'status' => $statusSE,
            'Gender' => $GenderSE,
            'QualificationId' => $statusSelectElement,
            'DepartmentId' => $DepartmentSE
        ]);
    }
    public function addAction()
    {
        $request = $this->getRequest();
        $postData = $request->getPost();
        
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
           
           
            if ($this->form->isValid()) {
                // echo '<pre>'; print_r($postData['VacancyNo']); die;
                $VacancyData = new RecruitmentVacancyModel();
                $VacancyData->exchangeArrayFromForm($this->form->getData());
               // Insert Vacancy table data
               $vacancyCount = count($postData['AdNo']);
                for($i = 0; $i < $vacancyCount; $i++){
                    $VacancyData->VacancyId         = ((int) Helper::getMaxId($this->adapter, RecruitmentVacancyModel::TABLE_NAME, RecruitmentVacancyModel::VACANCY_ID)) + 1;
                    $last_value = null;
                    if(empty($postData['VacancyNo'][$i])){
                        $VacancyData->VacancyNo         = $postData['VacancyNo'][$i-1];
                    } else {
                        $VacancyData->VacancyNo         = $postData['VacancyNo'][$i];
                    }
                    // $VacancyData->VacancyNo         = $postData['VacancyNo'][$i];
                    $VacancyData->OpeningId         = $postData['OpeningId'];
                    $VacancyData->Vacancy_type      = $postData['Vacancy_type'];
                    $VacancyData->LevelId           = $postData['LevelId'][$i];
                    $VacancyData->ServiceTypesId     = $postData['ServiceTypesId'][$i];
                    $VacancyData->ServiceEventsId   = $postData['ServiceEventsId'][$i]; 
                    $VacancyData->PositionId        = $postData['PositionId'][$i];
                    $VacancyData->AdNo              = $postData['AdNo'][$i];
                    $VacancyData->VacancyReservationNo   = $postData['VacancyReservationNo'][$i];
                    $VacancyData->QualificationId   = $postData['QualificationId'][$i];
                    $VacancyData->Experience        = $postData['Experiance'][$i];
                    $VacancyData->DepartmentId      = $postData['DepartmentId'][$i];                                 
                    $VacancyData->Remark            = $postData['Remark'][$i];
                    $VacancyData->CreatedBy = $this->employeeId;
                    // $VacancyData->CreatedDt = Helper::getcurrentExpressionDate();
                    $VacancyData->CreatedDt = date('Y-m-d');
                    // echo '<pre>'; print_r($VacancyData); die;
                    // $this->repository->add($VacancyData);
                    // $i += $i;
                }

                // echo '<pre>'; print_r($VacancyData); die;
                // Insert options table data
                $VacancyReservationNo = count($postData['VacancyReservationNo']);
                for($i = 0; $i < $VacancyReservationNo; $i++){
                    $VacancyData->AdNo                   = $postData['AdNo'][$i];
                    $VacancyData->VacancyReservationNo   = $postData['VacancyReservationNo'][$i];
                    $VacancyData->InclusionId            = $postData['InclusionId'][$i];
                    $VacancyData->Remark                   = $postData['Remark'][$i];
                    $VacancyData->AdNo                   = $postData['AdNo'][$i];
                }

                // Add Stage details
                // $Rec_stage = new VacancyStageModel();
                // $Rec_stage->RecVacancyStageId = ((int) Helper::getMaxId($this->adapter, VacancyStageModel::TABLE_NAME, VacancyStageModel::VacancyData_STAGE_ID)) + 1;
                // $Rec_stage->RecStageId = '1';  // Stage id 1 = OPEN
                // $Rec_stage->VacancyId = ((int) Helper::getMaxId($this->adapter, RecruitmentVacancyModel::TABLE_NAME, RecruitmentVacancyModel::VACANCY_ID));
                // $Rec_stage->CreatedBy = $this->employeeId;
                // $Rec_stage->Status = 'E';
                // $Rec_stage->CreatedDt = Helper::getcurrentExpressionDate();
                // $this->stageRepository->add($Rec_stage);


                // Inclusion Details add
                // $inclusionCount = count($postData['InclusionId']);
                // $Rec_inclusion = new VacancyInclusionModel();
                // $Rec_inclusion->exchangeArrayFromForm($this->form->getData());
                // for ($i = 0; $i < $inclusionCount; $i++) {
                //     $Rec_inclusion->vacancyInclusionId = ((int) Helper::getMaxId($this->adapter, VacancyInclusionModel::TABLE_NAME, VacancyInclusionModel::VACANCY_INCLUSION_ID)) + 1;
                //     $Rec_inclusion->InclusionId = $postData['InclusionId'][$i];
                //     $Rec_inclusion->VacancyId = ((int) Helper::getMaxId($this->adapter, RecruitmentVacancyModel::TABLE_NAME, RecruitmentVacancyModel::VACANCY_ID));
                //     $Rec_inclusion->CreatedBy = $this->employeeId;
                //     $Rec_inclusion->CreatedDt = Helper::getcurrentExpressionDate();
                //     $this->VacancyInclusionRepository->add($Rec_inclusion);
                //     // echo  $Rec_inclusion->InclusionId;
                // }
                $this->flashmessenger()->addMessage("Vacancy Successfully added!!!");
                return $this->redirect()->toRoute("vacancy");
            }
        }
        // echo 'HEre'; die;
        $ServiceEvents = EntityHelper::getTableList($this->adapter, 'HRIS_REC_SERVICE_EVENTS_TYPES', ['SERVICE_EVENT_ID','SERVICE_EVENT_NAME'], ['STATUS' => 'E']);
        $DepartmentList = EntityHelper::getTableList($this->adapter, Department::TABLE_NAME, ['DEPARTMENT_ID','DEPARTMENT_NAME'], ['STATUS' => 'E']);
        $QualificationList = EntityHelper::getTableList($this->adapter, AcademicDegree::TABLE_NAME, ['ACADEMIC_DEGREE_ID','ACADEMIC_DEGREE_NAME'], ['STATUS' => 'E']);
        $Positions = EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, ['DESIGNATION_ID','DESIGNATION_TITLE'], ['STATUS' => 'E']);
        $ServiceTypes = EntityHelper::getTableList($this->adapter, 'HRIS_SERVICE_TYPES', ['SERVICE_TYPE_ID','SERVICE_TYPE_NAME'], ['STATUS' => 'E']);
        $InclusionList = EntityHelper::getTableList($this->adapter, OptionsModel::TABLE_NAME, ['OPTION_ID','OPTION_EDESC'], ['STATUS' => 'E']);
        $LevelList = EntityHelper::getTableList($this->adapter, 'HRIS_FUNCTIONAL_LEVELS', ['FUNCTIONAL_LEVEL_ID','FUNCTIONAL_LEVEL_EDESC'], ['STATUS' => 'E']);
        $OpeningVacancyNo = EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS', ['OPENING_ID','VACANCY_TOTAL_NO'], ['STATUS' => 'E']);
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
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    // 'vacancyList' => EntityHelper::getTableList($this->adapter, RecruitmentVacancyModel::TABLE_NAME, [RecruitmentVacancyModel::VACANCY_ID]),
                    'messages' => $this->flashmessenger()->getMessages(),
                    // 'LevelList' => EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_FUNCTIONAL_LEVELS', 'FUNCTIONAL_LEVEL_ID', ['FUNCTIONAL_LEVEL_EDESC'], ["STATUS" => "E"], 'FUNCTIONAL_LEVEL_EDESC', "ASC", null, [null => '---'], true),
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
        // $inclusion = $detail['INCLUSION_ID'];
        $detail['INCLUSION_ID'] = explode(',', $detail['INCLUSION_ID']);
        // echo '<pre>'; print_r($detail); die();
        $model = new RecruitmentVacancyModel();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);

        return Helper::addFlashMessagesToArray($this, [
            'id' => $id,
            'form' => $this->form,
            'detail' => $detail,
            'inc'   => $inc,
            'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
            'QualificationList' => EntityHelper::getTableKVListWithSortOption($this->adapter, AcademicDegree::TABLE_NAME, AcademicDegree::ACADEMIC_DEGREE_ID, [AcademicDegree::ACADEMIC_DEGREE_NAME], ["STATUS" => "E"], AcademicDegree::ACADEMIC_DEGREE_NAME, "ASC", null, [null => '---'], true),
            'Positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"], Designation::DESIGNATION_TITLE, "ASC", null, [null => '---'], true),
            'DepartmentList' => EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], ["STATUS" => "E"], Department::DEPARTMENT_NAME, "ASC", null, [null => '---'], true),
            'GenderList' => EntityHelper::getTableKVListWithSortOption($this->adapter, Gender::TABLE_NAME, Gender::GENDER_ID, [Gender::GENDER_NAME], ["STATUS" => "E"], Gender::GENDER_NAME, "ASC", null, [null => '---'], true),
            'LevelList' => EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_FUNCTIONAL_LEVELS', 'FUNCTIONAL_LEVEL_ID', ['FUNCTIONAL_LEVEL_EDESC'], ["STATUS" => "E"], 'FUNCTIONAL_LEVEL_EDESC', "ASC", null, [null => '---'], true),
            'ServiceTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_SERVICE_TYPES', 'SERVICE_TYPE_ID', ['SERVICE_TYPE_NAME'], ["STATUS" => "E"], 'SERVICE_TYPE_NAME', "ASC", null, [null => '---'], true),
            'ServiceEvents' => EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_REC_SERVICE_EVENTS_TYPES', 'SERVICE_EVENT_ID', ['SERVICE_EVENT_NAME'], ["STATUS" => "E"], 'SERVICE_EVENT_NAME', "ASC", null, [null => '---'], true),
            'InclusionList' => EntityHelper::getTableKVListWithSortOption($this->adapter, OptionsModel::TABLE_NAME, OptionsModel::OPTION_ID, [OptionsModel::OPTION_EDESC], ["STATUS" => "E"], OptionsModel::OPTION_EDESC, "ASC", null, [null => '---'], true),

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
                $vacancydata->ModifiedDt = Helper::getcurrentExpressionDate();
                $vacancydata->ModifiedBy = $this->employeeId;
                $this->repository->edit($vacancydata, $id);
                //Inclusion insert
                $inclusionCount = count($postData['InclusionId']);
                $Rec_inclusion = new VacancyInclusionModel();
                $Rec_inclusion->exchangeArrayFromForm($this->form->getData());
                $Rec_inclusion->InclusionId = implode(",", array_values($vacancydata->InclusionId));
                //Delete Existing Inclusion First
                $DeletedBy = $this->employeeId;
                $delete = $this->VacancyInclusionRepository->delete($DeletedBy,$id);
                if ($delete) {
                    for ($i = 0; $i < $inclusionCount; $i++) {
                        $Rec_inclusion->vacancyInclusionId = ((int) Helper::getMaxId($this->adapter, VacancyInclusionModel::TABLE_NAME, VacancyInclusionModel::VACANCY_INCLUSION_ID)) + 1;
                        $Rec_inclusion->InclusionId = $postData['InclusionId'][$i];
                        $Rec_inclusion->VacancyId = $id;
                        $Rec_inclusion->CreatedBy = $this->employeeId;
                        $Rec_inclusion->CreatedDt = date('Y-m-d');
                        // echo '<pre>'; print_r($Rec_inclusion); die();
                        // $this->VacancyInclusionRepository->delete($Rec_inclusion,$id);
                        $this->VacancyInclusionRepository->add($Rec_inclusion);
                    }
                }
                $this->flashmessenger()->addMessage("Vacancy Successfully Edited!!!");
                return $this->redirect()->toRoute("vacancy");
            }
        }
        $detail = $this->repository->fetchById($id);
        $inclusion = Helper::extractDbData($this->VacancyInclusionRepository->fetchById($id));
        $inc = implode(',', array_map(function ($el) {return $el['INCLUSION_ID']; }, $inclusion));
        $model = new RecruitmentVacancyModel();
        $model->exchangeArrayFromDB($detail);
        $model->InclusionId = $inc;
        $this->form->bind($model);
        // echo '<pre>'; print_r($inc); die();
        return Helper::addFlashMessagesToArray($this, [
            'id' => $id,
            'form' => $this->form,
            'detail' => $detail,
            'inc'   => $inc,
            'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
            'QualificationList' => EntityHelper::getTableKVListWithSortOption($this->adapter, AcademicDegree::TABLE_NAME, AcademicDegree::ACADEMIC_DEGREE_ID, [AcademicDegree::ACADEMIC_DEGREE_NAME], ["STATUS" => "E"], AcademicDegree::ACADEMIC_DEGREE_NAME, "ASC", null, [null => '---'], true),
            'GenderList' => EntityHelper::getTableKVListWithSortOption($this->adapter, Gender::TABLE_NAME, Gender::GENDER_ID, [Gender::GENDER_NAME], ["STATUS" => "E"], Gender::GENDER_NAME, "ASC", null, [null => '---'], true),
            'Positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"], Designation::DESIGNATION_TITLE, "ASC", null, [null => '---'], true),
            'DepartmentList' => EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], ["STATUS" => "E"], Department::DEPARTMENT_NAME, "ASC", null, [null => '---'], true),
            'LevelList' => EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_FUNCTIONAL_LEVELS', 'FUNCTIONAL_LEVEL_ID', ['FUNCTIONAL_LEVEL_EDESC'], ["STATUS" => "E"], 'FUNCTIONAL_LEVEL_EDESC', "ASC", null, [null => '---'], true),
            'ServiceTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_SERVICE_TYPES', 'SERVICE_TYPE_ID', ['SERVICE_TYPE_NAME'], ["STATUS" => "E"], 'SERVICE_TYPE_NAME', "ASC", null, [null => '---'], true),
            'ServiceEvents' => EntityHelper::getTableKVListWithSortOption($this->adapter, 'HRIS_REC_SERVICE_EVENTS_TYPES', 'SERVICE_EVENT_ID', ['SERVICE_EVENT_NAME'], ["STATUS" => "E"], 'SERVICE_EVENT_NAME', "ASC", null, [null => '---'], true),
            'InclusionList' => EntityHelper::getTableKVListWithSortOption($this->adapter, OptionsModel::TABLE_NAME, OptionsModel::OPTION_ID, [OptionsModel::OPTION_EDESC], ["STATUS" => "E"], OptionsModel::OPTION_EDESC, "ASC", null, [null => '---'], true),
            // 'InclusionId' => EntityHelper::getTableKVListWithSortOption($this->adapter, VacancyInclusionModel::TABLE_NAME, VacancyInclusionModel::VACANCY_INCLUSION_ID, [VacancyInclusionModel::OPTION_EDESC], ["STATUS" => "E"], OptionsModel::OPTION_EDESC, "ASC", null, [null => '---'], true),
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
}
