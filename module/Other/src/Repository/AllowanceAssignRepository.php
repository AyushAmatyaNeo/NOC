<?php
namespace Other\Repository;

use Application\Helper\EntityHelper;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Gender;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;

class AllowanceAssignRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function filterEmployees($employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $companyId, $genderId = null, $employeeTypeId = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns(
                EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [
                    HrEmployees::BIRTH_DATE,
                    HrEmployees::FAM_SPOUSE_BIRTH_DATE,
                    HrEmployees::FAM_SPOUSE_WEDDING_ANNIVERSARY,
                    HrEmployees::ID_DRIVING_LICENCE_EXPIRY,
                    HrEmployees::ID_CITIZENSHIP_ISSUE_DATE,
                    HrEmployees::ID_PASSPORT_EXPIRY,
                    HrEmployees::JOIN_DATE
                        ], NULL, NULL, NULL, 'E'), false);

        $select->from(["E" => "HRIS_EMPLOYEES"]);

        $select
                ->join(['B' => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . "=B." . Branch::BRANCH_ID, ['BRANCH_NAME' => new Expression('(B.BRANCH_NAME)')], 'left')
                ->join(['D' => Department::TABLE_NAME], "E." . HrEmployees::DEPARTMENT_ID . "=D." . Department::DEPARTMENT_ID, ['DEPARTMENT_NAME' => new Expression('(D.DEPARTMENT_NAME)')], 'left')
                ->join(['DES' => Designation::TABLE_NAME], "E." . HrEmployees::DESIGNATION_ID . "=DES." . Designation::DESIGNATION_ID, ['DESIGNATION_TITLE' => new Expression('(DES.DESIGNATION_TITLE)')], 'left')
                ->join(['C' => Company::TABLE_NAME], "E." . HrEmployees::COMPANY_ID . "=C." . Company::COMPANY_ID, ['COMPANY_NAME' => new Expression('(C.COMPANY_NAME)')], 'left')
                ->join(['G' => Gender::TABLE_NAME], "E." . HrEmployees::GENDER_ID . "=G." . Gender::GENDER_ID, ['GENDER_NAME' => new Expression('INITCAP(G.GENDER_NAME)')], 'left')
                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "E." . HrEmployees::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left')
                ->join(['RG' => "HRIS_RELIGIONS"], "E." . HrEmployees::RELIGION_ID . "=RG.RELIGION_ID", ['RELIGION_NAME'], 'left')
                ->join(['CN' => "HRIS_COUNTRIES"], "E." . HrEmployees::COUNTRY_ID . "=CN.COUNTRY_ID", ['COUNTRY_NAME' => new Expression('INITCAP(CN.COUNTRY_NAME)')], 'left')
                ->join(['VM' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_PERM_VDC_MUNICIPALITY_ID . "=VM.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME' => new Expression('INITCAP(VM.VDC_MUNICIPALITY_NAME)')], 'left')
                ->join(['VM1' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_TEMP_VDC_MUNICIPALITY_ID . "=VM1.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME_TEMP' => 'VDC_MUNICIPALITY_NAME'], 'left')
                ->join(['D1' => Department::TABLE_NAME], "E." . HrEmployees::APP_DEPARTMENT_ID . "=D1." . Department::DEPARTMENT_ID, ['APP_DEPARTMENT_NAME' => new Expression('(D1.DEPARTMENT_NAME)')], 'left')
                ->join(['DES1' => Designation::TABLE_NAME], "E." . HrEmployees::APP_DESIGNATION_ID . "=DES1." . Designation::DESIGNATION_ID, ['APP_DESIGNATION_TITLE' => new Expression('(DES1.DESIGNATION_TITLE)')], 'left')
                ->join(['P1' => Position::TABLE_NAME], "E." . HrEmployees::POSITION_ID . "=P1." . Position::POSITION_ID, ['POSITION_NAME' => new Expression('(P1.POSITION_NAME)')], 'left')
                ->join(['S1' => ServiceType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_TYPE_ID . "=S1." . ServiceType::SERVICE_TYPE_ID, ['APP_SERVICE_TYPE_NAME' => new Expression('INITCAP(S1.SERVICE_TYPE_NAME)')], 'left')
                ->join(['SE1' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_EVENT_TYPE_ID . "=SE1." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['APP_SERVICE_EVENT_TYPE_NAME' => new Expression('INITCAP(SE1.SERVICE_EVENT_TYPE_NAME)')], 'left');

        $select->where(["E.STATUS='E'"]);

        if ($serviceEventTypeId == 5 || $serviceEventTypeId == 8 || $serviceEventTypeId == 14) {
            $select->where(["E.RETIRED_FLAG='Y'"]);
        } else {
            $select->where(["E.RETIRED_FLAG='N'"]);
        }

        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $select->where([
                "E.EMPLOYEE_TYPE= '{$employeeTypeId}'"
            ]);
        }

        if ($employeeId != -1) {
            $select->where([
                "E.EMPLOYEE_ID=" . $employeeId
            ]);
        }
        if ($companyId != -1) {
            $select->where([
                "E.COMPANY_ID=" . $companyId
            ]);
        }
        if ($branchId != -1) {
            $select->where([
                "E.BRANCH_ID=" . $branchId
            ]);
        }
        if ($departmentId != -1) {
            $select->where([
                "E.DEPARTMENT_ID=" . $departmentId
            ]);
        }
        if ($designationId != -1) {
            $select->where([
                "E.DESIGNATION_ID=" . $designationId
            ]);
        }
        if ($positionId != -1) {
            $select->where([
                "E.POSITION_ID=" . $positionId
            ]);
        }
        if ($serviceTypeId != -1) {
            $select->where([
                "E.SERVICE_TYPE_ID=" . $serviceTypeId
            ]);
        }
        if ($serviceEventTypeId != -1) {
            $select->where([
                "E.SERVICE_EVENT_TYPE_ID=" . $serviceEventTypeId
            ]);
        }
        if ($genderId != null && $genderId != -1) {
            $select->where([
                "E.GENDER_ID=" . $genderId
            ]);
        }
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function getHolidayAssignedEmployees($holidayId) {
        return EntityHelper::rawQueryResult($this->adapter, "SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEE_HOLIDAY WHERE HOLIDAY_ID='{$holidayId}'");
    }

    public function multipleEmployeeAssignToHoliday($holidayId, $employeeIdList) {
        EntityHelper::rawQueryResult($this->adapter, "DELETE FROM HRIS_EMPLOYEE_HOLIDAY WHERE HOLIDAY_ID={$holidayId}");
        foreach ($employeeIdList as $empId) {
            EntityHelper::rawQueryResult($this->adapter, "INSERT INTO HRIS_EMPLOYEE_HOLIDAY(HOLIDAY_ID,EMPLOYEE_ID) VALUES({$holidayId},{$empId})");
        }
    }

}
