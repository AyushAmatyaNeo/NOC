BEGIN
  FOR cur_rec IN
  (SELECT object_name,
    object_type
  FROM user_objects
  WHERE object_type IN ( 'VIEW','TABLE','FUNCTION','PROCEDURE','TRIGGER','CONSTRAINT')
  )
  LOOP
    BEGIN
      IF cur_rec.object_type = 'TABLE' THEN
        EXECUTE IMMEDIATE 'DROP ' || cur_rec.object_type || ' "' || cur_rec.object_name || '" CASCADE CONSTRAINTS';
      ELSE
        EXECUTE IMMEDIATE 'DROP ' || cur_rec.object_type || ' "' || cur_rec.object_name || '"';
      END IF;
    EXCEPTION
    WHEN OTHERS THEN
      DBMS_OUTPUT.put_line ( 'FAILED: DROP ' || cur_rec.object_type || ' "' || cur_rec.object_name || '"' );
    END;
  END LOOP;
END;


-- select 'drop '||object_type||' '|| object_name||  DECODE(OBJECT_TYPE,'TABLE',' CASCADE CONSTRAINTS;',';') from user_objects;