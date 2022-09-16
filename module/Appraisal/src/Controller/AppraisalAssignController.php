<?php

namespace Appraisal\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Appraisal\Model\AppraisalAssign;
use Appraisal\Model\Setup;
use Appraisal\Model\Stage;
use Appraisal\Repository\AppraisalAssignRepository;
use Appraisal\Repository\AppraisalReportRepository;
use Appraisal\Repository\SetupRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class AppraisalAssignController extends AbstractActionController {

    private $adapter;
    private $repository;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new AppraisalAssignRepository($adapter);
        $this->adapter = $adapter;
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC", NULL, FALSE, TRUE);
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC", NULL, FALSE, TRUE);
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC", NULL, FALSE, TRUE);
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        $appraisalFormElement = new Select();
        $appraisalFormElement->setName("appraisal");
        $appraisals = EntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::APPRAISAL_ID, [Setup::APPRAISAL_EDESC], [Setup::STATUS => 'E'], Setup::APPRAISAL_EDESC, "ASC", NULL, FALSE, TRUE);
        $appraisalFormElement->setValueOptions($appraisals);
        $appraisalFormElement->setAttributes(["id" => "appraisalId", "class" => "form-control"]);
        $appraisalFormElement->setLabel("Appraisal");

        $employeeResult = EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], "FIRST_NAME", "ASC", " ", false, true);
        $employeeList = [];
        foreach ($employeeResult as $key => $value) {
            array_push($employeeList, ['id' => $key, 'name' => $value]);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            switch ($postData->action) {
                case "pullEmployeeWidAssignDetail":
                    $responseData = $this->pullEmployeeWidAssignDetail($postData->data);
                    break;
                case "pullEmployeeListForReportingRole":
                    $responseData = $this->pullEmployeeListForReportingRole($postData->data);
                    break;
                case "assignAppraisal":
                    $responseData = $this->assignAppraisal($postData->data);
                    break;
                default:
                    $responseData = [
                        "success" => false
                    ];
                    break;
            }
            return new CustomViewModel($responseData);
        }

        return Helper::addFlashMessagesToArray($this, [
                    'branches' => $branchFormElement,
                    'departments' => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'appraisals' => $appraisalFormElement,
                    'stages' => EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::STAGE_EDESC], [Stage::STATUS => 'E'], Stage::ORDER_NO, "ASC", NULL, FALSE, TRUE),
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'employeeList' => $employeeList,
                    'acl' => $this->acl,
        ]);
    }

    public function pullEmployeeWidAssignDetail($data) {
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $employeeId = $data['employeeId'];
        $appraisalId = $data['appraisalId'];
        $companyId = $data['companyId'];
        $serviceTypeId = $data['serviceTypeId'];
        $positionId = $data['positionId'];

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeResult = $employeeRepo->filterRecords($employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, -1, 1, $companyId);

        $employeeList = [];
        foreach ($employeeResult as $employeeRow) {
            $employeeId = $employeeRow['EMPLOYEE_ID'];
            $assignList = $this->repository->getDetailByEmpAppraisalId($employeeId, $appraisalId);

            if ($assignList != null) {
                $middleNameR = ($assignList['MIDDLE_NAME_R'] != null) ? " " . $assignList['MIDDLE_NAME_R'] . " " : " ";
                $middleNameA = ($assignList['MIDDLE_NAME_A'] != null) ? " " . $assignList['MIDDLE_NAME_A'] . " " : " ";
                $middleNameALTR = ($assignList['MIDDLE_NAME_ALT_R'] != null) ? " " . $assignList['MIDDLE_NAME_ALT_R'] . " " : " ";
                $middleNameALTA = ($assignList['MIDDLE_NAME_ALT_A'] != null) ? " " . $assignList['MIDDLE_NAME_ALT_A'] . " " : " ";
                $middleNameSuperR = ($assignList['MIDDLE_NAME_SUPER_R'] != null) ? " " . $assignList['MIDDLE_NAME_SUPER_R'] . " " : " ";
                if ($assignList['RETIRED_R'] != 'Y' && $assignList['STATUS_R'] != 'D') {
                    $employeeRow['REVIEWER_NAME'] = $assignList['FIRST_NAME_R'] . $middleNameR . $assignList['LAST_NAME_R'];
                } else {
                    $employeeRow['REVIEWER_NAME'] = "";
                }
                if ($assignList['RETIRED_A'] != 'Y' && $assignList['STATUS_A'] != 'D') {
                    $employeeRow['APPRAISER_NAME'] = $assignList['FIRST_NAME_A'] . $middleNameA . $assignList['LAST_NAME_A'];
                } else {
                    $employeeRow['APPRAISER_NAME'] = "";
                }
                if ($assignList['RETIRED_ALT_R'] != 'Y' && $assignList['STATUS_ALT_R'] != 'D') {
                    $employeeRow['ALT_REVIEWER_NAME'] = $assignList['FIRST_NAME_ALT_R'] . $middleNameALTR . $assignList['LAST_NAME_ALT_R'];
                } else {
                    $employeeRow['ALT_REVIEWER_NAME'] = "";
                }
                if ($assignList['RETIRED_ALT_A'] != 'Y' && $assignList['STATUS_ALT_A'] != 'D') {
                    $employeeRow['ALT_APPRAISER_NAME'] = $assignList['FIRST_NAME_ALT_A'] . $middleNameALTA . $assignList['LAST_NAME_ALT_A'];
                } else {
                    $employeeRow['ALT_APPRAISER_NAME'] = "";
                }
                if ($assignList['RETIRED_SUPER_R'] != 'Y' && $assignList['STATUS_SUPER_R'] != 'D') {
                    $employeeRow['SUPER_REVIEWER_NAME'] = $assignList['FIRST_NAME_SUPER_R'] . $middleNameSuperR . $assignList['LAST_NAME_SUPER_R'];
                } else {
                    $employeeRow['SUPER_REVIEWER_NAME'] = "";
                }
                $employeeRow['APPRAISAL_EDESC'] = $assignList['APPRAISAL_EDESC'];
            } else {
                $employeeRow['REVIEWER_NAME'] = "";
                $employeeRow['APPRAISER_NAME'] = "";
                $employeeRow['APPRAISAL_EDESC'] = "";
            }
            $employeeRow['CURRENT_STAGE_NAME'] = $assignList['STAGE_EDESC'];
            array_push($employeeList, $employeeRow);
        }
        return [
            "success" => true,
            "data" => $employeeList
        ];
    }

    public function pullEmployeeListForReportingRole($data) {
        $branchId = $data['branchId'];
        $locationId = $data['locationId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];

        $repository = new EmployeeRepository($this->adapter);
        $employeeResult = $repository->filterRecords(-1, $branchId, $locationId,$departmentId, $designationId, -1, -1, -1, 1);

        $employeeList = [];
        $i = 0;
        foreach ($employeeResult as $employeeRow) {
            if ($employeeRow['MIDDLE_NAME'] != null) {
                $middleName = " " . $employeeRow['MIDDLE_NAME'] . " ";
            } else {
                $middleName = " ";
            }
            $employeeList [$i]["id"] = $employeeRow['EMPLOYEE_ID'];
            $employeeList [$i]["name"] = $employeeRow['FIRST_NAME'] . $middleName . $employeeRow['LAST_NAME'];
            $i++;
        }
        return [
            'success' => true,
            'data' => $employeeList
        ];
    }

    public function assignAppraisal($data) {
        $employeeId = (int) $data['employeeId'];
        $reviewerId = (int) $data['reviewerId'];
        $appraiserId = (int) $data['appraiserId'];
        $appraisalId = (int) $data['appraisalId'];
        $altAppraiserId = (int) $data['altAppraiserId'];
        $altReviewerId = (int) $data['altReviewerId'];
        $superReviewerId = (int) $data['superReviewerId'];
        $stageId = (int) $data['stageId'];
        $appraisalRepo = new SetupRepository($this->adapter);
        $appraisalDtl = $appraisalRepo->fetchById($appraisalId);

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);


        if ($reviewerId == "" || $reviewerId == null) {
            $reviewerIdNew = null;
        } else if ($employeeId == $reviewerId) {
            $reviewerIdNew = "";
        } else {
            $reviewerIdNew = $reviewerId;
        }

        if ($appraiserId == "" || $appraiserId == null) {
            $appraiserIdNew = null;
        } else if ($employeeId == $appraiserId) {
            $appraiserIdNew = "";
        } else {
            $appraiserIdNew = $appraiserId;
        }

        if ($altReviewerId == "" || $altReviewerId == null) {
            $altReviewerIdNew = null;
        } else if ($employeeId == $altReviewerId || $altReviewerId == '-1') {
            $altReviewerIdNew = "";
        } else {
            $altReviewerIdNew = $altReviewerId;
        }

        if ($superReviewerId == "" || $superReviewerId == null) {
            $superReviewerIdNew = null;
        } else if ($employeeId == $superReviewerId || $superReviewerId == '-1') {
            $superReviewerIdNew = "";
        } else {
            $superReviewerIdNew = $superReviewerId;
        }

        if ($altAppraiserId == "" || $altAppraiserId == null) {
            $altAppraiserIdNew = null;
        } else if ($employeeId == $altAppraiserId || $altAppraiserId == '-1') {
            $altAppraiserIdNew = "";
        } else {
            $altAppraiserIdNew = $altAppraiserId;
        }
//        print_r($superReviewerIdNew); die();
        $appraisalAssign = new AppraisalAssign();
        $employeePreDtl = $this->repository->getDetailByEmpAppraisalId($employeeId, $appraisalId);
        $appraisalReportRepo = new AppraisalReportRepository($this->adapter);
        $appraiserQuestionNum = $appraisalReportRepo->checkAppraiserQuestionOnStage($appraisalDtl['CURRENT_STAGE_ID'])['NUM'];
        if ($employeePreDtl == null) {
            $appraisalAssign->employeeId = $employeeId;
            $appraisalAssign->appraisalId = $appraisalId;
            $appraisalAssign->reviewerId = $reviewerIdNew;
            $appraisalAssign->appraiserId = $appraiserIdNew;
            $appraisalAssign->altAppraiserId = $altAppraiserIdNew;
            $appraisalAssign->altReviewerId = $altReviewerIdNew;
            $appraisalAssign->superReviewerId = $superReviewerIdNew;
            $appraisalAssign->createdDate = Helper::getcurrentExpressionDate();
            $appraisalAssign->approvedDate = Helper::getcurrentExpressionDate();
            $appraisalAssign->createdBy = $this->employeeId;
            $appraisalAssign->companyId = $employeeDetail['COMPANY_ID'];
            $appraisalAssign->branchId = $employeeDetail['BRANCH_ID'];
            $appraisalAssign->currentStageId = ($stageId == null) ? $appraisalDtl['CURRENT_STAGE_ID'] : $stageId;
            $appraisalAssign->status = 'E';
            $this->repository->add($appraisalAssign);
        } else if ($employeePreDtl != null) {
            $id = $employeePreDtl['EMPLOYEE_ID'];
            $appraisalAssign->employeeId = $employeeId;
            $appraisalAssign->reviewerId = $reviewerIdNew;
            $appraisalAssign->appraiserId = $appraiserIdNew;
            $appraisalAssign->altAppraiserId = $altAppraiserIdNew;
            $appraisalAssign->altReviewerId = $altReviewerIdNew;
            $appraisalAssign->superReviewerId = $superReviewerIdNew;
            $appraisalAssign->modifiedDate = Helper::getcurrentExpressionDate();
            $appraisalAssign->modifiedBy = $this->employeeId;
            $appraisalAssign->currentStageId = ($stageId == null) ? null : $stageId;
            $appraisalAssign->status = 'E';
            $this->repository->edit($appraisalAssign, [$employeeId, $appraisalId]);
        }
        $appraisalAssign->appraisalId = $appraisalId;
        $appraisalAssign->createdBy = $this->employeeId;
        if ($appraiserQuestionNum > 0 && $appraiserIdNew != null && $appraiserIdNew != "") {
            HeadNotification::pushNotification(NotificationEvents::MONTHLY_APPRAISAL_ASSIGNED, $appraisalAssign, $this->adapter, $this);
        }
        return [
            "success" => true,
            "data" => [
                'CURRENT_STAGE_NAME' => $appraisalDtl['STAGE_EDESC']
            ]
        ];
    }

}
