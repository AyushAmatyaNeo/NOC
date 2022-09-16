create or replace PROCEDURE hris_attd_insert_exe (
    p_thumb_id          NUMBER,
    p_attendance_dt     DATE,
    p_ip_address        VARCHAR2,
    p_attendance_from   VARCHAR2,
    p_attendance_time   TIMESTAMP,
    p_remarks           VARCHAR2 := NULL
) AS
    v_employee_id   NUMBER := NULL;
    v_purpose       hris_attd_device_master.purpose%TYPE := 'I/0';
BEGIN
    BEGIN
        SELECT
            purpose
        INTO
            v_purpose
        FROM
            hris_attd_device_master
        WHERE
            device_ip = p_ip_address;

    EXCEPTION
        WHEN no_data_found THEN
            NULL;
    END;

    BEGIN
        SELECT
            employee_id
        INTO
            v_employee_id
        FROM
            hris_employees
        WHERE
        status='E' and
            id_thumb_id = to_char(p_thumb_id);

    EXCEPTION
        WHEN no_data_found THEN
           NULL;

        WHEN too_many_rows THEN
            NULL;

    END;




    BEGIN
        IF
            v_employee_id IS NOT NULL
        THEN

    UPDATE hris_attendance 
    SET
    employee_id=v_employee_id,
    CHECKED='Y'
    WHERE 
    ATTENDANCE_DT=p_attendance_dt
    and ATTENDANCE_TIME=p_attendance_time
    and THUMB_ID=p_thumb_id;


           hris_attendance_after_insert(
              v_employee_id,
               p_attendance_dt,
                p_attendance_time,
                p_remarks,
                v_purpose
           );
        END IF;
    END;

EXCEPTION
    WHEN OTHERS THEN
        dbms_output.put_line('THUMB_ID: '
         || p_thumb_id
         || 'ATTENDANCE_DT:'
         || p_attendance_dt
         || 'IP_ADDRESS: '
         || p_ip_address
         || 'P_ATTENDANCE_FROM: '
         || p_attendance_from);
END;
 
 