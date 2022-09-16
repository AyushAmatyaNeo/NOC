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

class RetirementGratuity extends AbstractActionController {

    private $adapter;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    function calculateAction() {
        $empNameFE = new Select();
        $empNameFE->setName("Employee");
        $employeeName = EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ");
        $optionAll = [-1 => "All"] + $employeeName;
        $empNameFE->setValueOptions($optionAll);
        $empNameFE->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $empNameFE->setLabel("Employee");

        $designationFE = new Text();
        $designationFE->setName("designation");
        $designationFE->setAttributes(["class" => "form-control"]);
        $designationFE->setLabel("Designation");

        $servePeriodFE = new Text();
        $servePeriodFE->setName("servePeriod");
        $servePeriodFE->setAttributes(["class" => "form-control"]);
        $servePeriodFE->setLabel("Serve Period");


        $salaryOfTheMonthFE = new Text();
        $salaryOfTheMonthFE->setName("salaryOfTheMonth");
        $salaryOfTheMonthFE->setAttributes(["class" => "form-control"]);
        $salaryOfTheMonthFE->setLabel("Salary of The Month");

        $gratuityAmountFE = new Text();
        $gratuityAmountFE->setName("gratuityAmount");
        $gratuityAmountFE->setAttributes(["class" => "form-control"]);
        $gratuityAmountFE->setLabel("Gratuity Amount ");


        $submitFE = new Submit();
        $submitFE->setName("submit");
        $submitFE->setValue("Submit");
        $submitFE->setAttribute("class", "btn btn-success");

        return Helper::addFlashMessagesToArray($this, [
                    'employees' => $empNameFE,
                    'designation' => $designationFE,
                    'servePeriod' => $servePeriodFE,
                    'salaryOfTheMonth' => $salaryOfTheMonthFE,
                    'gratuityAmount' => $gratuityAmountFE,
                    'submit' => $submitFE
        ]);
    }

}
