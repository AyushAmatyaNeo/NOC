create or replace TRIGGER HRIS_BEFORE_LEAVE_REQUEST BEFORE
  UPDATE ON HRIS_EMPLOYEE_LEAVE_REQUEST
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
      :OLD.STATUS != 'AP' AND :NEW.STATUS = 'AP'
    THEN
      UPDATE HRIS_EMPLOYEE_LEAVE_ASSIGN
        SET
          BALANCE = BALANCE - V_BALANCE
      WHERE
          EMPLOYEE_ID =:NEW.EMPLOYEE_ID
        AND
          LEAVE_ID =:NEW.LEAVE_ID;

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

END;