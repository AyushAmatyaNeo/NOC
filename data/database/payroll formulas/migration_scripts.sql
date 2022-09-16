INSERT
INTO HRIS_FLAT_VALUE_DETAIL
  (
    FLAT_ID,
    EMPLOYEE_ID,
    FLAT_VALUE,
    FISCAL_YEAR_ID,
    CREATED_DT
  )
SELECT FVS.FLAT_ID,
  E.EMPLOYEE_ID,
  M.FLAT_VALUE,
  3 AS FISCAL_YEAR_ID,
  TRUNC(SYSDATE) AS CREATED_DT
FROM HRIS_FLAT_VALUE_DETAIL_MIG M
JOIN HRIS_FLAT_VALUE_SETUP FVS
ON (M.FLAT_CODE=FVS.FLAT_CODE)
JOIN HRIS_EMPLOYEES E
ON (M.EMPLOYEE_CODE=E.EMPLOYEE_CODE);