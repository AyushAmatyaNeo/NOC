CREATE OR REPLACE PROCEDURE HRIS_PREPARE_PAYROLL_DATA(
    V_FISCAL_YEAR_ID HRIS_MONTH_CODE.FISCAL_YEAR_ID%TYPE,
    V_FISCAL_YEAR_MONTH_NO HRIS_MONTH_CODE.FISCAL_YEAR_MONTH_NO%TYPE )
AS
  V_MONTH_START_DATE HRIS_MONTH_CODE.FROM_DATE%TYPE;
  V_MONTH_END_DATE HRIS_MONTH_CODE.TO_DATE %TYPE;
  V_DURATION                               NUMBER;
  V_DATA_COUNT                             NUMBER;
BEGIN
  SELECT TRUNC(FROM_DATE),
    TRUNC(TO_DATE),
    (TRUNC(TO_DATE)-TRUNC(FROM_DATE))+1
  INTO V_MONTH_START_DATE,
    V_MONTH_END_DATE,
    V_DURATION
  FROM HRIS_MONTH_CODE
  WHERE FISCAL_YEAR_ID    = V_FISCAL_YEAR_ID
  AND FISCAL_YEAR_MONTH_NO=V_FISCAL_YEAR_MONTH_NO;
  DELETE
  FROM HRIS_ATTENDANCE_PAYROLL
  WHERE ATTENDANCE_DT BETWEEN V_MONTH_START_DATE AND V_MONTH_END_DATE;
  FOR d IN
  (SELECT (level-1) AS DAY FROM dual CONNECT BY level <V_DURATION+1
  )
  LOOP
    FOR e IN
    (SELECT EMPLOYEE_ID,
      JOIN_DATE
    FROM HRIS_EMPLOYEES
    WHERE STATUS     ='E'
    )
    LOOP
      SELECT COUNT(*)
      INTO V_DATA_COUNT
      FROM HRIS_ATTENDANCE_DETAIL
      WHERE EMPLOYEE_ID =e.EMPLOYEE_ID
      AND ATTENDANCE_DT =V_MONTH_START_DATE+d.day;
      IF V_DATA_COUNT   >0 THEN
        INSERT
        INTO HRIS_ATTENDANCE_PAYROLL
          (
            EMPLOYEE_ID,
            ATTENDANCE_DT,
            IN_TIME,
            OUT_TIME,
            IN_REMARKS,
            OUT_REMARKS,
            TOTAL_HOUR,
            LEAVE_ID,
            HOLIDAY_ID,
            TRAINING_ID,
            ID,
            TRAVEL_ID,
            SHIFT_ID,
            DAYOFF_FLAG,
            OVERALL_STATUS,
            LATE_STATUS,
            HALFDAY_FLAG,
            GRACE_PERIOD,
            HALFDAY_PERIOD,
            TWO_DAY_SHIFT,
            TRAINING_TYPE
          )
        SELECT EMPLOYEE_ID,
          ATTENDANCE_DT,
          IN_TIME,
          OUT_TIME,
          IN_REMARKS,
          OUT_REMARKS,
          TOTAL_HOUR,
          LEAVE_ID,
          HOLIDAY_ID,
          TRAINING_ID,
          ID,
          TRAVEL_ID,
          SHIFT_ID,
          DAYOFF_FLAG,
          OVERALL_STATUS,
          LATE_STATUS,
          HALFDAY_FLAG,
          GRACE_PERIOD,
          HALFDAY_PERIOD,
          TWO_DAY_SHIFT,
          TRAINING_TYPE
        FROM HRIS_ATTENDANCE_DETAIL
        WHERE EMPLOYEE_ID =e.EMPLOYEE_ID
        AND ATTENDANCE_DT =V_MONTH_START_DATE+d.day;
      ELSE
        IF (e.JOIN_DATE IS NOT NULL AND e.JOIN_DATE > V_MONTH_START_DATE+d.day) THEN
          INSERT
          INTO HRIS_ATTENDANCE_PAYROLL
            (
              EMPLOYEE_ID,
              ATTENDANCE_DT,
              OVERALL_STATUS,
              LATE_STATUS,
              HALFDAY_FLAG,
              TWO_DAY_SHIFT
            )
            VALUES
            (
              e.EMPLOYEE_ID,
              V_MONTH_START_DATE+d.day,
              'AB',
              'N',
              'N',
              'D'
            );
        ELSE
          HRIS_PRELOAD_ATTEND_PAYROLL(V_MONTH_START_DATE+d.day,e.EMPLOYEE_ID);
        END IF;
      END IF;
    END LOOP;
  END LOOP;
END;