CREATE OR REPLACE PROCEDURE HRIS_SHIFT_EDIT(
    P_ID HRIS_EMPLOYEE_SHIFT_ASSIGN.ID%TYPE,
    P_SHIFT_ID HRIS_EMPLOYEE_SHIFT_ASSIGN.SHIFT_ID%TYPE,
    P_START_DATE HRIS_EMPLOYEE_SHIFT_ASSIGN.START_DATE%TYPE,
    P_END_DATE HRIS_EMPLOYEE_SHIFT_ASSIGN.END_DATE%TYPE,
    P_MODIFIED_BY HRIS_EMPLOYEE_SHIFT_ASSIGN.MODIFIED_BY%TYPE)
AS
  V_OLD_START_DATE HRIS_EMPLOYEE_SHIFT_ASSIGN.START_DATE%TYPE;
  V_EMPLOYEE_ID HRIS_EMPLOYEE_SHIFT_ASSIGN.EMPLOYEE_ID%TYPE;
BEGIN
  SELECT START_DATE,
    EMPLOYEE_ID
  INTO V_OLD_START_DATE,
    V_EMPLOYEE_ID
  FROM HRIS_EMPLOYEE_SHIFT_ASSIGN
  WHERE ID =P_ID;
  --
  IF(P_END_DATE IS NOT NULL) THEN
    UPDATE HRIS_EMPLOYEE_SHIFT_ASSIGN
    SET SHIFT_ID =P_SHIFT_ID,
      START_DATE =P_START_DATE,
      END_DATE   =P_END_DATE,
      MODIFIED_DT=SYSDATE,
      MODIFIED_BY=P_MODIFIED_BY
    WHERE ID     =P_ID;
  ELSE
    UPDATE HRIS_EMPLOYEE_SHIFT_ASSIGN
    SET SHIFT_ID =P_SHIFT_ID,
      START_DATE =P_START_DATE,
      MODIFIED_DT=SYSDATE,
      MODIFIED_BY=P_MODIFIED_BY
    WHERE ID     =P_ID;
  END IF;
  IF(TRUNC(V_OLD_START_DATE) <= TRUNC(P_START_DATE))THEN
    HRIS_REATTENDANCE(TRUNC(V_OLD_START_DATE),V_EMPLOYEE_ID);
  ELSE
    HRIS_REATTENDANCE(TRUNC(P_START_DATE),V_EMPLOYEE_ID);
  END IF;
END;