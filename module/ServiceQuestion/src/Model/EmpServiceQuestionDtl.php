<?php
namespace ServiceQuestion\Model;

use Application\Model\Model;

class EmpServiceQuestionDtl extends Model{
    const TABLE_NAME = "HRIS_EMPLOYEE_SERVICE_QA_DTL";
    const EMP_QA_ID = "EMP_QA_ID";
    const QA_ID = "QA_ID";
    const ANSWER = "ANSWER";
    const STATUS = "STATUS";
    
    public $empQaId;
    public $qaId;
    public $anwer;
    public $status;
    
    public $mappings = [
        'empQaId'=>self::EMP_QA_ID,
        'qaId'=>self::QA_ID,
        'answer'=>self::ANSWER,
        'status'=>self::STATUS
    ];
}