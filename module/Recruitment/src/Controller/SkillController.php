<?php
namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Recruitment\Form\SkillForm;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\Helper;
use Zend\View\Model\ViewModel;
use Recruitment\Model\SkillModel;
use Recruitment\Repository\SkillRepository;
use Application\Helper\EntityHelper;
use Zend\View\Model\JsonModel;
use Exception;

Class SkillController extends HrisController{

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(SkillRepository::class);
        $this->initializeForm(SkillForm::class);
        
    }
    public function indexAction(){
        
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
                $skill_data = new SkillModel();
                $skill_data->exchangeArrayFromForm($this->form->getData());
                $skill_data->SkillId = ((int) Helper::getMaxId($this->adapter, SkillModel::TABLE_NAME, SkillModel::SKILL_ID)) + 1;
                $skill_data->CreatedBy = $this->employeeId;
                $skill_data->CreatedDt = Helper::getcurrentExpressionDate();
                $skill_data->Status = 'E'; 
                // echo '<pre>'; print_r($skill_data); die();               
                $this->repository->add($skill_data);
                $this->flashmessenger()->addMessage("Skill Data Successfully added!!!");
                return $this->redirect()->toRoute("skill");
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
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute('options');
        }
        $request = $this->getRequest();
        if ($request->isPost()) 
        {
            $skill_data = new SkillModel();
            $postedData = $request->getPost();
            // echo '<pre>'; print_r($postedData); die;
            $this->form->setData($postedData);
            if ($this->form->isValid()) {  
                // echo 'Valid'; die();              
                $skill_data->exchangeArrayFromForm($this->form->getData());
                $skill_data->ModifiedDt = Helper::getcurrentExpressionDate();
                $skill_data->ModifiedBy = $this->employeeId;
                $skill_data->Status = 'E';
                // echo '<pre>'; print_r($Openingdata); die;
                $this->repository->edit($skill_data, $id);
                $this->flashmessenger()->addMessage("Skill Successfully Edited!!!");
                return $this->redirect()->toRoute("skill");
            }
        }
        $detail = $this->repository->fetchById($id);
        $model = new SkillModel();
        $model->exchangeArrayFromDB($detail);
        // echo '<pre>'; print_r($model); die;
        $this->form->bind($model);
                
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail,
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('options');
        }
        $detail = $this->repository->fetchById($id);
        $model = new SkillModel();
        $model->exchangeArrayFromDB($detail);
        $model->DeletedBy = $this->employeeId;
        $model->DeletedDt = Helper::getcurrentExpressionDate();
            // echo '<pre>'; print_r($model); die;
        $this->repository->delete($model, $id);
        $this->flashmessenger()->addMessage("Skill Deleted Successfully");
        return $this->redirect()->toRoute('skill');
    }
}