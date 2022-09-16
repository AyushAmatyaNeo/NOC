select 
employee_code,pay_Code,pay_type_flag,
CALC_PERCENT_VALUE
from hr_employee_facility 
where employee_code='1000445'
and pay_code='R003';


/

 select * from HRIS_FLAT_VALUE_SETUP WHERE ASSIGN_TYPE='P' and flat_id not in (71,73);

 select flat_id from HRIS_FLAT_VALUE_SETUP WHERE ASSIGN_TYPE='P' and flat_id not in (71,73);
 
 select *  from HRIS_POSITION_FLAT_VALUE WHERE flat_id
 in ( select flat_id from HRIS_FLAT_VALUE_SETUP WHERE ASSIGN_TYPE='P' and flat_id not in (71,73));
 
delete  from HRIS_POSITION_FLAT_VALUE WHERE flat_id
 in ( select flat_id from HRIS_FLAT_VALUE_SETUP WHERE ASSIGN_TYPE='P' and flat_id not in (71,73));
 
 
 select * from HRIS_SALARY_SHEET_EMP_DETAIL;





