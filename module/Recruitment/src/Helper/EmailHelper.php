<?php
namespace Recruitment\Helper;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class EmailHelper {
 
    const maxMassMail = 50;
    const massEmailId = '';

    /**
     * ZOHO config
     */
    public static function getSmtpTransportZOHO(): Smtp {
        $transport = new Smtp();
        $options = new SmtpOptions([
            'host' => 'smtp.zoho.com',
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

    // public static function getSmtpTransport(): Smtp {
    //     $transport = new Smtp();
    //     $options = new SmtpOptions([
    //         'host' => 'smtp.zeptomail.com',
    //         'port' => 465,
    //         'connection_class' => 'login',
    //         'connection_config' => [
    //             'username' => 'noreply@nepaloil.org.np',
    //             'password' => 'wSsVR612rB75CqwumTSqceo+nw4EBgmkRkx+jQP04nP/S6/Ap8cynxCYUQ6uFPAdE2BsRWAa9e4tkEwA0jYGh4h7yVEIDSiF9mqRe1U4J3x17qnvhDzJV2hdmxKOKYkKxA1jk2RiGskn+g==',
    //             'ssl' => 'ssl',
    //         ],
    //     ]);
    //     $transport->setOptions($options);
    //     return $transport;
    // }

    /**
     * Gmail config
     */
    public static function getSmtpTransport(): Smtp {
        $transport = new Smtp();
        $options = new SmtpOptions([
            'host' => 'smtp-relay.sendinblue.com',
            // 'port' => 465,
            'port' => 587,
            'connection_class' => 'login',
            'connection_config' => [
                'username' => 'nepaloilcorp.noreply@gmail.com',
                'password' => 'ZL2z1vOqI5Wh0b84',
                'ssl' => 'TLS'
                // 'ssl' => 'tls'
            ],
        ]);
        $transport->setOptions($options);
        return $transport;
    }

    public static function sendEmailZOHO(Message $mail) {
        if ('development' == APPLICATION_ENV || 'staging' == APPLICATION_ENV) {
            return true;
        }

        $transport = self::getSmtpTransportZOHO();
        $connectionConfig = $transport->getOptions()->getConnectionConfig();
        $mail->setFrom($connectionConfig['username'], "Nepal Oil Corporation");
        $transport->send($mail);
        
        return true;
    }

    public static function sendEmail(Message $mail) {
        if ('development' == APPLICATION_ENV || 'staging' == APPLICATION_ENV) {
            return true;
        }

        $transport = self::getSmtpTransport();
        $connectionConfig = $transport->getOptions()->getConnectionConfig();
        $mail->setFrom($connectionConfig['username'], "Nepal Oil Corporation");
        $transport->send($mail);
        
        return true;
    }
}
