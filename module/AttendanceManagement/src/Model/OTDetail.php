<?php
namespace AttendanceManagement\Model;

use Application\Model\Model;

class OTDetail extends Model {
	const TABLE_NAME = "HRIS_OT_DETAIL";
    const OT_DETAIL_ID = "OT_DETAIL_ID";
    const OT_ID = "OT_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const APPROVED_REMARKS = "APPROVED_REMARKS";
    const DESIGNATION_ID = "DESIGNATION_ID";
    const OTDATE = "OTDATE";
    const IN_TIME = "IN_TIME";
    const OUT_TIME = "OUT_TIME";
    const RAW_OT = "RAW_OT";
    const CALC = "CALC";
    const SATTA_BIDA = "SATTA_BIDA";
    const SATTA_BIDA_AMOUNT = "SATTA_BIDA_AMOUNT";
    const KHAJA_KARCHA = "KHAJA_KARCHA";
    const KHAJA_KHARCHA_10PM = "KHAJA_KHARCHA_10PM";
    const RATRI_VATTA = "RATRI_VATTA";
    const APPROVED_BY = "APPROVED_BY";
    const OVERTIME_ID = "OVERTIME_ID";
    const STATUS = "STATUS";

    public $otdetailId;
    public $otId;
    public $employeeId;
    public $approvedRemarks;
    public $designationId;
    public $toDate;
    public $inTime;
    public $outTime;
    public $rawOt;
    public $calc;
    public $sattaBida;
    public $sattabidaAmount;
    public $khajaKarcha;
    public $khajakarcha10PM;
    public $ratriVatta;
    public $approvedBy;
    public $overtimeId;
    public $status;
    public $mappings = [
    	'otdetailId' => self::OT_DETAIL_ID,
        'otId' => self::OT_ID,
        'employeeId' => self::EMPLOYEE_ID,
        'approvedRemarks' => self::APPROVED_REMARKS,
        'designationId' => self::DESIGNATION_ID,
        'toDate' => self::OTDATE,
        'inTime' => self::IN_TIME,
        'outTime' => self::OUT_TIME,
        'rawOt' => self::RAW_OT,
        'calc' => self::CALC,
        'sattaBida' => self::SATTA_BIDA,
        'sattabidaAmount' => self::SATTA_BIDA_AMOUNT,
        'khajaKarcha' => self::KHAJA_KARCHA,
        'khajakarcha10PM' => self::KHAJA_KHARCHA_10PM,
        'ratriVatta' => self::RATRI_VATTA,
        'approvedBy' => self::APPROVED_BY,
        'overtimeId' => self::OVERTIME_ID,
        'status' => self::STATUS,
    ];
}