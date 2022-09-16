<?php
namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\FunctionalTypesForm;
use Setup\Model\FunctionalTypes;
use Setup\Repository\FunctionalTypesRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;




class FunctionalTypesController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(FunctionalTypesRepository::class);
        $this->initializeForm(FunctionalTypesForm::class);
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
                $functionalTypes = new FunctionalTypes();
                $functionalTypes->exchangeArrayFromForm($this->form->getData());
                $functionalTypes->createdDt = Helper::getcurrentExpressionDate();
                $functionalTypes->createdBy = $this->employeeId;
                $functionalTypes->functionalTypeId = ((int) Helper::getMaxId($this->adapter, "HRIS_FUNCTIONAL_TYPES", "FUNCTIONAL_TYPE_ID")) + 1;
                $functionalTypes->status = 'E';
                
                $this->repository->add($functionalTypes);
                $this->flashmessenger()->addMessage("Functional Types Successfully added.");
                return $this->redirect()->toRoute("functionalTypes");
            }
        }

        return $this->stickFlashMessagesTo([
                    'form' => $this->form,
                    'customRender' => Helper::renderCustomView(),]);
    }

    public function editAction() {
        
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('functionalTypes');
        }
     //   $this->prepareForm($id);
        
        
        $request = $this->getRequest();
        $functionalTypes = new FunctionalTypes();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {

                $functionalTypes->exchangeArrayFromForm($this->form->getData());
                $functionalTypes->modifiedDt = Helper::getcurrentExpressionDate();
                $functionalTypes->modifiedBy = $this->employeeId;
                
                $this->repository->edit($functionalTypes, $id);

                $this->flashmessenger()->addMessage("Functional Types Successfully Updated.");
                return $this->redirect()->toRoute("functionalTypes");
            }
        }
        $fetchData = $this->repository->fetchById($id)->getArrayCopy();
        $functionalTypes->exchangeArrayFromDB($fetchData);
        $this->form->bind($functionalTypes);
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
            return $this->redirect()->toRoute('functionalTypes');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Functional Types Successfully Deleted!!!");
        return $this->redirect()->toRoute('functionalTypes');
    }

}
