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
        $excelData = $_POST['data'];
        $count = count($excelData);

        /**
         * INDEX 0 WILL BE NAME FIELD OF EXCEL
         * */

        for ($i=1; $i < $count ; $i++) { 


            // if(($data['A'] == null || $data['A'] == '') || ($data['B'] == null || $data['B'] == '')){ continue; }

            /* FOR TESTING PURPOSE */
            // $insert = [
            //     'ID' => ((int) Helper::getMaxId($this->adapter, 'HRIS_REC_IMPORT_TEST', 'ID')) + 1,
            //     'NAME' => base64_encode($excelData[$i]['A']),
            //     'ROLL_NO' => base64_encode($excelData[$i]['B']),
            //     'FATHER_NAME' => base64_encode($excelData[$i]['C'])
            // ];

            /**
             * FOR REAL UPLOADED EXCEL WITH MAPPING APPLICATION_ID AND UPDATING ROLL_NO AND SETTING 'Y' FLAG IN IS_ADMIT_GENERATED
             * */

            $update = [
                /**
                 * HERE B CAN CHANGE AS EXCEL FORMAT --- THIS IS FOR ROLL NO CELL  
                 * 
                 * IF CELL DATA HAS UNICODE THEN USE base64_encode BEFORE ASSIGNING
                 * */
                // 'ROLL_NO' => base64_encode($excelData[$i]['B']),
                'ROLL_NO' => $excelData[$i]['B'],
                'IS_ADMIT_GENERATED' => 'Y'

            ];

            /**
             * UPDATING DATA
             * 
             * CELL A -- CAN BE CHANGED AS EXCEL FORMAT -- THIS IS FOR APPLICATION_ID
             * */
            $result = $this->repository->getUpdateById('HRIS_REC_APPLICATION_PERSONAL', $update, 'APPLICATION_ID', $excelData[$i]['A']);


            if ($result) {

                /**
                 * SENDING EMAIL WITH MESSAGE OF ADMIT CARD HAS BEEN GENERATED
                 * */

                $applicationInfo = $this->repository->getRowId('HRIS_REC_VACANCY_APPLICATION', 'APPLICATION_ID', $excelData[$i]['A']);

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
                $mail = new Message();
                $mail->setSubject('Admit Card Generated');
                $mail->setBody($body);
                $mail->setFrom('nepaloil.noreply@gmail.com', 'NOC');
                // $mail->setFrom('nepaloilcorp.noreply@gmail.com', 'NOC');
                $mail->addTo($applicationData[0]['EMAIL_ID'], $applicationData[0]['FIRST_NAME']);

                // Commented for testing purpose
                // EmailHelper::sendEmail($mail);

            }
            
            // $result = $this->repository->insertData('HRIS_REC_IMPORT_TEST', $insert);

        }

        return new JsonModel(['success' => true, 'data' => 'Successfully Excel File Uploaded']);
    }
    
}