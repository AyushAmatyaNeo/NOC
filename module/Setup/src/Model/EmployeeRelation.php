<?php
namespace Setup\Model;

use Application\Model\Model;

class EmployeeRelation extends Model{
    const TABLE_NAME="HRIS_EMPLOYEE_RELATION";

    const E_R_ID="E_R_ID";
    const EMPLOYEE_ID="EMPLOYEE_ID";
    const RELATION_ID="RELATION_ID";
    const PERSON_NAME="PERSON_NAME";
    const IS_NOMINEE="IS_NOMINEE";
    const IS_DEPENDENT="IS_DEPENDENT";
    const DOB="DOB";
    const STATUS="STATUS";
    const CREATED_DT="CREATED_DT";
    const CREATED_BY="CREATED_BY";
    const MODIFIED_BY="MODIFIED_BY";
    const MODIFIED_DT="MODIFIED_DT";
    const DELETED_DT="DELETED_DT";
    const DELETED_BY="DELETED_BY";
    const CONTACT_NO="CONTACT_NO";

    public $eRId;
    public $employeeId;
    public $relationId;
    public $personName;
    public $isNominee;
    public $isDependent;
    public $dob;
    public $status;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $deletedDt;
    public $deleteBy;
    public $contactNo;

    
    
public $mappings = [
    'eRId'=>self::E_R_ID,
    'employeeId'=>self::EMPLOYEE_ID,
    'relationId'=>self::RELATION_ID,
    'personName'=>self::PERSON_NAME,
    'isNominee'=>self::IS_NOMINEE,
    'isDependent'=>self::IS_DEPENDENT,
    'dob'=>self::DOB,
    'status'=>self::STATUS,
    'createdBy'=>self::CREATED_BY,
    'createdDt'=>self::CREATED_DT,
    'modifiedBy'=>self::MODIFIED_BY,
    'modifiedDt'=>self::MODIFIED_DT,
    'deletedDt'=>self::DELETED_DT,
    'deleteBy'=>self::DELETED_BY,
    'contactNo'=>self::CONTACT_NO
];
    
}