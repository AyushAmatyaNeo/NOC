<?php

namespace DocumentRegistration\Model;

use Application\Model\Model;

class UserAssignModel extends Model {

    const TABLE_NAME = "DC_USER_ASSIGN";
    const ASSIGN_ID = "ASSIGN_ID";    
    const DEPTARTMENT_ID = "DEPTARTMENT_ID" ;    
    const BRANCH_ID = "BRANCH_ID";   
    const PROCESS_ID = "PROCESS_ID";   
    const DISPATCH_DRAFT_ID = "DISPATCH_DRAFT_ID";        
    const STATUS = "STATUS";                  
    const REMARKS = "REMARKS";           
    const CREATED_BY = "CREATED_BY";            
    const CREATED_DT = "CREATED_DT";       
    const CHECKED_BY = "CHECKED_BY";            
    const CHECKED_DATE = "CHECKED_DATE";     
    const APPROVED_BY = "APPROVED_BY";           
    const APPROVED_DATE = "APPROVED_DATE";              
    const MODIFIED_BY = "MODIFIED_BY";           
    const MODIFIED_DT = "MODIFIED_DT";                
    const DELETED_BY = "DELETED_BY";            
    const DELETED_DT = "DELETED_DT";                 
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const REG_DRAFT_ID = "REG_DRAFT_ID";   
    const LOCATION_ID = "LOCATION_ID";    


    public $assign_id;
    public $department_id;
    public $brach_id;
    public $process_id;
    public $dispatch_draft_id;
    public $status;
    public $remarks;
    public $created_by;    
    public $created_dt;    
    public $checked_by;    
    public $checked_date;    
    public $approved_by;
    public $approved_date;
    public $modified_by;
    public $modified_dt;
    public $deleted_by;
    public $deleted_dt;
    public $employee_id;
    public $reg_draft_id;
    public $location_id;


    public $mappings = [
        'assign_id' => self:: ASSIGN_ID,
        'department_id' => self:: DEPTARTMENT_ID, 
        'brach_id' => self:: BRANCH_ID, 
        'process_id' => self:: PROCESS_ID,
        'dispatch_draft_id' => self:: DISPATCH_DRAFT_ID,
        'status' => self:: STATUS, 
        'remarks' => self:: REMARKS,
        'created_by' => self:: CREATED_BY,
        'created_dt' => self:: CREATED_DT,  
        'checked_by' => self:: CHECKED_BY,  
        'checked_date' => self:: CHECKED_DATE,   
        'approved_by' => self:: APPROVED_BY,
        'approved_date' => self:: APPROVED_DATE,
        'modified_by' => self:: MODIFIED_BY,
        'modified_dt' => self:: MODIFIED_DT,
        'deleted_by' => self:: DELETED_BY,
        'deleted_dt' => self:: DELETED_DT,
        'employee_id' => self:: EMPLOYEE_ID,
        'reg_draft_id' => self:: REG_DRAFT_ID,
        'location_id' => self::LOCATION_ID,
    ];

}
