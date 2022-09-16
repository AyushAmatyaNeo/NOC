<?php

namespace Medical\Model;

use Application\Model\Model;

class MedicalBill extends Model {

    const TABLE_NAME = "HRIS_MEDICAL_BILL";
    const MEDICAL_ID = "MEDICAL_ID";
    const SERIAL_NO = "SERIAL_NO";
    const BILL_NO = "BILL_NO";
    const BILL_DATE = "BILL_DATE";
    const BILL_AMT = "BILL_AMT";

    public $medicalId;
    public $serialNo;
    public $billNO;
    public $billDate;
    public $billAmt;
    
    public $mappings = [
        'medicalId' => self::MEDICAL_ID,
        'serialNo' => self::SERIAL_NO,
        'billNO' => self::BILL_NO,
        'billDate' => self::BILL_DATE,
        'billAmt' => self::BILL_AMT
    ];

}
