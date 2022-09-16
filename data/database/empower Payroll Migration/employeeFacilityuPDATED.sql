



--
select * from HRIS_POSITION_FLAT_VALUE;

delete from HRIS_POSITION_FLAT_VALUE;

select * from HRIS_POSITION_FLAT_VALUE  where FLAT_ID is null;

DELETE from HRIS_POSITION_FLAT_VALUE where FLAT_ID is null;
---


declare
V_POSITION_ID NUMBER;
V_FLAT_ID NUMBER;
V_CHECK_COUNT NUMBER;
begin 

for all_list in (
select 
--es.employee_edesc,es.CUR_GRADE_CODE,gc.GRADE_EDESC,ef.PAY_CODE,ps.pay_edesc,ef.CALC_PERCENT_VALUE
gc.GRADE_CODE,ef.pay_code,max(ef.CALC_PERCENT_VALUE) AS VAL
from NBB7677JAN23.hr_employee_facility ef 
left join NBB7677JAN23.HR_EMPLOYEE_SETUP es on (ef.EMPLOYEE_CODE=es.employee_code)
left join NBB7677JAN23.HR_GRADE_CODE gc on (gc.GRADE_CODE=es.CUR_GRADE_CODE)
left join NBB7677JAN23.HR_PAY_SETUP ps on (ps.PAY_CODE=ef.PAY_CODE)
where es.CUR_GRADE_CODE is not null and ef.CALC_PERCENT_VALUE>0 and ef.EFFECTIVE_DATE_TO > '14-JAN-20'
group by gc.GRADE_CODE,ef.pay_code)
loop

V_POSITION_ID:=NULL;
V_FLAT_ID:=NULL;
V_CHECK_COUNT:=NULL;

begin
select POSITION_ID INTO V_POSITION_ID from HRIS_POSITIONS where position_id=all_list.GRADE_CODE;
EXCEPTION
WHEN no_data_found THEN
null;
END;

begin
select flat_id INTO V_FLAT_ID from HRIS_FLAT_VALUE_SETUP where ASSIGN_TYPE='P' and flat_code=all_list.PAY_CODE;
EXCEPTION
WHEN no_data_found THEN
null;
END;


select count(*) INTO V_CHECK_COUNT
from HRIS_POSITION_FLAT_VALUE where flat_id=V_FLAT_ID and POSITION_ID=V_POSITION_ID and FISCAL_YEAR_ID=5;

IF(V_CHECK_COUNT>0)
THEN
UPDATE  HRIS_POSITION_FLAT_VALUE SET ASSIGNED_VALUE=all_list.VAL where flat_id=V_FLAT_ID and POSITION_ID=V_POSITION_ID and FISCAL_YEAR_ID=5;
ELSE
insert into  HRIS_POSITION_FLAT_VALUE values (V_FLAT_ID,V_POSITION_ID,5,all_list.VAL);
END IF;

end loop;
end;
