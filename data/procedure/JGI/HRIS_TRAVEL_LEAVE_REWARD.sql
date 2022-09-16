CREATE OR REPLACE PROCEDURE HRIS_TRAVEL_LEAVE_REWARD(
    P_TRAVEL_ID NUMBER )
AS
  v_employee_id HRIS_EMPLOYEE_TRAVEL_REQUEST.EMPLOYEE_ID%TYPE;
  v_shift_id          NUMBER;
  v_travel_start_date DATE;
  v_travel_end_date   DATE;
  v_duration          NUMBER;
  v_weekday1 hris_shifts.weekday1%TYPE;
  v_weekday2 hris_shifts.weekday2%TYPE;
  v_weekday3 hris_shifts.weekday3%TYPE;
  v_weekday4 hris_shifts.weekday4%TYPE;
  v_weekday5 hris_shifts.weekday5%TYPE;
  v_weekday6 hris_shifts.weekday6%TYPE;
  v_weekday7 hris_shifts.weekday7%TYPE;
  v_dayoff        VARCHAR2(1 BYTE);
  v_holiday_count NUMBER;
  v_holiday       CHAR(1 BYTE);
  v_sub_days      NUMBER := 0;
  v_sub_leave_id  NUMBER;
  v_increment     NUMBER:=1;
  v_request_type HRIS_EMPLOYEE_TRAVEL_REQUEST.REQUESTED_TYPE%TYPE;
BEGIN

  dbms_output.put_line('LEAVE ADDITION OF TRAVEL  LEAVE');

  SELECT FROM_DATE,
    TO_DATE,
    (TO_DATE- FROM_DATE +1),
    EMPLOYEE_ID,
    REQUESTED_TYPE
  INTO v_travel_start_date,
    v_travel_end_date,
    v_duration,
    v_employee_id,
    v_request_type
  FROM HRIS_EMPLOYEE_TRAVEL_REQUEST where travel_id=P_TRAVEL_ID ;

IF(v_request_type in ('ad') and v_travel_start_date >to_date('04-MAR-20'))
THEN
  FOR i IN 0..v_duration - 1
  LOOP

    IF((v_travel_start_date+i)<=TRUNC(sysdate)) THEN
      dbms_output.put_line(v_travel_start_date + i);
      BEGIN
        SELECT hs.shift_id,
          hs.weekday1,
          hs.weekday2,
          hs.weekday3,
          hs.weekday4,
          hs.weekday5,
          hs.weekday6,
          hs.weekday7
        INTO v_shift_id,
          v_weekday1,
          v_weekday2,
          v_weekday3,
          v_weekday4,
          v_weekday5,
          v_weekday6,
          v_weekday7
        FROM HRIS_ATTENDANCE_DETAIL AD
        LEFT JOIN hris_shifts hs
        ON (AD.SHIFT_ID        = hs.SHIFT_ID)
        WHERE 1                = 1
        AND AD.employee_id     = v_employee_id
        AND hs.status          = 'E'
        and ad.attendance_dt=v_travel_start_date+i
        AND AD.shift_id        = hs.shift_id;
      EXCEPTION
      WHEN no_data_found THEN
        BEGIN
          SELECT shift_id,
            weekday1,
            weekday2,
            weekday3,
            weekday4,
            weekday5,
            weekday6,
            weekday7
          INTO v_shift_id,
            v_weekday1,
            v_weekday2,
            v_weekday3,
            v_weekday4,
            v_weekday5,
            v_weekday6,
            v_weekday7
          FROM hris_shifts
          WHERE v_travel_start_date + i BETWEEN start_date AND end_date
          AND default_shift = 'Y'
          AND status        = 'E'
          AND ROWNUM        = 1;
        EXCEPTION
        WHEN no_data_found THEN
          raise_application_error(-20344,'No default and normal shift defined for this time period');
        END;
      END;
      dbms_output.put_line(v_shift_id);
      v_dayoff := 'N';
      BEGIN
        IF ( TO_CHAR(v_travel_start_date + i,'D') = '1' ) THEN
          IF v_weekday1                           = 'DAY_OFF' THEN
            v_dayoff                             := 'Y';
          END IF;
        ELSIF ( TO_CHAR(v_travel_start_date + i,'D') = '2' ) THEN
          IF v_weekday2                              = 'DAY_OFF' THEN
            v_dayoff                                := 'Y';
          END IF;
        ELSIF ( TO_CHAR(v_travel_start_date + i,'D') = '3' ) THEN
          IF v_weekday3                              = 'DAY_OFF' THEN
            v_dayoff                                := 'Y';
          END IF;
        ELSIF ( TO_CHAR(v_travel_start_date + i,'D') = '4' ) THEN
          IF v_weekday4                              = 'DAY_OFF' THEN
            v_dayoff                                := 'Y';
          END IF;
        ELSIF ( TO_CHAR(v_travel_start_date + i,'D') = '5' ) THEN
          IF v_weekday5                              = 'DAY_OFF' THEN
            v_dayoff                                := 'Y';
          END IF;
        ELSIF ( TO_CHAR(v_travel_start_date + i,'D') = '6' ) THEN
          IF v_weekday6                              = 'DAY_OFF' THEN
            v_dayoff                                := 'Y';
          END IF;
        ELSIF ( TO_CHAR(v_travel_start_date + i,'D') = '7' ) THEN
          IF v_weekday7                              = 'DAY_OFF' THEN
            v_dayoff                                := 'Y';
          END IF;
        dbms_output.put_line('DAYOFF ' || v_dayoff);
        dbms_output.put_line('SUBdAYS test ' || v_sub_days);
        IF ( v_dayoff = 'Y' ) THEN
          dbms_output.put_line('today is day off day');
          v_sub_days := v_sub_days + v_increment;
        ELSE
          BEGIN
            SELECT COUNT(*)
            INTO v_holiday_count
            FROM hris_holiday_master_setup hs
            LEFT JOIN hris_employee_holiday eha
            ON ( hs.holiday_id    = eha.holiday_id )
            WHERE eha.employee_id = v_employee_id
            AND v_travel_start_date + i BETWEEN start_date AND end_date;
            IF ( v_holiday_count > 0 ) THEN
              dbms_output.put_line('HOLIDAY');
              v_sub_days := v_sub_days + v_increment;
            END IF;
          END;

        END IF;
      END IF;
      dbms_output.put_line('-----------');
    END;


  dbms_output.put_line('SUBdAYS ' || v_sub_days);


  END IF;

  END LOOP;

   BEGIN
    SELECT leave_id
    INTO v_sub_leave_id
    FROM hris_leave_master_setup
    WHERE is_substitute = 'Y';

     delete from hris_employee_leave_addition where employee_id=v_employee_id and travel_id=P_TRAVEL_ID;
    IF ( v_sub_days     > 0 ) THEN
      INSERT
      INTO hris_employee_leave_addition VALUES
        (
          v_employee_id,
          v_sub_leave_id,
          v_sub_days,
          'Travel REWARD',
          TRUNC(SYSDATE),
          NULL,
          NULL,
          NULL,
          P_TRAVEL_ID
        );
    END IF;
  END;

END IF;




END;