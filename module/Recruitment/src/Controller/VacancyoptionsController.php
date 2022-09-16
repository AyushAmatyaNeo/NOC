<?php

namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Recruitment\Form\VacancyoptionsForm;
use Recruitment\Model\Vacancyoptions;
use Recruitment\Model\OptionsModel as OptionsTable;
use Recruitment\Model\RecruitmentVacancy;
use Recruitment\Repository\VacancyoptionsRepository;
use Zend\View\Model\JsonModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\ViewModel;

class VacancyoptionsController extends HrisController {
    
    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(VacancyoptionsRepository::class);
        $this->initializeForm(VacancyoptionsForm::class);
        // $this->VacancyRepository = new VacancyRepository($adapter);
    }
    public function indexAction() 
    {
        $request = $this->getRequest();
                if ($request->isPost()) {
                    try {
                        $data = (array) $request->getPost();
                        $rawList = $this->repository->getFilteredRecords($data);
                        // print_r($rawList); die;
                        $list = iterator_to_array($rawList, false);
                        return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
                    } catch (Exception $e) {
                        return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
                    }
                }
        
        $statusSE = $this->getRecStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        $OptionList = EntityHelper::getTableList($this->adapter, OptionsTable::TABLE_NAME, ['OPTION_ID','OPTION_EDESC'], null);
        return $this->stickFlashMessagesTo([
            'status' => $statusSE,
            'OptionList' => $OptionList         
        ]);
    }
    public function addAction() 
    {   
        $request = $this->getRequest();
        $postData = $request->getPost();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            
            if ($this->form->isValid()) {
                $vacancy_option_data = new Vacancyoptions();
                $vacancy_option_data->exchangeArrayFromForm($this->form->getData());
                $detailCount=count($postData['OptionId']);
                // echo'<pre>'; print_r($vacancy_option_data); die();
                for($i=0; $i<$detailCount; $i++)
            {
                $vacancy_option_data->VacancyOptionId = ((int) Helper::getMaxId($this->adapter, Vacancyoptions::TABLE_NAME, Vacancyoptions::VACANCY_OPTION_ID)) + 1;
                $vacancy_option_data->OptionId=$postData['OptionId'][$i];
                $vacancy_option_data->OpenInternal=$postData['OpenInternal'][$i];;
                $vacancy_option_data->Quota=$postData['Quota'][$i];
                $vacancy_option_data->NormalAmt=$postData['NormalAmt'][$i];
                $vacancy_option_data->LateAmt=$postData['LateAmt'][$i];
                $vacancy_option_data->Status='E';
                $vacancy_option_data->Remarks=$postData['Remarks'][$i];
                $vacancy_option_data->CreatedBy = $this->employeeId;
                $vacancy_option_data->CreatedDt = Helper::getcurrentExpressionDate();
                $this->repository->add($vacancy_option_data);
            }
                $this->flashmessenger()->addMessage("Vacancy options Successfully added!!!");
                return $this->redirect()->toRoute("vacancyoptions");
            }
        }
        $positions = $this->repository->getVacancyPositions();
        $OptionList = EntityHelper::getTableList($this->adapter, OptionsTable::TABLE_NAME, ['OPTION_ID','OPTION_EDESC'], ['STATUS' => 'E']);
        $Quota_open = EntityHelper::getTableList($this->adapter, RecruitmentVacancy::TABLE_NAME, ['VACANCY_ID','QUOTA_OPEN'], null);
        $Quota_open_left = EntityHelper::getTableList($this->adapter, Vacancyoptions::TABLE_NAME, ['VACANCY_ID','OPEN_INTERNAL','QUOTA'], ['STATUS' => 'E']);
        $Quota_internal = EntityHelper::getTableList($this->adapter, RecruitmentVacancy::TABLE_NAME, ['VACANCY_ID','QUOTA_INTERNAL'], null);
        // echo '<pre>'; print_r($Quota_open); die;
        return new ViewModel(Helper::addFlashMessagesToArray(
                    $this, [
                        'customRenderer' => Helper::renderCustomView(),
                        'form' => $this->form,
                        'VacancyList' => EntityHelper::getTableKVListWithSortOption($this->adapter, RecruitmentVacancy::TABLE_NAME, RecruitmentVacancy::VACANCY_ID, [RecruitmentVacancy::AD_NO], ["STATUS" => "E"], RecruitmentVacancy::AD_NO, "ASC", null, [null => '---'], true),
                        'OptionList' => $OptionList,
                        'positions' => $positions,
                        'Quota_open' => $Quota_open,
                        'Quota_internal' => $Quota_internal,
                        'Quota_open_left' => $Quota_open_left
                    ]
                )
        );
    }
    public function viewAction() 
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancyoptions");
        }

        $detail = $this->repository->fetchBydetails($id);
        $model = new Vacancyoptions();
        // echo '<pre>'; print_r($detail); die();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);
                
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail,
                    'VacancyList' => EntityHelper::getTableKVListWithSortOption($this->adapter, RecruitmentVacancy::TABLE_NAME, RecruitmentVacancy::VACANCY_ID, [RecruitmentVacancy::AD_NO], ["STATUS" => "E"], RecruitmentVacancy::AD_NO, "ASC", null, [null => '---'], true),
                    'OptionList' => EntityHelper::getTableKVListWithSortOption($this->adapter, OptionsTable::TABLE_NAME, OptionsTable::OPTION_ID, [OptionsTable::OPTION_EDESC], ["STATUS" => "E"], OptionsTable::OPTION_EDESC, "ASC", null, [null => '---'], true)
        ]);
    }
    public function editAction() 
    {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id');        
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancyoptions");
        }       
        if ($request->isPost()) {
            $vacancy_option_data = new Vacancyoptions();
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {    
                $vacancy_option_data->exchangeArrayFromForm($this->form->getData());
                $vacancy_option_data->OptionId=$postData['OptionId'];
                $vacancy_option_data->VacancyOptionId=$id;
                $vacancy_option_data->OpenInternal=$postData['OpenInternal'];
                $vacancy_option_data->Quota=$postData['Quota'];
                $vacancy_option_data->NormalAmt=$postData['NormalAmt'];
                $vacancy_option_data->LateAmt=$postData['LateAmt'];
                $vacancy_option_data->Remarks=$postData['Remarks'];
                $vacancy_option_data->ModifiedDt = Helper::getcurrentExpressionDate();
                $vacancy_option_data->ModifiedBy = $this->employeeId;
                // echo '<pre>'; print_r($vacancy_option_data); die;
                $this->repository->edit($vacancy_option_data, $id);
                $this->flashmessenger()->addMessage("Vacancy  Options Successfully Edited!!!");
                return $this->redirect()->toRoute("vacancyoptions");
            }
        }
        $detail = $this->repository->fetchBydetails($id);
        $options = $this->repository->alldataoptions();
        $model = new Vacancyoptions();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);
        $OptionList = EntityHelper::getTableList($this->adapter, OptionsTable::TABLE_NAME, ['OPTION_ID','OPTION_EDESC'], ['STATUS' => 'E']);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail,
                    'VacancyList' => EntityHelper::getTableKVListWithSortOption($this->adapter, RecruitmentVacancy::TABLE_NAME, RecruitmentVacancy::VACANCY_ID, [RecruitmentVacancy::AD_NO], ["STATUS" => "E"], RecruitmentVacancy::AD_NO, "ASC", null, [null => '---'], true),
                    'OptionList' => $OptionList,
                    'optionsdata' => $options
        ]);
    }
    public function deleteAction() 
    {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('vacancyoptions');
        }
        $detail = $this->repository->fetchById($id);
        $model = new OptionsTable();
        // echo '<pre>'; print_r($detail); die;
        $model->exchangeArrayFromDB($detail);
        $model->DeletedBy = $this->employeeId;
        $model->DeletedDt = Helper::getcurrentExpressionDate();
            // echo '<pre>'; print_r($model); die;
        $this->repository->delete($model, $id);
        $this->flashmessenger()->addMessage("Vacancy Options Deleted Successfully");
        return $this->redirect()->toRoute('vacancyoptions');
    }

}