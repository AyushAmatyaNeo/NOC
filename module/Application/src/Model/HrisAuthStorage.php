<?php
/**
 * Created by PhpStorm.
 * User: himal
 * Date: 7/20/16
 * Time: 12:22 PM
 */

namespace Application\Model;

use Zend\Authentication\Storage;

class HrisAuthStorage extends Storage\Session
{
    public function setRememberMe($rememberMe = 0, $time = 1209600)
    {
        if (1 == $rememberMe) {
            $this->session->getManager()->rememberMe($time);
        }
    }

    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    }
}