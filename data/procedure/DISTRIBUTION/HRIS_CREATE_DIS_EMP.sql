CREATE OR REPLACE PROCEDURE HRIS_CREATE_DIS_EMP(
P_EMPLOYEE_ID  hris_EMPLOYEES.employee_id%TYPE
)
AS
V_DELETED_FLAG             CHAR(1 BYTE);
V_GENDER                   CHAR(1 BYTE);
V_MARITAL_STATUS           VARCHAR2(10 BYTE);
V_EMPLOYEE_STATUS          VARCHAR2(25 BYTE);
BEGIN

FOR EMP_DETAILS IN (SELECT C.COMPANY_CODE,E.*
FROM HRIS_EMPLOYEES E
LEFT JOIN HRIS_COMPANY C ON (C.COMPANY_ID=E.COMPANY_ID)
WHERE E.EMPLOYEE_ID=P_EMPLOYEE_ID)
LOOP

IF EMP_DETAILS.STATUS    = 'E' THEN
      V_DELETED_FLAG := 'N';
    ELSE
      V_DELETED_FLAG := 'Y';
    END IF;
    IF EMP_DETAILS.GENDER_ID    = 1 THEN
      V_GENDER          := 'M';
    ELSIF EMP_DETAILS.GENDER_ID = 2 THEN
      V_GENDER          := 'F';
    ELSE
      V_GENDER := 'O';
    END IF;
    IF EMP_DETAILS.MARITAL_STATUS = 'M' THEN
      V_MARITAL_STATUS    := 'Married';
    ELSE
      V_MARITAL_STATUS := 'Unmarried';
    END IF;
    IF EMP_DETAILS.RETIRED_FLAG = 'N' THEN
      V_EMPLOYEE_STATUS := 'Working';
    ELSE
      V_EMPLOYEE_STATUS := NULL;
    END IF;


 IF( EMP_DETAILS.EMPOWER_COMPANY_CODE!='-1' AND EMP_DETAILS.EMPOWER_COMPANY_CODE!=EMP_DETAILS.COMPANY_CODE)
   THEN

   DELETE  FROM HR_EMPLOYEE_SETUP WHERE EMPLOYEE_CODE=TO_CHAR(EMP_DETAILS.EMPLOYEE_ID) AND COMPANY_CODE=EMP_DETAILS.EMPOWER_COMPANY_CODE;

    INSERT INTO HR_EMPLOYEE_SETUP
        (
          EMPLOYEE_CODE ,
          EMPLOYEE_EDESC ,
          EMPLOYEE_NDESC ,
          GROUP_SKU_FLAG ,
          MASTER_EMPLOYEE_CODE ,
          PRE_EMPLOYEE_CODE ,
          EPERMANENT_ADDRESS1 ,
          EPERMANENT_COUNTRY ,
          PHONE ,
          MOBILE ,
          EMAIL ,
          SEX ,
          MARITAL_STATUS ,
          JOIN_DATE ,
          LINK_SUB_CODE ,
          EMPLOYEE_TYPE_CODE ,
          PERIOD_CODE ,
          CUR_DEPARTMENT_CODE ,
          CUR_DESIGNATION_CODE ,
          CUR_GRADE_CODE ,
          CUR_BASIC_SALARY ,
          EMPLOYEE_STATUS ,
          EARNING_BASIS ,
          COMPANY_CODE ,
          BRANCH_CODE ,
          CREATED_BY ,
          CREATED_DATE ,
          DELETED_FLAG ,
          EMPLOYEE_MANUAL_CODE ,
          LOCK_FLAG ,
          WEEK_OFF ,
          ACCOUNT_NO ,
          CIT_NUMBER ,
          REMOTE_CODE ,
          PAN_NO ,
          LICENSE_TYPE ,
          OVERTIME_APPLICABLE ,
          PF_NUMBER ,
          SAL_SHEET_CODE,
          DEPOSIT_ACCOUNT,
          THUMB_ID
        )
        VALUES
        (
          TO_CHAR(EMP_DETAILS.EMPLOYEE_ID) ,
          REPLACE(CONCAT(CONCAT(EMP_DETAILS.FIRST_NAME
          ||' ',EMP_DETAILS.MIDDLE_NAME
          ||' '),EMP_DETAILS.LAST_NAME),'  ',' ') ,
          NVL(EMP_DETAILS.NAME_NEPALI, CONCAT(CONCAT(EMP_DETAILS.FIRST_NAME
          ||' ',EMP_DETAILS.MIDDLE_NAME
          ||' '), EMP_DETAILS.LAST_NAME)) ,
          'I' ,
          '01.00' ,
          '01' ,
          EMP_DETAILS.ADDR_PERM_STREET_ADDRESS ,
          TO_CHAR(EMP_DETAILS.COUNTRY_ID) ,
          EMP_DETAILS.TELEPHONE_NO ,
          EMP_DETAILS.MOBILE_NO ,
          EMP_DETAILS.EMAIL_OFFICIAL ,
          V_GENDER ,
          V_MARITAL_STATUS ,
          EMP_DETAILS.JOIN_DATE ,
          'E'
          ||TO_CHAR(EMP_DETAILS.EMPLOYEE_ID) ,
          TO_CHAR(EMP_DETAILS.SERVICE_TYPE_ID,'FM00') ,
          '01' ,
          TO_CHAR(EMP_DETAILS.DEPARTMENT_ID,'FM000') ,
          TO_CHAR(EMP_DETAILS.DESIGNATION_ID,'FM000') ,
          TO_CHAR(EMP_DETAILS.POSITION_ID,'FM000') ,
          EMP_DETAILS.SALARY ,
          V_EMPLOYEE_STATUS ,
          'Monthly' ,
          EMP_DETAILS.EMPOWER_COMPANY_CODE ,
          EMP_DETAILS.EMPOWER_COMPANY_CODE
          ||'.01' ,
          'SYNC' ,
          NVL(EMP_DETAILS.CREATED_DT,TRUNC(SYSDATE)) ,
          V_DELETED_FLAG ,
          TO_CHAR(EMP_DETAILS.EMPLOYEE_ID) ,
          V_DELETED_FLAG ,
          'Saturday' ,
          EMP_DETAILS.ID_ACCOUNT_NO ,
          EMP_DETAILS.ID_LBRF ,
          0 ,
          EMP_DETAILS.ID_PAN_NO ,
          'None' ,
          EMP_DETAILS.OVERTIME_FLAG ,
          EMP_DETAILS.ID_PROVIDENT_FUND_NO ,
          '001',
          EMP_DETAILS.ID_ACC_CODE, 
          EMP_DETAILS.ID_THUMB_ID
        );

   END IF;



END LOOP;
END;
