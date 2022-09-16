<?php

namespace System\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\Attendance;
use System\Model\AttendanceDevice;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AttendanceDeviceRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(AttendanceDevice::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
//        $this->tableGateway->update([AttendanceDevice::STATUS => "D"], [AttendanceDevice::DEVICE_ID => $id]);
        $this->tableGateway->delete([AttendanceDevice::DEVICE_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [AttendanceDevice::DEVICE_ID => $id]);
    }

    public function fetchAll() {
        return $this->tableGateway->select(function(Select $select) {
                    $select->where([AttendanceDevice::STATUS => 'E']);
                    $select->order(AttendanceDevice::DEVICE_NAME . " ASC");
                });
    }

    public function fetchById($id) {
        $result = $this->tableGateway->select(function(Select $select) use($id) {
            $select->where([AttendanceDevice::DEVICE_ID => $id]);
        });
        return $result->current();
    }

    public function fetchByIP($ipList, $fromDate = null, $toDate = null): array {
        $boundedParameter = [];
        $attendanceCondition = EntityHelper::conditionBuilderBounded($ipList, "A." . Attendance::IP_ADDRESS, "AND", true);
        $condition =$attendanceCondition['sql'];
        $boundedParameter=array_merge($boundedParameter,$attendanceCondition['parameter']);
        if ($fromDate != null) {
        $boundedParameter['fromDate']=$fromDate;
            $condition .= " AND A.ATTENDANCE_DT >= TO_DATE(:fromDate,'DD-MON-YYYY') ";
        };
        if ($toDate != null) {
        $boundedParameter['toDate']=$toDate;
            $condition .= " AND A.ATTENDANCE_DT <= TO_DATE(:toDate,'DD-MON-YYYY') ";
        }
        $sql = "SELECT A.IP_ADDRESS,
                  A.THUMB_ID,
                  TO_CHAR(A.ATTENDANCE_DT,'DD-MON-YYYY') AS ATTENDANCE_DATE,
                  TO_CHAR(A.ATTENDANCE_TIME,'HH:MI:SS AM') AS ATTENDANCE_TIME,
                  A.EMPLOYEE_ID,
                  E.FULL_NAME AS EMPLOYEE_NAME
                FROM HRIS_ATTENDANCE A
                LEFT JOIN HRIS_EMPLOYEES E
                ON (A.EMPLOYEE_ID = E.EMPLOYEE_ID)
                WHERE 1           =1 {$condition} ORDER BY A.ATTENDANCE_DT DESC, A.ATTENDANCE_TIME DESC, A.THUMB_ID ASC";
        $statement = $this->adapter->query($sql);
        $iterator = $statement->execute($boundedParameter);
        return iterator_to_array($iterator, false);
    }

    public function fetchAllWithBranchManager() {

        $sql = "select ad.DEVICE_ID, 
            ad.DEVICE_NAME, 
            ad.DEVICE_IP, 
            ad.DEVICE_LOCATION, 
            ad.ISACTIVE, 
            ad.COMPANY_ID, 
            ad.BRANCH_ID, 
            ad.DEVICE_COMPANY, 
            ad.STATUS, 
            ad.PURPOSE, 
            (select FULL_NAME from hris_employees 
            where employee_id = ad.BRANCH_MANAGER_ID) as FULL_NAME 
            from HRIS_ATTD_DEVICE_MASTER ad";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
