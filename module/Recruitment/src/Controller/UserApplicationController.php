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
use Recruitment\Model\HrisRecApplicationStage;
use Recruitment\Model\OptionsModel;
use Recruitment\Model\OpeningVacancy;
use Application\Helper\EntityHelper;
use Zend\View\Model\JsonModel;
use Exception;
use Recruitment\Helper\AppHelper;
use Recruitment\Model\StageModel;
use Recruitment\Helper\EmailHelper;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

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
                // print_r($data);die;
                $rawList = $this->repository->applicationData($data, $this->employeeId);
                
                $rawListInternal = $this->repository->applicationDataInternal($data, $this->employeeId);                        
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
        $allAdNo = $this->repository->getAllAdNoDetail();  
        $rangedAdNo = array_filter($allAdNo,function ($value){
            if($value['IS_RANGED']=='Y'){
                return $value;
            }
        });
        $singleAdNo = array_filter($allAdNo,function ($value){
            if($value['IS_RANGED']!='Y'){
                return $value;
            }
        });

        // echo('<pre>');print_r(EntityHelper::getTableList($this->adapter, 'HRIS_REC_VACANCY', ['VACANCY_ID','AD_NO'], ['STATUS' => 'E']));die;
        // echo('<pre>');print_r($rangedAdNo);die;
        // $listOpen = iterator_to_array($rawList, false);
        // $listInternals = iterator_to_array($rawListInternal, false);
        // $allAdNo = 
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
            'rangedAdNo' => $rangedAdNo,
            'singleAdNo' => $singleAdNo,
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

        if ($VacancyData[0]['VACANCY_TYPE'] != 'OPEN') {
            $applicationData = iterator_to_array($this->repository->applicationDataByIdInternal($id), false);
            $eduDatas = $this->repository->applicationEduByIdInternal($id);
        } else {
           $applicationData = iterator_to_array($this->repository->applicationDataById($id), false);
           $eduDatas = iterator_to_array($this->repository->applicationEduById($id), false);
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
        if ($applicationData[0]['CANCELLED_INCLUSION_ID'] != null) {
            $cancelled_inc_names = $this->repository->applicationInclusionsbyId($applicationData[0]['CANCELLED_INCLUSION_ID']);
        }
        $applicationData[0]['SKILL_ID'] = $skill_names;
        $applicationData[0]['INCLUSIONS'] = $inc_names;
        $applicationData[0]['CANCELLED_INCLUSION_ID'] = $cancelled_inc_names;
        $addressData = iterator_to_array($this->repository->applicationaddressById($id), false);
        $expDatas = iterator_to_array($this->repository->applicationExpById($id), false);
        $totalExperienceDays = 0;
        if($expDatas){
            foreach($expDatas as $expData){
                $totalExperienceDays += $expData['TOTAL_DAYS'];
                $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB_AD'], $VacancyData[0]['END_DATE']);
            }
        }
        // print_r($totalExperienceDays);die;

        $totalExperienceYMD = AppHelper::DateDiffWithDays($totalExperienceDays);
        // print_r($totalExperienceYMD);die;
        $TrDatas = iterator_to_array($this->repository->applicationTrById($id), false);
        $DocDatas = iterator_to_array($this->repository->applicationDocById($id), false);
        $RegDatas = iterator_to_array($this->repository->registrationDocById($applicationData[0]['USER_ID']), false);
        $DocDatas = array_merge($DocDatas,$RegDatas);
        $applicationData[0]['FULL_NAME'] = $applicationData[0]['FIRST_NAME'].' '.$applicationData[0]['MIDDLE_NAME'].' '.$applicationData[0]['LAST_NAME'];
        // echo '<pre>'; print_r($VacancyData[0]);
        // echo '<pre>'; print_r($addressData[0]);
        // echo '<pre>'; print_r( $expDatas);die;
        
        if ($applicationData[0]['APPLICATION_TYPE'] == 'OPEN') {

            foreach ($applicationData as $app)
            {
                $folder = 'photograph';
                if ($app['DOC_FOLDER'] == $folder) {

                    $applicationData[0]['PROFILE_IMG'] = $app['PROFILE_IMG'];
                    break;

                }

            }
            
            /**
             * FOR AGE
             */
            $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB_AD'], $VacancyData[0]['EXTENDED_DATE']);

        } else {

            $applicationData[0]['PROFILE_IMG'] = $this->getRequest()->getBasePath().'/uploads/'.$applicationData[0]['PROFILE_IMG'];

            for ($i=0; $i < count($DocDatas) ; $i++) { 
                
                $DocDatas[$i]['DOC_PATH_NEW'] = $DocDatas[$i]['DOC_PATH'] . $DocDatas[$i]['DOC_NEW_NAME'] ;

            }

            $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB'], $VacancyData[0]['END_DATE']);

        }

        // echo '<pre>'; print_r( $DocDatas);die;
        // echo '<pre>'; print_r($DocDatas); die;
        $stageIds = EntityHelper::rawQueryResult($this->adapter, "select stage_ids from HRIS_REC_EMPLOYEE_STAGE_PERMISSION where employee_id = {$this->employeeId}");
        $stageIdsArr = iterator_to_array($stageIds);
        if($stageIdsArr){
            $stageIdsCsv=$stageIdsArr[1]['STAGE_IDS'];
        }else{
            $stageIdsCsv='0';
        }
        $applicationStageHistory = $this->repository->getApplicationStageHistory($id);

        // echo('<pre>');print_r($applicationData[0]);die;
        return Helper::addFlashMessagesToArray($this, [
            'applicationStageHistory'=>$applicationStageHistory,
                    'vacancyData' => $VacancyData[0],
                    'applicationData' => $applicationData[0],
                    'addressData' => $addressData[0],
                    'eduDatas' => $eduDatas,
                    'expDatas' => $expDatas,
                    'trDatas'  => $TrDatas,
                    'docDatas'  => $DocDatas,
                    'Stages' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_STAGES', ['REC_STAGE_ID','STAGE_EDESC'], ['STATUS' => 'E', 'rec_stage_id in ('.$stageIdsCsv.')'],'','ORDER_NO'),
                    'totalExperienceYMD' => $totalExperienceYMD
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

    public function updateStageAction() {
        try {
            $request = $this->getRequest();
            $postedData = $request->getPost();
            // print_r($postedData);die;
            $model = new HrisRecApplicationStage();
            $model->id = ((int) Helper::getMaxId($this->adapter, HrisRecApplicationStage::TABLE_NAME, HrisRecApplicationStage::ID)) + 1;
            $model->applicationId = $postedData['id'];
            $model->stageId = $postedData['StageId'];
            $model->createdBy = $this->employeeId;
            $model->createdDateTime = Helper::getcurrentExpressionDateTime();
            // print_r(base64_decode('4KSG4KSv4KWB4KS3IOCkheCkruCkvuCkpOCljeCkryDgpLngpL7gpLngpL4g'));die;
            // $model->remarksEn = $postedData['remarksEn'];
            $model->remarksNp = base64_encode($postedData['remarksNp']);
            // echo('<pre>');print_r($model); die;

            $VacancyData = iterator_to_array($this->repository->VacancyDataById($postedData['id']), false);

            if ($VacancyData[0]['VACANCY_TYPE'] != 'OPEN') {
                $applicationData = iterator_to_array($this->repository->applicationDataByIdInternal($postedData['id']), false);
            } else {
                $applicationData = iterator_to_array($this->repository->applicationDataById($postedData['id']), false);
            }

            // echo('<pre>');print_r($applicationData[0]['EMAIL_ID']);die;

            if ($postedData['StageId'] == 6 ){
                // $htmlDescription = self::mailHeader();
                $htmlDescription = "Dear ".$applicationData[0]['FIRST_NAME']." ".$applicationData[0]['MIDDLE_NAME']. " ". $applicationData[0]['LAST_NAME'].",<br>Applicant of application no: " . $VacancyData[0]['AD_NO']
                ."<br><br>Your applied application needs to be corrected with remarks: ". $postedData['remarksNp']
                ."<br>Please modify your application by ".  date("Y/m/d", strtotime(' + 5 days'))
                ."<br><br>Regards, <br>Nepal Oil Corporation Limited.";
                // $htmlDescription .= self::mailFooter();

                $htmlPart = new MimePart($htmlDescription);
                $htmlPart->type = "text/html";

                $body = new MimeMessage();
                $body->setParts(array($htmlPart));

                // print_r($body);die;
                $mail = new Message();
                $mail->setSubject('Application Update');
                $mail->setBody($body);
                $mail->setFrom('nepaloil.noreply@gmail.com', 'NOC');
                $mail->addTo($applicationData[0]['EMAIL_ID'], $applicationData[0]['FIRST_NAME']);
                EmailHelper::sendEmail($mail);
                // echo('<pre>');print_r('send');die;
            }
            $this->repository->addApplicationStageHistory($model);
            $this->repository->manualStageId($postedData['StageId'],$postedData['remarks'],$postedData['id'],$postedData['selectedInclusions'],$postedData['unSelectedInclusions']);
            // if ($postedData['StageId'] == 8) {
            //     $name = $this->repository->getEmpName($postedData['id']);
            // }
            
        //    $this->repository->addRollNo($postedData['StageId'],$postedData['id']);
            return new JsonModel(['success' => true, 'data' => [], 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
}