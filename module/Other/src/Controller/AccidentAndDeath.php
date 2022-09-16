<?php

namespace Other\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Mvc\Controller\AbstractActionController;

class AccidentAndDeath extends AbstractActionController {

    private $adapter;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function addAction() {
        $empNameFE = new Select();
        $empNameFE->setName("Employee");
        $employeeName = EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ");
        $optionAll = [-1 => "All"] + $employeeName;
        $empNameFE->setValueOptions($optionAll);
        $empNameFE->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $empNameFE->setLabel("Employee");


        $accDateFE = new Text();
        $accDateFE->setName("accDate");
        $accDateFE->setAttributes(["class" => "form-control"]);
        $accDateFE->setLabel("Accident Date");

        $accTypeFE = new Select();
        $accTypeFE->setName("accType");
        $accTypeFE->setValueOptions([1 => "Minor", 2 => "Partially Disabled", 3 => "Totally Disabled", 4 => "Death"]);
        $accTypeFE->setAttributes(["class" => "form-control"]);
        $accTypeFE->setLabel("Account Type");

        $lifeInsuranceFE = new Text();
        $lifeInsuranceFE->setName("lifeInsurance");
        $lifeInsuranceFE->setAttributes(["class" => "form-control"]);
        $lifeInsuranceFE->setLabel("Life Insurance");

        $isOnWork = new Select();
        $isOnWork->setName("isOnWork");
        $isOnWork->setValueOptions([1 => "Yes", 2 => "No"]);
        $isOnWork->setAttributes(["class" => "form-control"]);
        $isOnWork->setLabel("Is On Work");


        $submitFE = new Submit();
        $submitFE->setName("submit");
        $submitFE->setValue("Submit");
        $submitFE->setAttribute("class", "btn btn-success");

        return Helper::addFlashMessagesToArray($this, [
                    'employees' => $empNameFE,
                    'accDate' => $accDateFE,
                    'accType' => $accTypeFE,
                    'lifeInsurance' => $lifeInsuranceFE,
                    'isOnWork' => $isOnWork,
                    'submit' => $submitFE
        ]);
    }

}
