create or replace TRIGGER GROUP_SYNC_COMPANY
before insert or update
on HRIS_EMPLOYEES
for each row
begin
  :new.group_id := :new.company_id;
end;