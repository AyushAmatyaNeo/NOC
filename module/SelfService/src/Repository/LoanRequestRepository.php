<?php

namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\LoanRequest;
use SelfService\Model\LoanEmiDetail;
use Setup\Model\HrEmployees;
use Setup\Model\Loan;
use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class LoanRequestRepository extends HrisRepository implements RepositoryInterface {


    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(LoanRequest::TABLE_NAME, $adapter);
        $this->emiTableGateway = new TableGateway(LoanEmiDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([LoanRequest::STATUS => 'C'], [LoanRequest::LOAN_REQUEST_ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY')) AS LOAN_DATE"),
            new Expression("LR.STATUS AS STATUS"),
            new Expression("LR.REPAYMENT_MONTHS AS REPAYMENT_MONTHS"),
            new Expression("LR.APPLIED_LOAN AS APPLIED_LOAN"),
            new Expression("LR.PERIOD AS PERIOD"),
            new Expression("LR.REPAYMENT_INSTALLMENTS AS REPAYMENT_INSTALLMENTS"),
            new Expression("LR.INTEREST_RATE AS INTEREST_RATE"),
            new Expression("LR.MONTHLY_INSTALLMENT_AMOUNT AS MONTHLY_INSTALLMENT_AMOUNT"),
            new Expression("INITCAP(TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(LR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("INITCAP(TO_CHAR(LR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("LR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("LR.LOAN_REQUEST_ID AS LOAN_REQUEST_ID"),
            new Expression("LR.REASON AS REASON"),
            new Expression("LR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("LR.FILE_PATH AS FILE_PATH"),
            new Expression("LR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("LR.APPROVED_BY AS APPROVED_BY"),
            new Expression("LR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("LR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LR.LOAN_ID AS LOAN_ID"),
            new Expression("LR.MONTHID"),
            new Expression("LR.FISCAL_YEAR_ID"),
                ], true);

        $select->from(['LR' => LoanRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=LR." . LoanRequest::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['L' => Loan::TABLE_NAME], "L." . Loan::LOAN_ID . "=LR." . LoanRequest::LOAN_ID, [Loan::LOAN_CODE, "LOAN_NAME" => new Expression("INITCAP(L.LOAN_NAME)")])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=LR.RECOMMENDED_BY", ['FN1' => new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LR.APPROVED_BY", ['FN2' => new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")], "left");

        $select->where([
            "LR.LOAN_REQUEST_ID =" . $id
        ]);
        $select->order("LR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllByEmployeeId($employeeId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("INITCAP(TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY')) AS LOAN_DATE_AD"),
            new Expression("BS_DATE(LR.LOAN_DATE) AS LOAN_DATE_BS"),
            new Expression("LR.STATUS AS STATUS"),
            new Expression("INITCAP(TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD"),
            new Expression("BS_DATE(LR.REQUESTED_DATE) AS REQUESTED_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(LR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("INITCAP(TO_CHAR(LR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("LR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("LR.LOAN_REQUEST_ID AS LOAN_REQUEST_ID"),
            new Expression("LR.REASON AS REASON"),
            new Expression("LR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("LR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("LR.LOAN_ID AS LOAN_ID")
                ], true);

        $select->from(['LR' => LoanRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E." . HrEmployees::EMPLOYEE_ID . "=LR." . LoanRequest::EMPLOYEE_ID, ["FIRST_NAME" => new Expression("INITCAP(E.FIRST_NAME)"), "MIDDLE_NAME" => new Expression("INITCAP(E.MIDDLE_NAME)"), "LAST_NAME" => new Expression("INITCAP(E.LAST_NAME)")])
                ->join(['L' => Loan::TABLE_NAME], "L." . Loan::LOAN_ID . "=LR." . LoanRequest::LOAN_ID, [Loan::LOAN_CODE, "LOAN_NAME" => new Expression("INITCAP(L.LOAN_NAME)")])
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=LR.RECOMMENDED_BY", ['FN1' => new Expression("INITCAP(E1.FIRST_NAME)"), 'MN1' => new Expression("INITCAP(E1.MIDDLE_NAME)"), 'LN1' => new Expression("INITCAP(E1.LAST_NAME)")], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=LR.APPROVED_BY", ['FN2' => new Expression("INITCAP(E2.FIRST_NAME)"), 'MN2' => new Expression("INITCAP(E2.MIDDLE_NAME)"), 'LN2' => new Expression("INITCAP(E2.LAST_NAME)")], "left");

        $select->where([
            "E.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->where([
            "days_between(LR.REQUESTED_DATE, current_date) < (
                      CASE
                        WHEN LR.STATUS = 'C'
                        THEN 20
                        ELSE 365
                      END)"
        ]);
        $select->order("LR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    
    public function getDefaultInterestRate($loan_id){
        $sql = "SELECT INTEREST_RATE FROM HRIS_LOAN_MASTER_SETUP WHERE LOAN_ID = :loan_id";

        $boundedParameter = [];
        $boundedParameter['loan_id'] = $loan_id;
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getLoanDetails(){
        $sql = "SELECT LOAN_ID, IS_RATE_FLEXIBLE, INTEREST_RATE, MIN_AMOUNT, MAX_AMOUNT, REPAYMENT_PERIOD FROM HRIS_LOAN_MASTER_SETUP";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function pullFilebyId($id)
    {
        $boundedParams = [];
        $boundedParams['id'] = $id;
        $sql = "select FILE_PATH from HRIS_EMPLOYEE_LOAN_REQUEST where LOAN_REQUEST_ID = $id limit 1";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();;
    }

    public function getCitValOfLatestMonth($empId){
        $sql = "select ssd.val from hris_salary_sheet_detail ssd 
        left join hris_salary_sheet ss on (ss.sheet_no = ssd.sheet_no)
        where employee_id = {$empId} and pay_id = 18 order by ss.month_id desc limit 1";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current()['VAL'];
    }

    public function getLoanInfo($empId){
        $sql = "SELECT E.EMPLOYEE_CODE, E.FULL_NAME,
         (select BASIC_SALARY from hris_employees_grade_ceiling_master_setup 
         where service_type_id = E.service_type_id 
         and functional_level_id = ifnull(E.acting_FUNCTIONAL_LEVEL_ID, E.functional_level_id)
         and position_id = E.position_id) as BASIC_SALARY,
         (select flat_value from hris_flat_value_detail where employee_id = e.employee_id and flat_id = 16 and fiscal_year_id = (select fiscal_year_id from hris_fiscal_years where current_date between start_date and end_date)) as flat_value,
         (select \"1DAY_SALARY\" from hris_employees_grade_ceiling_master_setup 
         where service_type_id = E.service_type_id and functional_level_id = E.FUNCTIONAL_LEVEL_ID
         and position_id = E.position_id) as one_day FROM HRIS_EMPLOYEES E WHERE E.EMPLOYEE_ID = {$empId}";

        $statement = $this->adapter->query($sql);
        // print_r($sql);die;
        $result = $statement->execute();
        return $result->current();;
    }

    public function getDetailLoanInfo($empId){
        $sql = "Select ssd.val, ps.pay_edesc, ssd.pay_id from hris_salary_sheet_detail ssd
        left join hris_pay_setup ps on (ps.pay_id = ssd.pay_id)
        where ssd.employee_id = {$empId}
        and ssd.sheet_no = (select max(sheet_no) from hris_salary_sheet_detail where employee_id = {$empId}
        and sheet_no in (select sheet_no from hris_salary_sheet where salary_type_id = 1))
        and ssd.pay_id in (106,104,97,84,18,99,27,100,82,103,85,83,96,98)" ;
        
        
        return $this->rawQuery($sql);
    }

    public function validateLoanRequest($empId, $loanId, $loanAmount, $installment, $citVal){
        $parameter = "";
        if($empId){
            $parameter.=$empId.",";
        }else{
            $parameter.="null,";
        }
        if($loanAmount){
            $parameter.=$loanAmount.",null,";
        }else{
            $parameter.="null,null,";
        }
        if($installment){
            $parameter.=$installment.",";
        }else{
            $parameter.="null,";
        }
        if($loanId){
            $parameter.=$loanId.",";
        }else{
            $parameter.="null,";
        }
        if($citVal){
            $parameter.=$citVal;
        }else{
            $parameter.="null";
        }
        $sql="SELECT HRIS_FN_LOAN_CHECK($parameter) AS ERROR FROM DUMMY";
        // print_r($sql);die;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function emiAdd(Model $emiModel){
        // print_r($emiModel);die;
        $this->emiTableGateway->insert($emiModel->getArrayCopyForDB());
    }

    public function fetchLoanDetailView($loanId){
        $sql = "select * from hris_employee_emi_detail WHERE loan_request_id = {$loanId}";
        
        return $this->rawQuery($sql);

        
    }
}
