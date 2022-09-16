create or replace FUNCTION LATE_STATUS_DESC(
    P_STATUS HRIS_ATTENDANCE_DETAIL.LATE_STATUS%TYPE)
  RETURN VARCHAR2
IS
  V_STATUS_DESC VARCHAR2(50 BYTE);
BEGIN
  V_STATUS_DESC:=
  (
    CASE P_STATUS
    WHEN 'L' THEN
      '[Late In]'
    WHEN 'E'THEN
      '[Early Out]'
    WHEN 'B' THEN
      '[Late In and Early Out]'
    WHEN 'X' THEN
      '[Missed Punch]'
    WHEN 'Y' THEN
      '[Late In and Missed Punch]'
    END);
  RETURN V_STATUS_DESC;
END;
 