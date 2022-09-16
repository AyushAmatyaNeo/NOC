<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\RecommendApproveForm;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Setup\Model\RecommendApprove;
use Setup\Repository\EmployeeRepository;
use SelfService\Form\RoleTransferForm;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\View\Model\JsonModel;

class RoleTransfer extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(RecommendApproveRepository::class);
        $this->initializeForm(RoleTransferForm::class);
    }

    public function indexAction() {
        
        $request = $this->getRequest();
        
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeList' => EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_CODE", "FULL_NAME"], ["STATUS" => "E"], " - "),
        ]);
    }

    public function transferRoleAction() {
        $request = $this->getRequest();
        print_r($this->employeeId);
        print_r(' ');
        print_r($request->getPost('recommender'));
        print_r(' ');
        print_r($request->getPost('approver'));
        die();
    }

}
