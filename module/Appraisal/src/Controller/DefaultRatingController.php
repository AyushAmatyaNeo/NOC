<?php

namespace Appraisal\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Appraisal\Form\DefaultRatingForm;
use Appraisal\Model\DefaultRating;
use Appraisal\Model\Type;
use Appraisal\Repository\DefaultRatingRepository;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Repository\DesignationRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\PositionRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class DefaultRatingController extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new DefaultRatingRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        $getDesignationTitle = function($array) {
            if ($array != null) {
                $designationRepo = new DesignationRepository($this->adapter);
                $list = [];
                foreach ($array as $value) {
                    array_push($list, $designationRepo->fetchById($value)->getArrayCopy()['DESIGNATION_TITLE']);
                }
            }
            return $list;
        };
        $getPositionTitle = function($array) {
            $list = [];
            if ($array != null) {
                $positionRepo = new PositionRepository($this->adapter);
                foreach ($array as $value) {
                    array_push($list, $positionRepo->fetchById($value)->getArrayCopy()['POSITION_NAME']);
                }
            }
            return $list;
        };
        foreach ($result as $row) {
            $row['DESIGNATION_LIST'] = $getDesignationTitle(json_decode($row['DESIGNATION_IDS']));
            $row['POSITION_LIST'] = $getPositionTitle(json_decode($row['POSITION_IDS']));
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'defaultRating' => $list
        ]);
    }

    public function initializeForm() {
        $defaultRatingForm = new DefaultRatingForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($defaultRatingForm);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $defaultRating = new DefaultRating();
                $defaultRating->exchangeArrayFromForm($this->form->getData());
                $defaultRating->createdDate = Helper::getcurrentExpressionDate();
                $defaultRating->approvedDate = Helper::getcurrentExpressionDate();
                $defaultRating->createdBy = $this->employeeId;
                $defaultRating->companyId = $employeeDetail['COMPANY_ID'];
                $defaultRating->branchId = $employeeDetail['BRANCH_ID'];
                $defaultRating->id = ((int) Helper::getMaxId($this->adapter, DefaultRating::TABLE_NAME, DefaultRating::ID)) + 1;
                $defaultRating->status = 'E';
                $defaultRating->positionIds = json_encode($defaultRating->positionIds);
                $defaultRating->designationIds = json_encode($defaultRating->designationIds);
                $this->repository->add($defaultRating);
                $this->flashmessenger()->addMessage("Default Rating For Appraisal Type Successfully added!!!");
                return $this->redirect()->toRoute("defaultRating");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    "appraisalTypes" => EntityHelper::getTableKVListWithSortOption($this->adapter, Type::TABLE_NAME, Type::APPRAISAL_TYPE_ID, [Type::APPRAISAL_TYPE_EDESC], ["STATUS" => "E"], "APPRAISAL_TYPE_EDESC", "ASC", NULL, FALSE, TRUE),
                    "designations" => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => "E"], Designation::DESIGNATION_TITLE, "ASC"),
                    "positions" => EntityHelper::getTableKVListWithSortOption($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => "E"], Position::POSITION_NAME, "ASC"),
                    "form" => $this->form
        ]);
    }

    public function editAction() {
        $this->initializeForm();
        $id = $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $defaultRating = new DefaultRating();
        if (!$request->isPost()) {
            $defaultRating->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($defaultRating);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $defaultRating->exchangeArrayFromForm($this->form->getData());
                $defaultRating->modifiedDate = Helper::getcurrentExpressionDate();
                $defaultRating->modifiedBy = $this->employeeId;
                $defaultRating->positionIds = json_encode($defaultRating->positionIds);
                $defaultRating->designationIds = json_encode($defaultRating->designationIds);
                $this->repository->edit($defaultRating, $id);
                $this->flashmessenger()->addMessage("Default Rating For Appraisal Type Successfully Updated!!!");
                return $this->redirect()->toRoute("defaultRating");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    "appraisalTypes" => EntityHelper::getTableKVListWithSortOption($this->adapter, Type::TABLE_NAME, Type::APPRAISAL_TYPE_ID, [Type::APPRAISAL_TYPE_EDESC], ["STATUS" => "E"], "APPRAISAL_TYPE_EDESC", "ASC", NULL, FALSE, TRUE),
                    "designations" => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => "E"], Designation::DESIGNATION_TITLE, "ASC"),
                    "positions" => EntityHelper::getTableKVListWithSortOption($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => "E"], Position::POSITION_NAME, "ASC"),
                    "form" => $this->form,
                    'id' => $id
        ]);
    }

    public function deleteAction($id) {
        $id = $this->params()->fromRoute('id');
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Default Rating Successfully Deleted!!!");
        return $this->redirect()->toRoute("defaultRating");
    }

}
