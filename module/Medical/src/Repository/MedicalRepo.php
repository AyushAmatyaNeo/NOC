<?php

namespace Medical\Repository;

use Application\Helper\EntityHelper;
use Application\Repository\HrisRepository;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Medical\Model\Medical;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class MedicalRepo extends HrisRepository implements RepositoryInterface {

    protected $adapter;
    protected $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(Medical::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [Medical::MEDICAL_ID => $id]);
    }

    public function fetchAll() {
        $sql = "SELECT M.*
                    ,CASE M.BILL_STATUS 
                    WHEN 'RQ' THEN 'REQUESTED'
                    WHEN 'AP' THEN 'APPROVED'
                    WHEN 'PD' THEN 'PAID'
                    WHEN 'C' THEN 'CANCELLED'
                    END AS BILL_STATUS_NAME
                    ,CASE M.CLAIM_OF 
                    WHEN 'S' THEN 'SELF'
                    WHEN 'D' THEN 'DEPENDENT'
                    END AS CLAIM_OF_NAME
                    ,E.EMPLOYEE_CODE
                    ,E.FULL_NAME
                    ,D.DEPARTMENT_NAME
                    ,FUNT.FUNCTIONAL_TYPE_EDESC
                    FROM Hris_Medical M
                    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=M.EMPLOYEE_ID)
                    LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                    LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                    WHERE M.STATUS='E'";
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult;
    }

    public function fetchById($id) {
        $sql = "SELECT M.*
                    ,CASE M.BILL_STATUS 
                    WHEN 'RQ' THEN 'REQUESTED'
                    WHEN 'AP' THEN 'APPROVED'
                    WHEN 'PD' THEN 'PAID'
                    WHEN 'C' THEN 'CANCELLED'
                    END AS BILL_STATUS_NAME
                    ,CASE M.CLAIM_OF 
                    WHEN 'S' THEN 'SELF'
                    WHEN 'D' THEN 'DEPENDENT'
                    END AS CLAIM_OF_NAME
                    ,CASE M.OPERATION_FLAG 
                    WHEN 'Y' THEN 'Yes'
                    WHEN 'N' THEN 'No'
                    END AS OPERATION_FLAG_NAME
                    ,CASE WHEN
                    M.APPROVED_AMT IS  NULL then M.REQUESTED_AMT 
                    else M.APPROVED_AMT
                    END AS APPROVED_AMT_DISPALY
                    ,E.EMPLOYEE_CODE
                    ,E.FULL_NAME
                    ,D.DEPARTMENT_NAME
                    ,FUNT.FUNCTIONAL_TYPE_EDESC
                    ,ER.PERSON_NAME
                    ,R.RELATION_NAME
                    FROM Hris_Medical M
                    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=M.EMPLOYEE_ID)
                    LEFT JOIN HRIS_EMPLOYEE_RELATION ER ON (ER.E_R_Id=M.E_R_Id)
                    LEFT JOIN HRIS_RELATIONS R ON (R.RELATION_ID=ER.RELATION_ID)
                    LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                    LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                    WHERE M.MEDICAL_ID=:id";

        $boundedParameter = [];
        $boundedParameter['id'] = $id;

        return $this->rawQuery($sql, $boundedParameter)[0];
        // $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        // return $rawResult->current();
    }

    public function filterRecord($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId, $fromDate = null, $toDate = null, $status = null) {
        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);
        $fromDateCondition = "";
        $toDateCondition = "";
        $statusCondition = '';
        $rowNums = '';
        if ($fromDate != null) {
            $fromDateCondition = " AND M.TRANSACTION_DT>=TO_DATE(:fromDate,'DD-MM-YYYY') ";
            $boundedParameter['fromDate'] = $fromDate;
        }
        if ($toDate != null) {
            $toDateCondition = " AND M.TRANSACTION_DT<=TO_DATE(:toDate,'DD-MM-YYYY') ";
            $boundedParameter['toDate'] = $toDate;
        }

        if ($status != -1) {
            $statusCondition = " AND M.BILL_STATUS=:status";
            $boundedParameter['status'] = $status;
        }
//        $presentStatusMap = [
//            "LI" => "'L','B','Y'",
//            "EO" => "'E','B'",
//            "MP" => "'X','Y'",
//        ];
//        if ($presentStatus != null) {
//            if (gettype($presentStatus) === 'array') {
//                $q = "";
//                for ($i = 0; $i < sizeof($presentStatus); $i++) {
//                    if ($i == 0) {
//                        $q = $presentStatusMap[$presentStatus[$i]];
//                    } else {
//                        $q .= "," . $presentStatusMap[$presentStatus[$i]];
//                    }
//                }
//                $presentStatusCondition = "AND A.LATE_STATUS IN ({$q})";
//            } else {
//                $presentStatusCondition = "AND A.LATE_STATUS IN ({$presentStatusMap[$presentStatus]})";
//            }
//        }
//          $orderByString=EntityHelper::getOrderBy('A.ATTENDANCE_DT DESC ,A.IN_TIME ASC','A.ATTENDANCE_DT DESC ,A.IN_TIME ASC','E.SENIORITY_LEVEL','P.LEVEL_NO','E.JOIN_DATE','DES.ORDER_NO','E.FULL_NAME');

        $sql = "SELECT M.*
                    ,CASE M.BILL_STATUS 
                    WHEN 'RQ' THEN 'REQUESTED'
                    WHEN 'AP' THEN 'APPROVED'
                    WHEN 'PD' THEN 'PAID'
                    WHEN 'C' THEN 'CANCELLED'
                    END AS BILL_STATUS_NAME
                    ,CASE M.CLAIM_OF 
                    WHEN 'S' THEN 'SELF'
                    WHEN 'D' THEN 'DEPENDENT'
                    END AS CLAIM_OF_NAME
                    ,E.EMPLOYEE_CODE
                    ,E.FULL_NAME
                    ,D.DEPARTMENT_NAME
                    ,FUNT.FUNCTIONAL_TYPE_EDESC
                    FROM Hris_Medical M
                    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=M.EMPLOYEE_ID)
                    LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                    LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                    WHERE M.STATUS='E'
                {$searchCondition['sql']}
                {$fromDateCondition}
                {$toDateCondition}
                {$statusCondition}
                ";
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function fetchEmpMedicalDetail($employeeId) {
        $sql = "SELECT 
ST.SELF_TAKEN,DT.DEPENDENT_TAKEN,DTWO.DEPENDENT_TAKEN_WO
,SM.VALUE-ST.SELF_TAKEN AS SELF_REAMINING
,
case when DTWO.DEPENDENT_TAKEN_WO>DMO.VALUE
then
DM.VALUE-DT.DEPENDENT_TAKEN-(DTWO.DEPENDENT_TAKEN_WO-DT.DEPENDENT_TAKEN-DMO.VALUE)
else
DM.VALUE-DT.DEPENDENT_TAKEN
end
AS DEP_REAMINING
,DM.VALUE+DMO.VALUE-DTWO.DEPENDENT_TAKEN_WO AS DEP_REAMINING_WITH_OPER
FROM 
(SELECT NVL(SUM(CASE 
WHEN APPROVED_AMT IS NOT NULL 
THEN APPROVED_AMT
ELSE
REQUESTED_AMT
END),0) AS SELF_TAKEN
FROM HRIS_MEDICAL
WHERE 
STATUS='E' 
AND BILL_STATUS!='C' 
AND CLAIM_OF='S' 
AND EMPLOYEE_ID={$employeeId}) ST
LEFT JOIN (SELECT NVL(SUM(CASE 
WHEN M.APPROVED_AMT IS NOT NULL 
THEN M.APPROVED_AMT
ELSE
M.REQUESTED_AMT
END),0) AS DEPENDENT_TAKEN
FROM HRIS_MEDICAL M
WHERE 
M.STATUS='E' 
AND M.BILL_STATUS!='C' 
AND M.CLAIM_OF='D'
AND M.OPERATION_FLAG='N'
AND M.EMPLOYEE_ID={$employeeId}) DT ON (1=1)
LEFT JOIN (SELECT NVL(SUM(CASE 
WHEN APPROVED_AMT IS NOT NULL 
THEN APPROVED_AMT
ELSE
REQUESTED_AMT
END),0) AS DEPENDENT_TAKEN_WO
FROM HRIS_MEDICAL
WHERE 
STATUS='E' 
AND BILL_STATUS!='C' 
AND CLAIM_OF='D'
AND EMPLOYEE_ID={$employeeId}) DTWO ON (1=1)    
LEFT JOIN (select *  from HRIS_PREFERENCES WHERE KEY='STAFF_MEDICAL') SM ON (1=1)
LEFT JOIN (select *  from HRIS_PREFERENCES WHERE KEY='STAFF_DEP_MEDICAL') DM ON (1=1)
LEFT JOIN (select *  from HRIS_PREFERENCES WHERE KEY='STAFF_DEP_OPERATION') DMO ON (1=1)";
//echo $sql;
//die();
        $rawResult = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $rawResult->current();
//        return Helper::extractDbData($result);
    }

    public function fetchMedicalBalance($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId) {
        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);
        $fromDateCondition = "";
        $toDateCondition = "";
        $statusCondition = '';


        $sql = "SELECT  
                E.EMPLOYEE_CODE,E.FULL_NAME,
                D.DEPARTMENT_NAME,FUNT.FUNCTIONAL_TYPE_EDESC
                ,SM.VALUE-NVL(ST.SELF_TAKEN,0) AS SELF
                ,case when DTWO.DEPENDENT_TAKEN_WO>DMO.VALUE
then
DM.VALUE-NVL(DT.DEPENDENT_TAKEN,0)-(DTWO.DEPENDENT_TAKEN_WO-NVL(DT.DEPENDENT_TAKEN,0)-DMO.VALUE)
else
DM.VALUE-NVL(DT.DEPENDENT_TAKEN,0)
end  AS DEPENDENT
                ,DM.VALUE+DMO.VALUE-NVL(DTWO.DEPENDENT_TAKEN_WO,0) AS OPERATION
                FROM 
                HRIS_EMPLOYEES E
                LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                LEFT JOIN (SELECT EMPLOYEE_ID,NVL(SUM(CASE 
                WHEN APPROVED_AMT IS NOT NULL 
                THEN APPROVED_AMT
                ELSE
                REQUESTED_AMT
                END),0) AS SELF_TAKEN
                FROM HRIS_MEDICAL
                WHERE 
                STATUS='E' 
                AND BILL_STATUS!='C' 
                AND CLAIM_OF='S' 
                GROUP BY EMPLOYEE_ID
                ) ST ON (E.EMPLOYEE_ID=ST.EMPLOYEE_ID)
                LEFT JOIN (SELECT EMPLOYEE_ID,NVL(SUM(CASE 
                WHEN M.APPROVED_AMT IS NOT NULL 
                THEN M.APPROVED_AMT
                ELSE
                M.REQUESTED_AMT
                END),0) AS DEPENDENT_TAKEN
                FROM HRIS_MEDICAL M
                WHERE 
                M.STATUS='E' 
                AND M.BILL_STATUS!='C' 
                AND M.CLAIM_OF='D'
                AND M.OPERATION_FLAG='N'
                GROUP BY EMPLOYEE_ID
                ) DT ON (E.EMPLOYEE_ID=DT.EMPLOYEE_ID)
                LEFT JOIN (SELECT EMPLOYEE_ID,NVL(SUM(CASE 
                WHEN APPROVED_AMT IS NOT NULL 
                THEN APPROVED_AMT
                ELSE
                REQUESTED_AMT
                END),0) AS DEPENDENT_TAKEN_WO
                FROM HRIS_MEDICAL
                WHERE 
                STATUS='E' 
                AND BILL_STATUS!='C' 
                AND CLAIM_OF='D'
                GROUP BY EMPLOYEE_ID
                ) DTWO ON (E.EMPLOYEE_ID=DTWO.EMPLOYEE_ID)    
                LEFT JOIN (select *  from HRIS_PREFERENCES WHERE KEY='STAFF_MEDICAL') SM ON (1=1)
                LEFT JOIN (select *  from HRIS_PREFERENCES WHERE KEY='STAFF_DEP_MEDICAL') DM ON (1=1)
                LEFT JOIN (select *  from HRIS_PREFERENCES WHERE KEY='STAFF_DEP_OPERATION') DMO ON (1=1)
                WHERE E.STATUS='E'
                {$searchCondition['sql']}
                ";
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function fetchTransactionList($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId, $fromDate = null, $toDate = null) {
        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);
        $fromDateCondition = "";
        $toDateCondition = "";
//        $statusCondition = '';
//        $rowNums = '';
        if ($fromDate != null) {
            $fromDateCondition = " AND M.BANK_TRANSFER_DT>=TO_DATE(:fromDate,'DD-Mon-YYYY') ";
            $boundedParameter['fromDate'] = $fromDate;
        }
        if ($toDate != null) {
            $toDateCondition = " AND M.BANK_TRANSFER_DT<=TO_DATE(:toDate,'DD-Mon-YYYY') ";
            $boundedParameter['toDate'] = $toDate;
        }

        $sql = "SELECT M.*
                    ,CASE M.BILL_STATUS 
                    WHEN 'RQ' THEN 'REQUESTED'
                    WHEN 'AP' THEN 'APPROVED'
                    WHEN 'PD' THEN 'PAID'
                    WHEN 'C' THEN 'CANCELLED'
                    END AS BILL_STATUS_NAME
                    ,CASE M.CLAIM_OF 
                    WHEN 'S' THEN 'SELF'
                    WHEN 'D' THEN 'DEPENDENT'
                    END AS CLAIM_OF_NAME
                    ,E.EMPLOYEE_CODE
                    ,E.FULL_NAME
                    ,D.DEPARTMENT_NAME
                    ,FUNT.FUNCTIONAL_TYPE_EDESC
                    ,E.ID_ACCOUNT_NO
                    FROM Hris_Medical M
                    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=M.EMPLOYEE_ID)
                    LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                    LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                    WHERE M.STATUS='E' AND M.BILL_STATUS='PD'
                    {$searchCondition['sql']}
                    {$fromDateCondition}
                    {$toDateCondition}";
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function fetchTransactionTotal($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId, $fromDate = null, $toDate = null) {
        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);
        $fromDateCondition = "";
        $toDateCondition = "";
        if ($fromDate != null) {
            $fromDateCondition = " AND M.BANK_TRANSFER_DT>=TO_DATE(:fromDate,'DD-Mon-YYYY') ";
            $boundedParameter['fromDate'] = $fromDate;
        }
        if ($toDate != null) {
            $toDateCondition = " AND M.BANK_TRANSFER_DT<=TO_DATE(:toDate,'DD-Mon-YYYY') ";
            $boundedParameter['toDate'] = $toDate;
        }


        $sql = "SELECT 
            SUM(M.Approved_Amt) AS TOTAL_AMT
,TO_CHAR(TO_DATE(TRUNC(SUM(M.Approved_Amt)),'J'),'JSP')
||
case when MOD(SUM(M.Approved_Amt),1)*100 >0
then
' AND '||
 TO_CHAR(TO_DATE(TO_NUMBER(MOD(SUM(M.Approved_Amt),1)*100),'J'),'JSP')
||' PAISA ONLY' 
else
' '
end
AS TOTAL_AMT_IN_WORDS
,TO_CHAR(TRUNC(SYSDATE),'DD-MON-YYYY') AS CUR_DATE
                    FROM Hris_Medical M
                    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=M.EMPLOYEE_ID)
                    WHERE M.STATUS='E' AND M.BILL_STATUS='PD'
                    {$searchCondition['sql']}
                    {$fromDateCondition}
                    {$toDateCondition}";
        return $this->rawQuery($sql, $boundedParameter)[0];
        //return $result->current();
    }
    
    public function fetchVoucherList($companyId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $genderId, $functionalTypeId, $employeeId, $fromDate = null, $toDate = null) {
        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);
        $fromDateCondition = "";
        $toDateCondition = "";
//        $statusCondition = '';
//        $rowNums = '';
        if ($fromDate != null) {
            $fromDateCondition = " AND M.BANK_TRANSFER_DT>=TO_DATE(:fromDate,'DD-Mon-YYYY') ";
            $boundedParameter['fromDate'] = $fromDate;
        }
        if ($toDate != null) {
            $toDateCondition = " AND M.BANK_TRANSFER_DT<=TO_DATE(:toDate,'DD-Mon-YYYY') ";
            $boundedParameter['toDate'] = $toDate;
        }

        $sql = "SELECT ROWNUM,AA.* FROM (SELECT 
                         ' ' AS DR_AMT,
                    SUM(M.APPROVED_AMT) AS CR_AMT
                    ,E.EMPLOYEE_CODE
                    ,E.FULL_NAME
                    ,E.ID_ACCOUNT_NO as ID_ACCOUNT_NO
                    ,'Medical Reimbursement' AS REMARKS
                    FROM Hris_Medical M
                    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=M.EMPLOYEE_ID)
                    LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                    LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                    WHERE M.STATUS='E' AND M.BILL_STATUS='PD'
                    {$searchCondition['sql']}
                    {$fromDateCondition}
                    {$toDateCondition} GROUP BY E.EMPLOYEE_CODE,E.FULL_NAME,E.ID_ACCOUNT_NO) AA";

        return $this->rawQuery($sql, $boundedParameter);
    }

}
