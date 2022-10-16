<?php
namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Recruitment\Form\UserApplicationForm;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\Helper;
use Zend\View\Model\ViewModel;
use Recruitment\Repository\UserApplicationRepository;
use Setup\Model\Designation;
use Setup\Model\Department;
use Recruitment\Model\SkillModel;
use Recruitment\Model\OptionsModel;
use Recruitment\Model\OpeningVacancy;
use Application\Helper\EntityHelper;
use Zend\View\Model\JsonModel;
use Exception;
use Recruitment\Helper\AppHelper;
use Recruitment\Model\StageModel;

class UserApplicationController extends HrisController
{
    function __construct(AdapterInterface $adapter, StorageInterface $storage) {

            parent::__construct($adapter, $storage);
            $this->initializeRepository(UserApplicationRepository::class);
            $this->initializeForm(UserApplicationForm::class);
        
    }
    public function indexAction()
    {
        // var_dump('here'); die;
        $request = $this->getRequest();
         
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $rawList = $this->repository->applicationData($data);
                
                $rawListInternal = $this->repository->applicationDataInternal($data);                        
                $listOpen = iterator_to_array($rawList, false);
                $listInternals = iterator_to_array($rawListInternal, false);
                // print_r($listInternals);die;
                foreach($listInternals as $listInternal) {
                    $uri = $this->getRequest()->getUri();
                    $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
                    $baseUrl = ($baseUrl == 'http://localhost') ? 'http://localhost/hana-noc/neo-hris/public/uploads/' : 'http://hr.nepaloil.org.np/uploads/';
                    $listInternal['PROFILE_IMG'] = $baseUrl.$listInternal['PROFILE_IMG'];
                    $InternalData[] = $listInternal;
                }
                if(!empty($InternalData)){
                    $list = array_merge($listOpen,$InternalData); 
                }else{
                    $list = $listOpen;
                }
                return new JsonModel(['success' => true, 'data' => $list, 'here' => '$here', 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }        
        $statusSE = $this->getRecStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        $OpeningVacancyNo = EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS', ['OPENING_ID','OPENING_NO'], ['STATUS' => 'E']);
        return $this->stickFlashMessagesTo([
            'searchValues' => AppHelper::ApplicationData($this->adapter),
            'status' => $statusSE,
            'DepartmentList' => EntityHelper::getTableList($this->adapter,Department::TABLE_NAME, ['DEPARTMENT_ID','DEPARTMENT_NAME'], ["STATUS" => "E"]),           
            'designations' => EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID,Designation::DESIGNATION_TITLE], ["STATUS" => "E"]),          
            'QualificationList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_ACADEMIC_DEGREES', 'ACADEMIC_DEGREE_ID', ['ACADEMIC_DEGREE_NAME'], ["STATUS" => "E"], 'ACADEMIC_DEGREE_NAME', "ASC", null, [null => '---'], true),
            'Openings' => $OpeningVacancyNo,
            'InclusionList' => EntityHelper::getTableList($this->adapter, OptionsModel::TABLE_NAME,[OptionsModel::OPTION_ID,OptionsModel::OPTION_EDESC], ["STATUS" => "E"]),
            'Skills' => EntityHelper::getTableList($this->adapter, SkillModel::TABLE_NAME,[SkillModel::SKILL_ID,SkillModel::SKILL_NAME], ["STATUS" => "E"]),
            'Stages' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_STAGES', ['REC_STAGE_ID','STAGE_EDESC'], ['STATUS' => 'E'],'','ORDER_NO'),
            'acl' => $this->acl,
            'Adno' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_VACANCY', ['VACANCY_ID','AD_NO'], ['STATUS' => 'E']),
        ]);
    }
    public function viewAction(){
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute("userapplication");
        }
        $VacancyData = iterator_to_array($this->repository->VacancyDataById($id), false);
        // Vacancy Skills AND inclusion
        if ($VacancyData[0]['SKILL_ID'] != null) {
            $Vskill_names = $this->SkillIdToData($VacancyData[0]['SKILL_ID']);
        }
        
        $VInclusion_names = $this->repository->applicationInclusionsbyId($VacancyData[0]['INCLUSION_ID']);
        // echo '<pre>'; print_r($VInclusion_names); die;
        $VacancyData[0]['SKILL_ID'] = $Vskill_names;
        $VacancyData[0]['INCLUSIONS'] = $VInclusion_names;

        if ($VacancyData[0]['VACANCY_TYPE'] == 'INTERNAL') {
            $applicationData = iterator_to_array($this->repository->applicationDataByIdInternal($id), false);
            
        } else {
           $applicationData = iterator_to_array($this->repository->applicationDataById($id), false);
        }
        // Application Skills  AND inclusion
        $skill_names = '';
        $inc_names = '';
        if ($applicationData[0]['SKILL_ID'] != null) {
            $skill_names = $this->SkillIdToData($applicationData[0]['SKILL_ID']);
        }
        if ($applicationData[0]['INCLUSION_ID'] != null) {
            $inc_names = $this->repository->applicationInclusionsbyId($applicationData[0]['INCLUSION_ID']);
        }
        $applicationData[0]['SKILL_ID'] = $skill_names;
        $applicationData[0]['INCLUSIONS'] = $inc_names;
        $addressData = iterator_to_array($this->repository->applicationaddressById($id), false);
        $eduDatas = iterator_to_array($this->repository->applicationEduById($id), false);
        $expDatas = iterator_to_array($this->repository->applicationExpById($id), false);
        $TrDatas = iterator_to_array($this->repository->applicationTrById($id), false);
        $DocDatas = iterator_to_array($this->repository->applicationDocById($id), false);
        $RegDatas = iterator_to_array($this->repository->registrationDocById($applicationData[0]['USER_ID']), false);
        $DocDatas = array_merge($DocDatas,$RegDatas);
        $applicationData[0]['FULL_NAME'] = $applicationData[0]['FIRST_NAME'].$applicationData[0]['MIDDLE_NAME'].$applicationData[0]['LAST_NAME'];
        // echo '<pre>'; print_r($VacancyData[0]);
        // echo '<pre>'; print_r($addressData[0]);
        // echo '<pre>'; print_r( $eduDatas);
        // echo '<pre>'; print_r($VacancyData[0]);

        // echo '<pre>'; print_r($DocDatas); die;

        return Helper::addFlashMessagesToArray($this, [
                    'vacancyData' => $VacancyData[0],
                    'applicationData' => $applicationData[0],
                    'addressData' => $addressData[0],
                    'eduDatas' => $eduDatas,
                    'expDatas' => $expDatas,
                    'trDatas'  => $TrDatas,
                    'docDatas'  => $DocDatas,
                    'Stages' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_STAGES', ['REC_STAGE_ID','STAGE_EDESC'], ['STATUS' => 'E'],'','ORDER_NO'),
        ]);
    }
    public function bulkStageIdWSAction() {
        try {
            $request = $this->getRequest();
            $postedData = $request->getPost();
            // var_dump($postedData); die;
            $this->repository->manualStageId($postedData['StageId'],$postedData['remarks'],$postedData['id'],$postedData['inclusion']);
            if ($postedData['StageId'] == 8) {
                $name = $this->repository->getEmpName($postedData['id']);
            }
        //    $this->repository->addRollNo($postedData['StageId'],$postedData['id']);
            return new JsonModel(['success' => true, 'data' => [], 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
    public function SkillIdToData($data){
        // $Vskill_array = $VacancyData[0]['SKILL_ID'];
            // $Vskill_array = $data;
            $Vskill_array = explode(',', $data);
            foreach($Vskill_array as $Vskill_id){
                $Vskill_name[] = iterator_to_array($this->repository->applicationSkillsbyId($Vskill_id));
            }
            $Vskill_array = array_map(function($e){ return  $e[1]['SKILL_ID'];}, $Vskill_name);
            $Vskill_names = implode(', ',$Vskill_array);
            // $VacancyData[0]['SKILL_ID'] = $Vskill_names;
            return $Vskill_names;
    }
    public function InclusionIdToData($data){
        $Vskill_array = explode(',', $data);
        foreach($Vskill_array as $Vskill_id){
            $Vskill_name[] = iterator_to_array($this->repository->applicationInclusionsbyId($Vskill_id));
        }
        $Vskill_array = array_map(function($e){ return  $e[1]['INCLUSION_ID'];}, $Vskill_name);
        $Vskill_names = implode(', ',$Vskill_array);
        // $VacancyData[0]['SKILL_ID'] = $Vskill_names;
        return $Vskill_names;
    }
}