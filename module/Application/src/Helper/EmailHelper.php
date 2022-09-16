<?php
namespace Application\Helper;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class EmailHelper {

    const maxMassMail = 50;
    const massEmailId = '';

//     public static function getSmtpTransport(): Smtp {
//         $transport = new Smtp();
//         $options = new SmtpOptions([
//             'host' => 'namesmtp.zoho.com',
//             'port' => 465,
//             'connection_class' => 'login',
//             'connection_config' => [
// // 'username' => 'ukesh.gaiju@itnepal.com',
// // 'password' => 'ukesh@123',
//                 'username' => 'noreply@nepaloil.org.np',
//                 'password' => 'Erpnocnepal@1',
//                 'ssl' => 'ssl',
//             ],
//         ]);
//         $transport->setOptions($options);
//         return $transport;
//     }

    public static function getSmtpTransport(): Smtp {
        $transport = new Smtp();
        $options = new SmtpOptions([
            'host' => 'smtppro.zoho.com',
            'port' => 465,
            'connection_class' => 'login',
            'connection_config' => [
                'username' => 'noreply@nepaloil.org.np',
                'password' => 'Erpnocnepal@1',
                'ssl' => 'ssl',
            ],
        ]);
        $transport->setOptions($options);
        return $transport;
    }

    public static function sendEmail(Message $mail) {
        if ('development' == APPLICATION_ENV || 'staging' == APPLICATION_ENV) {
            return true;
        }
        $transport = self::getSmtpTransport();
        $connectionConfig = $transport->getOptions()->getConnectionConfig();
        $mail->setFrom($connectionConfig['username']);
        $transport->send($mail);
        return true;
    }
}
