CREATE OR REPLACE TRIGGER HRIS_BEFORE_LEAVE_REQUEST BEFORE
    INSERT OR UPDATE ON hris_employee_leave_request
    FOR EACH ROW
DECLARE
  V_BALANCE                     NUMBER(3,1);
  V_IS_MONTHLY                  HRIS_LEAVE_MASTER_SETUP.IS_MONTHLY%TYPE;
  V_FISCAL_YEAR_MONTH_NO        HRIS_MONTH_CODE.FISCAL_YEAR_MONTH_NO%TYPE;
  V_CARRY_FORWARD               HRIS_LEAVE_MASTER_SETUP.CARRY_FORWARD%TYPE;
  V_OLD_LEAVE_TAKEN             NUMBER(3,1);
  V_TOTAL_MONTHLY_LEAVE_TAKEN   NUMBER(3,1);
  V_LEAVE_DIVIDE NUMBER(1,0):=1;

BEGIN
  SELECT
    IS_MONTHLY,
    CARRY_FORWARD
  INTO
    V_IS_MONTHLY,V_CARRY_FORWARD
  FROM
    HRIS_LEAVE_MASTER_SETUP
  WHERE
    LEAVE_ID =:NEW.LEAVE_ID;
    --

    IF
        updating
    THEN  -- start condition if updateing
        IF
    V_IS_MONTHLY = 'N'
  THEN
    IF
      (
        :NEW.HALF_DAY IN (
          'F','S'
        )
      )
    THEN
      V_BALANCE :=:NEW.NO_OF_DAYS / 2;
    ELSE
      V_BALANCE :=:NEW.NO_OF_DAYS;
    END IF;

    IF
      :OLD.STATUS != 'AP' AND :NEW.STATUS = 'AP' AND :OLD.STATUS NOT IN ('CP','CR')
    THEN
      UPDATE HRIS_EMPLOYEE_LEAVE_ASSIGN
        SET
          BALANCE = BALANCE - V_BALANCE
      WHERE
          EMPLOYEE_ID =:NEW.EMPLOYEE_ID
        AND
          LEAVE_ID =:NEW.LEAVE_ID;

    ELSIF :OLD.STATUS IN('AP','CP','CR') AND
      :NEW.STATUS IN (
        'C','R'
      )
    THEN
      UPDATE HRIS_EMPLOYEE_LEAVE_ASSIGN
        SET
          BALANCE = BALANCE + V_BALANCE
      WHERE
          EMPLOYEE_ID =:NEW.EMPLOYEE_ID
        AND
          LEAVE_ID =:NEW.LEAVE_ID;

    END IF;

  END IF;
    --

  IF
    V_IS_MONTHLY = 'Y'
  THEN
    SELECT
      LEAVE_YEAR_MONTH_NO
    INTO
      V_FISCAL_YEAR_MONTH_NO
    FROM
      HRIS_LEAVE_MONTH_CODE
    WHERE
      TRUNC(:NEW.START_DATE) BETWEEN FROM_DATE AND TO_DATE;

    SELECT
      TOTAL_DAYS - BALANCE
    INTO
      V_OLD_LEAVE_TAKEN
    FROM
      HRIS_EMPLOYEE_LEAVE_ASSIGN
    WHERE
        EMPLOYEE_ID =:NEW.EMPLOYEE_ID
      AND
        LEAVE_ID =:NEW.LEAVE_ID
      AND
        FISCAL_YEAR_MONTH_NO = V_FISCAL_YEAR_MONTH_NO;

            IF
      ( V_CARRY_FORWARD = 'N' )
    THEN
      --
      V_BALANCE :=:NEW.NO_OF_DAYS;
      --
      IF
        :OLD.STATUS != 'AP' AND :NEW.STATUS = 'AP'
      THEN
        UPDATE HRIS_EMPLOYEE_LEAVE_ASSIGN
          SET
            BALANCE = BALANCE - V_BALANCE
        WHERE
            EMPLOYEE_ID =:NEW.EMPLOYEE_ID
          AND
            LEAVE_ID =:NEW.LEAVE_ID
          AND
            FISCAL_YEAR_MONTH_NO = V_FISCAL_YEAR_MONTH_NO;

      ELSIF :OLD.STATUS = 'AP' AND
        :NEW.STATUS IN (
          'C','R'
        )
      THEN
        UPDATE HRIS_EMPLOYEE_LEAVE_ASSIGN
          SET
            BALANCE = BALANCE + V_BALANCE
        WHERE
            EMPLOYEE_ID =:NEW.EMPLOYEE_ID
          AND
            LEAVE_ID =:NEW.LEAVE_ID
          AND
            FISCAL_YEAR_MONTH_NO = V_FISCAL_YEAR_MONTH_NO;

      END IF;

                NULL;
            END IF;

            IF
      ( V_CARRY_FORWARD = 'Y' )
    THEN

    IF
      (
        :NEW.HALF_DAY IN (
          'F','S'
        )
      )
    THEN
      V_LEAVE_DIVIDE := 2;
    END IF;

      IF
      :OLD.STATUS != 'AP' AND :NEW.STATUS = 'AP'
      THEN
        FOR LEAVE_ASSIGN_DTL IN (
          SELECT
            *
          FROM
            HRIS_EMPLOYEE_LEAVE_ASSIGN
          WHERE
              EMPLOYEE_ID =:NEW.EMPLOYEE_ID
            AND
              LEAVE_ID =:NEW.LEAVE_ID
          ORDER BY FISCAL_YEAR_MONTH_NO
        ) LOOP
          IF
            ( ( V_OLD_LEAVE_TAKEN +(:NEW.NO_OF_DAYS/V_LEAVE_DIVIDE) ) >= LEAVE_ASSIGN_DTL.TOTAL_DAYS )
          THEN
            V_BALANCE := 0;
          ELSE
            V_BALANCE := LEAVE_ASSIGN_DTL.BALANCE -(:NEW.NO_OF_DAYS/V_LEAVE_DIVIDE);
          END IF;

          UPDATE HRIS_EMPLOYEE_LEAVE_ASSIGN
            SET
              BALANCE = V_BALANCE
          WHERE
              EMPLOYEE_ID = LEAVE_ASSIGN_DTL.EMPLOYEE_ID
            AND
              LEAVE_ID = LEAVE_ASSIGN_DTL.LEAVE_ID
            AND
              FISCAL_YEAR_MONTH_NO = LEAVE_ASSIGN_DTL.FISCAL_YEAR_MONTH_NO;

        END LOOP;

       ELSIF :OLD.STATUS = 'AP' AND
        :NEW.STATUS IN (
          'C','R'
        )
      THEN
        FOR LEAVE_ASSIGN_DTL IN (
          SELECT
            *
          FROM
            HRIS_EMPLOYEE_LEAVE_ASSIGN
          WHERE
              EMPLOYEE_ID =:NEW.EMPLOYEE_ID
            AND
              LEAVE_ID =:NEW.LEAVE_ID
          ORDER BY FISCAL_YEAR_MONTH_NO
        ) LOOP
          IF
            ( ( V_OLD_LEAVE_TAKEN -(:NEW.NO_OF_DAYS/V_LEAVE_DIVIDE) ) >= LEAVE_ASSIGN_DTL.TOTAL_DAYS )
          THEN
            V_BALANCE := 0;
          ELSE
            V_BALANCE := LEAVE_ASSIGN_DTL.TOTAL_DAYS-V_OLD_LEAVE_TAKEN+(:NEW.NO_OF_DAYS/V_LEAVE_DIVIDE);
          END IF;

          UPDATE HRIS_EMPLOYEE_LEAVE_ASSIGN
            SET
              BALANCE = V_BALANCE
          WHERE
              EMPLOYEE_ID = LEAVE_ASSIGN_DTL.EMPLOYEE_ID
            AND
              LEAVE_ID = LEAVE_ASSIGN_DTL.LEAVE_ID
            AND
              FISCAL_YEAR_MONTH_NO = LEAVE_ASSIGN_DTL.FISCAL_YEAR_MONTH_NO;

        END LOOP;
      END IF;
    END IF;

        END IF;

    END IF; -- end condition if updateing

    IF
        inserting
    THEN  -- START CONDITION IF INSERTING
        IF
            :new.status = 'AP'
        THEN -- IF INSERT IS AP
            IF
                v_is_monthly = 'N'
            THEN
                IF
                    (
                        :new.half_day IN (
                            'F','S'
                        )
                    )
                THEN
                    v_balance :=:new.no_of_days / 2;
                ELSE
                    v_balance :=:new.no_of_days;
                END IF;

                UPDATE hris_employee_leave_assign
                    SET
                        balance = balance - v_balance
                WHERE
                        employee_id =:new.employee_id
                    AND
                        leave_id =:new.leave_id;

            END IF;
            
            
            -- MONTHLY LEAVE START HERE
                  IF
            v_is_monthly = 'Y'
        THEN
            SELECT
                leave_year_month_no
            INTO
                v_fiscal_year_month_no
            FROM
                hris_leave_month_code
            WHERE
                trunc(:new.start_date) BETWEEN from_date AND TO_DATE;

            SELECT
                total_days - balance
            INTO
                v_old_leave_taken
            FROM
                hris_employee_leave_assign
            WHERE
                    employee_id =:new.employee_id
                AND
                    leave_id =:new.leave_id
                AND
                    fiscal_year_month_no = v_fiscal_year_month_no;

            IF
                ( v_carry_forward = 'N' )
            THEN
      --
                v_balance :=:new.no_of_days;
      --
                IF
                    :old.status != 'AP' AND :new.status = 'AP'
                THEN
                    UPDATE hris_employee_leave_assign
                        SET
                            balance = balance - v_balance
                    WHERE
                            employee_id =:new.employee_id
                        AND
                            leave_id =:new.leave_id
                        AND
                            fiscal_year_month_no = v_fiscal_year_month_no;

                ELSIF :old.status = 'AP' AND
                    :new.status IN (
                        'C','R'
                    )
                THEN
                    UPDATE hris_employee_leave_assign
                        SET
                            balance = balance + v_balance
                    WHERE
                            employee_id =:new.employee_id
                        AND
                            leave_id =:new.leave_id
                        AND
                            fiscal_year_month_no = v_fiscal_year_month_no;

                END IF;

                NULL;
            END IF;

            IF
                ( v_carry_forward = 'Y' )
            THEN
                IF
                    (
                        :new.half_day IN (
                            'F','S'
                        )
                    )
                THEN
                    v_leave_divide := 2;
                END IF;

                    FOR leave_assign_dtl IN (
                        SELECT
                            *
                        FROM
                            hris_employee_leave_assign
                        WHERE
                                employee_id =:new.employee_id
                            AND
                                leave_id =:new.leave_id
                        ORDER BY fiscal_year_month_no
                    ) LOOP
                        IF
                            ( ( v_old_leave_taken + (:new.no_of_days / v_leave_divide ) ) >= leave_assign_dtl.total_days )
                        THEN
                            v_balance := 0;
                        ELSE
                            v_balance := leave_assign_dtl.balance - (:new.no_of_days / v_leave_divide );
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


            END IF;

        END IF;
            
            
            -- MONTHLY LEAVE END HERE
            
            begin
            HRIS_QUEUE_REATTENDANCE(:new.START_DATE,:new.employee_id,:new.END_DATE);
            end;
            

        END IF; -- END ID INSERT AP
    END IF; -- END CONDITION IF INSERTING

END;