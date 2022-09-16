create or replace PROCEDURE HRIS_POST_CHECK_ATTENDANCE(
    P_ATTENDANCE_DT DATE ,
    P_EMPLOYEE_ID   NUMBER:=NULL)
AS
  V_ATTENDANCE_DT DATE;
BEGIN
  V_ATTENDANCE_DT :=TRUNC(P_ATTENDANCE_DT);
  --
  HRIS_REATTENDANCE(V_ATTENDANCE_DT,P_EMPLOYEE_ID);
  --
  --
  IF V_ATTENDANCE_DT = TRUNC(SYSDATE) THEN
    --
    HRIS_COMPULSORY_OT_PROC(V_ATTENDANCE_DT);
    --
    FOR attendance IN
    (SELECT         *
    FROM HRIS_ATTENDANCE_DETAIL
    WHERE ATTENDANCE_DT= V_ATTENDANCE_DT
    AND (EMPLOYEE_ID   =
      CASE
        WHEN P_EMPLOYEE_ID IS NOT NULL
        THEN P_EMPLOYEE_ID
      END
    OR P_EMPLOYEE_ID IS NULL)
    )
    LOOP
      -- check if wod is present for every employee
      DECLARE
        V_ID HRIS_EMPLOYEE_WORK_DAYOFF.ID%TYPE;
      BEGIN
        SELECT ID
        INTO V_ID
        FROM HRIS_EMPLOYEE_WORK_DAYOFF
        WHERE EMPLOYEE_ID = attendance.EMPLOYEE_ID
        AND TO_DATE       = V_ATTENDANCE_DT-(
          CASE
            WHEN (attendance.TWO_DAY_SHIFT ='E')
            THEN 1
            ELSE 0
          END)
        AND STATUS ='AP'
        AND ROWNUM =1;
        --
        HRIS_WOD_REWARD(V_ID);
      EXCEPTION
      WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT('NO WORK ON DAYOFF FOUND');
      END;
      -- check if woh is present for every employee
      DECLARE
        V_ID HRIS_EMPLOYEE_WORK_HOLIDAY.ID%TYPE;
      BEGIN
        SELECT ID
        INTO V_ID
        FROM HRIS_EMPLOYEE_WORK_HOLIDAY
        WHERE EMPLOYEE_ID =attendance.EMPLOYEE_ID
        AND TO_DATE       = V_ATTENDANCE_DT-(
          CASE
            WHEN (attendance.TWO_DAY_SHIFT ='E')
            THEN 1
            ELSE 0
          END)
        AND STATUS = 'AP'
        AND ROWNUM =1;
        --
        HRIS_WOH_REWARD(V_ID);
      EXCEPTION
      WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT('NO WORK ON DAYOFF FOUND');
      END;
    END LOOP;
  END IF;
END;