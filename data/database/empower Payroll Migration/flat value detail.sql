begin
for f_list in (select ef.*,e.employee_id,e.full_name,fs.FLAT_ID from HR_FLAT_VALUE_DETAIL@EMPNEW ef
join hris_employees e on (to_number(ef.employee_code)=e.employee_id)
join HRIS_FLAT_VALUE_SETUP fs on (fs.FLAT_CODE=ef.flat_code)
)
loop
insert into HRIS_FLAT_VALUE_DETAIL values (
f_list.flat_id
,f_list.employee_id
,f_list.flat_value
,trunc(sysdate)
,null
,2
);
end loop;

end;