create or replace PROCEDURE hris_recalc_monthly_leaves (
    p_employee_id   hris_attendance.employee_id%TYPE := NULL,
    p_leave_id      hris_leave_master_setup.leave_id%TYPE := NULL
) AS
    v_balance   NUMBER(3,1);
BEGIN
    UPDATE hris_employee_leave_assign
        SET
            balance = total_days
    WHERE
            leave_id IN (
                SELECT
                    leave_id
                FROM
                    hris_leave_master_setup
                WHERE
                    is_monthly = 'Y'
            )
        AND (
                employee_id =
                    CASE
                        WHEN p_employee_id IS NOT NULL THEN p_employee_id
                    END
            OR
                p_employee_id IS NULL
        ) AND (
                leave_id =
                    CASE
                        WHEN p_leave_id IS NOT NULL THEN p_leave_id
                    END
            OR
                p_leave_id IS NULL
        );

    -- TO UPDATE MONTHYLY_LEAVE WHERE   CARYY FORWARD IS NO

    FOR leave IN (
        SELECT
            aa.employee_id,
            aa.leave_id,
            aa.leave_year_month_no,
            SUM(aa.total_no_of_days) AS total_no_of_days
        FROM
            (
                SELECT
                    r.employee_id,
                    r.leave_id,
                    m.leave_year_month_no,
                        CASE
                            WHEN r.half_day IN (
                                'F','S'
                            ) THEN r.no_of_days / 2
                            ELSE r.no_of_days
                        END
                    AS total_no_of_days
                FROM
                    hris_employee_leave_request r
                    LEFT JOIN (
                        SELECT
                            *
                        FROM
                            hris_leave_years
                        WHERE
                            trunc(SYSDATE) BETWEEN start_date AND end_date
                    ) ly ON (
                        1 = 1
                    )
                    JOIN hris_leave_master_setup l ON (
                        r.leave_id = l.leave_id
                    ),
                    hris_leave_month_code m
                WHERE
                        r.status = 'AP'
                    AND
                        l.is_monthly = 'Y'
                    AND
                        l.carry_forward = 'N'
                    AND
                        r.start_date BETWEEN m.from_date AND m.TO_DATE
                    AND
                        r.start_date BETWEEN ly.start_date AND ly.end_date
                    AND (
                            r.employee_id =
                                CASE
                                    WHEN p_employee_id IS NOT NULL THEN p_employee_id
                                END
                        OR
                            p_employee_id IS NULL
                    ) AND (
                            r.leave_id =
                                CASE
                                    WHEN p_leave_id IS NOT NULL THEN p_leave_id
                                END
                        OR
                            p_leave_id IS NULL
                    )
            ) aa
        GROUP BY
            aa.employee_id,
            aa.leave_id,
            aa.leave_year_month_no
    ) LOOP
        UPDATE hris_employee_leave_assign
            SET
                balance = total_days - leave.total_no_of_days
        WHERE
                employee_id = leave.employee_id
            AND
                leave_id = leave.leave_id
            AND
                fiscal_year_month_no = leave.leave_year_month_no;

    END LOOP;

  -- TO UPDATE MONTHYLY_LEAVE WHERE   CARYY FORWARD IS YES

    FOR leave IN (
        SELECT
            employee_id,
            leave_id,
            SUM(total_no_of_days) AS total_no_of_days
        FROM
            (
                SELECT
                    r.employee_id,
                    r.leave_id,
                    SUM(r.no_of_days) AS total_no_of_days
                FROM
                    hris_employee_leave_request r
                    JOIN hris_leave_master_setup l ON (
                        r.leave_id = l.leave_id
                    )
                    LEFT JOIN (
                        SELECT
                            *
                        FROM
                            hris_leave_years
                        WHERE
                            trunc(SYSDATE) BETWEEN start_date AND end_date
                    ) ly ON (
                        1 = 1
                    )
                WHERE
                        r.status = 'AP'
                    AND
                        l.is_monthly = 'Y'
                    AND
                        l.carry_forward = 'Y'
                    AND
                        r.half_day NOT IN (
                            'F','S'
                        )
                    AND
                        r.start_date BETWEEN ly.start_date AND ly.end_date
                    AND (
                            r.employee_id =
                                CASE
                                    WHEN p_employee_id IS NOT NULL THEN p_employee_id
                                END
                        OR
                            p_employee_id IS NULL
                    ) AND (
                            r.leave_id =
                                CASE
                                    WHEN p_leave_id IS NOT NULL THEN p_leave_id
                                END
                        OR
                            p_leave_id IS NULL
                    )
                GROUP BY
                    r.employee_id,
                    r.leave_id
                UNION ALL
                SELECT
                    r.employee_id,
                    r.leave_id,
                    SUM(r.no_of_days) / 2 AS total_no_of_days
                FROM
                    hris_employee_leave_request r
                    JOIN hris_leave_master_setup l ON (
                        r.leave_id = l.leave_id
                    )
                    LEFT JOIN (
                        SELECT
                            *
                        FROM
                            hris_leave_years
                        WHERE
                            trunc(SYSDATE) BETWEEN start_date AND end_date
                    ) ly ON (
                        1 = 1
                    )
                WHERE
                        r.status = 'AP'
                    AND
                        l.is_monthly = 'Y'
                    AND
                        l.carry_forward = 'Y'
                    AND
                        r.half_day IN (
                            'F','S'
                        )
                    AND
                        r.start_date BETWEEN ly.start_date AND ly.end_date
                    AND (
                            r.employee_id =
                                CASE
                                    WHEN p_employee_id IS NOT NULL THEN p_employee_id
                                END
                        OR
                            p_employee_id IS NULL
                    ) AND (
                            r.leave_id =
                                CASE
                                    WHEN p_leave_id IS NOT NULL THEN p_leave_id
                                END
                        OR
                            p_leave_id IS NULL
                    )
                GROUP BY
                    r.employee_id,
                    r.leave_id
            )
        GROUP BY
            employee_id,
            leave_id
    ) LOOP
        FOR leave_assign_dtl IN (
            SELECT
                *
            FROM
                hris_employee_leave_assign
            WHERE
                    employee_id = leave.employee_id
                AND
                    leave_id = leave.leave_id
            ORDER BY fiscal_year_month_no
        ) LOOP
            IF
                ( leave.total_no_of_days >= leave_assign_dtl.total_days )
            THEN
                v_balance := 0;
            ELSE
                v_balance := leave_assign_dtl.balance - leave.total_no_of_days;
            END IF;

            UPDATE hris_employee_leave_assign
                SET
                    balance = v_balance
            WHERE
                    employee_id = leave_assign_dtl.employee_id
                AND
                    leave_id = leave_assign_dtl.leave_id
                AND
                    fiscal_year_month_no = leave_assign_dtl.fiscal_year_month_no;

        END LOOP;
    END LOOP;

END;
