DECLARE
  P_EMPLOYEE_ID NUMBER(7)    := 1000378;
  P_THUMB_ID    NUMBER       := 495;
  P_START_DATE  VARCHAR2(25) := '15-NOV-19'; -- 'dd-MON-yy'
  P_END_DATE    VARCHAR2(25) := '22-NOV-19'; -- 'dd-MON-yy'
BEGIN
  FOR dates IN
  (SELECT to_date(P_START_DATE,'dd-mon-yyyy') + rownum -1 AS DAY
  FROM all_objects
  WHERE rownum <= to_date(P_END_DATE,'dd-mon-yyyy')-to_date(P_START_DATE,'dd-mon-yyyy')+1
  )
  LOOP
    ----For check in-----
    INSERT
    INTO HRIS_ATTENDANCE
      (
        EMPLOYEE_ID,
        ATTENDANCE_DT,
        IP_ADDRESS,
        ATTENDANCE_FROM,
        ATTENDANCE_TIME,
        REMARKS,
        THUMB_ID,
        CHECKED
      )
      VALUES
      (
        P_EMPLOYEE_ID,
        to_date(dates.DAY,'DD-MON-RR'),
        '192.168.1.4',
        'ATTENDANCE APPLICATION',
        to_timestamp( dates.DAY || ' 10.17.00.000000000 AM' ,'DD-MON-RR HH.MI.SSXFF AM'),
        NULL,
        495,
        'Y'
      );

    ------for check out-----
    INSERT
    INTO HRIS_ATTENDANCE
      (
        EMPLOYEE_ID,
        ATTENDANCE_DT,
        IP_ADDRESS,
        ATTENDANCE_FROM,
        ATTENDANCE_TIME,
        REMARKS,
        THUMB_ID,
        CHECKED
      )
      VALUES
      (
        P_EMPLOYEE_ID,
        to_date(dates.DAY,'DD-MON-RR'),
        '192.168.1.4',
        'ATTENDANCE APPLICATION',
        to_timestamp( dates.DAY || ' 06.18.00.000000000 PM' ,'DD-MON-RR HH.MI.SSXFF PM'),
        NULL,
        495,
        'Y'
      );
  END LOOP;

  HRIS_REATTENDANCE(P_START_DATE, P_EMPLOYEE_ID, P_END_DATE);

END;