<?php

namespace AutoNotification\Controller;

use Application\Helper\EmailHelper;
use AutoNotification\Repository\AutoNotificationRepository;
use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mail\Headers;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\View\Model\JsonModel;
use Application\Controller\HrisController;
use Zend\Authentication\Storage\StorageInterface;

class AutoNotificationController extends HrisController {

    protected $adapter;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AutoNotificationRepository::class);
        $this->adapter = $adapter;
    }

    public function indexAction() {
        $notifications = $this->repository->fetchCurrentNotifications();
        $this->sendEmail(); echo 'Success'; die;
        // foreach($notifications as $notification){
            
        // }
        // return new JsonModel(['success' => true, 'data' => '', 'message' => 'No data found']);
    }

    public function sendEmail(){
        $isValidEmail = function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        };
        // $emailTemplateRepo = new \Notification\Repository\EmailTemplateRepo($adapter);
        // $template = $emailTemplateRepo->fetchById($type);

        // if (null == $template) {
        //     throw new Exception('Email template not set.');
        // }
        $mail = new Message();
        // $mail->setSubject($model->processString($template['SUBJECT'], $url));
        $mail->setSubject('Happy Birthday');
        // $htmlDescription = self::mailHeader();
        // $htmlDescription .= $model->processString($template['DESCRIPTION'], $url);
        $htmlDescription .= 'Happy Birthday good sir!';
        // $htmlDescription .= self::mailFooter();

        $htmlPart = new MimePart($htmlDescription);
        $htmlPart->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($htmlPart));

        $mail->setBody($body);

        // if (!isset($model->fromEmail) || $model->fromEmail == null || $model->fromEmail == '' || !$isValidEmail($model->fromEmail)) {
        //     throw new Exception("Sender email is not set or valid.");
        // }
        // if (!isset($model->toEmail) || $model->toEmail == null || $model->toEmail == '' || !$isValidEmail($model->toEmail)) {
        //     throw new Exception("Receiver email is not set or valid.");
        // }
        $mail->addTo('intern4@neosoftware.com.np', '');
        $mail->addTo('intern5@neosoftware.com.np', '');
        // $cc = (array) json_decode($template['CC']);
        // foreach ($cc as $ccObj) {
        //     $ccObj = (array) $ccObj;
        //     $mail->addCc($ccObj['email'], $ccObj['name']);
        // }

        // $bcc = (array) json_decode($template['BCC']);
        // foreach ($bcc as $bccObj) {
        //     $bccObj = (array) $bccObj;
        //     $mail->addBcc($bccObj['email'], $bccObj['name']);
        // }
        EmailHelper::sendEmail($mail);
    }

    public function sendNotification(){

    }

}
