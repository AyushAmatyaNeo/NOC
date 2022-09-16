create or replace FUNCTION leave_status_desc ( p_status hris_employee_leave_request.status%TYPE ) RETURN VARCHAR2 IS
    v_status_desc   VARCHAR2(50 BYTE);
BEGIN
    v_status_desc := ( CASE p_status
        WHEN 'RQ' THEN 'Pending'
        WHEN 'RC' THEN 'Recommended'
        WHEN 'R' THEN 'Rejected'
        WHEN 'AP' THEN 'Approved'
        WHEN 'C' THEN 'Cancelled'
        WHEN 'CP' THEN 'C Pending'
        WHEN 'CR' THEN 'C Recommended'
    END );

    RETURN v_status_desc;
END;
