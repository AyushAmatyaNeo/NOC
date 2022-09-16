<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Training\Model;

use Application\Model\Model;


class TrainingAttendance extends Model {
    
    const TABLE_NAME = "HRIS_EMP_TRAINING_ATTENDANCE";
    const TRAINING_ID = "TRAINING_ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const TRAINING_DT = "TRAINING_DT";
    const ATTENDANCE_STATUS = "ATTENDANCE_STATUS";
    
    
    public $trainingId;
    public $employeeId;
    public $trainingDt;
    public $attendanceStatus;
    
    
    public $mappings = [
        'trainingId'=>self::TRAINING_ID,
        'employeeId'=>self::EMPLOYEE_ID,
        'trainingDt'=>self::TRAINING_DT,
        'attendanceStatus'=>self::ATTENDANCE_STATUS
    ];
    
    
}
