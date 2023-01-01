<?php

namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Recruitment\Helper\AppHelper;
use Recruitment\Model\HrisRecApplicationStage;
use Recruitment\Model\OptionsModel;
use Recruitment\Model\SkillModel;
use Recruitment\Repository\UserAdvanceRepository;
use Recruitment\Repository\UserApplicationRepository;
use Setup\Model\Department;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Zend\View\Model\JsonModel;

class UserAdvanceController extends HrisController {

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(UserAdvanceRepository::class);
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) 
        {
            $searchData = (array) $request->getPost();

            $openList = iterator_to_array($this->repository->getOpenData($searchData), false);

            $internalList = iterator_to_array($this->repository->getInternalData($searchData), false);

            $list = array_merge($openList, $internalList);

            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        }

        $OpeningVacancyNo = EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS', ['OPENING_ID','OPENING_NO'], ['STATUS' => 'E']);

        $stageIds = EntityHelper::rawQueryResult($this->adapter, "select stage_ids from HRIS_REC_EMPLOYEE_STAGE_PERMISSION where employee_id = {$this->employeeId}");
        $stageIdsArr = iterator_to_array($stageIds);
        if($stageIdsArr){
            $stageIdsCsv=$stageIdsArr[1]['STAGE_IDS'];
        }else{
            $stageIdsCsv='0';
        }

        return $this->stickFlashMessagesTo([
            'searchValues' => AppHelper::ApplicationData($this->adapter),
            'DepartmentList' => EntityHelper::getTableList($this->adapter,Department::TABLE_NAME, ['DEPARTMENT_ID','DEPARTMENT_NAME'], ["STATUS" => "E"]),           
            'designations' => EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID,Designation::DESIGNATION_TITLE], ["STATUS" => "E"]),          
            'QualificationList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_ACADEMIC_DEGREES', 'ACADEMIC_DEGREE_ID', ['ACADEMIC_DEGREE_NAME'], ["STATUS" => "E"], 'ACADEMIC_DEGREE_NAME', "ASC", null, [null => '---'], true),
            'Openings' => $OpeningVacancyNo,
            'InclusionList' => EntityHelper::getTableList($this->adapter, OptionsModel::TABLE_NAME,[OptionsModel::OPTION_ID,OptionsModel::OPTION_EDESC], ["STATUS" => "E"]),
            'Skills' => EntityHelper::getTableList($this->adapter, SkillModel::TABLE_NAME,[SkillModel::SKILL_ID,SkillModel::SKILL_NAME], ["STATUS" => "E"]),
            'Stages' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_STAGES', ['REC_STAGE_ID','STAGE_EDESC'], ['STATUS' => 'E','VACANCY_APPLICATION'=>'S'],'','ORDER_NO', 'DSC'),
            'acl' => $this->acl,
            'Adno' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_VACANCY', ['VACANCY_ID','AD_NO'], ['STATUS' => 'E'],'',"TO_INT(SUBSTR_BEFORE(AD_NO,'/'))", 'ASC'),
        ]);
    }

    public function bulkAction()
    {
        $request = $this->getRequest();

        $postData = $request->getPost();

        $id = $postData['data']['APPLICATION_ID'];
        $stageId = $postData['stage'];

        $model = new HrisRecApplicationStage();
        $model->id = ((int) Helper::getMaxId($this->adapter, HrisRecApplicationStage::TABLE_NAME, HrisRecApplicationStage::ID)) + 1;
        $model->applicationId = $id;
        $model->stageId = $stageId;
        $model->createdBy = $this->employeeId;
        $model->createdDateTime = Helper::getcurrentExpressionDateTime();

        $userRepo = new UserApplicationRepository($this->adapter);
        $staging = AppHelper::StageSelectorModifier($stageId);

        // Update flags
        $userRepo->getUpdateById('HRIS_REC_VACANCY_APPLICATION', $staging, 'APPLICATION_ID', $id);

        // Update Add history
        $userRepo->addApplicationStageHistory($model);

        // TODO: Handle if the user is being promoted to the employee

        return new CustomViewModel(['success' => true, 'message' => 'Data Successfully Updated']);
    }

}