<?php
namespace ManagerService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;
use Appraisal\Model\Stage;
use Appraisal\Model\Type;
use Appraisal\Model\Setup;
use Appraisal\Model\AppraisalAnswer;
use Appraisal\Model\AppraisalAssign;

class AppraisalEvaluationRepository implements RepositoryInterface{
    
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(AppraisalAnswer::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    //NOT USED YET
    public function getAllRequest($employeeId){
        $sql = "SELECT *
FROM
  (SELECT A.APPRAISAL_ID                         AS APPRAISAL_ID,
    A.APPRAISAL_TYPE_ID                          AS APPRAISAL_TYPE_ID,
    A.STATUS                                     AS STATUS,
    A.APPRAISAL_CODE                             AS APPRAISAL_CODE,
    A.APPRAISAL_EDESC                            AS APPRAISAL_EDESC,
    A.REMARKS                                    AS REMARKS,
    A.KPI_SETTING                                AS KPI_SETTING,
    A.COMPETENCIES_SETTING                       AS COMPETENCIES_SETTING,
    INITCAP(TO_CHAR(A.START_DATE,'DD-MON-YYYY')) AS START_DATE,
    INITCAP(TO_CHAR(A.END_DATE,'DD-MON-YYYY'))   AS END_DATE,
    E.EMPLOYEE_ID                                AS EMPLOYEE_ID,
    INITCAP(E.FIRST_NAME)                        AS FIRST_NAME,
    INITCAP(E.MIDDLE_NAME)                       AS MIDDLE_NAME,
    INITCAP(E.LAST_NAME)                         AS LAST_NAME,
    INITCAP(E.FULL_NAME)                         AS FULL_NAME,
    INITCAP(T.APPRAISAL_TYPE_EDESC)              AS APPRAISAL_TYPE_EDESC,
    INITCAP(S.STAGE_EDESC)                       AS STAGE_EDESC,
    S.STAGE_ID                                   AS STAGE_ID,
    (SELECT COUNT(*)
    FROM HRIS_APPRAISAL_ANSWER APNS
    WHERE APNS.EMPLOYEE_ID = E.EMPLOYEE_ID
    AND APNS.APPRAISAL_ID  = A.APPRAISAL_ID
    ) AS ANSWER_NUM,
    (SELECT COUNT(*)
    FROM HRIS_APPRAISAL_KPI APKPI
    WHERE APKPI.APPRAISAL_ID = A.APPRAISAL_ID
    AND APKPI.EMPLOYEE_ID    =E.EMPLOYEE_ID
    ) AS KPI_ANS_NUM,
    (SELECT COUNT(*)
    FROM HRIS_APPRAISAL_COMPETENCY APCOM
    WHERE APCOM.APPRAISAL_ID = A.APPRAISAL_ID
    AND APCOM.EMPLOYEE_ID    =E.EMPLOYEE_ID
    ) AS COM_ANS_NUM
  FROM HRIS_APPRAISAL_SETUP A
  INNER JOIN HRIS_APPRAISAL_ASSIGN AA
  ON A.APPRAISAL_ID=AA.APPRAISAL_ID
  INNER JOIN HRIS_EMPLOYEES E
  ON E.EMPLOYEE_ID=AA.EMPLOYEE_ID
  INNER JOIN HRIS_APPRAISAL_TYPE T
  ON T.APPRAISAL_TYPE_ID=A.APPRAISAL_TYPE_ID
  INNER JOIN HRIS_APPRAISAL_STAGE S
  ON S.STAGE_ID          =AA.CURRENT_STAGE_ID
  WHERE AA.APPRAISER_ID  =".$employeeId."
  OR AA.ALT_APPRAISER_ID =".$employeeId."
  AND AA.STATUS          ='E'
  AND E.STATUS           ='E'
  AND T.STATUS           ='E'
  AND S.STATUS           ='E'
  )
WHERE ( (COMPETENCIES_SETTING = (
  CASE
    WHEN ANSWER_NUM=0
    THEN ('Y')
  END )
AND COM_ANS_NUM >0)
OR (KPI_SETTING = (
  CASE
    WHEN ANSWER_NUM=0
    THEN ('Y')
  END)
AND KPI_ANS_NUM>0)
OR ANSWER_NUM  >0)";
        $statement = $this->adapter->query($sql);
//        print_r($statement->getSql()); die();
        $result = $statement->execute();
        return $result;
    }
}