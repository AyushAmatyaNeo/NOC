<?php

namespace Appraisal\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class EvaluationAndReview extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
        
    }

    public function evaluationAction() {
        
    }

    public function reviewAction() {
        
    }

}
