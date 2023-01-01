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
      $sql = "select JRA.ID,E.full_name, JR.JOB_RES_ENG_NAME, E.employee_code, JOB_RES_NEP_NAME,
      AE.full_name as Assigned_by, JRA.start_date, ifnull(to_char(JRA.end_date), 'Ongoing') as END_DATE 
      from HRIS_EMPLOYEE_JOB_RESPONSIBILITY_ASSIGN JRA
      left join hris_employees E on (E.employee_id = JRA.employee_id)
      left join HRIS_JOB_RESPONSIBILITY JR on (JR.id = JRA.JOB_RESPONSIBILITY_ID)
      left join hris_employees AE on (AE.employee_id = JRA.assigned_by)
      where JRA.status='E' and E.employee_id = $empId order by E.full_name";
      return $this->rawQuery($sql);
    }

    public function fetchById($id) {
      $sql = "
      select JR.*,
      JRA.start_date,
      ifnull(to_char(JRA.end_date),'Ongoing') as END_DATE,
      E.employee_code || ' - ' || E.full_name as ASSIGNED_BY from HRIS_EMPLOYEE_JOB_RESPONSIBILITY_ASSIGN JRA
      left join hris_job_responsibility JR
      on (JR.id = JRA.job_responsibility_id)
      left join hris_employees E on (E.employee_id = JRA.assigned_by)
      where JRA.id = $id";
      return $this->rawQuery($sql);
    }

}
