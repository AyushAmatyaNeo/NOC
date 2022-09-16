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
    }

    public function indexAction()
    {

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = (array) $request->getPost();
                $rawList = $this->repository->getFilteredRecords($data);
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


        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("vacancy");
        }

        $detail = $this->repository->fetchById($id);
        $inclusion = Helper::extractDbData($this->VacancyInclusionRepository->fetchById($id));
        $skills =  explode(',', $detail['SKILL_ID']);
        $Inclusions =  explode(',', $detail['INCLUSION_ID']);
        $model = new RecruitmentVacancyModel();
        $model->exchangeArrayFromDB($detail);
        $model->SkillId = $skills;
        $model->InclusionId = $Inclusions;
        $this->form->bind($model);
        $Vacancy_types = array("OPEN" => "OPEN", "INTERNAL" => "INTERNAL");
        $empId = $this->employeeId;
        $user_id = $this->repository->userId($empId);
        $employeeFirstJoin = $this->repository->inclusionAppliedCheck($empId);
        $employeeLastJoin = $this->repository->inclusionPromoCheck($empId);
        $vacancyApplyStage = $this->repository->checkVacancyStatus($id, $user_id[0]['USER_ID']);
        $inc = 'N';
        
        $employeeFirstJoinDate = $employeeFirstJoin['JOIN_DATE'];
        $employeeFirstFunctionalLevel = $employeeFirstJoin['FUNCTIONAL_LEVEL_NO'];
        if ($employeeFirstJoin['INCLUSION'] == 'Y') {
            $inc = 'Y';
        } else {
            foreach ($employeeLastJoin as $value) {
                if ($value['INCLUSION'] == 'Y') {
                    $inc = 'Y';
                }
            }
        }
        $curentJob['StartDate'] = $employeeFirstJoinDate;
        $curentJob['FUNCTIONAL_LEVEL_NO'] = $employeeFirstFunctionalLevel;
        $curentJob['INCLUSION'] = $inc;
        if (count($employeeLastJoin) != null) {
            foreach ($employeeLastJoin as $value) {
                if ($employeeFirstJoinDate < $value['START_DATE']) {
                    $curentJob['StartDate'] = $value['START_DATE'];
                    if ($value['FUNCTIONAL_LEVEL_NO'] != null) {
                        $curentJob['FUNCTIONAL_LEVEL_NO'] = $value['FUNCTIONAL_LEVEL_NO'];
                    }
                   
                }
            }
        }
        $date = date('Y-m-d');
        $curentJob['DURATION'] =  $date - $curentJob['StartDate'];
        // var_dump($curentJob); die;
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
        ]);
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
            if ($document['FILE_NAME'] == 'SLC Certificate') {
                $existingDocuments[] = array("SLC"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }
            if ($document['FILE_NAME'] == 'Intermediate Certificate') {
                $existingDocuments[] = array("+2/Intermediate"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }    
            if ($document['FILE_NAME'] == 'Bachelor Certificate') {
                $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }       
            if ($document['FILE_NAME'] == 'Master Certificate') {
                $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            } 
            if ($document['FILE_NAME'] == 'M.Phil Certificate') {
                $existingDocuments[] = array("M.Phil"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }      
            if ($document['FILE_NAME'] == 'P.HD. Certificate') {
                $existingDocuments[] = array("PHD"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }  
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
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData[$certificate['ACADEMIC_DEGREE_NAME']], $certificate['ACADEMIC_DEGREE_NAME'].' Certificate');
            }
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['right_finger_scan'], 'CitizenshipR');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['left_finger_scan'], 'CitizenshipL');
            $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['signature'], 'Signature');

            // echo '<pre>'; print_r($storingDocumentDatas); die;
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
                 if ($storingDocumentData['folder'] != 'CitizenshipR' && $storingDocumentData['folder'] != 'Signature' && $storingDocumentData['folder'] != 'CitizenshipL') {
                    $filecode = $this->repository->fileType($storingDocumentData['extension']);

                    $fileSetId = $this->repository->fileSetId($storingDocumentData['folder']);
                    // var_dump($storingDocumentData['folder']);die;
                     $empFile = array(
                       'FILE_CODE' => ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1,
                       'EMPLOYEE_ID' => $eid,
                       'FILETYPE_CODE' => $filecode[0]['FILETYPE_CODE'],
                       'FILE_PATH' => $storingDocumentData['newImageName'],
                       'STATUS'    => 'E',
                       'CREATED_DT' =>  date('Y-m-d'),
                       'FILE_ID' => $fileSetId[0]['FILE_ID'],
                       'MODIFIED_DT' => '',
                       'REMARKS' => '',
                      );
                    move_uploaded_file( $storingDocumentData['tmp_name'], $storingDocumentData['empFilePath']);
                    $this->repository->insertEmployeeDocuments($empFile);
                 }
                
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
            $eduCount = count($postData['edu_institute']);
            if ($eduCount > 0) {
                for($i=0; $i < $eduCount; $i++){
                    $eduData = array(
                        'EDUCATION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_EDUCATION', 'EDUCATION_ID')) + 1,
                        'APPLICATION_ID' =>$data['hris_personal']['APPLICATION_ID'],
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
        $request = $this->getRequest();
        $postData = $request->getPost();

        $vacancy_id = (int) $this->params()->fromRoute('id');
        $user_id = $this->repository->userId($this->employeeId);
        $detail = $this->repository->InternalVacancyData($vacancy_id);
        $regno = $this->repository->getRegNo($detail['VACANCY_ID']);
        $detail['form_no'] = $detail['AD_NO'].'-'.($regno['APP_ID']+1);
        // var_dump($detail);die;

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            $data['hris_personal'] = array(
                'PERSONAL_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_APPLICATION_PERSONAL', 'PERSONAL_ID')) + 1,
                'APPLICATION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_VACANCY_APPLICATION', 'APPLICATION_ID')) + 1,
                'USER_ID' => $user_id[0]['USER_ID'],
                'INCLUSION_ID' => 1,
                'CREATED_DATE' =>  date('Y-m-d'),
            );
            $this->repository->insertPersonal($data['hris_personal']);
                $data = [
                    'APPLICATION_ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_VACANCY_APPLICATION', 'APPLICATION_ID')) + 1,
                    'USER_ID' => $user_id[0]['USER_ID'],
                    'AD_NO' =>  $vacancy_id,
                    'REGISTRATION_NO' => $detail['form_no'],
                    'STAGE_ID' => 2,
                    'APPLICATION_AMOUNT' => 2000,
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
            if ($document['FILE_NAME'] == 'SLC Certificate') {
                $existingDocuments[] = array("SLC"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }
            if ($document['FILE_NAME'] == 'Intermediate Certificate') {
                $existingDocuments[] = array("Intermediate"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }    
            if ($document['FILE_NAME'] == 'Bachelor Certificate') {
                $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }       
            if ($document['FILE_NAME'] == 'Master Certificate') {
                $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            } 
            if ($document['FILE_NAME'] == 'M.Phil Certificate') {
                $existingDocuments[] = array("M.Phil"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }      
            if ($document['FILE_NAME'] == 'P.HD. Certificate') {
                $existingDocuments[] = array("PHD"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }  
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
                
               if ($postData[$certificate['ACADEMIC_DEGREE_NAME']]['name'] != null) {
                    $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData[$certificate['ACADEMIC_DEGREE_NAME']], $certificate['ACADEMIC_DEGREE_NAME'].' Certificate');
               } 
                // echo '<pre>'; print_r($storingDocumentDatas); die;
            }
            if ($postData['right_finger_scan']['name'] != "") {
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['right_finger_scan'], 'CitizenshipR');
            }
            if ($postData['left_finger_scan']['name'] != "") {
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['left_finger_scan'], 'CitizenshipL');
            } if ($postData['signature']['name'] != "") {
                $storingDocumentDatas[] = Helper::uploadFilesVacancy($postData['signature'], 'Signature');
            }

            // var_dump($postData); die;
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
                   
                ]
            )
        );
    }
    public function viewApplicationFormAction()
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
            if ($document['FILE_NAME'] == 'SLC Certificate') {
                $existingDocuments[] = array("SLC"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }
            if ($document['FILE_NAME'] == 'Intermediate Certificate') {
                $existingDocuments[] = array("+2/Intermediate"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }    
            if ($document['FILE_NAME'] == 'Bachelor Certificate') {
                $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }       
            if ($document['FILE_NAME'] == 'Master Certificate') {
                $existingDocuments[] = array("Bachelor"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            } 
            if ($document['FILE_NAME'] == 'M.Phil Certificate') {
                $existingDocuments[] = array("M.Phil"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }      
            if ($document['FILE_NAME'] == 'P.HD. Certificate') {
                $existingDocuments[] = array("PHD"=> $document['FILE_PATH'],"path" => $document['FILE_PATH'],'name'=>$document['FILE_NAME']);
            }  
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
                  
                ]
            )
        );
    }
}
