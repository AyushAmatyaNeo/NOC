<?php

namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Recruitment\Form\VacancyStageForm;
use Recruitment\Model\VacancyStageModel;
use Recruitment\Model\RecruitmentVacancy;
use Recruitment\Model\StageModel;
use Recruitment\Repository\VacancyStageRepository;
use Zend\View\Model\JsonModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\ViewModel;

class VacancyStageController extends HrisController {
    
    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(VacancyStageRepository::class);
        $this->initializeForm(VacancyStageForm::class);
        // $this->VacancyRepository = new VacancyRepository($adapter);
    }
    public function indexAction() 
    {
        $request = $this->getRequest();
                if ($request->isPost()) {
                    try {
                        $data = (array) $request->getPost();
                        // echo '<pre>'; print_r($data); die;   
                        $rawList = $this->repository->getFilteredRecords($data);
                        
                        $list = iterator_to_array($rawList, false);
                        return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
                    } catch (Exception $e) {
                        return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
                    }
                }
        
        $statusSE = $this->getRecStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        $stages =  $this->repository->getFilteredStages();
        return $this->stickFlashMessagesTo([
            'status' => $statusSE,
            'Stages' => $stages,
            'Adno' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_VACANCY', ['VACANCY_ID','AD_NO'], ['STATUS' => 'E']), 
            'opening' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS', ['OPENING_ID','OPENING_NO'], ['STATUS' => 'E'])
        ]);
    }
    public function addAction()
    {
        $request = $this->getRequest();
        $postData = $request->getPost();
        if ($request->isPost()) {
            $this->form->setData($postData);            
            if ($this->form->isValid()) {
                $vacancy_stage_data = new VacancyStageModel();
                $vacancy_stage_data->exchangeArrayFromForm($this->form->getData());
                $vacancy_stage_data->RecVacancyStageId = ((int) Helper::getMaxId($this->adapter, VacancyStageModel::TABLE_NAME, VacancyStageModel::REC_VACANCY_STAGE_ID)) + 1;
                $vacancy_stage_data->Status='E';
                $vacancy_stage_data->CreatedBy = $this->employeeId;
                $vacancy_stage_data->CreatedDt = Helper::getcurrentExpressionDate();
                // echo '<pre>'; print_r($vacancy_stage_data); die;
                $this->repository->add($vacancy_stage_data);
                $this->flashmessenger()->addMessage("Vacancy Stage Successfully added!!");
                return $this->redirect()->toRoute("vacancystage");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                    $this, [
                        'customRenderer' => Helper::renderCustomView(),
                        'form' => $this->form,
                        'VacancyList' => EntityHelper::getTableKVListWithSortOption($this->adapter, RecruitmentVacancy::TABLE_NAME, RecruitmentVacancy::VACANCY_ID, [RecruitmentVacancy::AD_NO], ["STATUS" => "E"], RecruitmentVacancy::AD_NO, "ASC", null, [null => '---'], true),
                        'RecStageList' => EntityHelper::getTableKVListWithSortOption($this->adapter, StageModel::TABLE_NAME, StageModel::REC_STAGE_ID, [StageModel::STAGE_EDESC], ["STATUS" => "E"], StageModel::STAGE_EDESC, StageModel::ORDER_NO, null, [null => '---'], true),
                        
                    ]
                )
        );
    }
    public function viewAction()
    {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute('vacancystage');
        }
        $detail = $this->repository->fetchById($id);
        // echo '<pre>'; print_r($detail); die;
        $model = new VacancyStageModel();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);
        return Helper::addFlashMessagesToArray($this, [
                'form' => $this->form,
                'detail' => $detail,
                'VacancyList' => EntityHelper::getTableKVListWithSortOption($this->adapter, RecruitmentVacancy::TABLE_NAME, RecruitmentVacancy::VACANCY_ID, [RecruitmentVacancy::AD_NO], ["STATUS" => "E"], RecruitmentVacancy::AD_NO, "ASC", null, [null => '---'], true),
                'RecStageList' => EntityHelper::getTableKVListWithSortOption($this->adapter, StageModel::TABLE_NAME, StageModel::REC_STAGE_ID, [StageModel::STAGE_EDESC], ["STATUS" => "E"], StageModel::STAGE_EDESC, "ASC", null, [null => '---'], true),
        ]);
    }
    public function editAction()
    {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute('vacancystage');
        }
        if ($request->isPost())
        {
            $stage_data = new VacancyStageModel();
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid())
            {
                $stage_data->exchangeArrayFromForm($this->form->getData());
                $stage_data->ModifiedDt = Helper::getcurrentExpressionDate();
                $stage_data->ModifiedBy = $this->employeeId;
                // echo '<pre>'; print_r($stage_data); die;
                $this->repository->edit($stage_data, $id);
                $this->flashmessenger()->addMessage("Vacancy Stage Edited Successfully!");
                return $this->redirect()->toRoute('vacancystage');

            }
        }
        $detail = $this->repository->fetchById($id);
        // echo '<pre>'; print_r($detail); die;
        $model = new VacancyStageModel();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);
        return Helper::addFlashMessagesToArray($this, [
                'form' => $this->form,
                'detail' => $detail,
                'VacancyList' => EntityHelper::getTableKVListWithSortOption($this->adapter, RecruitmentVacancy::TABLE_NAME, RecruitmentVacancy::VACANCY_ID, [RecruitmentVacancy::AD_NO], ["STATUS" => "E"], RecruitmentVacancy::AD_NO, "ASC", null, [null => '---'], true),
                'RecStageList' => EntityHelper::getTableKVListWithSortOption($this->adapter, StageModel::TABLE_NAME, StageModel::REC_STAGE_ID, [StageModel::STAGE_EDESC], ["STATUS" => "E"], StageModel::STAGE_EDESC, "ASC", null, [null => '---'], true),
        ]);
    }
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id == 0)
        {
            return $this->redirect()->toRoute('vacancystage');
        }
        $detail = $this->repository->fetchById($id);
        $model = new VacancyStageModel();
        $model->exchangeArrayFromDB($detail);
        $model->DeletedBy = $this->employeeId;
        $model->DeletedDt = Helper::getcurrentExpressionDate();
        $this->repository->delete($model, $id);
        $this->flashmessenger()->addMessage('Vacancy stage Deleted Successfully!');
        $this->redirect()->toRoute('vacancystage');

    }
    public function bulkStageWSAction() {
        try {
            $request = $this->getRequest();
            $postedData = $request->getPost();
            // echo '<pre>'; print_r($postedData); die;
            $this->repository->manualStage($postedData['StageId'],$postedData['id']);
            return new JsonModel(['success' => true, 'data' => [], 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
}