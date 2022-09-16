<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\FlatValueDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;

class FlatValueDetailRepo extends HrisRepository implements RepositoryInterface {

    protected $adapter;
    protected $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(FlatValueDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function delete($id) {
        
    }

    public function fetchById($id) {

       $boundedParameter = [];
        $boundedParameter['monthId'] = $id['MONTH_ID'];
        $boundedParameter['employeeId'] = $id['EMPLOYEE_ID'];
        $boundedParameter['flatId'] = $id['FLAT_ID'];
        $sql = "
                SELECT F.FLAT_VALUE
                FROM HRIS_FLAT_VALUE_DETAIL F,
                  (SELECT * FROM HRIS_MONTH_CODE WHERE MONTH_ID=?
                  ) Y
                WHERE F. EMPLOYEE_ID = ?
                AND F.FISCAL_YEAR_ID = F.FISCAL_YEAR_ID
                AND F.FLAT_ID        = ?";

       
        return $this->rawQuery($sql, $boundedParameter)[0];

        // $statement = $this->adapter->query($sql);
        // $rawResult = $statement->execute();
        // return $rawResult->current();
    }

    public function getFlatValuesDetailById($flatValueId, $fiscalYearId, $emp, $monthId = null) {
        $searchCondition = EntityHelper::getSearchConditonBounded($emp['companyId'], $emp['branchId'], $emp['departmentId'], $emp['positionId'], $emp['designationId'], $emp['serviceTypeId'], $emp['serviceEventTypeId'], $emp['employeeTypeId'], $emp['employeeId'], $emp['genderId'], $emp['locationId']);

        $boundedParameter = [];
        $boundedParameter['flatValueId'] = $flatValueId;
      $boundedParameter['fiscalYearId'] = $fiscalYearId;
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);

        $empQuery = "SELECT E.EMPLOYEE_ID FROM HRIS_EMPLOYEES E WHERE 1=1 {$searchCondition['sql']}";
        $sql = "SELECT  FVD.*,EE.EMPLOYEE_CODE FROM HRIS_FLAT_VALUE_DETAIL FVD
    LEFT JOIN HRIS_EMPLOYEES EE on (EE.EMPLOYEE_ID=FVD.EMPLOYEE_ID)  WHERE FVD.FLAT_ID = ? AND FVD.FISCAL_YEAR_ID = ? AND FVD.EMPLOYEE_ID IN ({$empQuery})";

      return $this->rawQuery($sql, $boundedParameter);
        // $statement = $this->adapter->query($sql);
        // return $statement->execute();
    }

    public function getEMPPositionDetails($employee_id){
      $result = [];
      $sql = "select position_id, functional_level_id, 
      (select functional_level_edesc from hris_functional_levels 
      where functional_level_id = (select functional_level_id from hris_employees where employee_id={$employee_id})) as funclevel
      from hris_employees where employee_id={$employee_id}";

      $result = $this->rawQuery($sql);

      return $result;
     }

     public function checkEmployeeGradeCeiling($position_id, $func_level_edesc, $position_type){
        $sql = "select grade_ceiling_no from HRIS_EMPLOYEES_GRADE_CEILING_MASTER_SETUP where position_id = {$position_id} 
        and functional_level_edesc = '{$func_level_edesc}' and trim(position_type) = '{$position_type}'";

        $result = $this->rawQuery($sql);

        return $result;
     }


    public function postFlatValuesDetail($data) {

        $boundedParameter = [];
        $boundedParameter['flatId'] = $data['flatId'];
        $boundedParameter['employeeId'] = $data['employeeId'];
        $boundedParameter['flatValue'] = $data['flatValue'];
        $boundedParameter['fiscalYearId'] = $data['fiscalYearId'];

        $sql = "
               DO 
BEGIN
                DECLARE  V_FLAT_ID INT DEFAULT {$data['flatId']};
                 DECLARE V_EMPLOYEE_ID INT DEFAULT {$data['employeeId']} ;
                 DECLARE V_FLAT_VALUE DECIMAL DEFAULT {$data['flatValue']} ;
                 DECLARE V_FISCAL_YEAR_ID INT DEFAULT {$data['fiscalYearId']}  ;
                 DECLARE  V_OLD_FLAT_VALUE DECIMAL;
                BEGIN
        
        IF (SELECT COUNT(FLAT_VALUE)
                  FROM HRIS_FLAT_VALUE_DETAIL
                  WHERE FLAT_ID       = V_FLAT_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID) > 0
        THEN 
                  SELECT FLAT_VALUE
                  INTO V_OLD_FLAT_VALUE default null
                  FROM HRIS_FLAT_VALUE_DETAIL
                  WHERE FLAT_ID       = V_FLAT_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
                  
                  UPDATE HRIS_FLAT_VALUE_DETAIL
                  SET FLAT_VALUE      = V_FLAT_VALUE
                  WHERE FLAT_ID       = V_FLAT_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
                  
              ELSE
                  INSERT
                  INTO HRIS_FLAT_VALUE_DETAIL
                    (
                      FLAT_ID,
                      EMPLOYEE_ID,
                      FISCAL_YEAR_ID,
                      FLAT_VALUE,
                      CREATED_DT
                    )
                    VALUES
                    (
                      V_FLAT_ID,
                      V_EMPLOYEE_ID,
                      V_FISCAL_YEAR_ID,
                      V_FLAT_VALUE,
                     CURRENT_DATE
                    );
          
          END IF;
                END;
        
        END;
        ";
        //echo $sql; die;
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function getBulkFlatValuesDetailById($flat_id, $fiscalYearId, $emp) {

        $searchCondition = EntityHelper::getSearchConditonHana($emp['companyId'], $emp['branchId'], $emp['departmentId'], $emp['positionId'], $emp['designationId'], $emp['serviceTypeId'], $emp['serviceEventTypeId'], $emp['employeeTypeId'], $emp['employeeId'], $emp['genderId'], $emp['locationId']);


        $boundedParameter = [];
        $boundedParameter['fiscalYearId'] = $fiscalYearId;
        $boundedParameter=array_merge($boundedParameter, $searchCondition['parameter']);
        $flatCondition = "";

        if($flat_id != null && $flat_id != ''){
            $flat_id = implode($flat_id, ',');
            $flatCondition .= "$flat_id";
        } else {
            $rawlist = $this->rawQuery("SELECT STRING_AGG(FLAT_ID,','ORDER BY FLAT_ID) as list FROM HRIS_FLAT_VALUE_SETUP WHERE status = 'E' ");
            $flatCondition .= $rawlist[0]['LIST'];
        }

  //       $empQuery = "SELECT E.EMPLOYEE_ID FROM HRIS_EMPLOYEES E WHERE 1=1 {$searchCondition['sql']}";
  //       $sql = "
  //       SELECT * FROM (
  //       SELECT  e.employee_id,
  //   fvd.flat_value,
  //   fvd.flat_id,
  //   e.employee_code,
  //   e.SENIORITY_LEVEL,
  //   e.full_name FROM HRIS_FLAT_VALUE_DETAIL FVD
  //   RIGHT JOIN HRIS_EMPLOYEES E on (E.EMPLOYEE_ID=FVD.EMPLOYEE_ID AND FVD.FISCAL_YEAR_ID = ?)  WHERE e.status='E' {$searchCondition['sql']}
  // ) PIVOT(MAX(FLAT_VALUE) FOR FLAT_ID IN ($pivotString)) order by SENIORITY_LEVEL asc";
  // echo $sql; die;
        $sql = "call BULK_FLAT_VALUE(' {$searchCondition} ', 'MAX', 'FLAT_ID', '{$flat_id}') ";

        // print_r($sql); die;
        
        return $this->rawQuery($sql, $boundedParameter);
        // $statement = $this->adapter->query($sql);
        // return $statement->execute();
    }

    public function getColumns($flat_id){
             $flatCondition = "";
       if($flat_id != null && $flat_id != ''){
            $flat_id = implode($flat_id, ',');
            $flatCondition .= "$flat_id";
        } else {
            $rawlist = $this->rawQuery("SELECT STRING_AGG(FLAT_ID,','ORDER BY FLAT_ID) as list FROM HRIS_FLAT_VALUE_SETUP WHERE status = 'E' ");
            $flatCondition .= $rawlist[0]['LIST'];
        }
     // print_r($flatCondition);die;

/*
      $flat_ids = ':F_' . implode(',:F_', $flat_id);
      

      $boundedParameter = [];
      for($i = 0; $i < count($flat_id); $i++){
        $boundedParameter['F_'.$flat_id[$i]] = $flat_id[$i];
      } */

      $sql = "select flat_id, flat_edesc, 'F_'||flat_id as title from hris_flat_value_setup where flat_id in ($flatCondition)";

     

      return $this->rawQuery($sql, $boundedParameter);
      // $statement = $this->adapter->query($sql);
      // return $statement->execute();
    }

    public function postBulkFlatValuesDetail($data, $fiscalYearId) {
        $flatId = $data['flatId'];
        $employeeId = $data['employeeId'];
        $fiscalYearId = $fiscalYearId;
        if($data['value'] == null || $data['value'] == ''){
          $sql = "DELETE FROM HRIS_FLAT_VALUE_DETAIL
                  WHERE FLAT_ID       = $flatId
                  AND EMPLOYEE_ID    = $employeeId
                  AND FISCAL_YEAR_ID = $fiscalYearId";
        }
        else{
          $value = $data['value'];
          $sql = "
               DO 
BEGIN
                 DECLARE V_FLAT_ID INT  DEFAULT {$data['flatId']};
                 DECLARE V_EMPLOYEE_ID INT DEFAULT {$data['employeeId']};
                 DECLARE V_FLAT_VALUE DECIMAL DEFAULT {$data['value']};  
                 DECLARE V_FISCAL_YEAR_ID INT  DEFAULT $fiscalYearId  ;
                 DECLARE V_OLD_FLAT_VALUE DECIMAL ;
                BEGIN
        
        IF (SELECT COUNT(FLAT_VALUE)
                  FROM HRIS_FLAT_VALUE_DETAIL
                  WHERE FLAT_ID       = V_FLAT_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID)>0
          THEN
                  SELECT FLAT_VALUE
                  INTO V_OLD_FLAT_VALUE
                  FROM HRIS_FLAT_VALUE_DETAIL
                  WHERE FLAT_ID       = V_FLAT_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
                  
                  UPDATE HRIS_FLAT_VALUE_DETAIL
                  SET FLAT_VALUE      = V_FLAT_VALUE
                  WHERE FLAT_ID       = V_FLAT_ID
                  AND EMPLOYEE_ID    = V_EMPLOYEE_ID
                  AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
                  
        ELSE 
                  INSERT
                  INTO HRIS_FLAT_VALUE_DETAIL
                    (
                      FLAT_ID,
                      EMPLOYEE_ID,
                      FISCAL_YEAR_ID,
                      FLAT_VALUE,
                      CREATED_DT
                    )
                    VALUES
                    (
                      V_FLAT_ID,
                      V_EMPLOYEE_ID,
                      V_FISCAL_YEAR_ID,
                      V_FLAT_VALUE,
                      CURRENT_DATE
                    );
               END IF;
                END;
        
        END;
            ";

           
        }
        // print_r($sql); die;
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    public function getPositionWiseFlatValue($flatId, $fiscalYearId, $position_id) {
        $positionids = implode(',', $position_id);

        $startSql = "
        SELECT
            p.position_id,
            p.position_name,
            p.level_no";

        $endSql = "FROM
            hris_position_flat_value   pfv
            RIGHT JOIN hris_positions           p 
            ON ( p.position_id = pfv.position_id AND pfv.fiscal_year_id = {$fiscalYearId} )
            WHERE P.POSITION_ID IN ($positionids)
            group by  p.position_id, p.position_name, p.level_no
            order by p.level_no
            ";

        foreach ($flatId as $value) {
            $startSql .= ", MAX(case when FLAT_ID = {$value} then ASSIGNED_VALUE end ) as F_{$value} ";
        }

        $sql = $startSql . $endSql ;
        return $this->rawQuery($sql);
    }

    public function setPositionWiseFlatValue($data, $fiscalYearId) {
        $flatId = $data['flatId'];
        $positionId = $data['positionId'];
        $fiscalYearId = $fiscalYearId; 
          if($data['value'] == null || $data['value'] == ''){
            $sql = "DELETE FROM HRIS_POSITION_FLAT_VALUE
                    WHERE FLAT_ID       = $flatId
                    AND POSITION_ID    = $positionId
                    AND FISCAL_YEAR_ID = $fiscalYearId";
          }
          else{
            $value = $data['value']; 
            $sql = "
                DO
                BEGIN
                  
                  DECLARE  V_FLAT_ID INT DEFAULT $flatId;
                  DECLARE  V_POSITION_ID INT DEFAULT $positionId;
                  DECLARE  V_FLAT_VALUE DECIMAL DEFAULT  $value;
                  DECLARE  V_FISCAL_YEAR_ID INT DEFAULT  $fiscalYearId;
                  DECLARE  V_OLD_FLAT_VALUE DECIMAL ;

                  BEGIN IF (SELECT COUNT(ASSIGNED_VALUE)
                    FROM HRIS_POSITION_FLAT_VALUE
                    WHERE FLAT_ID       = V_FLAT_ID
                    AND POSITION_ID    = V_POSITION_ID
                    AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID) >0

                  THEN
                    SELECT ASSIGNED_VALUE
                    INTO V_OLD_FLAT_VALUE
                    FROM HRIS_POSITION_FLAT_VALUE
                    WHERE FLAT_ID       = V_FLAT_ID
                    AND POSITION_ID    = V_POSITION_ID
                    AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
                    
                    UPDATE HRIS_POSITION_FLAT_VALUE
                    SET ASSIGNED_VALUE      = V_FLAT_VALUE
                    WHERE FLAT_ID       = V_FLAT_ID
                    AND POSITION_ID    = V_POSITION_ID
                    AND FISCAL_YEAR_ID = V_FISCAL_YEAR_ID;
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
                        V_FLAT_VALUE
                      );
                 
                    END IF;
                END;
        
        END;";
          } 
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
}
