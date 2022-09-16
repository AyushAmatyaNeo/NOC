<?php

namespace RestfulService\Controller;

use Application\Helper\AppraisalHelper;
use Application\Helper\ConstraintHelper;
use Application\Helper\Helper;
use Application\Repository\MonthRepository;
use Appraisal\Model\AppraisalStatus;
use Appraisal\Repository\AppraisalAssignRepository;
use Appraisal\Repository\AppraisalStatusRepository;
use Appraisal\Repository\HeadingRepository;
use Appraisal\Repository\QuestionRepository;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Model\AppraisalCompetencies;
use SelfService\Model\AppraisalKPI;
use SelfService\Repository\AppraisalCompetenciesRepo;
use SelfService\Repository\AppraisalKPIRepository;
use ServiceQuestion\Repository\EmpServiceQuestionDtlRepo;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\ServiceQuestionRepository;
use System\Repository\MenuSetupRepository;
use System\Repository\UserSetupRepository;
use Travel\Repository\TravelStatusRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class RestfulService extends AbstractRestfulController {

    private $adapter;
    private $loggedIdEmployeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->loggedIdEmployeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function convertResultInterfaceIntoArray(ResultInterface $result) {
        $tempArray = [];
        foreach ($result as $unit) {
            array_push($tempArray, $unit);
        }
        return $tempArray;
    }

    public function indexAction() {
        $request = $this->getRequest();

        $responseData = [];
        $files = $request->getFiles()->toArray();
        try {
            if (sizeof($files) > 0) {
                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/" . $newFileName);
                if ($success) {
                    $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
                }
            } else if ($request->isPost()) {
                $postedData = $request->getPost();
                if ($postedData == null) {
                    throw new Exception("request is not defined");
                }

                if (!isset($postedData->action) || $postedData->action == null || empty($postedData->action)) {
                    throw new Exception("action not defined");
                }

                switch ($postedData->action) {
                    case "checkUniqueConstraint":
                        $responseData = $this->checkUniqueConstraint($postedData->data);
                        break;
                    case "pullMonthsByFiscalYear":
                        $responseData = $this->pullMonthsByFiscalYear($postedData->data);
                        break;
                    case "getServerDate":
                        $responseData = $this->getServerDate($postedData->data);
                        break;
                    case "getServerDateBS":
                        $responseData = $this->getServerDateBS($postedData->data);
                        break;
                    case "submitAppraisalKPI":
                        $responseData = $this->submitAppraisalKPI($postedData->data);
                        break;
                    case "pullAppraisalKPIList":
                        $responseData = $this->pullAppraisalKPIList($postedData->data);
                        break;
                    case "deleteAppraisalKPI":
                        $responseData = $this->deleteAppraisalKPI($postedData->data);
                        break;
                    case "submitAppraisalCompetencies":
                        $responseData = $this->submitAppraisalCompetencies($postedData->data);
                        break;
                    case "pullAppraisalCompetenciesList":
                        $responseData = $this->pullAppraisalCompetenciesList($postedData->data);
                        break;
                    case "deleteAppraisalCompetencies":
                        $responseData = $this->deleteAppraisalCompetencies($postedData->data);
                    case "pullCurUserPwd";
                        $responseData = $this->pullCurUserPwd();
                        break;
                    case "updateCurUserPwd";
                        $responseData = $this->updateCurUserPwd($postedData->data);
                        break;
                    case "headingList":
                        $responseData = $this->headingList();
                        break;
                    case "pullEmployeeListForReportingRole":
                        $responseData = $this->pullEmployeeListForReportingRole($postedData->data);
                        break;
                    case "pullEmployeeDetailById":
                        $responseData = $this->pullEmployeeDetailById($postedData->data);
                        break;
                    case "pullTravelRequestStatusList":
                        $responseData = $this->pullTravelRequestStatusList($postedData->data);
                        break;
                    case "getServerDateForCalender":
                        $responseData = $this->getServerDateForCalender($postedData->data);
                        break;
                    default:
                        throw new Exception("action not found");
                        break;
                }
            } else {
                $responseData = [
                    "success" => false
                ];
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return new JsonModel($responseData);
    }

    private function menu($parent_menu = null) {
        $menuSetupRepository = new MenuSetupRepository($this->adapter);
        $result = $menuSetupRepository->getHierarchicalMenu($parent_menu);
        $num = count($result);
        if ($num > 0) {
            $temArray = array();
            foreach ($result as $row) {
                $children = $this->menu($row['MENU_ID']);
                if ($children) {
                    $temArray[] = array(
                        "text" => $row['MENU_NAME'],
                        "id" => $row['MENU_ID'],
                        "icon" => "fa fa-folder icon-state-success",
                        "children" => $children
                    );
                } else {
                    $temArray[] = array(
                        "text" => $row['MENU_NAME'],
                        "id" => $row['MENU_ID'],
                        "icon" => "fa fa-folder icon-state-success"
                    );
                }
            }
            return $temArray;
        } else {
            return false;
        }
    }

    public function generateQuestion($headingId) {
        $questionRepo = new QuestionRepository($this->adapter);
        $result = $questionRepo->fetchByHeadingId($headingId);
        $questionList = array();
        foreach ($result as $row) {
            $questionList[] = array(
                "text" => $row['QUESTION_EDESC'],
                "id" => $row['QUESTION_ID'],
                "icon" => "fa fa-folder icon-state-success"
            );
        }
        return $questionList;
    }

    public function headingsList() {
        $headingRepo = new HeadingRepository($this->adapter);
        $result = $headingRepo->fetchAll();
        $num = count($result);
        if ($num > 0) {
            $temArray = array();
            foreach ($result as $row) {
                $question = $this->generateQuestion($row['HEADING_ID']);
                if ($question) {
                    $temArray[] = array(
                        "text" => $row['HEADING_EDESC'],
                        "id" => $row['HEADING_ID'],
                        "icon" => "fa fa-folder icon-state-success",
                        "children" => $question
                    );
                } else {
                    $temArray[] = array(
                        "text" => $row['HEADING_EDESC'],
                        "id" => $row['HEADING_ID'],
                        "icon" => "fa fa-folder icon-state-success"
                    );
                }
            }
            return $temArray;
        } else {
            return false;
        }
    }

    private function pullEmployeeDetailById($data) {
        $employeeId = $data["employeeId"];
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employee = $employeeRepo->fetchForProfileById($employeeId);
        return ["success" => true, "data" => $employee];
    }

    public function pullEmployeeListForReportingRole($data) {
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $employeeId = $data['employeeId'];

        $repository = new EmployeeRepository($this->adapter);
        $employeeResult = $repository->filterRecords($employeeId, $branchId, $departmentId, $designationId, -1, -1, -1, 1, $companyId);

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

    public function checkUniqueConstraint($data) {
        $tableName = $data['tableName'];
        $columnsWidValues = $data['columnsWidValues'];
        $selfId = $data['selfId'];
        if ($selfId != 'R') {
            $selfId1 = $selfId;
            $requestTbl = 0;
        } else if ($selfId == 'R') {
            $requestTbl = 1;
            $selfId1 = 0;
        }
        $checkColumnName = $data['checkColumnName'];
        $result = ConstraintHelper::checkUniqueConstraint($this->adapter, $tableName, $columnsWidValues, $checkColumnName, $selfId1, $requestTbl);
        return [
            "success" => "true",
            "data" => (int) $result,
            "msg" => "* Already Exist!!!"
        ];
    }

    private function pullMonthsByFiscalYear($data) {
        $fiscalYearId = $data['fiscalYearId'];
        $monthRepo = new MonthRepository($this->adapter);
        $rawMonths = $monthRepo->fetchById($fiscalYearId);

        $months = Helper::extractDbData($rawMonths);
        return [
            "success" => true,
            "data" => $months
        ];
    }

    public function getServerDate($data) {
        return ["success" => true, "data" => ["serverDate" => date(Helper::PHP_DATE_FORMAT)]];
    }

    public function getServerDateBS($data) {
        $monthRepo = new MonthRepository($this->adapter);
        $date = [];
        $date['CURRENT_DATE'] = $monthRepo->getCurrentDateBS()['BS_DATE'];
        return ["success" => true, "data" => $date];
    }

    public function pullHierarchicalQuestion($serviceEventTypeId, $empQaId, $parentQaId = null) {
        $serviceQuestionRepo = new ServiceQuestionRepository($this->adapter);
        $empServiceQuestionDtlRepo = new EmpServiceQuestionDtlRepo($this->adapter);

        $result = $serviceQuestionRepo->fetchByServiceEventTypeId($serviceEventTypeId, $parentQaId);
        $num = count($result);
        if ($num > 0) {
            $x = 'a';
            $questionDtlArray = [];
            foreach ($result as $row) {
                $questionAnswerDtl = $empServiceQuestionDtlRepo->fetchByEmpQaIdQaId($row['QA_ID'], $empQaId);
                $tempResult = $this->pullHierarchicalQuestion($serviceEventTypeId, $empQaId, $row['QA_ID']);
                if ($tempResult) {
                    $questionDtlArray[] = array(
                        "sn" => $x,
                        "qaId" => $row['QA_ID'],
                        "questionEdesc" => $row['QUESTION_EDESC'],
                        "subQuestion" => true,
                        "subQuestionList" => $tempResult['array'],
                        "answer" => (!isset($questionAnswerDtl) || $questionAnswerDtl == null || gettype($questionAnswerDtl) == 'undefined') ? null : $questionAnswerDtl->ANSWER
                    );
                } else {
                    $questionDtlArray[] = array(
                        "sn" => $x,
                        "qaId" => $row['QA_ID'],
                        "questionEdesc" => $row['QUESTION_EDESC'],
                        "subQuestion" => false,
                        "answer" => (!isset($questionAnswerDtl) || $questionAnswerDtl == null || gettype($questionAnswerDtl) == 'undefined') ? null : $questionAnswerDtl->ANSWER
                    );
                }
                $x++;
            }
            return ['array' => $questionDtlArray];
        } else {
            return false;
        }
    }

    public function submitAppraisalKPI($data) {
        $appraisalKPIRepository = new AppraisalKPIRepository($this->adapter);
        $employeeRepository = new EmployeeRepository($this->adapter);
        $KPIList = $data['KPIList'];
        $employeeId = $data['employeeId'];
        $appraisalId = $data['appraisalId'];
        $currentUser = $data['currentUser'];
        $loggedInUser = $this->loggedIdEmployeeId;
        $loggedInUserDtl = $employeeRepository->getById($loggedInUser);
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $appraisalStatusRepo = new AppraisalStatusRepository($this->adapter);
        $appraisalStatus = new AppraisalStatus();
        $appraisalStatus->exchangeArrayFromDB($appraisalStatusRepo->fetchByEmpAppId($employeeId, $appraisalId)->getArrayCopy());
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($employeeId, $appraisalId);
        try {
            foreach ($KPIList as $KPIRow) {
                $appraisalKPI = new AppraisalKPI();
                $appraisalKPI->title = $KPIRow['title'];
                $appraisalKPI->successCriteria = $KPIRow['successCriteria'];
                $appraisalKPI->weight = $KPIRow['weight'];
                $appraisalKPI->keyAchievement = $KPIRow['keyAchievement'];
                $appraisalKPI->selfRating = (is_numeric($KPIRow['selfRating'])) ? $KPIRow['selfRating'] : null;
                $appraisalKPI->appraiserRating = (is_numeric($KPIRow['appraiserRating'])) ? $KPIRow['appraiserRating'] : null;
                if ($KPIRow['sno'] == 0 || $KPIRow['sno'] == null) {
                    $appraisalKPI->sno = (int) (Helper::getMaxId($this->adapter, AppraisalKPI::TABLE_NAME, AppraisalKPI::SNO)) + 1;
                    $appraisalKPI->appraisalId = $appraisalId;
                    $appraisalKPI->employeeId = $employeeId;
                    $appraisalKPI->createdBy = $loggedInUser;
                    $appraisalKPI->createdDate = Helper::getcurrentExpressionDate();
                    $appraisalKPI->branchId = $loggedInUserDtl['BRANCH_ID'];
                    $appraisalKPI->companyId = $loggedInUserDtl['COMPANY_ID'];
                    $appraisalKPI->status = 'E';
                    $appraisalKPIRepository->add($appraisalKPI);
                } else {
                    $appraisalKPI->modifiedBy = $loggedInUser;
                    $appraisalKPI->modifiedDate = Helper::getcurrentExpressionDate();
                    if ($appraisalKPI->employeeId != $loggedInUser) {
                        $appraisalKPI->approvedBy = $loggedInUser;
                        $appraisalKPI->approvedDate = Helper::getcurrentExpressionDate();
                    }
                    $appraisalKPIRepository->edit($appraisalKPI, $KPIRow['sno']);
                }
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 7) {
                $appraisalAssignRepo->updateCurrentStageByAppId(AppraisalHelper::getNextStageId($this->adapter, $assignedAppraisalDetail['STAGE_ORDER_NO'] + 1), $appraisalId, $employeeId);
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 5) {
                $annualRatingKPI = $data['annualRatingKPI'];
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::ANNUAL_RATING_KPI => $annualRatingKPI], $appraisalId, $employeeId);
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::APPRAISER_OVERALL_RATING => $annualRatingKPI], $appraisalId, $employeeId);
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 7) {
                switch ($currentUser) {
                    case 'appraisee':
                        HeadNotification::pushNotification(NotificationEvents::KEY_ACHIEVEMENT, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                        HeadNotification::pushNotification(NotificationEvents::KEY_ACHIEVEMENT, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['APPRAISER_ID'], 'USER_TYPE' => "APPRAISER"]);
                        $adminList = $employeeRepository->fetchByAdminFlagList();
                        foreach ($adminList as $adminRow) {
                            HeadNotification::pushNotification(NotificationEvents::KEY_ACHIEVEMENT, $appraisalStatus, $this->adapter, $this, null, ['ID' => $adminRow['EMPLOYEE_ID'], 'USER_TYPE' => "HR"]);
                        }
                        break;
                }
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        $appEmp = [
            'appraisalId' => $appraisalId,
            'employeeId' => $employeeId
        ];
        return [
            'success' => true,
        ];
    }

    public function pullAppraisalKPIList($data) {
        $appraisalId = $data['appraisalId'];
        $employeeId = $data['employeeId'];
        $appraisalKPIRepository = new AppraisalKPIRepository($this->adapter);
        $result = $appraisalKPIRepository->fetchByAppEmpId($employeeId, $appraisalId);
        $list = [];
        try {
            foreach ($result as $row) {
                array_push($list, $row);
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return [
            'success' => true,
            'data' => $list
        ];
    }

    public function deleteAppraisalKPI($data) {
        $sno = $data['sno'];
        $appraisalKPIRepository = new AppraisalKPIRepository($this->adapter);
        try {
            $appraisalKPIRepository->delete($sno);
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return [
            'success' => true,
            'data' => [
                'msg' => 'Appraisal KPI deleted successfully!!!'
            ]
        ];
    }

    public function submitAppraisalCompetencies($data) {
        $appraisalCompetenciesRepo = new AppraisalCompetenciesRepo($this->adapter);
        $employeeRepository = new EmployeeRepository($this->adapter);
        $competenciesList = $data['competenciesList'];
        $employeeId = $data['employeeId'];
        $appraisalId = $data['appraisalId'];
        $currentUser = $data['currentUser'];
        $loggedInUser = $this->loggedIdEmployeeId;
        $loggedInUserDtl = $employeeRepository->getById($loggedInUser);
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $appraisalStatusRepo = new AppraisalStatusRepository($this->adapter);
        $appraisalStatus = new AppraisalStatus();
        $appraisalStatus->exchangeArrayFromDB($appraisalStatusRepo->fetchByEmpAppId($employeeId, $appraisalId)->getArrayCopy());
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($employeeId, $appraisalId);
        try {
            foreach ($competenciesList as $competenciesRow) {
                $appraisalCompetencies = new AppraisalCompetencies();
                $appraisalCompetencies->title = $competenciesRow['title'];
                $appraisalCompetencies->rating = $competenciesRow['rating'];
                $appraisalCompetencies->comments = $competenciesRow['comments'];
                if ($competenciesRow['sno'] == 0 || $competenciesRow['sno'] == null) {
                    $appraisalCompetencies->sno = (int) (Helper::getMaxId($this->adapter, AppraisalCompetencies::TABLE_NAME, AppraisalCompetencies::SNO)) + 1;
                    $appraisalCompetencies->appraisalId = $appraisalId;
                    $appraisalCompetencies->employeeId = $employeeId;
                    $appraisalCompetencies->createdBy = $loggedInUser;
                    $appraisalCompetencies->createdDate = Helper::getcurrentExpressionDate();
                    $appraisalCompetencies->branchId = $loggedInUserDtl['BRANCH_ID'];
                    $appraisalCompetencies->companyId = $loggedInUserDtl['COMPANY_ID'];
                    $appraisalCompetencies->approvedDate = Helper::getcurrentExpressionDate();
                    $appraisalCompetencies->status = 'E';
                    $appraisalCompetenciesRepo->add($appraisalCompetencies);
                } else if ($competenciesRow['sno'] != 0) {
                    $appraisalCompetencies->modifiedBy = $loggedInUser;
                    $appraisalCompetencies->modifiedDate = Helper::getcurrentExpressionDate();
                    $appraisalCompetenciesRepo->edit($appraisalCompetencies, $competenciesRow['sno']);
                }
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 5) {
                $annualRatingCompetency = $data['annualRatingCompetency'];
                $appraiserOverallRating = $data['appraiserOverallRating'];
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::ANNUAL_RATING_COMPETENCY => $annualRatingCompetency], $appraisalId, $employeeId);
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::APPRAISER_OVERALL_RATING => $appraiserOverallRating], $appraisalId, $employeeId);
            }
            if ($assignedAppraisalDetail['STAGE_ID'] == 1) {
                switch ($currentUser) {
                    case 'appraisee':
                        HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                        HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['APPRAISER_ID'], 'USER_TYPE' => "APPRAISER"]);
                        if ($assignedAppraisalDetail['ALT_APPRAISER_ID'] != null && $assignedAppraisalDetail['ALT_APPRAISER_ID'] != "") {
                            HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['ALT_APPRAISER_ID'], 'USER_TYPE' => "APPRAISER"]);
                        }
                        if ($assignedAppraisalDetail['ALT_REVIEWER_ID'] != null && $assignedAppraisalDetail['ALT_REVIEWER_ID'] != "") {
                            HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $assignedAppraisalDetail['ALT_REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                        }
                        $adminList = $employeeRepository->fetchByAdminFlagList();
                        foreach ($adminList as $adminRow) {
                            HeadNotification::pushNotification(NotificationEvents::KPI_SETTING, $appraisalStatus, $this->adapter, $this, null, ['ID' => $adminRow['EMPLOYEE_ID'], 'USER_TYPE' => "HR"]);
                        }
                        break;
                    case 'appraiser':
                        HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $assignedAppraisalDetail['REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                        HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $employeeId, 'USER_TYPE' => "APPRAISEE"]);
                        $adminList1 = $employeeRepository->fetchByAdminFlagList();
                        foreach ($adminList1 as $adminRow1) {
                            HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $adminRow1['EMPLOYEE_ID'], 'USER_TYPE' => "HR"]);
                        }
                        break;
                    case 'reviewer':
                        HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $assignedAppraisalDetail['APPRAISER_ID'], 'USER_TYPE' => "APPRAISER"]);
                        HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $employeeId, 'USER_TYPE' => "APPRAISEE"]);
                        $adminList1 = $employeeRepository->fetchByAdminFlagList();
                        foreach ($adminList1 as $adminRow1) {
                            HeadNotification::pushNotification(NotificationEvents::KPI_APPROVED, $appraisalStatus, $this->adapter, $this, ['ID' => $this->loggedIdEmployeeId], ['ID' => $adminRow1['EMPLOYEE_ID'], 'USER_TYPE' => "HR"]);
                        }
                        break;
                }
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        $appEmp = [
            'appraisalId' => $appraisalId,
            'employeeId' => $employeeId
        ];
        return [
            'success' => true
        ];
    }

    public function pullAppraisalCompetenciesList($data) {
        $appraisalId = $data['appraisalId'];
        $employeeId = $data['employeeId'];
        $appraisalCompetenciesRepo = new AppraisalCompetenciesRepo($this->adapter);
        $result = $appraisalCompetenciesRepo->fetchByAppEmpId($employeeId, $appraisalId);
        $list = [];
        try {
            foreach ($result as $row) {
                array_push($list, $row);
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return [
            'success' => true,
            'data' => $list
        ];
    }

    public function deleteAppraisalCompetencies($data) {
        $sno = $data['sno'];
        $appraisalCompetenciesRepo = new AppraisalCompetenciesRepo($this->adapter);
        try {
            $appraisalCompetenciesRepo->delete($sno);
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return [
            'success' => true,
            'data' => [
                'msg' => 'Appraisal Competencies deleted successfully!!!'
            ]
        ];
    }

    public function pullCurUserPwd() {
        $userrepo = new UserSetupRepository($this->adapter);
        $userLoginData = $userrepo->getUserByEmployeeId($this->loggedIdEmployeeId);
        $oldPassword = $userLoginData['PASSWORD'];
        return [
            'success' => "true",
            "data" => $oldPassword
        ];
    }

    public function updateCurUserPwd($postData) {
        $newPassword = $postData['newPassword'];
        //$encryptedPwd = Helper::encryptPassword($newPassword);
        $userrepo = new UserSetupRepository($this->adapter);
        //$updateResult = $userrepo->updateByEmpId($this->loggedIdEmployeeId, $encryptedPwd);
        $updateResult = $userrepo->updateByEmpId($this->loggedIdEmployeeId, $newPassword);
        return [
            'success' => "true",
        ];
    }

    public function pullTravelRequestStatusList($data) {
        $travelStatusRepository = new TravelStatusRepository($this->adapter);
        if (key_exists('recomApproveId', $data)) {
            $recomApproveId = $data['recomApproveId'];
        } else {
            $recomApproveId = null;
        }
        $result = $travelStatusRepository->getFilteredRecord($data, $recomApproveId);

        $recordList = [];
        $getRoleDtl = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 'RECOMMENDER';
            } else if ($recomApproveId == $approver) {
                return 'APPROVER';
            } else {
                return null;
            }
        };
        $getRole = function($recommender, $approver, $recomApproveId) {
            if ($recomApproveId == $recommender) {
                return 2;
            } else if ($recomApproveId == $approver) {
                return 3;
            } else {
                return null;
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };
        $getRequestType = function($requestType) {
            if ($requestType == 'ad') {
                return "Advance";
            } else if ($requestType == 'ep') {
                return "Expense";
            } else {
                return "";
            }
        };

        foreach ($result as $row) {
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

            $status = $getValue($row['STATUS']);
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

            $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);

            $role = [
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                'ROLE' => $roleID
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $role['YOUR_ROLE'] = 'Recommender\Approver';
                $role['ROLE'] = 4;
            }
            $new_row = array_merge($row, ['STATUS' => $status, 'REQUESTED_TYPE' => $getRequestType($row['REQUESTED_TYPE'])]);
            $final_record = array_merge($new_row, $role);
            array_push($recordList, $final_record);
        }

        return [
            "success" => "true",
            "data" => $recordList,
            "num" => count($recordList),
            "recomApproveId" => $recomApproveId
        ];
    }
    
    public function getServerDateForCalender($data) {
        return ["success" => true, "data" => ["serverDate" => date('Y-m-d')]];
    }

}
