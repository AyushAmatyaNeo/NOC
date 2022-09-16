<?php

namespace Application\Model;

class FiscalYear extends Model {

    const TABLE_NAME = "HRIS_FISCAL_YEARS";
    const FISCAL_YEAR_ID = "FISCAL_YEAR_ID";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const REMARKS = "REMARKS";
    const FISCAL_YEAR_NAME = "FISCAL_YEAR_NAME";

    public $fiscalYearId;
    public $startDate;
    public $endDate;
    public $createdDt;
    public $modifiedDt;
    public $status;
    public $remarks;
    public $fiscalYearName;
    public $mappings = [
        'fiscalYearId' => self::FISCAL_YEAR_ID,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'remarks' => self::REMARKS,
        'fiscalYearName' => self::FISCAL_YEAR_NAME,
    ];

}
