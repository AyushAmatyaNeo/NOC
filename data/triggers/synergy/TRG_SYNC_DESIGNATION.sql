create or replace TRIGGER "TRG_SYNC_DESIGNATION" 
   AFTER INSERT OR UPDATE OR DELETE
   ON HRIS_DESIGNATIONS
   REFERENCING NEW AS NEW OLD AS OLD
   FOR EACH ROW
DECLARE
   V_DELETED_FLAG   CHAR (1 BYTE);
   V_COUNT          NUMBER;
   V_COMPANY_CODE   VARCHAR2(30 BYTE);
BEGIN
	BEGIN
		SELECT COUNT (*) INTO V_COUNT FROM HR_DESIGNATION_CODE
		WHERE DESIGNATION_CODE = TO_CHAR (:OLD.DESIGNATION_ID,'FM000');
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
			INSERT INTO HR_DESIGNATION_CODE (DESIGNATION_CODE,
                                       DESIGNATION_EDESC,
                                       DESIGNATION_NDESC,
                                       PRE_DESIGNATION_CODE,
                                       DESIGNATION_LEVEL,
                                       LEVEL_CODE,
                                       EREMARKS,
                                       NREMARKS,
                                       COMPANY_CODE,
                                       BRANCH_CODE,
                                       CREATED_BY,
                                       CREATED_DATE,
                                       DELETED_FLAG,
                                       MODIFY_DATE,
                                       MODIFY_BY)
			VALUES (TO_CHAR (:NEW.DESIGNATION_ID,'FM000'),
                   :NEW.DESIGNATION_TITLE,
                   :NEW.DESIGNATION_TITLE,
                   '000',
                   '1',
                   '1',
                   NULL,
                   NULL,
                   I.COMPANY_CODE,
                   I.COMPANY_CODE||'.01',
                   'ADMIN',
                   TRUNC (SYSDATE),
                   V_DELETED_FLAG,
                   NULL,
                   NULL);
		END LOOP;
	ELSIF V_COUNT >= 1 THEN
		UPDATE HR_DESIGNATION_CODE SET
		DESIGNATION_EDESC = :NEW.DESIGNATION_TITLE,
		DELETED_FLAG = V_DELETED_FLAG
		WHERE DESIGNATION_CODE = TO_CHAR (:OLD.DESIGNATION_ID,'FM000');
	END IF;
END;