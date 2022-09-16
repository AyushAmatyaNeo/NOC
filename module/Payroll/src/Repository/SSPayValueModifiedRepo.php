<?php
namespace Payroll\Repository;

use Application\Helper\Helper;
use Application\Repository\HrisRepository;
use Payroll\Model\SSPayValueModified;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;

class SSPayValueModifiedRepo extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName === null) {
            $tableName = SSPayValueModified::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    public function filter($monthId, $companyId = null, $groupId = null) {
//        $csv = $this->fetchCSVSSRules();
        $payId = explode(',',$this->fetchCSVSSRules());
        $employeeCondition = "";
        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        if ($companyId != null) {
            $employeeCondition = " AND E.COMPANY_ID = ?";
            $boundedParameter['companyId'] = $companyId;
        }

        if ($groupId != null) {
            $employeeCondition = " AND E.GROUP_ID = ?";
            $boundedParameter['groupId'] = $groupId;
        }

        $startSql = " SELECT E.EMPLOYEE_ID,
                  E.FULL_NAME,
                  C.COMPANY_ID,
                  C.COMPANY_NAME,
                  SSG.GROUP_ID,
                  SSG.GROUP_NAME,
                  PV.*
                FROM HRIS_EMPLOYEES E
                LEFT JOIN HRIS_COMPANY C ON (E.COMPANY_ID=C.COMPANY_ID)
                LEFT JOIN HRIS_SALARY_SHEET_GROUP SSG ON (E.GROUP_ID=SSG.GROUP_ID)
                LEFT JOIN
                  (SELECT *
                  FROM
                    (SELECT MONTH_ID ";

        $endSql = ", EMPLOYEE_ID AS E_ID 
                        FROM HRIS_SS_PAY_VALUE_MODIFIED WHERE MONTH_ID = ?
                        group by month_id, employee_id
                    )
                  ) PV
                ON (E.EMPLOYEE_ID=PV.E_ID)
                WHERE E.STATUS = 'E' {$employeeCondition} ORDER BY C.COMPANY_NAME,SSG.GROUP_NAME,E.FULL_NAME ";

        foreach ($payId as $pivotValue) {
            $startSql .= ", max(case when PAY_ID = {$pivotValue} then VAL end) as H_{$pivotValue} ";
        }

        $sql = $startSql . $endSql;
        return $this->rawQuery($sql, $boundedParameter);
    }

    private function fetchCSVSSRules(): string {
        $sql = "SELECT PAY_ID
                FROM HRIS_PAY_SETUP
                WHERE INCLUDE_IN_SALARY='Y'
                AND PAY_TYPE_FLAG     IN ('A','D')
                AND STATUS ='E'
                ORDER BY PRIORITY_INDEX";
        $statement = $this->adapter->query($sql);
        $rawList = $statement->execute();

        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['PAY_ID']}";
            } else {
                $dbArray .= "{$row['PAY_ID']},";
            }
        }
        return rtrim($dbArray,',');
    }

    public function bulkEdit($data) {
        foreach ($data as $value) {
            $this->createOrUpdate($value['MONTH_ID'], $value['EMPLOYEE_ID'], $value['PAY_ID'], $value['VAL']);
        }
    }

    private function createOrUpdate($m, $e, $p, $v) {
        $boundedParameter = [];
        $boundedParameter['m'] = $m;
        $boundedParameter['e'] = $e;
        $boundedParameter['p'] = $p;
        $boundedParameter['v'] = $v;
        $sql = "
        
                DO 
                BEGIN 
                
                DECLARE  V_MONTH_ID INT  DEFAULT $m ;
                DECLARE  V_EMPLOYEE_ID INT DEFAULT $e ;
                DECLARE  V_PAY_ID INT DEFAULT $p ;
                DECLARE  V_VAL DECIMAL  DEFAULT $v ;
                DECLARE   V_ROW_COUNT NUMBER;
                BEGIN
                  SELECT COUNT(*)
                  INTO V_ROW_COUNT
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE MONTH_ID  =V_MONTH_ID
                  AND EMPLOYEE_ID = V_EMPLOYEE_ID
                  AND PAY_ID      = V_PAY_ID;
                  IF (V_ROW_COUNT >0 ) THEN
                    UPDATE HRIS_SS_PAY_VALUE_MODIFIED
                    SET VAL         =V_VAL
                    WHERE MONTH_ID  =V_MONTH_ID
                    AND EMPLOYEE_ID = V_EMPLOYEE_ID
                    AND PAY_ID      = V_PAY_ID;
                  ELSE
                    INSERT
                    INTO HRIS_SS_PAY_VALUE_MODIFIED
                      (
                        MONTH_ID,
                        EMPLOYEE_ID,
                        PAY_ID,
                        VAL
                      )
                      VALUES
                      (
                        V_MONTH_ID,
                        V_EMPLOYEE_ID,
                        V_PAY_ID,
                        V_VAL
                      );
                  END IF;
                  END;
                END;";
        $this->executeStatement($sql, $boundedParameter);
    }

    public function fetch($q) {
        $iterator = $this->tableGateway->select(function(Select $select) use($q) {
            $select->where([
                SSPayValueModified::MONTH_ID => $q['MONTH_ID'],
                SSPayValueModified::PAY_ID => $q['PAY_ID'],
                SSPayValueModified::EMPLOYEE_ID => $q['EMPLOYEE_ID'],
                SSPayValueModified::SALARY_TYPE_ID => $q['SALARY_TYPE_ID']
            ]);
        });
        $data = iterator_to_array($iterator);
        if (count($data) == 1) {
            return $data[0]['VAL'];
        }
        return null;
    }

    public function setModifiedPayValue($data, $monthId, $salaryTypeId) {
        $payId = $data['payId'];
        $employeeId = $data['employeeId'];
        $sql = "";
        if($data['value'] == null || $data['value'] == ''){
          $sql = "UPDATE HRIS_SS_PAY_VALUE_MODIFIED SET VAL = null
                  WHERE PAY_ID       = $payId
                  AND EMPLOYEE_ID    = $employeeId
                  AND MONTH_ID = $monthId
                  AND SALARY_TYPE_ID = $salaryTypeId";
        }
        else{
          $value = $data['value'];
          $sql = "
            DO 
            BEGIN
                DECLARE  V_PAY_ID INT DEFAULT $payId;
                DECLARE  V_EMPLOYEE_ID INT DEFAULT $employeeId;
                DECLARE  V_PAY_VALUE DECIMAL DEFAULT  $value;
                DECLARE  V_MONTH_ID INT DEFAULT $monthId;
                DECLARE  V_SALARY_TYPE_ID INT DEFAULT  $salaryTypeId;
                DECLARE  V_OLD_FLAT_VALUE DECIMAL;
                BEGIN

                IF (SELECT COUNT(VAL)
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE PAY_ID       = V_PAY_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND MONTH_ID = V_MONTH_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID) > 0
                THEN 
                  SELECT VAL
                  INTO V_OLD_FLAT_VALUE
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE PAY_ID       = V_PAY_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND MONTH_ID = V_MONTH_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID;
                  
                  UPDATE HRIS_SS_PAY_VALUE_MODIFIED
                  SET VAL      = V_PAY_VALUE
                  WHERE PAY_ID       = V_PAY_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND MONTH_ID = V_MONTH_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID;

                ELSE 
                  INSERT
                  INTO HRIS_SS_PAY_VALUE_MODIFIED
                    (
                      PAY_ID,
                      EMPLOYEE_ID,
                      MONTH_ID,
                      SALARY_TYPE_ID,
                      VAL
                    )
                    VALUES
                    (
                      V_PAY_ID,
                      V_EMPLOYEE_ID,
                      V_MONTH_ID,
                      V_SALARY_TYPE_ID,
                      V_PAY_VALUE
                    );
                    END IF;
                    END;
                END;";
        } 
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function getColumns($payHeadId){
        $boundedParameter = [];
        $payHeadIds = "";
        for($i = 0; $i < count($payHeadId); $i++){
            $payHeadIds .= "?," ;
            $boundedParameter['F_'.$payHeadId[$i]] = $payHeadId[$i];
        }
        $payHeadIds = rtrim($payHeadIds,",");
        $sql = "select pay_id, pay_edesc, 'H_'|| pay_id as title from hris_pay_setup where pay_id in ($payHeadIds)
      order by pay_id";
        $statement = $this->adapter->query($sql);
        return $statement->execute($boundedParameter);
    }

    public function modernFilter($monthId, $companyId = null, $groupId = null, $payId, $employeeId, $salaryTypeId, $employeeType) {
//        $csv = $payId;
        $employeeCondition = "";
        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $boundedParameter['salaryTypeId'] = $salaryTypeId;
        if ($companyId != null && $companyId != -1) {
            $employeeCondition .= " AND E.COMPANY_ID = ?";
            $boundedParameter['companyId'] = $companyId;
        }

        if ($groupId != null && $groupId != -1) {
            $employeeCondition .= " AND E.GROUP_ID = ?";
            $boundedParameter['groupId'] = $groupId;
        }

        if ($employeeId != null && $employeeId != -1) {
          for($i = 0; $i < count($employeeId); $i++){
              $employeeIds .= "?," ;
              $boundedParameter['E_'.$employeeId[$i]] = $employeeId[$i];
          }
            $employeeIds = rtrim($employeeIds,",");
            $employeeCondition .= " AND E.EMPLOYEE_ID IN ($employeeIds)";
        }

        $startSql = "SELECT E.EMPLOYEE_ID,
                  E.FULL_NAME,
                  C.COMPANY_ID,
                  C.COMPANY_NAME,
                  SSG.GROUP_ID,
                  SSG.GROUP_NAME,
                  PV.*
                FROM HRIS_EMPLOYEES E
                LEFT JOIN HRIS_COMPANY C ON (E.COMPANY_ID=C.COMPANY_ID)
                LEFT JOIN HRIS_SALARY_SHEET_GROUP SSG ON (E.GROUP_ID=SSG.GROUP_ID)
                LEFT JOIN
                  (SELECT *
                  FROM
                    (SELECT MONTH_ID, EMPLOYEE_ID AS E_ID ";

        $endSql = " FROM HRIS_SS_PAY_VALUE_MODIFIED WHERE MONTH_ID = ?
                    and SALARY_TYPE_ID = ?
                    group by MONTH_ID, EMPLOYEE_ID ) 
                  ) PV
                ON (E.EMPLOYEE_ID=PV.E_ID)
                WHERE E.STATUS   ='E' 
                and E.employee_type = '{$employeeType}' 
				and E.include_in_payroll = 'Y' {$employeeCondition} ";

        foreach ($payId as $pivotValue){
            $startSql .= ", max(case when pay_id = {$pivotValue} then VAL end) as H_{$pivotValue}";
        }

        $sql = $startSql . $endSql;
        return $this->rawQuery($sql, $boundedParameter);
    }

    public function modernFilterEmployeeWise($monthId, $payId, $employeeId, $salaryTypeId) {
        $employeeCondition = "";
        $boundedParameter = [];

        $boundedParameter['monthId'] = $monthId;
        $boundedParameter['salaryTypeId'] = $salaryTypeId;
        $boundedParameter['employeeId'] = $employeeId;

        $condition = "";
        if($payId != null){
          $payId = implode(',', $payId);
          $condition .= " and hps.pay_id in($payId) ";
        }

        $sql = "SELECT
    e.employee_id,
    e.employee_code,
    e.full_name,
    hps.pay_id,
    hps.pay_edesc,
    ( CASE
        WHEN hps.pay_type_flag = 'A' THEN 'Addition'
        WHEN hps.pay_type_flag = 'D' THEN 'Deduction'
        WHEN hps.pay_type_flag = 'V' THEN 'View'
        else '-'
    END ) pay_type,
    pvm.val,
    pvm.remarks
FROM
    hris_pay_setup hps
    LEFT JOIN hris_employees e ON ( 1 = 1 )
    LEFT JOIN hris_ss_pay_value_modified pvm ON ( e.employee_id = pvm.employee_id
                                                  AND pvm.month_id = ?
                                                  AND hps.pay_id = pvm.pay_id
                                                  AND pvm.salary_type_id = ? )
WHERE
    e.employee_id = ? {$condition}
ORDER BY
    hps.pay_edesc";
               
                       return $this->rawQuery($sql, $boundedParameter);
    }

  public function setModifiedPayValueEmployeeWise($data, $monthId, $salaryTypeId, $employeeId) {
        $payId = $data['payId'];
        $sql = "";
        if($data['value'] == null || $data['value'] == ''){
          if($data['valueType'] == 'V'){
            $sql = "UPDATE HRIS_SS_PAY_VALUE_MODIFIED SET VAL = null
                  WHERE PAY_ID       = $payId
                  AND EMPLOYEE_ID    = $employeeId
                  AND MONTH_ID = $monthId
                  AND SALARY_TYPE_ID = $salaryTypeId";
          }
          if($data['valueType'] == 'R'){
            $sql = "UPDATE HRIS_SS_PAY_VALUE_MODIFIED SET REMARKS = null
                  WHERE PAY_ID       = $payId
                  AND EMPLOYEE_ID    = $employeeId
                  AND MONTH_ID = $monthId
                  AND SALARY_TYPE_ID = $salaryTypeId";
          }
        }
        else{
          $value = $data['value'];
          if($data['valueType'] == 'V'){
            $sql = "
              DO 
              BEGIN 
                DECLARE  V_PAY_ID INT DEFAULT $payId;
                DECLARE  V_EMPLOYEE_ID INT DEFAULT  $employeeId;
                DECLARE  V_PAY_VALUE DECIMAL DEFAULT $value;
                DECLARE  V_MONTH_ID INT DEFAULT  $monthId;
                DECLARE  V_SALARY_TYPE_ID INT DEFAULT  $salaryTypeId;
                 DECLARE V_OLD_FLAT_VALUE DECIMAL ;
                BEGIN
                IF (SELECT COUNT(VAL)
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE PAY_ID       = V_PAY_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND MONTH_ID = V_MONTH_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID) > 0
                THEN 
                  SELECT VAL
                  INTO V_OLD_FLAT_VALUE
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE PAY_ID       = V_PAY_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND MONTH_ID = V_MONTH_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID;
                  
                  UPDATE HRIS_SS_PAY_VALUE_MODIFIED
                  SET VAL      = V_PAY_VALUE
                  WHERE PAY_ID       = V_PAY_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND MONTH_ID = V_MONTH_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID;
                  
                ELSE
                  INSERT
                  INTO HRIS_SS_PAY_VALUE_MODIFIED
                    (
                      PAY_ID,
                      EMPLOYEE_ID,
                      MONTH_ID,
                      SALARY_TYPE_ID,
                      VAL,
                      REMARKS
                    )
                    VALUES
                    (
                      V_PAY_ID,
                      V_EMPLOYEE_ID,
                      V_MONTH_ID,
                      V_SALARY_TYPE_ID,
                      V_PAY_VALUE,
                      null
                    );
                    END IF;
                    END;
                END;";
          }
          if($data['valueType'] == 'R'){
            $sql = "
                DO 
                 BEGIN 
                
                DECLARE  V_PAY_ID INT DEFAULT $payId;
                DECLARE  V_EMPLOYEE_ID INT DEFAULT $employeeId;
                DECLARE  V_PAY_VALUE VARCHAR DEFAULT 'R';
                DECLARE  V_MONTH_ID INT DEFAULT $monthId;
                DECLARE  V_SALARY_TYPE_ID INT DEFAULT $salaryTypeId;
                DECLARE  V_OLD_REMARKS VARCHAR;
                BEGIN
                IF (SELECT COUNT(REMARKS)
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE PAY_ID       = V_PAY_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND MONTH_ID = V_MONTH_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID) > 0
                THEN 
                  SELECT REMARKS
                  INTO V_OLD_REMARKS
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE PAY_ID       = V_PAY_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND MONTH_ID = V_MONTH_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID;
                  
                  UPDATE HRIS_SS_PAY_VALUE_MODIFIED
                  SET REMARKS      = V_PAY_VALUE
                  WHERE PAY_ID       = V_PAY_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND MONTH_ID = V_MONTH_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID;
                  
                ELSE
                  INSERT
                  INTO HRIS_SS_PAY_VALUE_MODIFIED
                    (
                      PAY_ID,
                      EMPLOYEE_ID,
                      MONTH_ID,
                      SALARY_TYPE_ID,
                      VAL,
                      REMARKS
                    )
                    VALUES
                    (
                      V_PAY_ID,
                      V_EMPLOYEE_ID,
                      V_MONTH_ID,
                      V_SALARY_TYPE_ID,
                      null,
                      V_PAY_VALUE
                    );
                    END IF;
                    END;
                END;";
          }
        } 
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
}
