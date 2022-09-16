<?php
namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Recruitment\Model\OptionsModel;
use Recruitment\Form\OptionsForm;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\Helper;
use Zend\View\Model\ViewModel;
use Recruitment\Repository\OptionsRepository;
use Application\Helper\EntityHelper;
use Zend\View\Model\JsonModel;
use Exception;

class OptionsController extends HrisController
{
    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(OptionsRepository::class);
        $this->initializeForm(OptionsForm::class);
        
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
                $opening_data = new OptionsModel();
                $opening_data->exchangeArrayFromForm($this->form->getData());
                $opening_data->OptionId = ((int) Helper::getMaxId($this->adapter, OptionsModel::TABLE_NAME, OptionsModel::OPTION_ID)) + 1;
                $opening_data->CreatedBy = $this->employeeId;
                $opening_data->CreatedDt = Helper::getcurrentExpressionDate();
                $opening_data->Status = 'E'; 
                // echo '<pre>'; print_r($opening_data); die();               
                $this->repository->add($opening_data);
                $this->flashmessenger()->addMessage("Options Data Successfully added!!!");
                return $this->redirect()->toRoute("options");
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
            $Openingdata = new OptionsModel();
            $postedData = $request->getPost();
            // echo '<pre>'; print_r($postedData); die;
            $this->form->setData($postedData);
            if ($this->form->isValid()) {  
                // echo 'Valid'; die();              
                $Openingdata->exchangeArrayFromForm($this->form->getData());
                $Openingdata->ModifiedDt = Helper::getcurrentExpressionDate();
                $Openingdata->ModifiedBy = $this->employeeId;
                $Openingdata->Status = 'E';
                // echo '<pre>'; print_r($Openingdata); die;
                $this->repository->edit($Openingdata, $id);
                $this->flashmessenger()->addMessage("Options Successfully Edited!!!");
                return $this->redirect()->toRoute("options");
            }
        }
        $detail = $this->repository->fetchById($id);
        $model = new OptionsModel();
        $model->exchangeArrayFromDB($detail);
        // echo '<pre>'; print_r($model); die;
        $this->form->bind($model);
                
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail
        ]);
    }
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('options');
        }
        $detail = $this->repository->fetchById($id);
        $model = new OptionsModel();
        // echo '<pre>'; print_r($detail); die;
        $model->exchangeArrayFromDB($detail);
        $model->DeletedBy = $this->employeeId;
        $model->DeletedDt = Helper::getcurrentExpressionDate();
            // echo '<pre>'; print_r($model); die;
        $this->repository->delete($model, $id);
        $this->flashmessenger()->addMessage("Options Deleted Successfully");
        return $this->redirect()->toRoute('options');
    }
}