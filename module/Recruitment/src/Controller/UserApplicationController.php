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
use Recruitment\Helper\NepaliCalendarHelper;
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
                 $getInfo = $this->repository->getRowById('HRIS_REC_EMPLOYEE_STAGE_PERMISSION', 'EMPLOYEE_ID', $this->employeeId);

                if ($getInfo['ACCESS_AS'] == 'A') {

                    $allowStage = '7,8,9';

                } elseif ($getInfo['ACCESS_AS'] == 'V') {

                    $allowStage = '2,6,7,9';
                } else {

                     $allowStage = '1,2,3,4,5,6,7,8,9';
                }

                $rawList = $this->repository->applicationData($data, $this->employeeId, $allowStage);
                $rawListInternal = $this->repository->applicationDataInternal($data, $this->employeeId, $allowStage);                         


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
                usort($list, function($a, $b) {
                    return $a['AD_NO_ORDER'] <=> $b['AD_NO_ORDER'];
                });
                return new JsonModel(['success' => true, 'data' => $list, 'here' => '$here', 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }        
        $statusSE = $this->getRecStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control reset-field', 'label' => 'Status']);
        $OpeningVacancyNo = EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS', ['OPENING_ID','OPENING_NO'], ['STATUS' => 'E']);
        // echo "<pre>";
        // print_r($this->employeeId);
        // die;
        return $this->stickFlashMessagesTo([
            'searchValues' => AppHelper::ApplicationData($this->adapter),
            'status' => $statusSE,
            'DepartmentList' => EntityHelper::getTableList($this->adapter,Department::TABLE_NAME, ['DEPARTMENT_ID','DEPARTMENT_NAME'], ["STATUS" => "E"]),           
            'designations' => EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID,Designation::DESIGNATION_TITLE], ["STATUS" => "E"]),          
            'QualificationList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_ACADEMIC_DEGREES', 'ACADEMIC_DEGREE_ID', ['ACADEMIC_DEGREE_NAME'], ["STATUS" => "E"], 'ACADEMIC_DEGREE_NAME', "ASC", null, [null => '---'], true),
            'Openings' => $OpeningVacancyNo,
            'InclusionList' => EntityHelper::getTableList($this->adapter, OptionsModel::TABLE_NAME,[OptionsModel::OPTION_ID,OptionsModel::OPTION_EDESC], ["STATUS" => "E"]),
            'Skills' => EntityHelper::getTableList($this->adapter, SkillModel::TABLE_NAME,[SkillModel::SKILL_ID,SkillModel::SKILL_NAME], ["STATUS" => "E"]),
            'Stages' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_STAGES', ['REC_STAGE_ID','STAGE_EDESC'], ['STATUS' => 'E','VACANCY_APPLICATION'=>'A'],'','ORDER_NO', 'DSC'),
            'acl' => $this->acl,
            'Adno' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_VACANCY', ['VACANCY_ID','AD_NO'], ['STATUS' => 'E'],'',"TO_INT(SUBSTR_BEFORE(AD_NO,'/'))", 'ASC'),
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
        // echo "<pre>";
        // print_r($expDatas); die;
        $totalExperienceDays = 0;
        if($expDatas){
            foreach($expDatas as $expData){
                $totalExperienceDays += $expData['TOTAL_DAYS'];
                $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB_AD'], $VacancyData[0]['EXTENDED_DATE_AD']);
            }
        }
        // print_r($totalExperienceDays);die;

        $totalExperienceYMD = AppHelper::DateDiffWithDays($totalExperienceDays);
        $TrDatas = iterator_to_array($this->repository->applicationTrById($id), false);
        $DocDatas = iterator_to_array($this->repository->applicationDocById($id), false);
        $RegDatas = iterator_to_array($this->repository->registrationDocById($applicationData[0]['USER_ID']), false);
        $DocDatas = array_merge($DocDatas,$RegDatas);
        $applicationData[0]['FULL_NAME'] = $applicationData[0]['FIRST_NAME'].' '.$applicationData[0]['MIDDLE_NAME'].' '.$applicationData[0]['LAST_NAME'];
        // echo '<pre>'; print_r($VacancyData[0]);die;
        // echo '<pre>'; print_r($addressData[0]);
        // echo '<pre>'; print_r( $eduDatas);
        // echo '<pre>'; print_r($VacancyData[0]);
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
            // $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB_AD'], $VacancyData[0]['END_DATE_AD']);

            $applicationData[0]['AGE'] = AppHelper::DateDiffByNepaliDate($applicationData[0]['DOB'], $VacancyData[0]['END_DATE']);

        } else {

            $applicationData[0]['PROFILE_IMG'] = $this->getRequest()->getBasePath().'/uploads/'.$applicationData[0]['PROFILE_IMG'];

            for ($i=0; $i < count($DocDatas) ; $i++) { 
                
                $DocDatas[$i]['DOC_PATH_NEW'] = $DocDatas[$i]['DOC_PATH'] . $DocDatas[$i]['DOC_NEW_NAME'] ;

            }

            /**
             * FOR AGE
             */
            $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB'], $VacancyData[0]['END_DATE_AD']);

        }

        // echo '<pre>'; print_r($DocDatas); die;
        $stageIds = EntityHelper::rawQueryResult($this->adapter, "select stage_ids from HRIS_REC_EMPLOYEE_STAGE_PERMISSION where employee_id = {$this->employeeId}");
        $stageIdsArr = iterator_to_array($stageIds);
        if($stageIdsArr){
            $stageIdsCsv=$stageIdsArr[1]['STAGE_IDS'];
        }else{
            $stageIdsCsv='0';
        }

        $applicationStageHistory = $this->repository->getApplicationStageHistory($id);

        // print_r($totalExperienceYMD);die;
        return Helper::addFlashMessagesToArray($this, [
            'applicationStageHistory' => $applicationStageHistory,
                    'vacancyData' => $VacancyData[0],
                    'applicationData' => $applicationData[0],
                    'addressData' => $addressData[0],
                    'eduDatas' => $eduDatas,
                    'expDatas' => $expDatas,
                    'trDatas'  => $TrDatas,
                    'docDatas'  => $DocDatas,
                    'Stages' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_STAGES', ['REC_STAGE_ID','STAGE_EDESC'], ['STATUS' => 'E', 'rec_stage_id in ('.$stageIdsCsv.')'],'','ORDER_NO'),
                    'totalExperienceYMD' => $totalExperienceYMD,
                    'id'=>$id
        ]);
    }
    public function viewtestAction(){
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
        // echo "<pre>";
        // print_r($expDatas); die;
        $totalExperienceDays = 0;
        if($expDatas){
            foreach($expDatas as $expData){
                $totalExperienceDays += $expData['TOTAL_DAYS'];
                $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB_AD'], $VacancyData[0]['EXTENDED_DATE_AD']);
            }
        }
        // print_r($totalExperienceDays);die;

        $totalExperienceYMD = AppHelper::DateDiffWithDays($totalExperienceDays);
        $TrDatas = iterator_to_array($this->repository->applicationTrById($id), false);
        $DocDatas = iterator_to_array($this->repository->applicationDocById($id), false);
        $RegDatas = iterator_to_array($this->repository->registrationDocById($applicationData[0]['USER_ID']), false);
        $DocDatas = array_merge($DocDatas,$RegDatas);
        $applicationData[0]['FULL_NAME'] = $applicationData[0]['FIRST_NAME'].$applicationData[0]['MIDDLE_NAME'].$applicationData[0]['LAST_NAME'];
        // echo '<pre>'; print_r($VacancyData[0]);die;
        // echo '<pre>'; print_r($addressData[0]);
        // echo '<pre>'; print_r( $eduDatas);
        // echo '<pre>'; print_r($VacancyData[0]);
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
            // $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB_AD'], $VacancyData[0]['END_DATE_AD']);

            $applicationData[0]['AGE'] = AppHelper::DateDiffByNepaliDate($applicationData[0]['DOB'], $VacancyData[0]['END_DATE']);

        } else {

            $applicationData[0]['PROFILE_IMG'] = $this->getRequest()->getBasePath().'/uploads/'.$applicationData[0]['PROFILE_IMG'];

            for ($i=0; $i < count($DocDatas) ; $i++) { 
                
                $DocDatas[$i]['DOC_PATH_NEW'] = $DocDatas[$i]['DOC_PATH'] . $DocDatas[$i]['DOC_NEW_NAME'] ;

            }

            /**
             * FOR AGE
             */
            $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB'], $VacancyData[0]['END_DATE_AD']);

        }

        // echo '<pre>'; print_r($DocDatas); die;
        $stageIds = EntityHelper::rawQueryResult($this->adapter, "select stage_ids from HRIS_REC_EMPLOYEE_STAGE_PERMISSION where employee_id = {$this->employeeId}");
        $stageIdsArr = iterator_to_array($stageIds);
        if($stageIdsArr){
            $stageIdsCsv=$stageIdsArr[1]['STAGE_IDS'];
        }else{
            $stageIdsCsv='0';
        }
        // print_r($totalExperienceYMD);die;
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

    public function updateAction() {
        try {
             $id = (int) $this->params()->fromRoute('id');

            $request   = $this->getRequest();
            $postedData = $request->getPost();

            $getInclusion        = $this->repository->getRowById("HRIS_REC_APPLICATION_PERSONAL" ,'APPLICATION_ID', $id);

            $selectedInclusion    = implode(',', $postedData['approvedInclusion']);

            $getInclusionSpecific = explode(',',$getInclusion['INCLUSION_ID']);

            $unselectedInclusion  = implode(',',array_diff($getInclusionSpecific, $postedData['approvedInclusion']));


            $this->repository->updateSelectedUnselectedInclusion($id,$selectedInclusion,$unselectedInclusion);

            $model = new HrisRecApplicationStage();
            $model->id = ((int) Helper::getMaxId($this->adapter, HrisRecApplicationStage::TABLE_NAME, HrisRecApplicationStage::ID)) + 1;
            $model->applicationId = $postedData['id'];
            $model->stageId = $postedData['StageId'];
            $model->createdBy = $this->employeeId;
            $model->createdDateTime = Helper::getcurrentExpressionDateTime();
            // print_r(base64_decode('4KSG4KSv4KWB4KS3IOCkheCkruCkvuCkpOCljeCkryDgpLngpL7gpLngpL4g'));die;
            // $model->remarksEn = $postedData['remarksEn'];


            
            $model->remarksNp = base64_encode($postedData['remarksNp']);

            $VacancyData = iterator_to_array($this->repository->VacancyDataById($postedData['id']), false);

            if ($VacancyData[0]['VACANCY_TYPE'] != 'OPEN') {
                $applicationData = iterator_to_array($this->repository->applicationDataByIdInternal($postedData['id']), false);
            } else {
                $applicationData = iterator_to_array($this->repository->applicationDataById($postedData['id']), false);
            }

            if ($postedData['StageId'] == 6 ){
                // $htmlDescription = self::mailHeader();
                $htmlDescription = "Dear ".$applicationData[0]['FIRST_NAME']." ".$applicationData[0]['MIDDLE_NAME']. " ". $applicationData[0]['LAST_NAME'].",<br>Applicant of application no: " . $VacancyData[0]['AD_NO']
                ."<br><br>Your applied application needs to be corrected with remarks: ". $postedData['remarksNp']
                ."<br>Please modify your application by ".  date("Y/m/d", strtotime(' + 5 days'))
                ."<br><br>Regards, <br>Nepal Oil Corporation Limited.";
                // $htmlDescription .= self::mailFooter();
            }

            if ($postedData['StageId'] == 8 ){
                // $htmlDescription = self::mailHeader();
                $htmlDescription = "Dear ".$applicationData[0]['FIRST_NAME']." ".$applicationData[0]['MIDDLE_NAME']. " ". $applicationData[0]['LAST_NAME'].",<br>Applicant of application no: " . $VacancyData[0]['AD_NO']
                ."<br>We like to inform you that,"
                ."<br><br>Your applied application has been approved with remarks: ". $postedData['remarksNp']
                ."<br>Thank You For your application."
                ."<br><br>Regards, <br>Nepal Oil Corporation Limited.";
                // $htmlDescription .= self::mailFooter();
            }

            if ($postedData['StageId'] == 9 ){
                // $htmlDescription = self::mailHeader();
                $htmlDescription = "Dear ".$applicationData[0]['FIRST_NAME']." ".$applicationData[0]['MIDDLE_NAME']. " ". $applicationData[0]['LAST_NAME'].",<br>Applicant of application no: " . $VacancyData[0]['AD_NO']
                ."<br>We are sorry to inform you that,"
                ."<br><br>Your applied application has been rejected with remarks: ". $postedData['remarksNp']
                ."<br>Hope you try for next time."
                ."<br><br>Regards, <br>Nepal Oil Corporation Limited.";
                // $htmlDescription .= self::mailFooter();
            }



            $this->repository->addApplicationStageHistory($model);
            $this->repository->manuallyStageId($postedData['StageId'], $postedData['id']);
            // $this->repository->updateSelectedUnselectedInclusion()
            $staging = AppHelper::StageSelectorModifier($postedData['StageId']);
            $this->repository->getUpdateById('HRIS_REC_VACANCY_APPLICATION', $staging, 'APPLICATION_ID', $postedData['id']);


            if (($postedData['StageId'] == 6) || ($postedData['StageId'] == 8) || ($postedData['StageId'] == 9)) {

                date_default_timezone_set("Asia/Kathmandu");
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
                // Commented for testing purpose
                // EmailHelper::sendEmail($mail);
            }
                // echo('<pre>');print_r('send');die;
            /* UPDATING VACANCY EDIT VERIFIED APPROVED COLUMN */


           
            // }
        //    $this->repository->addRollNo($postedData['StageId'],$postedData['id']);
            // return new JsonModel(['success' => true, 'data' => [], 'error' => '']);
           $this->redirect()->toRoute("userapplication");
        } catch (Exception $e) {
            // return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            $this->redirect()->toRoute("userapplication");
        }
    }


    public function editApplicationFormAction(){
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute("userapplication");
        }
        $VacancyData = iterator_to_array($this->repository->VacancyDataById($id), false);

        

        $employee_id = $this->employeeId;


        // Vacancy Skills AND inclusion
        if ($VacancyData[0]['SKILL_ID'] != null) {
            $Vskill_names = $this->SkillIdToData($VacancyData[0]['SKILL_ID']);
        }
        
        $VInclusion_names = $this->repository->applicationInclusionsbyId($VacancyData[0]['INCLUSION_ID']);
        // echo '<pre>'; print_r($VInclusion_names); die;
        $VacancyData[0]['SKILL_ID'] = $Vskill_names;
        $VacancyData[0]['INCLUSIONS'] = $VInclusion_names;

        if ($VacancyData[0]['VACANCY_TYPE'] != 'OPEN') {

            $applicationData = iterator_to_array($this->repository->applicationDataByIdInternalEdit($id), false);
            $eduDatas = $this->repository->applicationEduByIdInternalEdit($id);
            $expDatas = $this->repository->applicationExpByIdInternalEdit($id);

        } else {

            $applicationData = iterator_to_array($this->repository->applicationDataById($id), false);
            $eduDatas = iterator_to_array($this->repository->applicationEduById($id), false);
            $expDatas = iterator_to_array($this->repository->applicationExpById($id), false);


            $chanageDate = new NepaliCalendarHelper();
            $i = 0;
            foreach ($expDatas as $exp) {

                if (!empty($exp['FROM_DATE'])) {

                    $from_year  = (int) substr($exp['FROM_DATE'], 0, 4);
                    $from_month = (int) substr($exp['FROM_DATE'], 5, 2);
                    $from_day   = (int) substr($exp['FROM_DATE'], 8, 2);

                    $exp['from_date'] = $chanageDate->nep_to_eng($from_year, $from_month, $from_day);

                    $expDatas[$i]['FROM_DATE_AD'] = $exp['from_date']['year'].'-'.$exp['from_date']['month'].'-'.$exp['from_date']['date'];


                    $to_year  = (int) substr($exp['TO_DATE'], 0, 4);
                    $to_month = (int) substr($exp['TO_DATE'], 5, 2);
                    $to_day   = (int) substr($exp['TO_DATE'], 8, 2);

                    $exp['to_date'] = $chanageDate->nep_to_eng($to_year, $to_month, $to_day);

                    $expDatas[$i]['TO_DATE_AD'] = $exp['to_date']['year'].'-'.$exp['to_date']['month'].'-'.$exp['to_date']['date'];
                    
                }
        
                

                $i++;
            }
        }


        /**
         * EDUCATION DATA
         * */
        $programs     = $this->repository->getAllRow('HRIS_ACADEMIC_PROGRAMS');
        $degrees      = $this->repository->getAllRow('HRIS_ACADEMIC_DEGREES');
        $universities = $this->repository->getAllRow('HRIS_ACADEMIC_UNIVERSITY');
        $courses      = $this->repository->getAllRow('HRIS_ACADEMIC_COURSES');

        /**
        * ADDRESS DATA
        * */
        $provinces      = $this->repository->getAllRow('HRIS_PROVINCES');
        $districts      = $this->repository->getAllRow('HRIS_DISTRICTS');
        $municipalities = $this->repository->getAllRow('HRIS_VDC_MUNICIPALITIES');
        $zones = $this->repository->getAllRow('HRIS_ZONES');

        /* EXPERIENCE DATE CHANGING TO ENGLISH*/

        


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
       
        // echo "<pre>";
        // print_r($expDatas); die;
        $totalExperienceDays = 0;
        if($expDatas){
            foreach($expDatas as $expData){
                $totalExperienceDays += $expData['TOTAL_DAYS'];
                $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB_AD'], $VacancyData[0]['EXTENDED_DATE_AD']);
            }
        }

        $totalExperienceYMD = AppHelper::DateDiffWithDays($totalExperienceDays);
        $TrDatas = iterator_to_array($this->repository->applicationTrById($id), false);
        $TrEmpDatas = $this->repository->applicationTrEmpById($id);
        $DocDatas = iterator_to_array($this->repository->applicationDocById($id), false);
        $RegDatas = iterator_to_array($this->repository->registrationDocById($applicationData[0]['USER_ID']), false);
        $DocDatas = array_merge($DocDatas,$RegDatas);
        $applicationData[0]['FULL_NAME'] = $applicationData[0]['FIRST_NAME'].' '.$applicationData[0]['MIDDLE_NAME'].' '.$applicationData[0]['LAST_NAME'];
        // echo '<pre>'; print_r($DocDatas);die;
        // echo '<pre>'; print_r($addressData[0]);
        // echo '<pre>'; print_r( $eduDatas);
        // echo '<pre>'; print_r($VacancyData[0]);
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
            // $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB_AD'], $VacancyData[0]['END_DATE_AD']);

            $applicationData[0]['AGE'] = AppHelper::DateDiffByNepaliDate($applicationData[0]['DOB'], $VacancyData[0]['END_DATE']);

        } else {

            $applicationData[0]['PROFILE_IMG'] = $this->getRequest()->getBasePath().'/uploads/'.$applicationData[0]['PROFILE_IMG'];

            for ($i=0; $i < count($DocDatas) ; $i++) { 
                
                $DocDatas[$i]['DOC_PATH_NEW'] = $DocDatas[$i]['DOC_PATH'] . $DocDatas[$i]['DOC_NEW_NAME'] ;

            }

            /**
             * FOR AGE
             */
            $applicationData[0]['AGE'] = AppHelper::DateDiff($applicationData[0]['DOB'], $VacancyData[0]['END_DATE_AD']);

        }

        // echo '<pre>'; print_r($DocDatas); die;
        $stageIds = EntityHelper::rawQueryResult($this->adapter, "select stage_ids from HRIS_REC_EMPLOYEE_STAGE_PERMISSION where employee_id = {$this->employeeId}");
        $stageIdsArr = iterator_to_array($stageIds);
        if($stageIdsArr){
            $stageIdsCsv=$stageIdsArr[1]['STAGE_IDS'];
        }else{
            $stageIdsCsv='0';
        }

        $applicationStageHistory = $this->repository->getApplicationStageHistory($id);

        // print_r($totalExperienceYMD);die;
        return Helper::addFlashMessagesToArray($this, [
            'applicationStageHistory' => $applicationStageHistory,
                    'vacancyData' => $VacancyData[0],
                    'applicationData' => $applicationData[0],
                    'addressData' => $addressData[0],
                    'eduDatas' => $eduDatas,
                    'expDatas' => $expDatas,
                    'trDatas'  => $TrDatas,
                    'trEmpDatas'  => $TrEmpDatas,
                    'docDatas'  => $DocDatas,
                    'Stages' => EntityHelper::getTableList($this->adapter, 'HRIS_REC_STAGES', ['REC_STAGE_ID','STAGE_EDESC'], ['STATUS' => 'E', 'rec_stage_id in ('.$stageIdsCsv.')'],'','ORDER_NO'),
                    'totalExperienceYMD' => $totalExperienceYMD,
                    'eduDegrees' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_DEGREES', ['ACADEMIC_DEGREE_ID' => "ACADEMIC_DEGREE_ID", 'ACADEMIC_DEGREE_NAME'=>"ACADEMIC_DEGREE_NAME"], ["STATUS" => "E"]),
                    'eduFaculty' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_PROGRAMS', ['ACADEMIC_PROGRAM_ID' => "ACADEMIC_PROGRAM_ID", 'ACADEMIC_PROGRAM_NAME'=>"ACADEMIC_PROGRAM_NAME"], ["STATUS" => "E"]),
                    'eduUniversity' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_UNIVERSITY', ['ACADEMIC_UNIVERSITY_ID' => "ACADEMIC_UNIVERSITY_ID", 'ACADEMIC_UNIVERSITY_NAME'=>"ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => "E"]),
                    'eduCourses' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_COURSES', ['ACADEMIC_COURSE_ID' => "ACADEMIC_COURSE_ID", 'ACADEMIC_COURSE_NAME'=>"ACADEMIC_COURSE_NAME"], ["STATUS" => "E"]),
                    'programs' => $programs,
                    'degrees' => $degrees,
                    'universities' => $universities,
                    'courses'=>$courses,
                    'districts'=>$districts,
                    'provinces'=>$provinces,
                    'municipalities'=>$municipalities,
                    'zones'=>$zones,
                    'id'=>$id,
                    'employee_id'=>$employee_id
        ]);
    }

    public function editPersonalInternalInfoAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
             
            /* UPDATING PROFILE IMAGE */

            if ($postData['form_type'] == 'internal') {

                $check_profile = $this->repository->getRowId('HRIS_EMPLOYEE_FILE', 'FILE_CODE', $postData['profile_picture_id']);

                if ($postData['profilepic']['error'] == 0) { 

                    $profile_id = $postData['profile_picture_id'];

                    $ext =  strtolower(pathinfo($postData['profilepic']['name'], PATHINFO_EXTENSION));
                    // FILE UPLOADING
                    $fileName    = pathinfo($postData['profilepic']['name'], PATHINFO_FILENAME);
                    $unique      = Helper::generateUniqueName();
                    $newFileName = $unique . "." . $ext;

                    $success = move_uploaded_file($postData['profilepic']['tmp_name'], Helper::UPLOAD_DIR . "/" . $newFileName);

                    $check_profile = $this->repository->getRowId('HRIS_EMPLOYEE_FILE', 'FILE_CODE', $profile_id);

                    if ($check_profile) {

                        if ($check_profile['FILE_PATH'] !== 'default-profile-picture.jpg') {

                            // UPDATING BUT UNLINK AS KEEP RECORD
                            // $path = Helper::UPLOAD_DIR . "/" . $check_profile['FILE_PATH'];

                            // if (file_exists($path)) {

                            //     unlink($path);

                            // }

                            $updateData = array(
                               'EMPLOYEE_ID' => $postData['employee_id'],
                               'FILE_PATH'   => $newFileName,
                               'MODIFIED_DT' =>  date('Y-m-d')
                            );

                            $this->repository->getUpdateById('HRIS_EMPLOYEE_FILE', $updateData, 'FILE_CODE', $profile_id);

                        }

                        // } else {

                        //     $profileInsert = array(
                        //        'FILE_CODE' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1,
                        //        'EMPLOYEE_ID' => $postData['employee_id'],
                        //        'FILETYPE_CODE' => '',
                        //        'FILE_PATH' => $newFileName,
                        //        'STATUS'    => 'E',
                        //        'CREATED_DT' =>  date('Y-m-d'),
                        //        'MODIFIED_DT' => '',
                        //        'REMARKS' => '',
                        //        'FILE_ID' => '',
                        //     );

                        //     $this->repository->insertData('HRIS_EMPLOYEE_FILE',$profileInsert);


                        // }
                    }

                }

                $updateData = [
                    'FIRST_NAME'  => $postData['first_name'],
                    'MIDDLE_NAME' => $postData['middle_name'],
                    'LAST_NAME'   => $postData['last_name'],
                    'BIRTH_DATE'  => $postData['dob'],
                    'MOBILE_NO'   => $postData['mobile_no'],
                    'EMAIL_OFFICIAL'   => $postData['email_id'],
                    'ID_CITIZENSHIP_NO'   => $postData['citizenship_no'],
                    'ID_CITIZENSHIP_ISSUE_DATE'   => $postData['ctz_issue_date'],
                    'ID_CITIZENSHIP_ISSUE_PLACE'   => $postData['ctz_issue_district']
                ];

                $this->repository->getUpdateById('HRIS_EMPLOYEES', $updateData, 'EMPLOYEE_ID', $employee_id);

            } elseif ($postData['form_type'] == 'open'){

                $updateData1 = [
                    'FIRST_NAME'  => $postData['first_name'],
                    'MIDDLE_NAME' => $postData['middle_name'],
                    'LAST_NAME'   => $postData['last_name'],
                    'MOBILE_NO'   => $postData['mobile_no'],
                    'EMAIL_ID'    => $postData['email_id']
                ];

                $this->repository->getUpdateById('HRIS_REC_VACANCY_USERS', $updateData1, 'USER_ID', $postData['user_id']);


                $updateData2 = [
                    'CITIZENSHIP_NO'         => $postData['citizenship_no'],
                    'ID_CITIZENSHIP_ISSUE_DATE' => $postData['ctz_issue_date']
                ];

                $this->repository->getUpdateById('HRIS_REC_USERS_REGISTRATION', $updateData2, 'REGISTRATIONS_ID', $postData['registration_id']);



            }
        }
        return $this->redirect()->toRoute('userapplication', 
                        ['action' => 'view', 
                        'id' => $postData['application_id']]);
    }

    public function editPersonalInternalFamInfoAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute("userapplication");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {

            $postData = $request->getPost()->toArray();

            if ($postData['form_type'] == 'internal') {

                $updateData = [
                    'FAM_FATHER_NAME'       => $postData['fam_father_name'],
                    'FAM_FATHER_OCCUPATION' => $postData['fam_father_occupation'],
                    'FAM_MOTHER_NAME'       => $postData['fam_mother_name'],
                    'FAM_MOTHER_OCCUPATION' => $postData['fam_mother_occupation'],
                    'FAM_GRAND_FATHER_NAME' => $postData['fam_grand_father_name'],
                ];

                $this->repository->getUpdateById('HRIS_EMPLOYEES', $updateData, 'EMPLOYEE_ID', $postData['employee_id']);

            } elseif ($postData['form_type'] == 'open') {

                $updateData = [
                    'FATHER_NAME'       => $postData['fam_father_name'],
                    'FATHER_OCCUPATION' => $postData['fam_father_occupation'],
                    'MOTHER_NAME'       => $postData['fam_mother_name'],
                    'MOTHER_OCCUPATION' => $postData['fam_mother_occupation'],
                    'GRANDFATHER_NAME'  => $postData['fam_grand_father_name'],
                ];

                $this->repository->getUpdateById('HRIS_REC_USERS_REGISTRATION', $updateData, 'REGISTRATION_ID', $registration_id);

            }

            return $this->redirect()->toRoute('userapplication', 
                        ['action' => 'view', 
                        'id' => $postData['application_id']]);
        }
    }

    public function editPersonalInternalAddressInfoAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute("userapplication");
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $postData = $request->getPost()->toArray();

            if ($postData['form_type'] == 'internal') {

                $updateData = [
                    'ADDR_PERM_PROVINCE_ID'=> $postData['addr_perm_province_id'],
                    'ADDR_PERM_ZONE_ID'=> $postData['addr_perm_zone_id'],
                    'ADDR_PERM_DISTRICT_ID'     => $postData['addr_perm_district_id'],
                    'ADDR_PERM_WARD_NO'    => $postData['addr_perm_ward_no'],
                    'ADDR_PERM_STREET_ADDRESS'       => $postData['addr_perm_street_address'],
                ];

                $this->repository->getUpdateById('HRIS_EMPLOYEES', $updateData, 'EMPLOYEE_ID', $postData['employee_id']);

            } elseif ($postData['form_type'] == 'open') {

                $updateData = [
                    'PER_PROVINCE_ID'=> $postData['province'],
                    'PER_DISTRICT_ID'=> $postData['district'],
                    'PER_VDC_ID'     => $postData['municipality'],
                    'PER_WARD_ID'    => $postData['ward'],
                    'PER_TOLE'       => $postData['tole'],
                ];

                $this->repository->getUpdateById('HRIS_REC_USERS_ADDRESS', $updateData, 'USERS_ADDRESS_ID', $postData['address_id']);

            }

            return $this->redirect()->toRoute('userapplication', 
                        ['action' => 'view', 
                        'id' => $postData['application_id']]);
        }
    }

    public function editPersonalInternalMailAddressInfoAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute("userapplication");
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $postData = $request->getPost()->toArray();

            if ($postData['form_type'] == 'internals') {

                $updateData = [
                    'ADDR_TEMP_PROVINCE_ID'=> $postData['addr_temp_province_id'],
                    'ADDR_TEMP_ZONE_ID'=> $postData['addr_temp_zone_id'],
                    'ADDR_TEMP_DISTRICT_ID'     => $postData['addr_temp_district_id'],
                    'ADDR_TEMP_WARD_NO'    => $postData['addr_temp_ward_no'],
                    'ADDR_TEMP_STREET_ADDRESS'       => $postData['addr_temp_street_address'],
                ];

                $this->repository->getUpdateById('HRIS_EMPLOYEES', $updateData, 'EMPLOYEE_ID', $postData['employee_id']);


            } elseif ($postData['form_type'] == 'open') {

                $updateData = [
                    'MAIL_PROVINCE_ID'=> $postData['province'],
                    'MAIL_DISTRICT_ID'=> $postData['district'],
                    'MAIL_VDC_ID'     => $postData['municipality'],
                    'MAIL_WARD_ID'    => $postData['ward'],
                    'MAIL_TOLE'       => $postData['tole'],
                ];

                $this->repository->getUpdateById('HRIS_REC_USERS_ADDRESS', $updateData, 'USERS_ADDRESS_ID', $postData['address_id']);

            }

            return $this->redirect()->toRoute('userapplication', 
                        ['action' => 'view', 
                        'id' => $postData['application_id']]);
        }
    }

    public function editPersonalInternalEduInfoAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0)
        {
            return $this->redirect()->toRoute("userapplication");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {

            $postData = $request->getPost()->toArray();

            if ($postData['form_type'] == 'internal') {

                /**
                 * FOR EDUCATION DATA
                 * */
                for ($i=0; $i < count($postData['level_id']) ; $i++) {

                    $updateData = [
                        'ACADEMIC_PROGRAM_ID'    => $postData['faculty'][$i],
                        'ACADEMIC_DEGREE_ID'     => $postData['level_id'][$i],
                        'ACADEMIC_COURSE_ID'     => $postData['major_subject'][$i],
                        'PASSED_YR'              => $postData['passed_year'][$i],
                        'ACADEMIC_UNIVERSITY_ID' => $postData['university_board'][$i],
                        'RANK_TYPE'              => $postData['rank_type'][$i],
                        'RANK_VALUE'             => $postData['rank_value'][$i],
                        'MODIFIED_DT'            => date('Y-m-d')
                    ];

                    $check_presence = $this->repository->getRowById('HRIS_EMPLOYEE_QUALIFICATIONS', 'ID', $postData['edu_id'][$i]);

                    if ($check_presence) {

                        $this->repository->getUpdateById('HRIS_EMPLOYEE_QUALIFICATIONS', $updateData, 'ID', $postData['edu_id'][$i]);

                    } else {

                        $updateData['EMPLOYEE_ID'] = $postData['employee_id'];
                        $updateData['ACADEMIC_PROGRAM_ID']    = $postData['faculty'][$i];
                        $updateData['ACADEMIC_DEGREE_ID']     = $postData['level_id'][$i];
                        $updateData['ACADEMIC_COURSE_ID']     = $postData['major_subject'][$i];
                        $updateData['PASSED_YR']              = $postData['passed_year'][$i];
                        $updateData['ACADEMIC_UNIVERSITY_ID'] = $postData['university_board'][$i];
                        $updateData['RANK_TYPE']              = $postData['rank_type'][$i];
                        $updateData['RANK_VALUE']             = $postData['rank_value'][$i];
                        $updateData['STATUS'] = 'E';
                        $updateData['CREATED_DT'] = date('Y-m-d');
                        $updateData['MODIFIED_DT'] = '';
                        $updateData['ID'] = ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_QUALIFICATIONS', 'ID')) + 1;

                        $this->repository->insertData('HRIS_EMPLOYEE_QUALIFICATIONS',$updateData);

                    }

                }


            }

            return $this->redirect()->toRoute('userapplication', 
                        ['action' => 'view', 'id' => $postData['application_id']]);
        }
    }

    public function editPersonalInternalExpInfoAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $postData = $request->getPost()->toArray();

            if ($postData['form_type'] == 'internal') {

                echo "<pre>";
                print_r($postData);
                die;

                $org_name     = $postData['org_name'];
                $post_name    = $postData['post_name'];
                $service_name = $postData['service_name'];
                $from_date    = $postData['from_date'];
                $to_date      = $postData['to_date'];

                /**
                 * FOR EXPERIENCE DATA
                 * */

                if ((!empty($org_name[0])) AND (!empty($post_name[0]))) {

                    for ($i=0; $i < count($postData['org_name']) ; $i++) {

                        $updateData = [
                                'ORGANIZATION_NAME' => $postData['org_name'][$i],
                                'POSITION'         => $postData['post_name'][$i],
                                'ORGANIZATION_TYPE' => $postData['service_name'][$i],
                                'FROM_DATE'         => $postData['from_date'][$i],
                                'TO_DATE'           => $postData['to_date'][$i],
                                'MODIFIED_DATE'     => date('Y-m-d')
                            ];

                        $check_presence = $this->repository->getRowById('HRIS_EMPLOYEE_EXPERIENCES', 'ID', $postData['exp_id'][$i]);

                        if ($check_presence) {

                            $this->repository->getUpdateById('HRIS_EMPLOYEE_EXPERIENCES', $updateData, 'ID', $postData['exp_id'][$i]);

                        } else {

                            $updateData = [
                                'ID' =>((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_EXPERIENCES', 'ID')) + 1,
                                'EMPLOYEE_ID' =>  $postData['employee_id'],
                                'STATUS' =>  'E',
                                'ORGANIZATION_NAME'      => $postData['org_name'][$i],
                                'ORGANIZATION_TYPE'      => $postData['org_type'][$i],
                                'POSITION'               => $postData['position'][$i],
                                'FROM_DATE'              => $postData['from_date'][$i],
                                'TO_DATE'                => $postData['to_date'][$i],
                                'CREATED_DATE'            => date('Y-m-d')
                            ];
                           

                            $this->repository->insertData('HRIS_EMPLOYEE_EXPERIENCES',$updateData);
                        }

                    }

                }

            } elseif ($postedData['form_type'] == 'open') {

                $org_name     = $postData['org_name'];
                $post_name    = $postData['post_name'];
                $service_name = $postData['service_name'];
                $org_level    = $postData['org_level'];
                $employee_type= $postData['employee_type'];
                $from_date    = $postData['from_date'];
                $to_date      = $postData['to_date'];

                /**
                 * FOR EDUCATION DATA
                 * */

                if ((!empty($org_name[0])) AND (!empty($post_name[0]))) {

                    for ($i=0; $i < count($postData['org_name']) ; $i++) {

                        $updateData = [
                                'ORGANIZATION_NAME' => $postData['org_name'][$i],
                                'POST_NAME'         => $postData['post_name'][$i],
                                'SERVICE_NAME'      => $postData['service_name'][$i],
                                'ORGANIZATION_TYPE' => $postData['service_name'][$i],
                                'LEVEL_ID'          => $postData['org_level'][$i],
                                'EMPLOYEE_TYPE_ID'  => $postData['employee_type'][$i],
                                'FROM_DATE'         => $postData['from_date'][$i],
                                'TO_DATE'           => $postData['to_date'][$i],
                                'MODIFIED_DATE'     => date('Y-m-d')
                            ];

                        $check_presence = $this->repository->getRowById('HRIS_REC_APPLICATION_EXPERIENCES', 'EXPERIENCE_ID', $postData['exp_id'][$i]);

                        if ($check_presence) {

                            $this->repository->getUpdateById('HRIS_REC_APPLICATION_EXPERIENCES', $updateData, 'EXPERIENCE_ID', $postData['exp_id'][$i]);

                        } else {

                            $updateData = [
                                'EXPERIENCE_ID' =>((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_EXPERIENCES', 'EXPERIENCE_ID')) + 1,
                                'APPLICATION_ID' =>  $postData['application_id'],
                                'USER_ID' =>  $postData['user_id'],
                                'STATUS' =>  'E',
                                'ORGANIZATION_NAME' => $postData['org_name'][$i],
                                'POST_NAME'         => $postData['post_name'][$i],
                                'SERVICE_NAME'      => $postData['service_name'][$i],
                                'ORGANIZATION_TYPE' => $postData['service_name'][$i],
                                'LEVEL_ID'          => $postData['org_level'][$i],
                                'EMPLOYEE_TYPE_ID'  => $postData['employee_type'][$i],
                                'FROM_DATE'         => $postData['from_date'][$i],
                                'TO_DATE'           => $postData['to_date'][$i],
                                'CREATED_DATE'            => date('Y-m-d')
                            ];
                           

                            $this->repository->insertData('HRIS_EMPLOYEE_EXPERIENCES',$updateData);
                        }

                    }

                }


            }

            return $this->redirect()->toRoute('userapplication', 
                        ['action' => 'view', 
                        'id' => $postData['application_id']]);
        }
    }


    public function editPersonalInternalTrainingInfoAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $postData = $request->getPost()->toArray();

            if ($postData['form_type'] == 'internal') {

                $training_name     = $postData['training_name'];
                $certificate      = $postData['certificate'];

                /**
                 * FOR EDUCATION DATA
                 * */

                if ((!empty($training_name[0])) AND (!empty($certificate[0]))) {

                    for ($i=0; $i < count($training_name) ; $i++) {

                        $updateData = [
                                'TRAINING_NAME' => $training_name[$i],
                                'CERTIFICATION' => $postData['certificate'][$i],
                                'DESCRIPTION'   => $postData['description'][$i],
                                'FROM_DATE'     => $postData['from_date'][$i],
                                'TO_DATE'       => $postData['to_date'][$i],
                                'MODIFIED_DATE' => date('Y-m-d')
                            ];

                        $check_presence = $this->repository->getRowById('HRIS_EMPLOYEE_TRAININGS', 'ID', $postData['training_id'][$i]);

                        if ($check_presence) {

                            $this->repository->getUpdateById('HRIS_EMPLOYEE_TRAININGS', $updateData, 'ID', $postData['training_id'][$i]);

                        } else {

                            $updateData = [
                                'ID' =>((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_TRAININGS', 'ID')) + 1,
                                'EMPLOYEE_ID'   =>  $postData['employee_id'],
                                'STATUS'        =>  'E',
                                'TRAINING_NAME' => $training_name[$i],
                                'CERTIFICATION' => $postData['certificate'][$i],
                                'DESCRIPTION'   => $postData['description'][$i],
                                'FROM_DATE'     => $postData['from_date'][$i],
                                'TO_DATE'       => $postData['to_date'][$i],
                                'CREATED_DATE'            => date('Y-m-d')
                            ];
                           

                            $this->repository->insertData('HRIS_EMPLOYEE_TRAININGS',$updateData);
                        }

                    }

                }

            } elseif ($postedData['form_type'] == 'open') {

                $training_name     = $postData['training_name'];
                $certificate      = $postData['certificate'];

                /**
                 * FOR EDUCATION DATA
                 * */

                if ((!empty($training_name[0])) AND (!empty($certificate[0]))) {

                    for ($i=0; $i < count($postData['training_name']) ; $i++) {

                        $updateData = [
                                'TRAINING_NAME' => $training_name[$i],
                                'CERTIFICATE'         => $certificate[$i],
                                'FROM_DATE'         => $postData['from_date'][$i],
                                'TO_DATE'      => $postData['to_date'][$i],
                                'TOTAL_DAYS' =>  ((strtotime($postData['to_date'][$i]) - strtotime($postData['from_date'][$i])) / 60 / 60 / 24),
                                'DESCRIPTION'          => $postData['description'][$i],
                                'MODIFIED_DATE'     => date('Y-m-d')
                            ];

                        $check_presence = $this->repository->getRowById('HRIS_REC_APPLICATION_TRAININGS', 'TRAINING_ID', $postData['training_id'][$i]);

                        if ($check_presence) {

                            $this->repository->getUpdateById('HRIS_REC_APPLICATION_TRAININGS', $updateData, 'TRAINING_ID', $postData['training_id'][$i]);

                        } else {

                            $updateData = [
                                'TRAINING_ID' =>((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_TRAININGS', 'TRAINING_ID')) + 1,
        
                                'APPLICATION_ID' => $postData['application_id'][$i],
                                'USER_ID'        => $postData['user_id'][$i],
                                'TRAINING_NAME' => $training_name[$i],
                                'CERTIFICATE'         => $certificate[$i],
                                'FROM_DATE'         => $postData['from_date'][$i],
                                'TO_DATE'      => $postData['to_date'][$i],
                                'TOTAL_DAYS' =>  ((strtotime($postData['to_date'][$i]) - strtotime($postData['from_date'][$i])) / 60 / 60 / 24),
                                'DESCRIPTION'          => $postData['description'][$i],
                                'STATUS' =>  'E',
                                'CREATED_DATE'            => date('Y-m-d')
                            ];
                           

                            $this->repository->insertData('HRIS_REC_APPLICATION_TRAININGS',$updateData);
                        }

                    }

                }


            }

            return $this->redirect()->toRoute('userapplication', 
                        ['action' => 'view', 
                        'id' => $postData['application_id']]);
        }
    }

    public function editPersonalDocInfoAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {


            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $rec_doc_id  = $postData['rec_doc_id'];
            $folder_name = $postData['doc_folder'];
            $file        = $postData[$folder_name];

            if ($postData['form_type'] == 'internal') {


                if ($file['error'] == 0) { 


                    $ext         =  strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    // FILE UPLOADING
                    $fileName    = pathinfo($file['name'], PATHINFO_FILENAME);
                    $unique      = Helper::generateUniqueName();
                    $newFileName = $postData['application_id'].'_'.$unique . "." . $ext;
                    $path        = 'documents/'.$folder_name.'/';
                    $path_ext    = 'documents/'.$folder_name.'/'.$newFileName;


                    $success = move_uploaded_file($file['tmp_name'], Helper::UPLOAD_DIR . $path_ext);

                    if ($success) {

                        $checkFile = $this->repository->getRowId('HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID', $rec_doc_id);

                        if ($checkFile) {

                            $update = [
                                'DOC_OLD_NAME'  => $file['doc_old_name'],
                                'DOC_NEW_NAME'  => $newFileName,
                                'DOC_PATH'      => $path,
                                'DOC_TYPE'      => $ext,
                                'DOC_FOLDER'    => $folder_name,
                                'MODIFIED_DATE' => date('Y-m-d')
                            ];

                            $this->repository->getUpdateById('HRIS_REC_APPLICATION_DOCUMENTS', $updateData, 'REC_DOC_ID', $rec_doc_id);
                        }
                    }
                }

            } 

                

            return $this->redirect()->toRoute('userapplication', 
                        ['action' => 'edit-application-form', 
                        'id' => $postData['application_id']]);
        }
    }

    public function newPersonalDocFileInfoAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {


            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $folder_name = $postData['document_name'];
            $file        = $postData['file_name'];

            if ($postData['form_type'] == 'internal') {

                if ($file['error'] == 0) { 

                    $ext         =  strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    // FILE UPLOADING
                    $fileName    = pathinfo($file['name'], PATHINFO_FILENAME);
                    $unique      = Helper::generateUniqueName();
                    $newFileName = $postData['application_id'].'_'.$unique . "." . $ext;
                    $path        = 'documents/'.$folder_name.'/';
                    $path_ext    = 'documents/'.$folder_name.'/'.$newFileName;

                    $success = move_uploaded_file($file['tmp_name'], Helper::UPLOAD_DIR . $path_ext);

                    if ($success) {


                        $updateData = [
                            'ID' =>((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID')) + 1,
                            'APPLICATION_ID' =>  $postData['application_id'],
                            'VACANCY_ID'     =>  $postData['vacancy_id'],
                            'USER_ID'        =>  $postData['user_id'],
                            'DOC_OLD_NAME'   => $file['name'],
                            'DOC_NEW_NAME'   => $newFileName,
                            'DOC_TYPE'       => $ext,
                            'DOC_FOLDER'     => $folder_name,
                            'STATUS'         =>  'E',
                            'CREATED_DATE'   => date('Y-m-d')
                        ];
                       

                        $this->repository->insertData('HRIS_REC_APPLICATION_DOCUMENTS',$updateData);
                        
                    }
                }

            } 

            return $this->redirect()->toRoute('userapplication', 
                        ['action' => 'edit-application-form', 
                        'id' => $postData['application_id']]);
        }
    }

    public function importNepaliAction(){
        return;
    }





   




}