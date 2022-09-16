<?php

namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Recruitment\Form\VacancyInclusionForm;
use Recruitment\Repository\VacancyInclusionRepository;
use Recruitment\Model\VacancyInclusionModel;
use Recruitment\Model\OptionsModel;
use Recruitment\Model\RecruitmentVacancy;
use Exception;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class VacancyInclusionController extends HrisController {
    
    protected $stageRepository;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(VacancyInclusionRepository::class);
        $this->initializeForm(VacancyInclusionForm::class);
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
                $inclusion_data = new VacancyInclusionModel();
                $inclusion_data->exchangeArrayFromForm($this->form->getData());
                $inclusion_data->vacancyInclusionId = ((int) Helper::getMaxId($this->adapter, VacancyInclusionModel::TABLE_NAME, VacancyInclusionModel::VACANCY_INCLUSION_ID)) + 1;
                $inclusion_data->CreatedBy = $this->employeeId;
                $inclusion_data->CreatedDt = Helper::getcurrentExpressionDate();
                $inclusion_data->InclusionId = implode(",",array_values($inclusion_data->InclusionId));
                $inclusion_data->Status = 'E'; 
                // echo '<pre>'; print_r($inclusion_data); die();               
                $this->repository->add($inclusion_data);
                $this->flashmessenger()->addMessage("Data Successfully added!!");
                return $this->redirect()->toRoute("vacancyinclusion");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                    $this, [
                        'customRenderer' => Helper::renderCustomView(),
                        'form' => $this->form,
                        'InclusionList' => EntityHelper::getTableKVListWithSortOption($this->adapter, OptionsModel::TABLE_NAME, OptionsModel::OPTION_ID, [OptionsModel::OPTION_EDESC], ["STATUS" => "E"], OptionsModel::OPTION_EDESC, "ASC", null, [null => '---'], true),
                        'VacancyList' => EntityHelper::getTableKVListWithSortOption($this->adapter, RecruitmentVacancy::TABLE_NAME, RecruitmentVacancy::VACANCY_ID, [RecruitmentVacancy::AD_NO], ["STATUS" => "E"], RecruitmentVacancy::AD_NO, "ASC", null, [null => '---'], true),
                    ]
                )
        );
        
    }
    public function editAction()
    {
        $id = (int) $this->params()->fromroute('id');
        if( $id === 0)
        {
            return $this->redirect()->toRoute('vacancyinclusion');
        }
            $request = $this->getRequest();
            if($request->isPost())
            {
                $inclusion_data = new VacancyInclusionModel();
                $postedData = $request->getpost();
                $this->form->setdata($postedData);
                if($this->form->isValid())
                {
                    $inclusion_data->exchangeArrayFromForm($this->form->getData());
                    $inclusion_data->ModifiedDt = Helper::getcurrentExpressionDate();
                    $inclusion_data->ModifiedBy = $this->employeeId; 
                    $this->repository->edit($inclusion_data, $id);
                    $this->flashmessenger()->addMessage("Inclusion Edited Successfully!");
                    return $this->redirect()->toRoute('vacancyinclusion');

                }
            }
        $details = $this->repository->fetchById($id);
        // echo '<pre>'; print_r($details); die;
        $model = new VacancyInclusionModel();
        $model->exchangeArrayFromDB($details);  
        $this->form->bind($model);

        return Helper::addFlashMessagesToArray($this, [
                'form' => $this->form,
                'details' => $details,
                'InclusionList' => EntityHelper::getTableKVListWithSortOption($this->adapter, OptionsModel::TABLE_NAME, OptionsModel::OPTION_ID, [OptionsModel::OPTION_EDESC], ["STATUS" => "E"], OptionsModel::OPTION_EDESC, "ASC", null, [null => '---'], true),
                'VacancyList' => EntityHelper::getTableKVListWithSortOption($this->adapter, RecruitmentVacancy::TABLE_NAME, RecruitmentVacancy::VACANCY_ID, [RecruitmentVacancy::AD_NO], ["STATUS" => "E"], RecruitmentVacancy::AD_NO, "ASC", null, [null => '---'], true),
        ]);

    }
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute("vacancyinclusion");
        }
        $details = $this->repository->fetchById($id);
        // echo '<pre>'; print_r($details); die;
        $model = new VacancyInclusionModel();
        $model->exchangeArrayFromDB($details);
        $this->form->bind($model);

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'details' => $details,
                    'InclusionList' => EntityHelper::getTableKVListWithSortOption($this->adapter, OptionsModel::TABLE_NAME, OptionsModel::OPTION_ID, [OptionsModel::OPTION_EDESC], ["STATUS" => "E"], OptionsModel::OPTION_EDESC, "ASC", null, [null => '---'], true),
                    'VacancyList' => EntityHelper::getTableKVListWithSortOption($this->adapter, RecruitmentVacancy::TABLE_NAME, RecruitmentVacancy::VACANCY_ID, [RecruitmentVacancy::AD_NO], ["STATUS" => "E"], RecruitmentVacancy::AD_NO, "ASC", null, [null => '---'], true),
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
        $model = new VacancyInclusionModel();
        $model->exchangeArrayFromDB($details);
        $model->DeletedBy = $this->employeeId;
        $model->DeletedDt = Helper::getcurrentExpressionDate();
        // echo '<pre>'; print_r($model); die;
        $this->repository->delete($model, $id);
        $this->flashmessenger()->addMessage('Stage Deleted!');
        return $this->redirect()->toRoute('stage');

    }

}