create or replace FUNCTION FN_ENCRYPT_PASSWORD(vText IN VARCHAR2)
RETURN VARCHAR2 IS
  vFalsePart VARCHAR2(100);
  vEndPart VARCHAR2(10);
  vRetValue VARCHAR2(100);
  vMainBody VARCHAR2(100):='';
  vRevertedText VARCHAR2(100):='';
  vASCIIValue VARCHAR2(3);
  nDiffRange NUMBER(2);
  vAddParts    VARCHAR2(100);
  vTemporaryPassword VARCHAR2(30);
BEGIN
  --Taking time factor
  IF LENGTH(vText)< 10 THEN
     vEndPart := '0'||TO_CHAR(LENGTH(vText))||TO_CHAR(SYSDATE, 'SS');
  ELSIF LENGTH(vText)>= 10 THEN
     vEndPart := TO_CHAR(LENGTH(vText))||TO_CHAR(SYSDATE, 'SS');
  END IF;

  --Temporary part goes here
  vTemporaryPassword := 'MAHESWORMAHARJANISHERO';
  vFalsePart :='';
  FOR i IN 1..LENGTH(vTemporaryPassword) LOOP
      vFalsePart := vFalsePart||TO_CHAR(TO_NUMBER(ASCII(SUBSTR(vTemporaryPassword,i,1)))+ TO_NUMBER(vEndPart));
  END LOOP;
  --Main part goes here

  FOR i IN 0..LENGTH(vText)-1 LOOP
     vRevertedText := vRevertedText||SUBSTR(vText, LENGTH(vText)-i,1);
  END LOOP;

  FOR i IN 1..LENGTH(vRevertedText) LOOP
    vASCIIValue :=  TO_CHAR(TO_NUMBER(ASCII(SUBSTR(vRevertedText,i,1)))+ 567 + TO_NUMBER(TO_CHAR(SYSDATE, 'SS')));
    vMainBody := vMainBody||vASCIIValue;
  END LOOP;

  vRetValue := vMainBody||vEndPart;
  IF LENGTH(vRetValue)< 64 THEN
     nDiffRange := 64-LENGTH(vRetValue);
  END IF;

  vRetValue := SUBSTR(vFalsePart,1,nDiffRange)||vRetValue;
  RETURN vRetValue;
END;