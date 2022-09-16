<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Repository\HrisRepository;
use Payroll\Model\PositionFlatValue;
use Zend\Db\Adapter\AdapterInterface;

class PositionFlatValueRepo extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = PositionFlatValue::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    public function fetchValue($keys) {
        $boundedParameter = [];
         $boundedParameter['MONTH_ID1'] = $keys['MONTH_ID'];
        $boundedParameter['EMPLOYEE_ID1'] = $keys['EMPLOYEE_ID'];
        $boundedParameter['MONTH_ID2'] = $keys['MONTH_ID'];
         $boundedParameter['EMPLOYEE_ID2'] = $keys['EMPLOYEE_ID'];
        $boundedParameter['FLAT_ID'] = $keys['FLAT_ID'];
        $sql = "SELECT (
                  CASE
                    WHEN ASSIGN_TYPE ='E'
                    THEN FLAT_VALUE
                    ELSE CASE WHEN FLAT_VALUE IS NOT NULL
                    THEN FLAT_VALUE
                    ELSE
                    ASSIGNED_VALUE END
                  END) AS ASSIGNED_VALUE
                FROM
                  (SELECT MVS.ASSIGN_TYPE,
                    MVD.FLAT_VALUE,
                    PMV.ASSIGNED_VALUE
                  FROM HRIS_FLAT_VALUE_SETUP MVS
                  LEFT JOIN
                    (SELECT *
                    FROM HRIS_FLAT_VALUE_DETAIL
                    WHERE FISCAL_YEAR_ID = (SELECT FISCAL_YEAR_ID FROM HRIS_MONTH_CODE WHERE MONTH_ID = ?)
                    AND EMPLOYEE_ID=?
                    ) MVD
                  ON (MVS.FLAT_ID=MVD.FLAT_ID)
                  LEFT JOIN
                    (SELECT *
                    FROM HRIS_POSITION_FLAT_VALUE
                    WHERE FISCAL_YEAR_ID =(SELECT FISCAL_YEAR_ID FROM HRIS_MONTH_CODE WHERE MONTH_ID = ?)
                    AND POSITION_ID=
                      (SELECT POSITION_ID FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID = ?
                      )
                    ) PMV
                  ON (MVS.FLAT_ID   =PMV.FLAT_ID)
                  WHERE MVS.FLAT_ID =?
                  )";
        $resultList = $this->rawQuery($sql, $boundedParameter);
        if (sizeof($resultList) != 1) {
            return 0;
        }
        return isset($resultList[0]['ASSIGNED_VALUE']) ? $resultList[0]['ASSIGNED_VALUE'] : 0;
    }

    public function getPositionFlatValue($fiscalYearId) {
        $flatId = rtrim($this->fetchPositionFlatValueAsDbArray(),',');
        $pivotValues = explode( ',', $flatId);

        $startSql = "
            SELECT FV.POSITION_ID,P.LEVEL_NO
             ";

        $endSql = "  FROM HRIS_POSITION_FLAT_VALUE FV left join 
                HRIS_POSITIONS P on (FV.POSITION_ID = P.POSITION_ID)
                WHERE FV.FISCAL_YEAR_ID = {$fiscalYearId}
              group by  FV.POSITION_ID, P.LEVEL_NO order by P.LEVEL_NO";

        foreach ($pivotValues as $value) {
            $startSql .= ", MAX(case when FV.FLAT_ID = {$value} then FV.ASSIGNED_VALUE end ) as F_{$value} ";
        }

        $sql = $startSql . $endSql;

        return $this->rawQuery($sql);
    }

    private function fetchPositionFlatValueAsDbArray() {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "SELECT FLAT_ID FROM HRIS_FLAT_VALUE_SETUP WHERE STATUS ='E' and assign_type = 'P'");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['FLAT_ID']}";
            } else {
                $dbArray .= "{$row['FLAT_ID']},";
            }
        }
        return $dbArray;
    }

    private function fetchFlatValueAsDbArray() {
        $rawList = EntityHelper::rawQueryResult($this->adapter, "SELECT FLAT_ID FROM HRIS_FLAT_VALUE_SETUP WHERE STATUS ='E'");
        $dbArray = "";
        foreach ($rawList as $key => $row) {
            if ($key == sizeof($rawList)) {
                $dbArray .= "{$row['FLAT_ID']}";
            } else {
                $dbArray .= "{$row['FLAT_ID']},";
            }
        }
        return $dbArray;
    }

    public function setPositionFlatValue($fiscalYearId, $positionId, $flatId, $assignedValue) {
        $boundedParameter = [];
        $boundedParameter['flatId'] = $flatId;
        $boundedParameter['positionId'] = $positionId;
        $boundedParameter['assignedValue'] = $assignedValue;
        $boundedParameter['fiscalYearId'] = $fiscalYearId;
        $sql = "
                DO 
                BEGIN
                
                DECLARE  V_FISCAL_YEAR_ID INT  DEFAULT $fiscalYearId;
                DECLARE  V_FLAT_ID INT DEFAULT   $flatId;
                DECLARE  V_POSITION_ID  INT DEFAULT $positionId;
                DECLARE  V_ASSIGNED_VALUE DECIMAL DEFAULT $assignedValue;
                DECLARE  V_OLD_ASSIGNED_VALUE DECIMAL ;


                BEGIN

                IF (SELECT COUNT(ASSIGNED_VALUE)
                  FROM HRIS_POSITION_FLAT_VALUE
                  WHERE FLAT_ID    = V_FLAT_ID
                  AND POSITION_ID = V_POSITION_ID
                  AND FISCAL_YEAR_ID    = V_FISCAL_YEAR_ID) > 0
                THEN

                  SELECT ASSIGNED_VALUE
                  INTO V_OLD_ASSIGNED_VALUE
                  FROM HRIS_POSITION_FLAT_VALUE
                  WHERE FLAT_ID    = V_FLAT_ID
                  AND POSITION_ID = V_POSITION_ID
                  AND FISCAL_YEAR_ID    = V_FISCAL_YEAR_ID;
                  UPDATE HRIS_POSITION_FLAT_VALUE
                  SET ASSIGNED_VALUE = V_ASSIGNED_VALUE
                  WHERE FLAT_ID       = V_FLAT_ID
                  AND POSITION_ID    = V_POSITION_ID
                  AND FISCAL_YEAR_ID       = V_FISCAL_YEAR_ID;


                    ELSE
                  INSERT
                  INTO HRIS_POSITION_FLAT_VALUE
                    (
                      FLAT_ID,
                      POSITION_ID,
                      FISCAL_YEAR_ID,
                      ASSIGNED_VALUE
                    )
                    VALUES
                    (
                      V_FLAT_ID,
                      V_POSITION_ID,
                      V_FISCAL_YEAR_ID,
                      V_ASSIGNED_VALUE
                    );
               END IF;
                END;
          END;";
        $statement = $this->adapter->query($sql);
        return $statement->execute($boundedParameter);
    }

}
