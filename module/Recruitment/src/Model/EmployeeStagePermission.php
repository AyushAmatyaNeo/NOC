<?php 
namespace Recruitment\Model;

use Application\Model\Model;

class EmployeeStagePermission extends Model{
    
    const TABLE_NAME = "HRIS_REC_EMPLOYEE_STAGE_PERMISSION";
    const ID = "ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const STAGE_IDS = "STAGE_IDS";
    const CREATED_DT = "CREATED_DT";
    const CREATED_BY = "CREATED_BY";
    const STATUS = "STATUS";
    const VACANCY_IDS = "VACANCY_IDS";
    
    public $id;
    public $employeeId;
    public $stageIds;
    public $createdDt;
    public $createdBy;
    public $status;
    public $vacancyIds;
    

    public $mappings = [
        'id'       => self::ID,
        'employeeId'       => self::EMPLOYEE_ID,
        'stageIds'       => self::STAGE_IDS,
        'createdDt'          => self::CREATED_DT,
        'createdBy'          => self::CREATED_BY,
        'status'           => self::STATUS,
        'vacancyIds' => self::VACANCY_IDS
    ];
}