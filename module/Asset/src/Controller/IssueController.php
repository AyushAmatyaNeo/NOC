<?php

namespace Asset\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Asset\Form\IssueForm;
use Asset\Model\Group;
use Asset\Model\Issue;
use Asset\Model\Setup;
use Asset\Repository\IssueRepository;
use Asset\Repository\SetupRepository;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeFile;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class IssueController extends AbstractActionController {

    private $adapter;
    private $form;
    private $employeeId;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new IssueRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $form = new IssueForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {

        //form element for asst type

        $assetTypeFormElement = new Select();
        $assetTypeFormElement->setName("asset");
        $assetTypes = ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Group::TABLE_NAME, Group::ASSET_GROUP_ID, [Group::ASSET_GROUP_EDESC], ["STATUS" => "E"], Group::ASSET_GROUP_EDESC, "ASC", NULL, FALSE, TRUE);
        $assetType1 = [-1 => "All Asset Type"] + $assetTypes;
        $assetTypeFormElement->setValueOptions($assetType1);
        $assetTypeFormElement->setAttributes(["id" => "assetTypeId", "class" => "form-control"]);
        $assetTypeFormElement->setLabel("Type");

        //form element for asset
        $assetFormElement = new Select();
        $assetFormElement->setName("asset");
        $assets = ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::ASSET_ID, [Setup::ASSET_EDESC], ["STATUS" => "E"], Setup::ASSET_EDESC, "ASC", NULL, FALSE, TRUE);
        $asset1 = [-1 => "All Asset"] + $assets;
        $assetFormElement->setValueOptions($asset1);
        $assetFormElement->setAttributes(["id" => "asset", "class" => "form-control"]);
        $assetFormElement->setLabel("Asset");


        //form element for asset status
        $assetStatus = [
            '-1' => 'All Status',
            'CUR' => 'Current',
            'NR' => 'Not Returnable',
            'R' => 'Returnable',
            'RED' => 'Returned'
        ];

        $assetStatusFormElement = new Select();
        $assetStatusFormElement->setName("assetStatus");
        $assetStatusFormElement->setValueOptions($assetStatus);
        $assetStatusFormElement->setAttributes(["id" => "assetStatusId", "class" => "form-control"]);
        $assetStatusFormElement->setLabel("Status");


        return Helper::addFlashMessagesToArray($this, [
//                    'issue' => $list,
                    'assetType' => $assetTypeFormElement,
                    'assets' => $assetFormElement,
                    'assetStatus' => $assetStatusFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {

                $issue = new Issue();
                $issue->exchangeArrayFromForm($this->form->getData());
                $issue->issueId = ((int) Helper::getMaxId($this->adapter, $issue::TABLE_NAME, $issue::ISSUE_ID)) + 1;
                $issue->sno = ((int) Helper::getMaxId($this->adapter, $issue::TABLE_NAME, $issue::SNO)) + 1;
                $issue->createdBy = $this->employeeId;
                $issue->autorizedBy = $this->employeeId;
                $issue->companyId = $employeeDetail['COMPANY_ID'];
                $issue->branchId = $employeeDetail['BRANCH_ID'];
                $issue->status = 'E';


                $remQty = $request->getPost()['balance'];
                $newRemQty = $remQty - $issue->quantity;

//                echo '<pre>';
//                print_r($issue);
//                die();

                $assetadd = $this->repository->add($issue);
                if ($assetadd) {
                    $setupModel = new Setup();
                    $setupModel->quantityBalance = $newRemQty;
                    $setupRepo = new SetupRepository($this->adapter);
                    $setupRepo->updateRemainingAssetBalance($setupModel, $issue->assetId);
                }

                $this->flashmessenger()->addMessage("Asset Successfully issued!!!");
                return $this->redirect()->toRoute("assetSetup");
            }
        } else {
            return $this->redirect()->toRoute("assetSetup");
        }
    }

    public function editAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('assetIssue');
        }

        $this->initializeForm();
        $request = $this->getRequest();
        $issue = new Issue();
        if (!$request->isPost()) {
            $issue->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($issue);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $issue->exchangeArrayFromForm($this->form->getData());
                $issue->modifiedDate = Helper::getcurrentExpressionDate();
                $issue->modifiedBy = $this->employeeId;

                $this->repository->edit($issue, $id);
                $this->flashmessenger()->addMessage("Asset issue Sucessfully updated");
                return $this->redirect()->toRoute("assetIssue");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'editVal' => $issue,
                    'asset' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::ASSET_ID, [Setup::ASSET_EDESC], ["STATUS" => "E"], Setup::ASSET_EDESC, "ASC", NULL, FALSE, TRUE),
//            'asset' => $asset['B'],
                    'employee' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", FALSE, TRUE),
                    'id' => $id
        ]);

//        echo "<pre>";
//        print_r($issue);
//        die();
    }

    public function viewAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('assetSetup');
        }
        $assetSetupRepo = new SetupRepository($this->adapter);
        $assetImageId = $assetSetupRepo->fetchById($id)['ASSET_IMAGE'];
        $assetFile = null;
        if ($assetImageId > 0) {
            $assetFile = $this->getFileInfo($this->adapter, $assetImageId)['fileName'];
        }
//        echo '<pre>';
//        print_r($assetData);
//        print_r($assetFile);
//        die();
        $result = $this->repository->fetchAllById($id);
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'issue' => $list,
                    'id' => $id,
                    'assetImage' => $assetFile
        ]);
    }

    public function returnAssetAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        $issue = new Issue();
        $redirectLink = $this->redirect()->toRoute('assetIssue', ['action' => 'index']);
        if ($request->isPost()) {

            $postdata = $request->getPost();
            $redirectPage = $postdata['redirectPage'];
            $assetId = $postdata['assetId'];
            $issueId = $postdata['issueId'];
            $issueBal = $postdata['issueBal'];
            $returnedDate = $postdata['returndeDate'];

            if ($redirectPage == 'indexPage') {
                $redirectLink = $this->redirect()->toRoute('assetIssue', ['action' => 'index']);
            } else {
                $redirectLink = $this->redirect()->toRoute('assetIssue', ['action' => 'view', 'id' => $assetId]);
            }

            if (!empty($assetId) && !empty($assetId) && !empty($issueId) && !empty($issueBal)) {
                $issue->modifiedDate = Helper::getcurrentExpressionDate();
                $issue->modifiedBy = $this->employeeId;
                $issue->returned = 'Y';
                $issue->returnedDate = $returnedDate;

                $setupRepo = new SetupRepository($this->adapter);
                $remQty = $setupRepo->fetchById($assetId)['QUANTITY_BALANCE'];
                $newRemQty = $remQty + $issueBal;

                $updateRD = $this->repository->edit($issue, $issueId);
                if ($updateRD) {
                    $setupModel = new Setup();
                    $setupModel->quantityBalance = $newRemQty;
                    $setupRepo->updateRemainingAssetBalance($setupModel, $assetId);
                }

                $this->flashmessenger()->addMessage("Asset return Sucessfully recorded");

                return $redirectLink;
            }
        } else {
            return $redirectLink;
        }
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

    public function getAssetIssueListAction() {

        $request = $this->getRequest();
        $postValue = $request->getPost();
        $postData = $postValue['data'];
        $result = $this->repository->getFilteredRecord($postData);

        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        return new CustomViewModel([
            "success" => "true",
            "data" => $list,
            "num" => count($list),
        ]);
    }

    public function getAssetFilterListAction() {
        $request = $this->getRequest();
        $postValue = $request->getPost();
        $assetTypeId = $postValue['assetTypeId'];
        if ($assetTypeId == -1) {
            $assetList = ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::ASSET_ID, [Setup::ASSET_EDESC], ["STATUS" => "E"], Setup::ASSET_EDESC, "ASC", NULL, FALSE, TRUE);
        } else {
            $assetList = ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::ASSET_ID, [Setup::ASSET_EDESC], ["STATUS" => "E", "ASSET_GROUP_ID" => $assetTypeId], Setup::ASSET_EDESC, "ASC", NULL, FALSE, TRUE);
        }

        return new CustomViewModel([
            "success" => "true",
            "data" => $assetList,
            "postData" => $assetTypeId,
            "num" => count($assetList)
        ]);
    }

}
