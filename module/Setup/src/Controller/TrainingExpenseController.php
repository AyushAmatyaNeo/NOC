<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\TrainingExpenseForm;
use Setup\Model\Company;
use Setup\Model\Institute;
use Setup\Model\TrainingExpenseSetup;
use Setup\Repository\TrainingExpenseRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class TrainingExpenseController extends AbstractActionController {

    private $form;
    private $adapter;
    private $employeeId;
    private $repository;
    private $storageData;
    private $acl;


    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new TrainingExpenseRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TrainingExpenseForm();
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
        $postData = $request->getPost();

        $trainingList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_TRAINING_MASTER_SETUP", "TRAINING_ID", ["TRAINING_NAME"], ["STATUS" => 'E'], "TRAINING_ID", "ASC", "-");
        $expenseList = EntityHelper::getTableKVListWithSortOption($this->adapter, "NOC_TRAINING_EXPENSE_HEAD", "EXPENSE_HEAD_ID", ["EXPENSE_NAME"]);



        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $trainingModel = new TrainingExpenseSetup();
                $trainingModel->exchangeArrayFromForm($this->form->getData());
                // echo'<pre>'; print_r($trainingModel); die();
                $detailCount=count($postData['expenseHeadId']);
                $trainingModel->trainingId = $postData['trainingId'];
                for($i=0; $i<$detailCount; $i++){
                    $trainingModel->expenseId = ((int) Helper::getMaxId($this->adapter, TrainingExpenseSetup::TABLE_NAME, TrainingExpenseSetup::EXPENSE_ID)) + 1;
                    $trainingModel->expenseHeadId = $postData['expenseHeadId'][$i];
                    $trainingModel->status = 'E';
                    $trainingModel->amount = $postData['amount'][$i];
                    $trainingModel->description = $postData['description'][$i];
                    $trainingModel->createdBy = $this->employeeId;
                    $trainingModel->createdDt = Helper::getcurrentExpressionDate();
                    // echo "<pre>";print_r($trainingModel);die;
                    $this->repository->add($trainingModel);
                }
                $this->flashmessenger()->addMessage("Training Expense Setup Done!!!");
                return $this->redirect()->toRoute('trainingExpenseSetup');
            }
        }
        $expenseHeads = [];
        $i = 0;
        foreach($expenseList as $k => $v){
            $expenseHeads[$i]['EXPENSE_HEAD_ID'] = $k;
            $expenseHeads[$i]['EXPENSE_NAME'] = $v;
            $i++;
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'training' => $trainingList,
                    'expense' => $expenseHeads,
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }



    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        
        if ($id == 0) {

            return $this->redirect()->toRoute('trainingExpenseSetup');
        }

        $this->initializeForm();
        $request = $this->getRequest();
        $trainingList = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_TRAINING_MASTER_SETUP", "TRAINING_ID", ["TRAINING_NAME"], ["STATUS" => 'E'], "TRAINING_ID", "ASC", "-");
        $expenseList = EntityHelper::getTableKVListWithSortOption($this->adapter, "NOC_TRAINING_EXPENSE_HEAD", "EXPENSE_HEAD_ID", ["EXPENSE_NAME"]);



        $trainingModel = new TrainingExpenseSetup();
        if (!$request->isPost()) {
            $data = $this->repository->fetchById($id);
            $trainingModel->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($trainingModel);

        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {

                $trainingModel->exchangeArrayFromForm($this->form->getData());
                $trainingModel->modifiedDate = Helper::getcurrentExpressionDate();
                $trainingModel->modifiedBy = $this->employeeId;
                $this->repository->edit($trainingModel, $id);
                $this->flashmessenger()->addMessage("Expense Successfully Updated!!!");
                return $this->redirect()->toRoute("trainingExpenseSetup");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'training' => $trainingList,
                    'expense' => $expenseList,
                    'selectedTraining'=>$data['TRAINING_ID'],
                    'selectedExpense' =>$data['EXPENSE_HEAD_ID'],

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
            return $this->redirect()->toRoute('trainingExpenseSetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Training Expense Successfully Deleted!!!");
        return $this->redirect()->toRoute('trainingExpenseSetup');
    }

}
