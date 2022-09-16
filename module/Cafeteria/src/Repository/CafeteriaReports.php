<?php

namespace Cafeteria\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class CafeteriaReports implements RepositoryInterface {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        //$this->tableGateway = new TableGateway(CafeteriaMenuModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        //$this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        //$this->tableGateway->update([CafeteriaMenuModel::STATUS => 'D'], [CafeteriaMenuModel::MENU_ID => $id]);
    }

    public function edit(Model $model, $id) {
        //$this->tableGateway->update($model->getArrayCopyForDB(), [CafeteriaMenuModel::MENU_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }
    
    public function fetchEmployeeWiseDetails($by){
        $fromDate = $by['fromDate']=='' ? 'TRUNC(SYSDATE)' : $by['fromDate'] ;
        $toDate = $by['toDate'];
        $time = $by['time'] != '' ? implode(',', $by['time']) : '' ;
        $payType = $by['payType'] != '' ? "'" . implode("','", $by['payType']) . "'" : '' ;
        
        $condition = EntityHelper::getSearchConditon($by['companyId'], $by['branchId'], $by['departmentId'], $by['positionId'], $by['designationId'], $by['serviceTypeId'], $by['serviceEventTypeId'], $by['employeeTypeId'], $by['employeeId'], $by['genderId'], $by['locationId'], $by['functionalTypeId']);
        $sql='';
        if($by['reportType'] == 1){
            $sql = "SELECT hcms.menu_name AS menu_name,
                    e.employee_id as employee_id,
                    e.full_name as full_name,
                    SUM(held.quantity) AS quantity,
                    SUM(held.total_amount) AS total_amount 
                FROM HRIS_CAFETERIA_LOG_DETAIL HELD JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID = HELD.EMPLOYEE_ID
                ) JOIN HRIS_CAFETERIA_MENU_SETUP HCMS ON (HELD.MENU_CODE = HCMS.MENU_ID)
                WHERE 1=1 {$condition} AND HELD.LOG_DATE BETWEEN '$fromDate'";
                
                $sql.= $toDate=='' ? " AND TRUNC(SYSDATE)" : " AND '$toDate'";
                $sql.= $time!='' && $time!=null ? " AND HELD.TIME_CODE IN ($time)" : '' ;
                $sql.= $payType!='' && $payType!=null ? " AND HELD.PAY_TYPE IN ($payType)" : '' ;
                $sql.=' GROUP BY
            hcms.menu_name, E.EMPLOYEE_ID, e.full_name
            order by e.employee_id';
        }
        
        if($by['reportType'] == 3){
            $sql = "
        SELECT
            e.full_name as FULL_NAME,
            hcms.menu_name AS menu_name,
            HELD.LOG_DATE AS LOG_DATE,
            SUM(held.quantity) AS QUANTITY,
            SUM(held.total_amount) AS TOTAL_AMOUNT
        FROM
            hris_cafeteria_log_detail held
            JOIN hris_employees e ON (
                e.employee_id = held.employee_id
            )
            JOIN hris_cafeteria_menu_setup hcms ON (
                held.menu_code = hcms.menu_id
            )
        WHERE
                1 = 1 
            {$condition} AND HELD.LOG_DATE BETWEEN '$fromDate'";
            $sql.= $toDate=='' ? " AND TRUNC(SYSDATE)" : " AND '$toDate'";
            $sql.= $time!='' && $time!=null ? " AND HELD.TIME_CODE IN ($time)" : '' ;
            $sql.= $payType!='' && $payType!=null ? " AND HELD.PAY_TYPE IN ($payType)" : '' ;
            $sql.= ' GROUP BY HELD.LOG_DATE, hcms.menu_name, e.full_name ORDER BY HELD.LOG_DATE';
        }
        
        if($by['reportType'] == 2){
            $sql = "SELECT
                e.employee_id,
                e.employee_code,
                e.full_name,
                SUM(hel.total_amount) AS TOTAL
            FROM
                hris_cafeteria_log hel
                JOIN hris_employees e ON (
                        e.employee_id = hel.employee_id
                )
            WHERE
            1 = 1 {$condition} AND HEL.LOG_DATE BETWEEN '$fromDate'";
            $sql.= $toDate=='' ? " AND TRUNC(SYSDATE)" : " AND '$toDate'";
            $sql.= $time!='' && $time!=null ? " AND HEL.TIME_ID IN ($time)" : '' ;
            $sql.= $payType!='' && $payType!=null ? " AND HEL.PAY_TYPE IN ($payType)" : '' ;
            $sql.= ' GROUP BY E.EMPLOYEE_ID, E.EMPLOYEE_CODE, E.full_name' ;
        }
        
        if($by['reportType'] == 4){
            $sql = "SELECT
            hcms.menu_id,
            hcms.menu_name,
            SUM(hcld.quantity) AS quantity,
            SUM(hcld.total_amount) AS TOTAL_AMOUNT
            FROM
            hris_cafeteria_menu_setup hcms
            JOIN hris_cafeteria_log_detail hcld
            ON(hcld.menu_code = hcms.menu_id) 
                JOIN hris_employees e ON (
                        e.employee_id = hcld.employee_id
                )
            WHERE
            1 = 1 {$condition} AND hcld.LOG_DATE BETWEEN '$fromDate'";
            $sql.= $toDate=='' ? " AND TRUNC(SYSDATE)" : " AND '$toDate'";
            $sql.= $time!='' && $time!=null ? " AND HCLD.TIME_CODE IN ($time)" : '' ;
            $sql.= $payType!='' && $payType!=null ? " AND HCld.PAY_TYPE IN ($payType)" : '' ;
            $sql.= ' GROUP BY 
                    hcms.menu_id,
                    hcms.menu_name
                    ORDER BY menu_id' ;
        }
        
        if($by['reportType'] == 5){
            $sql = "
        SELECT
            HELD.LOG_DATE AS LOG_DATE
        FROM
            hris_cafeteria_log_detail held
            JOIN hris_employees e ON (
                e.employee_id = held.employee_id
            )
            JOIN hris_cafeteria_menu_setup hcms ON (
                held.menu_code = hcms.menu_id
            )
        WHERE
                1 = 1 
            {$condition} AND HELD.LOG_DATE BETWEEN '$fromDate'";
            $sql.= $toDate=='' ? " AND TRUNC(SYSDATE)" : " AND '$toDate'";
            $sql.= $time!='' && $time!=null ? " AND HELD.TIME_CODE IN ($time)" : '' ;
            $sql.= $payType!='' && $payType!=null ? " AND HELD.PAY_TYPE IN ($payType)" : '' ;
            $sql.= ' GROUP BY HELD.LOG_DATE ORDER BY LOG_DATE';
            
            $statement = $this->adapter->query($sql);
            $result = $statement->execute();
            $dates = Helper::extractDbData($result);
            $datesIn = "'";
            for($i = 0; $i < count($dates); $i++){
                $i == 0 ? $datesIn.=$dates[$i]['LOG_DATE']."' as DATE_".str_replace('-', '_', $dates[$i]['LOG_DATE']) : $datesIn.=",'".$dates[$i]['LOG_DATE']."' as DATE_".str_replace('-', '_', $dates[$i]['LOG_DATE']);
            } 
            
            $sql = "
        SELECT * FROM (SELECT
            E.FULL_NAME, HELD.LOG_DATE, SUM(TOTAL_AMOUNT) AS TOTAL_AMOUNT
        FROM
            hris_cafeteria_log_detail held
            JOIN hris_employees e ON (
                e.employee_id = held.employee_id
            )
            JOIN hris_cafeteria_menu_setup hcms ON (
                held.menu_code = hcms.menu_id
            )
        WHERE
                1 = 1 
            {$condition} AND HELD.LOG_DATE BETWEEN '$fromDate'";
            $sql.= $toDate=='' ? " AND TRUNC(SYSDATE)" : " AND '$toDate'";
            $sql.= $time!='' && $time!=null ? " AND HELD.TIME_CODE IN ($time)" : '' ;
            $sql.= $payType!='' && $payType!=null ? " AND HELD.PAY_TYPE IN ($payType)" : '' ;
            $sql.= " GROUP BY HELD.LOG_DATE, e.full_name) 
                    PIVOT(SUM(TOTAL_AMOUNT) FOR(LOG_DATE) IN ($datesIn))";
        }
        //echo $sql; die;
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }
}
