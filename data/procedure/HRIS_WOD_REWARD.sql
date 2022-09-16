create or replace PROCEDURE HRIS_WOD_REWARD
  (
    P_ID HRIS_EMPLOYEE_WORK_DAYOFF.ID%TYPE
  )
AS
  V_EMPLOYEE_ID HRIS_EMPLOYEE_WORK_DAYOFF.EMPLOYEE_ID%TYPE;
  V_WOH_FLAG HRIS_POSITIONS.WOH_FLAG%TYPE;
  V_FROM_DATE HRIS_EMPLOYEE_WORK_DAYOFF.FROM_DATE%TYPE;
  V_TO_DATE HRIS_EMPLOYEE_WORK_DAYOFF.TO_DATE%TYPE;
BEGIN
--select  work on day of details from HRIS_EMPLOYEE_WORK_DAYOFF Table
  SELECT EMPLOYEE_ID,
    TRUNC(FROM_DATE),
    TRUNC( TO_DATE)
  INTO V_EMPLOYEE_ID,
    V_FROM_DATE,
    V_TO_DATE
  FROM HRIS_EMPLOYEE_WORK_DAYOFF
  WHERE ID    = P_ID;
  -- if to date greater than today date then end procedure
  IF(V_TO_DATE>TRUNC(SYSDATE)) THEN 
    RETURN;
  END IF;
  --
  
  -- check if employeewise reward type set in  start
  BEGIN
  SELECT WOH_FLAG INTO V_WOH_FLAG FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID=V_EMPLOYEE_ID;
   EXCEPTION
  WHEN no_data_found THEN
    NULL;
  END;
  
  -- check if employeewise reward type set in  end
  
  
  -- select employee reward type in  position and set in variable  if not found terminate
  IF(V_WOH_FLAG IS NULL OR (V_WOH_FLAG!='O' AND V_WOH_FLAG!='L'))
  THEN
  BEGIN
    SELECT P.WOH_FLAG
    INTO V_WOH_FLAG
    FROM HRIS_EMPLOYEES E
    JOIN HRIS_POSITIONS P
    ON (E.POSITION_ID   = P.POSITION_ID)
    WHERE E.EMPLOYEE_ID =V_EMPLOYEE_ID;
  EXCEPTION
  WHEN no_data_found THEN
    HRIS_RAISE_ERR(V_EMPLOYEE_ID,'Work on dayoff reward could not be given.','Employee position is not set');
  END;
  
  END IF;
  --
  --delete from from necessary tables
  DELETE FROM HRIS_EMPLOYEE_LEAVE_ADDITION WHERE WOD_ID=P_ID;
  DELETE FROM HRIS_OVERTIME_DETAIL WHERE WOD_ID= P_ID;
  DELETE FROM HRIS_OVERTIME WHERE WOD_ID = P_ID;
  -- call another procedure acordint to reward type
  IF V_WOH_FLAG ='L' THEN
    HRIS_WOD_LEAVE_ADDITION(P_ID);
  ELSIF V_WOH_FLAG ='O' THEN
    HRIS_WOD_OT_ADDITION(P_ID);
  END IF;
END;
 