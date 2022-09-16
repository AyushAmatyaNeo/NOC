<?php

namespace SelfService\Repository;

use Application\Helper\EntityHelper;
use SelfService\Model\BirthdayModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository; // changed here

class BirthdayRepository extends HrisRepository{

    // private $tableGateway;
    // private $adapter;          // changed heres

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(BirthdayModel::TABLE_NAME, $adapter);
    }

    public function getBirthdays() {
        $sql = "SELECT * FROM (
                                SELECT EMP.EMPLOYEE_ID,
                                  ( CASE
                                      WHEN EMP.MIDDLE_NAME IS NULL THEN EMP.FIRST_NAME || ' ' || EMP.LAST_NAME
                                      ELSE EMP.FIRST_NAME || ' ' || EMP.MIDDLE_NAME || ' ' || EMP.LAST_NAME
                                  END ) FULL_NAME, 
                                  DSG.DESIGNATION_TITLE,
                                  EFL.FILE_PATH,
                                  EMP.BIRTH_DATE,
                                  TO_CHAR(EMP.BIRTH_DATE, 'fmddth Month') EMP_BIRTH_DATE, 
                                  'TODAY' BIRTHDAYFOR
                                FROM HRIS_EMPLOYEES EMP, HRIS_DESIGNATIONS DSG, HRIS_EMPLOYEE_FILE EFL
                                WHERE TO_CHAR(EMP.BIRTH_DATE, 'MMDD') = TO_CHAR(SYSDATE,'MMDD')
                                AND EMP.RETIRED_FLAG = 'N' AND EMP.STATUS='E' 
                                AND EMP.DESIGNATION_ID = DSG.DESIGNATION_ID
                                AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
                                UNION ALL
                                SELECT EMP.EMPLOYEE_ID,
                                  ( CASE
                                      WHEN EMP.MIDDLE_NAME IS NULL THEN EMP.FIRST_NAME || ' ' || EMP.LAST_NAME
                                      ELSE EMP.FIRST_NAME || ' ' || EMP.MIDDLE_NAME || ' ' || EMP.LAST_NAME
                                  END ) FULL_NAME, 
                                  DSG.DESIGNATION_TITLE,
                                  EFL.FILE_PATH,
                                  EMP.BIRTH_DATE,
                                  TO_CHAR(EMP.BIRTH_DATE, 'fmddth Month') EMP_BIRTH_DATE, 
                                  'UPCOMING' BIRTHDAYFOR
                                FROM HRIS_EMPLOYEES EMP, HRIS_DESIGNATIONS DSG, HRIS_EMPLOYEE_FILE EFL
                                WHERE TO_CHAR(EMP.BIRTH_DATE, 'MMDD') > TO_CHAR(SYSDATE,'MMDD')
                                AND EMP.RETIRED_FLAG = 'N'  AND EMP.STATUS='E' 
                                AND EMP.DESIGNATION_ID = DSG.DESIGNATION_ID
                                AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
                ) ORDER BY TO_CHAR(BIRTH_DATE,'MMDD')
                ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $birthdayResult = array();
        foreach ($result as $rs) {
            if ('TODAY' == strtoupper($rs['BIRTHDAYFOR'])) {
                $birthdayResult['TODAY'][$rs['EMPLOYEE_ID']] = $rs;
            }
            if ('UPCOMING' == strtoupper($rs['BIRTHDAYFOR'])) {
                $birthdayResult['UPCOMING'][$rs['EMPLOYEE_ID']] = $rs;
            }
        }

        return $birthdayResult;
    }

    public function add($model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function getBirthdayMessage($employeeId) {
        $sql = "
                SELECT E.FULL_NAME AS FROM_EMPLOYEE_NAME,
                  EF.FILE_PATH,
                  BM.BIRTHDAY_ID,
                  TO_CHAR(BM.BIRTHDAY_DATE,'DD-MON-YYYY') AS BIRTHDAY_DATE,
                  BM.MESSAGE,
                  TO_CHAR(BM.CREATED_DT,'HH:MI AM DD-MON-YYYY') AS WISH_DATE
                FROM HRIS_BIRTHDAY_MESSAGES BM
                LEFT JOIN HRIS_EMPLOYEES E
                ON (E.EMPLOYEE_ID=BM.FROM_EMPLOYEE)
                LEFT JOIN HRIS_EMPLOYEE_FILE EF
                ON (E.PROFILE_PICTURE_ID=EF.FILE_CODE)
                WHERE BM.TO_EMPLOYEE=?
                ORDER BY BM.CREATED_DT DESC";

        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;

        $result = $this->rawQuery($sql, $boundedParameter);
        $list = [];

        foreach ($result as $data) {
            array_push($list, $data);
        }
        return $list;
    }

    public function getBirthdayEmpDet($employeeId) {
        $sql = "
                SELECT E.EMPLOYEE_ID,
                  E.FULL_NAME,
                  DES.DESIGNATION_TITLE,
                  E.BIRTH_DATE,
                  EF.FILE_PATH
                FROM HRIS_EMPLOYEES E
                LEFT JOIN HRIS_EMPLOYEE_FILE EF
                ON (E.PROFILE_PICTURE_ID=EF.FILE_CODE)
                LEFT JOIN HRIS_DESIGNATIONS DES
                ON (E.DESIGNATION_ID = DES.DESIGNATION_ID)
                WHERE E.EMPLOYEE_ID     =?";

        $boundedParameter = [];
        $boundedParameter['employeeId'] = $employeeId;
        return $this->rawQuery($sql, $boundedParameter);          // changed here
        // return EntityHelper::rawQueryResult($this->adapter, $sql, $boundedParameter);
    }

    public function checkMessagePosted($fromEmployee, $toEmployee) {
        $sql = "SELECT count(*) as c FROM HRIS_BIRTHDAY_MESSAGES WHERE FROM_EMPLOYEE=$fromEmployee "
                . "AND TO_EMPLOYEE=$toEmployee";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

}
