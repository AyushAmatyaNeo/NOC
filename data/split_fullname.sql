BEGIN
  FOR emp IN
  (SELECT EMPLOYEE_ID,
    REGEXP_SUBSTR (FIRST_NAME, '[^ ]+', 1, 1) AS part_1 ,
    REGEXP_SUBSTR (FIRST_NAME, '[^ ]+', 1, 2) AS part_2 ,
    REGEXP_SUBSTR (FIRST_NAME, '[^ ]+', 1, 3) AS part_3
  FROM HRIS_EMPLOYEES_TEMP
  )
  LOOP
    IF emp.part_3 IS NULL THEN
      UPDATE HRIS_EMPLOYEES_TEMP
      SET FIRST_NAME    = emp.part_1,
        LAST_NAME       = emp.part_2
      WHERE EMPLOYEE_ID = emp.EMPLOYEE_ID;
    ELSE
      UPDATE HRIS_EMPLOYEES_TEMP
      SET FIRST_NAME    = emp.part_1,
        MIDDLE_NAME     = emp.part_2,
        LAST_NAME       = emp.part_3
      WHERE EMPLOYEE_ID = emp.EMPLOYEE_ID;
    END IF;
  END LOOP;
END;