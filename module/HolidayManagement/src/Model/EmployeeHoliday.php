<?php

namespace HolidayManagement\Model;

class EmployeeHoliday {

    const TABLE_NAME = "HRIS_EMPLOYEE_HOLIDAY";
    const HOLIDAY_ID = "HOLIDAY_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";

    public $holidayId;
    public $employeeId;
    public $mappings = [
        'holidayId' => self::HOLIDAY_ID,
        'branchId' => self::EMPLOYEE_ID
    ];

}
