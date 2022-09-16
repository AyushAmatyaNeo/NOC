create or replace PROCEDURE hris_manual_attendance (
    p_employee_id     int default null,
    p_attendance_dt 	DATE default NULL,
    p_status          CHAR,
    p_shift_id        int default null,
    p_in_time        DATE default NULL,
    p_out_time        DATE default NULL
) AS
    v_week_day      INT;
    v_dynamic_sql   VARCHAR(1000);
    v_to_time       TIMESTAMP;
BEGIN
    IF
        ( p_shift_id != '0' AND
            p_shift_id IS NOT NULL
        )
    THEN
        BEGIN
            DELETE FROM hris_employee_shift_roaster WHERE
                    employee_id = p_employee_id
                AND
                    for_date = p_attendance_dt;

            INSERT INTO hris_employee_shift_roaster VALUES (
                p_employee_id,
                p_shift_id,
                p_attendance_dt,
                NULL,
                NULL,
                NULL,
                NULL
            );

        END;
    END IF;

    SELECT
        TO_CHAR(p_attendance_dt,'d')
    INTO
        v_week_day
    FROM
        dummy;

BEGIN
	declare cursor employee for ( SELECT
            a.employee_id,
            a.attendance_dt,
            s.start_time,
            s.end_time,
            a.overall_status,
            s.shift_id
        FROM
            hris_attendance_detail a
            JOIN hris_shifts s ON (
                a.shift_id = s.shift_id
            )
        WHERE
                a.employee_id = p_employee_id
            AND
                a.attendance_dt = p_attendance_dt
            AND
                a.overall_status IN (
                    'AB','PR','BA','LA')
                    );
                    
         FOR E AS EMPLOYEE DO
		v_to_time := E.end_time;
		
		  v_dynamic_sql := 'SELECT CASE WHEN WEEKDAY'
         || v_week_day
         || '=''H'' AND HALF_DAY_OUT_TIME IS NOT NULL  THEN
            HALF_DAY_OUT_TIME
            ELSE
            end_time
            END
            FROM  HRIS_SHIFTS WHERE SHIFT_ID='
         || e.shift_id;
        EXECUTE IMMEDIATE v_dynamic_sql INTO
            v_to_time;
		
		       IF
            p_status = 'P'
        THEN
            INSERT INTO hris_attendance (
                employee_id,
                attendance_dt,
                attendance_time,
                attendance_from
            ) VALUES (
                e.employee_id,
                e.attendance_dt,
                CASE
                    WHEN p_in_time IS NOT NULL THEN TO_DATE(
                        TO_CHAR(e.attendance_dt,'DD-MON-YYYY') || ' ' || TO_CHAR(p_in_time,'HH24:MI'),
                        'DD-MON-YYYY HH24:MI'
                    )
                    ELSE TO_DATE(
                        TO_CHAR(e.attendance_dt,'DD-MON-YYYY') || ' ' || TO_CHAR(e.start_time,'HH24:MI'),
                        'DD-MON-YYYY HH24:MI'
                    )
                END,
                'SYSTEM'
                
            );
            
            
             INSERT INTO hris_attendance (
                employee_id,
                attendance_dt,
                attendance_time,
                attendance_from
            ) VALUES (
                e.employee_id,
                e.attendance_dt,
                CASE
                    WHEN p_out_time IS NOT NULL THEN TO_DATE(
                        TO_CHAR(e.attendance_dt,'DD-MON-YYYY') || ' ' || TO_CHAR(p_out_time,'HH24:MI'),
                        'DD-MON-YYYY HH24:MI'
                    )
                    ELSE TO_DATE(
                        TO_CHAR(e.attendance_dt,'DD-MON-YYYY') || ' ' || TO_CHAR(v_to_time,'HH24:MI'),
                        'DD-MON-YYYY HH24:MI'
                    )
                END,
                'SYSTEM'
            );
            
            END IF;
		
		IF
            p_status = 'A'
        THEN
            DELETE FROM hris_attendance WHERE
                    employee_id = p_employee_id
                AND
                    attendance_dt = p_attendance_dt
                --AND
                    --attendance_from = 'SYSTEM'
;

        END IF;
		
		
		 hris_reattendance(
            e.attendance_dt,
            e.employee_id,
            e.attendance_dt
        );
		
		 END FOR;
      END;              
                    
END;
 