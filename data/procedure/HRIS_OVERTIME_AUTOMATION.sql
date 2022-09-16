create or replace PROCEDURE HRIS_OVERTIME_AUTOMATION(
    V_DATE DATE)
AS
  S_OVERTIME_REQUEST VARCHAR(255 BYTE):= 'OVERTIME_REQUEST';
  V_ADMIN_ID HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE;
  V_SHIFT_ID HRIS_SHIFTS.SHIFT_ID%TYPE;
  V_START_TIME HRIS_SHIFTS.START_TIME%TYPE;
  V_END_TIME HRIS_SHIFTS.END_TIME%TYPE;
  V_LATE_IN HRIS_SHIFTS.LATE_IN%TYPE;
  V_EARLY_OUT HRIS_SHIFTS.EARLY_OUT%TYPE;
  V_TOTAL_WORKING_HR HRIS_SHIFTS.TOTAL_WORKING_HR%TYPE;
  V_ACTUAL_WORKING_HR HRIS_SHIFTS.ACTUAL_WORKING_HR%TYPE;
  V_IN_TIME HRIS_ATTENDANCE_DETAIL.IN_TIME%TYPE;
  V_OUT_TIME HRIS_ATTENDANCE_DETAIL.OUT_TIME%TYPE;
  V_PUNCH_COUNT              NUMBER;
  V_NON_WORKING_HR           NUMBER;
  V_WORKING_HR               NUMBER;
  V_ACTUAL_WORKING_HR_IN_MIN NUMBER;
  V_TOTAL_WORKING_HR_IN_MIN  NUMBER;
  V_LATE_IN_MIN              NUMBER;
  V_EARLY_OUT_IN_MIN         NUMBER;
  V_BREAK_TIME_IN_MIN        NUMBER;
  V_NON_CONSIDERED_OVERTIME  NUMBER;
  V_OVERTIME                 NUMBER;
  V_CONSTRAINT_VAL_IN_MIN    NUMBER;
  V_PREF_CONDITION           CHAR(1 BYTE);
  V_OVERTIME_ID HRIS_OVERTIME.OVERTIME_ID%TYPE;
  V_OVERTIME_IN_HR          VARCHAR2(10 BYTE);
  V_COUNTER                 NUMBER:=1;
  V_PRE_OVERTIME            VARCHAR2(10 BYTE);
  V_PRE_OVERTIME_MIN        NUMBER;
  V_POST_OVERTIME           VARCHAR2(10 BYTE);
  V_POST_OVERTIME_MIN       NUMBER;
  V_OVERTIME_DETAIL_ID      NUMBER;
  V_EMP_OVERTIME_DATA_COUNT NUMBER;
BEGIN
  BEGIN
    SELECT EMPLOYEE_ID
    INTO V_ADMIN_ID
    FROM HRIS_EMPLOYEES
    WHERE IS_ADMIN  ='Y'
    AND STATUS      ='E'
    AND RETIRED_FLAG='N'
    AND ROWNUM      <2 ;
  EXCEPTION
  WHEN NO_DATA_FOUND THEN
    RAISE_APPLICATION_ERROR(-20001, 'NO EMPLOYEE IS DEFINED AS ADMIN');
  END;
  DECLARE
    V_PREF_COUNTER           NUMBER:=0;
    NO_PREFERENCE_DATA_FOUND EXCEPTION;
  BEGIN
    FOR CUR_PREF IN
    (SELECT       *
    FROM HRIS_PREFERENCE_SETUP
    WHERE PREFERENCE_NAME=S_OVERTIME_REQUEST
    AND STATUS           ='E'
    )
    LOOP
      DECLARE
        V_EMPLOYEE_COUNTER     NUMBER:=0;
        NO_EMPLOYEE_DATA_FOUND EXCEPTION;
      BEGIN
        FOR CUR_EMP IN
        (SELECT E.EMPLOYEE_ID AS EMPLOYEE_ID,
          S.START_TIME        AS START_TIME,
          S.END_TIME          AS END_TIME,
          S.LATE_IN           AS LATE_IN ,
          S.EARLY_OUT         AS EARLY_OUT,
          S.TOTAL_WORKING_HR  AS TOTAL_WORKING_HR,
          S.ACTUAL_WORKING_HR AS ACTUAL_WORKING_HR,
          S.SHIFT_ID          AS SHIFT_ID,
          AD.IN_TIME          AS IN_TIME,
          AD.OUT_TIME         AS OUT_TIME
        FROM HRIS_EMPLOYEES E
        JOIN
          (SELECT * FROM HRIS_EMPLOYEE_SHIFT_ASSIGN WHERE STATUS='E'
          ) SA
        ON (E.EMPLOYEE_ID =SA.EMPLOYEE_ID)
        LEFT JOIN
          (SELECT * FROM HRIS_SHIFTS WHERE STATUS='E'
          ) S
        ON (S.SHIFT_ID=SA.SHIFT_ID)
        LEFT JOIN HRIS_ATTENDANCE_DETAIL AD
        ON (E.EMPLOYEE_ID =AD.EMPLOYEE_ID)
        WHERE (V_DATE BETWEEN S.START_DATE AND S.END_DATE)
        AND AD.ATTENDANCE_DT = V_DATE
        AND E.STATUS         = 'E'
        AND E.RETIRED_FLAG   ='N'
        AND E.EMPLOYEE_TYPE  = CUR_PREF.EMPLOYEE_TYPE
        )
        LOOP
          BEGIN
            V_SHIFT_ID          := CUR_EMP.SHIFT_ID;
            V_START_TIME        := CUR_EMP.START_TIME;
            V_END_TIME          :=CUR_EMP.END_TIME;
            V_LATE_IN           :=CUR_EMP.LATE_IN;
            V_EARLY_OUT         :=CUR_EMP.EARLY_OUT;
            V_TOTAL_WORKING_HR  := CUR_EMP.TOTAL_WORKING_HR;
            V_ACTUAL_WORKING_HR :=CUR_EMP.ACTUAL_WORKING_HR;
            V_IN_TIME           := CUR_EMP.IN_TIME;
            V_OUT_TIME          :=CUR_EMP.OUT_TIME;
            IF CUR_EMP.SHIFT_ID IS NULL THEN
              BEGIN
                SELECT SHIFT_ID ,
                  START_TIME,
                  END_TIME,
                  NVL(LATE_IN,0),
                  NVL(EARLY_OUT,0),
                  NVL(TOTAL_WORKING_HR,0),
                  NVL(ACTUAL_WORKING_HR,0)
                INTO V_SHIFT_ID,
                  V_START_TIME,
                  V_END_TIME,
                  V_LATE_IN,
                  V_EARLY_OUT,
                  V_TOTAL_WORKING_HR,
                  V_ACTUAL_WORKING_HR
                FROM HRIS_SHIFTS
                WHERE STATUS     ='E'
                AND DEFAULT_SHIFT='Y'
                AND ROWNUM       <2;
              EXCEPTION
              WHEN NO_DATA_FOUND THEN
                RAISE_APPLICATION_ERROR(-20344, 'NO DEFAULT IS FOUND');
              END;
            END IF;
            --
            BEGIN
              SELECT COUNT(EMPLOYEE_ID)
              INTO V_EMP_OVERTIME_DATA_COUNT
              FROM HRIS_OVERTIME
              WHERE EMPLOYEE_ID            = CUR_EMP.EMPLOYEE_ID
              AND OVERTIME_DATE            =V_DATE;
              IF V_EMP_OVERTIME_DATA_COUNT =0 THEN
                --
                BEGIN
                  SELECT COUNT(*)
                  INTO V_PUNCH_COUNT
                  FROM HRIS_ATTENDANCE
                  WHERE EMPLOYEE_ID = CUR_EMP.EMPLOYEE_ID
                  AND ATTENDANCE_DT =V_DATE;
                  IF V_PUNCH_COUNT !=0 AND MOD(V_PUNCH_COUNT,2)=0 THEN
                    --
                    BEGIN
                      FOR CUR_OVERTIME       IN
                      (SELECT TRUNC(TOTAL_MINS/60,0)
                        ||':'
                        ||MOD(TOTAL_MINS,60) TOTAL_HRS,
                        TOTAL_MINS,
                        HR_TYPE
                      FROM
                        (SELECT
                          CASE MOD(RNUM,2)
                            WHEN 0
                            THEN 'WORKING'
                            ELSE 'NON-WORKING'
                          END AS HR_TYPE,
                          SUM(ABS(EXTRACT( HOUR FROM DIFF ))*60 + ABS(EXTRACT( MINUTE FROM DIFF ))) TOTAL_MINS
                        FROM
                          (SELECT ROW_NUMBER() OVER ( ORDER BY A.ATTENDANCE_TIME )    AS RNUM,
                            MOD((ROW_NUMBER() OVER ( ORDER BY A.ATTENDANCE_TIME )),2) AS NUM,
                            A.EMPLOYEE_ID,
                            A.IP_ADDRESS,
                            A.ATTENDANCE_DT,
                            A.ATTENDANCE_TIME,
                            (A.ATTENDANCE_TIME - LAG(A.ATTENDANCE_TIME) OVER (ORDER BY A.ATTENDANCE_TIME)) AS DIFF
                          FROM HRIS_ATTENDANCE A
                          WHERE A.EMPLOYEE_ID = CUR_EMP.EMPLOYEE_ID
                          AND A.ATTENDANCE_DT = V_DATE
                          )
                        GROUP BY MOD(RNUM,2)
                        )
                      )
                      LOOP
                        IF V_COUNTER       = 1 THEN
                          V_NON_WORKING_HR:=NVL(CUR_OVERTIME.TOTAL_MINS,0);
                        ELSE
                          V_WORKING_HR:=NVL(CUR_OVERTIME.TOTAL_MINS,0);
                        END IF;
                        -- NO EXCEPTION HANDLED HERE YET;
                        V_COUNTER:=V_COUNTER+1;
                      END LOOP;
                    END;
                    IF V_COUNTER != 3 THEN
                      CONTINUE;
                    END IF;
                    V_COUNTER:=1;
                    --
                    V_BREAK_TIME_IN_MIN          := (V_TOTAL_WORKING_HR-V_ACTUAL_WORKING_HR);
                    IF V_WORKING_HR               > V_ACTUAL_WORKING_HR THEN
                      IF V_BREAK_TIME_IN_MIN      > V_NON_WORKING_HR THEN
                        V_NON_CONSIDERED_OVERTIME:=(V_BREAK_TIME_IN_MIN - V_NON_WORKING_HR);
                      ELSE
                        V_NON_CONSIDERED_OVERTIME:=0;
                      END IF;
                      V_OVERTIME := ( V_WORKING_HR -V_ACTUAL_WORKING_HR)-V_NON_CONSIDERED_OVERTIME;
                      --                V_OVERTIME      :=V_OVERTIME      +NVL(V_LATE_IN_MIN,0)+NVL(V_EARLY_OUT_IN_MIN,0);
                      V_PREF_CONDITION:=
                      CASE
                      WHEN ((CUR_PREF.PREFERENCE_CONDITION = 'LESS_THAN') AND (V_OVERTIME <CUR_PREF.CONSTRAINT_VALUE)) OR ((CUR_PREF.PREFERENCE_CONDITION = 'GREATER_THAN') AND (V_OVERTIME >CUR_PREF.CONSTRAINT_VALUE)) OR ((CUR_PREF.PREFERENCE_CONDITION = 'EQUAL') AND (V_OVERTIME =CUR_PREF.CONSTRAINT_VALUE)) THEN
                        'Y'
                      ELSE
                        'N'
                      END;
                      IF V_PREF_CONDITION = 'Y' THEN
                        SELECT NVL(MAX (OVERTIME_ID),0) + 1 INTO V_OVERTIME_ID FROM HRIS_OVERTIME;
                        DBMS_OUTPUT.PUT_LINE(''||V_OVERTIME);
                        SELECT CONCAT( TRUNC(V_OVERTIME/60,0), CONCAT(':',MOD(V_OVERTIME,60)))
                        INTO V_OVERTIME_IN_HR
                        FROM DUAL;
                        INSERT
                        INTO HRIS_OVERTIME
                          (
                            OVERTIME_ID,
                            EMPLOYEE_ID,
                            OVERTIME_DATE,
                            REQUESTED_DATE,
                            DESCRIPTION,
                            REMARKS,
                            STATUS,
                            RECOMMENDED_BY,
                            RECOMMENDED_DATE,
                            RECOMMENDED_REMARKS,
                            APPROVED_BY,
                            APPROVED_DATE,
                            APPROVED_REMARKS,
                            MODIFIED_DATE,
                            TOTAL_HOUR
                          )
                          VALUES
                          (
                            V_OVERTIME_ID ,
                            CUR_EMP.EMPLOYEE_ID,
                            V_DATE,
                            V_DATE,
                            'Overtime Request',
                            NULL,
                            CUR_PREF.REQUEST_TYPE,
                            (
                            CASE CUR_PREF.REQUEST_TYPE
                              WHEN 'AP'
                              THEN V_ADMIN_ID
                              ELSE NULL
                            END),
                            (
                            CASE CUR_PREF.REQUEST_TYPE
                              WHEN 'AP'
                              THEN V_DATE
                              ELSE NULL
                            END),
                            NULL,
                            (
                            CASE CUR_PREF.REQUEST_TYPE
                              WHEN 'AP'
                              THEN V_ADMIN_ID
                              ELSE NULL
                            END),
                            (
                            CASE CUR_PREF.REQUEST_TYPE
                              WHEN 'AP'
                              THEN V_DATE
                              ELSE NULL
                            END),
                            NULL,
                            NULL,
                            V_OVERTIME
                          );
                        COMMIT;
                        IF (V_IN_TIME != V_START_TIME) AND (V_IN_TIME < V_START_TIME) THEN
                          SELECT NVL(MAX (DETAIL_ID),0) + 1
                          INTO V_OVERTIME_DETAIL_ID
                          FROM HRIS_OVERTIME_DETAIL;
                          SELECT SUM(ABS(EXTRACT( HOUR FROM PRE_OVERTIME ))*60 + ABS(EXTRACT( MINUTE FROM PRE_OVERTIME )))
                          INTO V_PRE_OVERTIME_MIN
                          FROM
                            (SELECT (V_START_TIME-V_IN_TIME) AS PRE_OVERTIME FROM DUAL
                            );
                          SELECT CONCAT( TRUNC(V_PRE_OVERTIME_MIN/60,0), CONCAT(':',MOD(V_PRE_OVERTIME_MIN,60)))
                          INTO V_PRE_OVERTIME
                          FROM DUAL;
                          INSERT
                          INTO HRIS_OVERTIME_DETAIL
                            (
                              DETAIL_ID,
                              OVERTIME_ID,
                              START_TIME,
                              END_TIME,
                              STATUS,
                              CREATED_BY,
                              CREATED_DATE,
                              MODIFIED_BY,
                              MODIFIED_DATE,
                              TOTAL_HOUR
                            )
                            VALUES
                            (
                              V_OVERTIME_DETAIL_ID ,
                              V_OVERTIME_ID,
                              V_IN_TIME,
                              V_START_TIME,
                              'E',
                              CUR_EMP.EMPLOYEE_ID,
                              V_DATE,
                              NULL,
                              NULL,
                              V_PRE_OVERTIME_MIN
                            );
                          COMMIT;
                        END IF;
                        IF (V_OUT_TIME != V_END_TIME) AND (V_END_TIME < V_OUT_TIME) THEN
                          SELECT NVL(MAX (DETAIL_ID),0) + 1
                          INTO V_OVERTIME_DETAIL_ID
                          FROM HRIS_OVERTIME_DETAIL;
                          SELECT SUM(ABS(EXTRACT( HOUR FROM POST_OVERTIME ))*60 + ABS(EXTRACT( MINUTE FROM POST_OVERTIME )))
                          INTO V_POST_OVERTIME_MIN
                          FROM
                            (SELECT (V_OUT_TIME-V_END_TIME) AS POST_OVERTIME FROM DUAL
                            );
                          SELECT CONCAT( TRUNC(V_POST_OVERTIME_MIN/60,0), CONCAT(':',MOD(V_POST_OVERTIME_MIN,60)))
                          INTO V_POST_OVERTIME
                          FROM DUAL;
                          INSERT
                          INTO HRIS_OVERTIME_DETAIL
                            (
                              DETAIL_ID,
                              OVERTIME_ID,
                              START_TIME,
                              END_TIME,
                              STATUS,
                              CREATED_BY,
                              CREATED_DATE,
                              MODIFIED_BY,
                              MODIFIED_DATE,
                              TOTAL_HOUR
                            )
                            VALUES
                            (
                              V_OVERTIME_DETAIL_ID ,
                              V_OVERTIME_ID,
                              V_END_TIME,
                              V_OUT_TIME,
                              'E',
                              CUR_EMP.EMPLOYEE_ID,
                              V_DATE,
                              NULL,
                              NULL,
                              V_POST_OVERTIME_MIN
                            );
                          COMMIT;
                        END IF;
                      END IF;
                    END IF;
                    --
                    --
                  END IF;
                END;
              END IF;
            END;
            V_EMPLOYEE_COUNTER:=V_EMPLOYEE_COUNTER+1;
          END;
        END LOOP;
        IF V_EMPLOYEE_COUNTER=0 THEN
          RAISE NO_EMPLOYEE_DATA_FOUND;
        END IF;
      EXCEPTION
      WHEN NO_EMPLOYEE_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20004,'FAILED ON PREFERENCE_ID : ' || CUR_PREF.PREFERENCE_ID || ' EMPLOYEE ARE NOT AVAILABLE WITH PROVIDED ATTENDANCE DATE ACCORDING TO SETUP');
      END;
      V_PREF_COUNTER:=V_PREF_COUNTER+1;
    END LOOP;
    IF V_PREF_COUNTER=0 THEN
      RAISE NO_PREFERENCE_DATA_FOUND;
    END IF;
  EXCEPTION
  WHEN NO_PREFERENCE_DATA_FOUND THEN
    RAISE_APPLICATION_ERROR(-20005, 'NO PREFERENCE SETTING IS DEFINED FOR OVERTIME AUTOMATION');
  END;
END HRIS_OVERTIME_AUTOMATION;