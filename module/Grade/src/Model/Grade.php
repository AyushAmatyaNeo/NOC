<?php

namespace Grade\Model;

use Application\Model\Model;

class GradeModel extends Model {

    const TABLE_NAME = "HRIS_EMPLOYEES_GRADE_CEILING";
    const GC_ID = "GC_ID";
    const SERVICE_EVENT_TYPE_ID = "SERVICE_EVENT_TYPE_ID";
    const POSITION_ID = "POSITION_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const DESIGNATION_ID = "DESIGNATION_ID";
    const FUNCTIONAL_LEVEL_EDESC = "FUNCTIONAL_LEVEL_EDESC";
    const GRADE_SANKHYA = "GRADE_SANKHYA";
    const GRADE_CHANGED_BY = "GRADE_CHANGED_BY";
    const EVENT_DATE = "EVENT_DATE";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const REMARKS = "REMARKS";
    

    public $gradeCeilingID;
    public $serviceEventTypeID;
    public $positionID;
    public $employeeID;
    public $designationID;
    public $functionalLevelEdesc;
    public $gradeSankhya;
    public $gradeChangedBy;
    public $eventDate;
    public $createdDate;
    public $modifiedDate;
    public $remarks;
     

    public $mappings = [
        'gradeCeilingID' => self:: GC_ID, 
        'serviceEventTypeID' => self:: SERVICE_EVENT_TYPE_ID,
        'positionID' => self:: POSITION_ID,
        'employeeID' => self:: EMPLOYEE_ID,
        'designationID' => self:: DESIGNATION_ID,
        'functionalLevelEdesc' => self:: FUNCTIONAL_LEVEL_EDESC,
        'gradeSankhya' => self:: GRADE_SANKHYA,
        'gradeChangedBy' => self:: GRADE_CHANGED_BY,
        'eventDate' => self:: EVENT_DATE,
        'createdDate' => self:: CREATED_DATE,
        'modifiedDate' => self:: MODIFIED_DATE,
        'remarks' => self:: REMARKS
    ];

}
