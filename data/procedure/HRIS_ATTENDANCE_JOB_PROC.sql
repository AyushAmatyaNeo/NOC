create or replace procedure hris_attendance_job_proc() as
begin
declare cursor L
for (
select * from  HRIS_ATTENDANCE where CHECKED='N'
and thumb_id in (select id_thumb_id from hris_employees where status='E' and retired_flag!='Y' and id_thumb_id is not null)
ORDER BY ATTENDANCE_TIME
);
for LIST as L DO 
call hris_attd_insert_exe(LIST.THUMB_ID,LIST.ATTENDANCE_DT,LIST.IP_ADDRESS,LIST.ATTENDANCE_FROM,LIST.ATTENDANCE_TIME,LIST.REMARKS);
END FOR;
end;