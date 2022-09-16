create or replace function HRIS_GET_BRANCH_JH
(
p_employee_id number,
p_attendance_dt date,
p_branch_id number
)
return Char
is
v_job_branch_name varchar2(200 byte):=null;
begin


begin
select 
b.BRANCH_NAME into v_job_branch_name
from 
HRIS_JOB_HISTORY jh
left join HRIS_BRANCHES b on (b.branch_id=jh.TO_BRANCH_ID)
where
jh.employee_id=p_employee_id and p_attendance_dt between jh.START_DATE and jh.end_date and ROWNUM=1;
EXCEPTION
WHEN no_data_found THEN
select BRANCH_NAME into v_job_branch_name  from HRIS_BRANCHES where branch_id=p_branch_id;
END;




return v_job_branch_name;
end;

