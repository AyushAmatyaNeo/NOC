create or replace PROCEDURE hris_shift_add (
    p_employee_id   DECIMAL,
    p_shift_id      DECIMAL,
    p_start_date    DATE,
    p_end_date      DATE,
    p_created_by    DECIMAL
) AS
    v_id   DECIMAL;
BEGIN
    SELECT
        ifnull(MAX(id),0) + 1
    INTO
        v_id
    FROM
        hris_employee_shift_assign;

    INSERT INTO hris_employee_shift_assign (
        id,
        employee_id,
        shift_id,
        start_date,
        end_date,
        created_dt,
        created_by
    ) VALUES (
        v_id,
        p_employee_id,
        p_shift_id,
        p_start_date,
        p_end_date,
        current_date,
        p_created_by
    );

    IF
        p_start_date <= current_date
    THEN
        hris_reattendance(p_start_date,p_employee_id);
    END IF;

END;
