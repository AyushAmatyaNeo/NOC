<?php

namespace Application\Controller;

use Application\Helper\EmailHelper;
use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class CronBrij extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
        return new JsonModel(['success' => true, 'data' => '', 'message' => 'No data found']);
    }


    public function autoEmailDailyAction() {
        $directory = 'E:/test';

        if (file_exists($directory)) {
            $allFiels = array_diff(scandir($directory), array('.', '..'));
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
