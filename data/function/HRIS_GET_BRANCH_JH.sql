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

select BRANCH_NAME into v_job_branch_name  from HRIS_BRANCHES where branch_id=p_branch_id;


return v_job_branch_name;
end;

