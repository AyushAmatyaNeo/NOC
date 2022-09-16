 create table hris_bcs as 
    (SELECT 
    employee_id
    ,LISTAGG(shift_id,',') WITHIN GROUP(ORDER BY shift_id) AS shift_ids
    FROM HRIS_EMPLOYEE_SHIFTS
    GROUP BY employee_id);

/


create table hris_employee_shifts_pra as select * from hris_employee_shifts;

/



declare
v_case_id number;
begin

for caseList in ( select DISTINCT SHIFT_IDS from hris_bcs)
loop

select nvl(max(CASE_ID),0)+1 into v_case_id  from HRIS_BEST_CASE_SETUP;

insert into HRIS_BEST_CASE_SETUP 
(CASE_ID,CASE_NAME,START_DATE,END_DATE,STATUS,CREATED_DT,CREATED_BY,MODIFIED_DT,MODIFIED_BY,DELETED_DT,DELETED_BY)
values
(
v_case_id,
--shift '|| shiftList.START_DATE ||'-'||shiftList.END_DATE,
caseList.SHIFT_IDS,
'01-JUL-19','01-JUL-25',
'E',trunc(sysdate),null,null,null,null,null
);

-- to insert shift  in case start
for shiftList in (
WITH DATA AS
   ( SELECT caseList.SHIFT_IDS IDS FROM dual
    )
   SELECT regexp_substr(ids, '[^,]+', 1, LEVEL) ids
  FROM DATA
   CONNECT BY instr(ids, ',', 1, LEVEL - 1) > 0)
   loop
   
   insert into HRIS_BEST_CASE_SHIFT_MAP values (v_case_id,shiftList.IDS);
   
   
   end loop;


-- to insert shift in case stop


-- for employee -shift mapping start

for empList in ( select * from hris_bcs where shift_ids=caseList.SHIFT_IDS)
loop

HRIS_CASE_EMP_MAP(empList.employee_id,v_case_id,'A');

--insert into HRIS_BEST_CASE_EMP_MAP values (v_case_id,empList.employee_id);



end loop;


-- for employee shift mapping end






end loop;
end;
