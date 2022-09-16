<?php

namespace Other\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Number;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Mvc\Controller\AbstractActionController;

class PaymentSuspended extends AbstractActionController {

    private $adapter;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    function indexAction() {
        
    }

    public function addAction() {
        $empNameFE = new Select();
        $empNameFE->setName("Employee");
        $employeeName = EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ");
        $optionAll = [-1 => "All"] + $employeeName;
        $empNameFE->setValueOptions($optionAll);
        $empNameFE->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $empNameFE->setLabel("Employee");

        $startDateFe = new Text();
        $startDateFe->setName("startDate");
        $startDateFe->setAttributes(["class" => "form-control"]);
        $startDateFe->setLabel("Start Date");

        $endDateFE = new Text();
        $endDateFE->setName("endDate");
        $endDateFE->setAttributes(["class" => "form-control"]);
        $endDateFE->setLabel("End Date");


        $reason = new Textarea();
        $reason->setName("reason");
        $reason->setAttributes(["class" => "form-control"]);
        $reason->setLabel("Reason");

        $suspendedRate = new Number();
        $suspendedRate->setName("suspendedRate");
        $suspendedRate->setAttributes(["class" => "form-control"]);
        $suspendedRate->setLabel("Suspended Rate(%)");

        $suspensionAmount = new Number();
        $suspensionAmount->setName("suspensionAmount");
        $suspensionAmount->setAttributes(["class" => "form-control"]);
        $suspensionAmount->setLabel("Suspension Amount");

        $submitFE = new Submit();
        $submitFE->setName("submit");
        $submitFE->setValue("Submit");
        $submitFE->setAttribute("class", "btn btn-success");

        return Helper::addFlashMessagesToArray($this, [
                    'employees' => $empNameFE,
                    'startDate' => $startDateFe,
                    'endDate' => $endDateFE,
                    'reason' => $reason,
                    'suspendedRate' => $suspendedRate,
                    'suspensionAmount' => $suspensionAmount,
                    'submit' => $submitFE
        ]);
    }

}
