create or replace TRIGGER GROUP_SYNC_DEPARTMENT
before insert or update
on HRIS_EMPLOYEES
for each row
begin
  :new.group_id := :new.DEPARTMENT_ID;
end;