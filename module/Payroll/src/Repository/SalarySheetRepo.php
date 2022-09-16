<?php

namespace Payroll\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Model\Months;
use Application\Repository\HrisRepository;
use Payroll\Model\SalarySheet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class SalarySheetRepo extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, SalarySheet::TABLE_NAME);
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        return $this->tableGateway->delete([SalarySheet::MONTH_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function (Select $select) {
                    $select->columns(Helper::convertColumnDateFormat($this->adapter, new SalarySheet(), [
                                'startDate',
                                'endDate',
                            ]), false);
                });
    }

    public function fetchById($id) {
        return $this->tableGateway->select([SalarySheet::SHEET_NO => $id]);
    }

    public function fetchByIds(array $ids) {
        return $this->tableGateway->select($ids);
    }

    public function fetchOneBy(array $ids) {
        return $this->tableGateway->select($ids)->current();
    }

    public function joinWithMonth($monthId = null, $employeeJoinDate = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([]);
        $select->from(['S' => SalarySheet::TABLE_NAME])
                ->join(['M' => Months::TABLE_NAME], 'S.' . SalarySheet::MONTH_ID . '=M.' . Months::MONTH_ID);
        if ($monthId != null) {
            $select->where([Months::MONTH_ID => $monthId]);
        }
        $select->where(["M." . Months::STATUS . " = " . "'E'"]);
        $select->where(["S." . SalarySheet::STATUS . " = " . "'E'"]);

        if ($employeeJoinDate != null) {
            $select->where(["'" . $employeeJoinDate . "'" . " <= " . "M." . Months::TO_DATE]);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function generateSalShReport($sheetNo) {
        $boundedParameter = [];
        $boundedParameter['sheetNo'] = $sheetNo;
     /*   $this->executeStatement("
                          call  HRIS_GEN_SAL_SH_REPORT($sheetNo);
                        ;", $boundedParameter);*/


        $this->executeStatement("
                          call  HRIS_GEN_SAL_SH_REPORT($sheetNo);
                        ");
    }

    public function updateLoanPaymentFlag($employeeId, $sheetNo) {
        $boundedParameter = [];
        $boundedParameter['sheetNo'] = $sheetNo;
        $boundedParameter['employeeId'] = $employeeId;
       /* $this->executeStatement("BEGIN
                            hris_loan_payment_flag_change(:employeeId,:sheetNo);
                        END;", $boundedParameter); */
    }

    public function fetchAllSalaryType() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(['*']);
        $select->from('HRIS_SALARY_TYPE');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function fetchEmployeeByGroup($monthId,$group,$salaryTypeId,$employeeType) {
        $boundedParameter = [];

        $boundedParameter['monthId1'] = $monthId;
        $boundedParameter['monthId2'] = $monthId;
        $boundedParameter['salaryTypeId1'] = $salaryTypeId;
         
        $sql = "SELECT 
            E.employee_id,E.employee_code,E.full_name,'Y' AS CHECKED_FLAG
            FROM HRIS_EMPLOYEES E
            JOIN (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID= ?) MC on (1=1)
            WHERE 
            E.INCLUDE_IN_PAYROLL = 'Y' AND
            E.STATUS='E' AND 
employee_id not in (SELECT employee_id FROM HRIS_SALARY_SHEET_EMP_DETAIL SED
JOIN HRIS_SALARY_SHEET SS ON (SS.SHEET_NO=SED.SHEET_NO) 
where SS.month_id=? AND SS.SALARY_TYPE_ID=?)  
AND  E.JOIN_DATE < MC.TO_DATE 
AND GROUP_ID IN ({$group})";
// AND EMPLOYEE_TYPE IN ({$employeeType})";

if($employeeType){
    $sql.="AND EMPLOYEE_TYPE IN ({$employeeType})";
}


        if($salaryTypeId>1){
            $boundedParameter['monthId3'] = $monthId;
            $boundedParameter['salaryTypeId2'] = $salaryTypeId;
            $sql.=" and E.employee_id in (
            select distinct employee_id from HRIS_SS_PAY_VALUE_MODIFIED 
            where 
            month_id=?
            and SALARY_TYPE_ID=?
            )";
        }
       $data = $this->rawQuery($sql, $boundedParameter);
        
       return $data;
    }

    public function fetchGeneratedSheetByGroup($monthId,$group,$salaryTypeId){
        $boundedParameter = [];

        $boundedParameter['monthId'] = $monthId;
        $boundedParameter['salaryTypeId'] = $salaryTypeId;
        
        $sql="select 
                ss.sheet_no,ssg.Group_Name,Mc.Month_Edesc,St.Salary_Type_Name,
                ss.approved, ss.locked 
                from HRIS_SALARY_SHEET ss 
                join hris_salary_sheet_group ssg on (ssg.group_id=ss.group_id)
                join Hris_Month_Code mc on (mc.month_id=ss.month_id)
                join HRIS_SALARY_TYPE st on (St.Salary_Type_Id=Ss.Salary_Type_Id)
                where ss.Month_Id=? and ss.salary_type_id=?  and ss.Group_Id in ($group)";

        $data = $this->rawQuery($sql, $boundedParameter);
        return $data;
    }

    public function insertPayrollEmp($empList,$monthId,$salaryTypeId) {

        $deleteSql = "delete from HRIS_PAYROLL_EMP_LIST";
        $this->executeStatement($deleteSql);
        $boundedParameter = [];
        
        foreach ($empList as $employeeId) {
            $boundedParameter['employeeId'] = $employeeId;
            $boundedParameter['monthId'] = $monthId;
            $boundedParameter['salaryTypeId'] = $salaryTypeId;
            
//            $tempSql = "INSERT INTO HRIS_PAYROLL_EMP_LIST VALUES ({$employeeId})";
            $tempSql = "INSERT INTO HRIS_PAYROLL_EMP_LIST 
                    select * from (select ? as employee_id from dummy) 
where employee_id not in (select employee_id from Hris_Salary_Sheet_Emp_Detail ssed
join HRIS_SALARY_SHEET ss on (ss.SHEET_NO=ssed.SHEET_NO)
where ss.month_id=? and ss.SALARY_TYPE_ID=?)";

//print_r($tempSql); die;
            $this->executeStatement($tempSql, $boundedParameter);
        }
        $toGenerateGroupSql="select  distinct group_id 
                from 
                 hris_employees where employee_id in
                 (select employee_id from HRIS_PAYROLL_EMP_LIST)";
        $groupData = $this->rawQuery($toGenerateGroupSql);
        return $groupData;
    }

    public function deleteSheetBySheetNo($sheetNo){

            $boundedParameter = [];
        $boundedParameter['sheetNo1'] = $sheetNo;
        $boundedParameter['sheetNo2'] = $sheetNo;
        $boundedParameter['sheetNo3'] = $sheetNo;
        $boundedParameter['sheetNo4'] = $sheetNo;


        $sql="
        DO
        BEGIN            
        delete from HRIS_TAX_SHEET where sheet_no=$sheetNo;
        delete from HRIS_SALARY_SHEET_DETAIL where sheet_no=$sheetNo;
        delete from HRIS_SALARY_SHEET_EMP_DETAIL where sheet_no=$sheetNo;
        delete from HRIS_SALARY_SHEET where sheet_no=$sheetNo;
        END;
";
        //print_r($sql); die;
        $this->executeStatement($sql, $boundedParameter);
        //return true;
    }

    //here
    public function deleteEmployeeSalarySheet($sheetNo, $employeeId){

        $boundedParameter = [];
        $boundedParameter['sheetNo1'] = $sheetNo;
         $boundedParameter['employee_id1'] = $employeeId;

          $boundedParameter['sheetNo2'] = $sheetNo;
          $boundedParameter['employee_id2'] = $employeeId;

          $boundedParameter['sheetNo3'] = $sheetNo;
          $boundedParameter['employee_id3'] = $employeeId;
        
       

        $sql="
        DO
        BEGIN
        delete from HRIS_TAX_SHEET where sheet_no=? and employee_id = ?;
        delete from HRIS_SALARY_SHEET_DETAIL where sheet_no=? and employee_id = ?;
        delete from HRIS_SALARY_SHEET_EMP_DETAIL where sheet_no=? and employee_id = ?;
        END;
";
        
        $this->executeStatement($sql, $boundedParameter);
    }

    public function bulkApproveLock($sheetNo, $col, $val){

        $boundedParameter = [];
        $boundedParameter['val'] = $val;
        $boundedParameter['sheetNo'] = $sheetNo;
        //$boundedParameter['col'] = $col;
        
        $sql = "UPDATE HRIS_SALARY_SHEET set $col = ? where sheet_no=?";
        
        $this->executeStatement($sql, $boundedParameter);
        return true;
    }

    public function checkApproveLock($sheetNo){
        $boundedParameter = [];
        $boundedParameter['sheetNo'] = $sheetNo;

        $sql = "SELECT SHEET_NO, APPROVED, LOCKED FROM HRIS_SALARY_SHEET WHERE SHEET_NO = ?";
        
        return $this->rawQuery($sql, $boundedParameter);
        //return Helper::extractDbData($data);
    }

    public function fetchSheetWiseEmployeeList($sheetNo){
        $boundedParameter = [];
        $boundedParameter['sheetNo'] = $sheetNo;

        $sql = "select SS.SHEET_NO,SS.MONTH_ID,SS.SALARY_TYPE_ID,SSED.EMPLOYEE_ID from HRIS_SALARY_SHEET SS
JOIN HRIS_SALARY_SHEET_EMP_DETAIL SSED ON (SS.SHEET_NO=SSED.SHEET_NO)
where SS.SHEET_NO=?";
        
        return $this->rawQuery($sql, $boundedParameter);
        //return Helper::extractDbData($data);
    }

    public function fetchCompanyByGroup($groupId) {
             $boundedParameter = [];
        $boundedParameter['groupId'] = $groupId;
        $sql = "select company_id from HRIS_EMPLOYEES where GROUP_ID = ?  limit 1";
        //print_r($sql); die;
        $data = $this->rawQuery($sql, $boundedParameter);
        return $data[0]['COMPANY_ID'];
//        return $this->rawQuery($sql, $boundedParameter);
    }
}
