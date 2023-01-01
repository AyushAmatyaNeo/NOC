<?php
namespace JobResponsibilityManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use JobResponsibilityManagement\Model\JobResponsibility;
use JobResponsibilityManagement\Model\JobResponsibilityAssign;
use Zend\Db\TableGateway\TableGateway;

class JobResponsibilityRepository extends HrisRepository implements RepositoryInterface {

    public function __construct(AdapterInterface $adapter) {
        parent::__construct($adapter, JobResponsibility::TABLE_NAME);
        $this->empJobResAssignGateway = new TableGateway(JobResponsibilityAssign::TABLE_NAME, $adapter);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
            $select->where(['STATUS' => 'E']);
        });
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select)use($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(JobResponsibility::class, [JobResponsibility::JOB_RES_ENG_NAME]), false);
            $select->where([JobResponsibility::ID => $id, JobResponsibility::STATUS => 'E']);
        });
        return $rowset->current();
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [JobResponsibility::ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([JobResponsibility::STATUS => 'D'], ["ID" => $id]);
    }

    public function fetchAllEmpJRAssign() {
        $sql = "select JRA.ID,E.full_name, JR.JOB_RES_ENG_NAME, E.employee_code, JOB_RES_NEP_NAME from HRIS_EMPLOYEE_JOB_RESPONSIBILITY_ASSIGN JRA
        left join hris_employees E on (E.employee_id = JRA.employee_id)
        left join HRIS_JOB_RESPONSIBILITY JR on (JR.id = JRA.JOB_RESPONSIBILITY_ID)
        where JRA.status='E' order by E.full_name";
        return $this->rawQuery($sql);
    }

    public function addJRAssign(Model $model) {
        $this->rawQuery("update HRIS_EMPLOYEE_JOB_RESPONSIBILITY_ASSIGN set status='D', remarks='Auto deeted when next inserted' where employee_id = {$model->employeeId} and  JOB_RESPONSIBILITY_ID = {$model->jobResponsibilityId}");
        $this->empJobResAssignGateway->insert($model->getArrayCopyForDB());
    }

    public function filter($locationId, $departmentId, $genderId, $designationId, $serviceTypeId, $employeeId, $companyId, $positionId, $employeeTypeId, $leaveId, $jobResId): array {

        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, null, $departmentId, $positionId, $designationId, $serviceTypeId, null, $employeeTypeId, $employeeId, $genderId, $locationId);


        $sql = "SELECT C.COMPANY_NAME,
                  B.BRANCH_NAME,
                  DEP.DEPARTMENT_NAME,
                  E.EMPLOYEE_ID,
                  E.EMPLOYEE_CODE,
                  E.FULL_NAME,
                  L.location_edesc as LOCATION_EDESC,
                  $jobResId as JOB_RES_ID
                FROM HRIS_EMPLOYEES E
                LEFT JOIN HRIS_COMPANY C
                ON (E.COMPANY_ID=C.COMPANY_ID)
                LEFT JOIN HRIS_BRANCHES B
                ON (E.BRANCH_ID=B.BRANCH_ID)
                LEFT JOIN HRIS_LOCATIONS L
                ON (E.LOCATION_ID=L.LOCATION_ID)
                LEFT JOIN HRIS_DEPARTMENTS DEP
                ON (E.DEPARTMENT_ID=DEP.DEPARTMENT_ID)
                WHERE 1            =1 AND E.STATUS='E'
                {$searchCondition['sql']}
                ORDER BY C.COMPANY_NAME,L.LOCATION_EDESC,DEP.DEPARTMENT_NAME,E.FULL_NAME";
// echo('<pre>');print_r($sql);die;
        return $this->rawQuery($sql, $searchCondition['parameter']);
    }

    public function deleteJRA($id) {
        $this->empJobResAssignGateway->update([JobResponsibilityAssign::STATUS => 'D'], ["ID" => $id]);
    }
}
