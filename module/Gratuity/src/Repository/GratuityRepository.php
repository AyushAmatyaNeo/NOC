<?php

/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/9/16
 * Time: 10:53 AM 
 */
 
namespace Gratuity\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Application\Repository\HrisRepository;
use Gratuity\Model\Gratuity;
use LeaveManagement\Model\LeaveApply;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

 class GratuityRepository extends HrisRepository {

        public function __construct(AdapterInterface $adapter, $tableName = null) {
            parent::__construct($adapter, $tableName);
        }

    public function pushFileLink($data){ 
        $fileName = $data['fileName'];
        $fileInDir = $data['filePath'];
        $sql = "INSERT INTO HRIS_LEAVE_FILES(FILE_ID, FILE_NAME, FILE_IN_DIR_NAME, LEAVE_ID) VALUES((SELECT IFNULL(MAX(FILE_ID),0)+1 FROM HRIS_LEAVE_FILES), '$fileName', '$fileInDir', null)";
        $statement = $this->adapter->query($sql);
        $statement->execute(); 
        $sql = "SELECT * FROM HRIS_LEAVE_FILES WHERE FILE_ID IN (SELECT MAX(FILE_ID) AS FILE_ID FROM HRIS_LEAVE_FILES)";
        $statement = $this->adapter->query($sql);
        return Helper::extractDbData($statement->execute());
    }

    public function linkLeaveWithFiles(){
        if(!empty($_POST['fileUploadList'])){
            $filesList = $_POST['fileUploadList'];
            $filesList = implode(',', $filesList);

            $sql = "UPDATE HRIS_LEAVE_FILES SET LEAVE_ID = (SELECT MAX(ID) FROM HRIS_EMPLOYEE_LEAVE_REQUEST) 
                    WHERE FILE_ID IN($filesList)";
            $statement = $this->adapter->query($sql);
            $statement->execute();
        }
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
        $this->linkLeaveWithFiles();
        $new = $model->getArrayCopyForDB();
        if($model->status == 'AP') {
            EntityHelper::rawQueryResult($this->adapter, "
                CALL HRIS_REATTENDANCE({$new['START_DATE']->getExpression()},{$new['EMPLOYEE_ID']},{$new['END_DATE']->getExpression()});
                ");
        }
        EntityHelper::rawQueryResult($this->adapter, "
                CALL HRIS_RECALCULATE_LEAVE({$new['EMPLOYEE_ID']},{$new['LEAVE_ID']});
                ");
    }

    public function edit(Model $model, $id) { 
        // TODO: Implement edit() method.
    }

    public function fetchAll() {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id) {
        return $this->tableGateway->select(function(Select $select) use($id) {
                    $select->columns(Helper::convertColumnDateFormat($this->adapter, new LeaveApply(), [
                                'startDate', 'endDate'
                            ]), false);
                    $select->where([LeaveApply::ID => $id]);
                })->current();
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }
    
    public function calculateGratuity($employeeId, $extraServiceYear,$retirementDate){
        EntityHelper::rawQueryResult($this->adapter, "
        CALL HRIS_PR_GRATUITY({$employeeId},{$extraServiceYear},{$retirementDate->getExpression()});
        ");
    }

    public function getFilteredRecords($search) {
        // print_r($search->employeeId);die;
        if($search->employeeId){
            $condition = " and E.employee_id in (".implode(',',$search->employeeId).")";
        }
        $sql = "select G.employee_id, G.gratuity_id, E.employee_code, E.full_name, G.TOTAL_amount, G.extra_service_yr 
        from hris_gratuity G
        left join hris_employees E on (E.employee_id = G.employee_id) where G.status='E' ". $condition;
        $statement = $this->adapter->query($sql);
        // print_r($statement);print_r($boundedParameter);die;
        $result = $statement->execute();
        return $result;
    }


    public function fetchGratuityDetails($id){
        $retirementDate = $this->rawQuery("SELECT RETIREMENT_DATE 
        FROM HRIS_GRATUITY WHERE GRATUITY_ID = {$id}")[0]['RETIREMENT_DATE'];

        $fiscalStartDate = $this->rawQuery("SELECT AD_DATE
        FROM CALENDAR_SETUP WHERE AD_DATE<'{$retirementDate}' AND SUBSTR(BS_MONTH,6,8)='04'
        ORDER BY AD_DATE DESC LIMIT 1")[0]["AD_DATE"];
        // print_r($fiscalStartDate);die;
        $sql = "select
                e.full_name,
                e.functional_level_id,
                e.position_id,
                e.salary,
                BS_DATE(e.join_date),
                BS_DATE(g.retirement_date) as EST_RETIREMENT_DATE,
                (select calculated_amount from hris_gratuity_detail 
                where gra_detail_id = 4 and gratuity_id = g.gratuity_id) as gratuity_amount,
                CAST(days_between(e.join_date,
            g.retirement_date)/365 as INT) as no_of_years,
                CAST(mod(days_between(e.join_date,
                g.retirement_date),
            365)/30.4 as INT) as no_of_months,
                CAST(mod(mod(days_between(e.join_date,
                g.retirement_date),
            365),
            30.4) as INT) as no_of_days,
                BS_DATE('{$fiscalStartDate}') as FISCAL_YEAR_START_DATE,
                CAST(days_between('{$fiscalStartDate}',
                g.retirement_date)/30.4 as INT) as fiscal_month,
                CAST(mod(days_between('{$fiscalStartDate}',
                g.retirement_date),
            30.4) as INT) as fiscal_days,
            days_between('{$fiscalStartDate}',
                g.retirement_date) as total_days
        from hris_gratuity g 
        left join hris_employees e on (e.employee_id = g.employee_id) 
        where g.gratuity_id = {$id}";
        // print_r($sql);die;
        $statement = $this->adapter->query($sql);
        // print_r($statement);print_r($boundedParameter);die;
        $result = $statement->execute()->current();
        return $result;
    }

    public function fetchGratuityAmount($id){
        $sql = "select (select days_between(e.join_date,g.retirement_date) from hris_gratuity g
                    left join hris_employees e on (e.employee_id = g.employee_id)
                    where g.gratuity_id = gd.gratuity_id) as days, (select
                    (g.extra_service_yr *365) from hris_gratuity g
                    left join hris_employees e on (e.employee_id = g.employee_id)
                    where g.gratuity_id = gd.gratuity_id) as extra_days , gd.calculated_amount, gd.description,
            gd.rate from hris_gratuity_detail gd
            where gd.group_id = 1 and gd.gratuity_id ={$id}";

        $sql1 = "select calculated_amount, description,rate from hris_gratuity_detail where group_id = 2 and gratuity_id = {$id}";

        $sql2 = "select calculated_amount, description from hris_gratuity_detail where group_id = 3 and gratuity_id = {$id}";


        $sql3="select days_between(e.join_date,g.retirement_date) as days, (g.extra_service_yr *365) as days from hris_gratuity g
        left join hris_employees e on (e.employee_id = g.employee_id)
        where gratuity_id = {$id}";

        $ar = [$this->rawQuery($sql),$this->rawQuery($sql1),$this->rawQuery($sql2),$this->rawQuery($sql3)];
        return $ar;
    }

    public function fetchSalaryDetail($id){
        $sql ="SELECT VAL/365 as B_G_PDAY
                FROM HRIS_SALARY_SHEET_DETAIL 
                WHERE EMPLOYEE_ID= {$id} 
                AND PAY_ID=132 
                AND SHEET_NO IN (SELECT
                    SHEET_NO 
                    FROM HRIS_SALARY_SHEET 
                    WHERE SALARY_TYPE_ID=1 
                    AND START_DATE = (SELECT
                    MAX(START_DATE) 
                        FROM HRIS_SALARY_SHEET 
                        WHERE SALARY_TYPE_ID=1))";
                        
        $sql1 = "SELECT hssd.val as grade_amount, hssd.sheet_no from hris_salary_sheet_detail hssd
        where hssd.employee_id = {$id} and hssd.pay_id = 67 and hssd.sheet_no in 
        (SELECT
                             SHEET_NO 
                            FROM HRIS_SALARY_SHEET 
                            WHERE SALARY_TYPE_ID=1 
                            AND START_DATE = (SELECT
                             MAX(START_DATE) 
                                FROM HRIS_SALARY_SHEET 
                                WHERE SALARY_TYPE_ID=1) )";

        $sql2 ="SELECT hssd.val as basic_salary, hssd.sheet_no from hris_salary_sheet_detail hssd
        where hssd.employee_id = {$id} and hssd.pay_id = 115 and hssd.sheet_no in 
        (SELECT
                             SHEET_NO 
                            FROM HRIS_SALARY_SHEET 
                            WHERE SALARY_TYPE_ID=1 
                            AND START_DATE = (SELECT
                             MAX(START_DATE) 
                                FROM HRIS_SALARY_SHEET 
                                WHERE SALARY_TYPE_ID=1) )";
        
        // print_r($sql);die;
        
        $ar = [$this->rawQuery($sql),$this->rawQuery($sql1),$this->rawQuery($sql2)];
        return $ar;
    }

    public function fetchLeaveDetail($id)
    {
        $sql="SELECT LEAVE_ENAME,SUM(BALANCE) BALANCE FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA , HRIS_LEAVE_MASTER_SETUP LMS, Hris_gratuity g
        WHERE LA.LEAVE_ID = LMS.LEAVE_ID
        AND LA.EMPLOYEE_ID = G.EMPLOYEE_ID
        AND LA.LEAVE_ID IN(SELECT LEAVE_ID FROM HRIS_LEAVE_MASTER_SETUP WHERE IS_MONTHLY='N' AND CASHABLE='Y')
        AND G.gratuity_id = {$id}
        GROUP BY LMS.LEAVE_ENAME";

        $sql1=" SELECT LMS.LEAVE_ENAME,SUM(BALANCE) AS BALANCE FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA , HRIS_LEAVE_MASTER_SETUP LMS, HRIS_GRATUITY G
        WHERE LA.LEAVE_ID = LMS.LEAVE_ID
        AND LA.EMPLOYEE_ID = G.EMPLOYEE_ID
        AND FISCAL_YEAR_MONTH_NO = (SELECT MAX(FISCAL_YEAR_MONTH_NO) FROM HRIS_EMPLOYEE_LEAVE_ASSIGN WHERE LEAVE_ID=LA.LEAVE_ID AND EMPLOYEE_ID=LA.EMPLOYEE_ID)
        AND LA.LEAVE_ID IN(SELECT LEAVE_ID FROM HRIS_LEAVE_MASTER_SETUP WHERE IS_MONTHLY='Y' AND CASHABLE='Y')
        AND G.gratuity_id = {$id}
        GROUP BY LMS.LEAVE_ENAME";

        $ld = [$this->rawQuery($sql),$this->rawQuery($sql1)];
        return $ld;
    }

    public function fetchRecalculateDetails($id){
        // $sql = "select * from hris_gratuity where gratuity_id = {$id}";
        // $statement = $this->adapter->query($sql);
        // // $result = $statement->execute()->current();
        // // print_r($result);die;
        // return $result;

        return $this->tableGateway->select(function(Select $select) use($id) {
            $select->columns(Helper::convertColumnDateFormat($this->adapter, new Gratuity()), false);
            $select->where([Gratuity::GRATUITY_ID => $id]);
        })->current();
    }
    public function deletePreviousData($id){
        $sql = "update hris_gratuity set status = 'D' where gratuity_id = {$id}";
        $statement = $this->adapter->query($sql);
        $statement->execute();

        $sql = "update hris_gratuity_detail set status = 'D' where gratuity_id = {$id}";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }
}
