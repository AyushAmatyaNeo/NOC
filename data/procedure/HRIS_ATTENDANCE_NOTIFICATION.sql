CREATE OR REPLACE PROCEDURE HRIS_ATTENDANCE_NOTIFICATION(
    P_EMPLOYEE_ID HRIS_ATTENDANCE_DETAIL.EMPLOYEE_ID%TYPE,
    P_MESSAGE_DATETIME HRIS_NOTIFICATION.MESSAGE_DATETIME%TYPE,
    P_OVERALL_STATUS HRIS_ATTENDANCE_DETAIL.OVERALL_STATUS%TYPE,
    P_LATE_STATUS HRIS_ATTENDANCE_DETAIL.LATE_STATUS%TYPE)
AS
  V_MESSAGE_TITLE HRIS_NOTIFICATION.MESSAGE_TITLE%TYPE :=NULL;
  V_MESSAGE_DESC HRIS_NOTIFICATION.MESSAGE_DESC%TYPE   :=NULL;
  V_ROUTE HRIS_NOTIFICATION.ROUTE%TYPE                 :=NULL;
BEGIN
  IF P_OVERALL_STATUS = 'AB' THEN
    V_MESSAGE_TITLE  := 'Attendance Notification';
    V_MESSAGE_DESC   := 'Request for leave? You were absent on '||TO_CHAR(P_MESSAGE_DATETIME,'DD-MON-YYYY') ||'.';
    V_ROUTE          :='{"route":"leaverequest","action":"add"}';
  END IF;
  --
  IF P_OVERALL_STATUS = 'LA' THEN
    V_MESSAGE_TITLE  := 'Attendance Notification';
    V_MESSAGE_DESC   := 'Request for leave? You have been given Three day late absent penalty on '||TO_CHAR(P_MESSAGE_DATETIME,'DD-MON-YYYY') ||'.';
    V_ROUTE          :='{"route":"leaverequest","action":"add"}';
  END IF;
  --
  IF P_OVERALL_STATUS = 'BA' THEN
    V_MESSAGE_TITLE  := 'Attendance Notification';
    V_MESSAGE_DESC   := 'Request for leave? You have been given Late In and Early out on same day penalty on '||TO_CHAR(P_MESSAGE_DATETIME,'DD-MON-YYYY') ||'.';
    V_ROUTE          :='{"route":"leaverequest","action":"add"}';
  END IF;
  --
  IF (P_LATE_STATUS  ='X' OR P_LATE_STATUS ='Y') THEN
    V_MESSAGE_TITLE := 'Attendance Notification';
    V_MESSAGE_DESC  := 'Request for attendance? Missed punch on '||TO_CHAR(P_MESSAGE_DATETIME,'DD-MON-YYYY') ||' Detected.';
    V_ROUTE         :='{"route":"attendancerequest","action":"add"}';
  END IF;
  IF (V_MESSAGE_TITLE IS NOT NULL AND V_MESSAGE_DESC IS NOT NULL AND V_ROUTE IS NOT NULL) THEN
    HRIS_SYSTEM_NOTIFICATION(P_EMPLOYEE_ID,P_MESSAGE_DATETIME,V_MESSAGE_TITLE,V_MESSAGE_DESC,V_ROUTE);
  END IF;
END;    
