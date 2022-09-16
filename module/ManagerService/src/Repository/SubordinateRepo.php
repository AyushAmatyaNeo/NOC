<?php

namespace ManagerService\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class SubordinateRepo implements RepositoryInterface {

    private $gateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway('HRIS_EMPLOYEES', $adapter);
    }

    public function fetchAll() {
        
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchSubordinates($id) {
        $sql = "
                SELECT E.EMPLOYEE_CODE,
                  E.EMPLOYEE_ID,
                  INITCAP(E.FULL_NAME) AS FULL_NAME,
                  E.MOBILE_NO ,
                  E.EMAIL_OFFICIAL,
                  TO_CHAR(E.BIRTH_DATE,'DD-MON-YYYY') AS BIRTH_DATE_AD,
                  BS_DATE(E.BIRTH_DATE)               AS BIRTH_DATE_BS,
                  C.COMPANY_NAME ,
                  B.BRANCH_NAME,
                  DEP.DEPARTMENT_NAME,
                  P.POSITION_NAME,
                  DES.DESIGNATION_TITLE,
                  ST.SERVICE_TYPE_NAME
                FROM HRIS_EMPLOYEES E
                JOIN HRIS_RECOMMENDER_APPROVER RA
                ON (E.EMPLOYEE_ID =RA.EMPLOYEE_ID)
                LEFT JOIN HRIS_COMPANY C
                ON (E.COMPANY_ID=C.COMPANY_ID)
                LEFT JOIN HRIS_BRANCHES B
                ON (E.BRANCH_ID =B.BRANCH_ID)
                LEFT JOIN HRIS_DEPARTMENTS DEP
                ON (E.DEPARTMENT_ID =DEP.DEPARTMENT_ID)
                LEFT JOIN HRIS_POSITIONS P
                ON (E.POSITION_ID=P.POSITION_ID)
                LEFT JOIN HRIS_DESIGNATIONS DES
                ON (E.DESIGNATION_ID =DES.DESIGNATION_ID)
                LEFT JOIN HRIS_SERVICE_TYPES ST
                ON (E.SERVICE_TYPE_ID=ST.SERVICE_TYPE_ID)
                WHERE RA.RECOMMEND_BY={$id}
                OR RA.APPROVED_BY    ={$id}";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
