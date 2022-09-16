<?php

namespace Loan\Repository;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Repository\RepositoryInterface;
use Setup\Model\HrEmployees;
use Application\Repository\HrisRepository;
use Application\Helper\EntityHelper;

class LoanReportRepository extends HrisRepository{

    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function add(\Application\Model\Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(\Application\Model\Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchEmployeeLoanDetails($data){
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $loanId = $data['loanId'];
        $loanRequestStatusId = $data['loanRequestStatusId'];
        $employeeTypeId = $data['employeeTypeId'];
        $loanStatus = $data['loanStatus'];
        $searchCondition = EntityHelper::getSearchConditon($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId);
        $sql = "SELECT DISTINCT
        e.employee_code,
        e.full_name,
        initcap(l.loan_name) AS loan_name,
        lr.requested_amount,
        lr.loan_request_id,
        ((
            SELECT
            IFNULL(SUM(amount), 0)
            FROM
                hris_loan_payment_detail
            WHERE
                    paid_flag = 'Y'
                AND
                    loan_request_id = lr.loan_request_id
        ) + (
            SELECT
            IFNULL(SUM(principle_amount), 0)
            FROM
                hris_loan_cash_payment
            WHERE
            loan_req_id = lr.loan_request_id
        )) AS paid_amount,
        ( 
            SELECT
                ROUND(SUM(amount))
            FROM
                hris_loan_payment_detail
            WHERE
                    paid_flag = 'N'
                AND
                    loan_request_id = lr.loan_request_id
        )  AS balance,
        (select amount from hris_loan_payment_detail where loan_request_id = lr.loan_request_id
        and from_date = to_date(month(current_date))) AS current_installment,
        initcap(TO_CHAR(lr.loan_date,'DD-MON-YYYY') ) AS loan_date_ad,
        bs_date((lr.loan_date) ) AS loan_date_bs,
        initcap(TO_CHAR(lr.requested_date,'DD-MON-YYYY') ) AS requested_date_ad,
        bs_date((lr.requested_date) ) AS requested_date_bs,
        lr.loan_status AS status,
        lr.employee_id AS employee_id
    FROM
        hris_employee_loan_request lr
        LEFT OUTER JOIN hris_loan_master_setup l ON l.loan_id = lr.loan_id
        LEFT OUTER JOIN hris_employees e ON e.employee_id = lr.employee_id
        LEFT OUTER JOIN hris_loan_payment_detail hlpd ON hlpd.loan_request_id = lr.loan_request_id
    WHERE
        1 = 1 {$searchCondition}";
    
        if ($fromDate != null) {
            $sql .= " AND LR.LOAN_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }

        if ($toDate != null) {
            $sql .= "AND LR.LOAN_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }

        if ($loanStatus != 'BOTH') {
            $sql .= " AND LR.LOAN_STATUS = '".$loanStatus."'";
        }

        $sql .= " ORDER BY LR.LOAN_REQUEST_ID DESC";

        $statement = $this->adapter->query($sql); 
        return $statement->execute();
    }
  
    public function fetchLoanVoucher($emp_id, $fromDate, $toDate, $loanId){

        $employeeCondition = $emp_id == '' || $emp_id == null || $emp_id == -1? '' : " AND HELR.EMPLOYEE_ID = $emp_id";
        $loanCondition = $loanId == '' || $loanId == null || $loanId == -1? ' AND HELR.LOAN_ID IN (1,2,3,7) ' : " AND  HELR.LOAN_ID = $loanId" ;
        $loanRequestCondition = $emp_id == '' || $emp_id == null || $emp_id == -1? '' : " AND EMPLOYEE_ID = $emp_id" ;
        $loanRequestCondition2 = $loanId == '' || $loanId == null || $loanId == -1? ' AND LOAN_ID IN (1,2,3,7) ' : " AND loan_id = $loanId" ;
        $employeeCondition2 = $emp_id == '' || $emp_id == null || $emp_id == -1? '' : " AND EMPLOYEE_ID = $emp_id";

        $sql = "SELECT DT, particulars, debit_amount, credit_amount, balance FROM(
        SELECT LAST_DAY(HLPD.FROM_DATE) AS DT, 'Interest Due' as PARTICULARS,
        TRUNC(SUM(HLPD.INTEREST_AMOUNT), 2) AS DEBIT_AMOUNT, 
        0 AS CREDIT_AMOUNT, 0 AS BALANCE
        FROM 
        HRIS_LOAN_PAYMENT_DETAIL HLPD JOIN 
        HRIS_EMPLOYEE_LOAN_REQUEST HELR ON(HELR.LOAN_REQUEST_ID = HLPD.LOAN_REQUEST_ID)
            WHERE hlpd.PAID_FLAG = 'Y' {$loanCondition} AND trunc(HLPD.FROM_DATE, 'month') IN(
        select trunc(add_Months('{$fromDate}', level-1),'month') result
        from DUAL
        connect by level <= MONTHS_BETWEEN('{$toDate}', '{$fromDate}')+1
        ) {$employeeCondition}
        and (helr.loan_status = 'OPEN' or helr.modified_date >= '{$fromDate}' or 
        helr.loan_request_id in 
            (select loan_req_id from hris_loan_cash_payment)
             ) 
        GROUP BY HLPD.FROM_DATE
        
        UNION ALL
        
        SELECT LAST_DAY(HLPD.FROM_DATE) AS DT, 'Interest Paid' as PARTICULARS,
            0 AS DEBIT_AMOUNT,
            TRUNC(SUM(HLPD.INTEREST_AMOUNT), 2) AS CREDIT_AMOUNT, 0 AS BALANCE
        FROM 
        HRIS_LOAN_PAYMENT_DETAIL HLPD JOIN 
        HRIS_EMPLOYEE_LOAN_REQUEST HELR ON(HELR.LOAN_REQUEST_ID = HLPD.LOAN_REQUEST_ID)
            WHERE hlpd.PAID_FLAG = 'Y' {$loanCondition} AND trunc(HLPD.FROM_DATE, 'month') IN(
        select trunc(add_Months('{$fromDate}', level-1),'month') result
        from DUAL
        connect by level <= MONTHS_BETWEEN('{$toDate}', '{$fromDate}')+1
        ) {$employeeCondition}
        and (helr.loan_status = 'OPEN' or helr.modified_date >= '{$fromDate}' or 
        helr.loan_request_id in 
            (select loan_req_id from hris_loan_cash_payment)
             )
        GROUP BY HLPD.FROM_DATE
        
        UNION ALL
        
        SELECT LAST_DAY(HLPD.FROM_DATE) AS DT, 'Amount Paid' as PARTICULARS,
            0 AS DEBIT_AMOUNT,
            TRUNC(SUM(HLPD.AMOUNT), 2) AS CREDIT_AMOUNT, 0 AS BALANCE
        FROM 
        HRIS_LOAN_PAYMENT_DETAIL HLPD JOIN 
        HRIS_EMPLOYEE_LOAN_REQUEST HELR ON(HELR.LOAN_REQUEST_ID = HLPD.LOAN_REQUEST_ID)
        WHERE hlpd.PAID_FLAG = 'Y' {$loanCondition} AND trunc(HLPD.FROM_DATE, 'month') IN(
        select trunc(add_Months('{$fromDate}', level-1),'month') result
        from DUAL
        connect by level <= MONTHS_BETWEEN('{$toDate}', '{$fromDate}')+1
        ) {$employeeCondition}
        and (helr.loan_status = 'OPEN' or helr.modified_date >= '{$fromDate}' 
        or helr.loan_request_id in 
            (select loan_req_id from hris_loan_cash_payment)
              )
        GROUP BY HLPD.FROM_DATE
        ORDER BY DT, DEBIT_AMOUNT DESC, CREDIT_AMOUNT DESC)
                
        UNION ALL

        (SELECT
        LOAN_DATE AS dt,
        'Loan Taken' AS particulars,
        REQUESTED_AMOUNT AS debit_amount,
        0 AS credit_amount,
        0 AS balance
    FROM
        hris_employee_loan_request
    WHERE 1=1
        {$loanRequestCondition2}
        AND
            (LOAN_DATE BETWEEN '{$fromDate}' AND '{$toDate}')    
            and to_char(to_date(loan_date,'dd-mon-yy'),'mm') <> 7
            {$employeeCondition2}
            and loan_request_id not in (select new_loan_req_id from hris_loan_cash_payment))

            UNION ALL

        (SELECT
        PAYMENT_DATE AS dt,
        'Cash Interest Paid' AS particulars,
        INTEREST AS debit_amount,
        0 AS credit_amount,
        0 AS balance
        FROM
        HRIS_LOAN_CASH_PAYMENT
        WHERE
            LOAN_REQ_ID IN (SELECT LOAN_REQUEST_ID FROM hris_employee_loan_request
            WHERE 1=1 {$loanRequestCondition2} {$employeeCondition2})
        AND
            PAYMENT_DATE BETWEEN '{$fromDate}' AND '{$toDate}'    
        )

        UNION ALL

        (SELECT
        PAYMENT_DATE AS dt,
        'Cash Interest Paid' AS particulars,
        0 AS debit_amount,
        INTEREST AS credit_amount,
        0 AS balance
        FROM
        HRIS_LOAN_CASH_PAYMENT
        WHERE
            LOAN_REQ_ID IN (SELECT LOAN_REQUEST_ID FROM hris_employee_loan_request
            WHERE 1=1 {$loanRequestCondition2} {$employeeCondition2})
        AND
            PAYMENT_DATE BETWEEN '{$fromDate}' AND '{$toDate}'    
        )

        UNION ALL

        (SELECT
        PAYMENT_DATE AS dt,
        'Cash Amount Paid' AS particulars,
        0 AS debit_amount,
        PAYMENT_AMOUNT AS credit_amount,
        0 AS balance
        FROM
        HRIS_LOAN_CASH_PAYMENT
        WHERE
            LOAN_REQ_ID IN (SELECT LOAN_REQUEST_ID FROM hris_employee_loan_request
            WHERE 1=1 {$loanRequestCondition2} {$employeeCondition2})
        AND
            PAYMENT_DATE BETWEEN '{$fromDate}' AND '{$toDate}'    
        )

    ORDER BY
        dt,
        debit_amount DESC,
        credit_amount DESC
                ";        
        // echo "<pre>";print_r($sql); die;
        $statement = $this->adapter->query($sql); 
        return $statement->execute();
    }
    
    public function getLoanlist(){
        $sql = "SELECT LOAN_ID, LOAN_NAME FROM HRIS_LOAN_MASTER_SETUP ORDER BY LOAN_ID";
        $statement = $this->adapter->query($sql); 
        return $statement->execute();
    }
      
    public function fetchOpeningBalance($emp_id, $fromDate, $loanId){
        $employeeCondition = $emp_id == '' || $emp_id == null || $emp_id == -1? '' : " AND HELR.EMPLOYEE_ID = $emp_id";
        $loanCondition = $loanId == '' || $loanId == null || $loanId == -1? ' AND HELR.LOAN_ID IN (1,2,3,7) ' : " AND  HELR.LOAN_ID = $loanId" ;
        $loanRequestCondition = $emp_id == '' || $emp_id == null || $emp_id == -1? '' : " AND EMPLOYEE_ID = $emp_id" ;
        $loanRequestCondition2 = $loanId == '' || $loanId == null || $loanId == -1? ' AND LOAN_ID IN (1,2,3,7) ' : " AND loan_id = $loanId" ;
        $employeeCondition2 = $emp_id == '' || $emp_id == null || $emp_id == -1? '' : " AND EMPLOYEE_ID = $emp_id";

        $sql = "SELECT 
    TRUNC(SUM(HLPD.PRINCIPLE_AMOUNT), 2) AS OPENING_BALANCE
    FROM 
    HRIS_LOAN_PAYMENT_DETAIL HLPD JOIN 
    HRIS_EMPLOYEE_LOAN_REQUEST HELR ON(HELR.LOAN_REQUEST_ID = HLPD.LOAN_REQUEST_ID)
        WHERE 
        to_char(to_date(hlpd.from_date,'dd-mon-yy'),'mm') = 7
        and HLPD.FROM_DATE >= trunc(TO_DATE('{$fromDate}'), 'month') 
        AND hlpd.paid_flag = 'Y'
        {$loanCondition}
        {$employeeCondition}
        AND HLPD.LOAN_REQUEST_ID IN(
            SELECT LOAN_REQUEST_ID FROM hris_employee_loan_request
        where 1=1 {$loanRequestCondition})
        and (helr.loan_status = 'OPEN' or helr.modified_date >= '{$fromDate}' or helr.loan_request_id in 
        (select loan_req_id from hris_loan_cash_payment)
        )
        
        ";
        //echo $sql; die;
        $statement = $this->adapter->query($sql); 
        return $statement->execute();
    }

    public function getCashPaymentsList($data) {
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $loanId = $data['loanId'];
        $loanRequestStatusId = $data['loanRequestStatusId'];
        $employeeTypeId = $data['employeeTypeId'];

        $sql = "SELECT
                e.employee_code AS employee_code,
                e.FULL_NAME AS FULL_NAME,
                HLCP.id,
                initcap(l.loan_name) AS LOAN_NAME,
                lr.requested_amount AS TOTAL_AMOUNT,
                HLCP.PAYMENT_AMOUNT AS PAID_AMOUNT,
                INITCAP(TO_CHAR(HLCP.payment_date, 'DD-MON-YYYY'))        AS PAID_DATE_AD,
                BS_DATE(TO_CHAR(HLCP.payment_date, 'DD-MON-YYYY'))        AS PAID_DATE_BS,
                HLCP.REMARKS AS REMARKS
                             
                FROM HRIS_EMPLOYEE_LOAN_REQUEST LR
                JOIN HRIS_LOAN_CASH_PAYMENT HLCP 
                ON LR.LOAN_REQUEST_ID = HLCP.LOAN_REQ_ID
                LEFT OUTER JOIN HRIS_LOAN_MASTER_SETUP L
                ON L.LOAN_ID=LR.LOAN_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=LR.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=LR.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=LR.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON LR.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID = RA.APPROVED_BY
                WHERE L.STATUS   ='E'
                AND E.STATUS     ='E' ";

        if ($loanId != -1) {
            $sql .= " AND LR.LOAN_ID ='" . $loanId . "'";
        }

        if ($fromDate != null) {
            $sql .= " AND LR.LOAN_DATE>=TO_DATE('" . $fromDate . "','DD-MM-YYYY')";
        }

        if ($toDate != null) {
            $sql .= "AND LR.LOAN_DATE<=TO_DATE('" . $toDate . "','DD-MM-YYYY')";
        }

        if ($employeeTypeId != null && $employeeTypeId != -1) {
            $sql .= "AND E.EMPLOYEE_TYPE='" . $employeeTypeId . "' ";
        }

        if ($employeeId != -1) {
            $sql .= "AND E." . HrEmployees::EMPLOYEE_ID . " = $employeeId";
        }

        if ($companyId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::COMPANY_ID . "= $companyId)";
        }
        if ($branchId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::BRANCH_ID . "= $branchId)";
        }
        if ($departmentId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::DEPARTMENT_ID . "= $departmentId)";
        }
        if ($designationId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::DESIGNATION_ID . "= $designationId)";
        }
        if ($positionId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::POSITION_ID . "= $positionId)";
        }
        if ($serviceTypeId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::SERVICE_TYPE_ID . "= $serviceTypeId)";
        }
        if ($serviceEventTypeId != -1) {
            $sql .= " AND E." . HrEmployees::EMPLOYEE_ID . " IN (SELECT " . HrEmployees::EMPLOYEE_ID . " FROM " . HrEmployees::TABLE_NAME . " WHERE " . HrEmployees::SERVICE_EVENT_TYPE_ID . "= $serviceEventTypeId)";
        }

        $sql .= " ORDER BY LR.LOAN_REQUEST_ID DESC";
        //echo $sql; die;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function fetchLoanSummary($emp_id, $fromDate, $toDate, $loanId){
        $employeeCondition = $emp_id == '' || $emp_id == null? '' : " AND HELR.EMPLOYEE_ID = $emp_id";
        $loanCondition = $loanId == '' || $loanId == null || $loanId == -1? ' AND HELR.LOAN_ID IN (1,2,3,7) ' : " AND  HELR.LOAN_ID = $loanId" ;
        $loanRequestCondition = $emp_id == '' || $emp_id == null? '' : " AND EMPLOYEE_ID = $emp_id" ;
        $loanRequestCondition2 = $loanId == '' || $loanId == null || $loanId == -1? ' AND LOAN_ID IN (1,2,3,7) ' : " AND loan_id = $loanId" ;
        $employeeCondition2 = $emp_id == '' || $emp_id == null? '' : " AND EMPLOYEE_ID = $emp_id";

        $sql = "select e.employee, opening_balance, dr_salary, dr_interest, cr_salary, dr_interest as cr_interest,
        (opening_balance + dr_salary - cr_salary) as balance
        from (
        select 
        nvl(sum(opening_balance), 0) as opening_balance,
        sum(dr_salary) as dr_salary,
        sum(dr_interest) as dr_interest,
        sum(cr_salary) as cr_salary
        from (
        SELECT
        0 as opening_balance,
        0 AS dr_salary,
        trunc(SUM(hlpd.interest_amount), 2) AS dr_interest,
        0 AS cr_salary
        FROM
        hris_loan_payment_detail     hlpd
        JOIN hris_employee_loan_request   helr ON ( helr.loan_request_id = hlpd.loan_request_id )
        WHERE
        hlpd.paid_flag = 'Y'
        {$loanCondition}
        AND trunc(hlpd.from_date, 'month') IN (
        SELECT
        trunc(add_months('{$fromDate}', level - 1), 'month') result
        FROM
        dual
        CONNECT BY
        level <= months_between('{$toDate}', '{$fromDate}') 
        )
        {$employeeCondition}
        AND ( helr.loan_status = 'OPEN' or helr.modified_date >= '{$fromDate}'
        OR helr.loan_request_id IN (
        SELECT
        loan_req_id
        FROM
        hris_loan_cash_payment
        ) 
        OR 
                (SELECT TO_CHAR(TO_DATE(FROM_DATE, 'DD-MM-YY'), 'MM') FROM HRIS_LOAN_PAYMENT_DETAIL 
                WHERE LOAN_REQUEST_ID = HELR.LOAN_REQUEST_ID AND SNO = (SELECT MAX(SNO) FROM 
                HRIS_LOAN_PAYMENT_DETAIL WHERE LOAN_REQUEST_ID = HELR.LOAN_REQUEST_ID)
              )  = 7)
        GROUP BY
        hlpd.from_date
        
        union all
        
        SELECT
        0 as opening_balance,
        0 AS dr_salary,
        0 AS dr_interest,
        trunc(SUM(hlpd.amount), 2) AS cr_salary
        FROM
        hris_loan_payment_detail     hlpd
        JOIN hris_employee_loan_request   helr ON ( helr.loan_request_id = hlpd.loan_request_id )
        WHERE
        hlpd.paid_flag = 'Y'
        {$loanCondition}
        AND trunc(hlpd.from_date, 'month') IN (
        SELECT
        trunc(add_months('{$fromDate}', level - 1), 'month') result
        FROM
        dual
        CONNECT BY
        level <= months_between('{$toDate}', '{$fromDate}')
        )
        {$employeeCondition}
        AND ( helr.loan_status = 'OPEN' or helr.modified_date >= '{$fromDate}'
        OR helr.loan_request_id IN (
        SELECT
        loan_req_id
        FROM
        hris_loan_cash_payment
        )
        OR 
                (SELECT TO_CHAR(TO_DATE(FROM_DATE, 'DD-MM-YY'), 'MM') FROM HRIS_LOAN_PAYMENT_DETAIL 
                WHERE LOAN_REQUEST_ID = HELR.LOAN_REQUEST_ID AND SNO = (SELECT MAX(SNO) FROM 
                HRIS_LOAN_PAYMENT_DETAIL WHERE LOAN_REQUEST_ID = HELR.LOAN_REQUEST_ID)
              )  = 7)
        
        GROUP BY
        hlpd.from_date
        
        union all
        
        SELECT
        0 as opening_balance,
        requested_amount AS dr_salary,
        0 AS dr_interest,
        0 AS cr_salary
        
        FROM
        hris_employee_loan_request
        WHERE
        1 = 1
        {$loanRequestCondition2}
        AND ( loan_date BETWEEN '{$fromDate}' AND '{$toDate}' )
        AND to_char(TO_DATE(loan_date, 'dd-mon-yy'), 'mm') <> 7
        {$employeeCondition2}
        AND loan_request_id NOT IN (
        SELECT
        new_loan_req_id
        FROM
        hris_loan_cash_payment
        )
        
        union all
        
        SELECT
            0 as opening_balance,
            0 AS dr_salary,
            0 AS dr_interest,
            payment_amount AS cr_salary
        FROM
            hris_loan_cash_payment
        WHERE
            loan_req_id IN (
                SELECT
                    loan_request_id
                FROM
                    hris_employee_loan_request
                WHERE
                    1 = 1
                    {$loanRequestCondition2}
                    {$employeeCondition2}
            )
            AND payment_date BETWEEN '{$fromDate}' AND '{$toDate}'
        
        union all
        
        SELECT
            trunc(SUM(hlpd.principle_amount), 2) as opening_balance,
            0 AS dr_salary,
            0 AS dr_interest,
            0 AS cr_salary
        FROM
            hris_loan_payment_detail     hlpd
            JOIN hris_employee_loan_request   helr ON ( helr.loan_request_id = hlpd.loan_request_id )
        WHERE
            to_char(to_date(hlpd.from_date, 'dd-mon-yy'), 'mm') = 7 AND
            hlpd.from_date >= trunc(to_date('$fromDate'), 'month')
            AND hlpd.paid_flag = 'Y'
            {$loanCondition}
            {$employeeCondition}
            AND hlpd.loan_request_id IN (
                SELECT
                    loan_request_id
                FROM
                    hris_employee_loan_request
                WHERE
                    1 = 1
                    {$employeeCondition2}
            )
        )) join 
        (select employee_id, employee_code || '-' || full_name as employee 
        from hris_employees where employee_id = {$emp_id}) e
        on 1=1
        ";
        //echo $sql; die;
        $statement = $this->adapter->query($sql); 
        return $statement->execute();
    }

    public function getAllEmployees(){
        $sql = "SELECT EMPLOYEE_ID, EMPLOYEE_CODE, EMPLOYEE_CODE || '-' || FULL_NAME AS FULL_NAME, FULL_NAME AS FULL_NAME_SCIENTIFIC, COMPANY_ID, 
        BRANCH_ID, DEPARTMENT_ID, 
        DESIGNATION_ID, POSITION_ID, SERVICE_TYPE_ID, SERVICE_EVENT_TYPE_ID, GENDER_ID, EMPLOYEE_TYPE, 
        GROUP_ID, FUNCTIONAL_TYPE_ID FROM HRIS_EMPLOYEES";
    
        $statement = $this->adapter->query($sql); 
        return $statement->execute();
    }

    public function getLoanPaymentReport($data){
        $searchCondition = EntityHelper::getSearchConditon($data['companyId'], $data['branchId'], $data['departmentId'], $data['positionId'], $data['designationId'], $data['serviceTypeId'], $data['serviceEventTypeId'], $data['employeeTypeId'], $data['employeeId']);
        $loanCondition = $data['loanId'] == "" ? "" : " and elr.loan_id = ".$data['loanId']." ";

        $sql = "
        select * from
        (select e.employee_code, e.full_name, lms.loan_name, 
        0 as paid_amount, 0 as interest,
        elr.requested_date as DATE_AD,
        BS_DATE(elr.requested_date) as DATE_BS, elr.requested_amount,
        'Loan Taken' as remarks,
        'A' as operation
        from hris_employee_loan_request elr         
        left join hris_loan_master_setup lms on (elr.loan_id = lms.loan_id)
        left join hris_employees e on (elr.employee_id = e.employee_id)
        where 1=1 
        {$searchCondition} {$loanCondition})
        union
        (select e.employee_code, e.full_name, lms.loan_name, 
        TO_DECIMAL(lpd.amount, 10, 2) as paid_amount, TO_DECIMAL(lpd.interest_amount, 10, 2) as interest,
        lpd.to_date as DATE_AD,
        BS_DATE(lpd.to_date) as DATE_BS, elr.requested_amount,
        (case when paid_flag = 'Y' then 'Deducted in payroll' else 'to be paid' end) as remarks,
        'D' as operation
        from hris_loan_payment_detail lpd 
        left join hris_employee_loan_request elr on (lpd.loan_request_id = elr.loan_request_id)
        left join hris_loan_master_setup lms on (elr.loan_id = lms.loan_id)
        left join hris_employees e on (elr.employee_id = e.employee_id)
        where 1=1 and paid_flag = 'Y'
        {$searchCondition} {$loanCondition})
        union
        (select e.employee_code, e.full_name, lms.loan_name, 
        TO_DECIMAL(lcp.principle_amount, 10, 2) as paid_amount, TO_DECIMAL(lcp.interest, 10, 2) as interest,
        lcp.payment_date as DATE_AD, 
        BS_DATE(lcp.payment_date) as DATE_BS, elr.requested_amount,
        'Paid in cash' as remarks,
        'D' as operation
        from hris_loan_cash_payment lcp
        left join hris_employee_loan_request elr on (lcp.loan_req_id = elr.loan_request_id)
        left join hris_loan_master_setup lms on (elr.loan_id = lms.loan_id)
        left join hris_employees e on (elr.employee_id = e.employee_id)
        where 1=1
        {$searchCondition} {$loanCondition})
        order by DATE_AD
        ";

        return $this->rawQuery($sql);
    }
}
