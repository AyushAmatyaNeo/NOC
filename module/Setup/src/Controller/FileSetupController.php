<?php

namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\FileSetupForm;
use Setup\Form\FileTypeForm;
use Setup\Model\FileSetup;
use Setup\Model\FileType;
use Setup\Repository\FileSetupRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class FileSetupController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(FileSetupRepo::class);
        $this->initializeForm(FileSetupForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $fileList = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $fileList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $fileSetupModel = new FileSetup();
                $fileSetupModel->exchangeArrayFromForm($this->form->getData());
                $fileSetupModel->fileId = ((int) Helper::getMaxId($this->adapter, "HRIS_EMPLOYEE_FILE_SETUP", "FILE_ID")) + 1;
                $fileSetupModel->createdDt = Helper::getcurrentExpressionDate();
                $fileSetupModel->createdBy = $this->employeeId;
                $fileSetupModel->status = 'E';
                $this->repository->add($fileSetupModel);
                $this->flashmessenger()->addMessage("File Type Successfully added.");
                return $this->redirect()->toRoute("fileSetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'messages' => $this->flashmessenger()->getMessages(),
                'fileTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_FILE_TYPE", "FILETYPE_CODE", ["NAME"], null ,"FILETYPE_CODE", "ASC", "-", true, true, null)
            ])
        );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('fileSetup');
        }
        $request = $this->getRequest();

        $fileSetupModel = new FileSetup();
        if (!$request->isPost()) {
            $fileSetupModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($fileSetupModel);
        } else {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $fileSetupModel->exchangeArrayFromForm($this->form->getData());
//                print_r($fileSetupModel); die;
                $fileSetupModel->modifiedDt = Helper::getcurrentExpressionDate();
                $fileSetupModel->modifiedBy = $this->employeeId;
                $this->repository->edit($fileSetupModel, $id);
                $this->flashmessenger()->addMessage("fileType Successfully Updated!!!");
                return $this->redirect()->toRoute("fileSetup");
            }
        }
        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'id' => $id,
                'fileTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_FILE_TYPE", "FILETYPE_CODE", ["NAME"], null ,"FILETYPE_CODE", "ASC", "-", true, true, null)
            ]
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("File Type Successfully Deleted!!!");
        return $this->redirect()->toRoute('fileSetup');
    }

}

?>