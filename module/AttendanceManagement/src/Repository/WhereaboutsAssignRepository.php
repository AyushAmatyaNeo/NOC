<?php
namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\RepositoryInterface;

class WhereaboutsAssignRepository {
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function getEmployeeList($data)
    {
        $condition = EntityHelper::getSearchConditonBounded($data['companyId'], $data['branchId'], $data['departmentId'], $data['positionId'], $data['designationId'], $data['serviceTypeId'], $data['serviceEventTypeId'], $data['employeeTypeId'], $data['employeeId']);

        $boundedParameter = [];
        $boundedParameter=array_merge($boundedParameter, $condition['parameter']);
        $sql =  new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("E.FULL_NAME as FULL_NAME"),
            new Expression("E.EMPLOYEE_ID as EMPLOYEE_ID"),
            new Expression("E.EMPLOYEE_CODE as EMPLOYEE_CODE"),
            new Expression("case when EWA.STATUS = 'E' then 'Y' else 'N' END AS ASSIGNED"),
        ],false);

        $select->from(["E" => "HRIS_EMPLOYEES"]);

        $select->join(["C" => "HRIS_COMPANY"],"E.COMPANY_ID = C.COMPANY_ID", ['COMPANY_NAME'],'left');
        $select->join(["EWA" => "HRIS_EMP_WHEREABOUT_ASN"],"E.EMPLOYEE_ID = EWA.EMPLOYEE_ID", ['ORDER_BY'], 'left');
        $select->join(["B" => "HRIS_BRANCHES"],"E.BRANCH_ID = B.BRANCH_ID", ['BRANCH_NAME' => new Expression("B.BRANCH_NAME ")], 'left');
        $select->join(["DEP" => "HRIS_DEPARTMENTS"],"E.DEPARTMENT_ID = DEP.DEPARTMENT_ID", ['DEPARTMENT_NAME'], 'left');
        $select->join(["P" => "HRIS_POSITIONS"],"E.POSITION_ID = P.POSITION_ID", ['POSITION_NAME'], 'left');
        $select->join(["DES" => "HRIS_DESIGNATIONS"],"E.DESIGNATION_ID = DES.DESIGNATION_ID", ['DESIGNATION_TITLE'], 'left');

        $select->where(["1=1 " . $condition['sql']]);

        $select->order("EWA.ORDER_BY");

        $statement = $sql->prepareStatementForSqlObject($select);
//        echo $statement->getSql();
//        die();
        $result = $statement->execute($boundedParameter);
        return $result;
    }

    public function updateStatus($employeeId){
        $updateSql = "UPDATE HRIS_EMP_WHEREABOUT_ASN SET STATUS = 'D' where employee_id = {$employeeId}";
        EntityHelper::rawQueryResult($this->adapter, $updateSql);
        return;
    }

    public function updateWhereabouts($employeeId, $updateData){
        $sql = '';
        $boundedParams = [];
        $boundedParams['employeeId'] = $employeeId;
        if($updateData['orderBy'] == null){
            $sql = "DELETE from HRIS_EMP_WHEREABOUT_ASN where EMPLOYEE_ID= ?";
        }
        else {
            $boundedParams['orderBy'] = $updateData['orderBy'];
            $sql = "
                DECLARE
                  p_employee_id   NUMBER := ?;
                  p_order_by      NUMBER := ?;
                  v_update      NUMBER := 1;
                  v_employee_id NUMBER;
                BEGIN
                  BEGIN
                    SELECT employee_id
                    INTO v_employee_id
                    FROM HRIS_EMP_WHEREABOUT_ASN
                    WHERE employee_id = p_employee_id;
                  EXCEPTION
                  WHEN no_data_found THEN
                    INSERT INTO HRIS_EMP_WHEREABOUT_ASN VALUES
                      ( p_employee_id, p_order_by, 'E'
                      );
                    v_update := 0;
                  END;
                  IF ( v_update = 1 ) THEN
                    UPDATE HRIS_EMP_WHEREABOUT_ASN
                    SET ORDER_BY      = p_order_by, status = 'E'
                    WHERE employee_id = p_employee_id;
                  END IF;
                  COMMIT;
                END;
                ";
        }

        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParams);
        return;
    }

}
