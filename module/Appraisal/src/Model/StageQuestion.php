<?php
namespace Appraisal\Model;

use Application\Model\Model;

class StageQuestion extends Model{
    const TABLE_NAME = "HRIS_APPRAISAL_STAGE_QUESTIONS";
    const QUESTION_ID = "QUESTION_ID";
    const STAGE_ID = "STAGE_ID";
    const STATUS = "STATUS";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_DATE = "MODIFIED_DATE";

    public $questionId;
    public $stageId;
    public $status;
    public $createdDate;
    public $modifiedDate;

    public $mappings = [
        'questionId'=>self::QUESTION_ID,
        'stageId'=>self::STAGE_ID,
        'status'=>self::STATUS,
        'createdDate'=>self::CREATED_DATE,
        'modifiedDate'=>self::MODIFIED_DATE
    ];
}