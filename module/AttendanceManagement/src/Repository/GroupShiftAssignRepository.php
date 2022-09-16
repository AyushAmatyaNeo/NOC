<?php
namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Application\Repository\RepositoryInterface;

class GroupShiftAssignRepository implements RepositoryInterface {
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function add(Model $model) {}

    public function edit(Model $model, $id) {}

    public function fetchAll() {}

    public function fetchById($id) {}

    public function filter($branchId, $departmentId, $genderId, $designationId, $serviceTypeId, $employeeId, $companyId, $positionId, $employeeTypeId, $caseId): array {
      $searchCondition = EntityHelper::getSearchConditon($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, null, $employeeTypeId, $employeeId, $genderId);
        $sql = "SELECT
    c.company_name,
    b.branch_name,
    dep.department_name,
    e.employee_id,
    e.employee_code,
    e.full_name,
    (case when (select count(*) from hris_best_case_emp_map where employee_id = e.employee_id and case_id = $caseId) > 0 then 'Y' else 'N' end) CHECKED
    --bcem.case_id as case_id,
    --bcs.case_name as case_name
FROM
    hris_employees                                                                    e
    --LEFT JOIN
    --hris_best_case_emp_map
    --bcem ON ( e.employee_id = bcem.employee_id )
    --LEFT JOIN hris_best_case_setup                                                           bcs ON ( bcs.case_id = $caseId and bcem.case_id = bcs.case_id)
    LEFT JOIN hris_company                                                                      c ON ( e.company_id = c.company_id )
    LEFT JOIN hris_branches                                                                     b ON ( e.branch_id = b.branch_id )
    LEFT JOIN hris_departments                                                                  dep ON ( e.department_id = dep.department_id )
WHERE
    1 = 1
    AND e.status = 'E'
    {$searchCondition}
ORDER BY
    c.company_name,
    b.branch_name,
    dep.department_name,
    e.full_name
";

      $statement = $this->adapter->query($sql);
      $result = $statement->execute();
      return Helper::extractDbData($result);
    }

    public function delete($id) {}

    public function insertOrUpdate($employee_id, $case_id, $action) {
        $sql = "BEGIN HRIS_CASE_EMP_MAP({$employee_id}, {$case_id}, '{$action}'); end;";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }
}
