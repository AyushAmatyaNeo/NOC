create or replace TRIGGER "HRIS_EMPLOYEE_ADD" AFTER
  INSERT ON HRIS_EMPLOYEES FOR EACH ROW DECLARE V_FISCAL_YEAR_ID HRIS_FISCAL_YEARS.FISCAL_YEAR_ID%TYPE;
  V_MONTH_ID HRIS_MONTH_CODE.MONTH_ID%TYPE;
  V_CURRENT_MONTH_COUNT NUMBER;
  V_DEFAULT_DAYS        NUMBER;
  V_PRODATA_DAYS        NUMBER;
  BEGIN
    BEGIN
      SELECT LEAVE_YEAR_ID,
        MONTH_ID
      INTO V_FISCAL_YEAR_ID,
        V_MONTH_ID
      FROM HRIS_LEAVE_MONTH_CODE
      WHERE TRUNC(SYSDATE) BETWEEN FROM_DATE AND TO_DATE;
    EXCEPTION
    WHEN no_data_found THEN
      SYS.DBMS_OUTPUT.PUT('No Current Month found.');
      RETURN;
    END;
    SELECT MONTH_ROWNUM
    INTO V_CURRENT_MONTH_COUNT
    FROM
      (SELECT ROWNUM AS MONTH_ROWNUM,
        MONTH_ID
      FROM HRIS_LEAVE_MONTH_CODE
      WHERE LEAVE_YEAR_ID= V_FISCAL_YEAR_ID
      ORDER BY FROM_DATE
      ) MONTHS
    WHERE MONTH_ID =V_MONTH_ID;
    BEGIN
      FOR leave IN
      (SELECT LEAVE_ID,
        DEFAULT_DAYS,
        IS_PRODATA_BASIS
      FROM HRIS_LEAVE_MASTER_SETUP
      WHERE STATUS                 ='E'
      AND ASSIGN_ON_EMPLOYEE_SETUP ='Y'
      AND  IS_MONTHLY='N'
      )
      LOOP
        V_DEFAULT_DAYS           := leave.DEFAULT_DAYS;
        V_PRODATA_DAYS           := leave.DEFAULT_DAYS;
        IF leave.IS_PRODATA_BASIS = 'Y' THEN
          V_PRODATA_DAYS         :=ROUND(V_DEFAULT_DAYS*((13-V_CURRENT_MONTH_COUNT)/12));
        END IF;
        INSERT
        INTO HRIS_EMPLOYEE_LEAVE_ASSIGN
          (
            EMPLOYEE_ID,
            LEAVE_ID,
            PREVIOUS_YEAR_BAL,
            TOTAL_DAYS,
            BALANCE,
           -- FISCAL_YEAR,
            CREATED_DT
          )
          VALUES
          (
            :new.EMPLOYEE_ID,
            leave.LEAVE_ID,
            0,
            V_PRODATA_DAYS,
            V_PRODATA_DAYS,
          --  V_FISCAL_YEAR_ID,
            TRUNC(SYSDATE)
          );
      END LOOP;
    END;
    BEGIN
      FOR holiday IN
      (SELECT HOLIDAY_ID
        FROM HRIS_HOLIDAY_MASTER_SETUP
        WHERE ASSIGN_ON_EMPLOYEE_SETUP = 'Y'
        AND STATUS                     ='E'
        AND START_DATE                >=TRUNC(SYSDATE)
      )
      LOOP
        INSERT
        INTO HRIS_EMPLOYEE_HOLIDAY
          (
            EMPLOYEE_ID,
            HOLIDAY_ID
          )
          VALUES
          (
            :new.EMPLOYEE_ID,
            holiday.HOLIDAY_ID
          );
      END LOOP;
    END;
  END;

