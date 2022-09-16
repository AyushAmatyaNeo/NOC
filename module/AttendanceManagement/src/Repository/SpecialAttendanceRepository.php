<?php

namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use AttendanceManagement\Model\SpecialAttendanceSetup;
use AttendanceManagement\Model\SpecialAttendanceAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;

class SpecialAttendanceRepository extends HrisRepository implements RepositoryInterface {

  public function __construct(AdapterInterface $adapter) {
    parent::__construct($adapter, SpecialAttendanceSetup::TABLE_NAME);
    $this->adapter = $adapter;
  }

  public function add(Model $model) {
    return $this->tableGateway->insert($model->getArrayCopyForDB());
  }

  public function edit(Model $model, $id) {
    return $this->tableGateway->update($model->getArrayCopyForDB(),[SpecialAttendanceSetup::ID=>$id]);
  }

  public function fetchAll() {
    return $this->tableGateway->select([SpecialAttendanceSetup::STATUS=>'E'])->toArray();
  }

  public function fetchById($id) {
    return $this->tableGateway->select([SpecialAttendanceSetup::ID=>$id, SpecialAttendanceSetup::STATUS=>'E'])->current();
  }

  public function delete($id) {
    return $this->tableGateway->update([SpecialAttendanceSetup::STATUS=>'D'], [SpecialAttendanceSetup::ID=>$id]);
  }

  public function filterEmployees($searchQuery) {
    $boundedParameter = [];
    $searchCondition = EntityHelper::getSearchConditonBounded($searchQuery['companyId'], $searchQuery['locationId'], $searchQuery['departmentId'], $searchQuery['positionId'], $searchQuery['designationId'], $searchQuery['serviceTypeId'], $searchQuery['serviceEventTypeId'], $searchQuery['employeeTypeId'], $searchQuery['employeeId'], $searchQuery['genderId'], $searchQuery['locationId'], $searchQuery['functionalTypeId']);
    $boundedParameter = array_merge($boundedParameter, $searchCondition['parameter']);

    $sql = "SELECT 
              E.EMPLOYEE_ID                                                AS EMPLOYEE_ID,
              E.EMPLOYEE_CODE                                                   AS EMPLOYEE_CODE,
              INITCAP(E.FIRST_NAME)                                              AS MIDDLE_NAME,
              INITCAP(E.MIDDLE_NAME)                                              AS FULL_NAME,
              INITCAP(E.LAST_NAME)                                              AS LAST_NAME,
              INITCAP(E.FULL_NAME)                                              AS FULL_NAME,
              INITCAP(G.GENDER_NAME)                                            AS GENDER_NAME,
              (C.COMPANY_NAME)                                           AS COMPANY_NAME,
              (L.LOCATION_EDESC)                                            AS LOCATION_NAME,
              (D.DEPARTMENT_NAME)                                        AS DEPARTMENT_NAME,
              (DES.DESIGNATION_TITLE)                                    AS DESIGNATION_TITLE,
              (P.POSITION_NAME)                                          AS POSITION_NAME,
              P.LEVEL_NO                                                        AS LEVEL_NO,
              INITCAP(ST.SERVICE_TYPE_NAME)                                     AS SERVICE_TYPE_NAME,
              (CASE WHEN E.EMPLOYEE_TYPE='R' THEN 'REGULAR' ELSE 'WORKER' END)  AS EMPLOYEE_TYPE
            FROM HRIS_EMPLOYEES E
            LEFT JOIN HRIS_COMPANY C
            ON E.COMPANY_ID=C.COMPANY_ID
            LEFT JOIN HRIS_LOCATIONS L
            ON E.LOCATION_ID=L.LOCATION_ID
            LEFT JOIN HRIS_DEPARTMENTS D
            ON E.DEPARTMENT_ID=D.DEPARTMENT_ID
            LEFT JOIN HRIS_DESIGNATIONS DES
            ON E.DESIGNATION_ID=DES.DESIGNATION_ID
            LEFT JOIN HRIS_POSITIONS P
            ON E.POSITION_ID=P.POSITION_ID
            LEFT JOIN HRIS_SERVICE_TYPES ST
            ON E.SERVICE_TYPE_ID=ST.SERVICE_TYPE_ID
            LEFT JOIN HRIS_GENDERS G
            ON E.GENDER_ID=G.GENDER_ID
            WHERE 1                 =1 AND E.STATUS='E' 
            {$searchCondition['sql']} order by E.FULL_NAME  ";
            $statement = $this->adapter->query($sql);
            return $statement->execute($boundedParameter); 
  }

  public function assignSpToEmployees($spId, $employeeId, $attendanceDt, $displayInOutFlag, $createdBy){
    // print_r($employeeId);die;
    // print_r($displayInOutFlag);die;
    $boundedParameter = [];
    $boundedParameter['EMPLOYEE_ID'] = $employeeId;
    $boundedParameter['SP_ID'] = $spId;
    $boundedParameter['ATTENDANCE_DT'] = $attendanceDt;
    $boundedParameter['DISPLAY_IN_OUT'] = $displayInOutFlag;
    $boundedParameter['CREATED_BY'] = $createdBy;



    $sql = "
    DO
    BEGIN
    DECLARE V_SP_ID DECIMAL(7,0);
    DECLARE V_EMPLOYEE_ID DECIMAL(7,0);
    DECLARE V_ATTENDANCE_DT DATE;
    DECLARE V_DISPLAY_IN_OUT CHAR(1);
    DECLARE V_CREATED_BY DECIMAL(7,0);
    DECLARE V_ID NUMBER(7,0);
    BEGIN
    DELETE FROM HRIS_SPECIAL_ATTENDANCE_ASSIGN WHERE EMPLOYEE_ID = V_EMPLOYEE_ID AND  ATTENDANCE_DT = V_ATTENDANCE_DT;
    INSERT INTO HRIS_SPECIAL_ATTENDANCE_ASSIGN(ID, SP_ID, EMPLOYEE_ID, ATTENDANCE_DT, DISPLAY_IN_OUT, STATUS, CREATED_DT, CREATED_BY) 
    VALUES((SELECT ifnull(MAX(ID), 0) + 1 FROM HRIS_SPECIAL_ATTENDANCE_ASSIGN),{$boundedParameter['SP_ID']},{$boundedParameter['EMPLOYEE_ID']},'{$boundedParameter['ATTENDANCE_DT']}','{$boundedParameter['DISPLAY_IN_OUT']}','AP', CURRENT_DATE,{$boundedParameter['CREATED_BY']});
    
    CALL HRIS_REATTENDANCE_AN('{$boundedParameter['ATTENDANCE_DT']}',{$boundedParameter['EMPLOYEE_ID']}, '{$boundedParameter['ATTENDANCE_DT']}');
    END;
    END;";
    // echo ('<pre>');print_r($sql);
    // print_r($boundedParameter);die;


    
    $this->executeStatement($sql, $boundedParameter);
  }

  public function removeSpFromEmployees($employeeId, $attendanceDt){
    $boundedParameter = [];
    $boundedParameter['EMPLOYEE_ID'] = $employeeId;
    $boundedParameter['ATTENDANCE_DT'] = $attendanceDt;
    $sql = "DELETE FROM HRIS_SPECIAL_ATTENDANCE_ASSIGN WHERE EMPLOYEE_ID = :EMPLOYEE_ID AND ATTENDANCE_DT = :ATTENDANCE_DT";
    $this->executeStatement($sql, $boundedParameter);
  }
  public function reAttendance($employeeId, $attendanceDt){
    $boundedParameter = [];
    $boundedParameter['EMPLOYEE_ID'] = $employeeId;
    $boundedParameter['ATTENDANCE_DT'] = $attendanceDt;
    $this->executeStatement("call HRIS_REATTENDANCE_AN('$attendanceDt',$employeeId, '$attendanceDt');", $boundedParameter);
  }

}

