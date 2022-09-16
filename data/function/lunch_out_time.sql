create or replace function lunch_out_time
(p_employee_id in number,
p_attendnace_dt in date,
p_shift_id in number,
p_in_time in timestamp,
p_out_time in timestamp
) 
return Char 
is 
-- variables
v_half_time char(20 byte):=null;
v_count number:=0;
v_in_time timestamp:=p_in_time;
v_out_time timestamp:=p_out_time;
begin

if(v_in_time is null)
then
select 
to_timestamp(TO_CHAR(p_attendnace_dt,'DD-MON-YY')||TO_CHAR(start_time,'HH:MI AM'),'DD-MON-YY HH:MI AM')+ INTERVAL '30'
MINUTE into v_in_time
from hris_shifts where shift_id=p_shift_id;
end if;

if(v_out_time is null)
then
select 
to_timestamp(
case when two_day_shift='E'
then
TO_CHAR(p_attendnace_dt+1,'DD-MON-YY')
else
TO_CHAR(p_attendnace_dt,'DD-MON-YY')
end 
||TO_CHAR(end_time,'HH:MI AM'),'DD-MON-YY HH:MI AM')- INTERVAL '30' MINUTE
into v_out_time
from hris_shifts where shift_id=p_shift_id;
end if;

select Count(*) into v_count from Hris_Attendance where employee_id=p_employee_id and Attendance_Dt=trunc(p_attendnace_dt);


if v_count>2 and mod(v_count,2)=0 then

select 
half_out_time
into v_half_time
from (select 
to_char(Attendance_Time,'HH:MI AM') as half_out_time
from Hris_Attendance where employee_id=p_employee_id 
and attendance_time>v_in_time
and 
attendance_time<v_out_time
order by Attendance_Time ASC) where Rownum=1;

--select systimestamp into v_half_time from dual;
end if;

return v_half_time; 
end;
 