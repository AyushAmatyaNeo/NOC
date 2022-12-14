<?php
namespace Recruitment\Controller;

use Application\Controller\HrisController;
use Recruitment\Form\AdmitForm;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\Storage\StorageInterface;
use Application\Helper\Helper;
use Zend\View\Model\ViewModel;
use Recruitment\Repository\UserApplicationRepository;
use Recruitment\Helper\EmailHelper;
use Application\Helper\EntityHelper;
use Zend\View\Model\JsonModel; 
use Exception;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

Class RollnoController extends HrisController{

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(UserApplicationRepository::class);
        
    }
    public function indexAction(){
        
    }

    public function excelUploadAction()
    {

        // Convert string 'true/false' into boolean
        $sendMail = $_POST['sendMail'] === 'true'? true: false;
        $generateAdmit = $_POST['generateAdmit'] === 'true'? true: false;

        $excelData = $_POST['data'];
        $count = count($excelData);

        /**
         * INDEX 0 WILL BE NAME FIELD OF EXCEL
         * */

        for ($i=0; $i < $count ; $i++) { 

            /**
             * FOR REAL UPLOADED EXCEL WITH MAPPING APPLICATION_ID AND UPDATING ROLL_NO AND SETTING 'Y' FLAG IN IS_ADMIT_GENERATED
             * */

            

            $applicationId = (int)Helper::numConverter($excelData[$i]['A']);

            // Do nothing until proper application id is reached
            if($applicationId==0){
                continue;
            }

            $update = [
                /**
                 * HERE B CAN CHANGE AS EXCEL FORMAT --- THIS IS FOR ROLL NO CELL  
                 * 
                 * IF CELL DATA HAS UNICODE THEN USE base64_encode BEFORE ASSIGNING
                 * */
                'ROLL_NO' => base64_encode($excelData[$i]['B']),
            ];

            if($generateAdmit){
                $update['IS_ADMIT_GENERATED'] = 'Y';
            }

            /**
             * UPDATING DATA
             * 
             * CELL A -- CAN BE CHANGED AS EXCEL FORMAT -- THIS IS FOR APPLICATION_ID
             * */
            $applicationInfo = $this->repository->getRowId('HRIS_REC_VACANCY_APPLICATION', 'APPLICATION_ID', $applicationId);

            $result = $this->repository->getUpdateById('HRIS_REC_APPLICATION_PERSONAL', $update, 'APPLICATION_ID', $applicationId);

            $applicationType = $applicationInfo['APPLICATION_TYPE'] == 'OPEN' ? 'OPEN' : 'INTERNAL';

            $nepaliData = [
                "ID" => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_VACANCY_USER_NEPALI', 'ID')) + 1,
                "USER_ID" => $applicationInfo["USER_ID"],
                "NAME" => base64_encode($excelData[$i]['C']),
                "FATHER_NAME" => base64_encode($excelData[$i]['F']),
                "MOTHER_NAME" => base64_encode($excelData[$i]['G']),
                "GRANDFATHER_NAME" => base64_encode($excelData[$i]['H']),
                "APPLICATION_TYPE" => $applicationType,
                "CREATED_DATE" => date('Y-m-d'),
                "CREATED_BY" => $this->employeeId,
                "STATUS" => 'E'
            ];

            $insertResult = $this->repository->insertData('HRIS_REC_VACANCY_USER_NEPALI', $nepaliData);


            if ($result && $insertResult && $sendMail) {

                /**
                 * SENDING EMAIL WITH MESSAGE OF ADMIT CARD HAS BEEN GENERATED
                 * */

                if ($applicationInfo['APPLICATION_TYPE'] != 'OPEN') {

                    $applicationData = iterator_to_array($this->repository->applicationDataByIdInternal($applicationInfo['APPLICATION_ID']), false);
                } else {

                    $applicationData = iterator_to_array($this->repository->applicationDataById($applicationInfo['APPLICATION_ID']), false);
                }


                /**
                 * EMAIL TEMPLATE
                 * */

                $htmlDescription = "Dear ".$applicationData[0]['FIRST_NAME']." ".$applicationData[0]['MIDDLE_NAME']. " ". $applicationData[0]['LAST_NAME']
                ."<br><br>Your applied application's admit card has been generated at  ".date('Y-m-d')
                ."<br>Please download your admit card. "
                ."<br><br>Regards, <br>Nepal Oil Corporation Limited.";
                // $htmlDescription .= self::mailFooter();


                date_default_timezone_set("Asia/Kathmandu");
                $htmlPart = new MimePart($htmlDescription);
                $htmlPart->type = "text/html";

                $body = new MimeMessage();
                $body->setParts(array($htmlPart));

                // print_r($body);die;
                try{
                    $mail = new Message();
                    $mail->setSubject('Admit Card Generated');
                    $mail->setBody($body);
                    $mail->setFrom('nepaloil.noreply@gmail.com', 'NOC');
                    if($applicationData[0]['EMAIL_ID'] != null){
                        $mail->addTo($applicationData[0]['EMAIL_ID'], $applicationData[0]['FIRST_NAME']);
                        // $mail->addTo('srbunitydeveloper@gmail.com', $applicationData[0]['FIRST_NAME']);

                        // Commented for testing purpose
                        // EmailHelper::sendEmailZOHO($mail);
                    }else{
                        continue;
                    }
                }catch(Exception $e){
                    continue;
                }

            }

        }

        return new JsonModel(['success' => true, 'data' => 'Successfully Excel File Uploaded']);
    }

    /**
     * Helper function to assist in ad_no nepali
     */
    // public function excelUploadAction(){
    //     $excelData = $_POST['data'];
    //     $count = count($excelData);

    //     for ($i=1; $i < $count ; $i++) {
    //         $id = $excelData[$i]['A'];
    //         $adNo = $excelData[$i]['B'];
    //         $conversion = Helper::convertAdNo($adNo);

    //         $update = [
    //             'AD_NO_NEPALI' => base64_encode($conversion),
    //         ];
            
    //         $result = $this->repository->getUpdateById('HRIS_REC_VACANCY', $update, 'VACANCY_ID', $id);
    //     }

    //     var_dump("success");
    //     die;
    // }
    
}