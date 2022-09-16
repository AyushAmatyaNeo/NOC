create or replace function hris_validate_overtime_attd(
    p_employee_id HRIS_ATTENDANCE_DETAIL.EMPLOYEE_ID%TYPE,
    p_attendance_dt HRIS_ATTENDANCE_DETAIL.ATTENDANCE_DT%TYPE
) 
return char
as
    V_total_worked_minutes integer;
begin
select total_hour into V_total_worked_minutes from hris_attendance_detail where 
employee_id = p_employee_id and 
attendance_dt = p_attendance_dt;

if V_total_worked_minutes > 600 then
return 'T';
else
return 'F';
end if;
end;