<?php

namespace Setup\Model;

use Application\Model\Model;

class Training extends Model {

    const TABLE_NAME = "HRIS_TRAINING_MASTER_SETUP";
    const TRAINING_ID = "TRAINING_ID";
    const TRAINING_CODE = "TRAINING_CODE";
    const TRAINING_NAME = "TRAINING_NAME";
    const INSTITUTE_ID = "INSTITUTE_ID";
    const TRAINING_TYPE = "TRAINING_TYPE";
    const START_DATE = "START_DATE";
    const END_DATE = "END_DATE";
    const DURATION = "DURATION";
    const INSTRUCTOR_NAME = "INSTRUCTOR_NAME";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DATE = "CREATED_DATE";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const MODIFIED_BY = "MODIFIED_BY";
    const COMPANY_ID = "COMPANY_ID";
    const IS_WITHIN_COMPANY = "IS_WITHIN_COMPANY";
    const DAILY_TRAINING_HOUR = "DAILY_TRAINING_HOUR";
    const SHOW_AS_TRAINING = "SHOW_AS_TRAINING";

    public $trainingId;
    public $trainingCode;
    public $trainingName;
    public $trainingType;
    public $instituteId;
    public $startDate;
    public $endDate;
    public $duration;
    public $remarks;
    public $status;
    public $createdDate;
    public $createdBy;
    public $modifiedDate;
    public $modifiedBy;
    public $instructorName;
    public $companyId;
    public $isWithinCompany;
    public $dailyTrainingHour;
    public $showAsTraining;
    public $mappings = [
        'trainingId' => self::TRAINING_ID,
        'trainingCode' => self::TRAINING_CODE,
        'trainingName' => self::TRAINING_NAME,
        'trainingType' => self::TRAINING_TYPE,
        'instituteId' => self::INSTITUTE_ID,
        'startDate' => self::START_DATE,
        'endDate' => self::END_DATE,
        'duration' => self::DURATION,
        'createdDate' => self::CREATED_DATE,
        'createdBy' => self::CREATED_BY,
        'modifiedDate' => self::MODIFIED_DATE,
        'modifiedBy' => self::MODIFIED_BY,
        'instructorName' => self::INSTRUCTOR_NAME,
        'remarks' => self::REMARKS,
        'status' => self::STATUS,
        'companyId' => self::COMPANY_ID,
        'isWithinCompany' => self::IS_WITHIN_COMPANY,
        'dailyTrainingHour' => self::DAILY_TRAINING_HOUR,
        'showAsTraining' => self::SHOW_AS_TRAINING
    ];

}
