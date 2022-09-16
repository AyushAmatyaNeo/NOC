create or replace PROCEDURE HRIS_PRELOAD_ATTENDANCE(
    V_ATTENDANCE_DATE DATE,
    P_EMPLOYEE_ID HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE:=NULL,
    P_SHIFT_ID HRIS_SHIFTS.SHIFT_ID%TYPE         :=NULL)
AS
  V_EMPLOYEE_ID HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE;
  V_SHIFT_ID HRIS_SHIFTS.SHIFT_ID%TYPE;
  V_WEEKDAY1 HRIS_SHIFTS.WEEKDAY1%TYPE;
  V_WEEKDAY2 HRIS_SHIFTS.WEEKDAY2%TYPE;
  V_WEEKDAY3 HRIS_SHIFTS.WEEKDAY3%TYPE;
  V_WEEKDAY4 HRIS_SHIFTS.WEEKDAY4%TYPE;
  V_WEEKDAY5 HRIS_SHIFTS.WEEKDAY5%TYPE;
  V_WEEKDAY6 HRIS_SHIFTS.WEEKDAY6%TYPE;
  V_WEEKDAY7 HRIS_SHIFTS.WEEKDAY7%TYPE;
  V_DAYOFF  VARCHAR2(1 BYTE);
  V_HALFDAY CHAR(1 BYTE);
  V_HOLIDAY_ID HRIS_HOLIDAY_MASTER_SETUP.HOLIDAY_ID%TYPE;
  V_LEAVE_ID HRIS_LEAVE_MASTER_SETUP.LEAVE_ID%TYPE;
  V_LEAVE_HALFDAY_PERIOD HRIS_EMPLOYEE_LEAVE_REQUEST.HALF_DAY%TYPE;
  V_LEAVE_GRACE_PERIOD HRIS_EMPLOYEE_LEAVE_REQUEST.GRACE_PERIOD%TYPE;
  V_TRAVEL_ID HRIS_EMPLOYEE_TRAVEL_REQUEST.TRAVEL_ID%TYPE;
  V_TRAINING_ID HRIS_TRAINING_MASTER_SETUP.TRAINING_ID%TYPE;
  V_TRAINING_TYPE HRIS_ATTENDANCE_DETAIL.TRAINING_TYPE%TYPE;
  V_WOD_ID HRIS_EMPLOYEE_WORK_DAYOFF.ID%TYPE;
  V_WOH_ID HRIS_EMPLOYEE_WORK_HOLIDAY.ID%TYPE;
  V_TWO_DAY_SHIFT HRIS_SHIFTS.TWO_DAY_SHIFT%TYPE;
  V_IGNORE_TIME HRIS_SHIFTS.IGNORE_TIME%TYPE;
  V_MAX_ID                NUMBER;
  V_ATTENDANCE_DATA_COUNT NUMBER;
  V_WEEKLY_SHIFT NUMBER;
  V_ROASTER_COUNT NUMBER;
  CURSOR CUR_EMPLOYEE
  IS
    SELECT EMPLOYEE_ID
    FROM HRIS_EMPLOYEES
    WHERE STATUS     ='E'
    AND (EMPLOYEE_ID =
      CASE
        WHEN P_EMPLOYEE_ID IS NOT NULL
        THEN P_EMPLOYEE_ID
      END
    OR P_EMPLOYEE_ID IS NULL)
    AND JOIN_DATE    <= TRUNC(V_ATTENDANCE_DATE);
  --
  V_OVERALL_STATUS CHAR(2 BYTE);
BEGIN
  --
  OPEN CUR_EMPLOYEE;
  LOOP
    FETCH CUR_EMPLOYEE INTO V_EMPLOYEE_ID;
    EXIT
  WHEN CUR_EMPLOYEE%NOTFOUND;
    -- RESET VALUES FOR EACH LOOP
    V_DAYOFF              :='N';
    V_HALFDAY             :='N';
    V_HOLIDAY_ID          :=NULL;
    V_LEAVE_ID            :=NULL;
    V_TRAINING_ID         :=NULL;
    V_TRAINING_TYPE       :=NULL;
    V_TRAVEL_ID           :=NULL;
    V_OVERALL_STATUS      :=NULL;
    V_LEAVE_HALFDAY_PERIOD:=NULL;
    V_LEAVE_GRACE_PERIOD  :=NULL;
    V_WEEKLY_SHIFT        :=NULL;
    V_ROASTER_COUNT       :=NULL;
    --
    -- DELETE IF ALREADY EXISTS
    DELETE
    FROM HRIS_ATTENDANCE_DETAIL
    WHERE EMPLOYEE_ID = V_EMPLOYEE_ID
    AND ATTENDANCE_DT =V_ATTENDANCE_DATE;
    --
    IF P_SHIFT_ID IS NOT NULL THEN
      --    FETCH SHIFT DATA IF PASSED
      BEGIN
        SELECT HS.SHIFT_ID,
          WEEKDAY1,
          WEEKDAY2,
          WEEKDAY3,
          WEEKDAY4,
          WEEKDAY5,
          WEEKDAY6,
          WEEKDAY7,
          TWO_DAY_SHIFT,
          IGNORE_TIME
        INTO V_SHIFT_ID,
          V_WEEKDAY1,
          V_WEEKDAY2,
          V_WEEKDAY3,
          V_WEEKDAY4,
          V_WEEKDAY5,
          V_WEEKDAY6,
          V_WEEKDAY7,
          V_TWO_DAY_SHIFT,
          V_IGNORE_TIME
        FROM HRIS_SHIFTS HS
        WHERE HS.SHIFT_ID = P_SHIFT_ID ;
      END;
      --
    ELSE
    
    BEGIN
BEGIN
    select 
CASE to_char(V_ATTENDANCE_DATE, 'DY') WHEN 'SUN' THEN SUN
WHEN 'MON' THEN MON
WHEN 'TUE' THEN TUE
WHEN 'WED' THEN WED
WHEN 'THU' THEN THU
WHEN 'FRI' THEN FRI
WHEN 'SAT' THEN SAT
END 
INTO V_WEEKLY_SHIFT
FROM   HRIS_WEEKLY_ROASTER WHERE  EMPLOYEE_ID=V_EMPLOYEE_ID AND V_ATTENDANCE_DATE=trunc(sysdate);
EXCEPTION
      WHEN NO_DATA_FOUND THEN
NULL;
END;


IF (V_WEEKLY_SHIFT IS NOT NULL AND V_WEEKLY_SHIFT>0) THEN

SELECT COUNT(*) INTO V_ROASTER_COUNT FROM HRIS_EMPLOYEE_SHIFT_ROASTER WHERE EMPLOYEE_ID=V_EMPLOYEE_ID AND FOR_DATE=V_ATTENDANCE_DATE;

IF(V_ROASTER_COUNT=0) 
THEN
INSERT INTO HRIS_EMPLOYEE_SHIFT_ROASTER (EMPLOYEE_ID,SHIFT_ID,FOR_DATE)
VALUES (V_EMPLOYEE_ID,V_WEEKLY_SHIFT,V_ATTENDANCE_DATE);
END IF;

END IF;


    END;
    
    
      BEGIN
        SELECT HS.SHIFT_ID,
          WEEKDAY1,
          WEEKDAY2,
          WEEKDAY3,
          WEEKDAY4,
          WEEKDAY5,
          WEEKDAY6,
          WEEKDAY7,
          TWO_DAY_SHIFT,
          IGNORE_TIME
        INTO V_SHIFT_ID,
          V_WEEKDAY1,
          V_WEEKDAY2,
          V_WEEKDAY3,
          V_WEEKDAY4,
          V_WEEKDAY5,
          V_WEEKDAY6,
          V_WEEKDAY7,
          V_TWO_DAY_SHIFT,
          V_IGNORE_TIME
        FROM HRIS_EMPLOYEE_SHIFT_ROASTER ES,
          HRIS_SHIFTS HS
        WHERE 1                = 1
        AND ES.EMPLOYEE_ID     = V_EMPLOYEE_ID
        AND TRUNC(ES.FOR_DATE) = V_ATTENDANCE_DATE
        AND HS.STATUS          = 'E'
        AND ES.SHIFT_ID        = HS.SHIFT_ID;
      EXCEPTION
      WHEN NO_DATA_FOUND THEN
        BEGIN
          SELECT HS.SHIFT_ID,
            WEEKDAY1,
            WEEKDAY2,
            WEEKDAY3,
            WEEKDAY4,
            WEEKDAY5,
            WEEKDAY6,
            WEEKDAY7,
            TWO_DAY_SHIFT,
            IGNORE_TIME
          INTO V_SHIFT_ID,
            V_WEEKDAY1,
            V_WEEKDAY2,
            V_WEEKDAY3,
            V_WEEKDAY4,
            V_WEEKDAY5,
            V_WEEKDAY6,
            V_WEEKDAY7,
            V_TWO_DAY_SHIFT,
            V_IGNORE_TIME
          FROM
            (SELECT *
            FROM
              (SELECT *
              FROM HRIS_EMPLOYEE_SHIFT_ASSIGN
              WHERE EMPLOYEE_ID              = V_EMPLOYEE_ID
              AND (TRUNC(V_ATTENDANCE_DATE) >= START_DATE
              AND TRUNC(V_ATTENDANCE_DATE)  <=
                CASE
                  WHEN END_DATE IS NOT NULL
                  THEN END_DATE
                  ELSE TRUNC(V_ATTENDANCE_DATE)
                END)
              ORDER BY START_DATE DESC,
                END_DATE ASC
              )
            WHERE ROWNUM=1
            ) ES,
            HRIS_SHIFTS HS
          WHERE ES.SHIFT_ID = HS.SHIFT_ID;
        EXCEPTION
        WHEN NO_DATA_FOUND THEN
          BEGIN
            SELECT SHIFT_ID,
              WEEKDAY1,
              WEEKDAY2,
              WEEKDAY3,
              WEEKDAY4,
              WEEKDAY5,
              WEEKDAY6,
              WEEKDAY7,
              TWO_DAY_SHIFT,
              IGNORE_TIME
            INTO V_SHIFT_ID,
              V_WEEKDAY1,
              V_WEEKDAY2,
              V_WEEKDAY3,
              V_WEEKDAY4,
              V_WEEKDAY5,
              V_WEEKDAY6,
              V_WEEKDAY7,
              V_TWO_DAY_SHIFT,
              V_IGNORE_TIME
            FROM HRIS_SHIFTS
            WHERE V_ATTENDANCE_DATE BETWEEN START_DATE AND END_DATE
            AND DEFAULT_SHIFT = 'Y'
            AND STATUS        ='E'
            AND ROWNUM        =1 ;
          EXCEPTION
          WHEN NO_DATA_FOUND THEN
            RAISE_APPLICATION_ERROR(-20344, 'No default and normal shift defined for this time period');
          END;
        END;
      END;
    END IF;
    SELECT NVL(MAX (ID),0) + 1 INTO V_MAX_ID FROM HRIS_ATTENDANCE_DETAIL;
    BEGIN
      IF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='1') THEN
        IF V_WEEKDAY1                    = 'DAY_OFF' THEN
          V_DAYOFF                      := 'Y';
        ELSIF V_WEEKDAY1                 ='H' THEN
          V_HALFDAY                     := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='2') THEN
        IF V_WEEKDAY2                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY2                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='3') THEN
        IF V_WEEKDAY3                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY3                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='4') THEN
        IF V_WEEKDAY4                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY4                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='5') THEN
        IF V_WEEKDAY5                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY5                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='6') THEN
        IF V_WEEKDAY6                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY6                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='7') THEN
        IF V_WEEKDAY7                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY7                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      END IF;
      IF (V_DAYOFF       ='Y') THEN
        V_OVERALL_STATUS:='DO';
      END IF;
      IF (V_HALFDAY      ='Y') THEN
        V_OVERALL_STATUS:='AB';
        --        NOT DEFINED IN SETUP SO DEFAULT IS FIRST HALF
        V_LEAVE_HALFDAY_PERIOD:='S';
        --
      END IF;
    END;
    -- CHECK FOR HOLIDAY
    BEGIN
      SELECT H.HOLIDAY_ID
      INTO V_HOLIDAY_ID
      FROM HRIS_HOLIDAY_MASTER_SETUP H
      JOIN HRIS_EMPLOYEE_HOLIDAY EH
      ON (H.HOLIDAY_ID=EH.HOLIDAY_ID)
      WHERE V_ATTENDANCE_DATE BETWEEN H.START_DATE AND H.END_DATE
      AND EH.EMPLOYEE_ID  =V_EMPLOYEE_ID
      AND ROWNUM          <2;
      IF V_HOLIDAY_ID    IS NOT NULL THEN
        V_OVERALL_STATUS :='HD';
      END IF;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    --
    BEGIN
      SELECT LEAVE_ID,
        HALF_DAY,
        GRACE_PERIOD
      INTO V_LEAVE_ID,
        V_LEAVE_HALFDAY_PERIOD,
        V_LEAVE_GRACE_PERIOD
      FROM
        (SELECT L.LEAVE_ID,
          (
          CASE
            WHEN L.HALF_DAY IS NULL
            OR L.HALF_DAY    = 'N'
            THEN NULL
            ELSE L.HALF_DAY
          END ) AS HALF_DAY ,
          L.GRACE_PERIOD
        FROM HRIS_EMPLOYEE_LEAVE_REQUEST L
        LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
        ON (L.LEAVE_ID      =LMS.LEAVE_ID)
        WHERE L.EMPLOYEE_ID = V_EMPLOYEE_ID
        AND V_ATTENDANCE_DATE BETWEEN L.START_DATE AND L.END_DATE
        AND L.STATUS            = 'AP'
        AND (V_DAYOFF          !='Y'
        OR LMS.DAY_OFF_AS_LEAVE ='Y')
        AND (V_HOLIDAY_ID      IS NULL
        OR LMS.HOLIDAY_AS_LEAVE ='Y')
        ORDER BY L.REQUESTED_DT DESC
        )
      WHERE ROWNUM        =1;
      IF V_LEAVE_ID      IS NOT NULL AND V_LEAVE_HALFDAY_PERIOD IS NULL AND V_LEAVE_GRACE_PERIOD IS NULL THEN
        V_OVERALL_STATUS :='LV';
      END IF;
      --       IF V_LEAVE_HALFDAY_PERIOD IS NOT NULL THEN
      --         V_HALFDAY               := 'Y';
      --       END IF;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    BEGIN
      SELECT TA.TRAINING_ID,
        'A'
      INTO V_TRAINING_ID,
        V_TRAINING_TYPE
      FROM HRIS_EMPLOYEE_TRAINING_ASSIGN TA
      LEFT JOIN HRIS_EMP_TRAINING_ATTENDANCE ETA ON (
        TA.TRAINING_ID = ETA.TRAINING_ID
        AND ETA.TRAINING_DT=V_ATTENDANCE_DATE
        AND ETA.EMPLOYEE_ID=V_EMPLOYEE_ID)
      INNER JOIN HRIS_TRAINING_MASTER_SETUP T
      ON TA.TRAINING_ID       = T.TRAINING_ID
      WHERE TA.EMPLOYEE_ID    = V_EMPLOYEE_ID
      AND TA.STATUS           = 'E'
      AND ETA.ATTENDANCE_STATUS = 'P'
      AND T.IS_WITHIN_COMPANY ='N'
      AND V_ATTENDANCE_DATE BETWEEN T.START_DATE AND T.END_DATE;
      IF V_TRAINING_ID  IS NOT NULL THEN
        V_OVERALL_STATUS:='TN';
      END IF;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    BEGIN
      SELECT TR.REQUEST_ID,
        'R'
      INTO V_TRAINING_ID,
        V_TRAINING_TYPE
      FROM HRIS_EMPLOYEE_TRAINING_REQUEST TR
      WHERE TR.EMPLOYEE_ID     = V_EMPLOYEE_ID
      AND TR.STATUS            = 'AP'
      AND TR.IS_WITHIN_COMPANY ='N'
      AND V_ATTENDANCE_DATE BETWEEN TR.START_DATE AND TR.END_DATE
      AND ROWNUM         =1;
      IF V_TRAINING_ID  IS NOT NULL THEN
        V_OVERALL_STATUS:='TN';
      END IF;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    BEGIN
      SELECT TRAVEL_ID
      INTO V_TRAVEL_ID
      FROM HRIS_EMPLOYEE_TRAVEL_REQUEST
      WHERE EMPLOYEE_ID = V_EMPLOYEE_ID
      AND STATUS        = 'AP'
      AND (V_ATTENDANCE_DATE BETWEEN FROM_DATE AND TO_DATE)
      AND ROWNUM          =1;
      IF V_TRAVEL_ID     IS NOT NULL THEN
        V_OVERALL_STATUS := 'TV';
      END IF;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    -- CHECK FOR WOD
    BEGIN
      SELECT ID
      INTO V_WOD_ID
      FROM
        (SELECT ID
        FROM HRIS_EMPLOYEE_WORK_DAYOFF
        WHERE EMPLOYEE_ID = V_EMPLOYEE_ID
        AND (V_ATTENDANCE_DATE BETWEEN FROM_DATE AND TO_DATE )
        AND STATUS ='AP'
        ORDER BY REQUESTED_DATE DESC
        )
      WHERE ROWNUM      =1 ;
      V_OVERALL_STATUS :=
      CASE
      WHEN V_OVERALL_STATUS ='TV' THEN
        'VP'
      ELSE
        'WD'
      END;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    --
    -- CHECK OF WOH
    BEGIN
      SELECT ID
      INTO V_WOH_ID
      FROM
        (SELECT ID
        FROM HRIS_EMPLOYEE_WORK_HOLIDAY
        WHERE EMPLOYEE_ID =V_EMPLOYEE_ID
        AND (V_ATTENDANCE_DATE BETWEEN FROM_DATE AND TO_DATE )
        AND STATUS ='AP'
        ORDER BY REQUESTED_DATE DESC
        )
      WHERE ROWNUM      =1 ;
      V_OVERALL_STATUS :=
      CASE
      WHEN V_OVERALL_STATUS ='TV' THEN
        'VP'
      ELSE
        'WH'
      END;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    --
    BEGIN
      INSERT
      INTO HRIS_ATTENDANCE_DETAIL
        (
          ID,
          EMPLOYEE_ID,
          ATTENDANCE_DT,
          SHIFT_ID,
          DAYOFF_FLAG,
          HALFDAY_FLAG,
          HALFDAY_PERIOD,
          GRACE_PERIOD,
          HOLIDAY_ID,
          LEAVE_ID,
          TRAVEL_ID,
          TRAINING_ID,
          TRAINING_TYPE,
          OVERALL_STATUS,
          TWO_DAY_SHIFT,
          IGNORE_TIME
        )
        VALUES
        (
          V_MAX_ID,
          V_EMPLOYEE_ID,
          V_ATTENDANCE_DATE,
          V_SHIFT_ID,
          V_DAYOFF,
          V_HALFDAY,
          V_LEAVE_HALFDAY_PERIOD,
          V_LEAVE_GRACE_PERIOD,
          V_HOLIDAY_ID,
          V_LEAVE_ID,
          V_TRAVEL_ID,
          V_TRAINING_ID,
          V_TRAINING_TYPE,
          (
          CASE
            WHEN V_OVERALL_STATUS IS NULL
            THEN 'AB'
            ELSE V_OVERALL_STATUS
          END),
          (
          CASE
            WHEN V_TWO_DAY_SHIFT IS NULL
            THEN 'D'
            ELSE V_TWO_DAY_SHIFT
          END),
          V_IGNORE_TIME
        );
    END;
  END LOOP;
  CLOSE CUR_EMPLOYEE;
END;