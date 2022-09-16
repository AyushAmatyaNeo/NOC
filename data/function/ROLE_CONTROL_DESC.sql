CREATE OR REPLACE FUNCTION "ROLE_CONTROL_DESC"(
    P_FLAG HRIS_ROLES.CONTROL%TYPE)
  RETURN VARCHAR2
IS
  V_FLAG_DESC VARCHAR2(50 BYTE);
BEGIN
  V_FLAG_DESC:=
  (
    CASE P_FLAG
    WHEN 'F' THEN
      'Full'
    WHEN 'C'THEN
      'Company Specific'
    WHEN 'U'THEN
      'User Specific'
    WHEN 'B'THEN
      'Branch Specific'
    WHEN 'DP'THEN
      'Department Specific'
    WHEN 'DS'THEN
      'Designation Specific'
    WHEN 'P'THEN
      'Position Specific'
    END);
  RETURN V_FLAG_DESC;
END;