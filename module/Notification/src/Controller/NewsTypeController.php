<?php

namespace Notification\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Exception;
use Notification\Form\NewsTypeForm;
use Notification\Model\NewsTypeModel;
use Notification\Repository\NewsTypeRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class NewsTypeController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(NewsTypeRepository::class);
        $this->initializeForm(NewsTypeForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $newTypeModel = new NewsTypeModel();
                $newTypeModel->exchangeArrayFromForm($this->form->getData());
                $newTypeModel->status = 'E';
                $newTypeModel->createdBy = $this->employeeId;
                $newTypeModel->newsTypeId = (int) Helper::getMaxId($this->adapter, NewsTypeModel::TABLE_NAME, NewsTypeModel::NEWS_TYPE_ID) + 1;
                $this->repository->add($newTypeModel);
                $this->flashmessenger()->addMessage("News Type Successfully added!!!");
                return $this->redirect()->toRoute("news-type");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeId' => $this->employeeId,
                    'customRenderer' => Helper::renderCustomView(),
        ]);
    }
    
    public function editAction(){
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('news-type');
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $newTypeModel = new NewsTypeModel();
                $newTypeModel->exchangeArrayFromForm($this->form->getData());
                $newTypeModel->modifiedBy=$this->employeeId;
                $newTypeModel->modifiedDt = Helper::getcurrentExpressionDate();
                $this->repository->edit($newTypeModel, $id);
                $this->flashmessenger()->addMessage("News Type Successfully edited!!!");
                return $this->redirect()->toRoute("news-type");
            }
        }
        
        $newTypeModel = new NewsTypeModel();
        $detail = $this->repository->fetchById($id)->getArrayCopy();
        $newTypeModel->exchangeArrayFromDB($detail);
        $this->form->bind($newTypeModel);
        
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'customRenderer' => Helper::renderCustomView(),
            "id" => $id
        ]);
    }
    
    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('news-type');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("News Type Successfully Deleted!!!");
        return $this->redirect()->toRoute("news-type");
    }

}
