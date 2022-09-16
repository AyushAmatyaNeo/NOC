<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\ContractAttendanceModel;
use Customer\Model\CustContractEmp;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class CustContractEmpRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(CustContractEmp::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->gateway->update([CustContractEmp::STATUS => EntityHelper::STATUS_DISABLED], [CustContractEmp::EMP_ASSIGN_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [CustContractEmp::EMP_ASSIGN_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = "select CONTRACT_ID,EMPLOYEE_ID,OLD_EMPLOYEE_ID,
            TO_CHAR(START_DATE, 'DD/MM/YYYY') AS START_DATE, 
            TO_CHAR(END_DATE, 'DD/MM/YYYY') AS END_DATE, 
            TO_CHAR(ASSIGNED_DATE, 'DD/MM/YYYY') AS ASSIGNED_DATE 
            from HRIS_CONTRACT_EMP_ASSIGN WHERE 
                CONTRACT_ID=$id";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function updateEmployeeAttendance(Model $model, $updateArray) {
        $tempArray = $model->getArrayCopyForDB();

        $custmomerAttendanceGateway = new TableGateway(ContractAttendanceModel::TABLE_NAME, $this->adapter);
        $custmomerAttendanceGateway->update($tempArray, $updateArray);
    }

    public function getAllMonthBetweenTwoDates($startDate, $endDate) {
        $sql = "select MONTH_ID,MONTH_EDESC,FROM_DATE,TO_DATE,YEAR,MONTH_NO,(YEAR||' '||MONTH_EDESC) AS MONTH_TITLE from hris_month_code where 
                month_id between 
                (select month_id from hris_month_code where '{$startDate}' between from_date and to_date)
                and 
                (
                select month_id from (select  month_id from hris_month_code where '{$endDate}' between from_date and to_date
                union ALL
                select max(month_id) from hris_month_code) where rownum=1
                )";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getAllMonthWiseEmployees($contractId, $monthId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustContractEmp::class, NULL, NULL, [
                    CustContractEmp::START_TIME,
                    CustContractEmp::END_TIME
                        ], null, null, null, false, false, [
                    CustContractEmp::WORKING_HOUR,
                ]), false);
        $select->from(CustContractEmp::TABLE_NAME);
        $select->where([CustContractEmp::MONTH_CODE_ID => $monthId, CustContractEmp::CONTRACT_ID => $contractId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function deleteContractEmpMonthly($id, $monthId) {
        $this->gateway->delete([CustContractEmp::CONTRACT_ID => $id, CustContractEmp::MONTH_CODE_ID => $monthId]);
    }

    public function getEmployeeAssignedDesignationWise($contractId, $designationId, $dutyTypeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustContractEmp::class, NULL, NULL, [
                    CustContractEmp::START_TIME,
                    CustContractEmp::END_TIME
                        ], null, null, null, false, false, null), false);
        $select->from(CustContractEmp::TABLE_NAME);
        $select->where([CustContractEmp::CONTRACT_ID => $contractId, CustContractEmp::STATUS => 'E']);
        if ($designationId) {
            $select->where([CustContractEmp::DESIGNATION_ID => $designationId]);
        }
        if ($dutyTypeId) {
            $select->where([CustContractEmp::DUTY_TYPE_ID => $dutyTypeId]);
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getEmployeeAssignedContractWise($contractId) {
        $sql = "SELECT ce.CONTRACT_ID AS CONTRACT_ID, ce.CUSTOMER_ID AS CUSTOMER_ID, ce.LOCATION_ID AS LOCATION_ID,
             ce.EMPLOYEE_ID AS EMPLOYEE_ID,ce.DESIGNATION_ID AS DESIGNATION_ID,
             INITCAP(TO_CHAR(ce.START_TIME, 'HH:MI AM')) AS START_TIME,
             INITCAP(TO_CHAR(ce.END_TIME, 'HH:MI AM')) AS END_TIME,
             ce.REMARKS AS REMARKS,
             ce.STATUS AS STATUS,
             INITCAP(TO_CHAR(ce.START_DATE, 'DD-MON-YYYY')) AS START_DATE_AD,
             BS_DATE(ce.START_DATE)               AS START_DATE_BS,
             INITCAP(TO_CHAR(ce.END_DATE, 'DD-MON-YYYY')) AS END_DATE_AD,
             BS_DATE(END_DATE)               AS END_DATE_BS,
             ce.EMP_ASSIGN_ID AS EMP_ASSIGN_ID,
             CL.LOCATION_NAME,e.full_name,d.designation_title,
             DT.DUTY_TYPE_ID,DT.DUTY_TYPE_NAME,
             CE.MONTHLY_RATE AS MONTHLY_RATE
             FROM HRIS_CONTRACT_EMP_ASSIGN ce
             left join hris_employees e on (ce.EMPLOYEE_ID=e.employee_id)
             left join HRIS_DESIGNATIONS d on (d.DESIGNATION_ID=ce.designation_id)
             left join HRIS_CUSTOMER_LOCATION CL ON (CL.LOCATION_ID=CE.LOCATION_ID)
             left join HRIS_DUTY_TYPE DT ON (DT.DUTY_TYPE_ID=CE.DUTY_TYPE_ID)
             where ce.STATUS='E' and ce.contract_id={$contractId}
                  ORDER BY E.FULL_NAME
                 --ORDER BY CE.END_DATE DESC,CE.DESIGNATION_ID";
//             echo $sql;
//             die();
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getContractEmpLocDesWise($contractId, $employeeId, $locationId, $designationId) {
        $sql = "SELECT CONTRACT_ID AS CONTRACT_ID, CUSTOMER_ID AS CUSTOMER_ID, LOCATION_ID AS LOCATION_ID,
             EMPLOYEE_ID AS EMPLOYEE_ID, DESIGNATION_ID AS DESIGNATION_ID,
             INITCAP(TO_CHAR(START_TIME, 'HH:MI AM')) AS START_TIME,
             INITCAP(TO_CHAR(END_TIME, 'HH:MI AM')) AS END_TIME,
             LAST_ASSIGNED_DATE AS LAST_ASSIGNED_DATE, CREATED_BY AS CREATED_BY,
             MODIFIED_DT AS MODIFIED_DT, MODIFIED_BY AS MODIFIED_BY, REMARKS AS REMARKS,
             STATUS AS STATUS,
             INITCAP(TO_CHAR(START_DATE, 'DD-MON-YYYY')) AS START_DATE_AD,
             BS_DATE(START_DATE)               AS START_DATE_BS,
             INITCAP(TO_CHAR(END_DATE, 'DD-MON-YYYY')) AS END_DATE_AD,
             BS_DATE(END_DATE)               AS END_DATE_BS,
             EMP_ASSIGN_ID AS ID FROM HRIS_CONTRACT_EMP_ASSIGN
             WHERE CONTRACT_ID = {$contractId}
             AND EMPLOYEE_ID = {$employeeId} AND LOCATION_ID = {$locationId} 
             AND DESIGNATION_ID = {$designationId} AND STATUS = 'E'";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function getContractDetail($contract, $designation, $dutyType) {

        $sql = "select * from HRIS_CUSTOMER_CONTRACT_DETAILS where status='E'
            and contract_id={$contract}
            and designation_id={$designation}
            and duty_type_id={$dutyType}";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function pullEmployeeAssignDataById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(CustContractEmp::class, NULL, NULL, [
                    CustContractEmp::START_TIME,
                    CustContractEmp::END_TIME
                        ], null, null, null, false, false, null), false);
        $select->from(CustContractEmp::TABLE_NAME);
        $select->where([CustContractEmp::EMP_ASSIGN_ID => $id, CustContractEmp::STATUS => 'E']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    
    public function pullEmployeeRate($dutyTypeId, $employeeId) {
        $sql="SELECT E.HOURLY_AMOUNT,DT.NORMAL_HOUR
                FROM HRIS_EMPLOYEES E
                JOIN HRIS_DUTY_TYPE DT ON(DT.DUTY_TYPE_ID={$dutyTypeId})
                WHERE E.EMPLOYEE_ID={$employeeId}";
        
        
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
        
    }

}
