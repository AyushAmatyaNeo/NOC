<?php

namespace SelfService\Repository;

use Application\Repository\HrisRepository;
use LeaveManagement\Model\LeaveAssign;
use Traversable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Application\Helper\EntityHelper;
use JobResponsibilityManagement\Model\JobResponsibility;

class JobResponsibilityRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = LeaveAssign::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    public function fetchAllEmp($empId) {
      $sql = "select JR.ID,E.full_name, E.employee_code, JR.JOB_RES_ENG_NAME, JOB_RES_NEP_NAME from HRIS_EMPLOYEE_JOB_RESPONSIBILITY_ASSIGN JRA
      left join hris_employees E on (E.employee_id = JRA.employee_id)
      left join HRIS_JOB_RESPONSIBILITY JR on (JR.id = JRA.JOB_RESPONSIBILITY_ID)
      where JRA.status='E' and E.employee_id = $empId order by E.full_name";
      return $this->rawQuery($sql);
    }

    public function fetchById($id) {
      $sql = "select * from HRIS_JOB_RESPONSIBILITY where id = $id";
      return $this->rawQuery($sql);
    }

}
