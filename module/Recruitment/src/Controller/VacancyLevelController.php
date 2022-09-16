<?php
namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\EntityHelper;
use Zend\View\Model\JsonModel;
use Recruitment\Repository\VacancyLevelRepository;
use Recruitment\Model\VacancyLevelModel;
use Recruitment\Model\OpeningVacancy;
use Recruitment\Form\VacancyLevelForm;
use Setup\Model\Designation;
use Zend\View\Model\ViewModel;
use Application\Helper\Helper;
use Exception;

class VacancyLevelController extends HrisController
{
    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(VacancyLevelRepository::class);
        $this->initializeForm(VacancyLevelForm::class);
        
    }
    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $rawList = $this->repository->getFilteredRecords($data);
                $list = iterator_to_array($rawList, false);
                // echo '<pre>'; print_r($list); die();                         
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
            if ($this->form->isValid()) 
            {
                $level_data = new VacancyLevelModel();
                $level_data->exchangeArrayFromForm($this->form->getData());
                $level_data->vacacnyLevelId = ((int) Helper::getMaxId($this->adapter, VacancyLevelModel::TABLE_NAME, VacancyLevelModel::VACANCY_LEVEL_ID)) + 1;
                $level_data->CreatedBy = $this->employeeId;
                $level_data->EffectiveDate = Helper::getExpressionDate($level_data->EffectiveDate);
                $level_data->CreatedDt = Helper::getcurrentExpressionDate();
                $level_data->Status = 'E';
                $this->repository->add($level_data);
                $this->flashmessenger()->addMessage("Vacancy level Successfully added!!");
                return $this->redirect()->toRoute("vacancylevel");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                    $this, [
                        'customRenderer' => Helper::renderCustomView(),
                        'form' => $this->form,
                        'LevelList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_FUNCTIONAL_LEVELS', 'FUNCTIONAL_LEVEL_ID', ['FUNCTIONAL_LEVEL_EDESC'], ["STATUS" => "E"], 'FUNCTIONAL_LEVEL_EDESC', "ASC", null, [null => '---'], true),
                        'Positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"], Designation::DESIGNATION_TITLE, "ASC", null, [null => '---'], true),
                        'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    ]
                )
        );
        
    }
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        // echo $id; die;
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancy");
        }

        $detail = $this->repository->fetchById($id);
        // echo '<pre>'; print_r($detail); die(); 
        $model = new VacancyLevelModel();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);
                
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'form' => $this->form,                    
                    'LevelList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_FUNCTIONAL_LEVELS', 'FUNCTIONAL_LEVEL_ID', ['FUNCTIONAL_LEVEL_EDESC'], ["STATUS" => "E"], 'FUNCTIONAL_LEVEL_EDESC', "ASC", null, [null => '---'], true),
                    'Positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"], Designation::DESIGNATION_TITLE, "ASC", null, [null => '---'], true),
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    
        ]);
    }
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        // echo $id; die;
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancy");
        }
        $request = $this->getRequest();
        if($request->isPost())
        {
            $level_data = new VacancyLevelModel();
                $postedData = $request->getpost();
                $this->form->setdata($postedData);
                if($this->form->isValid())
                {
                    $level_data->exchangeArrayFromForm($this->form->getData());
                    $level_data->ModifiedDt = Helper::getcurrentExpressionDate();
                    $level_data->EffectiveDate = Helper::getExpressionDate($level_data->EffectiveDate);
                    $level_data->ModifiedBy = $this->employeeId; 
                    $this->repository->edit($level_data, $id);
                    $this->flashmessenger()->addMessage("Vacancy Level Edited Successfully!");
                    return $this->redirect()->toRoute('vacancylevel');

                }
        }

        $detail = $this->repository->fetchById($id);
        $model = new VacancyLevelModel();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);
        $position_id =  $detail[0]['POSITION_ID'];
        // echo '<pre>'; print_r($detail); die;
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'form' => $this->form,                    
                    // 'LevelList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_FUNCTIONAL_LEVELS', 'FUNCTIONAL_LEVEL_ID', ['FUNCTIONAL_LEVEL_EDESC'], ["STATUS" => "E"], 'FUNCTIONAL_LEVEL_EDESC', "ASC", null, [null => '---'], true),
                    'Positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"], Designation::DESIGNATION_TITLE, "ASC", null, [null => '---'], true),
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    
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
        $model = new VacancyLevelModel();
        $model->exchangeArrayFromDB($details);
        $model->DeletedBy = $this->employeeId;
        $model->DeletedDt = Helper::getcurrentExpressionDate();
        // echo '<pre>'; print_r($model); die;
        $this->repository->delete($model, $id);
        $this->flashmessenger()->addMessage('Vacancy Level Deleted!');
        return $this->redirect()->toRoute('vacancylevel');

    }

    public function pulllevelIdAction(){
        // echo 'LOL';
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $rawList = $this->repository->pullLevelData($data['designation_id']);
                $list = iterator_to_array($rawList, false);
                // echo '<pre>'; print_r($rawList);                          
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }
}