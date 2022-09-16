create or replace TRIGGER "TRG_SYNC_BRANCH" 
   AFTER INSERT OR UPDATE OR DELETE
   ON HRIS_BRANCHES
   REFERENCING NEW AS NEW OLD AS OLD
   FOR EACH ROW
DECLARE
   V_DELETED_FLAG   CHAR (1 BYTE);
   V_COUNT          NUMBER;
   V_COMPANY_CODE   VARCHAR2(30 BYTE);
   V_BRANCH_CODE   VARCHAR2(30 BYTE);
BEGIN
	BEGIN
		SELECT COUNT (*) INTO V_COUNT FROM HR_SAL_SHEET_SETUP
		WHERE SAL_SHEET_CODE = TO_CHAR (:OLD.BRANCH_ID,'FM000');
	EXCEPTION
		WHEN OTHERS THEN
			V_COUNT := 0;
	END;
	V_COMPANY_CODE := '01';
	V_BRANCH_CODE := '01.01';


	IF :NEW.STATUS = 'E' THEN
		V_DELETED_FLAG := 'N';
	ELSE
		V_DELETED_FLAG := 'Y';
	END IF;

	IF V_COUNT = 0 THEN
		FOR I IN (SELECT COMPANY_CODE FROM COMPANY_SETUP) LOOP
			INSERT INTO HR_SAL_SHEET_SETUP (
                                        SAL_SHEET_CODE,
                                        SAL_SHEET_EDESC,
                                        SAL_SHEET_NDESC,
                                        COMPANY_CODE,
                                        BRANCH_CODE,
                                        CREATED_BY,
                                        CREATED_DATE,
                                        DELETED_FLAG,
                                        SYN_ROWID,
                                        MODIFY_DATE,
                                        VOUCHER_BRANCH_CODE,
                                        RULE_VALUE,
                                        STATUS_FLAG,
                                        VERIFY_BY,
                                        VERIFY_DATE,
                                        MODIFY_BY,
                                        COMMOM_ACC_CODE)
			VALUES (
                    TO_CHAR (:NEW.BRANCH_ID,'FM000'),
                   :NEW.BRANCH_NAME,
                   :NEW.BRANCH_NAME,
                   V_COMPANY_CODE,
                   V_BRANCH_CODE,
                    'ADMIN',
                   TRUNC (SYSDATE),
                   V_DELETED_FLAG,
                   NULL,
                   NULL,
                   NULL,
                   0,
                   NULL,
                   NULL,
                   NULL,
                   NULL,
                   NULL
                    );
		END LOOP;
	ELSIF V_COUNT >= 1 THEN
		UPDATE HR_SAL_SHEET_SETUP SET
		SAL_SHEET_EDESC = :NEW.BRANCH_NAME,
		DELETED_FLAG = V_DELETED_FLAG
		WHERE SAL_SHEET_CODE = TO_CHAR (:OLD.BRANCH_ID,'FM000');
	END IF;
END;