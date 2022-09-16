create or replace PROCEDURE HRIS_CASE_EMP_MAP(
    P_EMPLOYEE_ID          IN HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE,
    P_CASE_ID              IN hris_best_case_emp_map.case_id%TYPE,
    P_CASE_ACTION          VARCHAR2
    )
AS
v_exists varchar2(1) := 'F';
begin
IF P_CASE_ACTION = 'A' THEN
BEGIN
            select 'T'
            into v_exists
            from hris_best_case_emp_map
            where EMPLOYEE_ID = P_EMPLOYEE_ID
            and case_id = P_CASE_ID;
        exception
            when no_data_found then
            null;
        end;
        if v_exists <> 'T' then

        INSERT INTO hris_best_case_emp_map(CASE_ID, EMPLOYEE_ID)
        VALUES(P_CASE_ID, P_EMPLOYEE_ID);

        end if;

        FOR SHIFT_LIST IN (SELECT SHIFT_ID FROM HRIS_BEST_CASE_SHIFT_MAP WHERE CASE_ID = P_CASE_ID) LOOP

        begin
        select 'T'
            into v_exists
            from HRIS_EMPLOYEE_SHIFTS
            where EMPLOYEE_ID = P_EMPLOYEE_ID
            and shift_id = SHIFT_LIST.SHIFT_ID;
        exception
            when no_data_found then
            null;
        end;

        if v_exists <> 'T' then
        INSERT INTO HRIS_EMPLOYEE_SHIFTS VALUES(
        P_EMPLOYEE_ID,
        SHIFT_LIST.SHIFT_ID,
        (SELECT START_DATE FROM hris_best_case_setup WHERE CASE_ID = P_CASE_ID), 
        (SELECT END_DATE FROM hris_best_case_setup WHERE CASE_ID = P_CASE_ID),
        p_case_id
        );
        end if;
        END LOOP;

ELSE

DELETE FROM hris_best_case_emp_map WHERE EMPLOYEE_ID = P_EMPLOYEE_ID AND CASE_ID = P_CASE_ID;

DELETE FROM hris_employee_shifts WHERE EMPLOYEE_ID = P_EMPLOYEE_ID AND CASE_ID = P_CASE_ID;

--FOR SHIFT_LIST IN (SELECT SHIFT_ID FROM HRIS_BEST_CASE_SHIFT_MAP WHERE CASE_ID = P_CASE_ID) LOOP
--DELETE FROM HRIS_EMPLOYEE_SHIFTS WHERE SHIFT_ID = SHIFT_LIST.SHIFT_ID AND EMPLOYEE_ID = P_EMPLOYEE_ID;
--END LOOP;

END IF;
end;