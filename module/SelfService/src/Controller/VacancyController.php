<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Custom\CustomViewModel;
use SelfService\Form\InternalApplicationForm;
use Recruitment\Form\RecruitmentVacancyForm;
use SelfService\Repository\VacancyRepository;
use Recruitment\Model\OpeningVacancy;
use Recruitment\Model\RecruitmentVacancy as RecruitmentVacancyModel;
use Recruitment\Model\OptionsModel;
use Recruitment\Repository\VacancyStageRepository;
use Recruitment\Repository\VacancyInclusionRepository;
use Recruitment\Model\VacancyInclusionModel;
use Recruitment\Model\VacancyStageModel;
use Setup\Model\AcademicDegree;
use Setup\Model\Gender;
use Setup\Model\Designation;
use Setup\Model\Department;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Recruitment\Model\SkillModel;
use SelfService\Model\InternalvacancyModel;
use Exception;
use Zend\View\Model\JsonModel;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class VacancyController extends HrisController
{

    protected $stageRepository;

    function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {

        parent::__construct($adapter, $storage);
        $this->initializeRepository(VacancyRepository::class);
        $this->stageRepository = new VacancyStageRepository($this->adapter);
        $this->VacancyInclusionRepository = new VacancyInclusionRepository($this->adapter);
        $this->initializeForm(InternalApplicationForm::class);
        $this->initializeForm(RecruitmentVacancyForm::class);
        date_default_timezone_set("Asia/Kathmandu");

    }

    public function indexAction()
    {

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                // print_r($this->employeeId);die;
                $rawList = $this->repository->getFilteredRecords($data, $this->employeeId);
                $list = iterator_to_array($rawList, false);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

       
        return $this->stickFlashMessagesTo([
            // 'status' => $statusSE,
            // 'Gender' => $GenderSE,
            'qualificationId' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_DEGREES',['ACADEMIC_DEGREE_ID','ACADEMIC_DEGREE_NAME'], ["STATUS" => "E"]),
            'adnumber'        => EntityHelper::getTableList($this->adapter, 'HRIS_REC_VACANCY',['VACANCY_ID','AD_NO'], ["STATUS" => "E"]),            
            'DepartmentId'    => EntityHelper::getTableList($this->adapter, 'HRIS_DEPARTMENTS',['DEPARTMENT_ID','DEPARTMENT_NAME'], ["STATUS" => "E"]),
            'positionId'      => EntityHelper::getTableList($this->adapter, 'HRIS_DESIGNATIONS',['DESIGNATION_ID','DESIGNATION_TITLE'], ["STATUS" => "E"]),
            'openingId'      => EntityHelper::getTableList($this->adapter, 'HRIS_REC_OPENINGS',['OPENING_ID','OPENING_NO'], ["STATUS" => "E"]),
        ]);
    }

    public function viewAction()
    {   
        $request = $this->getRequest();
        if ($request->isPost()) {

            $has_inclusion            = $request->getPost('has_inclusion');
            $inclusion_id             = $request->getPost('inclusion_id');
            $has_inclusion_vacancy_id = $request->getPost('has_vacancy_id');
            $vacancy_opening_date     = $request->getPost('vacancy_opening_date');

            $form_type = ($request->getPost('form_type') == 'INTERNAL_APPRAISAL') ? 'appraisal' : 'form';

            // echo $form_type; die;

            // $has_inclusion = $request->getPost('has_inclusion');
            
            $files = $request->getFiles()->toArray();

            try {

                /**
                 *  CHECKING EMPLOYEE INCLUSION FILE ALREADY UPLOAD OR NOT
                 * */
                $checkEmployeeFile = $this->repository->getEmployeeInclusion($this->storageData['employee_detail']['EMPLOYEE_ID'], $form_type);

                /**
                 * BEFORE UPLOADING FORM OR APPRAISAL THAT MATCHES WITH PREVIOUS INCLUSION ID THEN UPLOADING FILE NOT REQUIRED
                 * 
                 * */
                if ($checkEmployeeFile['INCLUSION_ID_FORM'] !== $checkEmployeeFile['INCLUSION_ID_APPRAISAL']) {


                    /**
                     * UPLOAD FILE ERROR SHOULD BE ZERO
                     * */

                    if ($files['has_inclusion_file']['error'] == 0) {


                        /**
                         * CHECK INCLUSION FILE UPLOAD OR NOT
                         * 
                         * IF PREVIOUSLY UPLOAD INCLUSION THEN REMOVE FIRST PREVIOUS ONE
                         * 
                         * */

                        if ($form_type == 'appraisal') {


                            if ($checkEmployeeFile['INCLUSION_APPRAISAL_FILE_ID'] > 0) {

                                $path = Helper::UPLOAD_DIR . "/documents/Inclusion/" . $checkEmployeeFile['FILE_PATH'];


                                if (file_exists($path)) {

                                    unlink($path);

                                }

                                /**
                                 * DB FIELD INCLUSION_USED_PROCESS CONTAINS STATUS OF USE OF INCLUSION
                                 * 
                                 * FORM OR APPRAISAL USES SAME FIELD
                                 * 
                                 * SO EITHER PRESENCE INCLUSION IN FORM OR APPRAISAL THEN FIELD INCLUSION_USED_PROCESS VALUE SHOULD CHANGED
                                 * OF PREVIOUS VALUE
                                 * 
                                 * eg: IF APPRAISAL INCLUSION SUBMISSION THEN FORM INCLUSION PRESENT SHOULD AND VICE VERSA
                                 * */
                                $inclusion_used_process = ($checkEmployeeFile['INCLUSION_FORM_USED'] == 'Y') ? $checkEmployeeFile['INCLUSION_USED_PROCESS'] : '';
                                $this->repository->removeEmployeeInclusion($empId, $file_id, $form_type, $inclusion_used_process);


                            }

                        }


                        if ($form_type == 'form') {

                            if ($checkEmployeeFile['INCLUSION_FORM_FILE_ID'] > 0) {


                                $path = Helper::UPLOAD_DIR . "/documents/Inclusion/" . $checkEmployeeFile['FILE_PATH'];

                                if (file_exists($path)) {

                                    unlink($path);

                                }

                                /**
                                 * DB FIELD INCLUSION_USED_PROCESS CONTAINS STATUS OF USE OF INCLUSION
                                 * 
                                 * FORM OR APPRAISAL USES SAME FIELD
                                 * 
                                 * SO EITHER PRESENCE INCLUSION IN FORM OR APPRAISAL THEN FIELD INCLUSION_USED_PROCESS VALUE SHOULD CHANGED
                                 * OF PREVIOUS VALUE
                                 * 
                                 * eg: IF APPRAISAL INCLUSION SUBMISSION THEN FORM INCLUSION PRESENT SHOULD AND VICE VERSA
                                 * */
                                $inclusion_used_process = ($checkEmployeeFile['INCLUSION_APPRAISAL_USED'] == 'Y') ? $checkEmployeeFile['INCLUSION_USED_PROCESS'] : '';
                                $this->repository->removeEmployeeInclusion($empId, $file_id, $form_type, $inclusion_used_process);


                            }

                        }

                        $ext =  strtolower(pathinfo($files['has_inclusion_file']['name'], PATHINFO_EXTENSION));


                        if ($ext == 'pdf' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {


                            $fileName = pathinfo($files['has_inclusion_file']['name'], PATHINFO_FILENAME);
                            $unique = Helper::generateUniqueName();
                            $newFileName = $unique . "." . $ext;

                            $success = move_uploaded_file($files['has_inclusion_file']['tmp_name'], Helper::UPLOAD_DIR . "/documents/Inclusion/" . $newFileName);


                            if ($success) {

                                $inclusionFile = array(
                                   'FILE_CODE' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1,
                                   'EMPLOYEE_ID' => $this->storageData['employee_detail']['EMPLOYEE_ID'],
                                   'FILETYPE_CODE' => '',
                                   'FILE_PATH' => $newFileName,
                                   'STATUS'    => 'E',
                                   'CREATED_DT' =>  date('Y-m-d'),
                                   'MODIFIED_DT' => '',
                                   'REMARKS' => '',
                                   'FILE_ID' => '',
                                );

                                $this->repository->insertEmployeeDocuments($inclusionFile);


                                if ($form_type == 'appraisal') {

                                    $employeeInclusionUpdate = array(
                                        'INCLUSION_APPRAISAL_USED' => $has_inclusion,
                                        'INCLUSION_APPRAISAL_USED_DATE' => date('Y-m-d'),
                                        'INCLUSION_ID_APPRAISAL' => $inclusion_id,
                                        'INCLUSION_APPRAISAL_FILE_ID' => $inclusionFile['FILE_CODE'],
                                        'INCLUSION_APPRAISAL_FOR_YEAR' => $vacancy_opening_date
                                    );

                                } else {

                                    $employeeInclusionUpdate = array(
                                        'INCLUSION_FORM_USED' => $has_inclusion,
                                        'INCLUSION_FORM_USED_DATE' => date('Y-m-d'),
                                        'INCLUSION_ID_FORM' => $inclusion_id,
                                        'INCLUSION_FORM_FILE_ID' => $inclusionFile['FILE_CODE'],
                                        'INCLUSION_FORM_FOR_YEAR' => $vacancy_opening_date
                                    );

                                }

                                $this->repository->updateEmployeeInclusion($employeeInclusionUpdate, $this->storageData['employee_detail']['EMPLOYEE_ID']);

                            }

                            return $this->redirect()->toRoute('vacancies', ['action' => 'view', 'id' => $has_inclusion_vacancy_id]);

                        }



                    } else {


                        if ($form_type == 'appraisal') {

                            $employeeInclusionUpdate = array(
                                        'INCLUSION_APPRAISAL_USED' => $has_inclusion,
                                        'INCLUSION_APPRAISAL_USED_DATE' => date('Y-m-d'),
                                        'INCLUSION_ID_APPRAISAL' => $inclusion_id,
                                        'INCLUSION_APPRAISAL_FILE_ID'=> 0,
                                        'INCLUSION_APPRAISAL_FOR_YEAR' => $vacancy_opening_date
                                    );


                        } else {

                            $employeeInclusionUpdate = array(
                                        'INCLUSION_FORM_USED' => $has_inclusion,
                                        'INCLUSION_FORM_USED_DATE' => date('Y-m-d'),
                                        'INCLUSION_ID_FORM' => $inclusion_id,
                                        'INCLUSION_FORM_FILE_ID'=> 0,
                                        'INCLUSION_FORM_FOR_YEAR' => $vacancy_opening_date
                                    );

                        }

                        $this->repository->updateEmployeeInclusion($employeeInclusionUpdate, $this->storageData['employee_detail']['EMPLOYEE_ID']);

                        $this->flashmessenger()->addMessage("Inclusion Updated Successfully");
                        return $this->redirect()->toRoute('vacancies', ['action' => 'view', 'id' => $has_inclusion_vacancy_id]);


                    }

                }


                
            } catch (Exception $e) {

                $this->flashmessenger()->addMessage("Inclusion Cannot Updated ! Something went wrong");
                return $this->redirect()->toRoute('vacancies', ['action' => 'view', 'id' => $has_inclusion_vacancy_id]);
                
            }


           
        }
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {

            return $this->redirect()->toRoute("vacancy");

        }

        $detail     = $this->repository->fetchById($id);
        $inclusion  = Helper::extractDbData($this->VacancyInclusionRepository->fetchById($id));
        $Inclusions =  explode(',', $detail['INCLUSION_ID']);
        $skills     =  explode(',', $detail['SKILL_ID']);


        $model      = new RecruitmentVacancyModel();
        
        $model->exchangeArrayFromDB($detail);
        
        $model->SkillId     = $skills;
        $model->InclusionId = $Inclusions;

        $this->form->bind($model);

        $Vacancy_types     = array("OPEN" => "OPEN", "INTERNAL_FORM" => "INTERNAL-FORM","INTERNAL_APPRAISAL" => "INTERNAL-APPRAISAL", );
        $empId             = $this->employeeId;
        $user_id           = $this->repository->userId($empId);
        $employeeFirstJoin = $this->repository->inclusionAppliedCheck($empId);
        $employeeLastJoin  = $this->repository->inclusionPromoCheck($empId);
        $vacancyApplyStage = $this->repository->checkVacancyStatus($id, $user_id[0]['USER_ID']);
        $applicationApplied= $this->repository->checkVacancyApplicationApplied($id, $user_id[0]['USER_ID'], $vacancyApplyStage[0]['APPLICATION_ID']);

        $getApplicationStage = $this->repository->checkApplicationStages($applicationApplied['STAGE_ID']);

        

        $paymentGateways   = $this->repository->getPaymentGateway();
        $inclusionLists    = $this->repository->getInclusionsAll('internal');
        $employeeInclusionUsedForm = $this->repository->getEmployeeInclusion($empId, 'form');
        $employeeInclusionUsedAppraisal = $this->repository->getEmployeeInclusion($empId, 'appraisal');
        $openingAdInfo     = $this->repository->getOpening($detail['OPENING_ID']);

        /**
         * IF INCLUSION USES AS FEMALE, DALIT , MADESHI ETC
         * THEN ON APPLYING INTERNAL APPRAISEL 
         * THEN EMPLOYEE EXPERIENCE WILL HAVE 1 YEAR PLUS 
         * 
         * eg: employee have 2 years experience then using INCLUSION (IF CAME FROM OPEN INCLUSION ONLY) THEN can apply 3 years 
         * experience of INTERNAL APPRAISEL
         * AP => APPROVED
         * RQ => REQUEST
         * 
         * NOTE: IF INCLUSION USED TO APPLY INTERNAL APPRAISEL AND NOT SELECT THEN INCLUSION RESET AND CAN USE AGAIN FOR NEXT INTERNAL VACANCY
         * 
         * */
        if ($employeeInclusionUsed['INCLUSION_USED_PROCESS'] !== 'AP') {

            // echo date('Y', strtotime($employeeInclusionUsed['IS_INCLUSION_USED_DATE'])); die;
            if ($detail['VACANCY_TYPE'] == 'INTERNAL_APPRAISAL') {

                if (($employeeInclusionUsed['INCLUSION_APPRAISAL_USED'] == 'Y') AND 
                    (date('Y', strtotime($employeeInclusionUsed['INCLUSION_APPRAISAL_USED_DATE'])) == date('Y', strtotime($openingAdInfo)))) {
                        
                        /* SETTING 1 YEAR LESS EXPERIENCE */
                        $detail['MENTIONED_EXPERIENCE'] = $detail['EXPERIENCE'];
                        $detail['EXPERIENCE'] = ($detail['EXPERIENCE'] !== 0) ? ($detail['EXPERIENCE'] - 1) : $detail['EXPERIENCE'];

                }

            }
            
        }

        $inc = 'N';
        
        $employeeFirstJoinDate        = $employeeFirstJoin['JOIN_DATE'];
        $employeeFirstFunctionalLevel = $employeeFirstJoin['FUNCTIONAL_LEVEL_NO'];
        
        if ( $employeeFirstJoin['INCLUSION'] == 'Y' ) {
            
            $inc = 'Y';
        
        } else {
            
            foreach ( $employeeLastJoin as $value ) {
            
                if ( $value['INCLUSION'] == 'Y' ) {

                    $inc = 'Y';

                }
            }

        }

        $curentJob['StartDate']           = $employeeFirstJoinDate;
        $curentJob['FUNCTIONAL_LEVEL_NO'] = $employeeFirstFunctionalLevel;
        $curentJob['INCLUSION'] = $inc;


        if ( count($employeeLastJoin) != null ) {

            foreach ( $employeeLastJoin as $value ) {

                if ( $employeeFirstJoinDate < $value['START_DATE'] ) {

                    $curentJob['StartDate'] = $value['START_DATE'];

                    if ( $value['FUNCTIONAL_LEVEL_NO'] != null ) {
                        
                        $curentJob['FUNCTIONAL_LEVEL_NO'] = $value['FUNCTIONAL_LEVEL_NO'];

                    }

                }
            }
        }

        /* CHECKING DURATION */
        $date                  = date('Y-m-d');
        // $curentJob['DURATION'] =  $date - $curentJob['StartDate'];

        $curentJob['DURATION'] =  $employeeFirstJoin['DURATION'];
        // print_r($curentJob); die;
        $applicantsDocumentInernalForm = [];
        $appliedDataInternalForm       = $this->repository->getInclusions($user_id[0]['USER_ID'], 'Internal-form',$id);

        if ( $appliedDataInternalForm ) {

            $applicationStoredDocumentsInternalForm = $this->repository->getAppliedStoredDocuments($appliedDataInternalForm['aid'], $user_id[0]['USER_ID']);

            foreach ($applicationStoredDocumentsInternalForm as $applicationStoredDocument) {

                if ( $applicationStoredDocument['DOC_FOLDER'] == "Signature " ) {

                    $applicantsDocumentInernalForm['signature'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];

                } elseif ( $applicationStoredDocument['DOC_FOLDER'] == "FingerPrintR" ) {

                    $applicantsDocumentInernalForm['FingerPrintR'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];

                } elseif ( $applicationStoredDocument['DOC_FOLDER'] == "FingerPrintL" ) {

                    $applicantsDocumentInernalForm['FingerPrintL'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];

                } elseif ( $applicationStoredDocument['DOC_FOLDER'] == "CitizenshipF" ) {

                    $applicantsDocumentInernalForm['CitizenshipF'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];

                } elseif ( $applicationStoredDocument['DOC_FOLDER'] == "CitizenshipB" ) {

                    $applicantsDocumentInernalForm['CitizenshipB'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];

                }
            }
        }
        
        

        /**
         * FOR CONNECTIPS FORM DATA
         * 
         * a : APPLICATION ID     v: VACANCY ID
         * 
         * */
        if ($applicationApplied) {

            $connectips_data = [
                
                'merchant_id' => 212,
                'app_id'      => 'NOC-212-REC-1',
                'app_name'    => 'NOC Recruitment System',
                // 'txn_id'      => rand(0, 10000000).time(),
                'txn_id'      => time().'a'.$applicationApplied['APPLICATION_ID'].'v'.$applicationApplied['AD_NO'],
                'txn_date'    => date('d-m-Y'),
                'txn_currency'=> 'NPR',
                'txn_amount'  => $applicationApplied['APPLICATION_AMOUNT'] * 100, // converting into PAISA
                //'txn_amount'  => 2 * 100, // converting into PAISA
                'txn_actual_amount' => $applicationApplied['APPLICATION_AMOUNT'],
                'reference_id'=> 'REF'.rand(0, 10000000).'aid'.$applicationApplied['APPLICATION_ID'].'vid'.$applicationApplied['AD_NO'],
                'remarks'     => 'for payment registration no '. $applicationApplied['REGISTRATION_NO'],
                'particulars' => 'PART-001'

            ];

            $connectips_data['token'] = $this->_generateTokenConnectIPS($connectips_data);
        }

        $connectips_data = ($connectips_data) ? $connectips_data : '';

        return Helper::addFlashMessagesToArray($this, [
            'id' => $id,
            'form' => $this->form,
            'detail' => $detail,
            'user_id' => $user_id[0]['USER_ID'],
            'vacancyApplyStage' => $vacancyApplyStage,
            'curentJob' => $curentJob,
            'ServiceEvents' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_REC_SERVICE_EVENTS_TYPES', 'SERVICE_EVENT_ID', ['SERVICE_EVENT_NAME'], ["STATUS" => "E"], 'SERVICE_EVENT_NAME', "ASC", null, [null => '---'], true),
            'DepartmentList' => EntityHelper::getTableKVListWithSortOption($this->adapter,Department::TABLE_NAME, 'DEPARTMENT_ID', ['DEPARTMENT_NAME'], ["STATUS" => "E"], 'DEPARTMENT_NAME', "ASC", null, [null => '---'], true),           
            'Positions' => EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"], Designation::DESIGNATION_TITLE, "ASC", null, [null => '---'], true),
            'ServiceTypes' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_SERVICE_TYPES', 'SERVICE_TYPE_ID', ['SERVICE_TYPE_NAME'], ["STATUS" => "E"], 'SERVICE_TYPE_NAME', "ASC", null, [null => '---'], true),
            // 'InclusionList' => $InclusionList,
            'LevelList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_FUNCTIONAL_LEVELS', 'FUNCTIONAL_LEVEL_ID', ['FUNCTIONAL_LEVEL_EDESC'], ["STATUS" => "E"], 'FUNCTIONAL_LEVEL_EDESC', "ASC", null, [null => '---'], true),
            'QualificationList' => EntityHelper::getTableKVListWithSortOption($this->adapter,'HRIS_ACADEMIC_DEGREES', 'ACADEMIC_DEGREE_ID', ['ACADEMIC_DEGREE_NAME'], ["STATUS" => "E"], 'ACADEMIC_DEGREE_NAME', "ASC", null, [null => '---'], true),
            // 'OpeningVacancyNo' => $OpeningVacancyNo,
            'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
            'Vacancy_types' => $Vacancy_types,
            'InclusionList' => EntityHelper::getTableKVListWithSortOption($this->adapter, OptionsModel::TABLE_NAME, OptionsModel::OPTION_ID, [OptionsModel::OPTION_EDESC], ["STATUS" => "E"], OptionsModel::OPTION_EDESC, "ASC", null, [null => '---'], true),
            'Skills' => EntityHelper::getTableKVListWithSortOption($this->adapter, SkillModel::TABLE_NAME, SkillModel::SKILL_ID, [SkillModel::SKILL_NAME], ["STATUS" => "E"], SkillModel::SKILL_NAME, "ASC", null, [null => '---'], true),
            'employeeDetail' => $this->storageData['employee_detail'],
            'applicantsDocumentInernalForm' => $applicantsDocumentInernalForm,
            'applicationApplied'=> $applicationApplied,
            'paymentGateways' => $paymentGateways,
            'connectIpsData' => $connectips_data,
            'baseurl' => $this->getRequest()->getBasePath(),
            'inclusionLists' => $inclusionLists,
            'employeeInclusionUsedForm'=>$employeeInclusionUsedForm,
            'employeeInclusionUsedAppraisal'=>$employeeInclusionUsedAppraisal,
            'openingAdInfo'=>$openingAdInfo,
            'getApplicationStage'=> $getApplicationStage
            // 'detail' => $detail
        ]);
    }

    private function _generateTokenConnectIPS($data) 
    {
        ini_set('display_errors', '1');
        date_default_timezone_set("Asia/Kathmandu");
        // sessionCheck(); 

        if ( isset($data['token_for']) && $data['token_for'] == 'verify' ) {
            
            $string = "MERCHANTID=".$data['merchant_id'].",APPID=".$data['app_id'].",REFERENCEID=".$data['txn_id'].",TXNAMT=".$data['txn_amount'];
        
        } else {

            $string  = "MERCHANTID=".$data['merchant_id'].",APPID=".$data['app_id'].",APPNAME=".$data['app_name'].",TXNID=".$data['txn_id'].",TXNDATE=".$data['txn_date'].",TXNCRNCY=".$data['txn_currency'].",TXNAMT=".$data['txn_amount'].",REFERENCEID=".$data['reference_id'].",REMARKS=".$data['remarks'].",PARTICULARS=".$data['particulars'].",TOKEN=TOKEN";



        }
        

        $hash = hash('sha256', $string);


        if (!$cert_store = file_get_contents("CREDITOR/NOC.pfx")) {
            echo "Error: Unable to read the cert file\n";
            exit;
        }

        

        if (openssl_pkcs12_read($cert_store, $cert_info, "N0c@c3rt")) 
        {
           
            if($private_key = openssl_pkey_get_private($cert_info['pkey']))
            {
                $array = openssl_pkey_get_details($private_key);
                // print_r($array);
            }

        } else {

            echo "Error: Unable to read the cert store.\n";
            exit;

        }

        $hash = "";

        if (openssl_sign($string, $signature , $private_key, "sha256WithRSAEncryption"))
        {
           
            $hash = base64_encode($signature);
            openssl_free_key($private_key);

        } else {
            
            echo "Error: Unable openssl_sign";
            exit;

        } 
      // echo $hash; die;

        return $hash;

    }

    public function applyAction()
    {
        $request = $this->getRequest();    
        
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancies");
        }        
        $detail = $this->repository->InternalVacancyData($id);
        $eid = (int) $this->employeeId;
        $detail['EMPLOYEE_ID'] = $eid;
        $employeeData = $this->repository->empData($eid);
        $EducationData = $this->repository->empEdu($eid);
        $certificates = $this->repository->academicCertificates($detail['CODE']);
        $EducationData = Helper::extractDbData($EducationData);
        $regno = $this->repository->getRegNo($detail['VACANCY_ID']);
        $detail['form_no'] = $detail['AD_NO'].'-'.($regno['APP_ID']+1);
        $Inclusions =  explode(',', $detail['INCLUSION_ID']);
        foreach($Inclusions as $Inclusion){
            $inclusions[] = ($this->repository->fetchInclusionById($Inclusion[0]));
        }

        $documentsEducation = $this->repository->eduDocuments($eid);
        $user_id = $this->repository->userId($eid);
        // var_dump($user_id[0]['USER_ID']);die;
        $existingDocuments = [];
        foreach ($documentsEducation as $document) {
            $existingDocuments[] = array($document['FILE_NAME']=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']); 
        }
        if ($request->isPost()) {
            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            ); 

            $this->form->setData($request->getPost());
            $storingDocumentDatas = [];
            // echo '<pre>'; print_r($postData);die;
            // print_r(implode('_',explode(' ','higher Secondary School'))); die;
            // foreach($certificates as $certificate){
            //     $degree = '';
            //     $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData[implode('_',explode(' ',$certificate['ACADEMIC_DEGREE_NAME']))], implode('_',explode(' ',$certificate['ACADEMIC_DEGREE_NAME'])).'_Certificate');
            // }
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['right_finger_scan'], 'FingerPrintR');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['left_finger_scan'], 'FingerPrintL');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['front_citizen'], 'CitizenshipF');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['back_citizen'], 'CitizenshipB');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['signature'], 'Signature');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_trascript'], 'qualification_trascript');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_character'], 'qualification_character');
            if($postData['qualification_equivalent']['name']){
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_equivalent'], 'qualification_equivalent');
            }
            if($postData['qualification_council']['name']){
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_council'], 'qualification_council');
            }
            if($postData['qualification_license']['name']){
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_license'], 'qualification_license');
            }
            $profilePicDatas = [];

            if($postData['profile_pic2']['name']){
                $profilePicDatas[] = Helper::uploadFilesVacancy($postData['profile_pic2'], 'profile_pic2');
            }
            if($postData['profile_pic']['name']){
                $profilePicDatas[] = Helper::uploadFilesVacancy($postData['profile_pic'], 'profile_pic');
            }

            // echo '<pre>'; print_r($profilePicDatas); die;
            // $profilePicUploadPath = $this->basePath();
            foreach ($profilePicDatas as $profilePic) {
                $movingPathPp = getcwd().'/public/uploads/'.$profilePic['newImageName'];
            // print_r($this->employeeId);die;
                move_uploaded_file( $profilePic['tmp_name'], $movingPathPp);
                $this->repository->updateProfilePic($profilePic, $this->employeeId);
             }

            $incs = implode(',',$postData['inclusion']);
            $data['hris_personal'] = array(
                'PERSONAL_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_PERSONAL', 'PERSONAL_ID')) + 1,
                'APPLICATION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_VACANCY_APPLICATION', 'APPLICATION_ID')) + 1,
                'USER_ID' => $user_id[0]['USER_ID'],
                'INCLUSION_ID' => $incs,
                'CREATED_DATE' =>  date('Y-m-d'),
            );
            $this->repository->insertPersonal($data['hris_personal']);

            foreach ($storingDocumentDatas as $storingDocumentData) {
                $documents = array(
                     'REC_DOC_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID')) + 1,
                     'APPLICATION_ID' => $data['hris_personal']['APPLICATION_ID'],
                     'VACANCY_ID' => $postData['ad_no'],
                     'USER_ID'=> $user_id[0]['USER_ID'],
                     'DOC_OLD_NAME' => $storingDocumentData['imageName'],
                     'DOC_NEW_NAME' => $storingDocumentData['newImageName'],
                     'DOC_PATH' => $storingDocumentData['path'],
                     'DOC_FOLDER' => $storingDocumentData['folder'],
                     'DOC_TYPE' => $storingDocumentData['extension'],
                     'CREATED_DATE' =>  date('Y-m-d'),
                     'STATUS' => 'E',
                 );
                //  if ($storingDocumentData['folder'] != 'FingerPrintR' &&
                //  $storingDocumentData['folder'] != 'FingerPrintL' &&
                //  $storingDocumentData['folder'] != 'CitizenshipF' &&
                //  $storingDocumentData['folder'] != 'CitizenshipB' && $storingDocumentData['folder'] != 'Signature') {
                //     $filecode = $this->repository->fileType($storingDocumentData['extension']);

                //     $fileSetId = $this->repository->fileSetId($storingDocumentData['folder']);
                //     // var_dump($storingDocumentData['folder']);die;
                //      $empFile = array(
                //        'FILE_CODE' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1,
                //        'EMPLOYEE_ID' => $eid,
                //        'FILETYPE_CODE' => $filecode[0]['FILETYPE_CODE'],
                //        'FILE_PATH' => $storingDocumentData['newImageName'],
                //        'STATUS'    => 'E',
                //        'CREATED_DT' =>  date('Y-m-d'),
                //        'FILE_ID' => $fileSetId[0]['FILE_ID'],
                //        'MODIFIED_DT' => '',
                //        'REMARKS' => '',
                //       );
                //     move_uploaded_file( $storingDocumentData['tmp_name'], $storingDocumentData['movingPath']);
                //     // echo('<pre>');print_r($storingDocumentData['tmp_name']);print_r($storingDocumentData['movingPath']);die;
                //     move_uploaded_file( $storingDocumentData['tmp_name'], $storingDocumentData['empFilePath']);
                //     $this->repository->insertEmployeeDocuments($empFile);
                //  }
                
                 move_uploaded_file( $storingDocumentData['tmp_name'], $storingDocumentData['movingPath']);
                 $this->repository->insertDocuments($documents);
             }
            //  var_dump('$filesStoring');die;
            $data['hris_application'] = array(
                'APPLICATION_ID' => $data['hris_personal']['APPLICATION_ID'],
                'USER_ID' => $user_id[0]['USER_ID'],
                'AD_NO'   => $postData['ad_no'],
                'REGISTRATION_NO' => $postData['farm_no'],
                'STAGE_ID' => 2,
                'APPLICATION_AMOUNT' => $postData['inclusion_amount'],
                'STATUS' => 'E',
                'CREATED_DATE' =>  date('Y-m-d'),
                'APPLICATION_TYPE' => 'Internal-form'
            );          

            // var_dump($data['hris_application']); die;
            $this->repository->insertApplication($data['hris_application']);
            $eduCount = count($postData['level_id']);
            if ($eduCount > 0) {
                for($i=0; $i < $eduCount; $i++){
                    // $eduData = array(
                    //     'EDUCATION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_EDUCATION', 'EDUCATION_ID')) + 1,
                    //     'APPLICATION_ID' =>$data['hris_personal']['APPLICATION_ID'],
                    //     'USER_ID' =>  $user_id[0]['USER_ID'],
                    //     'AD_NO' => $postData['ad_no'],
                    //     'EDUCATION_INSTITUTE' => $postData['edu_institute'][$i],
                    //     'LEVEL_ID' => $postData['level_id'][$i],
                    //     'FACALTY' => $postData['faculty'][$i],
                    //     'RANK_TYPE' => $postData['rank_type'][$i],
                    //     'RANK_VALUE' => $postData['rank_value'][$i],
                    //     'MAJOR_SUBJECT' => $postData['major_subject'][$i],
                    //     'PASSED_YEAR' => $postData['passed_year'][$i],
                    //     'STATUS' => 'E',
                    //     'CREATED_DATE' =>  date('Y-m-d'),
                    //     'UNIVERSITY_BOARD' =>  $postData['univerity_board'][$i]
                    // );
                    $eduEmpData = array(
                        'ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_QUALIFICATIONS', 'ID')) + 1,
                        'EMPLOYEE_ID' => $eid,
                        'ACADEMIC_PROGRAM_ID' => $postData['faculty'][$i],
                        'ACADEMIC_DEGREE_ID' => $postData['level_id'][$i],
                        'ACADEMIC_COURSE_ID' => $postData['major_subject'][$i],
                        'ACADEMIC_UNIVERSITY_ID' => $postData['univerity_board'][$i],
                        'RANK_TYPE' => $postData['rank_type'][$i],
                        'RANK_VALUE' => $postData['rank_value'][$i],
                        'PASSED_YR' => $postData['passed_year'][$i],
                        'STATUS' => 'E',
                        'CREATED_DT' =>  date('Y-m-d'),
                    );
                    // echo '<pre>'; print_r($eduData); die;
                    // $this->repository->insertEdu($eduData);
                    $this->repository->insertEmpEdu($eduEmpData);

                }     
            }
                 
            // echo '<pre>'; print_r($data); die;
            $this->flashmessenger()->addMessage("Vacancy Successfully Applied!!!");
            return $this->redirect()->toRoute("vacancies");
        }

        // echo '<pre>'; print_r($existingDocuments); die;
    
        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'detail' => $detail,
                    'inclusions' => $inclusions,
                    'EducationData' => $EducationData,
                    'EmployeeData' => $employeeData,           
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    'messages' => $this->flashmessenger()->getMessages(),
                   'certificates'=> $certificates,
                   'existingDocuments' => $existingDocuments,
                   'eduDegrees' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_DEGREES', ['ACADEMIC_DEGREE_ID' => "ACADEMIC_DEGREE_ID", 'ACADEMIC_DEGREE_NAME'=>"ACADEMIC_DEGREE_NAME"], ["STATUS" => "E"]),
                   'eduFaculty' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_PROGRAMS', ['ACADEMIC_PROGRAM_ID' => "ACADEMIC_PROGRAM_ID", 'ACADEMIC_PROGRAM_NAME'=>"ACADEMIC_PROGRAM_NAME"], ["STATUS" => "E"]),
                   'eduUniversity' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_UNIVERSITY', ['ACADEMIC_UNIVERSITY_ID' => "ACADEMIC_UNIVERSITY_ID", 'ACADEMIC_UNIVERSITY_NAME'=>"ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => "E"]),
                   'eduCourses' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_COURSES', ['ACADEMIC_COURSE_ID' => "ACADEMIC_COURSE_ID", 'ACADEMIC_COURSE_NAME'=>"ACADEMIC_COURSE_NAME"], ["STATUS" => "E"]),
                ]
            )
        );
    }

    public function perfomanceAction()
    {
        // print_r('asdf');die;
        $request = $this->getRequest();
        $postData = $request->getPost();



        $vacancy_id = (int) $this->params()->fromRoute('id');
        $user_id = $this->repository->userId($this->employeeId);
        $detail = $this->repository->InternalVacancyData($vacancy_id);
        $regno = $this->repository->getRegNo($detail['VACANCY_ID']);
        $detail['form_no'] = $detail['AD_NO'].'-'.($regno['APP_ID']+1);
        $Inclusions =  explode(',', $detail['INCLUSION_ID']);
       
        foreach($Inclusions as $Inclusion) {

            $inclusions[] = ($this->repository->fetchInclusionById($Inclusion[0]));
        
        }
        
        // var_dump($detail);die;

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            ); 


            $this->form->setData($request->getPost());
            // echo('<pre>');print_r($postData);die;
            $eduCount = count($postData['level_id']);
            if ($eduCount > 0) {
                for($i=0; $i < $eduCount; $i++){
                    $eduEmpData = array(
                        'ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_QUALIFICATIONS', 'ID')) + 1,
                        'EMPLOYEE_ID' => $this->employeeId,
                        'ACADEMIC_PROGRAM_ID' => $postData['faculty'][$i],
                        'ACADEMIC_DEGREE_ID' => $postData['level_id'][$i],
                        'ACADEMIC_COURSE_ID' => $postData['major_subject'][$i],
                        'ACADEMIC_UNIVERSITY_ID' => $postData['univerity_board'][$i],
                        'RANK_TYPE' => $postData['rank_type'][$i],
                        'RANK_VALUE' => $postData['rank_value'][$i],
                        'PASSED_YR' => $postData['passed_year'][$i],
                        'STATUS' => 'E',
                        'CREATED_DT' =>  date('Y-m-d'),
                    );
                    // echo '<pre>'; print_r($eduEmpData); die;
                    // $this->repository->insertEdu($eduData);
                     $this->repository->insertEmpEdu($eduEmpData);

                }     
            }

            $storingDocumentDatas = [];
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['right_finger_scan'], 'FingerPrintR');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['left_finger_scan'], 'FingerPrintL');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['front_citizen'], 'CitizenshipF');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['back_citizen'], 'CitizenshipB');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['signature'], 'Signature');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_trascript'], 'qualification_trascript');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_character'], 'qualification_character');

            
            if($postData['qualification_equivalent']['name']){
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_equivalent'], 'qualification_equivalent');
            }
            if($postData['qualification_council']['name']){
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_council'], 'qualification_council');
            }
            if($postData['qualification_license']['name']){
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_license'], 'qualification_license');
            }
            $profilePicDatas = [];

            if($postData['profile_pic2']['name']){
                $profilePicDatas[] = Helper::uploadFilesVacancy($postData['profile_pic2'], 'profile_pic2');
            }
            if($postData['profile_pic']['name']){
                $profilePicDatas[] = Helper::uploadFilesVacancy($postData['profile_pic'], 'profile_pic');
            }

            foreach ($profilePicDatas as $profilePic) {
                $movingPathPp = getcwd().'/public/uploads/'.$profilePic['newImageName'];
                move_uploaded_file( $profilePic['tmp_name'], $movingPathPp);
                $this->repository->updateProfilePic($profilePic, $this->employeeId);
             }

            $incs = implode(',',$postData['inclusion']);

            // echo('<pre>');print_r($postData);die;

            foreach ($storingDocumentDatas as $storingDocumentData) {
                $documents = array(
                     'REC_DOC_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID')) + 1,
                     'APPLICATION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_VACANCY_APPLICATION', 'APPLICATION_ID')) + 1,
                     'VACANCY_ID' => $vacancy_id,
                     'USER_ID'=> $user_id[0]['USER_ID'],
                     'DOC_OLD_NAME' => $storingDocumentData['imageName'],
                     'DOC_NEW_NAME' => $storingDocumentData['newImageName'],
                     'DOC_PATH' => $storingDocumentData['path'],
                     'DOC_FOLDER' => $storingDocumentData['folder'],
                     'DOC_TYPE' => $storingDocumentData['extension'],
                     'CREATED_DATE' =>  date('Y-m-d'),
                     'STATUS' => 'E',
                 );
                
                 move_uploaded_file( $storingDocumentData['tmp_name'], $storingDocumentData['movingPath']);
                 $this->repository->insertDocuments($documents);
             }
            $this->form->setData($request->getPost());
            $data['hris_personal'] = array(
                'PERSONAL_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_PERSONAL', 'PERSONAL_ID')) + 1,
                'APPLICATION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_VACANCY_APPLICATION', 'APPLICATION_ID')) + 1,
                'USER_ID' => $user_id[0]['USER_ID'],
                'INCLUSION_ID' => $incs,
                'CREATED_DATE' =>  date('Y-m-d'),
            );
            $this->repository->insertPersonal($data['hris_personal']);
                $data = [
                    'APPLICATION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_VACANCY_APPLICATION', 'APPLICATION_ID')) + 1,
                    'USER_ID' => $user_id[0]['USER_ID'],
                    'AD_NO' =>  $vacancy_id,
                    'REGISTRATION_NO' => $detail['form_no'],
                    'STAGE_ID' => 2,
                    'APPLICATION_AMOUNT' => $postData['inclusion_amount'],
                    'STATUS' => 'E',
                    'CREATED_DATE' =>  date('Y-m-d'),
                    'APPLICATION_TYPE' => 'Internal-performance'
                ];
                $this->repository->insertApplication($data);
                // echo '<pre>'; print_r($_POST); die;
                $this->flashmessenger()->addMessage("Vacancy Applied Successfully !");
                return $this->redirect()->toRoute("vacancies");
        }

        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancy");
        }        
        $detail = $this->repository->InternalVacancyData($id);

        $eid = (int) $this->employeeId;
        $employeeData = $this->repository->empData($eid);
        // Education Data
        $EducationData = $this->repository->empEdu($eid);
        $EducationData = Helper::extractDbData($EducationData);
        $eduFirst = $EducationData[0];
        $eduLast = end($EducationData);
        // Job History Data
        $JobData = $this->repository->empjob($eid);
        $JobData = Helper::extractDbData($JobData);
        $JobEnd = $JobData[0];
        $JobStart = end($JobData);
        // this is casual leave
        $casLeaveEarlier = $this->repository->casLeaveEarlier($eid);
        $casLeaveLater = $this->repository->casLeaveLater($eid);

        // var_dump($casLeave[0]['TOTALLEAVE']);die;
        // print_r($detail);die;
        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'casLeaveEarlier' => $casLeaveEarlier[0]['TOTALLEAVE'],
                    'casLeaveLater' => $casLeaveLater[0]['TOTALLEAVE'],
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'detail' => $detail,
                    'JobStart' => $JobStart,
                    'JobEnd' => $JobEnd,
                    'EducationData' => $EducationData,
                    'eduFirst'  => $eduFirst,
                    'eduLast'  => $eduLast,
                    'EmployeeData' => $employeeData,           
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    'messages' => $this->flashmessenger()->getMessages(),
                    'eduDegrees' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_DEGREES', ['ACADEMIC_DEGREE_ID' => "ACADEMIC_DEGREE_ID", 'ACADEMIC_DEGREE_NAME'=>"ACADEMIC_DEGREE_NAME"], ["STATUS" => "E"]),
                    'eduFaculty' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_PROGRAMS', ['ACADEMIC_PROGRAM_ID' => "ACADEMIC_PROGRAM_ID", 'ACADEMIC_PROGRAM_NAME'=>"ACADEMIC_PROGRAM_NAME"], ["STATUS" => "E"]),
                    'eduUniversity' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_UNIVERSITY', ['ACADEMIC_UNIVERSITY_ID' => "ACADEMIC_UNIVERSITY_ID", 'ACADEMIC_UNIVERSITY_NAME'=>"ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => "E"]),
                    'eduCourses' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_COURSES', ['ACADEMIC_COURSE_ID' => "ACADEMIC_COURSE_ID", 'ACADEMIC_COURSE_NAME'=>"ACADEMIC_COURSE_NAME"], ["STATUS" => "E"]),
                    'inclusions' => $inclusions,
                ]
            )
        );
    }


    public function updateAction() 
    {
        $request = $this->getRequest();    
        
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancies");
        }        
        $detail = $this->repository->InternalVacancyData($id);
        
        $eid = (int) $this->employeeId;
        $detail['EMPLOYEE_ID'] = $eid;
        $employeeData = $this->repository->empData($eid);
        $EducationData = $this->repository->empEdu($eid);
        // var_dump($EducationData);
        $certificates = $this->repository->academicCertificates($detail['CODE']);
        $EducationData = Helper::extractDbData($EducationData);
        $regno = $this->repository->getRegNo($detail['VACANCY_ID']);
        $detail['form_no'] = $detail['AD_NO'].'-'.($regno['APP_ID']+1);
        $Inclusions =  explode(',', $detail['INCLUSION_ID']);
        foreach($Inclusions as $Inclusion){
            $inclusions[] = ($this->repository->fetchInclusionById($Inclusion[0]));
        }
        
        $documentsEducation = $this->repository->eduDocuments($eid);
        $user_id = $this->repository->userId($eid);
        // var_dump($user_id[0]['USER_ID']);die;
        $existingDocuments = [];
        foreach ($documentsEducation as $document) {
            $existingDocuments[] = array($document['FILE_NAME']=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);

            // if ($document['FILE_NAME'] == 'SLC Certificate') {
            //     $existingDocuments[] = array("SLC"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }
            // if ($document['FILE_NAME'] == 'Intermediate Certificate') {
            //     $existingDocuments[] = array("Intermediate"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }    
            // if ($document['FILE_NAME'] == 'Bachelor Certificate') {
            //     $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }       
            // if ($document['FILE_NAME'] == 'Master Certificate') {
            //     $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // } 
            // if ($document['FILE_NAME'] == 'M.Phil Certificate') {
            //     $existingDocuments[] = array("M.Phil"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }      
            // if ($document['FILE_NAME'] == 'P.HD. Certificate') {
            //     $existingDocuments[] = array("PHD"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }  
        }
        if ($request->isPost()) {
            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            ); 
            
            $this->form->setData($request->getPost());
              
            $storingDocumentDatas = [];
            // echo '<pre>'; print_r($postData); die;
            foreach($certificates as $certificate){
                $degree = '';
                
               if ($postData[implode('_',explode(' ',ucfirst($certificate['ACADEMIC_DEGREE_NAME'])))]['name'] != null) {
                    $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData[implode('_',explode(' ',ucfirst($certificate['ACADEMIC_DEGREE_NAME'])))], implode('_',explode(' ',$certificate['ACADEMIC_DEGREE_NAME'])).'_Certificate');
               } 
                // echo '<pre>'; print_r($storingDocumentDatas); die;
            }
            // echo('<pre>');print_r($storingDocumentDatas);die;
            if ($postData['right_finger_scan']['name'] != "") {
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['right_finger_scan'], 'CitizenshipR');
            }
            if ($postData['left_finger_scan']['name'] != "") {
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['left_finger_scan'], 'CitizenshipL');
            } if ($postData['signature']['name'] != "") {
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['signature'], 'Signature');
            }

            // echo('<pre>');print_r($storingDocumentDatas); die;
            $incs = implode(',',$postData['inclusion']);

            $data['hris_personal'] = array(
                'INCLUSION_ID' => $incs,
                'MODIFIED_DATE' =>  date('Y-m-d'),  
            );
            $application_id = $this->repository->updatePersonal($data['hris_personal'], $postData['ad_no'],$user_id[0]['USER_ID']  );
            foreach ($storingDocumentDatas as $storingDocumentData) {
                $document = array(
                     'DOC_OLD_NAME' => $storingDocumentData['imageName'],
                     'DOC_NEW_NAME' => $storingDocumentData['newImageName'],
                     'DOC_PATH' => $storingDocumentData['path'],
                     'DOC_FOLDER' => $storingDocumentData['folder'],
                     'DOC_TYPE' => $storingDocumentData['extension'],
                     'MODIFIED_DATE' =>  date('Y-m-d'),
                     'STATUS' => 'E',
                 );
                 if ($storingDocumentData['folder'] != 'CitizenshipR' && $storingDocumentData['folder'] != 'Signature' && $storingDocumentData['folder'] != 'CitizenshipL') {
                    $fileSetId = $this->repository->fileSetId($storingDocumentData['folder']);
                    // var_dump($storingDocumentData['folder']);die;
                     $empFile = array(
                       'FILE_PATH' => $storingDocumentData['newImageName'],
                       'STATUS'    => 'E',
                       'MODIFIED_DT' =>  date('Y-m-d'),
                      );
                    //   echo '<pre>'; print_r($storingDocumentData); die;
                    move_uploaded_file($storingDocumentData['tmp_name'], $storingDocumentData['movingPath']);
                    move_uploaded_file($storingDocumentData['tmp_name'], $storingDocumentData['empFilePath']);
                    $this->repository->updateEduDocuments($empFile, $fileSetId[0]['FILE_ID'], $eid);
                 }
                $this->repository->updateDocuments($document, $storingDocumentData['folder'], $user_id[0]['USER_ID'], $id);
                move_uploaded_file( $storingDocumentData['tmp_name'], $storingDocumentData['movingPath']);
             }
             $data['hris_application'] = array(
                'REGISTRATION_NO' => $postData['farm_no'],
                'APPLICATION_AMOUNT' => $postData['inclusion_amount'],
                'MODIFIED_DATE' =>  date('Y-m-d'),
            );            
            $this->repository->updateApplication($data['hris_application'], $id, $user_id[0]['USER_ID']);
            $eduCount = count($postData['edu_institute']);
            // echo '<pre>'; print_r($postData);die;
            if ($eduCount > 0) {
                for($i=0; $i < $eduCount; $i++){
                    $eduData = array(
                        'EDUCATION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_EDUCATION', 'EDUCATION_ID')) + 1,
                        'APPLICATION_ID' =>$application_id,
                        'USER_ID' =>  $user_id[0]['USER_ID'],
                        'AD_NO' => $postData['ad_no'],
                        'EDUCATION_INSTITUTE' => $postData['edu_institute'][$i],
                        'LEVEL_ID' => $postData['level_id'][$i],
                        'FACALTY' => $postData['faculty'][$i],
                        'RANK_TYPE' => $postData['rank_type'][$i],
                        'RANK_VALUE' => $postData['rank_value'][$i],
                        'MAJOR_SUBJECT' => $postData['major_subject'][$i],
                        'PASSED_YEAR' => $postData['passed_year'][$i],
                        'STATUS' => 'E',
                        'CREATED_DATE' =>  date('Y-m-d'),
                        'UNIVERSITY_BOARD' =>  $postData['univerity_board'][$i]
                    );
                    $eduEmpData = array(
                        'ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_QUALIFICATIONS', 'ID')) + 1,
                        'EMPLOYEE_ID' => $eid,
                        'ACADEMIC_PROGRAM_ID' => $postData['faculty'][$i],
                        'ACADEMIC_DEGREE_ID' => $postData['level_id'][$i],
                        'ACADEMIC_COURSE_ID' => $postData['major_subject'][$i],
                        'ACADEMIC_UNIVERSITY_ID' => $postData['univerity_board'][$i],
                        'RANK_TYPE' => $postData['rank_type'][$i],
                        'RANK_VALUE' => $postData['rank_value'][$i],
                        'PASSED_YR' => $postData['passed_year'][$i],
                        'STATUS' => 'E',
                        'CREATED_DT' =>  date('Y-m-d'),
                    );
                    // echo '<pre>'; print_r($eduData); die;
                    $this->repository->insertEdu($eduData);
                    $this->repository->insertEmpEdu($eduEmpData);

                }     
            }
           
            $this->flashmessenger()->addMessage("Vacancy Successfully Updated!!!");
            return $this->redirect()->toRoute("vacancies");
        }
        
        $appliedData = $this->repository->getInclusions($user_id[0]['USER_ID'], 'Internal-form',$id);
        $inclusionIds = (explode(',',$appliedData['application_personal'][0]['INCLUSION_ID']));
        $applicationAmount = $appliedData['application'][0]['APPLICATION_AMOUNT'];

        $applicationStoredDocuments = $this->repository->getAppliedStoredDocuments($appliedData['aid'], $user_id[0]['USER_ID']);
        $applicantsDocument = [];
        foreach ($applicationStoredDocuments as $applicationStoredDocument) {
            if ($applicationStoredDocument['DOC_FOLDER'] == "Signature") {
                $applicantsDocument['signature'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "CitizenshipR") {
                $applicantsDocument['CitizenshipR'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "CitizenshipL") {
                $applicantsDocument['CitizenshipL'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }
        }
        // print_r($certificates);die;
        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'detail' => $detail,
                    'inclusions' => $inclusions,
                    'EducationData' => $EducationData,
                    'EmployeeData' => $employeeData,           
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    'messages' => $this->flashmessenger()->getMessages(),
                   'certificates'=> $certificates,
                   'existingDocuments' => $existingDocuments,
                   'inclusionIds' => $inclusionIds,
                   'application_amount' => $applicationAmount,
                   'applicantsDocument' => $applicantsDocument,
                   'eduDegrees' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_DEGREES', ['ACADEMIC_DEGREE_ID' => "ACADEMIC_DEGREE_ID", 'ACADEMIC_DEGREE_NAME'=>"ACADEMIC_DEGREE_NAME"], ["STATUS" => "E"]),
                   'eduFaculty' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_PROGRAMS', ['ACADEMIC_PROGRAM_ID' => "ACADEMIC_PROGRAM_ID", 'ACADEMIC_PROGRAM_NAME'=>"ACADEMIC_PROGRAM_NAME"], ["STATUS" => "E"]),
                   'eduUniversity' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_UNIVERSITY', ['ACADEMIC_UNIVERSITY_ID' => "ACADEMIC_UNIVERSITY_ID", 'ACADEMIC_UNIVERSITY_NAME'=>"ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => "E"]),
                   'eduCourses' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_COURSES', ['ACADEMIC_COURSE_ID' => "ACADEMIC_COURSE_ID", 'ACADEMIC_COURSE_NAME'=>"ACADEMIC_COURSE_NAME"], ["STATUS" => "E"]),
                ]
            )
        );
    }

    // AJAX Call method (apply.js) for inclusion Amount in Internal Vacancy
    public function inclusionamountAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // print_r($data);die;
            $returnData = $this->repository->inclusionamount($data['level_id'],$data['position_id']);

            return new JsonModel(['success' => true, 'data' => $returnData[0], 'message' => null,'here' => 'here']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function viewPerformanceFormAction()
    {
        $request = $this->getRequest();
        $postData = $request->getPost();

        $vacancy_id = (int) $this->params()->fromRoute('id');
        $user_id = $this->repository->userId($this->employeeId);
        $detail = $this->repository->InternalVacancyData($vacancy_id);
        $regno = $this->repository->getRegNo($detail['VACANCY_ID']);
        $detail['form_no'] = $detail['AD_NO'].'-'.($regno['APP_ID']+1);
        $Inclusions =  explode(',', $detail['INCLUSION_ID']);
        foreach($Inclusions as $Inclusion){
            $inclusions[] = ($this->repository->fetchInclusionById($Inclusion[0]));
        }
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancy");
        }        
        $detail = $this->repository->InternalVacancyData($id);
        $eid = (int) $this->employeeId;
        $employeeData = $this->repository->empData($eid);
        // Education Data
        $EducationData = $this->repository->empEdu($eid);
        $EducationData = Helper::extractDbData($EducationData);
        $eduFirst = $EducationData[0];
        $eduLast = end($EducationData);
        // Job History Data
        $JobData = $this->repository->empjob($eid);
        $JobData = Helper::extractDbData($JobData);
        $JobEnd = $JobData[0];
        $JobStart = end($JobData);
        // this is casual leave
        $casLeaveEarlier = $this->repository->casLeaveEarlier($eid);
        $casLeaveLater = $this->repository->casLeaveLater($eid);

        // var_dump($casLeave[0]['TOTALLEAVE']);die;
        $appliedData = $this->repository->getInclusions($user_id[0]['USER_ID'], 'Internal-performance',$id);
        $inclusionIds = (explode(',',$appliedData['application_personal'][0]['INCLUSION_ID']));
        $applicationAmount = $appliedData['application'][0]['APPLICATION_AMOUNT'];

        $form_type  = ($detail['VACANCY_TYPE'] == 'INTERNAL_APPRAISAL') ? 'appraisal' : 'form';
        $employeeInclusionUsed = $this->repository->getEmployeeInclusion($eid, $form_type);
        $getInclusion          = $this->repository->fetchInclusionById($employeeInclusionUsed['INCLUSION_ID_APPRAISAL']);
        $openingAdInfo         = $this->repository->getOpening($detail['OPENING_ID']);



        $applicationStoredDocuments = $this->repository->getAppliedStoredDocuments($appliedData['aid'], $user_id[0]['USER_ID']);
        $applicantsDocument = [];
        // echo('<pre>');print_r($applicationStoredDocuments);die;
        foreach ($applicationStoredDocuments as $applicationStoredDocument) {
            if ($applicationStoredDocument['DOC_FOLDER'] == "Signature") {
                $applicantsDocument['signature'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "FingerPrintR") {
                $applicantsDocument['FingerPrintR'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "FingerPrintL") {
                $applicantsDocument['FingerPrintL'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "CitizenshipF") {
                $applicantsDocument['CitizenshipF'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "CitizenshipB") {
                $applicantsDocument['CitizenshipB'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }
        }

        $applicantsDocumentNew = [];

        if ($applicationStoredDocuments) {
            foreach($applicationStoredDocuments as $applicationStoredDocument){
                $applicantsDocumentNew[$applicationStoredDocument['DOC_FOLDER']] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }
        }
        // echo('<pre>');print_r($employeeData['EMPLOYEE_CODE']);die;
        // print_r($applicantsDocumentNew);die;
        // echo('<pre>');print_r($applicantsDocument);
        // echo "<pre>"; print_r($employeeInclusionUsed);die;
        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'casLeaveEarlier' => $casLeaveEarlier[0]['TOTALLEAVE'],
                    'casLeaveLater' => $casLeaveLater[0]['TOTALLEAVE'],
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'detail' => $detail,
                    'JobStart' => $JobStart,
                    'JobEnd' => $JobEnd,
                    'EducationData' => $EducationData,
                    'eduFirst'  => $eduFirst,
                    'eduLast'  => $eduLast,
                    'EmployeeData' => $employeeData,           
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    'messages' => $this->flashmessenger()->getMessages(),
                    'applicantsDocumentNew' => $applicantsDocumentNew,
                    'inclusions' => $inclusions,
                    'inclusionIds' => $inclusionIds,
                    'application_amount' => $applicationAmount,
                    'employeeInclusionUsed' => $employeeInclusionUsed,
                    'getInclusion' => $getInclusion,
                    'baseurl' => $this->getRequest()->getBasePath(),
                    'openingAdInfo'=>$openingAdInfo
                ]
            )
        );
    }

    public function editPerformanceFormAction() 
    {

        $request = $this->getRequest();

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            /**
             * FOR EDUCATION DATA
             * */
            for ($i=0; $i < count($postData['edu_id']) ; $i++) {

                $updateData = [
                    'ACADEMIC_PROGRAM_ID' => $postData['faculty'][$i],
                    'ACADEMIC_DEGREE_ID' => $postData['level_id'][$i],
                    'ACADEMIC_COURSE_ID' => $postData['major_subject'][$i],
                    'PASSED_YR' => $postData['passed_year'][$i],
                    'ACADEMIC_UNIVERSITY_ID' => $postData['univerity_board'][$i],
                    'RANK_TYPE' => $postData['rank_type'][$i],
                    'RANK_VALUE' => $postData['rank_value'][$i],
                    'MODIFIED_DT' => date('Y-m-d')
                ];

                // echo "<pre>";
                // print_r($postData['edu_id'][$i]);
                // die;

                $check_presence = $this->repository->getRowId('HRIS_EMPLOYEE_QUALIFICATIONS', 'ID', $postData['edu_id'][$i]);

                // echo "<pre>";
                // print_r($check_presence);
                // die;

                if ($check_presence) {

                    $this->repository->getUpdateById('HRIS_EMPLOYEE_QUALIFICATIONS', $updateData, 'ID', $postData['edu_id'][$i]);

                } else {

                    $updateData['EMPLOYEE_ID'] = $postData['employee_id'];
                    $updateData['STATUS'] = 'E';
                    $updateData['CREATED_DT'] = date('Y-m-d');
                    $updateData['MODIFIED_DT'] = '';
                    $updateData['ID'] = ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_QUALIFICATIONS', 'ID')) + 1;

                    $this->repository->insertEmpEdu($updateData);

                }

            }

            /**
             * FOR DOCUMENTS FILE 
             * */
            $qualification_transcript_id = $postData['qualification_transcript_id'];
            $qualification_character_id  = $postData['qualification_character_id'];
            $qualification_equivalent_id = $postData['qualification_equivalent_id'];
            $qualification_council_id    = $postData['qualification_council_id'];
            $qualification_license_id    = $postData['qualification_license_id'];
            $right_finger_scan_id        = $postData['right_finger_scan_id'];
            $left_finger_scan_id         = $postData['left_finger_scan_id'];
            $signature_id                = $postData['signature_id'];
            $front_citizen_id            = $postData['front_citizen_id'];
            $back_citizen_id             = $postData['back_citizen_id'];


            $storingDocumentDatas   = [];
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['right_finger_scan'], 'FingerPrintR');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['left_finger_scan'], 'FingerPrintL');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['front_citizen'], 'CitizenshipF');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['back_citizen'], 'CitizenshipB');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['signature'], 'Signature');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_trascript'], 'qualification_trascript');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_character'], 'qualification_character');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_equivalent'], 'qualification_equivalent');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_council'], 'qualification_council');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_license'], 'qualification_license');

             
            /**
             * ASSIGNING AND MAPPING DOCUMENTS FOLDER NAME WITH REC_DOC_ID 
             * */

            $mapDocumentId = [
                'qualification_trascript'  => $postData['qualification_trascript_id'],
                'qualification_character'  => $postData['qualification_character_id'],
                'qualification_equivalent' => $postData['qualification_equivalent_id'],
                'qualification_council'    => $postData['qualification_council_id'],
                'qualification_license'    => $postData['qualification_license_id'],
                'FingerPrintR'             => $postData['right_finger_scan_id'],
                'FingerPrintL'             => $postData['left_finger_scan_id'],
                'Signature'                => $postData['signature_id'],
                'CitizenshipF'             => $postData['front_citizen_id'],
                'CitizenshipB'             => $postData['back_citizen_id']
            ];
            

            /**
             * UPDATE OR INSERT NEW AS DOCUMENT CONDITION
             * */
            foreach ($storingDocumentDatas as $document) {
                    
                if ($document['imageName'] !== '') {

                    /**
                     * IF mapDocumentId IS EMPTY THEN INSERT ELSE UPDATE REMOVING OLD FILE
                     * */
                    if ( $mapDocumentId[$document['folder']] > 0 ) {

                        // GET OLD DATA 
                        $oldDocData = $this->repository->getRowId('HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID', $mapDocumentId[$document['folder']]);

                        // REMOVE OLD FILE
                        $oldFile = Helper::UPLOAD_DIR . "/documents/".$document['folder']."/" . $oldDocData['DOC_NEW_NAME'];
                        unlink($oldFile);


                        // UPLOAD NEW FILE
                        $success = move_uploaded_file($document['tmp_name'], Helper::UPLOAD_DIR . "/documents/".$document['folder']."/" . $document['newImageName']);

                        // UPDATE RELATED ID DB
                        if ($success) {

                            $updateData = array(
                                'DOC_OLD_NAME'  => $document['imageName'],
                                'DOC_NEW_NAME'  => $document['newImageName'],
                                'MODIFIED_DATE' =>  date('Y-m-d'),
                            );

                            $this->repository->getUpdateById('HRIS_REC_APPLICATION_DOCUMENTS', $updateData, 'REC_DOC_ID', $mapDocumentId[$document['folder']]);
                        }

                    } else {

                        /**
                         * INSERTING DATA
                         * */

                        $success = move_uploaded_file($document['tmp_name'], Helper::UPLOAD_DIR . "/documents/".$document['folder']."/" . $document['newImageName']);


                        if ($success) {

                            $insertData = array(
                               'REC_DOC_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID')) + 1,
                               'APPLICATION_ID' => $postData['application_id'],
                               'VACANCY_ID' => $postData['vacancy_id'],
                               'USER_ID' => $postData['user_id'],
                               'DOC_OLD_NAME' => $document['imageName'],
                               'DOC_NEW_NAME' => $document['newImageName'],
                               'DOC_PATH' => $document['path'],
                               'DOC_TYPE' => $document['extension'],
                               'DOC_FOLDER' => $document['folder'],
                               'STATUS' => 'E',
                               'CREATED_DATE' => date('Y-m-d')
                            );

                            $this->repository->insertDocuments($insertData);

                        }


                    }

                }


            }


            /**
             * FOR INCLUSION
             * */

            $inclusion_id       = $postData['inclusion_id'];
            $has_inclusion_file = $postData['has_inclusion_file'];
            $prev_inclusion_appraisal_used = $postData['prev_inclusion_appraisal_used'];
            $prev_inclusion_id  = $postData['prev_inclusion_id'];
            $prev_file_code     = $postData['prev_file_code'];
            $vacancy_opening_date = $postData['vacancy_opening_date'];

            // $getInclusionDetail = $this->repository->fetchInclusionById($inclusion_id);

            // $checkEmployeeFile = $this->repository->getEmployeeInclusion($this->storageData['employee_detail']['EMPLOYEE_ID'], 'internal');
            
            /**
             * CHECK SAME INCLUSION OR NOT
             * */
            if ($inclusion_id) {
                
                if ($inclusion_id !== $prev_inclusion_id) {

                    /**
                     * GET INCLUSION DETAIL
                     * */

                    $getInclusionDetail = $this->repository->fetchInclusionById($inclusion_id);
                    
                    $checkEmployeeFile = $this->repository->getEmployeeInclusion($this->storageData['employee_detail']['EMPLOYEE_ID'], 'internal');

                    if ($getInclusionDetail['UPLOAD_FLAG'] == 'Y') {

                        if ($has_inclusion_file['error'] == 0) {
                            /**
                             * CHECK PREVIOUSLY UPLOADED FILE PRESENT
                             * */
                            $ext =  strtolower(pathinfo($postData['has_inclusion_file']['name'], PATHINFO_EXTENSION));
                            // FILE UPLOADING
                            $fileName    = pathinfo($postData['has_inclusion_file']['name'], PATHINFO_FILENAME);
                            $unique      = Helper::generateUniqueName();
                            $newFileName = $unique . "." . $ext;

                            $success = move_uploaded_file($postData['has_inclusion_file']['tmp_name'], Helper::UPLOAD_DIR . "/documents/Inclusion/" . $newFileName);

                            if ($prev_file_code > 0) {

                                // UPDATING
                                $path = Helper::UPLOAD_DIR . "/documents/Inclusion/" . $checkEmployeeFile['FILE_PATH'];

                                if (file_exists($path)) {

                                    unlink($path);

                                }

                                $updateData = array(
                                   'FILE_PATH' => $newFileName,
                                   'MODIFIED_DT' =>  date('Y-m-d')
                                );

                                $this->repository->getUpdateById('HRIS_EMPLOYEE_FILE', $updateData, 'FILE_CODE', $checkEmployeeFile['INCLUSION_APPRAISAL_FILE_ID']);


                            } else {

                                // INSERTING
                                $insertFile = array(
                                   'FILE_CODE' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1,
                                   'EMPLOYEE_ID' => $this->storageData['employee_detail']['EMPLOYEE_ID'],
                                   'FILETYPE_CODE' => '',
                                   'FILE_PATH' => $newFileName,
                                   'STATUS'    => 'E',
                                   'CREATED_DT' =>  date('Y-m-d'),
                                   'MODIFIED_DT' => '',
                                   'REMARKS' => '',
                                   'FILE_ID' => 0
                                );

                                $this->repository->insertEmployeeDocuments($insertFile);

                                $employeeInclusionUpdate = array(
                                    'INCLUSION_APPRAISAL_USED' => 'Y',
                                    'INCLUSION_APPRAISAL_USED_DATE' => date('Y-m-d'),
                                    'INCLUSION_ID_APPRAISAL' => $inclusion_id,
                                    'INCLUSION_APPRAISAL_FILE_ID' => $insertFile['FILE_CODE'],
                                    'INCLUSION_APPRAISAL_FOR_YEAR' => $vacancy_opening_date,
                                    'INCLUSION_USED_PROCESS' => 'RQ'
                                );

                                $this->repository->updateEmployeeInclusion($employeeInclusionUpdate, $this->storageData['employee_detail']['EMPLOYEE_ID']);

                            }

                        }

                    } else {

                        if ($checkEmployeeFile['INCLUSION_APPRAISAL_FILE_ID']) {

                            $path = Helper::UPLOAD_DIR . "/documents/Inclusion/" . $checkEmployeeFile['FILE_PATH'];

                            if (file_exists($path)) {

                                unlink($path);

                            }

                            $employeeInclusionUpdate = array(
                                'INCLUSION_APPRAISAL_USED' => 'Y',
                                'INCLUSION_APPRAISAL_USED_DATE' => date('Y-m-d'),
                                'INCLUSION_ID_APPRAISAL' => $inclusion_id,
                                'INCLUSION_APPRAISAL_FILE_ID' => 0,
                                'INCLUSION_APPRAISAL_FOR_YEAR' => $vacancy_opening_date,
                                'INCLUSION_USED_PROCESS' => 'RQ'
                            );

                            $this->repository->updateEmployeeInclusion($employeeInclusionUpdate, $this->storageData['employee_detail']['EMPLOYEE_ID']);
                        }


                    }

                }
            }

            $this->flashmessenger()->addMessage("Data Updated Successfully");
            return $this->redirect()->toRoute('vacancies', ['action' => 'edit-performance-form', 'id' => $postData['vacancy_id']]);

        }
        /**
         * GET VACANCY ID
         * */
        $vacancy_id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            
            return $this->redirect()->toRoute("vacancy");
        
        } 
        /**
         * GET USER ID
         * */
        $user_id    = $this->repository->userId($this->employeeId);

        /**
         * GET VACANCY DETAILS
         * */
        $detail     = $this->repository->InternalVacancyData($vacancy_id);
        
        $regno      = $this->repository->getRegNo($detail['VACANCY_ID']);
        
        $detail['FORM_NO'] = $detail['AD_NO'].'-'.($regno['APP_ID']+1);
        

        /**
         * ASSIGN EMPLOYEE ID
         * */  
        $eid = (int) $this->employeeId;
        /**
         * GET EMPLOYEE DETAILS
         * */
        $employeeData  = $this->repository->empData($eid);


        /**
         * GET EDUCATION DATA
         * */
        $educationData = $this->repository->empEdu($eid);
        $educationData = Helper::extractDbData($educationData);
        $eduFirst      = $educationData[0];
        $eduLast       = end($EducationData);

        // echo "<pre>";
        // print_r($educationData);

        // print_r(EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_UNIVERSITY', ['ACADEMIC_UNIVERSITY_ID' => "ACADEMIC_UNIVERSITY_ID", 'ACADEMIC_UNIVERSITY_NAME'=>"ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => "E"]));
        // die;

        /**
         * EMPLOYEE JOB HISTORY
         * */
        $jobData       = $this->repository->empjob($eid);
        $jobData       = Helper::extractDbData($jobData);
        $jobEnd        = $jobData[0];
        $jobStart      = end($jobData);
        
        /**
         * CASUAL LEAVE
         * */
        $casLeaveEarlier = $this->repository->casLeaveEarlier($eid);
        $casLeaveLater   = $this->repository->casLeaveLater($eid);

        $appliedData      = $this->repository->getInclusions($user_id[0]['USER_ID'], 'Internal-performance',$vacancy_id);
        $inclusionIds     = (explode(',',$appliedData['application_personal'][0]['INCLUSION_ID']));
        $applicationAmount= $appliedData['application'][0]['APPLICATION_AMOUNT'];

        // echo "<pre>";
        // print_r($appliedData);
        // print_r($user_id);
        // die;


        $form_type        = ($detail['VACANCY_TYPE'] == 'INTERNAL_APPRAISAL') ? 'appraisal' : 'form';

        $inclusionLists    = $this->repository->getInclusionsAll('internal');
        // $employeeInclusionUsedForm = $this->repository->getEmployeeInclusion($empId, 'form');
        // $employeeInclusionUsedAppraisal = $this->repository->getEmployeeInclusion($empId, 'appraisal');

        $employeeInclusionUsed = $this->repository->getEmployeeInclusion($eid, $form_type);
        $getInclusion          = $this->repository->fetchInclusionById($employeeInclusionUsed['INCLUSION_ID_APPRAISAL']);
        $openingAdInfo         = $this->repository->getOpening($detail['OPENING_ID']);

        // echo "<pre>";
        // print_r($employeeInclusionUsed);
        // die;

        $applicationStoredDocuments = $this->repository->getAppliedStoredDocuments($appliedData['aid'], $user_id[0]['USER_ID']);

        $applicantsDocumentNew = [];
        $applicantsDocumentId  = [];

        if ($applicationStoredDocuments) {
            foreach($applicationStoredDocuments as $applicationStoredDocument) {
                $applicantsDocumentNew[$applicationStoredDocument['DOC_FOLDER']] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }
        }



        if ($applicationStoredDocuments) {

            foreach($applicationStoredDocuments as $applicationStoredDocument) {
                
                $key = strtoupper($applicationStoredDocument['DOC_FOLDER']);

                $applicantsDocumentId[$key]['REC_DOC_ID'] = $applicationStoredDocument['REC_DOC_ID'];

            }

        }

        // echo "<pre>";
        // print_r($applicantsDocumentNew);
        // print_r($applicantsDocumentId);
        // die;

        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'detail' => $detail,
                    'inclusions' => $inclusions,
                    'educationData' => $educationData,
                    'EmployeeData' => $employeeData,
                    'appliedData'=> $appliedData,          
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    'messages' => $this->flashmessenger()->getMessages(),
                   'certificates'=> $certificates,
                   'existingDocuments' => $existingDocuments,
                   'inclusionIds' => $inclusionIds,
                   'application_amount' => $applicationAmount,
                   'applicantsDocumentId' => $applicantsDocumentId,
                   'applicantsDocumentNew' => $applicantsDocumentNew,
                   'eduDegrees' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_DEGREES', ['ACADEMIC_DEGREE_ID' => "ACADEMIC_DEGREE_ID", 'ACADEMIC_DEGREE_NAME'=>"ACADEMIC_DEGREE_NAME"], ["STATUS" => "E"]),
                    'eduFaculty' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_PROGRAMS', ['ACADEMIC_PROGRAM_ID' => "ACADEMIC_PROGRAM_ID", 'ACADEMIC_PROGRAM_NAME'=>"ACADEMIC_PROGRAM_NAME"], ["STATUS" => "E"]),
                    'eduUniversity' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_UNIVERSITY', ['ACADEMIC_UNIVERSITY_ID' => "ACADEMIC_UNIVERSITY_ID", 'ACADEMIC_UNIVERSITY_NAME'=>"ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => "E"]),
                    'eduCourses' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_COURSES', ['ACADEMIC_COURSE_ID' => "ACADEMIC_COURSE_ID", 'ACADEMIC_COURSE_NAME'=>"ACADEMIC_COURSE_NAME"], ["STATUS" => "E"]), 
                   'inclusionLists'=>$inclusionLists,
                   'employeeInclusionUsed' => $employeeInclusionUsed,
                   'employeeId'=> $eid,
                    'getInclusion' => $getInclusion,
                    'baseurl' => $this->getRequest()->getBasePath(),
                    'openingAdInfo'=>$openingAdInfo
                ]
            )
        );

    }

    public function editApplicationFormAction()
    {
        $request = $this->getRequest();    
        
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancies");
        }        
        $detail = $this->repository->InternalVacancyData($id);

        
        $eid = (int) $this->employeeId;
        $detail['EMPLOYEE_ID'] = $eid;
        $employeeData = $this->repository->empData($eid);

        /**
         * GET EDUCATION DATA
         * */
        $educationData = $this->repository->empEdu($eid);
        $educationData = Helper::extractDbData($educationData);
        $eduFirst      = $educationData[0];
        $eduLast       = end($EducationData);

        $regno = $this->repository->getRegNo($detail['VACANCY_ID']);
        $detail['form_no'] = $detail['AD_NO'].'-'.($regno['APP_ID']+1);
        $Inclusions =  explode(',', $detail['INCLUSION_ID']);
        foreach($Inclusions as $Inclusion){
            $inclusions[] = ($this->repository->fetchInclusionById($Inclusion[0]));
        }
        
        $documentsEducation = $this->repository->eduDocuments($eid);
        $user_id = $this->repository->userId($eid);
        
        $existingDocuments = [];
        foreach ($documentsEducation as $document) {
            $existingDocuments[] = array(
                $document['FILE_NAME'] => $document['FILE_PATH'],
                "path" => $document['FILE_PATH'],
                'name' =>$document['FILE_NAME']
            );
        }
        // print_r($certificates);
        // print_r($existingDocuments);die;
        $inclusionLists    = $this->repository->getInclusionsAll('form');
        $appliedData = $this->repository->getInclusions($user_id[0]['USER_ID'], 'Internal-form',$id);
        $inclusionIds = (explode(',',$appliedData['application_personal'][0]['INCLUSION_ID']));
        $applicationAmount = $appliedData['application'][0]['APPLICATION_AMOUNT'];

        $form_type  = ($detail['VACANCY_TYPE'] == 'INTERNAL_APPRAISAL') ? 'appraisal' : 'form';
        $employeeInclusionUsed = $this->repository->getEmployeeInclusion($eid, $form_type);
        $getInclusion          = $this->repository->fetchInclusionById($employeeInclusionUsed['INCLUSION_ID_FORM']);
        $openingAdInfo         = $this->repository->getOpening($detail['OPENING_ID']);



        $applicationStoredDocuments = $this->repository->getAppliedStoredDocuments($appliedData['aid'], $user_id[0]['USER_ID']);

        $applicantsDocumentNew = [];
        $applicantsDocumentId  = [];

        if ($applicationStoredDocuments) {
            foreach($applicationStoredDocuments as $applicationStoredDocument) {
                $applicantsDocumentNew[$applicationStoredDocument['DOC_FOLDER']] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }
        }



        if ($applicationStoredDocuments) {

            foreach($applicationStoredDocuments as $applicationStoredDocument) {
                
                $key = strtoupper($applicationStoredDocument['DOC_FOLDER']);

                $applicantsDocumentId[$key]['REC_DOC_ID'] = $applicationStoredDocument['REC_DOC_ID'];

            }

        }
        

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            /**
             * FOR EDUCATION DATA
             * */
            for ($i=0; $i < count($postData['level_id']) ; $i++) {

                $updateData = [
                    'ACADEMIC_PROGRAM_ID' => $postData['faculty'][$i],
                    'ACADEMIC_DEGREE_ID' => $postData['level_id'][$i],
                    'ACADEMIC_COURSE_ID' => $postData['major_subject'][$i],
                    'PASSED_YR' => $postData['passed_year'][$i],
                    'ACADEMIC_UNIVERSITY_ID' => $postData['univerity_board'][$i],
                    'RANK_TYPE' => $postData['rank_type'][$i],
                    'RANK_VALUE' => $postData['rank_value'][$i],
                    'MODIFIED_DT' => date('Y-m-d')
                ];

                $check_presence = $this->repository->getRowId('HRIS_EMPLOYEE_QUALIFICATIONS', 'ID', $postData['edu_id'][$i]);

                if ($check_presence) {

                    $this->repository->getUpdateById('HRIS_EMPLOYEE_QUALIFICATIONS', $updateData, 'ID', $postData['edu_id'][$i]);

                } else {

                    $updateData['EMPLOYEE_ID'] = $postData['employee_id'];
                    $updateData['STATUS'] = 'E';
                    $updateData['CREATED_DT'] = date('Y-m-d');
                    $updateData['MODIFIED_DT'] = '';
                    $updateData['ID'] = ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_QUALIFICATIONS', 'ID')) + 1;

                    $this->repository->insertEmpEdu($updateData);

                }

            }


            /**
             * FOR PROFILE IMAGE
             * */
            $check_profile = $this->repository->getRowId('HRIS_EMPLOYEE_FILE', 'FILE_CODE', $postData['profile_id']);

            if ($postData['profile_pic']['error'] == 0) { 

                $profile_id = $postData['profile_id'];

                $ext =  strtolower(pathinfo($postData['profile_pic']['name'], PATHINFO_EXTENSION));
                // FILE UPLOADING
                $fileName    = pathinfo($postData['profile_pic']['name'], PATHINFO_FILENAME);
                $unique      = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;

                $success = move_uploaded_file($postData['profile_pic']['tmp_name'], Helper::UPLOAD_DIR . "/" . $newFileName);

                $check_profile = $this->repository->getRowId('HRIS_EMPLOYEE_FILE', 'FILE_CODE', $postData['profile_id']);

                if ($check_profile) {

                    if ($check_profile['FILE_PATH'] !== 'default-profile-picture.jpg') {

                        // UPDATING
                        $path = Helper::UPLOAD_DIR . "/" . $check_profile['FILE_PATH'];

                        if (file_exists($path)) {

                            unlink($path);

                        }

                        $updateData = array(
                           'EMPLOYEE_ID' => $postData['employee_id'],
                           'FILE_PATH' => $newFileName,
                           'MODIFIED_DT' =>  date('Y-m-d')
                        );

                        $this->repository->getUpdateById('HRIS_EMPLOYEE_FILE', $updateData, 'FILE_CODE', $postData['profile_id']);

                    }

                }

            }


            /**
             * FOR DOCUMENTS FILE 
             * */
            $qualification_transcript_id = $postData['qualification_transcript_id'];
            $qualification_character_id  = $postData['qualification_character_id'];
            $qualification_equivalent_id = $postData['qualification_equivalent_id'];
            $qualification_council_id    = $postData['qualification_council_id'];
            $qualification_license_id    = $postData['qualification_license_id'];
            $right_finger_scan_id        = $postData['right_finger_scan_id'];
            $left_finger_scan_id         = $postData['left_finger_scan_id'];
            $signature_id                = $postData['signature_id'];
            $front_citizen_id            = $postData['front_citizen_id'];
            $back_citizen_id             = $postData['back_citizen_id'];


            $storingDocumentDatas   = [];
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['right_finger_scan'], 'FingerPrintR');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['left_finger_scan'], 'FingerPrintL');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['front_citizen'], 'CitizenshipF');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['back_citizen'], 'CitizenshipB');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['signature'], 'Signature');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_trascript'], 'qualification_trascript');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_character'], 'qualification_character');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_equivalent'], 'qualification_equivalent');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_council'], 'qualification_council');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['qualification_license'], 'qualification_license');

             
            /**
             * ASSIGNING AND MAPPING DOCUMENTS FOLDER NAME WITH REC_DOC_ID 
             * */

            $mapDocumentId = [
                'qualification_trascript'  => $postData['qualification_trascript_id'],
                'qualification_character'  => $postData['qualification_character_id'],
                'qualification_equivalent' => $postData['qualification_equivalent_id'],
                'qualification_council'    => $postData['qualification_council_id'],
                'qualification_license'    => $postData['qualification_license_id'],
                'FingerPrintR'             => $postData['right_finger_scan_id'],
                'FingerPrintL'             => $postData['left_finger_scan_id'],
                'Signature'                => $postData['signature_id'],
                'CitizenshipF'             => $postData['front_citizen_id'],
                'CitizenshipB'             => $postData['back_citizen_id']
            ];
            

            /**
             * UPDATE OR INSERT NEW AS DOCUMENT CONDITION
             * */
            foreach ($storingDocumentDatas as $document) {
                    
                if ($document['imageName'] !== '') {

                    /**
                     * IF mapDocumentId IS EMPTY THEN INSERT ELSE UPDATE REMOVING OLD FILE
                     * */
                    if ( $mapDocumentId[$document['folder']] > 0 ) {

                        // GET OLD DATA 
                        $oldDocData = $this->repository->getRowId('HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID', $mapDocumentId[$document['folder']]);

                        // REMOVE OLD FILE
                        $oldFile = Helper::UPLOAD_DIR . "/documents/".$document['folder']."/" . $oldDocData['DOC_NEW_NAME'];
                        unlink($oldFile);


                        // UPLOAD NEW FILE
                        $success = move_uploaded_file($document['tmp_name'], Helper::UPLOAD_DIR . "/documents/".$document['folder']."/" . $document['newImageName']);

                        // UPDATE RELATED ID DB
                        if ($success) {

                            $updateData = array(
                                'DOC_OLD_NAME'  => $document['imageName'],
                                'DOC_NEW_NAME'  => $document['newImageName'],
                                'MODIFIED_DATE' =>  date('Y-m-d'),
                            );

                            $this->repository->getUpdateById('HRIS_REC_APPLICATION_DOCUMENTS', $updateData, 'REC_DOC_ID', $mapDocumentId[$document['folder']]);
                        }

                    } else {

                        /**
                         * INSERTING DATA
                         * */

                        $success = move_uploaded_file($document['tmp_name'], Helper::UPLOAD_DIR . "/documents/".$document['folder']."/" . $document['newImageName']);


                        if ($success) {

                            $insertData = array(
                               'REC_DOC_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID')) + 1,
                               'APPLICATION_ID' => $postData['application_id'],
                               'VACANCY_ID' => $postData['vacancy_id'],
                               'USER_ID' => $postData['user_id'],
                               'DOC_OLD_NAME' => $document['imageName'],
                               'DOC_NEW_NAME' => $document['newImageName'],
                               'DOC_PATH' => $document['path'],
                               'DOC_TYPE' => $document['extension'],
                               'DOC_FOLDER' => $document['folder'],
                               'STATUS' => 'E',
                               'CREATED_DATE' => date('Y-m-d')
                            );

                            $this->repository->insertDocuments($insertData);

                        }


                    }

                }


            }



            /**
             * FOR INCLUSION
             * */

            $inclusion_id       = $postData['inclusion_id'];
            $has_inclusion_file = $postData['has_inclusion_file'];
            $prev_inclusion_form_used = $postData['prev_inclusion_form_used'];
            $prev_inclusion_id  = $postData['prev_inclusion_id'];
            $prev_file_code     = $postData['prev_file_code'];
            $vacancy_opening_date = $postData['vacancy_opening_date'];

            /**
             * CHECK SAME INCLUSION OR NOT
             * */

            if ($inclusion_id) {
                
                if ($inclusion_id !== $prev_inclusion_id) {


                    /**
                     * GET INCLUSION DETAIL
                     * */

                    $getInclusionDetail = $this->repository->fetchInclusionById($inclusion_id);
                    
                    $checkEmployeeFile = $this->repository->getEmployeeInclusion($this->storageData['employee_detail']['EMPLOYEE_ID'], 'form');


                    if ($getInclusionDetail['UPLOAD_FLAG'] == 'Y') {

                        if ($postData['has_inclusion_file']['error'] == 0) {
                            /**
                             * CHECK PREVIOUSLY UPLOADED FILE PRESENT
                             * */
                            $ext =  strtolower(pathinfo($postData['has_inclusion_file']['name'], PATHINFO_EXTENSION));
                            // FILE UPLOADING
                            $fileName    = pathinfo($postData['has_inclusion_file']['name'], PATHINFO_FILENAME);
                            $unique      = Helper::generateUniqueName();
                            $newFileName = $unique . "." . $ext;

                            $success = move_uploaded_file($postData['has_inclusion_file']['tmp_name'], Helper::UPLOAD_DIR . "/documents/Inclusion/" . $newFileName);

                            if ($prev_file_code > 0) {

                                // UPDATING
                                $path = Helper::UPLOAD_DIR . "/documents/Inclusion/" . $checkEmployeeFile['FILE_PATH'];

                                if (file_exists($path)) {

                                    unlink($path);

                                }

                                $updateData = array(
                                   'FILE_PATH' => $newFileName,
                                   'MODIFIED_DT' =>  date('Y-m-d')
                                );

                                $this->repository->getUpdateById('HRIS_EMPLOYEE_FILE', $updateData, 'FILE_CODE', $checkEmployeeFile['INCLUSION_FORM_FILE_ID']);


                            } else {

                                // INSERTING
                                $insertFile = array(
                                   'FILE_CODE' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1,
                                   'EMPLOYEE_ID' => $this->storageData['employee_detail']['EMPLOYEE_ID'],
                                   'FILETYPE_CODE' => '',
                                   'FILE_PATH' => $newFileName,
                                   'STATUS'    => 'E',
                                   'CREATED_DT' =>  date('Y-m-d'),
                                   'MODIFIED_DT' => '',
                                   'REMARKS' => '',
                                   'FILE_ID' => 0
                                );

                                $this->repository->insertEmployeeDocuments($insertFile);

                                $employeeInclusionUpdate = array(
                                    'INCLUSION_FORM_USED' => 'Y',
                                    'INCLUSION_FORM_USED_DATE' => date('Y-m-d'),
                                    'INCLUSION_ID_FORM' => $inclusion_id,
                                    'INCLUSION_FORM_FILE_ID' => $insertFile['FILE_CODE'],
                                    'INCLUSION_FORM_FOR_YEAR' => $vacancy_opening_date,
                                    'INCLUSION_USED_PROCESS' => 'RQ'
                                );

                                $this->repository->updateEmployeeInclusion($employeeInclusionUpdate, $this->storageData['employee_detail']['EMPLOYEE_ID']);

                            }

                        }

                    } else {


                        if ($checkEmployeeFile['INCLUSION_FORM_FILE_ID']) {

                            $path = Helper::UPLOAD_DIR . "/documents/Inclusion/" . $checkEmployeeFile['FILE_PATH'];

                            if (file_exists($path)) {

                                unlink($path);

                            }
                        }


                        $employeeInclusionUpdate = array(
                            'INCLUSION_FORM_USED' => 'Y',
                            'INCLUSION_FORM_USED_DATE' => date('Y-m-d'),
                            'INCLUSION_ID_FORM' => $inclusion_id,
                            'INCLUSION_FORM_FILE_ID' => 0,
                            'INCLUSION_FORM_FOR_YEAR' => $vacancy_opening_date,
                            'INCLUSION_USED_PROCESS' => 'RQ'
                        );

                        $this->repository->updateEmployeeInclusion($employeeInclusionUpdate, $this->storageData['employee_detail']['EMPLOYEE_ID']);


                    }


                }
            }

            $this->flashmessenger()->addMessage("Data Updated Successfully");
            return $this->redirect()->toRoute('vacancies', ['action' => 'editApplicationForm', 'id' => $postData['has_vacancy_idf']]);
            

        }

       
        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'detail' => $detail,
                    'inclusions' => $inclusions,
                    'educationData' => $educationData,
                    'EmployeeData' => $employeeData,           
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    'messages' => $this->flashmessenger()->getMessages(),
                   'certificates'=> $certificates,
                   'existingDocuments' => $existingDocuments,
                   'inclusionLists'=>$inclusionLists,
                   'inclusionIds' => $inclusionIds,
                   'application_amount' => $applicationAmount,
                   'applicantsDocument' => $applicantsDocument,
                   'applicantsDocumentNew' => $applicantsDocumentNew,
                   'eduDegrees' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_DEGREES', ['ACADEMIC_DEGREE_ID' => "ACADEMIC_DEGREE_ID", 'ACADEMIC_DEGREE_NAME'=>"ACADEMIC_DEGREE_NAME"], ["STATUS" => "E"]),
                    'eduFaculty' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_PROGRAMS', ['ACADEMIC_PROGRAM_ID' => "ACADEMIC_PROGRAM_ID", 'ACADEMIC_PROGRAM_NAME'=>"ACADEMIC_PROGRAM_NAME"], ["STATUS" => "E"]),
                    'eduUniversity' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_UNIVERSITY', ['ACADEMIC_UNIVERSITY_ID' => "ACADEMIC_UNIVERSITY_ID", 'ACADEMIC_UNIVERSITY_NAME'=>"ACADEMIC_UNIVERSITY_NAME"], ["STATUS" => "E"]),
                    'eduCourses' => EntityHelper::getTableList($this->adapter, 'HRIS_ACADEMIC_COURSES', ['ACADEMIC_COURSE_ID' => "ACADEMIC_COURSE_ID", 'ACADEMIC_COURSE_NAME'=>"ACADEMIC_COURSE_NAME"], ["STATUS" => "E"]),
                   'employeeInclusionUsed' => $employeeInclusionUsed,
                    'getInclusion' => $getInclusion,
                    'employeeId'=> $eid,
                    'baseurl' => $this->getRequest()->getBasePath(),
                    'openingAdInfo'=>$openingAdInfo
                ]
            )
        );
    }

    public function removedocumentAction()
    {

        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            /**
             * GET DATA
             * */
            $document = $this->repository->getRowId('HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID', $data['rec_doc_id']);

            // REMOVE OLD FILE
            $oldFile = Helper::UPLOAD_DIR . '/documents/'. $document['DOC_FOLDER'] .'/'. $document['DOC_NEW_NAME'];
            unlink($oldFile);

            $this->repository->deleteRowId('HRIS_REC_APPLICATION_DOCUMENTS', 'REC_DOC_ID', $data['rec_doc_id']);

            return new JsonModel(['success' => true, 'message' => 'Document Removed Successfully']);

        } catch (Exception $e) {

            return new JsonModel(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function viewApplicationFormAction()
    {
        // print_r($this->profile);die;
        $request = $this->getRequest();    
        
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancies");
        }        
        $detail = $this->repository->InternalVacancyData($id);

        
        $eid = (int) $this->employeeId;
        $detail['EMPLOYEE_ID'] = $eid;
        $employeeData = $this->repository->empData($eid);
        $EducationData = $this->repository->empEdu($eid);
        // var_dump($employeeData);die;
        $certificates = $this->repository->academicCertificates($detail['CODE']);
        $EducationData = Helper::extractDbData($EducationData);
        $regno = $this->repository->getRegNo($detail['VACANCY_ID']);
        $detail['form_no'] = $detail['AD_NO'].'-'.($regno['APP_ID']+1);
        $Inclusions =  explode(',', $detail['INCLUSION_ID']);
        foreach($Inclusions as $Inclusion){
            $inclusions[] = ($this->repository->fetchInclusionById($Inclusion[0]));
        }
        
        $documentsEducation = $this->repository->eduDocuments($eid);
        $user_id = $this->repository->userId($eid);
        // var_dump($user_id[0]['USER_ID']);die;
        // print_r($certificates);
        // print_r('<pre>');print_r($documentsEducation);
        $existingDocuments = [];
        foreach ($documentsEducation as $document) {
            $existingDocuments[] = array($document['FILE_NAME']=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // if ($document['FILE_NAME'] == 'SLC Certificate') {
            //     $existingDocuments[] = array("SLC"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }
            // if ($document['FILE_NAME'] == 'Intermediate Certificate') {
            //     $existingDocuments[] = array("+2/Intermediate"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }    
            // if ($document['FILE_NAME'] == 'Bachelor Certificate') {
            //     $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }       
            // if ($document['FILE_NAME'] == 'Master Certificate') {
            //     $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // } 
            // if ($document['FILE_NAME'] == 'M.Phil Certificate') {
            //     $existingDocuments[] = array("M.Phil"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }      
            // if ($document['FILE_NAME'] == 'P.HD. Certificate') {
            //     $existingDocuments[] = array("PHD"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            // }  
        }
        // print_r($certificates);
        // print_r($existingDocuments);die;
        
        $appliedData = $this->repository->getInclusions($user_id[0]['USER_ID'], 'Internal-form',$id);
        $inclusionIds = (explode(',',$appliedData['application_personal'][0]['INCLUSION_ID']));
        $applicationAmount = $appliedData['application'][0]['APPLICATION_AMOUNT'];


        $form_type  = ($detail['VACANCY_TYPE'] == 'INTERNAL_APPRAISAL') ? 'appraisal' : 'form';

        $employeeInclusionUsed = $this->repository->getEmployeeInclusion($eid, $form_type);
        $getInclusion          = $this->repository->fetchInclusionById($employeeInclusionUsed['INCLUSION_ID_FORM']);
        $openingAdInfo         = $this->repository->getOpening($detail['OPENING_ID']);

        $applicationStoredDocuments = $this->repository->getAppliedStoredDocuments($appliedData['aid'], $user_id[0]['USER_ID']);
        $applicantsDocument = [];
        // echo('<pre>');print_r($applicationStoredDocuments);die;
        foreach ($applicationStoredDocuments as $applicationStoredDocument) {
            if ($applicationStoredDocument['DOC_FOLDER'] == "Signature") {
                $applicantsDocument['signature'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "FingerPrintR") {
                $applicantsDocument['FingerPrintR'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "FingerPrintL") {
                $applicantsDocument['FingerPrintL'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "CitizenshipF") {
                $applicantsDocument['CitizenshipF'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }elseif ($applicationStoredDocument['DOC_FOLDER'] == "CitizenshipB") {
                $applicantsDocument['CitizenshipB'] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }
        }

        $applicantsDocumentNew = [];

        if ($applicationStoredDocuments) {
            foreach($applicationStoredDocuments as $applicationStoredDocument){
                $applicantsDocumentNew[$applicationStoredDocument['DOC_FOLDER']] = $applicationStoredDocument['DOC_PATH'].$applicationStoredDocument['DOC_NEW_NAME'];
            }
        }
        // echo('<pre>');print_r($applicantsDocument);
        // print_r($applicantsDocumentNew);die;


        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'detail' => $detail,
                    'inclusions' => $inclusions,
                    'EducationData' => $EducationData,
                    'EmployeeData' => $employeeData,           
                    'Openings' => EntityHelper::getTableKVListWithSortOption($this->adapter, OpeningVacancy::TABLE_NAME, OpeningVacancy::OPENING_ID, [OpeningVacancy::OPENING_NO], ["STATUS" => "E"], OpeningVacancy::OPENING_NO, "ASC", null, [null => '---'], true),
                    'messages' => $this->flashmessenger()->getMessages(),
                   'certificates'=> $certificates,
                   'existingDocuments' => $existingDocuments,
                   'inclusionIds' => $inclusionIds,
                   'application_amount' => $applicationAmount,
                   'applicantsDocument' => $applicantsDocument,
                   'applicantsDocumentNew' => $applicantsDocumentNew,
                   'employeeInclusionUsed' => $employeeInclusionUsed,
                    'getInclusion' => $getInclusion,
                    'baseurl' => $this->getRequest()->getBasePath(),
                    'openingAdInfo'=>$openingAdInfo
                ]
            )
        );
    }

    public function paymentsAction()
    {
        $allInfo =  $this->params()->fromRoute('id');

        $splits = explode('&', $allInfo);

        $arr = [];

        foreach ($splits as $split) {
            
            $arr[substr($split,0,3)] .= substr($split, 3);

        } 

        $data = [
            'vacancy_id' => $arr['vid'],
            'application_id' => $arr['aid'],
            'payment_id' => $arr['pid'],
            'user_id' => $arr['uid']
        ];

        // echo "<pre>";

        // print_r($data);


        // die;

        /**
         * PAYMENT ID  [1:ESEWA  2:KHALTI  3:CONNECTIPS]
         * 
         * */
        $this->paymentProcess($data);


        
        
    }

    public function paymentProcess($data)
    {

        $applicationDetail = $this->repository->checkVacancyApplicationApplied($data['vacancy_id'], $data['user_id'], $data['application_id']);


        $path = 'http://localhost/NOC1/public/selfservice/vacancies/success';


        if ($data['payment_id'] == 2) {

                // echo "here"; die;

                $return_url          = 'http://localhost/NOC1/public/selfservice/vacancies/success';
                $base_url            = 'https://khalti.com/api/v2/epayment/initiate/';
                $purchase_order_id   = 'NOC'.rand(0, 10000).time().'aid'.$data['application_id'].'vid'.$data['vacancy_id']; // example 123567;
                // $purchase_order_name = $applicationDetail['recruitment_post'].' level:'.$applicationDetail['recruitment_post_level'];
                $purchase_order_name = 'Internal'; // example Transaction: 1234,
                $amount_in_paisa     = $applicationDetail['APPLICATION_AMOUNT'] * 100; // Your total amount in paisa Rs 1 = 100 paisa
                //$amount_in_paisa     = 10 * 100; // Your total amount in paisa Rs 1 = 100 paisa

                $data['actual_amount'] = $applicationDetail['APPLICATION_AMOUNT'];


                /* khalti payment*/

                $private_key = 'live_secret_key_a7071610f28b47448abb9731884db925';


                $request_data = [

                    'return_url' => $return_url,
                    'website_url' => $path,
                    'amount' => $amount_in_paisa,
                    'purchase_order_id' => $purchase_order_id,
                    'purchase_order_name' => $purchase_order_name,

                ];

                // $base_url = config_item('khalti_request_url');

                $curl = curl_init();

                curl_setopt_array($curl, array(

                    CURLOPT_URL => $base_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($request_data),

                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Key ${private_key}",
                        "Content-Type: application/json",

                    ),

                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,

                ));

                $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                $response = curl_exec($curl);
                curl_close($curl);


                $response = json_decode($response); // response in php object

                /* khalti payment */
                // $baseUrl = new Zend_View_Helper_BaseUrl();
                // echo $this->getResponse()->setRedirect($response['payment_url']);

                if ( isset($response->payment_url) ) {


                    /**
                     *  INSERTING PAYMENT DATA IN HRIS_REC_APPLICATION_PAYMENT
                     * 
                     * */

                    /*
                     * GETTING MAX ROW COUNT OF TABLE 
                     *
                     */

                    $paymentId = $this->repository->getMaxIds('PAYMENT_ID','HRIS_REC_APPLICATION_PAYMENT');

                    $insert_data = [

                        'payment_id'         => $paymentId['MAXID'] + 1,
                        'application_id'     => $data['application_id'],
                        'user_id'            => $data['user_id'],
                        'vacancy_id'         => $data['vacancy_id'], 
                        'payment_gateway_id' => $data['payment_id'],
                        // 'payment_amount'     => $data['actual_amount'],
                        'payment_amount'     => 10,
                        'payment_unique_id'  => $response->pidx,
                        'payment_order_id'   => $request_data['purchase_order_id'],
                        'payment_order_name' => $request_data['purchase_order_name'],
                        'payment_status'     => 'pending',
                        'created_date'       => date('Y-m-d H:i:s.v')
                        
                    ];

                    // CHECK IF SAME PIDX INSERTED
                    $pidx = $insert_data['payment_unique_id'];

                    $checkPidx = $this->repository->checkVacancyApplicationPaymentByColumn(['PAYMENT_UNIQUE_ID' => $pidx]);

                    if ( !$checkPidx )
                    {     

                        $this->repository->insertApplicationPayment($insert_data);

                        header('Location: '.$response->payment_url);

                        exit;

                    } else {

                        return $this->redirect()->toRoute("vacancies", ['action' => 'view', 'id' => $data['vacancy_id']]);

                    }


                }


        }

       
    }

    public function successAction()
    {

        if ( isset($_GET['pidx']) ) {

            /* SETTING */
            $private_key = 'live_secret_key_a7071610f28b47448abb9731884db925';
            $base_url    = 'https://khalti.com/api/v2/epayment/lookup/';
            /* SETTING */


            /**
             *  RESPONSE QUERY VIA KHALTI
             * */
            $data = [
                'pidx' => $_GET['pidx'],
                'txnId' => $_GET['txnId'],
                'amount' => $_GET['amount'],
                'user_mobile' => $_GET['mobile'],
                'purchase_order_id' => $_GET['purchase_order_id'],
                'purchase_order_name' => $_GET['purchase_order_name'],
                'transaction_id' => $_GET['transaction_id'],
            ];

            $pidx = $data['pidx'];
            $checkPIDXAvailable = $this->repository->checkVacancyApplicationPaymentByColumn(['PAYMENT_UNIQUE_ID' => $pidx]);


            if ($checkPIDXAvailable)
            // if (true)
            {
                $updating_data = [
                    'PAYMENT_TRANSACTION_ID' => $data['transaction_id'],
                    'STATUS' => 1,
                    'PAYMENT_PAID' => 'Y'
                ];

                $column = implode(',', array_keys($updating_data));
                $update = implode("','", array_values($updating_data));

                $result = $this->repository->updateApplicationPayment($column, $update, [ 'PAYMENT_UNIQUE_ID' => $pidx ]);


                $vacancyUpdating_data = [
                    'PAYMENT_ID' => $checkPIDXAvailable['PAYMENT_ID'],
                    'PAYMENT_PAID' => 'Y'
                ];

                $column = implode(',', array_keys($vacancyUpdating_data));
                $update = implode("','", array_values($vacancyUpdating_data));

                $result = $this->repository->updateVacancyApplication($column, $update, $checkPIDXAvailable['APPLICATION_ID'], $checkPIDXAvailable['VACANCY_ID']);



                try {

                    $curl = curl_init();

                    curl_setopt_array($curl, array(

                        CURLOPT_URL => $base_url,
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => TRUE,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode([
                            'pidx' => $pidx
                        ]),

                        CURLOPT_HTTPHEADER => array(
                            "Authorization: Key ${private_key}",
                            'Content-type: application/json'
                        ),

                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,

                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    $response = json_decode($response, true); //convert in array


                    /**
                     *   VERIFIED TRANSACTION RESPONSE
                     * 
                     *   (fee charged by KHALTI of our payment)
                     *   
                     *       (
                     *           [pidx] => thi3PeT4AzAwr7hkwx9tvG
                     *           [total_amount] => 40000
                     *           [status] => 'Completed'
                     *           [transaction_id] => gYvaTVHHLVCYagwdZ8mGJ7
                     *           [fee] => 1200
                     *           [refunded] => ''
                     *       )
                     * 
                     * */

                    $fee = ($response['fee'] !== 0) ? ($response['fee'] / 100) : 0;

                    $update_data = [
                        'payment_status' => strtolower($response['status']),
                        'fee' => $fee,
                        'payment_verified' => '',
                        'payment_verified_date'  => date('Y-m-d H:i:s.v')
                    ];


                    if ( $response['status'] == 'Completed' ) 
                    {
                        /**
                         *  for status == completed
                         * 
                         *  status = 1, payment_paid = Y, payment_verified = Y
                         * 
                         * */
                        $update_data['payment_verified'] = 'Y';

                        $column = implode(',', array_keys($update_data));
                        $update = implode("','", array_values($update_data));

                        $result = $this->repository->updateApplicationPayment($column, $update, [ 'PAYMENT_UNIQUE_ID' => $pidx ]);

                        if ($result) {

                            // $payment_verified = $update_data['payment_verified'];
                            $application_id   = $checkPIDXAvailable['APPLICATION_ID'];
                            $vacancy_id       = $checkPIDXAvailable['VACANCY_ID'];
                            $payment_id       = $checkPIDXAvailable['PAYMENT_ID'];

                            $this->repository->updateVacancyApplication('PAYMENT_VERIFIED', 'Y', $application_id, $vacancy_id);



                            return $this->redirect()->toRoute('vacancies', ['action' => 'view', 'id' => $vacancy_id]);

                        } else {

                            return $this->redirect()->toRoute("vacancies", ['action' => 'view']);

                        }
                        
                    }

                    
                } catch (Exception $e) {
                 
                    throw $e;

                }

            }
        }
        
        
    }



    /**
     *  CONNECTI IPS SUCCESS
     * 
     *  vacancies/successIPS?TXNID=1667039604a8v77
        https://hr.nepaloil.org.np/selfservice/vacancies/successIPS?TXNID=1667200104a143v41
     * */
    public function successIPS()
    {

        $transaction_id = $_GET['TXNID'];
        echo $transaction_id;
        die;
        // $transaction_id = '1667039604a8v77';

        /* a POSITION and v POSITION */
        $a = strpos($transaction_id, 'a');
        $v = strpos($transaction_id, 'v');

        // GET APPLICATION ID
        $application_id = substr($transaction_id, $a + 1, $v - ($a + 1));

        // GET VACANCY ID
        $vacancy_id     = substr($transaction_id, $v + 1);


        /* GET APPLICANT DETAIL */

        $applicantDetail = $this->repository->checkVacancyApplicationByAidVid($application_id, $vacancy_id);

        $paymentId       = $this->repository->getMaxIds('PAYMENT_ID', 'HRIS_REC_APPLICATION_PAYMENT');

        $gateway         = $this->repository->getPaymentGatewayByWhere(['GATEWAY_COMPANY' => 'connectips']);


        /* INSERT PAYMENT DETAILS */
        $insert_data = [

            'payment_id'             => $paymentId['MAXID'] + 1,
            'application_id'         => $applicantDetail['APPLICATION_ID'],
            'user_id'                => $applicantDetail['USER_ID'],
            'vacancy_id'             => $applicantDetail['AD_NO'],
            'payment_gateway_id'     => $gateway['ID'],
            'payment_currency'       => 'NPR',
            'payment_amount'         => $applicantDetail['APPLICATION_AMOUNT'],
            'payment_transaction_id' => $transaction_id,
            'payment_reference_id'   => 'REF'.rand(0, 10000000).'aid'.$applicantDetail['APPLICATION_ID'].'vid'.$applicantDetail['AD_NO'],
            'status'                 => 1,
            'payment_paid'           => 'Y',
            'payment_status'         => 'pending',
            'remarks'                => 'for payment registration no '. $applicantDetail['REGISTRATION_NO'],
            'particulars'            => 'PART-001',
            'created_date'           => date('Y-m-d H:i:s.v'),
        ];


        $this->repository->insertApplicationPayment($insert_data);

        $result = $this->repository->updateVacancyApplicationArray(['PAYMENT_ID' => $insert_data['payment_id'], 'PAYMENT_PAID' => 'Y'] , $application_id, $vacancy_id);


        if ( $result ) {


            /* FOR VERIFYING TRANSACTION */
            $connectips_data = [

                "merchant_id" => 212,
                "app_id" => 'NOC-212-REC-1',
                "txn_id" => $transaction_id,
                "txn_amount" => $applicantDetail['APPLICATION_AMOUNT'] * 100,
                "token" => '',
                "token_for" => 'verify'
            
            ];

            $connectips_data['token'] = $this->_generateTokenConnectIPS($connectips_data);

            $verifyData = [

                "merchantId" => $connectips_data['merchant_id'],
                "appId" => $connectips_data['app_id'],
                "referenceId" => $connectips_data['txn_id'],
                "txnAmt" => $connectips_data['txn_amount'],
                "token" => $connectips_data['token']
            
            ];


            /* HERE STARTS PROCESS */
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            // CURLOPT_URL => 'https://uat.connectips.com:7443/connectipswebws/api/creditor/validatetxn/',

            $curl = curl_init();
            $client_id   = 'NOC-212-REC-1';
            $client_pass = 'Abcd@123';

           
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://login.connectips.com:5443/connectipswebws/api/creditor/validatetxn',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($verifyData),
                CURLOPT_HTTPHEADER => array(
                    'Content-type: application/json',
                    'Authorization: Basic ' . base64_encode("$client_id:$client_pass")
                ),

                // ----- THIS BELOW LINE IS FOR LOCALHOST ONLY ----
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
                // ----- THIS ABOVE LINE IS FOR LOCALHOST ONLY ----
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $responseObj = json_decode($response, true);

           
            if ( $responseObj['status'] == 'SUCCESS' ) {
                
                $payment_verified_data = [
                    'payment_status'       => $responseObj['status'],
                    'payment_verified'     => 'Y',
                    'payment_verified_date'=> date('Y-m-d H:i:s.v')
                ];


                $this->repository->updateApplicationPaymentArray($payment_verified_data,  ['PAYMENT_TRANSACTION_ID' => $transaction_id]);

                $result = $this->repository->updateVacancyApplicationArray(['PAYMENT_VERIFIED' => 'Y'] , $application_id, $vacancy_id);

                return $this->redirect()->toRoute('vacancies', ['action' => 'view', 'id' => $vacancy_id]);

            
            } else {

                return $this->redirect()->toRoute('vacancies', ['action' => 'view', 'id' => $vacancy_id]);

            }


        }

    }

    public function failedIPSAction()
    {
        return $this->redirect()->toRoute('vacancies');
    }

    
}
