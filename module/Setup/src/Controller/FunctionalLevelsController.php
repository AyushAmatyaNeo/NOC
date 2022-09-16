<?php

namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\FunctionalLevelsForm;
use Setup\Model\FunctionalLevels;
use Setup\Repository\FunctionalLevelsRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class FunctionalLevelsController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(FunctionalLevelsRepository::class);
        $this->initializeForm(FunctionalLevelsForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $functionalList = iterator_to_array($result, FALSE);
                return new JsonModel(['success' => true, 'data' => $functionalList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo([
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $functionalLevels = new FunctionalLevels();
                $functionalLevels->exchangeArrayFromForm($this->form->getData());
                $functionalLevels->createdDt = Helper::getcurrentExpressionDate();
                $functionalLevels->createdBy = $this->employeeId;
                $functionalLevels->functionalLevelId = ((int) Helper::getMaxId($this->adapter, "HRIS_FUNCTIONAL_LEVELS", "FUNCTIONAL_LEVEL_ID")) + 1;
                $functionalLevels->status = 'E';

                $this->repository->add($functionalLevels);
                $this->flashmessenger()->addMessage("Functional Levels Successfully added.");
                return $this->redirect()->toRoute("functionalLevels");
            }
        }

        return $this->stickFlashMessagesTo([
                    'form' => $this->form,
                    'customRender' => Helper::renderCustomView(),]);
    }

    public function editAction() {

        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('functionalLevels');
        }
        //   $this->prepareForm($id);


        $request = $this->getRequest();
        $functionalLevels = new FunctionalLevels();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {

                $functionalLevels->exchangeArrayFromForm($this->form->getData());
                $functionalLevels->modifiedDt = Helper::getcurrentExpressionDate();
                $functionalLevels->modifiedBy = $this->employeeId;

                $this->repository->edit($functionalLevels, $id);

                $this->flashmessenger()->addMessage("Functional Levels Successfully Updated.");
                return $this->redirect()->toRoute("functionalLevels");
            }
        }
        $fetchData = $this->repository->fetchById($id)->getArrayCopy();
        $functionalLevels->exchangeArrayFromDB($fetchData);
        $this->form->bind($functionalLevels);
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRender' => Helper::renderCustomView(),
                    'id' => $id
                ])
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('functionalLevels');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Functional Levels Successfully Deleted!!!");
        return $this->redirect()->toRoute('functionalLevels');
    }

}
