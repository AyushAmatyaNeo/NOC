<?php

namespace DartaChalani\Controller;

use Application\Custom\CustomViewModel;
use Application\Controller\HrisController;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use DartaChalani\Form\OfficesForm;
use DartaChalani\Repository\OfficesRepository;
use DartaChalani\Model\Offices;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element;
use Exception;
use Zend\View\Model\JsonModel;

class OfficesController extends HrisController
{

    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        parent::__construct($adapter, $storage);
        $this->initializeForm(OfficesForm::class);
        $this->initializeRepository(OfficesRepository::class);
    }

    public function indexAction()
    {
        $officeList = EntityHelper::getTableKVListWithSortOption($this->adapter, 'DC_OFFICES', 'OFFICE_ID', ['OFFICE_EDESC'], "STATUS='E'", "OFFICE_EDESC", "ASC", null, true);
        // $depList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'], "DEPARTMENT_ID", "ASC", "-");
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll(); 
                $officesList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $officesList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return $this->stickFlashMessagesTo([
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'acl' => $this->acl,
            'employeeDetail' => $this->storageData['employee_detail'],
            'preference' => $this->preference,
            'officeList' => $officeList,
            //'department' => $depList,
    ]);
    }

    public function addAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $offices = new Offices();
                $offices->exchangeArrayFromForm($this->form->getData());  //gets info from the form
                $offices->officeId = ((int) Helper::getMaxId($this->adapter, "DC_OFFICES", "OFFICE_ID")) + 1;
                $offices->createdDt = Helper::getcurrentExpressionDate();
                $offices->createdBy = $this->employeeId;
                $offices->responseFlag = $postedData['responseFlag'];
                $offices->status = 'E';

                // echo"<pre>";print_r($offices);die;
                $this->repository->add($offices);

                $this->flashmessenger()->addMessage("Office Successfully Added!!!");
                return $this->redirect()->toRoute("offices");
            }
        }


        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'customRenderer' => Helper::renderCustomView(),
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $request = $this->getRequest();

        $offices = new Offices();
        if (!$request->isPost()) {
            $offices->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($offices);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $offices->exchangeArrayFromForm($this->form->getData());
                $offices->modifiedDt = Helper::getcurrentExpressionDate();
                $offices->modifiedBy = $this->employeeId;
                $offices->status = 'E';
                $this->repository->edit($offices, $id);
                $this->flashmessenger()->addMessage("Office Successfully Updated!!!");
                return $this->redirect()->toRoute("offices");
            }
        }
        
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('offices');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Office Successfully Deleted!!!");
        return $this->redirect()->toRoute('offices');
    }

    public function getSearchAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // print_r($data); die;
            $recordList = $this->repository->getSearchResults($data);
            // print_r($recordList); die;
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "message" => null
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function fileUploadAction() {
        $request = $this->getRequest();
        $responseData = [];
        $files = $request->getFiles()->toArray();
        try {
            if (sizeof($files) > 0) {
                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/dartachalani_docs/" . $newFileName);
                if (!$success) {
                    throw new Exception("Upload unsuccessful.");
                }
                $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
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

    public function pushFileLinkAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            return new JsonModel(['success' => true, 'data' => $data, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}