create or replace PROCEDURE hris_backdate_attendance ( p_id hris_attendance_request.id%TYPE ) AS

    p_attendance_dt   hris_attendance_request.attendance_dt%TYPE;
    p_employee_id     hris_employees.employee_id%TYPE;
    p_in_time         hris_attendance_request.in_time%TYPE;
    p_out_time        hris_attendance_request.out_time%TYPE;
    p_status          hris_attendance_request.status%TYPE;
    p_in_remarks      hris_attendance_request.in_remarks%TYPE;
    p_out_remarks      hris_attendance_request.out_remarks%TYPE;
    p_next_day_out char(1 BYTE);
BEGIN
    SELECT
        attendance_dt,
        employee_id,
        in_time,
        out_time,
        status,
        in_remarks,
        out_remarks,
        NEXT_DAY_OUT
    INTO
        p_attendance_dt,p_employee_id,p_in_time,p_out_time,p_status,p_in_remarks,p_out_remarks,p_next_day_out
    FROM
        hris_attendance_request
    WHERE
        id = p_id;

    IF
        p_status != 'AP'
    THEN
        return;
    END IF;
    IF
        p_in_time IS NOT NULL
    THEN
        INSERT INTO hris_attendance (
            attendance_dt,
            employee_id,
            attendance_time,
            attendance_from,
            remarks,
            IP_ADDRESS
        ) VALUES (
            p_attendance_dt,
            p_employee_id,
            TO_DATE(
                TO_CHAR(p_attendance_dt,'DD-MON-YYYY') || ' ' || TO_CHAR(p_in_time,'HH:MI AM'),
                'DD-MON-YYYY HH:MI AM'
            ),
            'SYSTEM',
            p_in_remarks,
            'IN'
        );

        hris_attendance_after_insert(
            p_employee_id,
            p_attendance_dt,
            p_in_time,
            p_in_remarks
        );
    END IF;

    IF
        p_out_time IS NOT NULL
    THEN
        INSERT INTO hris_attendance (
            attendance_dt,
            employee_id,
            attendance_time,
            attendance_from,
            remarks,
            IP_ADDRESS
        ) VALUES (
            p_attendance_dt,
            p_employee_id,
            case when p_next_day_out = 'Y'
            then
            TO_DATE(
                TO_CHAR((p_attendance_dt+1),'DD-MON-YYYY') || ' ' || TO_CHAR(p_out_time,'HH:MI AM'),
                'DD-MON-YYYY HH:MI AM'
            )
            else
            TO_DATE(
                TO_CHAR(p_attendance_dt,'DD-MON-YYYY') || ' ' || TO_CHAR(p_out_time,'HH:MI AM'),
                'DD-MON-YYYY HH:MI AM'
            )
            end,
            'SYSTEM',
            p_out_remarks,
            'OUT'
        );

        hris_attendance_after_insert(
            p_employee_id,
            p_attendance_dt,
            p_out_time,
            p_out_remarks
        );
    END IF;

    IF
        ( trunc(p_attendance_dt) <= trunc(SYSDATE) )
    THEN
        hris_queue_reattendance(
            trunc(p_attendance_dt),
            p_employee_id,
            trunc(p_attendance_dt)
        );
    END IF;

EXCEPTION
    WHEN no_data_found THEN
        NULL;
END;