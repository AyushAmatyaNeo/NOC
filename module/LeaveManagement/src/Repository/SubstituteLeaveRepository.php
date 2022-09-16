<?php

namespace LeaveManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use LeaveManagement\Model\SubtituteLeave;
use Zend\Db\Adapter\AdapterInterface;


class SubstituteLeaveRepository extends HrisRepository {

    public function getAllSubtituteLeaveData($search){
        $condition = '';
        if($search->employeeId){
            $condition = " and E.employee_id in (".implode(',',$search->employeeId).")";
        }

        $sql = "select E.employee_id, E.employee_code, E.full_name, 
        (select count(*) from hris_attendance_detail where employee_id = E.employee_id and overall_status = 'WD' 
        and attendance_dt between
        (select from_date from hris_month_code where month_id = $search->monthId) 
        and (select to_date from hris_month_code where month_id =$search->monthId) ) as total_wod,
        (select count(*) from hris_attendance_detail where employee_id = E.employee_id and overall_status = 'WH'
        and attendance_dt between
        (select from_date from hris_month_code where month_id = $search->monthId) 
        and (select to_date from hris_month_code where month_id =$search->monthId)) as total_woh,
        get_total_substitute_leave_month_wise(E.employee_id, $search->monthId) as total_substitute_leave
        from hris_Employees E where E.status ='E'". $condition;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
        
    }

    public function getWohWodList($data){
        $sql = "select * from (
            Select
                 E.employee_id,
                 E.employee_code,
                 HAD.attendance_dt as attendance_dt_ad,
                 HAD.attendance_dt || ' ('||BS_DATE(HAD.attendance_dt) || ' )' as attendance_dt,
                 E.full_name,
                 to_char(HAD.in_time,
                'HH:MI AM') as in_time,
                 to_char(HAD.out_time,
                 'HH:MI AM') as out_time,
                 case when (HAD.overall_status = 'WH') 
            then 'Work on Holiday' when (HAD.overall_status = 'WD') 
            then 'Work on Dayoff' 
            End as overall_status_detail,
            ewd.id as wod,
            ewh.id as woh,
            case when ewd.id is null and ewh.id is null then 'Y'
            when ewd.id is null and ewh.id is not null then 
                case when ewh.id in (SELECT WOH_ID FROM HRIS_EMPLOYEE_LEAVE_ADDITION WHERE WOH_ID IS NOT NULL) then 'N'
                else 'Y'
                end
            when ewd.id is null and ewh.id is not null then 
                case when ewh.id in (SELECT WOH_ID FROM HRIS_OVERTIME WHERE WOH_ID IS NOT NULL) then 'N'
                else 'Y'
                end
            when ewd.id is not null and ewh.id is null then
                case when ewd.id in (SELECT WOD_ID FROM HRIS_EMPLOYEE_LEAVE_ADDITION WHERE WOD_ID IS NOT NULL) then 'N'
                else 'Y'
                end
            when ewd.id is not null and ewh.id is null then
                case when ewd.id in (SELECT WOD_ID FROM HRIS_OVERTIME WHERE WOD_ID IS NOT NULL) then 'N'
                else 'Y'
                end
            end as allow_classify,
            HAD.overall_status 
            from hris_attendance_detail HAD 
            left join hris_employees E on (E.employee_id = HAD.employee_id)
            left join hris_employee_work_dayoff ewd on (ewd.employee_id = {$data['employeeId']} and ewd.status = 'AP'
            and HAD.attendance_dt between ewd.from_date and ewd.to_date)
            left join hris_employee_work_holiday ewh on (ewh.employee_id = {$data['employeeId']} and ewh.status = 'AP'
            and HAD.attendance_dt between ewh.from_date and ewh.to_date)
            where HAD.employee_id = {$data['employeeId']} 
            and HAD.attendance_dt between (select
                 from_date 
                from hris_month_code 
                where month_id = {$data['monthId']}) 
            and (select
                 to_date 
                from hris_month_code 
                where month_id = {$data['monthId']}) 
            and overall_status in ('WH',
                'WD') 
            order by HAD.attendance_dt
            )
            where allow_classify = 'Y'";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function classifyAsSubstituteLeave($data){
        EntityHelper::rawQueryResult($this->adapter, "
        CALL HRIS_CLASSIFY_SUBSTITUTE_LEAVE({$data['employeeId']},'{$data['date']}','{$data['status']}');
        ");
        return;
    }

    public function classifyAsOverTime($data){
        EntityHelper::rawQueryResult($this->adapter, "
        CALL HRIS_CLASSIFY_OVERTIME({$data['employeeId']},'{$data['date']}','{$data['status']}');
        ");
        return;
    }

}