<?php
namespace AttendanceManagement\Model;

use Application\Model\Model;

class OT extends Model {
	const TABLE_NAME = "HRIS_OT";
	const OT_ID = "OT_ID";
    const FISCAL_YEAR_ID = "FISCAL_YEAR_ID";
    const MONTH_NO = "MONTH_NO";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const IS_CALCULATED = "IS_CALCULATED";

    public $otId;
    public $fiscalyearId;
    public $monthNo;
    public $createdDt;
    public $modifiedDt;
    public $isCacluated;
    public $mappings = [
    	'otId' => self::OT_ID,
        'fiscalyearId' => self::FISCAL_YEAR_ID,
        'monthNo' => self::MONTH_NO,
        'createdDt' => self::CREATED_DT,
        'modifiedDt' => self::MODIFIED_DT,
        'isCacluated' => self::IS_CALCULATED,
    ];
}