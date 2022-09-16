<?php

namespace Asset\Controller;

use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Asset\Form\IssueForm;
use Asset\Form\SetupForm;
use Asset\Model\Group;
use Asset\Model\Setup;
use Asset\Repository\IssueRepository;
use Asset\Repository\SetupRepository;
use Exception;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeFile;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class SetupController extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new SetupRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $form = new SetupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {

        $issueRepo = new IssueRepository($this->adapter);
        $asset = $issueRepo->fetchallIssuableAsset();
        $issueForm = new IssueForm();
        $builder = new AnnotationBuilder();
        $issueFm = $builder->createForm($issueForm);


        $result = $this->repository->fetchAll();
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'setup' => $list,
                    'form' => $issueFm,
                    'asset' => $asset['B'],
                    'employee' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", FALSE, TRUE),
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        $request = $this->getRequest();
        $imageData = null;
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $setup = new Setup();
                $setup->exchangeArrayFromForm($this->form->getData());
                $setup->assetImage = $postedData['logo'];
                $setup->createdBy = $this->employeeId;
                $setup->createdDate = Helper::getcurrentExpressionDate();
                $setup->approvedDate = Helper::getcurrentExpressionDate();
                $setup->companyId = $employeeDetail['COMPANY_ID'];
                $setup->branchId = $employeeDetail['BRANCH_ID'];
                $setup->assetId = ((int) Helper::getMaxId($this->adapter, $setup::TABLE_NAME, $setup::ASSET_ID)) + 1;
                $setup->status = 'E';
                $setup->purchaseDate = Helper::getExpressionDate($setup->purchaseDate);
                $setup->expiaryDate = Helper::getExpressionDate($setup->expiaryDate);
                $setup->quantityBalance = $setup->quantity;
                $this->repository->add($setup);
                $this->flashmessenger()->addMessage("Asset Successfully added!!!");
                return $this->redirect()->toRoute("assetSetup");
            } else {
                $imageData = $this->getFileInfo($this->adapter, $postedData['logo']);
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
//            'group'=>$groupList
                    'group' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Group::TABLE_NAME, Group::ASSET_GROUP_ID, [Group::ASSET_GROUP_EDESC], ["STATUS" => "E"], Group::ASSET_GROUP_EDESC, "ASC", NULL, FALSE, TRUE),
                    'imageData' => $imageData
        ]);
    }

    private function getFileInfo(AdapterInterface $adapter, $fileId) {
        $fileRepo = new EmployeeFile($adapter);
        $fileDetail = $fileRepo->fetchById($fileId);

        if ($fileDetail == null) {
            $imageData = [
                'fileCode' => null,
                'fileName' => null,
                'oldFileName' => null
            ];
        } else {
            $imageData = [
                'fileCode' => $fileDetail['FILE_CODE'],
                'oldFileName' => $fileDetail['FILE_NAME'],
                'fileName' => $fileDetail['FILE_PATH']
            ];
        }
        return $imageData;
    }

    public function editAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('assetSetup');
        }
        $this->initializeForm();
        $request = $this->getRequest();
        $setup = new Setup();
        if (!$request->isPost()) {
            $setup->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($setup);
        } else {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $setup->exchangeArrayFromForm($this->form->getData());
                $setup->assetImage = $postedData['logo'];
                $setup->modifiedDate = Helper::getcurrentExpressionDate();
                $setup->modifiedBy = $this->employeeId;
                $setup->quantityBalance = $setup->quantity;
                $setup->purchaseDate = Helper::getExpressionDate($setup->purchaseDate);
                

                $this->repository->edit($setup, $id);
                $this->flashmessenger()->addMessage("Asset Setup Successfully Updated!!!");
                return $this->redirect()->toRoute("assetSetup");
            }
        }

        $imageData = $this->getFileInfo($this->adapter, $setup->assetImage);
//        print_r($imageData);
//        die();

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'group' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Group::TABLE_NAME, Group::ASSET_GROUP_ID, [Group::ASSET_GROUP_EDESC], ["STATUS" => "E"], Group::ASSET_GROUP_EDESC, "ASC", NULL, FALSE, TRUE),
                    'id' => $id,
                    'imageData' => $imageData
        ]);
    }

    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('assetSetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Asset Setup Successfully Deleted!!!");
        return $this->redirect()->toRoute("assetSetup");
    }

    public function pullAssetBalanceAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $assetId = $data['assetId'];

            $assetIssueRepo = new IssueRepository($this->adapter);
            $assetRemQuantity = $assetIssueRepo->fetchAssetRemBalance($assetId);


            return new JsonModel(['success' => true, 'data' => $assetRemQuantity['QUANTITY_BALANCE'], 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
