<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Application\Repository\HrisRepository;

class ExcelUploadRepository extends HrisRepository{

    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
      $this->adapter = $adapter;
    }

    public function updateEmployeeSalary($id, $salary){
        $boundedParameter = [];
        $boundedParameter['salary'] = $salary;
        $boundedParameter['id'] = $id;
        $sql = "UPDATE HRIS_EMPLOYEES SET SALARY = ? WHERE EMPLOYEE_ID = ?"; 

        $statement = $this->adapter->query($sql);
        $statement->execute($boundedParameter);
    }
    
    public function postPayValuesModifiedDetail($data) {
        $boundedParameter = [];
        $boundedParameter['monthId'] = $data['monthId'];
        $boundedParameter['employeeId'] = $data['employeeId'];
        $boundedParameter['payId'] = $data['payId'];
        $boundedParameter['val'] = $data['val'];
        $boundedParameter['salaryTypeId'] = $data['salaryTypeId'];
        $sql = " DO 
                BEGIN
                 DECLARE V_MONTH_ID  INT DEFAULT  {$data['monthId']};
                 DECLARE V_EMPLOYEE_ID  INT DEFAULT {$data['employeeId']};
                DECLARE  V_PAY_ID INT DEFAULT  {$data['payId']};
                DECLARE  V_VAL    DECIMAL DEFAULT {$data['val']}; 
                DECLARE  V_SALARY_TYPE_ID  INT DEFAULT {$data['salaryTypeId']};
                BEGIN
                IF(SELECT COUNT(VAL)
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE MONTH_ID       = V_MONTH_ID
                  AND PAY_ID = V_PAY_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID)  > 0 

                THEN
                  SELECT VAL
                  INTO V_VAL
                  FROM HRIS_SS_PAY_VALUE_MODIFIED
                  WHERE MONTH_ID       = V_MONTH_ID
                  AND PAY_ID = V_PAY_ID
                  AND SALARY_TYPE_ID = V_SALARY_TYPE_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID;
                  
                  UPDATE HRIS_SS_PAY_VALUE_MODIFIED
                  SET VAL      = V_VAL
                  WHERE MONTH_ID       = V_MONTH_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND PAY_ID = V_PAY_ID
                  AND SALARY_TYPE_ID       = V_SALARY_TYPE_ID;
                  ELSE
                  INSERT
                  INTO HRIS_SS_PAY_VALUE_MODIFIED
                    (
                      MONTH_ID,
                      EMPLOYEE_ID,
                      PAY_ID,
                      VAL,
                      SALARY_TYPE_ID
                    )
                    VALUES
                    (
                      V_MONTH_ID,
                      V_EMPLOYEE_ID,
                      V_PAY_ID,
                      V_VAL,
                      V_SALARY_TYPE_ID
                    );
                    END IF;
                    END;
                END;
";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
    
    public function getSalaryTypes(){
        $sql = "SELECT SALARY_TYPE_ID, SALARY_TYPE_NAME FROM HRIS_SALARY_TYPE";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
}
