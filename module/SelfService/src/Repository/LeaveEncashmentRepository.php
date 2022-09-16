<?php
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\HrisRepository;
use SelfService\Model\LeaveEncashment;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class LeaveEncashmentRepository extends HrisRepository {

    //private $tableGateway;
    //private $adapter;
    
    /*public function __construct(AdapterInterface $adapter, $tableName = null) {
        //$this->tableGateway = new TableGateway(LeaveEncashment::TABLE_NAME, $adapter);
        if ($tableName == null) {
            $tableName = LeaveEncashment::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }*/

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveEncashment::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }


    public function fetchEncashableLeave($employeeId, $fiscalYearId) {
        $resultList_Final = [];
        $boundedParameter = [];

        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['fiscalYearId'] = $fiscalYearId;
        
        $sql = "SELECT HLMS.LEAVE_ID, HLMS.LEAVE_CODE, HLMS.LEAVE_ENAME, HLMS.FISCAL_YEAR, HELA.EMPLOYEE_ID, HELA.LEAVE_ID 
        FROM HRIS_LEAVE_MASTER_SETUP HLMS
        INNER JOIN HRIS_EMPLOYEE_LEAVE_ASSIGN HELA
        ON HLMS.LEAVE_ID = HELA.LEAVE_ID
        WHERE HELA.EMPLOYEE_ID = ? AND HLMS.FISCAL_YEAR = ? AND HLMS.CASHABLE = 'Y' AND HLMS.LEAVE_CODE = 'HOUSLEV'
        ";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        if(!empty($resultList) == 1) {
            $resultListFinal = $resultList;

            foreach($resultListFinal as $indx=>$resultListFinalSIngle) {
                $resultList_Final[$resultListFinalSIngle['LEAVE_ID']] = $resultListFinalSIngle['LEAVE_ENAME'];
            }
        }
        

        return $resultList_Final;
    }

    public function fetchTotalAccumulatedDays($employeeId, $fiscalYearId, $fiscalYearMonthNo) {
        $resultList_Final = [];
        $boundedParameter = [];

        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['fiscalYearId'] = $fiscalYearId;
        $boundedParameter['fiscalYearMonthNo'] = $fiscalYearMonthNo;

        $sql = "SELECT HLMS.LEAVE_ID, HLMS.LEAVE_CODE, HLMS.LEAVE_ENAME, HLMS.FISCAL_YEAR, 
        HELA.EMPLOYEE_ID, HELA.LEAVE_ID, HELA.BALANCE, HELA.PREVIOUS_YEAR_BAL
        FROM HRIS_LEAVE_MASTER_SETUP HLMS
        INNER JOIN HRIS_EMPLOYEE_LEAVE_ASSIGN HELA
        ON HLMS.LEAVE_ID = HELA.LEAVE_ID
        WHERE HELA.EMPLOYEE_ID = ? AND HLMS.FISCAL_YEAR = ? AND HELA.FISCAL_YEAR_MONTH_NO = ?
        AND HLMS.CASHABLE = 'Y' AND HLMS.LEAVE_CODE = 'HOUSLEV'
        ";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        if(!empty($resultList) == 1) {
            $resultListFinal = $resultList;
            $resultList_Final['BALANCE'] = $resultListFinal[0]['BALANCE'];
            $resultList_Final['PREVIOUS_YEAR_BAL'] = $resultListFinal[0]['PREVIOUS_YEAR_BAL'];

        }

        return $resultList_Final;
    }

    public function getfiscalYearMontNo($fiscalYearId) {
        $resultList_Final = [];
        $boundedParameter = [];

        $boundedParameter['fiscalYearId'] = $fiscalYearId;

        $sql = "SELECT * FROM HRIS_MONTH_CODE 
        WHERE FISCAL_YEAR_ID=? AND CURRENT_DATE BETWEEN FROM_DATE AND TO_DATE; 
        ";

        $resultList = $this->rawQuery($sql, $boundedParameter);
        if(!empty($resultList)) {
            //$resultList_Final['FISCAL_YEAR_MONTH_NO'] = $resultList[0]['FISCAL_YEAR_MONTH_NO'];
            $resultList_Final['MONTH_NO'] = $resultList[0]['MONTH_NO'];
        }

        return $resultList_Final;

    }


    public function fetchTotalLeaveBalance($employeeId, $fiscalYearId, $leaveId) {

    }

    public function addLeaveEncashmentDetails(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
        return 1;
    }

    public function updatePreviousYearBalance($employeeId, $leaveId, $fiscalYearMonthNo, $leaveBalance) {

        /*$sql = "UPDATE HRIS_EMPLOYEE_LEAVE_ASSIGN SET PREVIOUS_YEAR_BAL = {$leaveBalance}, 
        REMARKS = 'Leave Encashment Applied'
        WHERE EMPLOYEE_ID = {$employeeId} AND LEAVE_ID = {$leaveId} 
        AND FISCAL_YEAR_MONTH_NO >= {$fiscalYearMonthNo}
        "; */
        $sql = "UPDATE HRIS_EMPLOYEE_LEAVE_ASSIGN SET PREVIOUS_YEAR_BAL = {$leaveBalance}, 
        REMARKS = 'Leave Encashment Applied'
        WHERE EMPLOYEE_ID = {$employeeId} AND LEAVE_ID = {$leaveId} 
        AND FISCAL_YEAR_MONTH_NO > {$fiscalYearMonthNo}
        ";

        $resultList = $this->rawQuery($sql);

        try {
            $this->rawQuery("
            CALL HRIS_RECALC_MONTHLY_LEAVES({$employeeId},'{$leaveId}');
            " );
        } catch (Exception $e) {
            return $e->getMessage();
        }
        

        return 1;

    }

    public function checkifAlreadyEncashApplied($employeeId, $fiscalYearId, $leaveId) {
        $resultList_Final = 0;
        $boundedParameter = [];

        $boundedParameter['employeeId'] = $employeeId;
        $boundedParameter['fiscalYearId'] = $fiscalYearId;
        $boundedParameter['leaveId'] = $leaveId;

        $sql = "SELECT LEAVE_ID, EMPLOYEE_ID, FISCAL_YEAR_ID FROM HRIS_LEAVE_ENCASHMENT WHERE
        EMPLOYEE_ID = ? AND FISCAL_YEAR_ID = ? AND LEAVE_ID = ?
        ";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        if(!empty($resultList) == 1) {
            $resultList_Final = 1;
        }

        return $resultList_Final;
    }

    public function getLeaveId($fiscalYearId, $leaveCode) {
        $resultList_Final = [];
        $boundedParameter = [];

        $boundedParameter['fiscalYearId'] = $fiscalYearId;
        $boundedParameter['leaveCode'] = $leaveCode;

        $sql = "SELECT LEAVE_ID FROM HRIS_LEAVE_MASTER_SETUP WHERE  
        FISCAL_YEAR = ? AND LEAVE_CODE = ?";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        if(!empty($resultList) == 1) {
            $resultList_Final['LEAVE_ID'] = $resultList[0]['LEAVE_ID'];
        }

        return $resultList_Final;
    }

    public function  getLeaveEncashment($employeeId){
    // print_r($employeeId);die;

        $sql = "select hle.employee_id, he.full_name, lms.leave_ename,hle.leave_id, hle.requested_date, 
        hle.requested_days_to_encash,
        hle.TOTAL_ACCUMULATED_DAYS, hle.remaining_balance from hris_leave_encashment hle
        left join hris_employees he on (he.employee_id = hle.employee_id)
        left join hris_leave_master_setup lms on (lms.leave_id = hle.leave_id)
        where hle.employee_id = " .$employeeId;
        // print_r($sql);die;
        return $this->rawQuery($sql);
    }

}
?>