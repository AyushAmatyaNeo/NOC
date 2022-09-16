<?php

namespace KioskApi\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class LoanDetailRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchLoanDetail($employeeId, $loanId) {
        $boundedParams = [];
        $sql = "
            SELECT DISTINCT
            E.EMPLOYEE_CODE,
            E.EMPLOYEE_ID,
            E.FULL_NAME,
            LR.LOAN_ID,
            INITCAP(L.LOAN_NAME) AS LOAN_NAME,
            LR.REQUESTED_AMOUNT as OPENING,
            LR.LOAN_REQUEST_ID,
            NVL(ROUND((
                SELECT SUM(AMOUNT)
                FROM HRIS_LOAN_PAYMENT_DETAIL
                WHERE PAID_FLAG = 'Y'
                AND LOAN_REQUEST_ID = LR.LOAN_REQUEST_ID
                )), 0)AS PAID_AMOUNT,
            ( 
            SELECT
              ROUND(SUM(AMOUNT))
            FROM
              HRIS_LOAN_PAYMENT_DETAIL
            WHERE
                  PAID_FLAG = 'N'
              AND
                  LOAN_REQUEST_ID = LR.LOAN_REQUEST_ID
            )  AS BALANCE,
            TRUNC(HLPD.AMOUNT, 2) AS CURRENT_INSTALLMENT,
            LR.INTEREST_RATE,
            LR.REPAYMENT_MONTHS,
            INITCAP(TO_CHAR(LR.LOAN_DATE,'DD-MON-YYYY') ) AS LOAN_DATE_AD,
            BS_DATE(TO_CHAR(LR.LOAN_DATE,'DD-MON-YYYY') ) AS LOAN_DATE_BS,
            INITCAP(TO_CHAR(LR.REQUESTED_DATE,'DD-MON-YYYY') ) AS REQUESTED_DATE_AD,
            BS_DATE(TO_CHAR(LR.REQUESTED_DATE,'DD-MON-YYYY') ) AS REQUESTED_DATE_BS,
            LR.REASON,
            LR.LOAN_STATUS AS STATUS
            FROM
            HRIS_EMPLOYEE_LOAN_REQUEST LR
            LEFT OUTER JOIN HRIS_LOAN_MASTER_SETUP L ON L.LOAN_ID= LR.LOAN_ID
            LEFT OUTER JOIN HRIS_EMPLOYEES E ON E.EMPLOYEE_ID = LR.EMPLOYEE_ID
            LEFT OUTER JOIN HRIS_LOAN_PAYMENT_DETAIL HLPD ON HLPD.LOAN_REQUEST_ID = LR.LOAN_REQUEST_ID
            WHERE
            1 = 1 
            AND
            LR.LOAN_STATUS = 'OPEN'
            AND L.LOAN_ID = :loanId
            AND E.EMPLOYEE_ID = :employeeId
            ";

        $boundedParams['loanId'] = $loanId;
        $boundedParams['employeeId'] = $employeeId;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParams);
        return Helper::extractDbData($result);
    }

}
