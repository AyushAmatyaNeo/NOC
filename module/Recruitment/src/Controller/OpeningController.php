<?php
namespace Recruitment\Controller;
use Application\Controller\HrisController;
use Recruitment\Model\OpeningVacancy;
use Recruitment\Form\OpeningForm;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\Helper;
use Zend\View\Model\ViewModel;
use Recruitment\Repository\OpeningRepository;
use Application\Helper\EntityHelper;
use Zend\View\Model\JsonModel;
use Exception;

class OpeningController extends HrisController
{
    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(OpeningRepository::class);
        $this->initializeForm(OpeningForm::class);
        
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
            // 'Gender' => $GenderSE
            'opening' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS', ['OPENING_ID','OPENING_NO'], ['STATUS' => 'E'])
            
        ]);
    }
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) 
            {
                // echo '<pre>'; print_r($request->getPost()); die();
                $opening_data = new OpeningVacancy();
                $opening_data->exchangeArrayFromForm($this->form->getData());
                $opening_data->OpeningId = ((int) Helper::getMaxId($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID)) + 1;
                $opening_data->CreatedBy = $this->employeeId;
                $opening_data->Start_dt = Helper::getExpressionDate($opening_data->Start_dt);
                $opening_data->End_dt = Helper::getExpressionDate($opening_data->End_dt);
                $opening_data->Extended_dt = Helper::getExpressionDate($opening_data->Extended_dt);
                $opening_data->CreatedDt = Helper::getcurrentExpressionDate();
                $opening_data->Instruction_Edesc = strip_tags(html_entity_decode($request->getPost('Instruction_Edesc')));
                $opening_data->Instruction_Ndesc = strip_tags(html_entity_decode($request->getPost('Instruction_Ndesc')));
                // $opening_data->CreatedDt = date('Y-m-d');  // HANA accept this format
                $this->repository->add($opening_data);
                $this->flashmessenger()->addMessage("Opening Data Successfully added!!!");
                return $this->redirect()->toRoute("opening");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                    $this, [
                        'customRenderer' => Helper::renderCustomView(),
                        'form' => $this->form,
                        'OpeningList' => EntityHelper::getTableList($this->adapter, OpeningVacancy::TABLE_NAME, [OpeningVacancy::OPENING_ID]),
                        'messages' => $this->flashmessenger()->getMessages()
                    ]
                )
        );
        
    }
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("opening");
        }

        $detail = $this->repository->fetchById($id);
        $documents = $this->repository->fetchDocuments($id);
        $model = new OpeningVacancy();
        $model->exchangeArrayFromDB($detail);
        // echo '<pre>'; print_r($detail); die();

        $this->form->bind($model);
        $instructions = strip_tags($detail['INSTRUCTION_EDESC']);
        $instruction_ndesc =  strip_tags($detail['INSTRUCTION_NDESC']);


                
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail,
                    'id' => $id,
                    'documents' => $documents,
                    'instructions' => $instructions,
                    'instruction_ndesc'=> $instruction_ndesc
        ]);
    }
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute('opening');
        }
        $request = $this->getRequest();
        if ($request->isPost()) 
        {
            $Openingdata = new OpeningVacancy();
            $postedData = $request->getPost();
            // echo '<pre>'; print_r($postedData); die;
            $this->form->setData($postedData);
            if ($this->form->isValid()) {  
                // echo 'Valid'; die();              
                $Openingdata->exchangeArrayFromForm($this->form->getData());
                $Openingdata->ModifiedDt = Helper::getcurrentExpressionDate();
                $Openingdata->ModifiedBy = $this->employeeId;
                $Openingdata->Start_dt = Helper::getExpressionDate($Openingdata->Start_dt);
                $Openingdata->End_dt = Helper::getExpressionDate($Openingdata->End_dt);
                $Openingdata->Extended_dt = Helper::getExpressionDate($Openingdata->Extended_dt);
                // echo '<pre>'; print_r($Openingdata); die;
                $this->repository->edit($Openingdata, $id);
                $this->flashmessenger()->addMessage("Opening Successfully Edited!!!");
                return $this->redirect()->toRoute("opening");
            }
        }
        $detail = $this->repository->fetchById($id);
        $model = new OpeningVacancy();
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);
                
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'detail' => $detail,
                    'id' => $id,
        ]);
    }
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('vacancy');
        }
        $detail = $this->repository->fetchById($id);
        $model = new OpeningVacancy();
        // echo '<pre>'; print_r($detail); die;
        $model->exchangeArrayFromDB($detail);
        $model->DeletedBy = $this->employeeId;
        $model->DeletedDt = Helper::getcurrentExpressionDate();
            // echo '<pre>'; print_r($model); die;
        $this->repository->delete($model, $id);
        $this->flashmessenger()->addMessage("Opening Deleted Successfully");
        return $this->redirect()->toRoute('opening');
    }
    // File Upload Section------------------------------------------------------------
    public function fileUploadAction()
    {
        $request = $this->getRequest();
        $responseData = [];
        $files = $request->getFiles()->toArray();
        try {
            if (sizeof($files) > 0) {
                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/noc_documents/" . $newFileName);
                $status  = 'E';
                if (!$success) {
                    throw new Exception("Upload unsuccessful.");
                }
                $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext, $status]];
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return new JsonModel($responseData);
    }
    public function fileEditAction()
    {
        $request = $this->getRequest();
        $Vid = (int) $this->params()->fromRoute('id');
        $responseData = [];
        $files = $request->getFiles()->toArray();
        // var_dump($id); die();
        try {
            if (sizeof($files) > 0) {

                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/noc_documents/" . $newFileName);
                if (!$success) {
                    throw new Exception("Upload unsuccessful.");
                }
                $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext, "Vid" => $Vid]];
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return new JsonModel($responseData);
    }
    public function pushVacancyFileLinkAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // print_r($data); die();
            $returnData = $this->repository->pushFileLink($data);
            // print_r($returnData); die();
            return new JsonModel(['success' => true, 'data' => $returnData[0], 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
    public function pullVacancyFileLinkAction()
    {
        try {
            $Vid = (int) $this->params()->fromRoute('id');
            $request = $this->getRequest();
            $returnData = $this->repository->pullFileLink($Vid);
            // if($re)
            $fileId = $returnData['FILE_ID'];
            $fileName = $returnData['FILE_NAME'];
            $fileNameDir  = $returnData['FILE_IN_DIR_NAME'];
            $responseData = ["success" => true, "data" => ["fileId" => $fileId, "fileName" => $fileName, "fileNameDir" => $fileNameDir]];
            // print_r($returnData); die();
            return new JsonModel($responseData);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
    public function deleteFileByNameAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // echo '<pre>'; print_r($data); die();
            $this->repository->deleteFileByName($data['name']);
            return new JsonModel(['success' => true, 'data' => null, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }
    public function updateVacancyFileLinkAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // print_r($data); die();
            $returnData = $this->repository->updateFileLink($data);
            // print_r($returnData); die();
            return new JsonModel(['success' => true, 'data' => $returnData[0], 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
