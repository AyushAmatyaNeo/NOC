create or replace TRIGGER GROUP_SYNC_BRANCH
before insert or update
on HRIS_EMPLOYEES
for each row
begin
  :new.group_id := :new.branch_id;
end;
