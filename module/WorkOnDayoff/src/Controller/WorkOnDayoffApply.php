<?php

namespace WorkOnDayoff\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use SelfService\Form\WorkOnDayoffForm;
use SelfService\Model\WorkOnDayoff;
use SelfService\Repository\WorkOnDayoffRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Controller\HrisController;

class WorkOnDayoffApply extends HrisController {

//    private $form;
//    private $adapter;
//    private $workOnDayoffRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->adapter = $adapter;
        $this->workOnDayoffRepository = new WorkOnDayoffRepository($adapter);
    }

//    public function initializeForm() {
//        $builder = new AnnotationBuilder();
//        $form = new WorkOnDayoffForm();
//        $this->form = $builder->createForm($form);
//    }

    public function indexAction() {
        return $this->redirect()->toRoute("workOnDayoffStatus");
    }

    public function addAction() {
        $this->initializeForm(WorkOnDayoffForm::class);
        $request = $this->getRequest();

        $model = new WorkOnDayoff();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                
                $model->id = ((int) Helper::getMaxId($this->adapter, WorkOnDayoff::TABLE_NAME, WorkOnDayoff::ID)) + 1;
                $model->requestedDate = Helper::getExpressionDate($model->requestedDate);
                $model->fromDate = Helper::getExpressionDate($model->fromDate);
                $model->toDate = Helper::getExpressionDate($model->toDate);
//                $model->status = 'RQ';
                $model->status = ($postData['applyStatus'] == 'AP') ? 'AP' : 'RQ';
                $this->workOnDayoffRepository->add($model);
                $this->flashmessenger()->addMessage("Work on Day-off Request Successfully added!!!");

                return $this->redirect()->toRoute("workOnDayoffStatus");
            }
        }
        
        $applyOptionValues = [
            'RQ' => 'Pending',
            'AP' => 'Approved'
        ];
        $applyOption = $this->getSelectElement(['name' => 'applyStatus', 'id' => 'applyStatus', 'class' => 'form-control', 'label' => 'Type'], $applyOptionValues);

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'applyOption' => $applyOption,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE","FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", "-", false, true, $this->employeeId),
        ]);
    }

}
