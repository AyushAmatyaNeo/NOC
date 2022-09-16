create or replace PROCEDURE HRIS_EMPLOYEE_SETUP_PROC(
    P_EMPLOYEE_ID HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE)
AS
  V_JOIN_DATE HRIS_EMPLOYEES.JOIN_DATE%TYPE;
  V_FISCAL_YEAR_ID HRIS_FISCAL_YEARS.FISCAL_YEAR_ID%TYPE;
  V_MONTH_ID HRIS_MONTH_CODE.MONTH_ID%TYPE;
  V_FISCAL_YEAR_MONTH_NO NUMBER;
  V_IS_EMP_IN            CHAR(1 BYTE);
  V_PRODATA_DAYS         NUMBER;
  V_COUNT                NUMBER;
  V_CUR_FIS_YR_ID HRIS_FISCAL_YEARS.FISCAL_YEAR_ID%TYPE;
  V_CUR_FIS_YR_START_DATE DATE;
  V_MONTH_COUNT NUMBER:=1;
BEGIN
  SELECT LEAVE_YEAR_ID,
    TRUNC(START_DATE)
  INTO V_CUR_FIS_YR_ID,
    V_CUR_FIS_YR_START_DATE
  FROM HRIS_LEAVE_YEARS
  WHERE TRUNC(SYSDATE) BETWEEN START_DATE AND END_DATE;
  --
  BEGIN
    SELECT TRUNC(JOIN_DATE)
    INTO V_JOIN_DATE
    FROM HRIS_EMPLOYEES
    WHERE EMPLOYEE_ID = P_EMPLOYEE_ID;
  EXCEPTION
  WHEN NO_DATA_FOUND THEN
    SYS.DBMS_OUTPUT.PUT_LINE('NO JOIN_DATE SET FROM THE EMPLOYEE WITH EMPLOYEE_ID : '||P_EMPLOYEE_ID);
    RETURN;
  END;
  BEGIN
    SELECT LEAVE_YEAR_ID,
      MONTH_ID,
      LEAVE_YEAR_MONTH_NO
    INTO V_FISCAL_YEAR_ID,
      V_MONTH_ID,
      V_FISCAL_YEAR_MONTH_NO
    FROM HRIS_LEAVE_MONTH_CODE
    WHERE (
      CASE
        WHEN V_JOIN_DATE>V_CUR_FIS_YR_START_DATE
        THEN V_JOIN_DATE
        ELSE V_CUR_FIS_YR_START_DATE
      END ) BETWEEN FROM_DATE AND TO_DATE;
  EXCEPTION
  WHEN no_data_found THEN
    SYS.DBMS_OUTPUT.PUT('No Current Month found.');
    RETURN;
  END;
  BEGIN
    FOR leave IN
    (SELECT LEAVE_ID,CARRY_FORWARD,
      DEFAULT_DAYS,
      IS_PRODATA_BASIS,
      IS_MONTHLY
    FROM HRIS_LEAVE_MASTER_SETUP
    WHERE STATUS                 ='E'
    AND ASSIGN_ON_EMPLOYEE_SETUP ='Y'
    )
    LOOP
      V_IS_EMP_IN    := HRIS_IS_EMP_IN(P_EMPLOYEE_ID,'HRIS_LEAVE_MASTER_SETUP','LEAVE_ID',leave.LEAVE_ID);
      IF V_IS_EMP_IN !='Y' THEN
        CONTINUE;
      END IF;
      IF (leave.IS_MONTHLY ='Y') THEN

      -- IF MONTHLY CARRY FORWARD IS NO 
      IF(leave.CARRY_FORWARD ='N')
      THEN
      DBMS_OUTPUT.PUT_LINE('FISCAL_YEAR_NO:'||V_FISCAL_YEAR_MONTH_NO);
      FOR i IN V_FISCAL_YEAR_MONTH_NO..12
        LOOP
          SELECT COUNT(*)
          INTO V_COUNT
          FROM HRIS_EMPLOYEE_LEAVE_ASSIGN
          WHERE EMPLOYEE_ID       =P_EMPLOYEE_ID
          AND LEAVE_ID            = leave.LEAVE_ID
          AND FISCAL_YEAR_MONTH_NO=i ;
          IF ( V_COUNT            =0 )THEN
            INSERT
            INTO HRIS_EMPLOYEE_LEAVE_ASSIGN
              (
                EMPLOYEE_ID,
                LEAVE_ID,
                PREVIOUS_YEAR_BAL,
                TOTAL_DAYS,
                BALANCE,
                --FISCAL_YEAR,
                FISCAL_YEAR_MONTH_NO,
                CREATED_DT
              )
              VALUES
              (
                P_EMPLOYEE_ID,
                leave.LEAVE_ID,
                0,
                leave.DEFAULT_DAYS,
                leave.DEFAULT_DAYS,
               -- V_FISCAL_YEAR_ID,
                i,
                TRUNC(SYSDATE)
              );
          END IF;
        END LOOP;


      END IF;

      -- IF MONTHLY CARRY FORWARD IS YES
      IF(leave.CARRY_FORWARD ='Y')
      THEN
      V_MONTH_COUNT:=1;
      
      SELECT COUNT(*)
          INTO V_COUNT
          FROM HRIS_EMPLOYEE_LEAVE_ASSIGN
          WHERE EMPLOYEE_ID       =P_EMPLOYEE_ID
          AND LEAVE_ID            = leave.LEAVE_ID;
          
        IF(V_COUNT=0) THEN
       FOR i IN V_FISCAL_YEAR_MONTH_NO..12
            LOOP

                INSERT
                INTO HRIS_EMPLOYEE_LEAVE_ASSIGN
                  (
                    EMPLOYEE_ID,
                    LEAVE_ID,
                    PREVIOUS_YEAR_BAL,
                    TOTAL_DAYS,
                    BALANCE,
                   -- FISCAL_YEAR,
                    FISCAL_YEAR_MONTH_NO,
                    CREATED_DT
                  )
                  VALUES
                  (
                    P_EMPLOYEE_ID,
                     leave.LEAVE_ID,
                    0,
                    leave.DEFAULT_DAYS*V_MONTH_COUNT,
                    leave.DEFAULT_DAYS*V_MONTH_COUNT,
                  --  V_FISCAL_YEAR_ID,
                    i,
                    TRUNC(SYSDATE)
                  );

                  V_MONTH_COUNT:=V_MONTH_COUNT+1;
            END LOOP;
            
             END IF;


      END IF;



        CONTINUE;
      END IF;
      V_PRODATA_DAYS           := leave.DEFAULT_DAYS;
      IF leave.IS_PRODATA_BASIS = 'Y' THEN
        V_PRODATA_DAYS         :=ROUND(leave.DEFAULT_DAYS*((13-V_FISCAL_YEAR_MONTH_NO)/12));
      END IF;
      SELECT COUNT(*)
      INTO V_COUNT
      FROM HRIS_EMPLOYEE_LEAVE_ASSIGN
      WHERE EMPLOYEE_ID =P_EMPLOYEE_ID
      AND LEAVE_ID      = leave.LEAVE_ID;
      IF ( V_COUNT      =0 )THEN
        INSERT
        INTO HRIS_EMPLOYEE_LEAVE_ASSIGN
          (
            EMPLOYEE_ID,
            LEAVE_ID,
            PREVIOUS_YEAR_BAL,
            TOTAL_DAYS,
            BALANCE,
            FISCAL_YEAR,
            CREATED_DT
          )
          VALUES
          (
            P_EMPLOYEE_ID,
            leave.LEAVE_ID,
            0,
            V_PRODATA_DAYS,
            V_PRODATA_DAYS,
            V_FISCAL_YEAR_ID,
            TRUNC(SYSDATE)
          );
      END IF;
    END LOOP;
  END;
  BEGIN
    FOR holiday IN
    (SELECT HOLIDAY_ID
      FROM HRIS_HOLIDAY_MASTER_SETUP
      WHERE ASSIGN_ON_EMPLOYEE_SETUP = 'Y'
      AND STATUS                     ='E'
      AND START_DATE                >=V_JOIN_DATE
    )
    LOOP
      HRIS_HOLIDAY_ASSIGN_AUTO
      (
        holiday.HOLIDAY_ID,P_EMPLOYEE_ID
      )
      ;
    END LOOP;
  END;
  --
  BEGIN
    FOR news IN
    (SELECT NEWS_ID FROM HRIS_NEWS WHERE STATUS ='E'
    )
    LOOP
      HRIS_NEWS_TO_PROC
      (
        news.NEWS_ID,P_EMPLOYEE_ID
      )
      ;
    END LOOP;
  END;
END;