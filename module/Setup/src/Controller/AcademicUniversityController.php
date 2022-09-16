<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/16
 * Time: 4:37 PM
 */

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\AcademicUniversityForm;
use Setup\Model\AcademicUniversity;
use Setup\Repository\AcademicUniversityRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AcademicUniversityController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new AcademicUniversityRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $form = new AcademicUniversityForm();
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
                $universityList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $universityList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {
        ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this);
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $academicUniversity = new AcademicUniversity();
                $academicUniversity->exchangeArrayFromForm($this->form->getData());
                $academicUniversity->academicUniversityId = ((int) Helper::getMaxId($this->adapter, AcademicUniversity::TABLE_NAME, AcademicUniversity::ACADEMIC_UNIVERSITY_ID)) + 1;
                $academicUniversity->createdDt = Helper::getcurrentExpressionDate();
                $academicUniversity->createdBy = $this->employeeId;
                $academicUniversity->status = 'E';
                $this->repository->add($academicUniversity);

                $this->flashmessenger()->addMessage("Academic University Successfully added!!!");
                return $this->redirect()->toRoute("academicUniversity");
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
            return $this->redirect()->toRoute('academicUniversity');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $academicUniversity = new AcademicUniversity();
        if (!$request->isPost()) {
            $academicUniversity->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($academicUniversity);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $academicUniversity->exchangeArrayFromForm($this->form->getData());
                $academicUniversity->modifiedDt = Helper::getcurrentExpressionDate();
                $academicUniversity->modifiedBy = $this->employeeId;
                $this->repository->edit($academicUniversity, $id);
                $this->flashmessenger()->addMessage("Academic University Successfully Updated!!!");
                return $this->redirect()->toRoute("academicUniversity");
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
            return $this->redirect()->toRoute('academicUniversity');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Academic University Successfully Deleted!!!");
        return $this->redirect()->toRoute('academicUniversity');
    }

}
