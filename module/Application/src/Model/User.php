<?php
/**
 * Created by PhpStorm.
 * User: himal
 * Date: 7/20/16
 * Time: 12:23 PM
 */

namespace Application\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("User")
 */

class User
{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Username:", "label_attributes":{"class":"sr-only"}})
     */
    public $username;

    /**
     * @Annotation\Type("Zend\Form\Element\Password")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Password:", "label_attributes":{"class":"sr-only"}})
     */
    public $password;

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * * @Annotation\Required(false)
     * @Annotation\Options({"label":"Remember Me ?:", "label_attributes":{"id":"remember-me"}})
     */
    public $rememberme;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Login"})
     */
    public $submit;
}