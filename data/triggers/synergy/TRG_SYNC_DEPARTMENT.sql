create or replace TRIGGER "TRG_SYNC_DEPARTMENT" 
   AFTER INSERT OR UPDATE OR DELETE
   ON HRIS_DEPARTMENTS
   REFERENCING NEW AS NEW OLD AS OLD
   FOR EACH ROW
DECLARE

    V_DELETED_FLAG   CHAR (1 BYTE);
    V_COUNT          NUMBER;
    V_COMPANY_CODE   VARCHAR2(30 BYTE);
BEGIN
    BEGIN
      SELECT COUNT (*) INTO V_COUNT FROM HR_DEPARTMENT_CODE
       WHERE DEPARTMENT_CODE = TO_CHAR (:OLD.DEPARTMENT_ID,'FM000');
    EXCEPTION
        WHEN OTHERS THEN
            V_COUNT := 0;
    END;

    V_COMPANY_CODE := '01';

    IF :NEW.STATUS = 'E' THEN
        V_DELETED_FLAG := 'N';
    ELSE
        V_DELETED_FLAG := 'Y';
    END IF;

    IF V_COUNT = 0 THEN
        FOR I IN (SELECT COMPANY_CODE FROM COMPANY_SETUP) LOOP
            INSERT INTO HR_DEPARTMENT_CODE (DEPARTMENT_CODE,
                                      DEPARTMENT_EDESC,
                                      DEPARTMENT_NDESC,
                                      PRE_DEPARTMENT_CODE,
                                      MASTER_DEPARTMENT_CODE,
                                      GROUP_SKU_FLAG,
                                      EREMARKS,
                                      NREMARKS,
                                      COMPANY_CODE,
                                      BRANCH_CODE,
                                      CREATED_BY,
                                      CREATED_DATE,
                                      DELETED_FLAG,
                                      MODIFY_DATE,
                                      MODIFY_BY)
            VALUES (TO_CHAR (:NEW.DEPARTMENT_ID,'FM000'),
                   :NEW.DEPARTMENT_NAME,
                   :NEW.DEPARTMENT_NAME,
                   '01',
                   '01.01',
                   'I',
                   :NEW.REMARKS,
                   :NEW.REMARKS,
                   I.COMPANY_CODE,
                   I.COMPANY_CODE||'.01',
                   TO_CHAR (:NEW.CREATED_BY),
                   TRUNC (SYSDATE),
                   V_DELETED_FLAG,
                   NULL,
                   NULL);
        END LOOP;
    ELSIF V_COUNT >= 1 THEN
        UPDATE HR_DEPARTMENT_CODE SET
        DEPARTMENT_EDESC = :NEW.DEPARTMENT_NAME,
        DELETED_FLAG = V_DELETED_FLAG
        WHERE DEPARTMENT_CODE = TO_CHAR (:NEW.DEPARTMENT_ID,'FM000');
    END IF;
END;