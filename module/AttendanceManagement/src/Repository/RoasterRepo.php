<?php

namespace AttendanceManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\RoasterModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class RoasterRepo extends HrisRepository implements RepositoryInterface {

//    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        parent::__construct($adapter);
//        $this->adapter = $adapter;
        $this->gateway = new TableGateway(RoasterModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        $raw = EntityHelper::rawQueryResult($this->adapter, "
                SELECT EMPLOYEE_ID,
                  SHIFT_ID,
                  TO_CHAR(FOR_DATE,'DD-MON-YYYY') AS FOR_DATE,
                  TO_CHAR(FOR_DATE,'YYYY-MM-DD')  AS FOR_DATE_FORMATTED,
                  trim(LEADING '0' FROM TO_CHAR(FOR_DATE, 'DD'))||TO_CHAR(FOR_DATE, '-MON-YYYY') AS FOR_CHECK
                FROM HRIS_EMPLOYEE_SHIFT_ROASTER");
        return Helper::extractDbData($raw);
    }

    public function fetchById($id) {
        
    }

    public function merge($employeeId, $forDate, $shiftId) {
        /*EntityHelper::rawQueryResult($this->adapter, "
        DO
        Begin
                      DECLARE   V_EMPLOYEE_ID  NUMBER {$employeeId};
                       DECLARE  V_FOR_DATE     DATE {$forDate} ;
                       DECLARE  V_SHIFT_ID_NEW NUMBER {$shiftId};
                      DECLARE   V_SHIFT_ID_OLD NUMBER;
                      
                      
                       BEGIN
                       
                       SELECT SHIFT_ID
                         INTO V_SHIFT_ID_OLD default null
                         FROM HRIS_EMPLOYEE_SHIFT_ROASTER
                         WHERE EMPLOYEE_ID = V_EMPLOYEE_ID
                         AND FOR_DATE      =V_FOR_DATE;
                          IF(V_SHIFT_ID_NEW!=-1) THEN
                         INSERT
                         INTO HRIS_EMPLOYEE_SHIFT_ROASTER
                           (
                             EMPLOYEE_ID,
                             FOR_DATE,
                             SHIFT_ID
                           )
                           VALUES
                           (
                             V_EMPLOYEE_ID,
                             V_FOR_DATE,
                             V_SHIFT_ID_NEW
                           );
                           END IF;
                            END;
                        IF(V_SHIFT_ID_NEW=-1)
                       THEN
                       DELETE FROM HRIS_EMPLOYEE_SHIFT_ROASTER WHERE EMPLOYEE_ID =V_EMPLOYEE_ID AND FOR_DATE=V_FOR_DATE;
                       ELSE
                         UPDATE HRIS_EMPLOYEE_SHIFT_ROASTER
                         SET SHIFT_ID     = V_SHIFT_ID_NEW
                         WHERE EMPLOYEE_ID=V_EMPLOYEE_ID
                         AND FOR_DATE     =V_FOR_DATE;
                       END IF;
                      
                       IF( V_FOR_DATE<=current_date  AND ((V_SHIFT_ID_OLD!=V_SHIFT_ID_NEW)
                           OR (V_SHIFT_ID_OLD IS NULL AND V_SHIFT_ID_NEW!=-1 ))  )
                           THEN
                          call Hris_Reattendance(V_FOR_DATE,V_EMPLOYEE_ID,V_FOR_DATE);
                           END IF;
                      
                      
                      
                      END;"); */
    EntityHelper::rawQueryResult($this->adapter, "
    DO
    Begin
                DECLARE   V_EMPLOYEE_ID  NUMBER := {$employeeId};
                    DECLARE  V_FOR_DATE     DATE := to_date('{$forDate}', 'DD-MON-YYYY') ;
                    DECLARE  V_SHIFT_ID_NEW NUMBER := {$shiftId};
                DECLARE   V_SHIFT_ID_OLD NUMBER;
                
                
                    BEGIN
                    
                    SELECT MAX(SHIFT_ID)
                    INTO V_SHIFT_ID_OLD default null
                    FROM HRIS_EMPLOYEE_SHIFT_ROASTER
                    WHERE EMPLOYEE_ID = V_EMPLOYEE_ID
                    AND FOR_DATE      =V_FOR_DATE;
                    IF(V_SHIFT_ID_NEW!=-1) THEN
                    INSERT
                    INTO HRIS_EMPLOYEE_SHIFT_ROASTER
                        (
                        EMPLOYEE_ID,
                        FOR_DATE,
                        SHIFT_ID
                        )
                        VALUES
                        (
                        V_EMPLOYEE_ID,
                        V_FOR_DATE,
                        V_SHIFT_ID_NEW
                        );
                        END IF;
                        END;
                    IF(V_SHIFT_ID_NEW=-1)
                    THEN
                    DELETE FROM HRIS_EMPLOYEE_SHIFT_ROASTER WHERE EMPLOYEE_ID =V_EMPLOYEE_ID AND FOR_DATE=V_FOR_DATE;
                    ELSE
                    UPDATE HRIS_EMPLOYEE_SHIFT_ROASTER
                    SET SHIFT_ID     = V_SHIFT_ID_NEW
                    WHERE EMPLOYEE_ID=V_EMPLOYEE_ID
                    AND FOR_DATE     =V_FOR_DATE;
                    END IF;
                
                    IF( V_FOR_DATE<=current_date  AND ((V_SHIFT_ID_OLD!=V_SHIFT_ID_NEW)
                        OR (V_SHIFT_ID_OLD IS NULL AND V_SHIFT_ID_NEW!=-1 ))  )
                        THEN
                    call Hris_Reattendance(V_FOR_DATE,V_EMPLOYEE_ID,V_FOR_DATE);
                        END IF;
                
                
                
                END;");
    }

    public function getshiftDetail($data) {
        $shiftId = $data['shiftId'];
        //$fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        $selectedDate = $data['selectedDate'];

        $total_days = [];
        $total_days_between = 0;
        $getAllDates = [];
        $final_arr = [];

        $fromDate = $data['selectedDate'];

        /* find days between start date and end date */
        $sql_for_date = "SELECT DAYS_BETWEEN (TO_DATE ('{$fromDate}', 'DD-MON-YYYY'), TO_DATE('{$toDate}', 'DD-MON-YYYY')) as Total_Days FROM DUMMY";

        $statement_date = $this->adapter->query($sql_for_date);
        $result_date = $statement_date->execute();
        $total_days = Helper::extractDbData($result_date);
        if(!empty($total_days)) {
            $total_days_between = $total_days[0]['TOTAL_DAYS'];
            if( $total_days_between > 0 ) {
                if(!empty($total_days)) {
                    for($i=0;$i<=$total_days_between; $i++) {
                        $str_date = strtotime($fromDate);
                        $str_date_inc = strtotime("+{$i} days", $str_date);
                        $getAllDates[$i]['DATES'] = date('d-M-Y',$str_date_inc);
                        //$getAllDates[$i]['FORMATE_DATE'] = 'F'.date('Ymd',$str_date_inc);
                    }
                }
            }
        }       
        /* ends getting start date and end date */

        /**
         * Day = selected one
         * weekno = other remaining weekday
         */

        $sql_1 = "SELECT WEEKDAY (TO_DATE ('{$selectedDate}', 'DD-MON-YYYY')) week_days FROM DUMMY";
        $statement_wd = $this->adapter->query($sql_1);
        $result_wd = $statement_wd->execute();
        $weekday_arr = Helper::extractDbData($result_wd);
        $wd = $weekday_arr[0]['WEEK_DAYS'];
        if($wd == 6){
            $wd = 1;
        } else {
            $wd = $wd + 2;
        }

        //shiftID as selected 
        if(!empty($getAllDates)) {
            $inc_date = 0;
            foreach($getAllDates as $get_detail_single) {
                $final_arr[$inc_date]['DATES'] = $get_detail_single['DATES'];
                $final_arr[$inc_date]['DAY'] = $wd;
                //GET Day off or not
                if($wd == 1){
                    $sql_3 = "SELECT WEEKDAY1 FROM HRIS_SHIFTS WHERE SHIFT_ID={$shiftId}";
                }

                if($wd == 2){
                    $sql_3 = "SELECT WEEKDAY2 FROM HRIS_SHIFTS WHERE SHIFT_ID={$shiftId}";
                }

                if($wd == 3){
                    $sql_3 = "SELECT WEEKDAY3 FROM HRIS_SHIFTS WHERE SHIFT_ID={$shiftId}";
                }

                if($wd == 4){
                    $sql_3 = "SELECT WEEKDAY4 FROM HRIS_SHIFTS WHERE SHIFT_ID={$shiftId}";
                }

                if($wd == 5){
                    $sql_3 = "SELECT WEEKDAY5 FROM HRIS_SHIFTS WHERE SHIFT_ID={$shiftId}";
                }

                if($wd == 6){
                    $sql_3 = "SELECT WEEKDAY6 FROM HRIS_SHIFTS WHERE SHIFT_ID={$shiftId}";
                }

                if($wd == 7){
                    $sql_3 = "SELECT WEEKDAY7 FROM HRIS_SHIFTS WHERE SHIFT_ID={$shiftId}";
                }
                
                $statement_wd3 = $this->adapter->query($sql_3);
                $result_wd3 = $statement_wd3->execute();
                $weekday_arr_3 = Helper::extractDbData($result_wd3);

                if(!empty($weekday_arr_3)) {
                    if($wd == 1) {
                        $final_arr[$inc_date]['DAY_OFF'] = $weekday_arr_3[0]['WEEKDAY1'];
                    }
                    else if($wd == 2) {
                        $final_arr[$inc_date]['DAY_OFF'] = $weekday_arr_3[0]['WEEKDAY2'];
                    }
                    else if($wd == 3) {
                        $final_arr[$inc_date]['DAY_OFF'] = $weekday_arr_3[0]['WEEKDAY3'];
                    }
                    else if($wd == 4) {
                        $final_arr[$inc_date]['DAY_OFF'] = $weekday_arr_3[0]['WEEKDAY4'];
                    }
                    else if($wd == 5) {
                        $final_arr[$inc_date]['DAY_OFF'] = $weekday_arr_3[0]['WEEKDAY5']; 
                    }
                    else if($wd == 6) {
                        $final_arr[$inc_date]['DAY_OFF'] = $weekday_arr_3[0]['WEEKDAY6'];
                    }
                    else if($wd == 7) {
                        $final_arr[$inc_date]['DAY_OFF'] = $weekday_arr_3[0]['WEEKDAY7'];
                    } else {
                        $final_arr[$inc_date]['DAY_OFF'] = 'N';
                    }
                    
                }

                $final_arr[$inc_date]['SHIFT_ID'] = $shiftId;

                $sql_2 = "SELECT WEEKDAY (TO_DATE ('{$get_detail_single['DATES']}', 'DD-MON-YYYY')) week_days FROM DUMMY";
                $statement_wd2 = $this->adapter->query($sql_2);
                $result_wd2 = $statement_wd2->execute();
                $weekday_arr_2 = Helper::extractDbData($result_wd2);
                $wd2 = $weekday_arr_2[0]['WEEK_DAYS'];
                if($wd2 == 6){
                    $wd2 = 1;
                } else {
                    $wd2 = $wd2 + 2;
                }
                $final_arr[$inc_date]['WEEK_NO'] = $wd2;

                $inc_date++;
            }
        }

        /*$sql = "SELECT
TO_CHAR(DATES,'DD')||TO_CHAR(DATES,'-MON-YYYY') AS DATES,
WEEK_NO,
DAY_OFF,
DAY,
{$shiftId} AS SHIFT_ID
FROM
(SELECT * FROM 
       (select rownum - 1 + to_date('{$selectedDate}', 'dd-mon-yyyy') AS dates,
        TO_CHAR(rownum - 1 + to_date('{$selectedDate}', 'dd-mon-yyyy'),'D') AS WEEK_NO
        from all_objects 
        where rownum < to_date('{$toDate}', 'dd-mon-yyyy') -
       to_date('{$selectedDate}', 'dd-mon-yyyy') + 2) DATE_LIST) DL
       JOIN (select 
       TO_CHAR(to_date('{$selectedDate}'),'D') DAY,
       CASE 
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=1
       THEN
       WEEKDAY1
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=2
       THEN
       WEEKDAY2
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=3
       THEN
       WEEKDAY3
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=4
       THEN
       WEEKDAY4
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=5
       THEN
       WEEKDAY5
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=6
       THEN
       WEEKDAY6
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=7
       THEN
       WEEKDAY7
       END AS DAY_OFF
       from hris_shifts where shift_id={$shiftId}) SD  ON (1=1)";
       
        $raw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($raw);  */

        return $final_arr;
    }
    
    
    public function getRosterDetailList($data) {
        $total_days = [];
        $total_days_between = 0;
        $getAllDates = [];
        $result_arr = [];
        $final_result_arr = [];
        $result_arr_full = [];
        $final_result_arr_emp = [];

        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $employeeTypeId = $data['employeeTypeId'];
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];

        /* find days between start date and end date */
        $sql_for_date = "SELECT DAYS_BETWEEN (TO_DATE ('{$fromDate}', 'DD-MON-YYYY'), TO_DATE('{$toDate}', 'DD-MON-YYYY')) as Total_Days FROM DUMMY";

        $statement_date = $this->adapter->query($sql_for_date);
        $result_date = $statement_date->execute();
        $total_days = Helper::extractDbData($result_date);
        if(!empty($total_days)) {
            $total_days_between = $total_days[0]['TOTAL_DAYS'];
            if( $total_days_between > 0 ) {
                if(!empty($total_days)) {
                    for($i=0;$i<=$total_days_between; $i++) {
                        $str_date = strtotime($fromDate);
                        $str_date_inc = strtotime("+{$i} days", $str_date);
                        $getAllDates[$i]['DATES'] = date('d-M-Y',$str_date_inc);
                        $getAllDates[$i]['FORMATE_DATE'] = 'F'.date('Ymd',$str_date_inc);
                    }
                }
            }
        }       
        /* ends getting start date and end date */

        /*$getAllDates = EntityHelper::rawQueryResult($this->adapter, "
                SELECT  
TO_CHAR(TO_DATE('{$fromDate}','DD-MON-YYYY') + ROWNUM -1,'DD-MON-YYYY')  AS DATES,
'F'||TO_CHAR((TO_DATE('{$fromDate}','DD-MON-YYYY') + ROWNUM -1),'YYYYMMDD') AS FORMATE_DATE
        FROM dual D
        CONNECT BY  rownum <=  TO_DATE('{$toDate}','DD-MON-YYYY') -  TO_DATE('{$fromDate}','DD-MON-YYYY') + 1
                ");*/
        


        /*$pivotString = '';
        $i = 0;
        foreach ($getAllDates as $list) {
            if ($i == 0) {
                $pivotString .= '\'' . $list['DATES'] . '\' AS ' . $list['FORMATE_DATE'];
            } else {
                $pivotString .= ', \'' . $list['DATES'] . '\' AS ' . $list['FORMATE_DATE'];
            }
            $i++;
        }*/
        
        
        $searchCondition = $this->getSearchConditon($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId);

        if(!empty($getAllDates)) {
            $incre = 0;
            foreach($getAllDates as $getAllDatesSingle) {
                $sql = "
                            SELECT * FROM (select E.employee_code,E.employee_id,E.full_name,
                D.*,
                ER.SHIFT_ID
                from hris_employees E
                left JOIN 
                (SELECT  
                TO_CHAR(TO_DATE('{$getAllDatesSingle['DATES']}','DD-MON-YYYY'))  AS DATES
                        FROM dummy D ) D on (1=1) 
                        LEFT JOIN HRIS_EMPLOYEE_SHIFT_ROASTER ER ON (D.DATES=ER.FOR_DATE AND E.EMPLOYEE_ID=ER.EMPLOYEE_ID)
                        WHERE 1=1 AND E.STATUS='E' {$searchCondition}
                )
                ORDER BY FULL_NAME
                                ";

                $statement = $this->adapter->query($sql);
                $result = $statement->execute();
                $result_arr = Helper::extractDbData($result);

                $result_arr_full[$incre] =  $result_arr;

                $incre++;
            }
            
        } 

        if(!empty($result_arr_full)) {
            $inc_num = 0;
            foreach($result_arr_full as $result_arr_single_arr) {
                //Construct array by employee id
                if(!empty($result_arr_single_arr)) {
                    foreach($result_arr_single_arr as $indx=>$result_single_record) {
                        $final_result_arr_emp[$result_arr_single_arr[$indx]['EMPLOYEE_ID']][$inc_num]['EMPLOYEE_CODE'] = $result_single_record['EMPLOYEE_CODE'];
                        $final_result_arr_emp[$result_arr_single_arr[$indx]['EMPLOYEE_ID']][$inc_num]['EMPLOYEE_ID'] = $result_single_record['EMPLOYEE_ID'];
                        $final_result_arr_emp[$result_arr_single_arr[$indx]['EMPLOYEE_ID']][$inc_num]['FULL_NAME'] = $result_single_record['FULL_NAME'];
                        $final_result_arr_emp[$result_arr_single_arr[$indx]['EMPLOYEE_ID']][$inc_num]['DATE_D'] = $result_single_record['DATES'];
                        $final_result_arr_emp[$result_arr_single_arr[$indx]['EMPLOYEE_ID']][$inc_num]['DATE_S'] = $result_single_record['SHIFT_ID'];

                        $inc_num++;
                    }
                }
            }
        }

        //Construct final array
        if(!empty($final_result_arr_emp)) {
            $p_num = 0;
            foreach($final_result_arr_emp as $indx=>$final_arr_emp) {
                if(!empty($final_arr_emp)) {
                    $total_cnt = count($final_arr_emp); 
                    if($total_cnt > 1) {
                        $n_num = 1;
                          foreach($final_arr_emp as $indx2=>$final_emp_single) {
                            if($n_num == 1) {
                                $final_result_arr[$p_num]['EMPLOYEE_CODE'] = $final_emp_single['EMPLOYEE_CODE'];
                                $final_result_arr[$p_num]['EMPLOYEE_ID'] = $final_emp_single['EMPLOYEE_ID'];
                                $final_result_arr[$p_num]['FULL_NAME'] = $final_emp_single['FULL_NAME'];
                                $dt_format = 'F'.date('Ymd', strtotime($final_emp_single['DATE_D']));
                                $final_result_arr[$p_num][$dt_format.'_D'] = date('d-M-Y', strtotime($final_emp_single['DATE_D']));
                                $final_result_arr[$p_num][$dt_format.'_S'] = $final_emp_single['DATE_S'];
                            }
                            if($n_num > 1) {
                                $dt_format_1 = 'F'.date('Ymd', strtotime($final_emp_single['DATE_D']));
                                $final_result_arr[$p_num][$dt_format_1.'_D'] = date('d-M-Y', strtotime($final_emp_single['DATE_D']));
                                $final_result_arr[$p_num][$dt_format_1.'_S'] = $final_emp_single['DATE_S'];
                            }
                              $n_num++;
                          }
                    }
                }
                $p_num++;
            }
        }

/*****************working code ************************/
/*$sql = "
            SELECT * FROM (select E.employee_code,E.employee_id,E.full_name,
D.*,
ER.SHIFT_ID
from hris_employees E
left JOIN 
(SELECT  
TO_CHAR(TO_DATE('{$fromDate}','DD-MON-YYYY'))  AS DATES
        FROM dummy D ) D on (1=1) 
        LEFT JOIN HRIS_EMPLOYEE_SHIFT_ROASTER ER ON (D.DATES=ER.FOR_DATE AND E.EMPLOYEE_ID=ER.EMPLOYEE_ID)
         WHERE 1=1 AND E.STATUS='E' {$searchCondition}
)
ORDER BY FULL_NAME
                ";

$statement = $this->adapter->query($sql);
$result = $statement->execute();
$result_arr = Helper::extractDbData($result);

*/

        /*$sql = "
            SELECT * FROM (select E.employee_code,E.employee_id,E.full_name,
D.*,
ER.SHIFT_ID
from hris_employees E
left JOIN 
(SELECT  
TO_CHAR(TO_DATE('{$fromDate}','DD-MON-YYYY') + ROWNUM -1,'DD-MON-YYYY')  AS DATES
        FROM dual D
        CONNECT BY  rownum <=  TO_DATE('{$toDate}','DD-MON-YYYY') -  TO_DATE('{$fromDate}','DD-MON-YYYY') + 1) D on (1=1)
        LEFT JOIN HRIS_EMPLOYEE_SHIFT_ROASTER ER ON (D.DATES=ER.FOR_DATE AND E.EMPLOYEE_ID=ER.EMPLOYEE_ID)
         WHERE 1=1 AND E.STATUS='E' {$searchCondition}
)
PIVOT(
 MAX (DATES) AS D ,
  MAX (SHIFT_ID) AS S
 FOR DATES IN ({$pivotString})) ORDER BY FULL_NAME
                ";

                $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);*/

        return $final_result_arr;
    }
    
    public function getWeeklyRosterDetailList($data) {
        $employeeId = $data['employeeId'];
        $companyId = $data['companyId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $serviceEventTypeId = $data['serviceEventTypeId'];
        $employeeTypeId = $data['employeeTypeId'];
        
        $searchCondition = $this->getSearchConditon($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId);

        $sql = "
            SELECT *
FROM
  (SELECT E.employee_code,
    E.employee_id,
    E.full_name,
    case when ER.SUN is null then 0 else ER.SUN end as SUN,
    case when ER.MON is null then 0 else ER.MON end as MON,
    case when ER.TUE is null then 0 else ER.TUE end as TUE,
    case when ER.WED is null then 0 else ER.WED end as WED,
    case when ER.THU is null then 0 else ER.THU end as THU,
    case when ER.FRI is null then 0 else ER.FRI end as FRI,
    case when ER.SAT is null then 0 else ER.SAT end as SAT,
    case when s1.shift_ename is null then 'select shift' else s1.shift_ename end as SUN_NAME,
    case when s2.shift_ename is null then 'select shift' else s2.shift_ename end as MON_NAME,
    case when s3.shift_ename is null then 'select shift' else s3.shift_ename end as TUE_NAME,
    case when s4.shift_ename is null then 'select shift' else s4.shift_ename end as WED_NAME,
    case when s5.shift_ename is null then 'select shift' else s5.shift_ename end as THU_NAME,
    case when s6.shift_ename is null then 'select shift' else s6.shift_ename end as FRI_NAME,
    case when s7.shift_ename is null then 'select shift' else s7.shift_ename end as SAT_NAME
  
  FROM hris_employees E
  LEFT JOIN HRIS_WEEKLY_ROASTER ER
  left join hris_shifts s1 on (s1.shift_id=er.sun) 
            left join hris_shifts s2 on (s2.shift_id=er.mon) 
            left join hris_shifts s3 on (s3.shift_id=er.tue)  
            left join hris_shifts s4 on (s4.shift_id=er.wed) 
            left join hris_shifts s5 on (s5.shift_id=er.thu) 
            left join hris_shifts s6 on (s6.shift_id=er.fri)
            left join hris_shifts s7 on (s7.shift_id=er.sat)
  ON(E.EMPLOYEE_ID = ER.EMPLOYEE_ID)
  WHERE 1=1 AND E.STATUS='E' {$searchCondition}
  )
  ";
  
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
//        
        $retData=[];
        foreach ($result as $data){
            $tempArr=$data;
            $tempArr['SUNARR']=array('SHIFT_ID'=>$data['SUN'] ,'SHIFT_ENAME'=>$data['SUN_NAME']);
            $tempArr['MONARR']=array('SHIFT_ID'=>$data['MON'] ,'SHIFT_ENAME'=>$data['MON_NAME']);
            $tempArr['TUEARR']=array('SHIFT_ID'=>$data['TUE'] ,'SHIFT_ENAME'=>$data['TUE_NAME']);
            $tempArr['WEDARR']=array('SHIFT_ID'=>$data['WED'] ,'SHIFT_ENAME'=>$data['WED_NAME']);
            $tempArr['THUARR']=array('SHIFT_ID'=>$data['THU'] ,'SHIFT_ENAME'=>$data['THU_NAME']);
            $tempArr['FRIARR']=array('SHIFT_ID'=>$data['FRI'] ,'SHIFT_ENAME'=>$data['FRI_NAME']);
            $tempArr['SATARR']=array('SHIFT_ID'=>$data['SAT'] ,'SHIFT_ENAME'=>$data['SAT_NAME']);
            array_push($retData, $tempArr);
        }
        
        return $retData;
    }
    
    public function getWeeklyShiftDetail(){
        $shiftId = $data['shiftId'];
        $selectedDay = $data['selectedDay'];


        $sql = "SELECT
TO_CHAR(DATES,'DD')||TO_CHAR(DATES,'-MON-YYYY') AS DATES,
WEEK_NO,
DAY_OFF,
DAY,
{$shiftId} AS SHIFT_ID
FROM
(SELECT * FROM 
       (select rownum - 1 + to_date('{$selectedDate}', 'dd-mon-yyyy') AS dates,
        TO_CHAR(rownum - 1 + to_date('{$selectedDate}', 'dd-mon-yyyy'),'D') AS WEEK_NO
        from all_objects 
        where rownum < to_date('{$toDate}', 'dd-mon-yyyy') -
       to_date('{$selectedDate}', 'dd-mon-yyyy') + 2) DATE_LIST) DL
       JOIN (select 
       TO_CHAR(to_date('{$selectedDate}'),'D') DAY,
       CASE 
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=1
       THEN
       WEEKDAY1
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=2
       THEN
       WEEKDAY2
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=3
       THEN
       WEEKDAY3
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=4
       THEN
       WEEKDAY4
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=5
       THEN
       WEEKDAY5
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=6
       THEN
       WEEKDAY6
       WHEN TO_CHAR(to_date('{$selectedDate}'),'D')=7
       THEN
       WEEKDAY7
       END AS DAY_OFF
       from hris_shifts where shift_id={$shiftId}) SD  ON (1=1)";
       
        $raw = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($raw);
    }

}
