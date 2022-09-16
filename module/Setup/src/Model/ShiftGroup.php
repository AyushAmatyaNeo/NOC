<?php

namespace Setup\Model;

use Application\Model\Model;

class ShiftGroup extends Model {

const TABLE_NAME = "HRIS_BEST_CASE_SETUP";
const CASE_ID = "CASE_ID";
const CASE_NAME = "CASE_NAME";
const START_DATE = "START_DATE";
const END_DATE = "END_DATE";
const STATUS = "STATUS";
const CREATED_DT = "CREATED_DT";
const MODIFIED_DT = "MODIFIED_DT";
const CREATED_BY = "CREATED_BY";
const MODIFIED_BY = "MODIFIED_BY";

public $caseId;
public $caseName;
public $startDate;
public $endDate;
public $status;
public $createdDt;
public $modifiedDt;
public $createdBy;
public $modifiedBy;
public $mappings = [
'caseId' => self::CASE_ID,
'caseName' => self::CASE_NAME,
'startDate' => self::START_DATE,
'endDate' => self::END_DATE,
'status' => self::STATUS,
'createdDt' => self::CREATED_DT,
'modifiedDt' => self::MODIFIED_DT,
'createdBy' => self::CREATED_BY,
'modifiedBy' => self::MODIFIED_BY,
];

}