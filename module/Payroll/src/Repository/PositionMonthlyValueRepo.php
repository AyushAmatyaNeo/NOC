<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Repository\HrisRepository;
use Payroll\Model\PositionMonthlyValue;
use Zend\Db\Adapter\AdapterInterface;

class PositionMonthlyValueRepo extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = PositionMonthlyValue::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    public function fetchValue($keys) {
        $boundedParameter = [];

        $boundedParameter['MONTH_ID1'] = $keys['MONTH_ID'];
        $boundedParameter['EMPLOYEE_ID1'] = $keys['EMPLOYEE_ID'];
        $boundedParameter['MONTH_ID2'] = $keys['MONTH_ID'];
        $boundedParameter['EMPLOYEE_ID2'] = $keys['EMPLOYEE_ID'];
        $boundedParameter['MTH_ID'] = $keys['MTH_ID'];
        $sql = "SELECT (
                  CASE
                    WHEN ASSIGN_TYPE ='E'
                    THEN MTH_VALUE
                    ELSE ASSIGNED_VALUE
                  END) AS ASSIGNED_VALUE
                FROM
                  (SELECT MVS.ASSIGN_TYPE,
                    MVD.MTH_VALUE,
                    PMV.ASSIGNED_VALUE
                  FROM HRIS_MONTHLY_VALUE_SETUP MVS
                  LEFT JOIN
                    (SELECT *
                    FROM HRIS_MONTHLY_VALUE_DETAIL
                    WHERE MONTH_ID =?
                    AND EMPLOYEE_ID=?
                    ) MVD
                  ON (MVS.MTH_ID=MVD.MTH_ID)
                  LEFT JOIN
                    (SELECT *
                    FROM HRIS_POSITION_MONTHLY_VALUE
                    WHERE MONTH_ID =?
                    AND POSITION_ID=
                      (SELECT POSITION_ID FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID = ?
                      )
                    ) PMV
                  ON (MVS.MTH_ID   =PMV.MTH_ID)
                  WHERE MVS.MTH_ID =?
                  )";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (sizeof($resultList) != 1) {
            return 0;
        }
        return isset($resultList[0]['ASSIGNED_VALUE']) ? $resultList[0]['ASSIGNED_VALUE'] : 0;
    }

    public function fetchById($id) {
        $sql = "SELECT PMV.ASSIGNED_VALUE
                FROM HRIS_POSITION_MONTHLY_VALUE PMV
                JOIN HRIS_EMPLOYEES E
                ON(PMV.POSITION_ID = E.POSITION_ID)
                WHERE PMV.MTH_ID   =:mthId
                AND PMV.MONTH_ID   =:monthId
                AND E.EMPLOYEE_ID  =:employeeId
                ";

        $boundedParameter = [];
        $boundedParameter['mthId'] = $id['MTH_ID'];
        $boundedParameter['monthId'] = $id['MONTH_ID'];
        $boundedParameter['employeeId'] = $id['EMPLOYEE_ID'];
        return $this->rawQuery($sql, $boundedParameter)[0];
        // $statement = $this->adapter->query($sql);
        // $rawResult = $statement->execute();
        // return $rawResult->current();
    }

    public function getPositionMonthlyValue($monthId) {
      $mthId = rtrim($this->fetchMonthlyValueAsDbArray(),',');
        $pivotValues = explode( ',', $mthId);

      
      $boundedParameter = [];
      $boundedParameter['monthId'] = $monthId;

         $startSql = "SELECT FV.POSITION_ID,P.LEVEL_NO
          ";

          $endSql = "FROM HRIS_POSITION_MONTHLY_VALUE FV left join 
                HRIS_POSITIONS P on (FV.POSITION_ID = P.POSITION_ID) WHERE FV.MONTH_ID =?
           group by   FV.POSITION_ID,P.LEVEL_NO order by P.LEVEL_NO

            ";

 foreach ($pivotValues as $value) {
            $startSql .= ", MAX(case when FV.MTH_ID= {$value} then FV.ASSIGNED_VALUE end ) as M_{$value} ";
        }

        $sql = $startSql . $endSql;
       
        //print_r($sql); die;

         return $this->rawQuery($sql, $boundedParameter);

      /*  $sql = "
            SELECT *
            FROM
              ( SELECT MTH_ID,POSITION_ID,ASSIGNED_VALUE FROM HRIS_POSITION_MONTHLY_VALUE WHERE MONTH_ID =:monthId
              ) PIVOT ( MAX(ASSIGNED_VALUE) FOR MTH_ID IN ({$this->fetchMonthlyValueAsDbArray()}) )";
        return $this->rawQuery($sql, $boundedParameter); */
    }

    private function fetchpositionMonthlyValueAsDbArray() {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "SELECT MTH_ID FROM HRIS_MONTHLY_VALUE_SETUP WHERE STATUS ='E' and assign_type = 'P'");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['MTH_ID']}";
            } else {
                $dbArray .= "{$row['MTH_ID']},";
            }
        }
        return $dbArray;
    }

    private function fetchMonthlyValueAsDbArray() {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "SELECT MTH_ID FROM HRIS_MONTHLY_VALUE_SETUP WHERE STATUS ='E'");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['MTH_ID']}";
            } else {
                $dbArray .= "{$row['MTH_ID']},";
            }
        }
        return $dbArray;
    }

    public function setPositionMonthlyValue($monthId, $positionId, $mthId, $assignedValue) {
        $boundedParameter = [];
        $boundedParameter['monthId'] = $monthId;
        $boundedParameter['mthId'] = $mthId;
        $boundedParameter['positionId'] = $positionId;
        $boundedParameter['assignedValue'] = $assignedValue;
        $sql = "
              DO 
              BEGIN
                
                DECLARE  V_MONTH_ID INT DEFAULT $monthId; 
                DECLARE  V_MTH_ID INT DEFAULT $mthId;
                DECLARE  V_POSITION_ID INT DEFAULT $positionId ;
                DECLARE  V_ASSIGNED_VALUE DECIMAL DEFAULT $assignedValue;
                DECLARE   V_OLD_ASSIGNED_VALUE DECIMAL ;
                BEGIN
                IF (SELECT COUNT(ASSIGNED_VALUE)
                  FROM HRIS_POSITION_MONTHLY_VALUE
                  WHERE MTH_ID    = V_MTH_ID
                  AND POSITION_ID = V_POSITION_ID
                  AND MONTH_ID    = V_MONTH_ID) > 0

                  THEN

                  SELECT ASSIGNED_VALUE
                  INTO V_OLD_ASSIGNED_VALUE
                  FROM HRIS_POSITION_MONTHLY_VALUE
                  WHERE MTH_ID    = V_MTH_ID
                  AND POSITION_ID = V_POSITION_ID
                  AND MONTH_ID    = V_MONTH_ID;

                  UPDATE HRIS_POSITION_MONTHLY_VALUE
                  SET ASSIGNED_VALUE = V_ASSIGNED_VALUE
                  WHERE MTH_ID       = V_MTH_ID
                  AND POSITION_ID    = V_POSITION_ID
                  AND MONTH_ID       = V_MONTH_ID;

                  ELSE

                
                  INSERT
                  INTO HRIS_POSITION_MONTHLY_VALUE
                    (
                      MTH_ID,
                      POSITION_ID,
                      MONTH_ID,
                      ASSIGNED_VALUE
                    )
                    VALUES
                    (
                      V_MTH_ID,
                      V_POSITION_ID,
                      V_MONTH_ID,
                      V_ASSIGNED_VALUE
                    );
                    END IF;
                    END;
                END;";
        $statement = $this->adapter->query($sql);
        return $statement->execute($boundedParameter);
    }

}
