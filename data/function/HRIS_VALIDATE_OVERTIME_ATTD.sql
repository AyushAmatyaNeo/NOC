create or replace function hris_validate_overtime_attd(
    p_employee_id HRIS_ATTENDANCE_DETAIL.EMPLOYEE_ID%TYPE,
    p_attendance_dt HRIS_ATTENDANCE_DETAIL.ATTENDANCE_DT%TYPE
) 
return char
as
begin
return 'T';
end;