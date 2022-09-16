<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Exception;
use Setup\Model\Branch;
use Setup\Model\Company;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Gender;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ApiRepository implements RepositoryInterface {

    private $adapter;
    private $gateway;

    //put your code here
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway('HRIS_EMPLOYEES', $adapter);
    }

    public function add(Model $model) {
        try {
            $this->gateway->insert($model->getArrayCopyForDB());
            return ['success' => true,];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            $this->gateway->update(['STATUS' => 'D'], ['EMPLOYEE_ID' => $id]);
            return ['success' => true,];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function edit(Model $model, $id) {

        $tempArray = $model->getArrayCopyForDB();

        if (array_key_exists('CREATED_DT', $tempArray)) {
            unset($tempArray['CREATED_DT']);
        }
        if (array_key_exists('EMPLOYEE_ID', $tempArray)) {
            unset($tempArray['EMPLOYEE_ID']);
        }
        if (array_key_exists('STATUS', $tempArray)) {
            unset($tempArray['STATUS']);
        }

        try {
            $this->gateway->update($tempArray, ['EMPLOYEE_ID' => $id]);
            return ['success' => true,];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchAllEmployee($id = null) {


        try {

            $sql = new Sql($this->adapter);
            $select = $sql->select();

            $select->columns(
                    EntityHelper::getColumnNameArrayWithOracleFns(HrEmployees::class, [
                        HrEmployees::FULL_NAME,
                        HrEmployees::FIRST_NAME,
                        HrEmployees::MIDDLE_NAME,
                        HrEmployees::LAST_NAME], [
                        HrEmployees::BIRTH_DATE,
                        HrEmployees::FAM_SPOUSE_BIRTH_DATE,
                        HrEmployees::FAM_SPOUSE_WEDDING_ANNIVERSARY,
                        HrEmployees::ID_DRIVING_LICENCE_EXPIRY,
                        HrEmployees::ID_CITIZENSHIP_ISSUE_DATE,
                        HrEmployees::ID_PASSPORT_EXPIRY,
                        HrEmployees::JOIN_DATE
                            ], NULL, NULL, NULL, 'E'), false);

            $select->from(["E" => "HRIS_EMPLOYEES"]);

            $select
                    ->join(['B' => Branch::TABLE_NAME], "E." . HrEmployees::BRANCH_ID . "=B." . Branch::BRANCH_ID, ['BRANCH_NAME' => new Expression('INITCAP(B.BRANCH_NAME)')], 'left')
                    ->join(['D' => Department::TABLE_NAME], "E." . HrEmployees::DEPARTMENT_ID . "=D." . Department::DEPARTMENT_ID, ['DEPARTMENT_NAME' => new Expression('INITCAP(D.DEPARTMENT_NAME)')], 'left')
                    ->join(['DES' => Designation::TABLE_NAME], "E." . HrEmployees::DESIGNATION_ID . "=DES." . Designation::DESIGNATION_ID, ['DESIGNATION_TITLE' => new Expression('INITCAP(DES.DESIGNATION_TITLE)')], 'left')
                    ->join(['C' => Company::TABLE_NAME], "E." . HrEmployees::COMPANY_ID . "=C." . Company::COMPANY_ID, ['COMPANY_NAME' => new Expression('INITCAP(C.COMPANY_NAME)')], 'left')
                    ->join(['G' => Gender::TABLE_NAME], "E." . HrEmployees::GENDER_ID . "=G." . Gender::GENDER_ID, ['GENDER_NAME' => new Expression('INITCAP(G.GENDER_NAME)')], 'left')
                    ->join(['BG' => "HRIS_BLOOD_GROUPS"], "E." . HrEmployees::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left')
                    ->join(['RG' => "HRIS_RELIGIONS"], "E." . HrEmployees::RELIGION_ID . "=RG.RELIGION_ID", ['RELIGION_NAME'], 'left')
                    ->join(['CN' => "HRIS_COUNTRIES"], "E." . HrEmployees::COUNTRY_ID . "=CN.COUNTRY_ID", ['COUNTRY_NAME' => new Expression('INITCAP(CN.COUNTRY_NAME)')], 'left')
                    ->join(['VM' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_PERM_VDC_MUNICIPALITY_ID . "=VM.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME' => new Expression('INITCAP(VM.VDC_MUNICIPALITY_NAME)')], 'left')
                    ->join(['VM1' => "HRIS_VDC_MUNICIPALITIES"], "E." . HrEmployees::ADDR_TEMP_VDC_MUNICIPALITY_ID . "=VM1.VDC_MUNICIPALITY_ID", ['VDC_MUNICIPALITY_NAME_TEMP' => 'VDC_MUNICIPALITY_NAME'], 'left')
                    ->join(['D1' => Department::TABLE_NAME], "E." . HrEmployees::APP_DEPARTMENT_ID . "=D1." . Department::DEPARTMENT_ID, ['APP_DEPARTMENT_NAME' => new Expression('INITCAP(D1.DEPARTMENT_NAME)')], 'left')
                    ->join(['DES1' => Designation::TABLE_NAME], "E." . HrEmployees::APP_DESIGNATION_ID . "=DES1." . Designation::DESIGNATION_ID, ['APP_DESIGNATION_TITLE' => new Expression('INITCAP(DES1.DESIGNATION_TITLE)')], 'left')
                    ->join(['P1' => Position::TABLE_NAME], "E." . HrEmployees::APP_POSITION_ID . "=P1." . Position::POSITION_ID, ['APP_POSITION_NAME' => new Expression('INITCAP(P1.POSITION_NAME)'), 'LEVEL_NO' => 'LEVEL_NO'], 'left')
                    ->join(['S1' => ServiceType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_TYPE_ID . "=S1." . ServiceType::SERVICE_TYPE_ID, ['APP_SERVICE_TYPE_NAME' => new Expression('INITCAP(S1.SERVICE_TYPE_NAME)')], 'left')
                    ->join(['SE1' => ServiceEventType::TABLE_NAME], "E." . HrEmployees::APP_SERVICE_EVENT_TYPE_ID . "=SE1." . ServiceEventType::SERVICE_EVENT_TYPE_ID, ['APP_SERVICE_EVENT_TYPE_NAME' => new Expression('INITCAP(SE1.SERVICE_EVENT_TYPE_NAME)')], 'left')
            ;

            if ($id != null) {
                $select->where([
                    "E.EMPLOYEE_ID=" . $id
                ]);
            }

            $select->where(["E.STATUS='E'"]);
            $select->order("E.FIRST_NAME ASC");
            $statement = $sql->prepareStatementForSqlObject($select);

            $result = $statement->execute();

            $tempArray = [];
            foreach ($result as $item) {
                array_push($tempArray, $item);
            }
            return ['success' => true, 'data' => $tempArray];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function fetchAttendance($date,$employeeCode=null){
        
        $whereCondition=($employeeCode==null)? " ":" AND E.EMPLOYEE_CODE='{$employeeCode}'";
        $sql="select E.FULL_NAME,A.EMPLOYEE_ID,E.EMPLOYEE_CODE,
            A.ATTENDANCE_DT,
            INITCAP(TO_CHAR(A.ATTENDANCE_DT, 'YYYY-MM-DD')) AS ATTENDANCE_DT,
          BS_DATE(TO_CHAR(A.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT_NEPALI,
       INITCAP(TO_CHAR(A.IN_TIME, 'HH:MI:SS AM'))          AS IN_TIME,
      INITCAP(TO_CHAR(A.OUT_TIME, 'HH:MI:SS AM'))         AS OUT_TIME,
        A.IN_REMARKS,A.OUT_REMARKS,
        MIN_TO_HOUR(TOTAL_HOUR) AS TOTAL_HOUR,
     DEP.DEPARTMENT_NAME                              AS DEPARTMENT_NAME,
      A.LATE_STATUS                                    AS LATE_STATUS,
      (
                  CASE
                    WHEN A.OVERALL_STATUS = 'DO'
                    THEN 'Day Off'
                    WHEN A.OVERALL_STATUS ='HD'
                    THEN 'On Holiday ('
                      ||H.HOLIDAY_ENAME
                      ||')'
                    WHEN A.OVERALL_STATUS ='LV'
                    THEN 'On Leave ('
                      ||L.LEAVE_ENAME
                      || ')'
                    WHEN A.OVERALL_STATUS ='TV'
                    THEN 'On Travel ('
                      ||TVL.DESTINATION
                      ||')'
                    WHEN A.OVERALL_STATUS ='TN'
                    THEN 'On Training ('
                      || (CASE WHEN A.TRAINING_TYPE = 'A' THEN T.TRAINING_NAME ELSE ETN.TITLE END)
                      ||')'
                    WHEN A.OVERALL_STATUS ='WD'
                    THEN 'Work On Dayoff'
                    WHEN A.OVERALL_STATUS ='WH'
                    THEN 'Work on Holiday ('
                      ||H.HOLIDAY_ENAME
                      ||')'
                    WHEN A.OVERALL_STATUS ='LP'
                    THEN 'On Partial Leave ('
                      ||L.LEAVE_ENAME
                      ||') '
                      ||LATE_STATUS_DESC(A.LATE_STATUS) 
                    WHEN A.OVERALL_STATUS ='VP'
                    THEN 'Work on Travel ('
                      ||TVL.DESTINATION
                      ||')'
                      ||LATE_STATUS_DESC(A.LATE_STATUS)
                    WHEN A.OVERALL_STATUS ='TP'
                    THEN 'Present ('
                      ||T.TRAINING_NAME
                      ||')'
                      ||LATE_STATUS_DESC(A.LATE_STATUS)
                    WHEN A.OVERALL_STATUS ='PR'
                    THEN 'Present '
                      ||LATE_STATUS_DESC(A.LATE_STATUS)
                    WHEN A.OVERALL_STATUS ='AB'
                    THEN 'Absent'
                    WHEN A.OVERALL_STATUS ='BA'
                    THEN 'Present(Late In and Early Out)'
                    WHEN A.OVERALL_STATUS ='LA'
                    THEN 'Present(Late Penalty)'
                  END) AS STATUS
        

        from 
        HRIS_ATTENDANCE_DETAIL A
        LEFT JOIN HRIS_EMPLOYEES E
        ON (E.EMPLOYEE_ID=A.EMPLOYEE_ID)
        LEFT JOIN HRIS_DEPARTMENTS DEP
        ON E.DEPARTMENT_ID = DEP.DEPARTMENT_ID
        LEFT JOIN HRIS_HOLIDAY_MASTER_SETUP H
        ON A.HOLIDAY_ID=H.HOLIDAY_ID
        LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
        ON A.LEAVE_ID=L.LEAVE_ID
        LEFT JOIN HRIS_TRAINING_MASTER_SETUP T
        ON (A.TRAINING_ID=T.TRAINING_ID AND A.TRAINING_TYPE='A')
        LEFT JOIN HRIS_EMPLOYEE_TRAINING_REQUEST ETN
        ON (ETN.REQUEST_ID=A.TRAINING_ID AND A.TRAINING_TYPE ='R')
        LEFT JOIN HRIS_EMPLOYEE_TRAVEL_REQUEST TVL
        ON A.TRAVEL_ID      =TVL.TRAVEL_ID
        

        WHERE A.ATTENDANCE_DT=TO_DATE('{$date}','YYYY-MM-DD') {$whereCondition}";
        
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
        
    }
    
    

    
  
  
    
  
    

    


}
