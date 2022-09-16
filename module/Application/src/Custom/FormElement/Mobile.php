<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/4/16
 * Time: 11:45 AM
 */

namespace Application\Custom\FormElement;

use Zend\Filter\StringTrim;
use Zend\Filter\StripNewlines;
use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Regex as RegexValidator;
use Zend\Validator\ValidatorInterface;

class Mobile extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'text',
    ];

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Get validator
     *
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = new RegexValidator("/\b\d{3}[-.]?\d{3}[-.]?\d{4}\b/");
        }
        return $this->validator;
    }

    /**
     * Provide default input rules for this element
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
                ['name' => StripNewlines::class],
            ],
            'validators' => [
                $this->getValidator(),
            ],
        ];
    }
}