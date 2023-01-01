<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Application\Repository\HrisRepository;

class PayrollReportRepo extends HrisRepository implements RepositoryInterface
{

    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {

    }

    public function delete($id)
    {

    }

    public function edit(Model $model, $id)
    {

    }

    public function fetchAll()
    {

    }

    public function fetchById($id)
    {

    }

    public function getVarianceColumns()
    {
        $data['previous'] = $this->varianceColumnsPre();
        $data['current'] = $this->varianceColumnsCur();
        $data['difference'] = $this->varianceColumnsDif();
        $data['addition'] = $this->varianceColumnsAddi();
        return $data;
    }

    public function varianceColumnsPre()
    {
        $sql = "select 'V'||Variance_Id||'_P'  as VARIANCE,VARIANCE_NAME from Hris_Variance where status='E' and Show_Default='Y' AND VARIABLE_TYPE='V'  order by order_no asc ";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function varianceColumnsCur()
    {
        $sql = "select 'V'||Variance_Id||'_C'  as VARIANCE,VARIANCE_NAME from Hris_Variance where status='E' and Show_Default='Y' AND VARIABLE_TYPE='V'  order by order_no asc ";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function varianceColumnsDif()
    {
        $sql = "select 'V'||Variance_Id||'_D'  as VARIANCE,VARIANCE_NAME from Hris_Variance where status='E' and Show_Default='Y' and Show_Difference='Y' AND VARIABLE_TYPE='V' order by order_no asc ";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function varianceColumnsAddi()
    {
        $sql = "SELECT
                    variance_id,
                    'V' || variance_id || '_P' AS PREV,
                    'V' || variance_id || '_C' AS CURR
                
                FROM HRIS_VARIANCE
                
                WHERE
                        status = 'E'
                    AND
                        show_default = 'Y'
                    AND
                        is_sum = 'Y'
                    AND
                        variable_type = 'V'";
        
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        
        return Helper::extractDbData($data);
    }

    public function getVarianceReprot($data)
    {
        $varianceVariable = $this->fetchVarianceVariable();

        $companyId          = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId           = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId       = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId      = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId         = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId      = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId     = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId           = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId   = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId         = isset($data['employeeId']) ? $data['employeeId'] : -1;

        $monthId = $data['monthId'];


        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);

        $sql = "SELECT 
                    E.FULL_NAME, E.EMPLOYEE_CODE,
                    D.DEPARTMENT_NAME,
                    FUNT.FUNCTIONAL_TYPE_EDESC,
                    AD.CUR_ADDRESS, AD.CUR_ACCOUNT, AD.PRE_ADDRESS, AD.PRE_ACCOUNT, AD.ADDRESS_REMARKS, AD.ACCOUNT_REMARKS,

                    VARY.* 

                FROM 

                    -- RETURN COLUMN
                    -- EMPLOYEE_ID  |  VARIANCE_ID  |   C_TOTAL   |   P_TOTAL   |    DIFFERENCE
                    (SELECT 
                        * 
                    FROM 
                        (SELECT 
                            C.EMPLOYEE_ID, C.VARIANCE_ID, C.TOTAL AS C_TOTAL,
                            P.TOTAL AS P_TOTAL,
                            (P.Total - C.TOTAL) AS DIFFERENCE 
                        FROM 

                            -- RETURN EMPLOYEE_ID | VARIANCE_ID | MONTH_ID | SHOW_DIFFERENCE  |  TOTAL
                            (SELECT 
                                SD.EMPLOYEE_ID,
                                VP.Variance_Id,
                                SS.Month_ID,
                                V.Show_Difference,
                                SUM(VAL) AS TOTAL

                            FROM HRIS_VARIANCE V
                            
                            LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID = VP.VARIANCE_ID)
                            LEFT JOIN (SELECT * FROM HRIS_SALARY_SHEET WHERE month_id=:monthId) SS ON (1=1)
                            LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO = SD.SHEET_NO AND SD.Pay_Id = VP.Pay_Id)
                            
                            WHERE V.SHOW_DEFAULT='Y' AND V.STATUS='E' AND V.VARIABLE_TYPE='V' 
                            
                            GROUP BY 
                                SD.EMPLOYEE_ID, 
                                V.VARIANCE_NAME, 
                                VP.Variance_Id, 
                                SS.Month_ID,
                                V.Show_Difference
                            ) C
                                    
                            -- RETURN ROW OF COLUMN
                            -- EMPLOYEE_ID  |  VARIANCE_ID  |  MONTH_ID  |  SHOW_DIFFERENCE  |  TOTAL
                        LEFT JOIN (SELECT 
                                        SD.EMPLOYEE_ID,
                                        VP.Variance_Id,
                                        SS.Month_ID,
                                        V.Show_Difference,
                                        SUM(VAL) AS TOTAL 

                                    FROM HRIS_VARIANCE V

                                    LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID=VP.VARIANCE_ID)
                                    LEFT JOIN  (SELECT 
                                                    * 
                                                FROM HRIS_SALARY_SHEET 
                                                WHERE month_id = (SELECT 
                                                                    MONTH_ID 
                                                                  FROM HRIS_MONTH_CODE 
                                                                  WHERE TO_DATE = (SELECT 
                                                                                        --FROM_DATE-1
                                                                                        FROM_DATE
                                                                                    FROM HRIS_MONTH_CODE 
                                                                                    WHERE MONTH_ID=:monthId)
                                                                  )
                                               ) SS ON (1=1)
                                    
                                    LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND SD.Pay_Id = VP.Pay_Id)
                                    
                                    WHERE V.SHOW_DEFAULT = 'Y' AND V.STATUS='E' AND V.VARIABLE_TYPE= 'V' 
                                    
                                    GROUP BY 
                                        SD.EMPLOYEE_ID,
                                        V.VARIANCE_NAME,
                                        VP.Variance_Id,
                                        SS.Month_ID,
                                        V.Show_Difference
                                    ) P ON (C.EMPLOYEE_ID=P.EMPLOYEE_ID AND C.VARIANCE_ID=P.VARIANCE_ID )
                        )
            
                    --PIVOT ( 
                            --MAX(C_TOTAL) AS C, MAX(P_TOTAL) AS P, MAX(DIFFERENCE) AS D
                            --FOR Variance_Id 
                               -- IN ({$varianceVariable})
                    --)
                    ) VARY
                
                LEFT JOIN (SELECT
                                CUR.EMPLOYEE_ID, 
                                CUR.PERMANENT_ADDRESS AS CUR_ADDRESS,
                                CUR.ACCOUNT_NO AS CUR_ACCOUNT,

                                PREV.PERMANENT_ADDRESS AS PRE_ADDRESS,
                                PREV.ACCOUNT_NO AS PRE_ACCOUNT,

                                CASE 
                                    WHEN CUR.PERMANENT_ADDRESS != PREV.PERMANENT_ADDRESS THEN 'Changed'
                                    ELSE 'Not Changed'
                                END AS ADDRESS_REMARKS,

                                CASE 
                                    WHEN CUR.ACCOUNT_NO != PREV.ACCOUNT_NO THEN 'Changed'
                                    ELSE 'Not Changed'
                                END AS ACCOUNT_REMARKS

                            FROM

                                (SELECT * FROM 
                                    HRIS_SALARY_SHEET SSC
                                 
                                 LEFT JOIN HRIS_SALARY_SHEET_EMP_DETAIL SEDC ON (SEDC.SHEET_NO=SSC.SHEET_NO)
                                 WHERE SSC.month_id=:monthId) CUR
            
                            LEFT JOIN (SELECT 
                                            * 
                                      FROM HRIS_SALARY_SHEET SSP
                                      LEFT JOIN HRIS_SALARY_SHEET_EMP_DETAIL SEDP ON (SEDP.SHEET_NO=SSP.SHEET_NO)
                                      
                                      WHERE SSP.month_id = (SELECT 
                                                                MONTH_ID 
                                                            FROM  HRIS_MONTH_CODE 
                                                            WHERE TO_DATE = (SELECT 
                                                                                FROM_DATE-1 
                                                                             FROM HRIS_MONTH_CODE 
                                                                             WHERE MONTH_ID={$monthId})
                                                            )
                                      ) PREV ON (CUR.EMPLOYEE_ID=PREV.EMPLOYEE_ID)
                          ) AD ON (AD.EMPLOYEE_ID=VARY.EMPLOYEE_ID)
                    
                    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=VARY.EMPLOYEE_ID)
                    LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                    LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                    WHERE 1=1 AND VARY.EMPLOYEE_ID IS NOT NULL {$searchCondition['sql']}";

        return EntityHelper::rawQueryResult($this->adapter, $sql, $boundedParameter);
    }

    public function getGbVariables()
    {
        $sql = "select VARIANCE_ID,VARIANCE_NAME from Hris_Variance 
where status='E' 
AND VARIABLE_TYPE='O'
AND Show_Default='N'";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function getGradeBasicReport($data)
    {
        $varianceVariable = $this->fetchOtVariable();

        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $monthId = $data['monthId'];
//        $fiscalId = $data['fiscalId'];

        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $searchCondition = $this->getSearchConditonBoundedPayroll($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);


        $sql = "SELECT 
            SSED.FULL_NAME,
            E.EMPLOYEE_CODE
            ,E.BIRTH_DATE
            ,SSED.JOIN_DATE
            ,SSED.DEPARTMENT_NAME
            ,SSED.FUNCTIONAL_TYPE_EDESC
            ,GB.*
            ,SSED.SERVICE_TYPE_NAME
            ,SSED.DESIGNATION_TITlE
            ,SSED.POSITION_NAME
            ,SSED.ACCOUNT_NO
            FROM
            (
            SELECT * FROM (SELECT 
            SD.EMPLOYEE_ID
            ,Vp.Variance_Id
            ,SS.Month_ID
            ,SS.SHEET_NO
            ,SUM(VAL) AS TOTAL
            FROM HRIS_VARIANCE V
            LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID=VP.VARIANCE_ID)
            LEFT JOIN (select * from HRIS_SALARY_SHEET) SS ON (1=1)
            JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND SD.Pay_Id=VP.Pay_Id)
            WHERE  V.STATUS='E' AND V.VARIABLE_TYPE='O' 
            and SS.MONTH_ID=:monthId
            GROUP BY SD.EMPLOYEE_ID,V.VARIANCE_NAME,Vp.Variance_Id,SS.Month_ID,SS.SHEET_NO)
            PIVOT ( MAX( TOTAL )
                FOR Variance_Id 
                IN ($varianceVariable)
                ))GB
                LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=GB.EMPLOYEE_ID)
                LEFT JOIN Hris_Salary_Sheet_Emp_Detail SSED ON 
    (SSED.SHEET_NO=GB.SHEET_NO AND SSED.EMPLOYEE_ID=GB.EMPLOYEE_ID AND SSED.MONTH_ID=GB.MONTH_ID)
                LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                WHERE 1=1 
             {$searchCondition['sql']}
             ";

        return EntityHelper::rawQueryResult($this->adapter, $sql, $boundedParameter);
    }

    private function fetchVarianceVariable()
    {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "select  * from Hris_Variance where  SHOW_DEFAULT='Y' AND STATUS='E' AND VARIABLE_TYPE='V'");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['VARIANCE_ID']} AS V{$row['VARIANCE_ID']}";
            } else {
                $dbArray .= "{$row['VARIANCE_ID']} AS V{$row['VARIANCE_ID']},";
            }
        }
        return $dbArray;
    }

    private function fetchOtVariable()
    {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "select  * from Hris_Variance where   STATUS='E' AND VARIABLE_TYPE='O'");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['VARIANCE_ID']} AS V{$row['VARIANCE_ID']}";
            } else {
                $dbArray .= "{$row['VARIANCE_ID']} AS V{$row['VARIANCE_ID']},";
            }
        }
        return $dbArray;
    }

    public function otDefaultColumns()
    {
        $sql = "select 'V'||Variance_Id  as VARIANCE,VARIANCE_NAME from Hris_Variance where status='E'
and Show_Default='Y'  AND VARIABLE_TYPE='O'";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function getOtDefaultColumns()
    {
        $data = $this->otDefaultColumns();
        return $data;
    }

    public function getBasicMonthly($data, $defaultColumnsList)
    {
        // to calculate total start
        $totalString = "0";
        $colCount = 0;
        foreach ($defaultColumnsList as $columns) {
            if ($columns['TYPE'] == 'M') {
                $totalString .= "+CASE WHEN BS.{$columns['DEFAULT_COL']} IS NOT NULL THEN BS.{$columns['DEFAULT_COL']} ELSE 0 END";
            } else {
                $colCount++;
//                $totalString .= " AS {$columns['DEFAULT_COL']},";
                $totalString .= ($colCount == $columns['TOTAL_NO']) ? " AS {$columns['DEFAULT_COL']}," : " AS {$columns['DEFAULT_COL']},0";
            }
        }
        // to calculate total end

        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $fiscalId = $data['fiscalId'];

        $varianceVariable = $this->fetchOtVariableMonthly();
        $monthIdList = $this->fetchMonthIdList($fiscalId);

        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = [];
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);

        $sql = "
             select 
        {$totalString}
            E.FULL_NAME,
            E.EMPLOYEE_CODE,
            E.Id_Account_No AS ACCOUNT_NO,
            E.BIRTH_DATE,
            E.JOIN_DATE,
            D.DEPARTMENT_NAME,
            FUNT.FUNCTIONAL_TYPE_EDESC,
            DES.DESIGNATION_TITLE,
            P.POSITION_NAME,
            ST.SERVICE_TYPE_NAME,
            BS.*
            from  ( select 
              *
              from ( SELECT
            *
        FROM
            ( SELECT
                    sd.employee_id,
                    vp.variance_id,
                    ss.month_id,
                    SUM(val) AS total
                FROM
                    hris_variance v
                    LEFT JOIN hris_variance_payhead vp ON (
                        v.variance_id = vp.variance_id
                    )
                    LEFT JOIN (
                        SELECT
                            *
                        FROM
                            hris_salary_sheet
                    ) ss ON (
                        1 = 1
                    )
                    LEFT JOIN hris_salary_sheet_detail sd ON (
                            ss.sheet_no = sd.sheet_no
                        AND
                            sd.pay_id = vp.pay_id
                    )
                WHERE
                        v.status = 'E'
                    AND
                        v.variable_type = 'O' 
                        and v.Show_Default='Y'
                         GROUP BY
                    sd.employee_id,
                    v.variance_name,
                    vp.variance_id,
                    ss.month_id,
                    ss.sheet_no
                      )
                PIVOT ( MAX ( total )
                    FOR variance_id
                    IN ( {$varianceVariable['VARIABLES']} )
                )
                )
                 PIVOT ( {$varianceVariable['VARIABLES_MAX']}
                    FOR month_id
                    IN ( {$monthIdList} ))
                    ) BS
                     LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=BS.EMPLOYEE_ID)
                     LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                     LEFT JOIN HRIS_DESIGNATIONS DES ON (E.DESIGNATION_ID=DES.DESIGNATION_ID)
                     LEFT JOIN HRIS_POSITIONS P ON (E.POSITION_ID=P.POSITION_ID)
                     LEFT JOIN HRIS_SERVICE_TYPES ST ON (E.SERVICE_TYPE_ID=ST.SERVICE_TYPE_ID)
                     LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                     WHERE 1=1 {$searchCondition['sql']}
                ";

        return $this->rawQuery($sql, $boundedParameter);
    }

    private function fetchOtVariableMonthly()
    {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "select  * from Hris_Variance where   STATUS='E' AND VARIABLE_TYPE='O' and Show_Default='Y'");
        $dbArray['VARIABLES'] = "";
        $dbArray['VARIABLES_MAX'] = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray['VARIABLES'] .= "{$row['VARIANCE_ID']} AS V{$row['VARIANCE_ID']}";
                $dbArray['VARIABLES_MAX'] .= "MAX (V{$row['VARIANCE_ID']}) AS V{$row['VARIANCE_ID']}";
            } else {
                $dbArray['VARIABLES'] .= "{$row['VARIANCE_ID']} AS V{$row['VARIANCE_ID']},";
                $dbArray['VARIABLES_MAX'] .= "MAX (V{$row['VARIANCE_ID']}) AS V{$row['VARIANCE_ID']},";
            }
        }
        return $dbArray;
    }

    private function fetchMonthIdList($fiscalId)
    {
        $boundedParameter = [];
        $boundedParameter['fiscalId'] = $fiscalId;
        $rawList = EntityHelper::rawQueryResult($this->adapter, "select month_id from hris_month_code where Fiscal_Year_Id=:fiscalId", $boundedParameter);
        $monthArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $monthArray .= "{$row['MONTH_ID']} AS M{$row['MONTH_ID']}";
            } else {
                $monthArray .= "{$row['MONTH_ID']} AS M{$row['MONTH_ID']},";
            }
        }
        return $monthArray;
    }

    public function getOtMonthlyDefaultColumns($fiscalId)
    {
        $sql = "SELECT 'M'||MONTH_ID||'_V'||V.VARIANCE_ID AS DEFAULT_COL,
                CASE WHEN YEAR!='TOTAL'
                THEN
                SUBSTR(MC.MONTH_EDESC, 1, 3)||'-'||YEAR
                ELSE
                YEAR||'-'||V.VARIANCE_NAME
                END
                AS MONTH_NAME
                ,TYPE,
                (SELECT count(*) FROM Hris_Variance WHERE
                Variable_Type='O' AND Show_Default='Y') AS TOTAL_NO
                FROM
                (SELECT * FROM Hris_Variance WHERE
                Variable_Type='O' AND Show_Default='Y') V
                LEFT JOIN (select MONTH_ID,
                MONTH_EDESC,
                TO_CHAR(YEAR) AS YEAR,
                'M' AS TYPE
                from hris_month_code where Fiscal_Year_Id=:fiscalId
                union
                select 
                20000 AS MONTH_ID,
                'TOTAL' AS MONTH_EDESC,
                'TOTAL' AS YEAR,
                'T' AS TYPE 
                from dual) MC ON (1=1)";

        $boundedParameter = [];
        $boundedParameter['fiscalId'] = $fiscalId;

        $rawList = $this->rawQuery($sql, $boundedParameter);
        return Helper::extractDbData($rawList);
    }

    public function getGradeBasicSummary($data)
    {
        $varianceVariable = $this->fetchOtVariable();

        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
//        $fiscalId = $data['fiscalId'];
        $monthId = $data['monthId'];
        $extraMonth = $data['extraMonth'];

        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = [];
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);
        $boundedParameter['extraMonth'] = $extraMonth;
        $boundedParameter['monthId'] = $monthId;

        $sql = "SELECT 
            E.FULL_NAME,
            E.EMPLOYEE_CODE,
            E.Id_Account_No AS ACCOUNT_NO
            ,E.BIRTH_DATE
            ,E.JOIN_DATE
            ,D.DEPARTMENT_NAME
            ,FUNT.FUNCTIONAL_TYPE_EDESC
             ,P.POSITION_NAME
            ,ST.SERVICE_TYPE_NAME
            ,DES.DESIGNATION_TITLE
            ,GB.*
            FROM
            (
            SELECT * FROM (SELECT 
            SD.EMPLOYEE_ID
            ,Vp.Variance_Id
            ,SUM(VAL) AS TOTAL
            FROM HRIS_VARIANCE V
            LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID=VP.VARIANCE_ID)
            LEFT JOIN (select * from HRIS_SALARY_SHEET) SS ON (1=1)
            JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND SD.Pay_Id=VP.Pay_Id)
            JOIN HRIS_MONTH_CODE MC ON (SS.MONTH_ID=MC.MONTH_ID) 
            WHERE  V.STATUS='E' AND V.VARIABLE_TYPE='O' 
            AND (SS.MONTH_ID between  :monthId and :extraMonth)
            GROUP BY SD.EMPLOYEE_ID,V.VARIANCE_NAME,Vp.Variance_Id)
            PIVOT ( MAX( TOTAL )
                FOR Variance_Id 
                IN ($varianceVariable)
                ))GB
                LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=GB.EMPLOYEE_ID)
                LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                LEFT JOIN HRIS_POSITIONS P ON (E.POSITION_ID=P.POSITION_ID)
                LEFT JOIN HRIS_SERVICE_TYPES ST ON (E.SERVICE_TYPE_ID=ST.SERVICE_TYPE_ID)
                LEFT JOIN HRIS_DESIGNATIONS DES ON (E.DESIGNATION_ID=DES.DESIGNATION_ID)
                WHERE 1=1 
             {$searchCondition['sql']}
                ";
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getSpecialMonthly($data)
    {
        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $fiscalId = $data['fiscalId'];
        $monthId = $data['monthId'];

        $varianceVariable = $this->fetchOtVariableMonthly();
        $monthIdList = $this->fetchMonthIdList($fiscalId);

        $searchCondition = EntityHelper::getSearchConditonBounded($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = [];
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);
        $boundedParameter['monthId'] = $monthId;

        $sql = "SELECT ROWNUM AS S_NO, HLSED.ACCOUNT_NO, HLSED.FULL_NAME, (
                    SELECT HLSED.SALARY +
                    sum((case when hps.pay_type_flag = 'D' then -1 else 1 end)* 
                    val) FROM HRIS_SALARY_SHEET_DETAIL  hssd
                    join hris_pay_setup hps on hssd.pay_id = hps.pay_id where hssd.employee_id = hlsed.employee_id
                    AND 
                    hssd.pay_id IN(
                    SELECT PAY_ID FROM HRIS_SALARY_SHEET_DETAIL
                    WHERE SHEET_NO IN(
                    SELECT SHEET_NO FROM HRIS_SALARY_SHEET WHERE MONTH_ID = :monthId
                    )) 
                    and hps.include_in_salary = 'Y') CR_AMOUNT FROM HRIS_SALARY_SHEET_EMP_DETAIL HLSED
                     LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=HLSED.EMPLOYEE_ID)
                     LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                     LEFT JOIN HRIS_DESIGNATIONS DES ON (E.DESIGNATION_ID=DES.DESIGNATION_ID)
                     LEFT JOIN HRIS_POSITIONS P ON (E.POSITION_ID=P.POSITION_ID)
                     LEFT JOIN HRIS_SERVICE_TYPES ST ON (E.SERVICE_TYPE_ID=ST.SERVICE_TYPE_ID)
                     LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                     WHERE 1=1 {$searchCondition['sql']}";
        
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getSalaryGroupColumns($type, $default = null)
    {
        $defaultString = " ";
        if ($default != null) {
            $defaultString = "AND Show_Default='{$default}'";
        }
        $sql = "select VARIANCE_ID,VARIANCE_NAME from Hris_Variance 
            where status='E' 
            AND VARIABLE_TYPE='{$type}'
        {$defaultString}";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function getGroupReport($variableType, $data)
    {
        $variable = $this->fetchSalaryGroupVariable($variableType);
        $variableSelector = $this->fetchSalaryGroupVariableSelector($variableType, 'GB');

        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $monthId = $data['monthId'];
        $salaryTypeId = $data['salaryTypeId'];
        $orderBy = $data['orderBy'];
        $sheetNo = isset($data['sheetNo']) ? $data['sheetNo'] : -1;
        $groupId = isset($data['groupId']) ? $data['groupId'] : -1;
//        $fiscalId = $data['fiscalId'];
        $boundedParameter = [];
        $searchCondition = EntityHelper::getSearchConditonBoundedPayroll($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);

        $strSalaryType = " ";
        if ($salaryTypeId != null && $salaryTypeId != -1) {
            $strSalaryType = " WHERE SALARY_TYPE_ID= ?";
            $boundedParameter['salaryTypeId'] = $salaryTypeId;
        }

        $groupIdSql="";
        if($groupId != null && $groupId != -1) {
            $groupIdSql=" and SS.GROUP_ID = ?";
            $boundedParameter['groupId'] = $groupId;
        }

        $boundedParameter['monthId'] = $monthId;
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);

        $sheetNoSql = "";
        if($sheetNo != null && $sheetNo != -1) {
            $sheetNoSql=" and GB.SHEET_NO = ?";
            $boundedParameter['sheetNo'] = $sheetNo;
        }

        $orderbySql = "";
        if ($orderBy) {
            if ($orderBy == 'E') {
                $orderbySql = " ORDER BY E.FULL_NAME";
            } elseif ($orderBy == 'S') {
                $orderbySql = " ORDER BY E.SENIORITY_LEVEL";
            }
        }

        $varId = rtrim($variable, ',');
        $pivotValues = explode(',', $varId);

        $startSql = "
                SELECT 
                row_number() over (order by E.full_name) as Serial,
                E.ID_PAN_NO, E.ID_ACCOUNT_NO, E.ID_PROVIDENT_FUND_NO, E.ID_RETIREMENT_NO,
                hb.bank_name,
                E.FULL_NAME,
                case
            when
                E.tax_base = 'M'
            then
                'Married'
            when
                E.tax_base = 'U'
            then
                'Unmarried'
            Else
                'Empty'
            End as marital_status,
                E.EMPLOYEE_CODE
                ,E.ID_PAN_NO
                ,E.ID_ACCOUNT_NO
                ,BR.BRANCH_NAME
                ,E.BIRTH_DATE
                ,E.JOIN_DATE
                ,D.DEPARTMENT_NAME
                ,FUNT.FUNCTIONAL_TYPE_EDESC
                ,SSED.SERVICE_TYPE_NAME
                ,SSED.DESIGNATION_TITlE
                ,SSED.POSITION_NAME
                ,SSED.ACCOUNT_NO
                ,CASE
                WHEN E.ACTING_FUNCTIONAL_LEVEL_ID is NULL
                THEN HFL.FUNCTIONAL_LEVEL_EDESC
                ELSE CONCAT('ACTING - ', AHFL.FUNCTIONAL_LEVEL_EDESC)
                END as FUNCTIONAL_LEVEL_EDESC
                ,HLO.LOCATION_EDESC
                ,GB.*
                FROM
                (
                    SELECT * FROM (SELECT 
                    SD.EMPLOYEE_ID ";

        $endSql = " ,SS.Month_ID
                    ,SS.SHEET_NO
                    ,SUM(VAL) AS TOTAL
                FROM HRIS_VARIANCE V
                LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID=VP.VARIANCE_ID)
                LEFT JOIN (select * from HRIS_SALARY_SHEET {$strSalaryType}) SS ON (1=1)
                LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND SD.Pay_Id=VP.Pay_Id)
                WHERE  V.STATUS='E' AND V.VARIABLE_TYPE='{$variableType}' {$groupIdSql}
                and SS.MONTH_ID = ?
                GROUP BY SD.EMPLOYEE_ID,SS.Month_ID,SS.SHEET_NO)
                )GB
                JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=GB.EMPLOYEE_ID)
                LEFT JOIN Hris_Salary_Sheet_Emp_Detail SSED ON 
                (SSED.SHEET_NO=GB.SHEET_NO AND SSED.EMPLOYEE_ID=GB.EMPLOYEE_ID AND SSED.MONTH_ID=GB.MONTH_ID)
                LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                LEFT JOIN HRIS_FUNCTIONAL_LEVELS HFL ON (E.FUNCTIONAL_LEVEL_ID=HFL.FUNCTIONAL_LEVEL_ID)
                LEFT JOIN HRIS_FUNCTIONAL_LEVELS AHFL ON (E.ACTING_FUNCTIONAL_LEVEL_ID=AHFL.FUNCTIONAL_LEVEL_ID)
                LEFT JOIN HRIS_LOCATIONS HLO ON (E.LOCATION_ID = HLO.LOCATION_ID)
                LEFT JOIN HRIS_BRANCHES BR ON ( E.BRANCH_ID=BR.BRANCH_ID)
                left join hris_banks hb on (hb.bank_id = E.bank_id)
                WHERE 1=1 
             {$searchCondition['sql']}  {$sheetNoSql}
                 {$orderbySql} ";

        foreach ($pivotValues as $value) {
            $startSql .= ", MAX(case when Vp.Variance_Id = {$value} then VAL end ) as V_{$value} ";
        }
        $sql = $startSql . $endSql;
        // echo('<pre>');print_r($sql);die;

        return $this->rawQuery($sql, $boundedParameter);
    }

    private function fetchSalaryGroupVariable($variableType)
    {

        // $boundedParameter = [];
        // $boundedParameter['variableType'] = $variableType;
        $rawList = EntityHelper::rawQueryResult($this->adapter, "select  * from Hris_Variance where   STATUS='E' AND VARIABLE_TYPE='{$variableType}' order by order_no");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['VARIANCE_ID']}";
            } else {
                $dbArray .= "{$row['VARIANCE_ID']},";
            }
        }
        return $dbArray;
    }

    public function getDefaultColumns($type)
    {
        $sql = "select 'V_'||Variance_Id  as VARIANCE,VARIANCE_NAME from Hris_Variance where status='E'
        and Show_Default='Y'  AND VARIABLE_TYPE='{$type}' order by order_no";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function getGroupDetailReport($data)
    {


        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $monthId = $data['monthId'];
        $salaryTypeId = $data['salaryTypeId'];
//        $fiscalId = $data['fiscalId'];

        $boundedParameter = [];
        $strSalaryType = " ";
        if ($salaryTypeId != null && $salaryTypeId != -1) {
            $strSalaryType = " WHERE SALARY_TYPE_ID=:salaryTypeId";
            $boundedParameter['salaryTypeId'] = $salaryTypeId;
        }

        $groupVariable = $data['groupVariable'];
        $variable = $this->fetchGroupDetailVariable($groupVariable);


        $searchCondition = $this->getSearchConditonBoundedPayroll($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);
        $boundedParameter['monthId'] = $monthId;

        $sql = "SELECT 
        row_number() over (order by E.full_name) as Serial,
        E.ID_PAN_NO, E.ID_ACCOUNT_NO, E.ID_PROVIDENT_FUND_NO, E.ID_RETIREMENT_NO,
        hb.bank_name,
            E.FULL_NAME,
            case
            when
                E.tax_base = 'M'
            then
                'Married'
            when
                E.tax_base = 'U'
            then
                'Unmarried'
            Else
                'Empty'
            End as marital_status,
            E.EMPLOYEE_CODE
            ,E.ID_PAN_NO
            ,E.BIRTH_DATE
            ,E.JOIN_DATE
            ,D.DEPARTMENT_NAME
            ,FUNT.FUNCTIONAL_TYPE_EDESC
            ,CASE
                WHEN E.ACTING_FUNCTIONAL_LEVEL_ID is NULL
                THEN HFL.FUNCTIONAL_LEVEL_EDESC
                ELSE CONCAT('ACTING - ', AHFL.FUNCTIONAL_LEVEL_EDESC)
                END as FUNCTIONAL_LEVEL_EDESC
            ,HLO.LOCATION_EDESC
            ,GB.*
            ,SSED.SERVICE_TYPE_NAME
            ,SSED.DESIGNATION_TITlE
            ,SSED.POSITION_NAME
            ,SSED.ACCOUNT_NO
            FROM
            (
            select * from (
            SELECT 
            SD.EMPLOYEE_ID
            ,vp.pay_id
            ,SS.Month_ID
            ,SS.SHEET_NO
            ,VAL AS TOTAL
            FROM HRIS_VARIANCE V
            LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID=VP.VARIANCE_ID)
            LEFT JOIN (select * from HRIS_SALARY_SHEET {$strSalaryType}) SS ON (1=1)
            LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND SD.Pay_Id=VP.Pay_Id)
            WHERE  V.STATUS='E' 
            and V.VARIANCE_ID={$groupVariable}
            and SS.MONTH_ID=:monthId
            )
            pivot(
            MAX( total )
                FOR pay_id 
                IN ({$variable})
                )
            )GB
                LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=GB.EMPLOYEE_ID)
                LEFT JOIN Hris_Salary_Sheet_Emp_Detail SSED ON 
    (SSED.SHEET_NO=GB.SHEET_NO AND SSED.EMPLOYEE_ID=GB.EMPLOYEE_ID AND SSED.MONTH_ID=GB.MONTH_ID)
                LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                LEFT JOIN HRIS_FUNCTIONAL_LEVELS HFL ON (E.FUNCTIONAL_LEVEL_ID=HFL.FUNCTIONAL_LEVEL_ID)
                LEFT JOIN HRIS_FUNCTIONAL_LEVELS AHFL ON (E.ACTING_FUNCTIONAL_LEVEL_ID=AHFL.FUNCTIONAL_LEVEL_ID)
                LEFT JOIN HRIS_LOCATIONS HLO ON (E.LOCATION_ID = HLO.LOCATION_ID)
                left join hris_banks hb on (hb.bank_id = E.bank_id)
                WHERE 1=1 
                
             {$searchCondition['sql']}
             ";
            //  echo('<pre>');print_r($sql);die;
        return EntityHelper::rawQueryResult($this->adapter, $sql, $boundedParameter);
    }

    public function getVarianceDetailColumns($varianceId)
    {
        $sql = "select 
            'V'||vp.pay_id  as VARIANCE,ps.pay_edesc as VARIANCE_NAME
            from 
            Hris_Variance_Payhead vp
            left join Hris_Pay_Setup ps on (vp.pay_id=ps.pay_id)
            where variance_id=:varianceId";

        $boundedParameter = [];
        $boundedParameter['varianceId'] = $varianceId;
        return $this->rawQuery($sql, $boundedParameter);
        //return Helper::extractDbData($data);
    }

    private function fetchGroupDetailVariable($varianceId)
    {
        $boundedParameter = [];
        $boundedParameter['varianceId'] = $varianceId;
        $sql = "select 
        vp.pay_id  as VARIANCE_ID,ps.pay_edesc as VARIANCE_NAME
        from 
        Hris_Variance_Payhead vp
        left join Hris_Pay_Setup ps on (vp.pay_id=ps.pay_id)
        where variance_id=:varianceId";
        $rawList = EntityHelper::rawQueryResult($this->adapter, $sql, $boundedParameter);
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['VARIANCE_ID']} AS V{$row['VARIANCE_ID']}";
            } else {
                $dbArray .= "{$row['VARIANCE_ID']} AS V{$row['VARIANCE_ID']},";
            }
        }
        return $dbArray;
    }

    public function fetchMonthlySummary($type, $data)
    {

        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $monthId = $data['monthId'];
        $salaryTypeId = $data['salaryTypeId'];

        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $searchCondition = $this->getSearchConditonBoundedPayroll($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);

        $strSalaryType = " ";
        if ($salaryTypeId != null && $salaryTypeId != -1) {
            $strSalaryType = " and ss.Salary_Type_Id=:salaryTypeId";
            $boundedParameter['salaryTypeId'] = $salaryTypeId;
        }

        $sql = "SELECT 
            PS.PAY_ID,PS.PAY_CODE,PS.PAY_EDESC,PS.PAY_TYPE_FLAG
            ,CASE WHEN SUM(SD.VAL) IS NULL THEN 0 ELSE SUM(SD.VAL) END AS TOTAL
            FROM HRIS_PAY_SETUP PS 
            -- LEFT JOIN HRIS_SALARY_SHEET SS ON (SS.MONTH_ID=? {$strSalaryType})
            LEFT JOIN HRIS_SALARY_SHEET SS ON (SS.MONTH_ID=:month_id {$strSalaryType})
            LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND PS.PAY_ID=SD.PAY_ID)
            LEFT JOIN Hris_Salary_Sheet_Emp_Detail SSED ON (SSED.SHEET_NO=SD.SHEET_NO AND SSED.EMPLOYEE_ID=SD.EMPLOYEE_ID)
            WHERE PS.PAY_Type_flag='{$type}'
             {$searchCondition['sql']} 
            GROUP BY PS.PAY_ID,PS.PAY_CODE,PS.PAY_EDESC,PS.PAY_TYPE_FLAG";

        $result = EntityHelper::rawQueryResult($this->adapter, $sql, $boundedParameter);
        return Helper::extractDbData($result);
    }

    public function pulldepartmentWise($data)
    {

        $salarySheetRepo = new SalarySheetDetailRepo($this->adapter);

        $in = $salarySheetRepo->fetchPayIdsAsArray();

        $departmentId = (isset($data['departmentId']) && $data['departmentId'] != -1) ? $data['departmentId'] : null;
        $monthId = $data['monthId'];

        $othersList = array();

        $departmentList = $this->fetchDepartmentList($departmentId);
        $counter = 0;
        foreach ($departmentList as $dep) {
            $tempVal = $this->getMonthlySummaryByDep($monthId, $dep['DEPARTMENT_ID'], $in, $data['salaryTypeId']);
            if (isset($tempVal['PARENT_DEPARTMENT']) && $departmentId && $counter == 0) {
                $tempVal['PARENT_DEPARTMENT'] = null;
                $counter++;
            }
            if ($tempVal) {
                array_push($othersList, $tempVal);
            }
        }
        return $othersList;
    }

    public function fetchDepartmentList($departmentId = null)
    {
        if ($departmentId != null) {
            $sql = "
                        select $departmentId as DEPARTMENT_ID from dual
                            union all
                SELECT CD.DEPARTMENT_ID FROM
                         HRIS_DEPARTMENTS CD
                        START WITH CD.PARENT_DEPARTMENT=:departmentId
                        CONNECT BY CD.PARENT_DEPARTMENT= PRIOR CD.DEPARTMENT_ID
                        ";
            $boundedParameter = [];
            $boundedParameter['departmentId'] = $departmentId;
            return $this->rawQuery($sql, $boundedParameter);
        } else {
            $sql = "select * from hris_departments where status='E' ";
            return $this->rawQuery($sql);
        }
        //return Helper::extractDbData($result);
    }

    public function getMonthlySummaryByDep($monthId, $departmentId, $inVal, $salaryTypeId)
    {
        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $boundedParameter['departmentId'] = $departmentId;
        $strSalaryType = " ";
        if ($salaryTypeId != null && $salaryTypeId != -1) {
            $strSalaryType = " AND SS.SALARY_TYPE_ID=:salaryTypeId";
            $boundedParameter['salaryTypeId'] = $salaryTypeId;
        }

        $sql = "select D.Department_Name,D.Parent_Department,p.* from (SELECT 
            Department_Id
            ,PAY_ID
            ,CASE WHEN SUM(VAL) IS NULL THEN 0 ELSE SUM(VAL) END AS TOTAL
            from
             (SELECT 
            :departmentId as Department_Id
            ,PS.PAY_ID
            ,SD.VAL
            FROM HRIS_PAY_SETUP PS 
            left JOIN HRIS_SALARY_SHEET SS ON (SS.MONTH_ID=:monthId {$strSalaryType})
            LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND PS.PAY_ID=SD.PAY_ID)
            LEFT JOIN Hris_Salary_Sheet_Emp_Detail SSED ON (SSED.SHEET_NO=SD.SHEET_NO AND SSED.EMPLOYEE_ID=SD.EMPLOYEE_ID and Ssed.Department_Id in (
            SELECT CD.DEPARTMENT_ID FROM
                         HRIS_DEPARTMENTS CD
                        START WITH CD.PARENT_DEPARTMENT=:departmentId
                        CONNECT BY CD.PARENT_DEPARTMENT= PRIOR CD.DEPARTMENT_ID
                        union
                        select to_number(:departmentId) from dual
            )) where Department_Id is not null
            )
             GROUP BY Department_Id,PAY_ID
            ) P
            PIVOT (
            MAX(total) FOR PAY_ID IN ({$inVal})
            ) P 
            LEFT JOIN Hris_Departments D ON (D.Department_Id=P.Department_Id)";
        $result = EntityHelper::rawQueryResult($this->adapter, $sql, $boundedParameter);
        return $result->current();
    }

    public function getMonthList()
    {
        $sql = "select * from Hris_Month_Code where 
                Fiscal_Year_Id=(select max(Fiscal_Year_Id) 
                from Hris_Fiscal_Years) order by Fiscal_Year_Month_No";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function getJvReport($data)
    {
        $salaryType = ($data['salaryTypeId'] != null && $data['salaryTypeId'] != -1) ? $data['salaryTypeId'] : '';
        $monthId = $data['monthId'];
        $deptId = $data['departmentId'] != -1 ? $data['departmentId'] : '';
        $reportTypeId = $data['reportTypeId'];
        $sql = '';

        if ($reportTypeId == 2) {
            $sql .= "SELECT jv_name,
            listagg(department_name,',') within group( order by department_name) DEPARTMENT_NAME, 
            SUM(jv_value) JV_VALUE, PAY_TYPE_FLAG FROM( ";
        }

        $sql .= "select 
        Pjv.Jv_Name,
        Sed.Department_Id,
        SED.DEPARTMENT_NAME,
        PJV.Pay_Id,
        (CASE WHEN PJV.PAY_TYPE_FLAG = 'D' THEN 'DEBIT' ELSE 'CREDIT' END) PAY_TYPE_FLAG, 
        SUM(SSD.VAL) JV_VALUE
        from Hris_Salary_Sheet SS
        JOIN Hris_Salary_Sheet_Emp_Detail SED ON (SS.SHEET_NO=SED.SHEET_NO )
        JOIN Hris_Salary_Sheet_Detail SSD ON (SED.EMPLOYEE_ID=SSD.EMPLOYEE_ID AND SS.SHEET_NO=SSD.SHEET_NO)
        JOIN Hris_Payroll_Jv PJV ON (PJV.STATUS='E' AND PJV.FLAG='Y' AND PJV.DEPARTMENT_ID=SED.DEPARTMENT_ID AND SSD.PAY_ID=PJV.PAY_ID)
        WHERE SS.MONTH_ID=$monthId ";

        $sql .= $deptId != '' ? " AND Sed.Department_Id = $deptId " : '';
        $sql .= $salaryType != '' ? " AND SS.SALARY_TYPE_ID = $salaryType " : '';
        $sql .= " GROUP BY Pjv.Jv_Name,Sed.Department_Id,SED.DEPARTMENT_NAME,PJV.Pay_Id,PAY_TYPE_FLAG
        ORDER BY Sed.Department_Id";

        if ($reportTypeId == 2) {
            $sql .= ")
            GROUP BY jv_name,
            pay_id,
            department_name, pay_type_flag";
        }

        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function gettaxYearlyByHeads($heads, $type = 'arr')
    {
        $sql = "select 
 Variance_Id,variance_name, 'V'||Variance_Id as template_name             
from hris_variance 
            where variable_type='Y'
            and v_heads = ?
            and status='E'
            order by order_no asc";
        $boundedParameter = [];
        $boundedParameter['heads'] = $heads;
        $result = $this->rawQuery($sql, $boundedParameter);
        if ($type == 'sin') {
            return $result != null ? $result[0] : '';
        } else {
            return Helper::extractDbData($result);
        }
    }

    private function fetchSalaryTaxYearlyVariable()
    {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "select  * from Hris_Variance where   STATUS='E' AND VARIABLE_TYPE='Y'");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['VARIANCE_ID']}";
            } else {
                $dbArray .= "{$row['VARIANCE_ID']},";
            }
        }
        return $dbArray;
    }


    public function getTaxYearly($data)
    {
        $variable = $this->fetchSalaryTaxYearlyVariable();
        $varId = rtrim($variable, ',');
        $pivotValues = explode(',', $varId);

        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $monthId = $data['monthId'];
        $salaryTypeId = $data['salaryTypeId'];
//        $fiscalId = $data['fiscalId'];

        $boundedParameter = [];
        $strSalaryType = "";
        if ($salaryTypeId != null && $salaryTypeId != -1) {
            $strSalaryType = " WHERE SALARY_TYPE_ID = ?";
            $boundedParameter['salaryTypeId'] = $salaryTypeId;
        }
        $boundedParameter['monthId1'] = $monthId;
        $boundedParameter['monthId2'] = $monthId;
        $searchCondition = $this->getSearchConditonBoundedPayroll($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);

        $startSql = "SELECT 
            E.FULL_NAME,
            E.EMPLOYEE_CODE
            ,E.ID_PAN_NO
            ,E.ID_ACCOUNT_NO
            ,BR.BRANCH_NAME
            ,E.BIRTH_DATE
            ,E.JOIN_DATE
            ,CASE E.MARITAL_STATUS
            WHEN  'M' THEN 'Married'
            WHEN  'U' THEN 'Unmarried'
            END AS MARITAL_STATUS
            ,D.DEPARTMENT_NAME
            ,SSED.FUNCTIONAL_TYPE_EDESC
            ,GB.*
            ,SSED.SERVICE_TYPE_NAME
            ,SSED.DESIGNATION_TITlE
            ,SSED.POSITION_NAME
            ,SSED.ACCOUNT_NO
            ,CASE SSED.MARITAL_STATUS_DESC
            WHEN 'MARRIED' THEN 'Couple'
            WHEN 'UNMARRIED' THEN 'Single' 
            END AS ASSESSMENT_CHOICE
            ,C.COMPANY_NAME
            ,MCD.YEAR||'-'||MCD.MONTH_EDESC AS YEAR_MONTH_NAME
            FROM
            (
            SELECT EMPLOYEE_ID, MONTH_ID, SHEET_NO ";

        $endSql = "FROM (SELECT 
            SD.EMPLOYEE_ID
            ,Vp.Variance_Id
            ,SS.Month_ID
            ,SS.SHEET_NO
            ,SUM(VAL) AS TOTAL
            FROM HRIS_VARIANCE V
            LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID=VP.VARIANCE_ID)
            LEFT JOIN (select * from HRIS_SALARY_SHEET {$strSalaryType}) SS ON (1=1)
            LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND SD.Pay_Id=VP.Pay_Id)
            WHERE  V.STATUS='E' AND V.VARIABLE_TYPE='Y' 
            and SS.MONTH_ID= ?
            GROUP BY SD.EMPLOYEE_ID,V.VARIANCE_NAME,Vp.Variance_Id,SS.Month_ID,SS.SHEET_NO)
            GROUP BY EMPLOYEE_ID, MONTH_ID, SHEET_NO
            )GB
                LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=GB.EMPLOYEE_ID)
                LEFT JOIN Hris_Salary_Sheet_Emp_Detail SSED ON 
    (SSED.SHEET_NO=GB.SHEET_NO AND SSED.EMPLOYEE_ID=GB.EMPLOYEE_ID AND SSED.MONTH_ID=GB.MONTH_ID)
                LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=SSED.DEPARTMENT_ID)
                LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (SSED.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                LEFT JOIN HRIS_BRANCHES BR ON (SSED.BRANCH_ID=BR.BRANCH_ID)
                LEFT JOIN HRIS_COMPANY C ON (SSED.COMPANY_ID=C.COMPANY_ID)
                LEFT JOIN HRIS_MONTH_CODE MCD ON (MCD.MONTH_ID= ?)
                WHERE 1=1 {$searchCondition['sql']} ";
        if(!empty($pivotValues[0])){
            foreach ($pivotValues as $pivotValue){
                $startSql .= ", MAX( Case when variance_id = {$pivotValue} then TOTAL end) as V_{$pivotValue} ";
            }
        }

        $sql = $startSql . $endSql;
        return $this->rawQuery($sql, $boundedParameter);
    }

    private function fetchSalaryGroupVariableSelector($variableType, $prefix)
    {
        $boundedParameter = [];
        $boundedParameter['variableType'] = $variableType;
        $rawList = EntityHelper::rawQueryResult($this->adapter, "select  * from Hris_Variance where   STATUS='E' AND VARIABLE_TYPE=? order by order_no", $boundedParameter);
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            $tempPrefix = $prefix . ".V" . $row['VARIANCE_ID'];
            if ($key == sizeof($rawList)) {
                $dbArray .= "IFNULL({$tempPrefix},0) AS V{$row['VARIANCE_ID']}";
            } else {
                $dbArray .= "IFNULL({$tempPrefix},0) AS V{$row['VARIANCE_ID']},";
            }
        }
        return $dbArray;
    }

    public function getAllSheetNo()
    {
        $sql = "select 
                SHEET_NO,
                MONTH_ID, 
                SALARY_TYPE_ID
                from HRIS_SALARY_SHEET  
                where  status='CR'  order by sheet_no asc  ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        $allSheetNo = [];
        foreach ($result as $allSheet) {
            $monthId = $allSheet['MONTH_ID'];
            $typeId = $allSheet['SALARY_TYPE_ID'];
            (!array_key_exists($monthId, $allSheetNo)) ?
                $allSheetNo[$monthId][$typeId] = $allSheet :
                array_push($allSheetNo[$monthId], $allSheet);
        }

        return $allSheetNo;
    }

    public function getEmployeeWiseGroupReport($variableType, $data)
    {
        $variable = $this->fetchSalaryGroupVariable($variableType);
        $variableSelector = $this->fetchSalaryGroupVariableSelector($variableType, 'GB');

        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $groupId = isset($data['groupId']) ? $data['groupId'] : -1;
        $fiscalId = $data['fiscalId'];
        $boundedParameter = [];

        // $boundedParameter['groupId'] = $groupId;
        // $groupJoinCondition = "";
        // if($groupId == -1){
        //     $groupJoinCondition = "1=1";
        // }else{
        //     foreach ($groupId as $g){
        //         if ($groupJoinCondition == ""){
        //             $groupJoinCondition.="SS.GROUP_ID=".$g;
        //         }else{
        //             $groupJoinCondition.=" OR SS.GROUP_ID=".$g;
        //         }
        //     }
        // }

        $boundedParameter['fiscalId'] = $fiscalId;
        $searchCondition = EntityHelper::getSearchConditonBoundedPayroll($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);

        $varId = rtrim($variable, ',');
        $pivotValues = explode(',', $varId);

        $startSql = " SELECT 
                        E.FULL_NAME, E.EMPLOYEE_CODE, E.ID_PAN_NO, E.ID_ACCOUNT_NO, E.BIRTH_DATE, E.JOIN_DATE,
                        BR.BRANCH_NAME,
                        D.DEPARTMENT_NAME,
                        FUNT.FUNCTIONAL_TYPE_EDESC ,
                        SSED.SERVICE_TYPE_NAME, SSED.DESIGNATION_TITlE, SSED.POSITION_NAME, SSED.ACCOUNT_NO,
                        GB.*,
                        HL.LOCATION_EDESC

                    FROM
                      (SELECT 
                        *
                        FROM
                            (SELECT SD.EMPLOYEE_ID ";

                        $endSql = " , SS.Month_ID, SS.SHEET_NO,
                                      MC.MONTH_NO, MC.MONTH_EDESC, MC.FISCAL_YEAR_ID,
                                      ST.SALARY_TYPE_NAME 
                            FROM HRIS_VARIANCE V
                            
                            LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID=VP.VARIANCE_ID)
                            LEFT JOIN HRIS_SALARY_SHEET SS ON (1=1)
                            LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND SD.Pay_Id  =VP.Pay_Id)
                            LEFT JOIN HRIS_MONTH_CODE MC ON (MC.month_id = SS.month_id)
                            LEFT JOIN HRIS_SALARY_TYPE ST ON (SS.SALARY_TYPE_ID = ST.SALARY_TYPE_ID)
                            
                            WHERE V.STATUS = 'E' AND V.VARIABLE_TYPE   ='{$variableType}'
                            
                            GROUP BY SD.EMPLOYEE_ID,
                                      SS.Month_ID,
                                      SS.SHEET_NO,
                                      MC.MONTH_NO,
                                      MC.MONTH_EDESC,
                                      MC.FISCAL_YEAR_ID,
                                      ST.SALARY_TYPE_NAME
                            ) 
                      )GB

                    JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=GB.EMPLOYEE_ID)
                    LEFT JOIN Hris_Salary_Sheet_Emp_Detail SSED ON (SSED.SHEET_NO = GB.SHEET_NO AND SSED.EMPLOYEE_ID=GB.EMPLOYEE_ID )
                    LEFT JOIN HRIS_DEPARTMENTS D ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
                    LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
                    LEFT JOIN HRIS_BRANCHES BR ON ( E.BRANCH_ID = BR.BRANCH_ID)
                    LEFT JOIN HRIS_LOCATIONS HL ON (HL.LOCATION_ID = E.LOCATION_ID)


                WHERE 1=1 AND GB.FISCAL_YEAR_ID = ?
                
                {$searchCondition['sql']} 
                
                ORDER BY E.FULL_NAME, GB.SHEET_NO ";


        foreach ($pivotValues as $value) {
            $startSql .= ", MAX(case when Vp.Variance_Id = {$value} then VAL end ) as V_{$value} ";
        }

        $sql = $startSql . $endSql;
        // print_r($sql);die;
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getAnnualSheetReport($variableType, $data) {
        $variable = $this->fetchSalaryGroupVariable($variableType);
        $csvMonthId = '-1';
        $csvSalaryType='-1';
        if($data['monthId']){
            $csvMonthId = implode($data['monthId'],',');
        }else{
            $csvMonthId = "select month_id from hris_month_code where fiscal_year_id = {$data['fiscalId']}";
        }
        if($data['salaryTypeId']){
            $csvSalaryType= implode($data['salaryTypeId'],',');
        }else{
			$csvSalaryType = "select salary_type_id from hris_salary_type";
		}
        // print_r($data);die;
        $whereCondition = "";
        if($data['companyId'] != null && $data['companyId'] != -1 ){
            $whereCondition .= " and he.company_id = {$data['companyId']}";
        }

        if($data['branchId'] != null && $data['branchId'] != -1 ){
            $whereCondition .= " and he.branch_id in (".implode($data['branchId'],',').")";
        }

        if($data['departmentId'] != null && $data['departmentId'] != -1 ){
            $whereCondition .= " and he.department_id in (".implode($data['departmentId'],',').")";
        }

        if($data['designationId'] != null && $data['designationId'] != -1 ){
            $whereCondition .= " and he.designation_id in (".implode($data['designationId'],',').")";
        }

        if($data['positionId'] != null && $data['positionId'] != -1 ){
            $whereCondition .= " and he.position_id in (".implode($data['positionId'],',').")";
        }

        if($data['serviceTypeId'] != null && $data['serviceTypeId'] != -1 ){
            $whereCondition .= " and he.SERVICE_TYPE_ID in (".implode($data['serviceTypeId'],',').")";
        }

        if($data['serviceEventTypeId'] != null && $data['serviceEventTypeId'] != -1 ){
            $whereCondition .= " and he.SERVICE_EVENT_TYPE_ID in (".implode($data['serviceEventTypeId'],',').")";
        }

        if($data['employeeTypeId'] != null && $data['employeeTypeId'] != -1 ){
            $whereCondition .= " and he.employee_type in ('".implode($data['employeeTypeId'],"','")."')";
        }

        if($data['genderId'] != null && $data['genderId'] != -1 ){
            $whereCondition .= " and he.gender_id in (".implode($data['genderId'],',').")";
        }

        if($data['functionalTypeId'] != null && $data['functionalTypeId'] != -1 ){
            $whereCondition .= " and he.functional_type_id in (".implode($data['functionalTypeId'],',').")";
        }

        if($data['employeeId'] != null && $data['employeeId'] != -1 ){
            $whereCondition .= " and he.employee_id in (".implode($data['employeeId'],',').")";
        }
        // print_r($whereCondition);die;

        $sql = "SELECT
        he.employee_code,
        he.ID_ACCOUNT_NO,
        c.company_name,
        b.bank_name,
        he.full_name,
        ss.employee_id,
        ss.pay_id,
        ps.pay_edesc,
        SUM(ss.val) amount,
        ps.pay_type_flag
    FROM
        hris_salary_sheet_detail   ss,
        hris_pay_setup             ps,
        hris_employees             he,
        hris_company c,
        hris_banks b
    WHERE
        (c.company_id = he.company_id or he.company_id is null)
        and (b.bank_id = he.bank_id or he.bank_id is null)
        and ss.pay_id = ps.pay_id
        AND ss.sheet_no IN (
            SELECT
                sheet_no
            FROM
                hris_salary_sheet
            WHERE
                month_id IN (
                    {$csvMonthId}
                ) 
                 AND salary_type_id IN (
                    {$csvSalaryType}
                )
        ) 
        AND pay_type_flag IN (
            'A',
            'D'
        )
        AND ss.employee_id = he.employee_id
        {$whereCondition}
    GROUP BY
        he.ID_ACCOUNT_NO,
        b.bank_name,
        he.employee_code,
        c.company_name,
        he.full_name,
        ss.employee_id,
        ss.pay_id,
        ps.pay_edesc,
        ps.pay_type_flag
     ORDER BY he.FULL_NAME
             ";
        return $this->rawQuery($sql);
    }

    public function getTdsReport($data){
        $condition="";
        if($data['employeeType'])
        {
            $csvEmployeeType = implode("','",$data['employeeType']);
            $condition = " and E.Employee_type in ('{$csvEmployeeType}')";
        }
        $sql = "select
                        E.employee_id,
                        E.employee_code,
                        E.id_pan_no,
                        E.full_name,
                        ps.pay_edesc,
                        ssd2.val as taxable_income ,
                        SSD.val as TDS_AMOUNT,
                        ps.pay_code as revenue_code ,
                        E.EMPLOYEE_TYPE ,
                        CASE when E.EMPLOYEE_TYPE='R' then  'Regular'
                             when E.EMPLOYEE_TYPE='C' then  'Contract'
                             when E.EMPLOYEE_TYPE='O' then  'OutSource'
                             when E.EMPLOYEE_TYPE='D' then  'Daily wages'
                              ELSE ''
                              END as employee_type_name
                from hris_salary_sheet_detail SSD 
                left join hris_employees E on (SSD.employee_id = E.employee_id) 
                left join hris_pay_setup ps on (ps.pay_id = SSD.pay_id) 
                left join (select
                        * 
                    from hris_salary_sheet_detail 
                    where pay_id=87) ssd2 on (ssd.employee_id = ssd2.employee_id) 
                where SSD.sheet_no in (select
                        sheet_no 
                    from hris_salary_sheet 
                    where month_id = {$data['monthId']}) 
                and ssd2.sheet_no in (select
                        sheet_no 
                    from hris_salary_sheet 
                    where month_id = {$data['monthId']}) 
                and SSD.pay_id = {$data['payId']} 
                {$condition}

                ";
                return $this->rawQuery($sql);
        
    }
    // public function getPfReport($data)
    // {
    //     $condition="";
    //     if($data['employeeType'])
    //     {
    //         $csvEmployeeType = implode("','",$data['employeeType']);
    //         $condition = $condition. " and E.Employee_type in ('{$csvEmployeeType}')";
    //     }
    //     if($data['employeeId'])
    //     {
    //         $csvEmployeeId = implode("','",$data['employeeId']);
    //         $condition = $condition." and E.Employee_id in ('{$csvEmployeeId}')";
    //     }
    //     $sql=" SELECT 
    //     row_number() over (order by E.full_name) as Serial, D.designation_title, E.full_name, E.ID_PROVIDENT_FUND_NO,
    //     (select ssd.val from hris_salary_sheet_detail ssd
    //     left join hris_salary_sheet hss on (hss.sheet_no = ssd.sheet_no)
    //     where hss.salary_type_id = 1 and hss.month_id = {$data['monthId']} and ssd.employee_id = E.employee_id and ssd.pay_id = 36)
    //     as total_fund_deduction,
    //     (select ssd.val from hris_salary_sheet_detail ssd
    //     left join hris_salary_sheet hss on (hss.sheet_no = ssd.sheet_no)
    //     where hss.salary_type_id = 1 and hss.month_id = {$data['monthId']} and ssd.employee_id = E.employee_id and ssd.pay_id = 34)
    //     as PF_Deduction_from_employee,
    //     (select ssd.val from hris_salary_sheet_detail ssd
    //     left join hris_salary_sheet hss on (hss.sheet_no = ssd.sheet_no)
    //     where hss.salary_type_id = 1 and hss.month_id = {$data['monthId']} and ssd.employee_id = E.employee_id and ssd.pay_id = 34)
    //     as PF_contribution_by_employee
    //     from hris_employees E
    //     left join hris_designations D on (D.designation_id = E.designation_id)
    //     left join hris_functional_levels F on (F.functional_level_id = E.functional_level_id)
    //     where E.employee_id in 
    //     (select distinct employee_id from hris_salary_sheet_detail where
    //     sheet_no in (select sheet_no from hris_salary_sheet where month_id = {$data['monthId']} and salary_type_id = 1)) 
    //     {$condition}";
    //     // print_r($sql);die;

    //     return $this->rawQuery($sql);
        
    // }

    // public function getCitReport($data)
    // {   
    //     $condition="";
    //     if($data['employeeType'])
    //     {
    //         $csvEmployeeType = implode("','",$data['employeeType']);
    //         $condition = $condition. " and E.Employee_type in ('{$csvEmployeeType}')";
    //     }
    //     if($data['employeeId'])
    //     {
    //         $csvEmployeeId = implode("','",$data['employeeId']);
    //         $condition = $condition." and E.Employee_id in ('{$csvEmployeeId}')";
    //     }
    //     $sql=" SELECT 
    //     row_number() over (order by E.full_name) as Serial, P.position_name, E.full_name,
    //     (select ssd.val from hris_salary_sheet_detail ssd
    //     left join hris_salary_sheet hss on (hss.sheet_no = ssd.sheet_no)
    //     where hss.salary_type_id = 1 and hss.month_id = {$data['monthId']} and ssd.employee_id = E.employee_id and ssd.pay_id = 18)
    //     as Cit_deduction, E.ID_RETIREMENT_NO as cit_no, E.id_account_no
    //     from hris_employees E
    //     left join hris_positions P on (P.position_id= E.position_id)
    //     left join hris_functional_levels F on (F.functional_level_id = E.functional_level_id)
    //     where  E.employee_id in 
    //     (select distinct employee_id from hris_salary_sheet_detail where
    //     sheet_no in (select sheet_no from hris_salary_sheet where month_id = {$data['monthId']} and salary_type_id = 1))
    //     {$condition}";

    //     return $this->rawQuery($sql);
    // }

    public function getGradeSankhyaReport($data){
        $condition=" and E.employee_type in ('C', 'R')";
        if($data['employeeType'])
        {
            $csvEmployeeType = implode("','",$data['employeeType']);
            $condition = " and E.Employee_type in ('{$csvEmployeeType}')";
        }
        $sql = "select E.employee_code, E.full_name, F.functional_level_edesc, P.position_name,GS.val as grade_sankhya, TGS.val as technical_grade_sankhya
        ,CASE when E.EMPLOYEE_TYPE='R' then  'Regular'
        when E.EMPLOYEE_TYPE='C' then  'Contract'
        when E.EMPLOYEE_TYPE='O' then  'OutSource'
        when E.EMPLOYEE_TYPE='D' then  'Daily wages'
         ELSE ''
         END as employee_type_name
        from hris_employees E
        left join hris_functional_levels F on (F.functional_level_id = E.functional_level_id)
        left join hris_positions P on (P.position_id = E.position_id)
        left join (select * from hris_salary_sheet_detail where sheet_no in (
        select sheet_no from hris_salary_sheet where month_id = {$data['monthId']} and salary_type_id = 1)
        and pay_id =68) GS on (GS.employee_id = E.employee_id)
        left join (select * from hris_salary_sheet_detail where sheet_no in (
        select sheet_no from hris_salary_sheet where month_id = {$data['monthId']} and salary_type_id = 1)
        and pay_id =79) TGS on (TGS.employee_id = E.employee_id)
        where E.status = 'E'
        {$condition}
        order by F.created_dt
        
                

                ";
                // print_r($sql);die;
                return $this->rawQuery($sql);
        
    }

    public function getFinalReconcilationSheetReport($data)
    {
        $variable = $this->fetchSalaryGroupVariable($variableType);
        $variableSelector = $this->fetchSalaryGroupVariableSelector($variableType, 'GB');

        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $groupId = ($data['groupId']) ? $data['groupId'] : -1;
        $fiscalId = $data['fiscalId'];
        $locationId = isset($data['locationId']) ? $data['locationId'] : -1;
        $boundedParameter = [];
        // print_r($fiscalId);die;

        // $boundedParameter['groupId'] = $groupId;
        // $groupJoinCondition = "";
        // if($groupId == -1){
        //     $groupJoinCondition = "1=1";
        // }else{
        //     foreach ($groupId as $g){
        //         if ($groupJoinCondition == ""){
        //             $groupJoinCondition.="SS.GROUP_ID=".$g;
        //         }else{
        //             $groupJoinCondition.=" OR SS.GROUP_ID=".$g;
        //         }
        //     }
        // }

        // $boundedParameter['fiscalId'] = $fiscalId;
        $searchCondition = EntityHelper::getSearchConditonBoundedPayroll($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, $locationId, $functionalTypeId);
        $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);
        // print_r($searchCondition['sql']);die;
        // $varId = rtrim($variable, ',');
        // $pivotValues = explode(',', $varId);

        $sql = "select AAA.*, ten_per + twenty_per + thirty_per + thirty_six_per
        as tax_need_to_be, $fiscalId as FISCAL_YEAR,
        (ten_per + twenty_per + thirty_per + thirty_six_per) - tax_in_system as tax_diff ,
        P.position_name,
        l.location_edesc,
        case when (E.employee_type = 'C') then 'Contract'
        when (E.employee_type = 'R') then 'Regular'
        when (E.employee_type = 'D') then 'Daily Wages'
        when (E.employee_type = 'O') then 'Outsource'
        end as employee_type 
        from 
        (
        select *,
        Annual_income - annual_cit_epf + extra_amount as annual_taxable_amount,
        
        case when tax_base = 'M' then
            ( case when ( (Annual_income - annual_cit_epf + extra_amount) > 450000 and (Annual_income - annual_cit_epf + extra_amount) <= 550000 ) 
                then ( (Annual_income - annual_cit_epf + extra_amount) - 450000 ) * 0.1 
                else ( 
                    case when ( (Annual_income - annual_cit_epf + extra_amount) > 550000 ) 
                        then 100000 * 0.1 
                        else 0 
                        end )  
            end
            )
        else
        ( case when ( (Annual_income - annual_cit_epf + extra_amount) > 400000 and (Annual_income - annual_cit_epf + extra_amount) <= 500000 ) 
            then ( (Annual_income - annual_cit_epf + extra_amount) - 400000 ) * 0.1 
            else ( 
                case when ( (Annual_income - annual_cit_epf + extra_amount) > 500000 ) 
                    then 100000 * 0.1 
                    else 0 
                end) 
            end) 
        end
        
        as ten_per,
        
        case when tax_base = 'M' then
            ( case when ( (Annual_income - annual_cit_epf + extra_amount) > 550000 and (Annual_income - annual_cit_epf + extra_amount) <= 750000 ) 
                then ( (Annual_income - annual_cit_epf + extra_amount) - 550000 ) * 0.2 
                else ( 
                    case when ( (Annual_income - annual_cit_epf + extra_amount) > 750000 ) 
                        then 200000 * 0.2 
                        else 0 
                        end )  
            end
            )
        else
        ( case when ( (Annual_income - annual_cit_epf + extra_amount) > 500000 and (Annual_income - annual_cit_epf + extra_amount) <= 700000 ) 
            then ( (Annual_income - annual_cit_epf + extra_amount) - 500000 ) * 0.2 
            else ( 
                case when ( (Annual_income - annual_cit_epf + extra_amount) > 700000 ) 
                    then 200000 * 0.2 
                    else 0 
                end) 
            end) 
        end
        
        as twenty_per,
        
        case when tax_base = 'M' then
            ( case when ( (Annual_income - annual_cit_epf + extra_amount) > 750000 and (Annual_income - annual_cit_epf + extra_amount) <= 2000000 ) 
                then ( (Annual_income - annual_cit_epf + extra_amount) - 750000 ) * 0.3 
                else ( 
                    case when ( (Annual_income - annual_cit_epf + extra_amount) > 2000000 ) 
                        then 1250000 * 0.3 
                        else 0 
                        end )  
            end
            )
        else
        ( case when ( (Annual_income - annual_cit_epf + extra_amount) > 700000 and (Annual_income - annual_cit_epf + extra_amount) <= 2000000 ) 
            then ( (Annual_income - annual_cit_epf + extra_amount) - 700000 ) * 0.3 
            else ( 
                case when ( (Annual_income - annual_cit_epf + extra_amount) > 2000000 ) 
                    then 1300000 * 0.3 
                    else 0 
                end) 
            end) 
        end
        
        as thirty_per,
        
        case when tax_base = 'M' then
            ( case when ( (Annual_income - annual_cit_epf + extra_amount) > 2000000 ) then
                ( (Annual_income - annual_cit_epf + extra_amount) - 2000000 ) * 0.36 
                else 0 
            end) 
        else
            ( case when ( (Annual_income - annual_cit_epf + extra_amount) > 2000000 ) then
                ( (Annual_income - annual_cit_epf + extra_amount) - 2000000 ) * 0.36 
                else 0 
            end) 
        end
        
        as thirty_six_per
        from (
        select E.employee_id, E.employee_code, E.full_name,
        ifnull(IS_DIS.flat_value,0) as is_disabled,
        E.tax_base,
        ifnull(AI.val,0) - ifnull(OGT.val,0) as Annual_income,
        ifnull(CIT.val,0) as annual_CIT,
        ifnull(EPF.val,0) as annual_epf_deduction,
        ifnull(CIT.val,0) + ifnull(EPF.val,0) as annual_cit_epf,
        case when ((ifnull(AI.val,0)- ifnull(OGT.val,0))/3 < 300000) then
            case when (ifnull(CIT.val,0) + ifnull(EPF.val,0) > ifnull(AI.val,0)/3) then
            ifnull(CIT.val,0) + ifnull(EPF.val,0) - (ifnull(AI.val,0)- ifnull(OGT.val,0))/3 else
            0
            end
        else
            case when (ifnull(CIT.val,0) + ifnull(EPF.val,0) > 300000) then
            ifnull(CIT.val,0) + ifnull(EPF.val,0) - 300000 else
            0
            end
        end 
        as extra_amount,
        ifnull(tax.val,0) as tax_in_system
        from hris_employees E
        left join 
        (select employee_id, sum(val) as val from hris_salary_sheet_detail where 
        pay_id in (select pay_id from hris_pay_setup where pay_type_flag = 'A' and status='E')
        and sheet_no in (select sheet_no from hris_salary_sheet
        where month_id in (select month_id from hris_month_code where fiscal_year_id = 7))
        group by employee_id) AI on (AI.employee_id = E.employee_id)
        
        left join 
        (
        select employee_id, sum(val) as val from hris_salary_sheet_detail where 
        pay_id in (33)
        and sheet_no in (select sheet_no from hris_salary_sheet
        where month_id in (select month_id from hris_month_code where fiscal_year_id = 7) and salary_type_id = 5)
        group by employee_id) OGT on (OGT.employee_id = E.employee_id)
        
        left join 
        (select employee_id, sum(val) as val from hris_salary_sheet_detail where 
        pay_id in (18)
        and sheet_no in (select sheet_no from hris_salary_sheet
        where month_id in (select month_id from hris_month_code where fiscal_year_id = 7))
        group by employee_id) CIT on (CIT.employee_id = E.employee_id)
        
        left join 
        (select employee_id, sum(val) as val from hris_salary_sheet_detail where 
        pay_id in (98)
        and sheet_no in (select sheet_no from hris_salary_sheet
        where month_id in (select month_id from hris_month_code where fiscal_year_id = 7))
        group by employee_id) tax on (tax.employee_id = E.employee_id)
        
        left join 
        (select employee_id, sum(val) as val from hris_salary_sheet_detail where 
        pay_id in (36)
        and sheet_no in (select sheet_no from hris_salary_sheet
        where month_id in (select month_id from hris_month_code where fiscal_year_id = 7))
        group by employee_id) EPF on (EPF.employee_id = E.employee_id)
        
        left join 
        (select * from hris_flat_value_detail where flat_id = 34) IS_DIS on (IS_DIS.employee_id = E.employee_id)
        where E.status='E'
        order by E.full_name)
        ) AAA
        left join hris_employees E on (E.employee_id = AAA.employee_id)
        left join hris_positions P on (E.position_id = P.position_id)
        left join hris_locations L on (E.location_id = L.location_id)
        where 1=1 
        AND E.status='E' and E.status is not null
        {$searchCondition['sql']}";

        // print_r($sql);die;
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function getEmployeeAdditionBreakDown($fiscalYear, $employeeId){
        $sql = "select SSD.employee_id,E.employee_code, E.full_name, SSD.pay_id, P.pay_edesc, sum(SSD.val) as val, $fiscalYear as fiscal_year from hris_salary_sheet_detail SSD
        left join hris_pay_setup P on (SSD.pay_id = P.pay_id)
        left join hris_employees E on (E.employee_id = SSD.employee_id)
        where 
        SSD.pay_id in (select pay_id from hris_pay_setup where pay_type_flag = 'A' and status='E'
        and pay_id <> 33)
        and SSD.sheet_no in (select sheet_no from hris_salary_sheet
        where month_id in (select month_id from hris_month_code where fiscal_year_id = $fiscalYear))
        and SSD.employee_id = $employeeId
        group by SSD.employee_id, SSD.pay_id, P.pay_edesc,E.employee_code, E.full_name
        
        union
        
        select SSD.employee_id,E.employee_code, E.full_name, SSD.pay_id, P.pay_edesc, sum(SSD.val) as val, $fiscalYear as fiscal_year from hris_salary_sheet_detail SSD
        left join hris_pay_setup P on (SSD.pay_id = P.pay_id)
        left join hris_employees E on (E.employee_id = SSD.employee_id)
        where 
        SSD.pay_id in (33)
        and SSD.sheet_no in (select sheet_no from hris_salary_sheet
        where month_id in (select month_id from hris_month_code where fiscal_year_id = $fiscalYear) and salary_type_id <> 5)
        and SSD.employee_id = $employeeId
        group by SSD.employee_id, SSD.pay_id, P.pay_edesc,E.employee_code, E.full_name";
        $data = $this->rawQuery($sql);
        return Helper::extractDbData($data);
    }

    public function getEmployeeSubDetail($payId, $employeeId, $fiscalId){
        $sql = "select E.full_name, MC.month_edesc, ST.Salary_type_name, P.pay_edesc, SSD.val from hris_salary_sheet_detail SSD 
        left join hris_pay_setup P on (P.pay_id = SSD.pay_id)
        left join hris_employees E on (E.employee_id = SSD.employee_id)
        left join hris_salary_sheet SS on (SS.sheet_no = SSD.sheet_no)
        left join hris_salary_type ST on (ST.salary_type_id = SS.salary_type_id)
        left join hris_month_code MC on (MC.month_id = SS.month_id)
        where SSD.employee_id = $employeeId 
        and SSD.pay_id = $payId
        and SS.month_id in (select month_id from hris_month_code where fiscal_year_id=$fiscalId)
        and SSD.val<>0
        order by MC.month_id        
        ";
        $data = $this->rawQuery($sql);
        return Helper::extractDbData($data);
    }


}
