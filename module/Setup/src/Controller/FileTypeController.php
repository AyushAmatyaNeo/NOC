<?php

namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\FileTypeForm;
use Setup\Model\FileType;
use Setup\Repository\FileTypeRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class FileTypeController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(FileTypeRepo::class);
        $this->initializeForm(FileTypeForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $companyList = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $companyList, 'error' => '']);
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
                $fileTypeModel = new FileType();
                $fileTypeModel->exchangeArrayFromForm($this->form->getData());
                $fileTypeModel->createdDt = Helper::getcurrentExpressionDate();
                $fileTypeModel->status = 'E';
                $fileTypeModel->filetypeCode = EntityHelper::rawQueryResult($this->adapter, "select lpad(max(FILETYPE_CODE)+1, 3, '0') as MAX  from Hris_File_Type")->current()['MAX'];
                $this->repository->add($fileTypeModel);
                $this->flashmessenger()->addMessage("File Type Successfully added.");
                return $this->redirect()->toRoute("fileType");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages()
                        ]
                )
        );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('company');
        }
        $request = $this->getRequest();

        $fileTypeModel = new FileType();
        if (!$request->isPost()) {
            $fileTypeModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($fileTypeModel);
        } else {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $company->exchangeArrayFromForm($this->form->getData());
                $company->modifiedDt = Helper::getcurrentExpressionDate();
                $company->modifiedBy = $this->employeeId;
                $company->logo = $postedData['logo'];
                $this->repository->edit($company, $id);
                $this->flashmessenger()->addMessage("fileType Successfully Updated!!!");
                return $this->redirect()->toRoute("fileType");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id
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
        return $this->redirect()->toRoute('fileType');
    }

}

?>