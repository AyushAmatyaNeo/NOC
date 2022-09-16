create or replace TRIGGER "TRG_SYNC_EMPLOYEE" AFTER
  DELETE OR
  INSERT OR
  UPDATE ON HRIS_EMPLOYEES REFERENCING NEW AS NEW OLD AS OLD FOR EACH ROW DECLARE V_DELETED_FLAG CHAR(1 BYTE);
  V_GENDER                                                                                                     CHAR(1 BYTE);
  V_MARITAL_STATUS                                                                                             VARCHAR2(10 BYTE);
  V_EMPLOYEE_STATUS                                                                                            VARCHAR2(25 BYTE);
  V_COUNT                                                                                                      NUMBER;
  V_COMPANY_CODE                                                                                               VARCHAR2(30 BYTE);
  V_SUB_CODE                                                                                                   NUMBER;
  V_EMPSUB_CODE VARCHAR2(50 BYTE);
--new added
  V_EMP_BLOOD_GROUP VARCHAR2(50 BYTE):='';
  BEGIN
    BEGIN
      SELECT COUNT (*)
      INTO V_COUNT
      FROM HR_EMPLOYEE_SETUP
      WHERE TRIM(EMPLOYEE_CODE) = TO_CHAR (:NEW.EMPLOYEE_ID);
    EXCEPTION
    WHEN OTHERS THEN
      V_COUNT := 0;
    END;
    BEGIN
      SELECT COMPANY_CODE
      INTO V_COMPANY_CODE
      FROM HRIS_COMPANY
      WHERE COMPANY_ID = :NEW.COMPANY_ID;
    EXCEPTION
    WHEN OTHERS THEN
      V_COMPANY_CODE := TO_CHAR ('0'||:NEW.COMPANY_ID);
    END;
    IF :NEW.STATUS    = 'E' THEN
      V_DELETED_FLAG := 'N';
    ELSE
      V_DELETED_FLAG := 'Y';
    END IF;
    IF :NEW.GENDER_ID    = 1 THEN
      V_GENDER          := 'M';
    ELSIF :NEW.GENDER_ID = 2 THEN
      V_GENDER          := 'F';
    ELSE
      V_GENDER := 'O';
    END IF;
    IF :NEW.MARITAL_STATUS = 'M' THEN
      V_MARITAL_STATUS    := 'Married';
    ELSE
      V_MARITAL_STATUS := 'Unmarried';
    END IF;

    V_EMPLOYEE_STATUS :=:NEW.EMPLOYEE_STATUS;


IF :NEW.RESIGNED_FLAG = 'Y' THEN
      V_EMPLOYEE_STATUS := 'Resigned';
    END IF;


    IF :NEW.RETIRED_FLAG = 'Y' THEN
      V_EMPLOYEE_STATUS := 'Retired';
    END IF;





IF :NEW.BLOOD_GROUP_ID IS NOT NULL THEN
      SELECT BLOOD_GROUP_CODE  INTO V_EMP_BLOOD_GROUP
FROM HRIS_BLOOD_GROUPS 
WHERE BLOOD_GROUP_ID=:NEW.BLOOD_GROUP_ID;
END IF;


    IF V_COUNT = 0 THEN
      INSERT
      INTO HR_EMPLOYEE_SETUP
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
          DEPOSIT_ACCOUNT
-- new added fields
,THUMB_ID
,BIRTH_DATE
,BLOOD_GROUP
,PERSONAL_EMAIL
,CITIZENSHIP_NO
,CTZ_ISSUED_DATE
,LICENSE_NO
,EFATHER_NAME
,ETEMPORARY_ADDRESS1
,PASSPORT_NO
,PASS_EXPIRY_DATE
,PERMANENT_DATE
,BAR_CODE
,RESIGNED_DATE
,REASON
,RF_NUMBER
,ACC_CODE

        )
        VALUES
        (
          TO_CHAR(:NEW.EMPLOYEE_ID) ,
          REPLACE(CONCAT(CONCAT(:NEW.FIRST_NAME
          ||' ',:NEW.MIDDLE_NAME
          ||' '),:NEW.LAST_NAME),'  ',' ') ,
          NVL(:NEW.NAME_NEPALI, CONCAT(CONCAT(:NEW.FIRST_NAME
          ||' ',:NEW.MIDDLE_NAME
          ||' '), :NEW.LAST_NAME)) ,
          'I' ,
          '01.00' ,
          '01' ,
          :NEW.ADDR_PERM_STREET_ADDRESS ,
          TO_CHAR(:NEW.COUNTRY_ID) ,
          :NEW.TELEPHONE_NO ,
          :NEW.MOBILE_NO ,
          :NEW.EMAIL_OFFICIAL ,
          V_GENDER ,
          V_MARITAL_STATUS ,
          :NEW.JOIN_DATE ,
          'E'
          ||TO_CHAR(:NEW.EMPLOYEE_ID) ,
          TO_CHAR(:NEW.SERVICE_TYPE_ID,'FM00') ,
          '01' ,
          TO_CHAR(:NEW.DEPARTMENT_ID,'FM000') ,
          TO_CHAR(:NEW.DESIGNATION_ID,'FM000') ,
          TO_CHAR(:NEW.POSITION_ID,'FM000') ,
          :NEW.SALARY ,
          'Working' ,
          'Monthly' ,
          V_COMPANY_CODE ,
          V_COMPANY_CODE
          ||'.01' ,
          'SYNC' ,
          NVL(:NEW.CREATED_DT,TRUNC(SYSDATE)) ,
          V_DELETED_FLAG ,
          TO_CHAR(:NEW.EMPLOYEE_ID) ,
          V_DELETED_FLAG ,
          'Saturday' ,
          :NEW.ID_ACCOUNT_NO ,
          :NEW.ID_RETIREMENT_NO ,
          0 ,
          :NEW.ID_PAN_NO ,
          'None' ,
          :NEW.OVERTIME_FLAG ,
          :NEW.ID_PROVIDENT_FUND_NO ,
          TO_CHAR(:NEW.BRANCH_ID,'FM000'),
          :NEW.ID_ACC_CODE
--new added fields
,:NEW.ID_THUMB_ID
,:NEW.BIRTH_DATE
,V_EMP_BLOOD_GROUP
,:NEW.EMAIL_PERSONAL
,:NEW.ID_CITIZENSHIP_NO
,:NEW.ID_CITIZENSHIP_ISSUE_DATE
,:NEW.ID_DRIVING_LICENCE_NO
,:NEW.FAM_FATHER_NAME
,:NEW.ADDR_TEMP_STREET_ADDRESS
,:NEW.ID_PASSPORT_NO
,:NEW.ID_PASSPORT_EXPIRY
,:NEW.PERMANENT_DATE
,:NEW.ID_BAR_CODE
,:NEW.RESIGNED_DATE
,:NEW.REMARKS
,:NEW.ID_LBRF
,'1007'
        );
      V_EMPSUB_CODE := 'E'||:NEW.EMPLOYEE_ID;
      SELECT COUNT(*)
      INTO V_SUB_CODE
      FROM FA_SUB_LEDGER_SETUP
      WHERE TRIM(SUB_CODE) =TRIM( V_EMPSUB_CODE);
      IF V_SUB_CODE        = 0 THEN
        BEGIN
          INSERT
          INTO FA_SUB_LEDGER_SETUP
            (
              SUB_CODE,
              SUB_EDESC,
              SUB_NDESC,
              SUB_LEDGER_FLAG,
              COMPANY_CODE,
              CREATED_BY,
              CREATED_DATE,
              DELETED_FLAG
            )
            VALUES
            (
              'E'
              ||:NEW.EMPLOYEE_ID,
              NVL(:NEW.NAME_NEPALI, REPLACE(CONCAT(CONCAT(:NEW.FIRST_NAME
              ||' ',:NEW.MIDDLE_NAME
              ||' '),:NEW.LAST_NAME),'  ',' ')),
              NVL(:NEW.NAME_NEPALI, CONCAT(CONCAT(:NEW.FIRST_NAME
              ||' ',:NEW.MIDDLE_NAME
              ||' '),:NEW.LAST_NAME)),
              'E',
              V_COMPANY_CODE,
              'SYSTEM',
              SYSDATE,
              'N'
            );
        EXCEPTION
        WHEN OTHERS THEN
          NULL;
        END;
      ELSE
        BEGIN
          UPDATE FA_SUB_LEDGER_SETUP
          SET SUB_EDESC = REPLACE(CONCAT(CONCAT(:NEW.FIRST_NAME
            ||' ',:NEW.MIDDLE_NAME
            ||' '),:NEW.LAST_NAME),'  ',' ')
          WHERE TRIM(SUB_CODE)= 'E'
            ||:NEW.EMPLOYEE_ID;
        EXCEPTION
        WHEN OTHERS THEN
          NULL;
        END;
      END IF;
    ELSIF V_COUNT = 1 THEN
      BEGIN
        UPDATE HR_EMPLOYEE_SETUP
        SET EMPLOYEE_EDESC = REPLACE(CONCAT(CONCAT(:NEW.FIRST_NAME
          ||' ',:NEW.MIDDLE_NAME
          ||' '),:NEW.LAST_NAME),'  ',' ') ,
          EMPLOYEE_NDESC = NVL(:NEW.NAME_NEPALI, CONCAT(CONCAT(:NEW.FIRST_NAME
          ||' ',:NEW.MIDDLE_NAME
          ||' '),:NEW.LAST_NAME)) ,
          EPERMANENT_ADDRESS1 = :NEW.ADDR_PERM_STREET_ADDRESS ,
          EPERMANENT_COUNTRY  = TO_CHAR(:NEW.COUNTRY_ID) ,
          PHONE               = :NEW.TELEPHONE_NO ,
          MOBILE              = :NEW.MOBILE_NO ,
          EMAIL               = :NEW.EMAIL_OFFICIAL ,
          SEX                 = V_GENDER ,
          MARITAL_STATUS      = V_MARITAL_STATUS ,
          JOIN_DATE           = :NEW.JOIN_DATE ,
          LINK_SUB_CODE       = 'E'
          ||TO_CHAR(:NEW.EMPLOYEE_ID) ,
          EMPLOYEE_TYPE_CODE   = TO_CHAR(:NEW.SERVICE_TYPE_ID,'FM00') ,
          PERIOD_CODE          = '01' ,
          CUR_DEPARTMENT_CODE  = TO_CHAR(:NEW.DEPARTMENT_ID,'FM000') ,
          CUR_DESIGNATION_CODE = TO_CHAR(:NEW.DESIGNATION_ID,'FM000') ,
          CUR_GRADE_CODE       = TO_CHAR(:NEW.POSITION_ID,'FM000') ,
          CUR_BASIC_SALARY     = :NEW.SALARY ,
          EMPLOYEE_STATUS      = V_EMPLOYEE_STATUS ,
          COMPANY_CODE         = V_COMPANY_CODE ,
          BRANCH_CODE          = V_COMPANY_CODE
          ||'.01' ,
          LOCK_FLAG               = V_DELETED_FLAG ,
          ACCOUNT_NO              = :NEW.ID_ACCOUNT_NO ,
          CIT_NUMBER              = :NEW.ID_RETIREMENT_NO ,
          PAN_NO                  = :NEW.ID_PAN_NO ,
          THUMB_ID                = :NEW.ID_THUMB_ID ,
          PF_NUMBER               = :NEW.ID_PROVIDENT_FUND_NO ,
          OVERTIME_APPLICABLE     = :NEW.OVERTIME_FLAG,
          DEPOSIT_ACCOUNT         = :NEW.ID_ACC_CODE,
          SAL_SHEET_CODE=TO_CHAR(:NEW.BRANCH_ID,'FM000')
-- new update queries
,BIRTH_DATE=:NEW.BIRTH_DATE
,BLOOD_GROUP=V_EMP_BLOOD_GROUP
,PERSONAL_EMAIL=:NEW.EMAIL_PERSONAL
,CITIZENSHIP_NO=:NEW.ID_CITIZENSHIP_NO
,CTZ_ISSUED_DATE=:NEW.ID_CITIZENSHIP_ISSUE_DATE
,LICENSE_NO=:NEW.ID_DRIVING_LICENCE_NO
,EFATHER_NAME=:NEW.FAM_FATHER_NAME
,ETEMPORARY_ADDRESS1=:NEW.ADDR_TEMP_STREET_ADDRESS
,PASSPORT_NO=:NEW.ID_PASSPORT_NO
,PASS_EXPIRY_DATE=:NEW.ID_PASSPORT_EXPIRY
,PERMANENT_DATE=:NEW.PERMANENT_DATE
,BAR_CODE=:NEW.ID_BAR_CODE
,RESIGNED_DATE=:NEW.RESIGNED_DATE
,REASON=:NEW.REMARKS
,RF_NUMBER=:NEW.ID_LBRF 
        WHERE TRIM(EMPLOYEE_CODE) = TO_CHAR(:OLD.EMPLOYEE_ID);
        BEGIN
          UPDATE FA_SUB_LEDGER_SETUP
          SET SUB_EDESC = CONCAT(CONCAT(:NEW.FIRST_NAME
            ||' ',:NEW.MIDDLE_NAME
            ||' '),:NEW.LAST_NAME),
            SUB_NDESC = NVL(:NEW.NAME_NEPALI, CONCAT(CONCAT(:NEW.FIRST_NAME
            ||' ',:NEW.MIDDLE_NAME
            ||' '),:NEW.LAST_NAME))
          WHERE TRIM(SUB_CODE) = 'E'
            ||:OLD.EMPLOYEE_ID;
        EXCEPTION
        WHEN OTHERS THEN
          NULL;
        END;
      END;
      
      --to update positionWise automatic  rule assign
      
      IF(:OLD.POSITION_ID!=:NEW.POSITION_ID OR :OLD.POSITION_ID IS NULL OR :NEW.POSITION_ID IS NULL)
      THEN
      BEGIN
      
      delete from HR_EMPLOYEE_FACILITY 
        where employee_code=TO_CHAR(:NEW.EMPLOYEE_ID) 
        and company_code=V_COMPANY_CODE 
        and branch_code=V_COMPANY_CODE
          ||'.01';
          
        INSERT INTO HR_EMPLOYEE_FACILITY 
( 
employee_code,
pay_code,
effective_date,
pay_type_flag,
taxation_flag,
earning_basis_on, 
tds_flag,
priority_index,
ref_pay_code,
calc_percent_value_flag,
calc_percent_value,
eremarks,nremarks, 
company_code,
branch_code,
created_by,
created_date,
deleted_flag,
EFFECTIVE_DATE_TO
)
SELECT 
TO_CHAR(:NEW.EMPLOYEE_ID),
A.PAY_CODE,
B.EFFECTIVE_DATE_FROM,
B.PAY_TYPE_FLAG,
B.TAXATION_FLAG,
B.EARNING_BASIS_ON,
B.TDS_FLAG,
A.PRIORITY_INDEX,
A.REF_PAY_CODE,
A.CALC_PERCENT_VALUE_FLAG,
 A.CALC_PERCENT_VALUE,
'',
'',
V_COMPANY_CODE,
V_COMPANY_CODE||'.01',
'ADMIN',
TRUNC(SYSDATE),
'N',
B.EFFECTIVE_DATE_TO
FROM HR_PAY_GRADE_SETUP A,
HR_PAY_SETUP B 
WHERE a.company_code=V_COMPANY_CODE And a.branch_code=V_COMPANY_CODE||'.01'
And a.deleted_flag='N' 
And a.pay_code=b.pay_code 
And a.company_code=b.company_code 
And a.branch_code=b.branch_code 
and B.REF_RULE_FLAG = 'N'  
And a.grade_code=:NEW.POSITION_ID 
order by a.priority_index asc;

--TO INSERT INTO hr_employee_facility_detail

delete from hr_employee_facility_detail 
where employee_code=TO_CHAR(:NEW.EMPLOYEE_ID) 
and company_code=V_COMPANY_CODE
and branch_code=V_COMPANY_CODE||'.01';


FOR FACILITY_LIST IN (select * from HR_EMPLOYEE_FACILITY where employee_code='1001319')
LOOP

insert into hr_employee_facility_detail 
(employee_code,
pay_code,
sr_no,
mnemonic_name,
mnemonic_type,
company_code,
branch_code,
created_by, 
created_date,
deleted_flag
)
select
TO_CHAR(:NEW.EMPLOYEE_ID),
FACILITY_LIST.PAY_CODE,
sr_no,
mnemonic_name,
mnemonic_type,
V_COMPANY_CODE,
V_COMPANY_CODE||'.01',
'ADMIN',
TRUNC(SYSDATE),
'N'
from hr_pay_grade_detail_setup where 
pay_code=FACILITY_LIST.PAY_CODE and grade_code=:NEW.POSITION_ID 
and company_code=V_COMPANY_CODE
and branch_code=V_COMPANY_CODE||'.01' and deleted_flag='N';

END LOOP;





      END;
      END IF;
      
      
      
      
      
      
    END IF;
  END;