CREATE OR REPLACE PROCEDURE HRIS_NOTIFY_BIRTHDAYS(
    P_DATETIME DATE:=NULL)
AS
BEGIN
  FOR birthday_employees IN
  (SELECT EMP.EMPLOYEE_ID,
    EMP.FULL_NAME,
    EMP.BIRTH_DATE,
    TO_CHAR(EMP.BIRTH_DATE, 'fmddth Month') EMP_BIRTH_DATE
  FROM HRIS_EMPLOYEES EMP
  WHERE TO_CHAR(EMP.BIRTH_DATE, 'MMDD') = TO_CHAR(SYSDATE,'MMDD')
  AND EMP.RETIRED_FLAG                  = 'N'
  AND EMP.STATUS                        ='E'
  )
  LOOP
    BEGIN
      FOR all_employees IN
      (SELECT EMPLOYEE_ID
      FROM HRIS_EMPLOYEES
      WHERE RETIRED_FLAG ='N'
      AND STATUS         ='E'
      )
      LOOP
        IF birthday_employees.EMPLOYEE_ID = all_employees.EMPLOYEE_ID THEN
          HRIS_SYSTEM_NOTIFICATION(all_employees.EMPLOYEE_ID,SYSDATE,'Birthday','Happy Birthday '||birthday_employees.FULL_NAME||'. Have a nice day.' ,'{"route":"birthday","action":"wish","id":"'||birthday_employees.EMPLOYEE_ID||'"}');
        ELSE
          HRIS_SYSTEM_NOTIFICATION(all_employees.EMPLOYEE_ID,SYSDATE,'Birthday',birthday_employees.FULL_NAME||' has birthday today.','{"route":"birthday","action":"wish","id":"'||birthday_employees.EMPLOYEE_ID||'"}');
        END IF;
      END LOOP;
    END;
  END LOOP;
END;