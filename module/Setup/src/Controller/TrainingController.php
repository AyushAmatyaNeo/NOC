<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\TrainingForm;
use Setup\Model\Company;
use Setup\Model\Institute;
use Setup\Model\Training;
use Setup\Repository\TrainingRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class TrainingController extends AbstractActionController {

    private $form;
    private $adapter;
    private $employeeId;
    private $repository;
    private $storageData;
    private $acl;

    const TRAINING_TYPES = [
        'CP' => 'Personal',
        'CC' => 'Company Contribution'
    ];

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new TrainingRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TrainingForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $trainingList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $trainingList, 'error' => '']);
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
            $trainingModel = new Training();
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $trainingModel->exchangeArrayFromForm($this->form->getData());
                $trainingModel->trainingId = ((int) Helper::getMaxId($this->adapter, Training::TABLE_NAME, Training::TRAINING_ID)) + 1;
                $trainingModel->createdBy = $this->employeeId;
                $trainingModel->createdDate = Helper::getcurrentExpressionDate();
                $trainingModel->status = 'E';
                $trainingModel->startDate = Helper::getExpressionDate($trainingModel->startDate);
                $trainingModel->endDate = Helper::getExpressionDate($trainingModel->endDate);
                // echo"<pre>";print_r($trainingModel);die;
                $this->repository->add($trainingModel);
                $this->flashmessenger()->addMessage("Training Successfully added!!!");
                return $this->redirect()->toRoute('training');
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'instituteNameList' => EntityHelper::getTableKVListWithSortOption($this->adapter, Institute::TABLE_NAME, Institute::INSTITUTE_ID, [Institute::INSTITUTE_NAME], [Institute::STATUS => 'E'], Institute::INSTITUTE_NAME, "ASC", null, [null => '---'], true),
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", null, [null => '---'], true),
                    'trainingTypeList' => self::TRAINING_TYPES,
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('training');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $trainingModel = new Training();
        if (!$request->isPost()) {
            $trainingModel->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($trainingModel);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $trainingModel->exchangeArrayFromForm($this->form->getData());
                $trainingModel->modifiedDate = Helper::getcurrentExpressionDate();
                $trainingModel->modifiedBy = $this->employeeId;
                $this->repository->edit($trainingModel, $id);
                $this->flashmessenger()->addMessage("Training Successfully Updated!!!");
                return $this->redirect()->toRoute("training");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'instituteNameList' => EntityHelper::getTableKVListWithSortOption($this->adapter, Institute::TABLE_NAME, Institute::INSTITUTE_ID, [Institute::INSTITUTE_NAME], [Institute::STATUS => 'E'], Institute::INSTITUTE_NAME, "ASC", null, [null => '---'], true),
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", null, [null => '---'], true),
                    'trainingTypeList' => self::TRAINING_TYPES,
                    'customRenderer' => Helper::renderCustomView()
                        ]
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('training');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Training Successfully Deleted!!!");
        return $this->redirect()->toRoute('training');
    }

}
