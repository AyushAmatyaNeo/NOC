<?php
namespace SelfService\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\OvertimeDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OvertimeDetailRepository implements RepositoryInterface {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(OvertimeDetail::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $tempData = $model->getArrayCopyForDB();
        $this->tableGateway->insert($tempData);
    }

    public function delete($id) {
        $this->tableGateway->update([OvertimeDetail::STATUS => 'D'], [OvertimeDetail::DETAIL_ID => $id]);
    }

    public function deleteByOvertimeId($overtimeId) {
        $this->tableGateway->update([OvertimeDetail::STATUS => 'D'], [OvertimeDetail::OVERTIME_ID => $overtimeId]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[OvertimeDetail::DETAIL_ID]);
        unset($data[OvertimeDetail::CREATED_DATE]);
        unset($data[OvertimeDetail::STATUS]);
        $this->tableGateway->update($data, [OvertimeDetail::DETAIL_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchByOvertimeId($overtimeId): array {
        $rowset = $this->tableGateway->select(function(Select $select) use($overtimeId) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(OvertimeDetail::class, null, [OvertimeDetail::CREATED_DATE, OvertimeDetail::MODIFIED_DATE], [OvertimeDetail::START_TIME, OvertimeDetail::END_TIME], null, null, null, false, false, [OvertimeDetail::TOTAL_HOUR]), false);
            $select->where([OvertimeDetail::STATUS => 'E', OvertimeDetail::OVERTIME_ID => $overtimeId]);
            $select->order(OvertimeDetail::DETAIL_ID . " ASC");
        });
        return iterator_to_array($rowset, FALSE);
    }

    public function getAttendanceOvertimeValidation($employeeId, $date){
        $rawResult = EntityHelper::rawQueryResult($this->adapter, "SELECT HRIS_VALIDATE_OVERTIME_ATTD({$employeeId}, '{$date}') AS VALIDATION FROM DUMMY");
        return $rawResult->current();
    }

    public function getOvertimeDateValidation($employeeId, $date){
        $result = [];
        $rawResult = EntityHelper::rawQueryResult($this->adapter, "SELECT CURRENT_DATE FROM DUMMY WHERE CURRENT_DATE<=TO_DATE('{$date}','DD-MON-YY')");
        $result = $rawResult->current();
        return $result;
    }

    public function getOvertimeEmployeeShiftValidation($employeeId, $date, $startTime, $endTime){
        $result = [];
        $rawResult = EntityHelper::rawQueryResult($this->adapter, "SELECT HESA.EMPLOYEE_ID, HESA.SHIFT_ID, HESA.START_DATE, HESA.END_DATE, HS.SHIFT_ID , TO_CHAR(HS.START_TIME, 'HH:MI AM') AS SHIFT_START_TIME, TO_CHAR(HS.END_TIME, 'HH:MI AM') AS SHIFT_END_TIME
            FROM HRIS_EMPLOYEE_SHIFT_ASSIGN HESA
            INNER JOIN HRIS_SHIFTS HS
            ON HESA.SHIFT_ID = HS.SHIFT_ID
            WHERE HESA.EMPLOYEE_ID = {$employeeId} AND HS.STATUS = 'E' AND (TO_DATE('{$date}', 'DD-MON-YY')) BETWEEN HS.START_DATE AND HS.END_DATE");
        $result = $rawResult->current();

        if(empty($result)){
             $result["VALIDATION"] = 'F';
        } else {
            $shiftStartTime = strtotime($result['SHIFT_START_TIME']);
            $shiftEndTime = strtotime($result['SHIFT_END_TIME']);

            if(!empty($startTime) && !empty($endTime)) {
                $ot_start_time = strtotime($startTime);
                $ot_end_time = strtotime($endTime);

                if(($ot_start_time < $shiftStartTime && $ot_end_time<=$shiftStartTime) || ($ot_start_time >= $shiftEndTime && $ot_end_time>$shiftEndTime)) {
                    $result["VALIDATION"] = 'T';
                } else {
                    $result["VALIDATION"] = 'N';
                }

                if($ot_end_time<=$ot_start_time) {
                    $result["VALIDATION"] = 'G';
                }
            }            
        }

        return $result;
    }
}
