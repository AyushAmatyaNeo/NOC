<?php

namespace Cron\Controller;

use Application\Helper\EmailHelper;
use Cron\Repository\CronRepository;
use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mail\Headers;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class Cron extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
        return new JsonModel(['success' => true, 'data' => '', 'message' => 'No data found']);
    }

    public function todayAction() {
        $responseData = [];
        try {
            $request = $this->getRequest();
            $requestType = $request->getMethod();

            switch ($requestType) {
                case Request::METHOD_GET:
                    $responseData = $this->getAbsentOrLateList();
                    if ($responseData == NULL) {
                        return new JsonModel(['success' => true, 'data' => $responseData, 'message' => 'No data found']);
                    }
                    break;
                default :
                    throw new Exception('The request is unknown');
            }

            $this->getAbsentList($responseData);
            $this->getLateList($responseData);

            return new JsonModel(['success' => true, 'data' => $responseData, 'message' => $requestType]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => $responseData, 'message' => $e->getMessage()]);
        }
    }

    public function nextDayAction() {
        $responseData = [];
        try {
            $request = $this->getRequest();
            $requestType = $request->getMethod();

            switch ($requestType) {
                case Request::METHOD_GET:
                    $responseData = $this->getMissedOrEarlyOutList();
                    if ($responseData == NULL) {
                        return new JsonModel(['success' => true, 'data' => $responseData, 'message' => 'No data found']);
                    }
                    break;
                default :
                    throw new Exception('The request is unknown');
            }

            $this->getEarlyOut($responseData);
            $this->getMissedPunch($responseData);

            return new JsonModel(['success' => true, 'data' => $responseData, 'message' => $requestType]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => $responseData, 'message' => $e->getMessage()]);
        }
    }

    public function getAbsentOrLateList() {
        $cronRepo = new CronRepository($this->adapter);
        return $cronRepo->fetchAbsentOrLate();
    }

    public function getMissedOrEarlyOutList() {
        $cronRepo = new CronRepository($this->adapter);
        return $cronRepo->fetchMissedOrEarlyOut();
    }

    public function getAbsentList($data) {
        $absentList = array();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['ABS_LATE'] == 'ABSENT' && $data[$i]['EMPLOYEE_MAIL'] != null) {
                array_push($absentList, $data[$i]);
            }
        }
        $this->prepareAbsentEmail($absentList);
    }

    public function getLateList($data) {
        $lateList = array();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['ABS_LATE'] == 'LATE' && $data[$i]['EMPLOYEE_MAIL'] != null) {
                array_push($lateList, $data[$i]);
            }
        }
        $this->prepareLateEmail($lateList);
    }

    public function getMissedPunch($data) {
        $missedPunch = array();
        for ($i = 0; $i < count($data); $i++) {
            $lateStatus = $data[$i]['LATE_STATUS'];
            if (($lateStatus == 'X' || $lateStatus == 'Y') && $data[$i]['EMPLOYEE_MAIL'] != null) {
                array_push($missedPunch, $data[$i]);
            }
        }
        $this->prepareMissedEmail($missedPunch);
    }

    public function getEarlyOut($data) {
        $earlyOut = array();
        for ($i = 0; $i < count($data); $i++) {
            $lateStatus = $data[$i]['LATE_STATUS'];
            if (($lateStatus == 'E' || $lateStatus == 'B') && $data[$i]['EMPLOYEE_MAIL'] != null) {
                array_push($earlyOut, $data[$i]);
            }
        }
        $this->prepareEarlyOutEmail($earlyOut);
    }

    public function prepareEarlyOutEmail($early) {
        for ($i = 0; $i < count($early); $i++) {
            $to = $early[$i]['EMPLOYEE_MAIL'];
            $cc = $early[$i]['MANAGER_MAIL'];
            $date = $early[$i]['ATTENDANCE_DT'];
            $body = '<p>Dear Sir/Madam,</p>
<p><span style="color: red;">' . $early[$i]['EMPLOYEE_NAME'] . '</span> You have left office early at <span style="color: red;">' . $early[$i]['OUT_TIME'] . '</span> on <span style="color: red;">' . $date . '</span>.</p>
<p>Thank You.</p>
<p>Human Resource Department <br>
Nepal Bangladesh Bank Ltd.<br>
Head Office, Kamaladi</p>';
            $subject = "Early Check Out";

            $this->sendEmail($to, $body, $subject, $cc);
        }
    }

    public function prepareMissedEmail($missed) {
        for ($i = 0; $i < count($missed); $i++) {
            $to = $missed[$i]['EMPLOYEE_MAIL'];
            $cc = $missed[$i]['MANAGER_MAIL'];
            $date = (string) $missed[$i]['ATTENDANCE_DT'];
            $body = '<p>Dear Sir/Madam,</p>
<p><span style="color: red;">' . $missed[$i]['EMPLOYEE_NAME'] . '</span>  have missed punch on departure from office on <span style="color: red;">' . $date . '</span>. Kindly remember to punch to mark your presence.</p>
<p>Thank You.</p>
<p>Human Resource Department <br>
Nepal Bangladesh Bank Ltd.<br>
Head Office, Kamaladi</p>';
            $subject = "Missed Punch";

            $this->sendEmail($to, $body, $subject, $cc);
        }
    }

    public function prepareLateEmail($late) {
        for ($i = 0; $i < count($late); $i++) {
            $to = $late[$i]['EMPLOYEE_MAIL'];
            $cc = $late[$i]['MANAGER_MAIL'];

            $body = '<p>Dear Sir/Madam,</p>
<p><span style="color: red;">' . $late[$i]['EMPLOYEE_NAME'] . '</span>  have arrived office late today at <span style="color: red;">' . $late[$i]['IN_TIME'] . '</span>.</p>
<p>Thank You.</p>
<p>Human Resource Department <br>
Nepal Bangladesh Bank Ltd.<br>
Head Office, Kamaladi</p>';

            $subject = "Late Check In";

            $this->sendEmail($to, $body, $subject, $cc);
        }
    }

    public function prepareAbsentEmail($absent) {
        for ($i = 0; $i < count($absent); $i++) {
            $to = $absent[$i]['EMPLOYEE_MAIL'];
            $cc = $absent[$i]['MANAGER_MAIL'];
            $date = (string) $absent[$i]['ATTENDANCE_DT'];
            $body = '<p>Dear Sir/Madam,</p>
<p><span style="color: red;">' . $absent[$i]['EMPLOYEE_NAME'] . '</span>  have been marked absent on <span style="color: red;">' . $date . '</span>. Kindly apply for leave.</p>
<p>Thank You.</p>
<p>Human Resource Department <br>
Nepal Bangladesh Bank Ltd.<br>
Head Office, Kamaladi</p>';

            $subject = "Missing Attendance";

            $this->sendEmail($to, $body, $subject, $cc);
        }
    }

    public function sendEmail($to, $body, $subject, $cc) {
        try {
            if ($to != null) {
                $msg = new Message();
                $headers = new Headers();
                $headers->addHeaderLine('MIME-Version', '1.0');
                $headers->addHeaderLine('Content-type', 'text/html');

                $msg->setHeaders($headers);
                $msg->setSubject($subject);
                $msg->setBody($body);
                $msg->setTo($to);
                if ($cc != null) {
                    $msg->setCc($cc);
                }
                return EmailHelper::sendEmail($msg);
            } else {
                return;
            }
        } catch (Exception $ex) {
            return $ex;
        }
    }

    public function autoEmailDailyAction() {
        $directory = 'E:/test';

        if (file_exists($directory)) {
//            $allFiels = array_diff(scandir($directory), array('.', '..'));
            $allFiels = array_diff(glob($directory.'/*.{jpg}', GLOB_BRACE), array('.', '..'));
            
            print_r($allFiels);
            die();
            $fileToday = [];
            $dateToday = date("F d Y", strtotime("today"));
            foreach ($allFiels as $fileList) {
                $fileFullPath = addslashes($directory . '\\' . $fileList);
                if (file_exists($fileFullPath)) {
                    $fileModifiedDate = date("F d Y", filemtime($fileFullPath));
                    if ($fileModifiedDate == $dateToday) {
                        array_push($fileToday, array('filename' => $fileList, 'filePath' => $fileFullPath));
                    }
                }
            }

            $to=['prabin.maharjan@itnepal.com','shijan.shrestha@itnepal.com'] ;
            $cc = ['mhrpravin@gmail.com'];

            $subject = "testing";
            
            $body = '<p>Dear Sir/Madam,</p>
                <p>This is Auto Generated Message</p>';


            try {
                $this->sendEmailWithAttachment($to, $body, $subject, $cc, $fileToday);
            } catch (Exception $ex) {
                echo $ex;
            }

            echo "Email has beeen sent";

        } else {
            echo "The file $directory does not exists";
        }

        die();
    }

    public function sendEmailWithAttachment($to, $html, $subject, $cc, $attachments) {

        if ($to != null) {
            
            $message = new Message();
            $body = new MimeMessage();
            if ($to != null) {
                $message->addTo($to);
            }
            if ($cc != null) {
                $message->addCc($cc);
            }
            $message->setSubject($subject);

            // HTML part
            $htmlPart = new MimePart($html);
            $htmlPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $htmlPart->type = "text/html; charset=UTF-8";

            // Plain text part
            $textPart = new MimePart('');
            $textPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $textPart->type = "text/plain; charset=UTF-8";


            if ($attachments) {
                // With attachments, we need a multipart/related email. First part
                // is itself a multipart/alternative message        
                $content = new MimeMessage();
                $content->addPart($textPart);
                $content->addPart($htmlPart);

                $contentPart = new MimePart($content->generateMessage());
                $contentPart->type = "multipart/alternative;\n boundary=\"" .
                        $content->getMime()->boundary() . '"';

                $body->addPart($contentPart);

                $messageType = 'multipart/related';

                // Add each attachment
                foreach ($attachments as $thisAttachment) {
                    $Sfile = fopen($thisAttachment['filePath'], "rd");
                    $data = fread($Sfile, filesize($thisAttachment['filePath']));
                    fclose($Sfile);

                    $attachment = new MimePart($data);
                    $attachment->filename = $thisAttachment['filename'];
                    $attachment->type = Mime::TYPE_OCTETSTREAM;
                    $attachment->encoding = Mime::ENCODING_BASE64;
                    $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
                    $body->addPart($attachment);
                }
            } else {
                // No attachments, just add the two textual parts to the body
                $body->setParts(array($textPart, $htmlPart));
                $messageType = 'multipart/alternative';
            }
            $message->setBody($body);
            $message->getHeaders()->get('content-type')->setType($messageType);
            $message->setEncoding('UTF-8');
            return EmailHelper::sendEmail($message);
            
        } else {
            return;
        }
    }

}
