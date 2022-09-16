create or replace FUNCTION FN_DECRYPT_PASSWORD(vText IN VARCHAR2)
RETURN VARCHAR2 IS
  nPasswordLength NUMBER;
  nIncrement NUMBER;
  vBodyText VARCHAR2(100);
  vFoundText VARCHAR2(100);
  vCharString CHAR(1);
  vPassword VARCHAR2(100);
  nPosition NUMBER;
  nStartPosition NUMBER;
BEGIN
 IF vText IS NOT NULL AND LENGTH(vText)=64 THEN
    nPasswordLength := TO_NUMBER(SUBSTR(vText, (LENGTH(vText)+1)-4,2));
    nIncrement := TO_NUMBER(SUBSTR(vText, (LENGTH(vText)+1)-2,2));
    nStartPosition := LENGTH(vText)-NVL(nPasswordLength,0)*3-3;
    vBodyText := SUBSTR(vText,nStartPosition,nPasswordLength * 3);

    nPosition := 1;
    vFoundText :='';

    FOR i IN 1..LENGTH(vBodyText/3) LOOP
      vCharString := CHR(TO_NUMBER(SUBSTR(vBodyText,nPosition,3))-567-nIncrement);
      vFoundText := vFoundText||vCharString;
      nPosition := nPosition + 3;
    END LOOP;


    vPassword := '';
    FOR i IN 0..LENGTH(vFoundText)-1 LOOP
       vPassword := vPassword||SUBSTR(vFoundText, LENGTH(vFoundText)-i,1);
    END LOOP;
    RETURN vPassword;
  ELSE
    RETURN NULL;
  END IF;
END;