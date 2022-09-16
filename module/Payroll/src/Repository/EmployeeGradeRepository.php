<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Application\Repository\HrisRepository;

class EmployeeGradeRepository extends HrisRepository{

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
      $this->adapter = $adapter;
    }

    public function getEmployeeGradeDetails($emp){
      $searchCondition = EntityHelper::getSearchConditon($emp['companyId'], $emp['branchId'], $emp['departmentId'], $emp['positionId'], $emp['designationId'], $emp['serviceTypeId'], $emp['serviceEventTypeId'], $emp['employeeTypeId'], $emp['employeeId'], $emp['genderId'], $emp['locationId']);

      $sql = "SELECT
        e.employee_id,
        e.employee_code,
        e.full_name,
        g.opening_grade,
        g.additional_grade,
        g.GRADE_VALUE,
        g.grade_date,
        g.remarks
    FROM
        hris_employees e
        LEFT JOIN hris_employee_grade_info g on (e.employee_id = g.employee_id)
        left join hris_fiscal_years f on (g.fiscal_year_id = f.fiscal_year_id)
        where 1=1 {$searchCondition} ";

    return $this->rawQuery($sql);
    }

    public function postEmployeeGradeDetails($data, $fiscalYearId, $createdBy){
        // $boundedParameter = [];
        // $boundedParameter['flatId'] = $data['flatId'];
        // $boundedParameter['employeeId'] = $data['employeeId'];
        // $boundedParameter['flatValue'] = $data['flatValue'];
        // $boundedParameter['fiscalYearId'] = $data['fiscalYearId'];
      $opening_grade = $data['OPENING_GRADE'] == null || $data['OPENING_GRADE'] == '' ? 'null' : $data['OPENING_GRADE'];
      $employee_id = $data['EMPLOYEE_ID'] == null || $data['EMPLOYEE_ID'] == '' ? 'null' : $data['EMPLOYEE_ID'];
      $additional_grade = $data['ADDITIONAL_GRADE'] == null || $data['ADDITIONAL_GRADE'] == '' ? 'null' : $data['ADDITIONAL_GRADE'];
      $fiscal_year_id = $fiscalYearId == null || $fiscalYearId == '' ? 'null' : $fiscalYearId;
      $grade_value = $data['GRADE_VALUE'] == null || $data['GRADE_VALUE'] == '' ? 'null' : $data['GRADE_VALUE'];
      $grade_date = $data['GRADE_DATE'];
      $remarks = $data['REMARKS'];

        $sql = "
               DO
BEGIN  
 
                DECLARE  V_OPENING_GRADE INT DEFAULT {$opening_grade};
                DECLARE  V_EMPLOYEE_ID INT DEFAULT {$employee_id};
                DECLARE  V_ADDITIONAL_GRADE INT DEFAULT {$additional_grade};
                DECLARE  V_FISCAL_YEAR_ID INT DEFAULT {$fiscalYearId};
                DECLARE  V_GRADE_VALUE DECIMAL DEFAULT {$grade_value};
                DECLARE  V_GRADE_DATE DATE DEFAULT '{$grade_date}';
                DECLARE  V_REMARKS VARCHAR(500) DEFAULT '{$remarks}';
                DECLARE  V_CREATED_BY INT DEFAULT {$createdBy};
                DECLARE  V_OLD_DATE DATE;

                BEGIN
                IF (SELECT COUNT(GRADE_DATE)
                 
                  FROM HRIS_EMPLOYEE_GRADE_INFO
                  WHERE EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID) > 0
                  THEN
                  SELECT GRADE_DATE
                  INTO V_OLD_DATE
                  FROM HRIS_EMPLOYEE_GRADE_INFO
                  WHERE EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
                  
                  UPDATE HRIS_EMPLOYEE_GRADE_INFO
                  SET OPENING_GRADE = V_OPENING_GRADE,
                  ADDITIONAL_GRADE = V_ADDITIONAL_GRADE,
                  GRADE_VALUE = V_GRADE_VALUE,
                  GRADE_DATE = V_GRADE_DATE,
                  REMARKS = V_REMARKS,
                  MODIFIED_DATE = CURRENT_DATE,
                  MODIFIED_BY = V_CREATED_BY
                  WHERE EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
                  
                ELSE
                  INSERT
                  INTO HRIS_EMPLOYEE_GRADE_INFO
                    (
                      EMPLOYEE_ID,
                      FISCAL_YEAR_ID,
                      OPENING_GRADE,
                      ADDITIONAL_GRADE,
                      GRADE_VALUE,
                      GRADE_DATE,
                      REMARKS,
                      created_date,
                      created_by
                    )
                    VALUES
                    (
                      V_EMPLOYEE_ID,
                      V_FISCAL_YEAR_ID,
                      V_OPENING_GRADE,
                      V_ADDITIONAL_GRADE,
                      V_GRADE_VALUE,
                      V_GRADE_DATE,
                      V_REMARKS,
                      CURRENT_DATE,
                      V_CREATED_BY
                    );
                    END IF;
                    END;
                END;
";

      return $this->rawQuery($sql);
    }
}
