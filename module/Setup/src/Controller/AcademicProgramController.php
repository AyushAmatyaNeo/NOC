<?php
namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\AcademicProgramForm;
use Setup\Model\AcademicProgram;
use Setup\Repository\AcademicProgramRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AcademicProgramController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new AcademicProgramRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $form = new AcademicProgramForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($form);
        }
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $academicProgramList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $academicProgramList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {
        ACLHelper::checkFor(ACLHelper:: ADD, $this->acl, $this);
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $academicProgram = new AcademicProgram();
                $academicProgram->exchangeArrayFromForm($this->form->getData());
                $academicProgram->academicProgramId = ((int) Helper::getMaxId($this->adapter, AcademicProgram::TABLE_NAME, AcademicProgram::ACADEMIC_PROGRAM_ID)) + 1;
                $academicProgram->createdDt = Helper::getcurrentExpressionDate();
                $academicProgram->createdBy = $this->employeeId;
                $academicProgram->status = 'E';
                $this->repository->add($academicProgram);

                $this->flashmessenger()->addMessage("Academic Program Successfully added!!!");
                return $this->redirect()->toRoute("academicProgram");
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
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('academicProgram');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $academicProgram = new AcademicProgram();
        if (!$request->isPost()) {
            $academicProgram->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($academicProgram);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $academicProgram->exchangeArrayFromForm($this->form->getData());
                $academicProgram->modifiedDt = Helper::getcurrentExpressionDate();
                $academicProgram->modifiedBy = $this->employeeId;
                $this->repository->edit($academicProgram, $id);
                $this->flashmessenger()->addMessage("Academic Program Successfully Updated!!!");
                return $this->redirect()->toRoute("academicProgram");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, ['form' => $this->form, 'id' => $id]
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('academicProgram');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Academic Program Successfully Deleted!!!");
        return $this->redirect()->toRoute('academicProgram');
    }

}
