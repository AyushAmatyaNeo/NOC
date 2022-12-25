<?php

namespace Training\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use Setup\Model\Institute;
use Setup\Model\Training;
use Training\Model\TrainingAttendance;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Application\Repository\HrisRepository;

class TrainingAttendanceRepository extends HrisRepository implements RepositoryInterface {

    protected $tableGateway;
    protected $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(TrainingAttendance::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $customCols = ["BS_DATE((T.START_DATE)) AS START_DATE_BS",
            "BS_DATE((T.END_DATE)) AS END_DATE_BS",
            "TO_CHAR(T.START_DATE, 'DD-MON-YYYY') AS START_DATE_AD",
            "TO_CHAR(T.END_DATE, 'DD-MON-YYYY') AS END_DATE_AD",
            "TRAINING_CATEGORY_DESC(T.TRAINING_CATEGORY) as CATEGORY_DESC"    
        ];
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(
                        Training::class, [
                    Training::TRAINING_NAME
                        ], NULL, NULL, NULL, NULL, 'T', FALSE, FALSE, NULL, $customCols)
                , false);


        $select->from(['T' => Training::TABLE_NAME]);
        $select->join(['I' => Institute::TABLE_NAME], "T." . Training::INSTITUTE_ID . "=I." . Institute::INSTITUTE_ID, [Institute::INSTITUTE_NAME => new Expression('INITCAP(I.' . Institute::INSTITUTE_NAME . ')')], 'left');
        $select->join(['C' => Company::TABLE_NAME], "T." . Training::COMPANY_ID . "=C." . Company::COMPANY_ID, [Company::COMPANY_NAME => new Expression('(C.' . Company::COMPANY_NAME . ')')], 'left');
        $select->where(["T.STATUS='E'"]);
        $select->order("T." . Training::TRAINING_NAME . " ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        // echo"<pre>";print_r($statement->getSql());die;
        $result = $statement->execute();
        $arrayList = [];
        foreach ($result as $row) {
            if ($row['TRAINING_TYPE'] == 'CP') {
                $row['TRAINING_TYPE'] = 'Company Personal';
            } else if ($row['TRAINING_TYPE'] == 'CC') {
                $row['TRAINING_TYPE'] = 'Company Contribution';
            } else {
                $row['TRAINING_TYPE'] = '';
            }
            array_push($arrayList, $row);
        }
        return $arrayList;
    }

    public function fetchById($id) {
        
    }

    public function fetchTrainingAssignedEmp($id) {
        
        $boundedParameter = [];
        $boundedParameter['id'] = $id;

        $sql = "
                SELECT (
                  CASE
                    WHEN E.MIDDLE_NAME IS NULL
                    THEN E.FIRST_NAME
                      || ' '
                      || E.LAST_NAME
                    ELSE E.FIRST_NAME
                      || ' '
                      || E.MIDDLE_NAME
                      || ' '
                      || E.LAST_NAME
                  END ) FULL_NAME,
                  ET.*
                FROM HRIS_EMPLOYEE_TRAINING_ASSIGN ET
                JOIN HRIS_EMPLOYEES E
                ON (ET.EMPLOYEE_ID   = E.EMPLOYEE_ID)
                WHERE ET.TRAINING_ID =? AND ET.STATUS='E' and ET.ACCEPT_FLAG='Y'";

        
        return $this->rawQuery($sql, $boundedParameter);
        // $result = EntityHelper::rawQueryResult($this->adapter, $sql);
        // return Helper::extractDbData($result);
    }

    public function updateTrainingAtd($data, $trainingId) {
        $insertList = "";
        foreach ($data as $date => $emp) {
            foreach ($emp as $employeeId => $status) {
                $insert = "INSERT INTO HRIS_EMP_TRAINING_ATTENDANCE (TRAINING_ID,EMPLOYEE_ID,TRAINING_DT,ATTENDANCE_STATUS) VALUES";
                $dStatus = ($status == 'true') ? 'P' : 'A';
                $insert = $insert . "({$trainingId},{$employeeId},TO_DATE('{$date}'),'{$dStatus}');\n";
                $insertList = $insertList .  $insert . "CALL HRIS_REATTENDANCE('{$date}',{$employeeId},'{$date}');\n";
            }

        }
        $sql = "
                DO
                BEGIN
                DELETE FROM HRIS_EMP_TRAINING_ATTENDANCE WHERE TRAINING_ID= {$trainingId};
                {$insertList}
            BEGIN
            DECLARE CURSOR EMPLOYEE_LIST
            FOR select * from HRIS_EMPLOYEE_TRAINING_ASSIGN where training_id={$trainingId};
            
            For EMPLOYEE_LISTS as EMPLOYEE_LIST
            DO
            CALL HRIS_TRAINING_LEAVE_REWARD (EMPLOYEE_LISTS.EMPLOYEE_ID,EMPLOYEE_LISTS.TRAINING_ID);
            END FOR;
            END;
                END;
                ";
            // echo"<pre>";print_r($sql);die;
        $result = EntityHelper::rawQueryResult($this->adapter, $sql);
        return $result;
    }

    public function fetchTrainingDates($id) {
        // $boundedParameter = [];
        // $boundedParameter['id1'] = $id;
        // $boundedParameter['id2'] = $id;
        // $sql = "
        // select add_days(TR.START_DATE, ROW_NUMBER() OVER (ORDER BY tr.training_id) ) as dates,
        // ROW_NUMBER() OVER (ORDER BY tr.training_id) as rownum,
        // (CASE WHEN add_days(TR.START_DATE, ROW_NUMBER() OVER (ORDER BY tr.training_id)) <= CURRENT_DATE
        // THEN 1 ELSE 0 END) AS STATUS,
        // current_date from HIERARCHY (
        // SOURCE (SELECT T.*,t.training_id as node_id,t.training_id as parent_id,
        // DAYS_BETWEEN(T.END_DATE,START_DATE) AS DIFF
        // FROM HRIS_TRAINING_MASTER_SETUP T
        // WHERE T.TRAINING_ID=?) start where TRAINING_ID = ?
        // ORPHAN IGNORE
        // CACHE FORCE ) TR
        // ORDER BY node_id
        // ";

        $sql= "do
        begin
        DECLARE v_start_date date;
        DECLARE v_index1 INT = 1;
        DECLARE v_end_date date;
        DECLARE v_sql varchar(10000);
        select start_date, end_date 
        into v_start_date, v_end_date
        from HRIS_TRAINING_MASTER_SETUP where training_id = {$id};
        v_sql = 'select  '''||:v_start_date|| ''' as dates,'||:v_index1 ||' as rownum, case when days_between('''||:v_start_date||''',current_date) > 0 then 1 else 0 end as status, current_date from dummy';
        WHILE :v_start_date <= :v_end_date DO
        v_sql = :v_sql || ' union select  '''||:v_start_date|| ''' as dates,'||:v_index1||' as rownum,  case when days_between('''||:v_start_date||''',current_date) > 0 then 1 else 0 end as status, current_date from dummy';
        v_start_date = add_days(:v_start_date,1);
        v_index1 = :v_index1 + 1;
            END WHILE;
            EXECUTE IMMEDIATE v_sql;
        end";

        // echo"<pre>";print_r($boundedParameter);die;
        // return $this->rawQuery($sql, $boundedParameter);
        return $this->rawQuery($sql);

        // $result = EntityHelper::rawQueryResult($this->adapter, $sql);
        // return Helper::extractDbData($result);
    }

    public function fetchAttendance($id) {
        $sql = "SELECT EMPLOYEE_ID,TO_CHAR(TRAINING_DT,'DD-MON-YYYY') AS TRAINING_DT,ATTENDANCE_STATUS FROM HRIS_EMP_TRAINING_ATTENDANCE WHERE TRAINING_ID =?";

        $boundedParameter = [];
        $boundedParameter['id'] = $id;
        return $this->rawQuery($sql, $boundedParameter);

        // $result = EntityHelper::rawQueryResult($this->adapter, $sql);
        // return Helper::extractDbData($result);
    }

}
